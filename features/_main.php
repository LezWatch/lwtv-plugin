<?php
/**
 * Name: Site Features
 */

// Include the base files
require_once 'apis.php';           // Misc. APIs we use
require_once 'clickjacking.php';   // Prevent Clickjacking
require_once 'cron.php';           // Custom Cron jobs.
require_once 'custom-loops.php';   // Custom Loops
require_once 'custom-roles.php';   // Custom Roles
require_once 'dashboard.php';      // Dashboard Tools
require_once 'debug.php';          // Debug tools
require_once 'embeds.php';         // Custom Embeds
require_once 'grading.php';        // Show scores and grading from 3rd parties
require_once 'languages.php';      // Language Code (in progress)
require_once 'private-data.php';   // Data Removal Code
require_once 'profiles.php';       // User Profile Stuff
require_once 'shortcodes.php';     // Custom Shortcodes
require_once 'spammers.php';       // Catch and Kill Spammers
require_once 'upgrades.php';       // Handle Upgrades

/**
 * WP CLI.
 * Only include this if WP-CLI is being used.
 */
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once 'wp-cli.php';
}
