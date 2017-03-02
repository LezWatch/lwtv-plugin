<?php
/*
 Plugin Name: LezWatch TV
 Plugin URI:  https://lezwatchtv.com
 Description: All the base code for LezWatch TV - If this isn't active, the site dies. An ugly death.
 Version: 2.0
 Author: Mika Epstein
*/

/**
 * class LWTV_Functions
 *
 * The background functions for the site, independant of the theme.
 */
class LWTV_Functions {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'admin_init') );
	}

	/**
	 * Admin Init
	 */
	public function admin_init() {
		// If Yoast SEO is active, call customizations
		if (is_plugin_active( 'wordpress-seo/wp-seo.php' ) ) {
			require_once( 'plugins/yoast-seo.php' );
		}
	}
}
new LWTV_Functions();

// Call CMB2 - it doesn't error if it's not there
require_once( 'plugins/cmb2.php' );

// Include CPTs
include( 'cpts/characters.php' );
include( 'cpts/shows.php' );
include( 'cpts/featured-images.php' );

// JSON API
include( 'rest-api/bury-your-queers.php' );
include( 'rest-api/stats.php' );

// Include Others
include( 'search.php' );
include( 'seo.php' );
include( 'custom-loops.php' );
include( 'statistics.php' );