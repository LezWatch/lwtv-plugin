<?php
/**
 * Name: Site Features
 */

// Include the base files
require_once 'clickjacking.php';   // Prevent Clickjacking
require_once 'cron.php';           // Custom Cron jobs.
require_once 'custom-loops.php';   // Custom Loops
require_once 'custom-roles.php';   // Custom Roles
require_once 'dashboard.php';      // Dashboard Tools
require_once 'debug.php';          // Debug tools
require_once 'embeds.php';         // Custom Embeds
require_once 'gutenslam.php';      // Force Gutenberg to keep preferences
require_once 'languages.php';      // Language Code (in progress)
require_once 'private-data.php';   // Data Removal Code
require_once 'profiles.php';       // User Profile Stuff
require_once 'search.php';         // Search Features
require_once 'shortcodes.php';     // Custom Shortcodes
require_once 'sort-stopwords.php'; // Remove stopwords from sorting on archives
require_once 'spammers.php';       // Catch and Kill Spammers
require_once 'upgrades.php';       // Handle Upgrades

/**
 * WP CLI.
 * Only include this if WP-CLI is being used.
 */
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once 'wp-cli.php';
}
