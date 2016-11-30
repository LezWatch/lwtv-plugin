<?php

/*
Plugin Name: Base Functions
Plugin URI:  http://lezwatchtv.com
Description: This file calls all the children files, depending on what other
Version: 1.0
Author: Mika Epstein
*/

function lez_check_admin_plugins() {
	// If WP Help is active, call customizations
	if ( is_plugin_active( 'wp-help/wp-help.php' ) ) {
		require_once( 'plugins/wp-help.php' );
	}

	// If Yoast SEO is active, call customizations
	if (is_plugin_active( 'wordpress-seo/wp-seo.php' ) ) {
		require_once( 'plugins/yoast-seo.php' );
	}
}
add_action( 'admin_init', 'lez_check_admin_plugins' );

// Front end SEO extras
require_once( 'seo.php' );

// Call CMB2 - it doesn't error if it's not there
require_once( 'plugins/cmb2.php' );

// Include CPTs
include( 'cpts/characters.php' );
include( 'cpts/shows.php' );