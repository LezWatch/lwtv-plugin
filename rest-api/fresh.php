<?php
/*
Description: REST-API: Fresh

Reports back new stuff.
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * class LWTV_Fresh_JSON
 *
 * The basic constructor class that will set up our JSON API.
 */
class LWTV_Fresh_JSON {

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
	 *   - /lwtv/v1/fresh/
	 */
	public function rest_api_init() {
		register_rest_route(
			'lwtv/v1',
			'/fresh/',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_api_callback' ),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			'lwtv/v1',
			'/fresh/(?P<type>[a-zA-Z.\-]+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_api_callback' ),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			'lwtv/v1',
			'/fresh/(?P<type>[a-zA-Z.\-]+)/(?P<time>[a-zA-Z]+)',
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
		$time   = ( isset( $params['type'] ) && '' !== $params['time'] ) ? sanitize_title_for_query( $params['time'] ) : 'none';

		if ( ! in_array( $type, array( 'show', 'character', 'array' ), true ) || ! in_array( $type, array( 'hour', 'day', 'week' ), true ) ) {
			$return = new WP_Error( 'invalid', 'An unexpected error has occurred.' );
		}

		$return = $this->whats_new( $type, $time );
		if ( false === $return ) {
			return new WP_Error( 'not_found', 'No route was found matching the URL and request method' );
		}

		return $return;
	}

	public function whats_new( $type, $time ) {
		// this will spit out everyone added in the last X timeframe (hour, day, week)
	}

}

new LWTV_Fresh_JSON();
