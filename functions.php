<?php

/*
Plugin Name: Base Functions
Plugin URI:  http://lezwatchtv.com
Description: This file calls all the children files, depending on what other
Version: 1.0
Author: Mika Epstein
*/

function lez_check_plugins() {
	// If WP Help is active, call customizations
	if ( is_plugin_active( 'wp-help/wp-help.php' ) ) {
		require_once( 'plugins/wp-help.php' );
	}
}
add_action( 'admin_init', 'lez_check_plugins' );

// Call CMB2 - it doesn't error if it's not there
require_once( 'plugins/cmb2.php' );

// Include CPTs
include( 'cpts/characters.php' );
include( 'cpts/shows.php' );