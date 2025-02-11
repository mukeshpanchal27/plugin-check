<?php
/**
 * Tests for the Plugin_Readme_Check class.
 *
 * @package plugin-check
 */

use WordPress\Plugin_Check\Checker\Check_Context;
use WordPress\Plugin_Check\Checker\Check_Result;
use WordPress\Plugin_Check\Checker\Checks\Plugin_Repo\Plugin_Readme_Check;

class Plugin_Readme_Check_Tests extends WP_UnitTestCase {

	public function test_run_with_errors_no_readme() {
		$readme_check  = new Plugin_Readme_Check();
		$check_context = new Check_Context( UNIT_TESTS_PLUGIN_DIR . 'test-plugin-plugin-readme-errors-no-readme/load.php' );
		$check_result  = new Check_Result( $check_context );

		$readme_check->run( $check_result );

		$errors = $check_result->get_errors();

		$this->assertNotEmpty( $errors );
		$this->assertArrayHasKey( 'readme.txt', $errors );
		$this->assertEquals( 1, $check_result->get_error_count() );

		// Check for no readme file error.
		$this->assertArrayHasKey( 0, $errors['readme.txt'] );
		$this->assertArrayHasKey( 0, $errors['readme.txt'][0] );
		$this->assertArrayHasKey( 'code', $errors['readme.txt'][0][0][0] );
		$this->assertEquals( 'no_plugin_readme', $errors['readme.txt'][0][0][0]['code'] );
	}

	public function test_run_with_errors_invalid_readme_files() {
		$readme_check  = new Plugin_Readme_Check();
		$check_context = new Check_Context( UNIT_TESTS_PLUGIN_DIR . 'test-plugin-plugin-readme-errors-invalid-readme/load.php' );
		$check_result  = new Check_Result( $check_context );

		$readme_check->run( $check_result );

		$errors = $check_result->get_errors();

		$this->assertNotEmpty( $errors );
		$this->assertArrayHasKey( 'readme.txt', $errors );
		$this->assertEquals( 1, $check_result->get_error_count() );

		$this->assertArrayHasKey( 0, $errors['readme.txt'] );
		$this->assertArrayHasKey( 0, $errors['readme.txt'][0] );
		$this->assertArrayHasKey( 'code', $errors['readme.txt'][0][0][0] );
		$this->assertSame( 'no_plugin_readme', $errors['readme.txt'][0][0][0]['code'] );
	}

	public function test_run_with_errors_invalid_name() {
		$readme_check  = new Plugin_Readme_Check();
		$check_context = new Check_Context( UNIT_TESTS_PLUGIN_DIR . 'test-plugin-plugin-readme-errors-invalid-name/load.php' );
		$check_result  = new Check_Result( $check_context );

		$readme_check->run( $check_result );

		$errors = $check_result->get_errors();

		$this->assertNotEmpty( $errors );
		$this->assertArrayHasKey( 'readme.txt', $errors );

		// Check for invalid name error.
		$this->assertArrayHasKey( 0, $errors['readme.txt'] );
		$this->assertArrayHasKey( 0, $errors['readme.txt'][0] );
		$this->assertCount( 1, wp_list_filter( $errors['readme.txt'][0][0], array( 'code' => 'invalid_plugin_name' ) ) );
	}

	public function test_run_with_errors_restricted_contributors() {
		$readme_check  = new Plugin_Readme_Check();
		$check_context = new Check_Context( UNIT_TESTS_PLUGIN_DIR . 'test-plugin-plugin-readme-errors-invalid-name/load.php' );
		$check_result  = new Check_Result( $check_context );

		$readme_check->run( $check_result );

		$errors   = $check_result->get_errors();
		$warnings = $check_result->get_warnings();

		$this->assertNotEmpty( $errors );
		$this->assertNotEmpty( $warnings );
		$this->assertArrayHasKey( 'readme.txt', $errors );
		$this->assertArrayHasKey( 'readme.txt', $warnings );

		// Check for restricted contributors error.
		$this->assertCount( 1, wp_list_filter( $errors['readme.txt'][0][0], array( 'code' => 'readme_restricted_contributors' ) ) );

		// Check for reserved contributors warning.
		$this->assertCount( 1, wp_list_filter( $warnings['readme.txt'][0][0], array( 'code' => 'readme_reserved_contributors' ) ) );
	}

