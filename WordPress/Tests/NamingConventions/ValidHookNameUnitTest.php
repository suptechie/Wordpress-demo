<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     https://make.wordpress.org/core/handbook/best-practices/coding-standards/
 */

/**
 * Unit test class for the ValidHookName sniff.
 *
 * A sniff unit test checks a .inc file for expected violations of a single
 * coding standard. Expected errors and warnings are stored in this class.
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Juliette Reinders Folmer <wpplugins_nospam@adviesenzo.nl>
 */
class WordPress_Tests_NamingConventions_ValidHookNameUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * The key of the array should represent the line number and the value
	 * should represent the number of errors that should occur on that line.
	 *
	 * @param string $testFile The name of the file being tested.
	 *
	 * @return array<int, int>
	 */
	public function getErrorList( $testFile = 'ValidHookNameUnitTest.inc' ) {

		switch ( $testFile ) {
			case 'ValidHookNameUnitTest.inc':
				return array(
					14 => 1,
					15 => 1,
					16 => 1,
					17 => 1,
					28 => 1,
					29 => 1,
					30 => 1,
					32 => 1,
					53 => 1,
					54 => 1,
					55 => 1,
					56 => 1,
					57 => 1,
					58 => 1,
					59 => 1,
					60 => 1,
					61 => 1,
					62 => 1,
					63 => 1,
					64 => 1,
					65 => 1,
					66 => 1,
					68 => 1,
					69 => 1,
					70 => 1,
					71 => 1,
					72 => 1,
					73 => 1,
					74 => 1,
					75 => 1,
					76 => 1,
					77 => 1,
					78 => 1,
					79 => 1,
					80 => 1,
					81 => 1,
				);

			case 'ValidHookNameUnitTest.1.inc':
			case 'ValidHookNameUnitTest.2.inc':
			default:
				return array();

		} // end switch

	} // end getErrorList()

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * The key of the array should represent the line number and the value
	 * should represent the number of warnings that should occur on that line.
	 *
	 * @param string $testFile The name of the file being tested.
	 *
	 * @return array<int, int>
	 */
	public function getWarningList( $testFile = 'ValidHookNameUnitTest.inc' ) {

		switch ( $testFile ) {
			case 'ValidHookNameUnitTest.inc':
				return array(
					8 => 1,
					9 => 1,
					10 => 1,
					11 => 1,
					68 => 1,
					72 => 1,
					77 => 1,
				);

			case 'ValidHookNameUnitTest.1.inc':
			case 'ValidHookNameUnitTest.2.inc':
				return array(
					12 => 1,
					13 => 1,
					14 => 1,
					15 => 1,
				);

			default:
				return array();

		} // end switch

	} // end getWarningList()

} // end class
