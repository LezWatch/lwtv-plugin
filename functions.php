<?php

/*
Plugin Name: LezWatch TV
Plugin URI:  https://lezwatchtv.com
Description: All the base code for LezWatch TV - If this isn't active, the site dies. An ugly death.
Version: 2.0
Author: Mika Epstein
*/

// Functions to run if certain plugins are active
function lez_check_admin_plugins() {
	// If Yoast SEO is active, call customizations
	if (is_plugin_active( 'wordpress-seo/wp-seo.php' ) ) {
		require_once( 'plugins/yoast-seo.php' );
	}
}
add_action( 'admin_init', 'lez_check_admin_plugins' );

// Call CMB2 - it doesn't error if it's not there
require_once( 'plugins/cmb2.php' );

// Include CPTs
include( 'cpts/characters.php' );
include( 'cpts/shows.php' );
include( 'cpts/featured-images.php' );

// Include Others
include( 'number-of-posts.php' );
include( 'search.php' );
include( 'seo.php' );
include( 'socialicons.php' );