	public function test_run_with_errors_empty_name() {
		$readme_check  = new Plugin_Readme_Check();
		$check_context = new Check_Context( UNIT_TESTS_PLUGIN_DIR . 'test-plugin-plugin-readme-errors-empty-name/load.php' );
		$check_result  = new Check_Result( $check_context );

		$readme_check->run( $check_result );

		$errors = $check_result->get_errors();

		$this->assertNotEmpty( $errors );
		$this->assertArrayHasKey( 'readme.txt', $errors );

		// Check for empty name error.
		$this->assertArrayHasKey( 0, $errors['readme.txt'] );
		$this->assertArrayHasKey( 0, $errors['readme.txt'][0] );
		$this->assertCount( 1, wp_list_filter( $errors['readme.txt'][0][0], array( 'code' => 'empty_plugin_name' ) ) );
	}

	public function test_run_with_errors_default_text() {
		$readme_check  = new Plugin_Readme_Check();
		$check_context = new Check_Context( UNIT_TESTS_PLUGIN_DIR . 'test-plugin-plugin-readme-errors-default-text/load.php' );
		$check_result  = new Check_Result( $check_context );

		$readme_check->run( $check_result );

		$errors = $check_result->get_errors();

		$this->assertNotEmpty( $errors );
		$this->assertArrayHasKey( 'readme.txt', $errors );

		// Check for default readme text error.
		$this->assertCount( 1, wp_list_filter( $errors['readme.txt'][0][0], array( 'code' => 'default_readme_text' ) ) );
	}

	public function test_run_with_errors_stable_tag() {
		$readme_check  = new Plugin_Readme_Check();
		$check_context = new Check_Context( UNIT_TESTS_PLUGIN_DIR . 'test-plugin-plugin-readme-errors-stable-tag/load.php' );
		$check_result  = new Check_Result( $check_context );

		$readme_check->run( $check_result );

		$errors = $check_result->get_errors();

		$this->assertNotEmpty( $errors );
		$this->assertArrayHasKey( 'readme.txt', $errors );

		// Check for trunk stable tag error.
		$this->assertArrayHasKey( 0, $errors['readme.txt'] );
		$this->assertArrayHasKey( 0, $errors['readme.txt'][0] );
		$this->assertCount( 1, wp_list_filter( $errors['readme.txt'][0][0], array( 'code' => 'trunk_stable_tag' ) ) );

		// Check for stable tag mismatch file error. This should NOT be triggered as there is already 'trunk_stable_tag' error.
		$this->assertCount( 0, wp_list_filter( $errors['readme.txt'][0][0], array( 'code' => 'stable_tag_mismatch' ) ) );
	}

	public function test_run_with_errors_no_stable_tag() {
		$readme_check  = new Plugin_Readme_Check();
		$check_context = new Check_Context( UNIT_TESTS_PLUGIN_DIR . 'test-plugin-plugin-readme-errors-no-stable-tag/load.php' );
		$check_result  = new Check_Result( $check_context );

		$readme_check->run( $check_result );

		$errors = $check_result->get_errors();

		$this->assertNotEmpty( $errors );
		$this->assertArrayHasKey( 'readme.txt', $errors );

		// Check for no stable tag error.
		$this->assertArrayHasKey( 0, $errors['readme.txt'] );
		$this->assertArrayHasKey( 0, $errors['readme.txt'][0] );
		$this->assertCount( 1, wp_list_filter( $errors['readme.txt'][0][0], array( 'code' => 'no_stable_tag' ) ) );
	}

