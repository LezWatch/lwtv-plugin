<?php
/*
 * All CPTs Code
 *
 * Code that runs on all custom post types.
 *
 * @since 1.5
 */


/**
 * class LWTV_All_CPTs
 */
class LWTV_All_CPTs {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );

		// Suppress the editorial calendar for all custom post types.
		add_filter( 'edcal_show_calendar_post_type_shows', function() {
			return false;
		} );
		add_filter( 'edcal_show_calendar_post_type_characters', function() {
			return false;
		} );
		add_filter( 'edcal_show_calendar_post_type_actors', function() {
			return false; }
		);

	}

	/**
	 * Front End CSS Customizations
	 */
	public function wp_enqueue_scripts() {
		wp_register_style( 'cpt-shows-styles', plugins_url( 'shows.css', __FILE__ ), true, filemtime( plugin_dir_path( __FILE__ ) . 'shows.css' ) );

		if ( is_single() && 'post_type_shows' === get_post_type() ) {
			wp_enqueue_style( 'cpt-shows-styles' );
		}
	}

}

new LWTV_All_CPTs();
