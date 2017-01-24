<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

/**
 * Restricts usage of some classes.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.10.0
 */
abstract class WordPress_AbstractClassRestrictionsSniff extends WordPress_AbstractFunctionRestrictionsSniff {

	/**
	 * Regex pattern with placeholder for the function names.
	 *
	 * @var string
	 */
	protected $regex_pattern = '`^\\\\(?:%s)$`i';

	/**
	 * Temporary storage for retrieved class name.
	 *
	 * @var string
	 */
	protected $classname;

	/**
	 * Groups of classes to restrict.
	 *
	 * This method should be overridden in extending classes.
	 *
	 * Example: groups => array(
	 * 	'lambda' => array(
	 * 		'type'      => 'error' | 'warning',
	 * 		'message'   => 'Avoid direct calls to the database.',
	 * 		'classes'   => array( 'PDO', '\Namespace\Classname' ),
	 * 	)
	 * )
	 *
	 * You can use * wildcards to target a group of (namespaced) classes.
	 * Aliased namespaces (use ..) are currently not supported.
	 *
	 * Documented here for clarity. Not (re)defined as it is already defined in the parent class.
	 *
	 * @return array
	 *
	abstract public function getGroups();
	 */

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		// Prepare the function group regular expressions only once.
		if ( false === $this->setup_groups( 'classes' ) ) {
			return array();
		}

		return array(
			T_DOUBLE_COLON,
			T_NEW,
			T_EXTENDS,
			T_IMPLEMENTS,
		);

	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
	 * @param int                  $stackPtr  The position of the current token
	 *                                        in the stack passed in $tokens.
	 *
	 * @return int|void Integer stack pointer to skip forward or void to continue
	 *                  normal file processing.
	 */
	public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {
		// Reset the temporary storage before processing the token.
		unset( $this->classname );

		return parent::process( $phpcsFile, $stackPtr );
	}

	/**
	 * Determine if we have a valid classname for the target token.
	 *
	 * @since 0.11.0 This logic was originally contained in the `process()` method.
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return bool
	 */
	public function is_targetted_token( $stackPtr ) {

		$token     = $this->tokens[ $stackPtr ];
		$classname = '';

		if ( in_array( $token['code'], array( T_NEW, T_EXTENDS, T_IMPLEMENTS ), true ) ) {
			if ( T_NEW === $token['code'] ) {
				$nameEnd   = ( $this->phpcsFile->findNext( array( T_OPEN_PARENTHESIS, T_WHITESPACE, T_SEMICOLON, T_OBJECT_OPERATOR ), ( $stackPtr + 2 ) ) - 1 );
			} else {
				$nameEnd   = ( $this->phpcsFile->findNext( array( T_CLOSE_CURLY_BRACKET, T_WHITESPACE ), ( $stackPtr + 2 ) ) - 1 );
			}

			$length    = ( $nameEnd - ( $stackPtr + 1 ) );
			$classname = $this->phpcsFile->getTokensAsString( ( $stackPtr + 2 ), $length );

			if ( T_NS_SEPARATOR !== $this->tokens[ ( $stackPtr + 2 ) ]['code'] ) {
				$classname = $this->get_namespaced_classname( $classname, ( $stackPtr - 1 ) );
			}
		}

		if ( T_DOUBLE_COLON === $token['code'] ) {
			$nameEnd   = $this->phpcsFile->findPrevious( array( T_STRING ), ( $stackPtr - 1 ) );
			$nameStart = ( $this->phpcsFile->findPrevious( array( T_STRING, T_NS_SEPARATOR, T_NAMESPACE ), ( $nameEnd - 1 ), null, true, null, true ) + 1 );
			$length    = ( $nameEnd - ( $nameStart - 1) );
			$classname = $this->phpcsFile->getTokensAsString( $nameStart, $length );

			if ( T_NS_SEPARATOR !== $this->tokens[ $nameStart ]['code'] ) {
				$classname = $this->get_namespaced_classname( $classname, ( $nameStart - 1 ) );
			}
		}

		// Stop if we couldn't determine a classname.
		if ( empty( $classname ) ) {
			return false;
		}

		// Nothing to do if 'parent', 'self' or 'static'.
		if ( in_array( $classname, array( 'parent', 'self', 'static' ), true ) ) {
			return false;
		}

		$this->classname = $classname;
		return true;

	} // End is_targetted_token().