	public function test_run_with_errors_license() {
		$readme_check  = new Plugin_Readme_Check();
		$check_context = new Check_Context( UNIT_TESTS_PLUGIN_DIR . 'test-plugin-plugin-readme-errors-license/load.php' );
		$check_result  = new Check_Result( $check_context );

		$readme_check->run( $check_result );

		$errors = $check_result->get_errors();

		$this->assertNotEmpty( $errors );
		$this->assertArrayHasKey( 'readme.txt', $errors );

		// Check for invalid license.
		$this->assertArrayHasKey( 0, $errors['readme.txt'] );
		$this->assertArrayHasKey( 0, $errors['readme.txt'][0] );
		$this->assertCount( 1, wp_list_filter( $errors['readme.txt'][0][0], array( 'code' => 'invalid_license' ) ) );

		// Check for not same license.
		$this->assertCount( 1, wp_list_filter( $errors['readme.txt'][0][0], array( 'code' => 'license_mismatch' ) ) );
	}

	public function test_run_with_errors_no_license() {
		$readme_check  = new Plugin_Readme_Check();
		$check_context = new Check_Context( UNIT_TESTS_PLUGIN_DIR . 'test-plugin-plugin-readme-errors-no-license/load.php' );
		$check_result  = new Check_Result( $check_context );

		$readme_check->run( $check_result );

		$errors = $check_result->get_errors();

		$this->assertNotEmpty( $errors );
		$this->assertArrayHasKey( 'readme.txt', $errors );

		// Check for no license.
		$this->assertArrayHasKey( 0, $errors['readme.txt'] );
		$this->assertArrayHasKey( 0, $errors['readme.txt'][0] );
		$this->assertCount( 1, wp_list_filter( $errors['readme.txt'][0][0], array( 'code' => 'no_license' ) ) );
	}

	public function test_run_without_error_mpl2_license() {
		$readme_check  = new Plugin_Readme_Check();
		$check_context = new Check_Context( UNIT_TESTS_PLUGIN_DIR . 'test-plugin-plugin-readme-mpl2-license-without-errors/load.php' );
		$check_result  = new Check_Result( $check_context );

		$readme_check->run( $check_result );

		$errors = $check_result->get_errors();

		$this->assertEmpty( $errors );
	}

	public function test_run_with_errors_tested_upto() {
		$readme_check  = new Plugin_Readme_Check();
		$check_context = new Check_Context( UNIT_TESTS_PLUGIN_DIR . 'test-plugin-plugin-readme-errors-tested-upto/load.php' );
		$check_result  = new Check_Result( $check_context );

		$readme_check->run( $check_result );

		$errors = $check_result->get_errors();

		$this->assertNotEmpty( $errors );
		$this->assertArrayHasKey( 'readme.txt', $errors );

		// Check for tested upto.
		$this->assertArrayHasKey( 0, $errors['readme.txt'] );
		$this->assertArrayHasKey( 0, $errors['readme.txt'][0] );
		$this->assertCount( 1, wp_list_filter( $errors['readme.txt'][0][0], array( 'code' => 'outdated_tested_upto_header' ) ) );
	}

	public function test_run_md_with_errors() {
		$readme_check  = new Plugin_Readme_Check();
		$check_context = new Check_Context( UNIT_TESTS_PLUGIN_DIR . 'test-plugin-plugin-readme-md-with-errors/load.php' );
		$check_result  = new Check_Result( $check_context );

		$readme_check->run( $check_result );

		$errors   = $check_result->get_errors();
		$warnings = $check_result->get_warnings();

		$this->assertNotEmpty( $errors );
		$this->assertArrayHasKey( 'readme.md', $errors );

		$this->assertCount( 1, wp_list_filter( $errors['readme.md'][0][0], array( 'code' => 'trunk_stable_tag' ) ) );
		$this->assertCount( 1, wp_list_filter( $errors['readme.md'][0][0], array( 'code' => 'outdated_tested_upto_header' ) ) );
		$this->assertCount( 1, wp_list_filter( $errors['readme.md'][0][0], array( 'code' => 'default_readme_text' ) ) );
		$this->assertCount( 1, wp_list_filter( $errors['readme.md'][0][0], array( 'code' => 'invalid_license' ) ) );
		$this->assertCount( 1, wp_list_filter( $errors['readme.md'][0][0], array( 'code' => 'license_mismatch' ) ) );

		$this->assertNotEmpty( $warnings );
		$this->assertArrayHasKey( 'readme.md', $warnings );

		$this->assertCount( 1, wp_list_filter( $warnings['readme.md'][0][0], array( 'code' => 'mismatched_plugin_name' ) ) );
		$this->assertCount( 1, wp_list_filter( $warnings['readme.md'][0][0], array( 'code' => 'readme_invalid_contributors' ) ) );
		$this->assertCount( 1, wp_list_filter( $warnings['readme.md'][0][0], array( 'code' => 'readme_invalid_donate_link' ) ) );
	}

