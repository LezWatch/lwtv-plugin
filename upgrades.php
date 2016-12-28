<?php
/*
Plugin Name: Upgrade Control
Description: Control for upgrades
Version: 1.0
Author: Mika Epstein
*/

// auto updates
define( 'WP_AUTO_UPDATE_CORE', true );
define( 'CORE_UPGRADE_SKIP_NEW_BUNDLED', true );

// Enable nightlies (dev updates):
add_filter( 'allow_dev_auto_core_updates', '__return_true' );

// Force auto plugin updates:
add_filter( 'auto_update_plugin', '__return_true' );

// Force auto theme updates
add_filter( 'auto_update_theme', '__return_true' );

// Suspend or force emails (false == no email ; true == yes email)
add_filter( 'auto_core_update_send_email', '__return_true', 1 );
add_filter( 'automatic_updates_send_debug_email', '__return_true', 1 );

// SSL fixes
//add_filter('https_ssl_verify', '__return_false');
//add_filter('https_local_ssl_verify', '__return_false');
