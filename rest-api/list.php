<?php
/*
Description: REST-API: List

Lists things
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * class LWTV_List_JSON
 *
 * The basic constructor class that will set up our JSON API.
 */
class LWTV_List_JSON {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
	}

	/**
	 * Rest API init
	 *
	 * Creates callbacks
	 *   - /lwtv/v1/list/
	 */
	public function rest_api_init() {
		register_rest_route(
			'lwtv/v1',
			'/list/',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_api_callback' ),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			'lwtv/v1',
			'/list/(?P<type>[a-zA-Z.\-]+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_api_callback' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Rest API Callback
	 */
	public function rest_api_callback( $data ) {
		$params = $data->get_params();
		$type   = ( isset( $params['type'] ) && '' !== $params['type'] ) ? sanitize_title_for_query( $params['type'] ) : 'none';

		if ( ! in_array( $type, array( 'shows', 'characters', 'actors' ), true ) ) {
			$return = new WP_Error( 'invalid', 'An unexpected error has occurred.' );
		}

		$return = $this->list( $type );
		if ( false === $return ) {
			return new WP_Error( 'not_found', 'No route was found matching the URL and request method' );
		}

		return $return;
	}

	public function list( $type ) {
		// Spit back an array of all ID/title/link?

		// Get a list of all posts per $type
		$return = ( new LWTV_CMB2() )->get_post_options(
			array(
				'post_type'   => 'post_type_' . $type,
				'numberposts' => ( 50 + wp_count_posts( 'post_type_' . $type )->publish ),
				'post_status' => array( 'publish' ),
			)
		);

		return $return;

	}

}

new LWTV_List_JSON();