	public function test_single_file_plugin_without_error_for_trademarks() {
		$readme_check  = new Plugin_Readme_Check();
		$check_context = new Check_Context( WP_PLUGIN_DIR . '/single-file-plugin.php' );
		$check_result  = new Check_Result( $check_context );

		$readme_check->run( $check_result );

		$errors   = $check_result->get_errors();
		$warnings = $check_result->get_warnings();

		$this->assertEmpty( $errors );
		$this->assertEmpty( $warnings );
		$this->assertSame( 0, $check_result->get_error_count() );
		$this->assertSame( 0, $check_result->get_warning_count() );
	}

	public function test_run_with_errors_parser_warnings() {
		$readme_check  = new Plugin_Readme_Check();
		$check_context = new Check_Context( UNIT_TESTS_PLUGIN_DIR . 'test-plugin-plugin-readme-parser-warnings/load.php' );
		$check_result  = new Check_Result( $check_context );

		$readme_check->run( $check_result );

		$warnings = $check_result->get_warnings();

		$this->assertNotEmpty( $warnings );
		$this->assertArrayHasKey( 'readme.txt', $warnings );

		// Check for parser warning.
		$this->assertCount( 1, wp_list_filter( $warnings['readme.txt'][0][0], array( 'code' => 'readme_parser_warnings_tested_header_ignored' ) ) );
	}

	public function test_run_with_errors_multiple_parser_warnings() {
		$readme_check  = new Plugin_Readme_Check();
		$check_context = new Check_Context( UNIT_TESTS_PLUGIN_DIR . 'test-plugin-plugin-readme-multiple-parser-warnings/load.php' );
		$check_result  = new Check_Result( $check_context );

		$readme_check->run( $check_result );

		$errors   = $check_result->get_errors();
		$warnings = $check_result->get_warnings();

		$this->assertNotEmpty( $warnings );
		$this->assertArrayHasKey( 'readme.txt', $warnings );
		$this->assertSame( 7, $check_result->get_warning_count() );
		$this->assertEmpty( $errors );
		$this->assertSame( 0, $check_result->get_error_count() );

		// Check for parser warnings.
		$this->assertArrayHasKey( 0, $warnings['readme.txt'] );
		$this->assertArrayHasKey( 0, $warnings['readme.txt'][0] );

		$this->assertCount( 1, wp_list_filter( $warnings['readme.txt'][0][0], array( 'code' => 'readme_parser_warnings_ignored_tags' ) ) );
		$this->assertCount( 1, wp_list_filter( $warnings['readme.txt'][0][0], array( 'code' => 'readme_parser_warnings_too_many_tags' ) ) );
		$this->assertCount( 1, wp_list_filter( $warnings['readme.txt'][0][0], array( 'code' => 'readme_parser_warnings_requires_header_ignored' ) ) );
		$this->assertCount( 1, wp_list_filter( $warnings['readme.txt'][0][0], array( 'code' => 'readme_parser_warnings_tested_header_ignored' ) ) );
		$this->assertCount( 1, wp_list_filter( $warnings['readme.txt'][0][0], array( 'code' => 'readme_parser_warnings_requires_php_header_ignored' ) ) );
		$this->assertCount( 1, wp_list_filter( $warnings['readme.txt'][0][0], array( 'code' => 'readme_parser_warnings_trimmed_short_description' ) ) );
		$this->assertCount( 1, wp_list_filter( $warnings['readme.txt'][0][0], array( 'code' => 'readme_parser_warnings_trimmed_section_changelog' ) ) );
	}

