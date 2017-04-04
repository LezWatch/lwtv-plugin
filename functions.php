<?php
/*
 Plugin Name: LezWatch TV
 Plugin URI:  https://lezwatchtv.com
 Description: All the base code for LezWatch TV - If this isn't active, the site dies. An ugly death.
 Version: 2.0
 Author: Mika Epstein
*/

// DEFINES

define( 'LWTV_SYMBOLICONS_URL', plugins_url( 'symbolicons/images', __FILE__ ) );
define( 'LWTV_SYMBOLICONS_PATH', plugin_dir_path( __FILE__ ).'/symbolicons/images' );

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
	 * Init
	 */
	public function init() {
		// Nothing to see here
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

// If Facet WP is active, call customizations
if ( is_plugin_active( 'facetwp/index.php' ) ) {
	require_once( 'plugins/facetwp.php' );
}

// Include CPTs
include_once( 'cpts/characters.php' );
include_once( 'cpts/shows.php' );
include_once( 'cpts/featured-images.php' );

// JSON API
include_once( 'rest-api/bury-your-queers.php' );
include_once( 'rest-api/stats.php' );

// Symbolicons
include_once( 'symbolicons/symbolicons.php' );

// Include Others
include_once( 'search.php' );
include_once( 'seo.php' );
include_once( 'custom-loops.php' );
include_once( 'statistics.php' );
include_once( 'query_vars.php' );   // Query Variables for custom pages