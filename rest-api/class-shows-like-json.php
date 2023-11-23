<?php
/*
Description: REST-API - Shows Like This

Calls the

Version: 1.0.0
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * class LWTV_Rest_API_Shows_Like_JSON
 *
 * The basic constructor class that will set up our JSON API.
 */
class LWTV_Rest_API_Shows_Like_JSON {

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
			'/similar-shows/',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_api_callback' ),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			'lwtv/v1',
			'/similar-shows/(?P<show>[a-zA-Z]+)',
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
		$show   = ( isset( $params['show'] ) && '' !== $params['show'] ) ? sanitize_title_for_query( $params['show'] ) : 'unknown';

		$response = $this->similar_show( $show );
		return $response;
	}

	/*
	 * Similar Show function
	 */
	public function similar_show( $show = 'unknown' ) {

		$return = false;

		if ( 'unknown' !== $show ) {
			$show_obj = get_page_by_path( $show, OBJECT, 'post_type_shows' );
			if ( $show_obj ) {
				$show_id = $show_obj->ID;

				$response = wp_remote_get( home_url() . '/wp-json/related-posts-by-taxonomy/v1/posts/' . $show_id . '?fields=ids&taxonomies=lez_genres,lez_intersections,lez_showtagged' );
				if ( is_array( $response ) ) {
					$rel_shows = json_decode( wp_remote_retrieve_body( $response ), true );
					$related   = array();

					foreach ( $rel_shows['posts'] as $each_show ) {
						$related[] = array(
							'post_id' => $each_show,
							'title'   => get_the_title( $each_show ),
							'url'     => get_permalink( $each_show ),
						);
					}

					$return = array(
						'post_id' => $show_id,
						'title'   => get_the_title( $show_id ),
						'url'     => get_permalink( $show_id ),
						'related' => $related,
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
