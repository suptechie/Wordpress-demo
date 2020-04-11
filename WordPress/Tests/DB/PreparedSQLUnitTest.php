<?php
/**
 * Unit test class for WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Tests\DB;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the PreparedSQL sniff.
 *
 * @package WPCS\WordPressCodingStandards
 *
 * @since   0.8.0
 * @since   0.13.0 Class name changed: this class is now namespaced.
 * @since   1.0.0  This sniff has been moved from the `WP` category to the `DB` category.
 */
class PreparedSQLUnitTest extends AbstractSniffUnitTest {

	/**
	 * Returns the lines where errors should occur.
	 *
	 * @since 0.8.0
	 *
	 * @return array <int line number> => <int number of errors>
	 */
	public function getErrorList() {
		return array(
			3   => 1,
			4   => 1,
			5   => 1,
			7   => 1,
			8   => 1,
			11  => 1, // Old-style WPCS ignore comments are no longer supported.
			12  => 1, // Old-style WPCS ignore comments are no longer supported.
			16  => 1,
			17  => 1,
			18  => 1,
			20  => 1,
			21  => 1,
			54  => 1,
			64  => 1,
			71  => 1,
			85  => 1,
			90  => 1,
			106 => 1,
			107 => 1,
			108 => 1,
			109 => 1,
			112 => 1,
			115 => 1,
			118 => 1,
		);
	}

	/**
	 * Returns the lines where warnings should occur.
	 *
	 * @since 0.8.0
	 *
	 * @return array <int line number> => <int number of warnings>
	 */
	public function getWarningList() {
		return array();
	}
}
