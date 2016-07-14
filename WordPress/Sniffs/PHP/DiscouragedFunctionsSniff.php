<?php
/**
 * WordPress_Sniffs_PHP_DiscouragedFunctionsSniff.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   John Godley <john@urbangiraffe.com>
 */

if ( false === class_exists( 'Generic_Sniffs_PHP_ForbiddenFunctionsSniff', true ) ) {
	throw new PHP_CodeSniffer_Exception( 'Class Generic_Sniffs_PHP_ForbiddenFunctionsSniff not found' );
}

/**
 * WordPress_Sniffs_PHP_DiscouragedFunctionsSniff.
 *
 * Discourages the use of debug functions and suggests deprecated WordPress alternatives
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   John Godley <john@urbangiraffe.com>
 */
class WordPress_Sniffs_PHP_DiscouragedFunctionsSniff extends Generic_Sniffs_PHP_ForbiddenFunctionsSniff {

	/**
	 * A list of forbidden functions with their alternatives.
	 *
	 * The value is NULL if no alternative exists. I.e. the
	 * function should just not be used.
	 *
	 * @var array(string => string|null)
	 */
	public $forbiddenFunctions = array(
		// Deprecated.
		'ereg_replace'             => 'preg_replace',
		'ereg'                     => null,
		'eregi_replace'            => 'preg_replace',
		'split'                    => null,
		'spliti'                   => null,

		// Development.
		'print_r'                  => null,
		'debug_print_backtrace'    => null,
		'var_dump'                 => null,
		'var_export'               => null,

		// Discouraged.
		'json_encode'              => 'wp_json_encode',

		// WordPress deprecated.
		'find_base_dir'            => 'WP_Filesystem::abspath',
		'get_base_dir'             => 'WP_Filesystem::abspath',
		'dropdown_categories'      => 'wp_link_category_checklist',
		'dropdown_link_categories' => 'wp_link_category_checklist',
		'get_link'                 => 'get_bookmark',
		'get_catname'              => 'get_cat_name',
		'register_globals'         => null,
		'wp_setcookie'             => 'wp_set_auth_cookie',
		'wp_get_cookie_login'      => null,
		'wp_login'                 => 'wp_signon',
		'get_the_attachment_link'  => 'wp_get_attachment_link',
		'get_attachment_icon_src'  => 'wp_get_attachment_image_src',
		'get_attachment_icon'      => 'wp_get_attachment_image',
		'get_attachment_innerHTML' => 'wp_get_attachment_image',

		// WordPress discouraged.
		'query_posts'              => 'WP_Query',
		'wp_reset_query'           => 'wp_reset_postdata',
	);

	/**
	 * If true, an error will be thrown; otherwise a warning.
	 *
	 * @var bool
	 */
	public $error = false;

} // end class
