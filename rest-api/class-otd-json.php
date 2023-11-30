<?php
/*
Description: REST-API: X Of The Day

The code that runs the X Of the Day API service
Every 24 hours, a new character and show of the day are spawned

Version: 1.0.0
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * class LWTV_Rest_API_OTD_JSON
 *
 * The basic constructor class that will set up our JSON API.
 */
class LWTV_Rest_API_OTD_JSON {

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
	 *   - /lwtv/v1/of-the-day/
	 */
	public function rest_api_init() {

		register_rest_route(
			'lwtv/v1',
			'/of-the-day/',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'otd_rest_api_callback' ),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			'lwtv/v1',
			'/of-the-day/(?P<type>[a-zA-Z]+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'otd_rest_api_callback' ),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			'lwtv/v1',
			'/of-the-day/(?P<type>[a-zA-Z]+)/(?P<format>[a-zA-Z0-9-]+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'otd_rest_api_callback' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Rest API Callback for Of The Day
	 */
	public function otd_rest_api_callback( $data ) {
		$params = $data->get_params();
		$type   = ( isset( $params['type'] ) && '' !== $params['type'] ) ? sanitize_title_for_query( $params['type'] ) : 'unknown';
		$format = ( isset( $params['format'] ) && '' !== $params['format'] ) ? sanitize_title_for_query( $params['format'] ) : 'default';

		$response = ( new LWTV_Of_The_Day() )->of_the_day( $type, $format );
		return $response;
	}
}
