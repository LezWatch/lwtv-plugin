<?php
/**
 * Plugin Name: Core LezWatch.TV Plugin
 * Plugin URI:  https://lezwatchtv.com
 * Description: All the base code for LezWatch.TV - If this isn't active, the site dies. An ugly death.
 * Version: 2.8
 * Author: LezWatch.TV
 *
 * @package LWTV_PLUGIN
*/

/*
 * Require the library code
 */
if ( file_exists( WP_CONTENT_DIR . '/library/functions.php' ) ) {
	require_once WP_CONTENT_DIR . '/library/functions.php';
	define( 'LWTV_LIBRARY', true );
}

/*
 * Define the first year
 */
if ( ! defined( 'FIRST_LWTV_YEAR' ) ) {
	define( 'FIRST_LWTV_YEAR', '1961' );
}

/**
 * class LWTV_Functions
 *
 * The background functions for the site, independant of the theme.
 */
class LWTV_Functions {

	/**
	 * Constructor.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		add_filter( 'http_request_args', array( $this, 'disable_wp_update' ), 10, 2 );
		add_filter( 'attachment_fields_to_edit', array( $this, 'add_attachment_attribution' ), 10000, 2 );
		add_action( 'edit_attachment', array( $this, 'save_attachment_attribution' ) );
		add_action( 'pre_current_active_plugins', array( $this, 'hide_lwtv_plugin' ) );
		add_filter( 'avatar_defaults', array( $this, 'default_avatar' ) );
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
			if ( in_array( $plugin, $hide_plugins, true ) ) {
				unset( $wp_list_table->items[ $plugin ] );
			}
		}
	}

	/**
	 * Disable WP from updating this plugin..
	 *
	 * @access public
	 * @param mixed $return - array to return.
	 * @param mixed $url    - URL from which checks come and need to be blocked (i.e. wp.org)
	 * @return array        - $return
	 */
	public function disable_wp_update( $return, $url ) {
		if ( 0 === strpos( $url, 'https://api.wordpress.org/plugins/update-check/' ) ) {
			$my_plugin = plugin_basename( __FILE__ );
			$plugins   = json_decode( $return['body']['plugins'], true );
			unset( $plugins['plugins'][ $my_plugin ] );
			unset( $plugins['active'][ array_search( $my_plugin, $plugins['active'], true ) ] );
			$return['body']['plugins'] = wp_json_encode( $plugins );
		}
		return $return;
	}

	/**
	 * Get the icon as SVG.
	 *
	 * Forked from Yoast SEO
	 *
	 * @access public
	 * @param bool $base64 (default: true) - Use SVG, true/false?
	 * @param string $icon_color - What color to use.
	 * @return string
	 */
	public static function get_icon_svg( $base64 = true, $icon_color = false ) {
		global $_wp_admin_css_colors;

		$fill = ( false !== $icon_color ) ? sanitize_hex_color( $icon_color ) : '#82878c';

		if ( is_admin() && false === $icon_color ) {
			$admin_colors  = json_decode( wp_json_encode( $_wp_admin_css_colors ), true );
			$current_color = get_user_option( 'admin_color' );
			$fill          = $admin_colors[ $current_color ]['icon_colors']['base'];
		}

		$svg = '<svg width="100%" height="100%" version="1.1" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="fill:' . $fill . '"><path d="M4,10c0,-4.411 3.589,-8 8,-8c4.411,0 8,3.589 8,8v2.08c0.706,0.102 1.378,0.308 2,0.605v-2.685c0,-5.514 -4.486,-10 -10,-10c-5.514,0 -10,4.486 -10,10v2.685c0.622,-0.297 1.294,-0.503 2,-0.605Zm8,-6c-3.309,0 -6,2.691 -6,6v1.025c0.578,-0.772 1.294,-1.43 2.112,-1.929c0.412,-1.77 1.994,-3.096 3.888,-3.096c1.894,0 3.476,1.326 3.888,3.096c0.819,0.499 1.534,1.157 2.112,1.929v-1.025c0,-3.309 -2.691,-6 -6,-6Zm7,10h-1.712c-0.654,-2.307 -2.771,-4 -5.288,-4c-2.517,0 -4.634,1.693 -5.288,4h-1.712c-2.761,0 -5,2.239 -5,5c0,2.761 2.239,5 5,5h14c2.761,0 5,-2.239 5,-5c0,-2.761 -2.239,-5 -5,-5Z" transform="scale(0.666667)" fill="' . $fill . '"></path></svg>';

		if ( $base64 ) {
			return 'data:image/svg+xml;base64,' . base64_encode( $svg );
		}

		return $svg;
	}

	/**
	 * Add attribution element to images.
	 *
	 * @access public
	 * @param mixed $form_fields  array - the fields.
	 * @param mixed $post         int   - the post ID.
	 * @return                    array - form fields.
	 */
	public function add_attachment_attribution( $form_fields, $post ) {
		$field_value                     = get_post_meta( $post->ID, 'lwtv_attribution', true );
		$form_fields['lwtv_attribution'] = array(
			'value' => $field_value ? $field_value : '',
			'label' => __( 'Attribution' ),
			'helps' => __( 'Insert image attribution here (i.e. "NBCUniversal" etc)' ),
		);
		return $form_fields;
	}

	/**
	 * Save attribution element to attachment post meta.
	 *
	 * @access public
	 * @param mixed $attachment_id  int - attachment ID.
	 * @return void
	 */
	public function save_attachment_attribution( $attachment_id ) {
		if ( isset( $_REQUEST['attachments'][ $attachment_id ]['lwtv_attribution'] ) ) { // WPCS: CSRF ok.
			$lwtv_attribution = $_REQUEST['attachments'][ $attachment_id ]['lwtv_attribution']; // WPCS: CSRF ok.
			update_post_meta( $attachment_id, 'lwtv_attribution', $lwtv_attribution );
		}
	}

	/**
	 * Adding new options for default avatar
	 * @param  array $defaults
	 * @return array $defaults
	 */
	public function default_avatar( $defaults ) {
		$toaster              = plugins_url( 'assets/images/toaster.png', __FILE__ );
		$defaults[ $toaster ] = 'Toaster';
		$unicorn              = plugins_url( 'assets/images/unicorn.png', __FILE__ );
		$defaults[ $unicorn ] = 'Unicorn';
		return $defaults;
	}

}
new LWTV_Functions();

/*
 * Add-Ons.
 */
require_once 'admin/_tools.php';
require_once 'affiliates/_main.php';
require_once 'cpts/_main.php';
require_once 'features/_main.php';
require_once 'gutenberg/_main.php';
require_once 'plugins/_main.php';
require_once 'rest-api/_main.php';
require_once 'statistics/_main.php';
