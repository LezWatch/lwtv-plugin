<?php
/**
 * Name: Site Features
 */

// Include the base files
require_once 'cron.php';           // Custom Cron jobs.
require_once 'custom-loops.php';   // Custom Loops
require_once 'debug.php';          // Debug tools
require_once 'search.php';         // Search Features
require_once 'shortcodes.php';     // Custom Shortcodes
require_once 'sort-stopwords.php'; // Remove stopwords from sorting on archives
require_once 'query_vars.php';     // Create custom this-year/stats pages.


/**
 * WP CLI.
 * Only include this if WP-CLI is being used.
 */
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once 'wp-cli.php';
}
