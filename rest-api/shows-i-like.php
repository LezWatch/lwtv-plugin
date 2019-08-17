<?php
/*
Description: REST-API - Shows I Like

Calls the

Version: 1.0.0
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * class LWTV_Shows_I_Like_JSON
 *
 * The basic constructor class that will set up our JSON API.
 */
class LWTV_Shows_I_Like_JSON {

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
	public static function rest_api_init() {

		register_rest_route(
			'lwtv/v1',
			'/similar-shows/',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'rest_api_callback' ),
			)
		);
		register_rest_route(
			'lwtv/v1',
			'/similar-shpws/(?P<show>[a-zA-Z]+)',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'rest_api_callback' ),
			)
		);
	}

	/**
	 * Rest API Callback
	 */
	public static function rest_api_callback( $data ) {
		$params = $data->get_params();
		$show   = ( isset( $params['show'] ) && '' !== $params['show'] ) ? sanitize_title_for_query( $params['show'] ) : 'unknown';

		$response = $this->similar_show( $show );
		return $response;
	}

	/*
	 * Similar Show function
	 */
	public static function similar_show( $show = 'unknown' ) {

		$return = false;

		if ( 'unknown' !== $show ) {
			$show_obj = get_page_by_path( $show, OBJECT, 'post_type_shows' );
			if ( $show_obj ) {
				$show_id = $show_obj->ID;

				$response = wp_remote_get( home_url() . '/wp-json/related-posts-by-taxonomy/v1/posts/' . $show_id . '?fields=ids&taxonomies=lez_country,lez_stars,lez_genres,lez_intersections,lez_showtagged' );
				if ( is_array( $response ) ) {
					$return = array(
						'post_id' => $show_id,
						'slug'    => $show,
						'related' => $response['body'],
					);
				}
			}
		}

		if ( ! $return ) {
			return new WP_Error( 'invalid_show', 'Invalid show name.', array( 'status' => 404 ) );
		} else {
			return $return;
		}
	}
}

new LWTV_Shows_I_Like_JSON();
