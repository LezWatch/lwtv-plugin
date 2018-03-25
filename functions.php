<?php
/*
 Plugin Name: Core LezWatchTV Plugin
 Plugin URI:  https://lezwatchtv.com
 Description: All the base code for LezWatch TV - If this isn't active, the site dies. An ugly death.
 Version: 2.2
 Author: Mika Epstein
*/

if ( file_exists( WP_CONTENT_DIR . '/library/functions.php' ) ) include_once( WP_CONTENT_DIR . '/library/functions.php' );

if ( !defined( 'FIRST_LWTV_YEAR' ) ) define('FIRST_LWTV_YEAR', '1961');

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
		add_action( 'init', array( $this, 'init') );
		add_filter( 'http_request_args', array( $this, 'disable_wp_update' ), 10, 2 );
	}

	/**
	 * Init
	 */
	public function init() {
		// Only call on the front end
		if ( !is_admin() ) {
			include_once( 'amazon-affiliates.php' );
		}
	}

	/**
	 * Admin Init
	 */
	public function admin_init() {
		// If Yoast SEO is active, call customizations
		if ( is_plugin_active( 'wordpress-seo/wp-seo.php' ) || is_plugin_active( 'wordpress-seo-premium/wp-seo-premium.php' ) || defined( 'WPSEO_VERSION' ) ) {
			require_once( 'plugins/yoast-seo.php' );
		}
	}

	/**
	 * Disable WP from updating this plugin..
	 * 
	 * @access public
	 * @param mixed $return
	 * @param mixed $url
	 * @return $return
	 */
	public function disable_wp_update( $return, $url ) {
		if ( 0 === strpos( $url, 'https://api.wordpress.org/plugins/update-check/' ) ) {
			$my_plugin = plugin_basename( __FILE__ );
			$plugins = json_decode( $return['body']['plugins'], true );
			unset( $plugins['plugins'][$my_plugin] );
			unset( $plugins['active'][array_search( $my_plugin, $plugins['active'] )] );
			$return['body']['plugins'] = json_encode( $plugins );
		}
		return $return;
	}

}
new LWTV_Functions();

/* 
 * Include Plugins
 */

// Call CMB2 - it doesn't error if it's not there
require_once( 'plugins/cmb2.php' );

// If Facet WP is active, call customizations
if ( class_exists( 'FacetWP' ) ) {
	require_once( 'plugins/facetwp.php' );
}

/* 
 * Include Custom Post Types
 */
include_once( 'cpts/actors.php' );
include_once( 'cpts/characters.php' );
include_once( 'cpts/shows.php' );
include_once( 'cpts/all-cpts.php' );

/* 
 * Include JSON API related tools
 */
include_once( 'rest-api/alexa-skills.php' );
include_once( 'rest-api/bury-your-queers.php' );
include_once( 'rest-api/imdb.php' );
include_once( 'rest-api/of-the-day.php' );
include_once( 'rest-api/stats.php' );
include_once( 'rest-api/what-happened.php' );

/*
 * Statistics
 */
include_once( 'statistics/_main.php' );
include_once( 'statistics/array.php' );
include_once( 'statistics/output.php' );

/* 
 * Include Misc
 */
include_once( 'cron.php' );
include_once( 'custom-loops.php' );
include_once( 'search.php' );
include_once( 'shortcodes.php' );
include_once( 'query_vars.php' );

/**
 * WP CLI
 */
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	include( 'wp-cli.php' );
}