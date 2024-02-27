<?php
/**
 * PHPUnit bootstrap file.
 *
 * @package Lwtv_Plugin
 */

if ( file_exists( '/vendor/aldavigdis/wp-tests-strapon/bootstrap.php' ) && file_exists( '/vendor/autoload.php' ) ) {
	// defer to strapon
	require 'vendor/autoload.php';

	use Aldavigdis\WpTestsStrapon\Bootstrap;
	use Aldavigdis\WpTestsStrapon\FetchWP;

	if ( getenv( 'WP_VERSION' ) === false ) {
		putenv( 'WP_VERSION=master' );
	}

	if ( defined( 'WP_TESTS_CONFIG_FILE_PATH' ) === false ) {
		define(
			'WP_TESTS_CONFIG_FILE_PATH',
			Aldavigdis\WpTestsStrapon\Config::path()
		);
	}

	Bootstrap::init( getenv( 'WP_VERSION' ) );

	$_tests_dir   = FetchWP::extractDirPath() . 'wordpress-develop-trunk/tests/phpunit/';
	$no_functions = "Could not find {$_tests_dir}/includes/functions.php, please check if your Strapon is secure.";
} else {
	// Fallback to wp-tests.
	$_tests_dir = getenv( 'WP_TESTS_DIR' );

	if ( ! $_tests_dir ) {
		$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
	}

	// Forward custom PHPUnit Polyfills configuration to PHPUnit bootstrap file.
	$_phpunit_polyfills_path = getenv( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH' );
	if ( false !== $_phpunit_polyfills_path ) {
		define( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH', $_phpunit_polyfills_path );
	}

	$no_functions = "Could not find {$_tests_dir}/includes/functions.php, have you run plugins/lwtv-plugin/bin/install-wp-tests.sh ?";
}

if ( ! file_exists( "{$_tests_dir}/includes/functions.php" ) ) {
	echo $no_functions . PHP_EOL; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	exit( 1 );
}

// Give access to tests_add_filter() function.
require_once "{$_tests_dir}/includes/functions.php";

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
	require dirname( dirname( __FILE__ ) ) . '/functions.php';
}

tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Start up the WP testing environment.
require "{$_tests_dir}/includes/bootstrap.php";