	public function test_run_with_errors_parser_warnings_with_custom_set_transient_version() {
		$version = '5.0';

		set_transient( 'wp_plugin_check_latest_wp_version', $version );

		$readme_check  = new Plugin_Readme_Check();
		$check_context = new Check_Context( UNIT_TESTS_PLUGIN_DIR . 'test-plugin-plugin-readme-parser-warnings/load.php' );
		$check_result  = new Check_Result( $check_context );

		$readme_check->run( $check_result );

		$warnings = $check_result->get_warnings();

		$this->assertNotEmpty( $warnings );
		$this->assertArrayHasKey( 'readme.txt', $warnings );

		$filtered_items = wp_list_filter( $warnings['readme.txt'][0][0], array( 'code' => 'readme_parser_warnings_tested_header_ignored' ) );

		$this->assertCount( 1, $filtered_items );

		$filtered_items = array_values( $filtered_items );

		$this->assertStringContainsString( 'The "Tested up to" field was ignored. This field should only contain a valid WordPress version such as "' . $version . '"', $filtered_items[0]['message'] );

		delete_transient( 'wp_plugin_check_latest_wp_version' );
	}

	public function test_run_with_errors_multiple_parser_warnings_and_empty_ignored_array() {
		add_filter( 'wp_plugin_check_ignored_readme_warnings', '__return_empty_array' );

		$readme_check  = new Plugin_Readme_Check();
		$check_context = new Check_Context( UNIT_TESTS_PLUGIN_DIR . 'test-plugin-plugin-readme-multiple-parser-warnings/load.php' );
		$check_result  = new Check_Result( $check_context );

		$readme_check->run( $check_result );

		$errors   = $check_result->get_errors();
		$warnings = $check_result->get_warnings();

		$this->assertNotEmpty( $warnings );
		$this->assertArrayHasKey( 'readme.txt', $warnings );

		/*
		 * Parser warning `contributor_ignored` is ignored by default. When empty array is returned for
		 * 'wp_plugin_check_ignored_readme_warnings' then that ignored warning is also added in the list of warnings.
		 */
		$this->assertEquals( 8, $check_result->get_warning_count() );
		$this->assertEmpty( $errors );
		$this->assertEquals( 0, $check_result->get_error_count() );

		remove_filter( 'wp_plugin_check_ignored_readme_warnings', '__return_empty_array' );
	}

	public function test_filter_readme_warnings_ignored() {
		// Define custom ignore for testing.
		$custom_ignores = array(
			'requires_php_header_ignored',
		);

		// Create a mock filter that will return our custom ignores.
		$filter_name = 'wp_plugin_check_ignored_readme_warnings';
		add_filter(
			$filter_name,
			static function () use ( $custom_ignores ) {
				return $custom_ignores;
			}
		);

		$result = apply_filters( $filter_name, array() );

		$this->assertEquals( $custom_ignores, $result );

		// Remove the filter to avoid interfering with other tests.
		remove_filter(
			$filter_name,
			static function () use ( $custom_ignores ) {
				return $custom_ignores;
			}
		);
	}

	public function test_filter_wp_plugin_check_ignored_readme_warnings_will_return_no_error() {
		// Define custom ignore for testing.
		$custom_ignores = array(
			'tested_header_ignored',
			'contributor_ignored',
		);

		// Create a mock filter that will return our custom ignores.
		$filter_name = 'wp_plugin_check_ignored_readme_warnings';
		add_filter(
			$filter_name,
			static function () use ( $custom_ignores ) {
				return $custom_ignores;
			}
		);

		$readme_check  = new Plugin_Readme_Check();
		$check_context = new Check_Context( UNIT_TESTS_PLUGIN_DIR . 'test-plugin-plugin-readme-parser-warnings/load.php' );
		$check_result  = new Check_Result( $check_context );

		$readme_check->run( $check_result );

		$errors   = $check_result->get_errors();
		$warnings = $check_result->get_warnings();

		$this->assertEmpty( $errors );
		$this->assertEmpty( $warnings );
		$this->assertSame( 0, $check_result->get_error_count() );
		$this->assertSame( 0, $check_result->get_warning_count() );

		// Remove the filter to avoid interfering with other tests.
		remove_filter(
			$filter_name,
			static function () use ( $custom_ignores ) {
				return $custom_ignores;
			}
		);
	}

