<?php
/*
    Plugin Name: Core LezWatch.TV Plugin
    Plugin URI:  https://lezwatchtv.com
    Description: All the base code for LezWatch.TV - If this isn't active, the site dies. An ugly death.
    Version: 2.6
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
		add_filter( 'http_request_args', array( $this, 'disable_wp_update' ), 10, 2 );
		add_filter( 'attachment_fields_to_edit', array( $this, 'add_attachment_attribution' ), 10000, 2);
		add_action( 'edit_attachment', array( $this, 'save_attachment_attribution' ) );
		add_action( 'pre_current_active_plugins', array( $this, 'hide_lwtv_plugin' ) );
	}


	/**
	 * Hide the LWTV Plugin.
	 * 
	 * @access public
	 * @return void
	 */
	public function hide_lwtv_plugin() {
		global $wp_list_table;

		$hide_plugins = array(
			plugin_basename( __FILE__ ),
		);
		$curr_plugins = $wp_list_table->items;
		foreach ( $curr_plugins as $plugin => $data ) {
			if (in_array( $plugin, $hide_plugins ) ) {
				unset( $wp_list_table->items[$plugin] );
			}
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
			$plugins   = json_decode( $return['body']['plugins'], true );
			unset( $plugins['plugins'][$my_plugin] );
			unset( $plugins['active'][array_search( $my_plugin, $plugins['active'] )] );
			$return['body']['plugins'] = json_encode( $plugins );
		}
		return $return;
	}

	/*
	 * Add attribution element to images
	 */
	function add_attachment_attribution( $form_fields, $post ) {
		$field_value = get_post_meta( $post->ID, 'lwtv_attribution', true );
		$form_fields[ 'lwtv_attribution' ] = array(
			'value'    => $field_value ? $field_value : '',
			'label'    => __( 'Attribution' ),
			'helps'    => __( 'Insert image attribution here (i.e. "NBCUniversal" etc)' )
		);
		return $form_fields;
	}

	/*
	 * Save attribution element to attachment post meta
	 */
	function save_attachment_attribution( $attachment_id ) {
		if ( isset( $_REQUEST['attachments'][$attachment_id]['lwtv_attribution'] ) ) {
			$lwtv_attribution = $_REQUEST['attachments'][$attachment_id]['lwtv_attribution'];
			update_post_meta( $attachment_id, 'lwtv_attribution', $lwtv_attribution );
		}
	}
}
new LWTV_Functions();

/* 
 * Include Plugins
 */

// Call CMB2 - it doesn't error if it's not there
include_once( 'plugins/cmb2.php' );

// If Facet WP is active, call customizations
if ( class_exists( 'FacetWP' ) ) {
	require_once( 'plugins/facetwp.php' );
}

/*
 * Include Custom wp-admin pages
 */
include_once( 'admin/_tools.php' );

/*
 * Add-Ons
 */
include_once( 'affiliates/_main.php' );
include_once( 'cpts/_main.php' );
include_once( 'rest-api/_main.php' );
include_once( 'statistics/_main.php' );

/* 
 * Include Misc
 */
include_once( 'cron.php' );
include_once( 'custom-loops.php' );
include_once( 'debug.php' );
include_once( 'search.php' );
include_once( 'shortcodes.php' );
include_once( 'sort-stopwords.php' );
include_once( 'query_vars.php' );

/**
 * WP CLI
 */
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	include( 'wp-cli.php' );
}