	/**
	 * Verify if the current token is one of the targetted classes.
	 *
	 * @since 0.11.0 Split out from the `process()` method.
	 *
	 * @param int $stackPtr The position of the current token in the stack.
	 *
	 * @return int|void Integer stack pointer to skip forward or void to continue
	 *                  normal file processing.
	 */
	public function check_for_matches( $stackPtr ) {
		$skip_to = array();

		foreach ( $this->groups as $groupName => $group ) {

			if ( isset( $this->excluded_groups[ $groupName ] ) ) {
				continue;
			}

			if ( preg_match( $group['regex'], $this->classname ) === 1 ) {
				$skip_to[] = $this->process_matched_token( $stackPtr, $groupName, $this->classname );
			}
		}

		if ( empty( $skip_to ) || min( $skip_to ) === 0 ) {
			return;
		}

		return min( $skip_to );

	} // End is_targetted_token().

	/**
	 * Prepare the class name for use in a regular expression.
	 *
	 * The getGroups() method allows for providing class names with a wildcard * to target
	 * a group of classes within a namespace. It also allows for providing class names as
	 * 'ordinary' names or prefixed with one or more namespaces.
	 * This prepare routine takes that into account while still safely escaping the
	 * class name for use in a regular expression.
	 *
	 * @param string $classname Class name, potentially prefixed with namespaces.
	 * @return string Regex escaped class name.
	 */
	protected function prepare_name_for_regex( $classname ) {
		$classname = trim( $classname, '\\' ); // Make sure all classnames have a \ prefix, but only one.
		$classname = str_replace( array( '.*', '*' ) , '#', $classname ); // Replace wildcards with placeholder.
		$classname = preg_quote( $classname, '`' );
		$classname = str_replace( '#', '.*', $classname ); // Replace placeholder with regex wildcard.

		return $classname;
	}

	/**
	 * See if the classname was found in a namespaced file and if so, add the namespace to the classname.
	 *
	 * @param string $classname   The full classname as found.
	 * @param int    $search_from The token position to search up from.
	 * @return string Classname, potentially prefixed with the namespace.
	 */
	protected function get_namespaced_classname( $classname, $search_from ) {
		// Don't do anything if this is already a fully qualified classname.
		if ( empty( $classname ) || '\\' === $classname[0] ) {
			return $classname;
		}

		// Remove the namespace keyword if used.
		if ( 0 === strpos( $classname, 'namespace\\' ) ) {
			$classname = substr( $classname, 10 );
		}

		$namespace_keyword = $this->phpcsFile->findPrevious( T_NAMESPACE, $search_from );
		if ( false === $namespace_keyword ) {
			// No namespace keyword found at all, so global namespace.
			$classname = '\\' . $classname;
		} else {
			$namespace = $this->determine_namespace( $search_from );

			if ( ! empty( $namespace ) ) {
				$classname = '\\' . $namespace . '\\' . $classname;
			} else {
				// No actual namespace found, so global namespace.
				$classname = '\\' . $classname;
			}
		}

		return $classname;
	}

	/**
	 * Determine the namespace name based on whether this is a scoped namespace or a file namespace.
	 *
	 * @param int $search_from The token position to search up from.
	 * @return string Namespace name or empty string if it couldn't be determined or no namespace applied.
	 */
	protected function determine_namespace( $search_from ) {
		$namespace = '';

		if ( ! empty( $this->tokens[ $search_from ]['conditions'] ) ) {
			// Scoped namespace {}.
			foreach ( $this->tokens[ $search_from ]['conditions'] as $pointer => $type ) {
				if ( T_NAMESPACE === $type && $this->tokens[ $pointer ]['scope_closer'] > $search_from ) {
					$namespace = $this->get_namespace_name( $pointer );
				}
				break; // We only need to check the highest level condition.
			}
		} else {
			// Let's see if we can find a file namespace instead.
			$first = $this->phpcsFile->findNext( array( T_NAMESPACE ), 0, $search_from );

			if ( false !== $first && empty( $this->tokens[ $first ]['scope_condition'] ) ) {
				$namespace = $this->get_namespace_name( $first );
			}
		}

		return $namespace;
	}

	/**
	 * Get the namespace name based on the position of the namespace scope opener.
	 *
	 * @param int $namespace_token The token position to search from.
	 * @return string Namespace name.
	 */
	protected function get_namespace_name( $namespace_token ) {
		$nameEnd   = ( $this->phpcsFile->findNext( array( T_OPEN_CURLY_BRACKET, T_WHITESPACE, T_SEMICOLON ), ( $namespace_token + 2 ) ) - 1 );
		$length    = ( $nameEnd - ( $namespace_token + 1 ) );
		$namespace = $this->phpcsFile->getTokensAsString( ( $namespace_token + 2 ), $length );

		return $namespace;
	}

} // End class.