	public function test_run_with_errors_upgrade_notice() {
		$readme_check  = new Plugin_Readme_Check();
		$check_context = new Check_Context( UNIT_TESTS_PLUGIN_DIR . 'test-plugin-plugin-readme-errors-upgrade-notice/load.php' );
		$check_result  = new Check_Result( $check_context );

		$readme_check->run( $check_result );

		$warnings = $check_result->get_warnings();

		$this->assertNotEmpty( $warnings );
		$this->assertArrayHasKey( 'readme.txt', $warnings );

		// Check for upgrade notices.
		$this->assertCount( 2, wp_list_filter( $warnings['readme.txt'][0][0], array( 'code' => 'upgrade_notice_limit' ) ) );
	}

	public function test_run_with_errors_tested_up_to_latest_plus_two_version() {
		$version = '5.9'; // Target plugin has "6.1" is readme.

		set_transient( 'wp_plugin_check_latest_wp_version', $version );

		$readme_check  = new Plugin_Readme_Check();
		$check_context = new Check_Context( UNIT_TESTS_PLUGIN_DIR . 'test-plugin-plugin-readme-md-with-errors/load.php' );
		$check_result  = new Check_Result( $check_context );

		$readme_check->run( $check_result );

		$errors = $check_result->get_errors();

		$this->assertNotEmpty( $errors );

		$filtered_items = wp_list_filter( $errors['readme.md'][0][0], array( 'code' => 'nonexistent_tested_upto_header' ) );

		$this->assertCount( 1, $filtered_items );
		$this->assertStringContainsString( 'Tested up to: 6.1', $filtered_items[0]['message'] );
		$this->assertStringContainsString( 'This version of WordPress does not exist (yet).', $filtered_items[0]['message'] );

		delete_transient( 'wp_plugin_check_latest_wp_version' );
	}

	public function test_run_with_errors_tested_up_to_latest_plus_one_version() {
		$version = '6.0'; // Target plugin has "6.1" is readme.

		set_transient( 'wp_plugin_check_latest_wp_version', $version );

		$readme_check  = new Plugin_Readme_Check();
		$check_context = new Check_Context( UNIT_TESTS_PLUGIN_DIR . 'test-plugin-plugin-readme-md-with-errors/load.php' );
		$check_result  = new Check_Result( $check_context );

		$readme_check->run( $check_result );

		$errors = $check_result->get_errors();

		$this->assertCount( 0, wp_list_filter( $errors['readme.md'][0][0], array( 'code' => 'nonexistent_tested_upto_header' ) ) );

		delete_transient( 'wp_plugin_check_latest_wp_version' );
	}

	public function test_run_with_errors_tested_up_to_latest_stable_version() {
		$version = '6.1'; // Target plugin has "6.1" is readme.

		set_transient( 'wp_plugin_check_latest_wp_version', $version );

		$readme_check  = new Plugin_Readme_Check();
		$check_context = new Check_Context( UNIT_TESTS_PLUGIN_DIR . 'test-plugin-plugin-readme-md-with-errors/load.php' );
		$check_result  = new Check_Result( $check_context );

		$readme_check->run( $check_result );

		$errors = $check_result->get_errors();

		$this->assertCount( 0, wp_list_filter( $errors['readme.md'][0][0], array( 'code' => 'nonexistent_tested_upto_header' ) ) );

		delete_transient( 'wp_plugin_check_latest_wp_version' );
	}

	public function test_run_without_errors_readme_contributors_warning() {
		$readme_check  = new Plugin_Readme_Check();
		$check_context = new Check_Context( UNIT_TESTS_PLUGIN_DIR . 'test-plugin-plugin-readme-parser-warnings/load.php' );
		$check_result  = new Check_Result( $check_context );

		$readme_check->run( $check_result );

		$warnings = $check_result->get_warnings();

		// Should not contain contributors warning.
		$this->assertCount( 0, wp_list_filter( $warnings['readme.txt'][0][0], array( 'code' => 'readme_invalid_contributors' ) ) );
	}
}
