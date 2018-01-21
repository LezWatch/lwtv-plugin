<?php
/*
Description: REST-API: IMDb

Calls IMDb

Version: 1.0.0
*/

if ( ! defined('WPINC' ) ) die;

/**
 * class LWTV_IMDb_JSON
 *
 * The basic constructor class that will set up our JSON API.
 */
class LWTV_IMDb_JSON {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'rest_api_init') );
	}

	/**
	 * Rest API init
	 *
	 * Creates callbacks
	 *   - /lwtv/v1/imdb/
	 */
	public function rest_api_init() {
		register_rest_route( 'lwtv/v1', '/imdb/', array(
			'methods' => 'GET',
			'callback' => array( $this, 'imdb_rest_api_callback' ),
		) );
		register_rest_route( 'lwtv/v1', '/imdb/(?P<type>[a-zA-Z]+)', array(
			'methods' => 'GET',
			'callback' => array( $this, 'imdb_rest_api_callback' ),
		) );
		register_rest_route( 'lwtv/v1', '/imdb/(?P<type>[a-zA-Z]+)/(?P<id>[a-zA-Z0-9-]+)', array(
			'methods' => 'GET',
			'callback' => array( $this, 'imdb_rest_api_callback' ),
		) );
	}

	/**
	 * Rest API Callback
	 */
	public function imdb_rest_api_callback( $data ) {
		$params = $data->get_params();
		$type   = ( isset( $params['type'] ) && $params['type'] !== '' )? sanitize_title_for_query( $params['type'] ) : false;
		$id     = ( isset( $params['id'] ) && $params['id'] !== '' )? sanitize_title_for_query( $params['id'] ) : false;

		// Valid Data
		$valid_type = array( 'title', 'name' );
		$valid_id   = array( 'tt', 'nm' );

		// If the type and ID are valid, 
		if ( ( in_array( $type, $valid_type ) && in_array( substr( $id, 0, 2 ), $valid_id ) ) && ( ( $type == 'title' && substr( $id, 0, 2 ) === 'tt' ) || ( $type == 'name' && substr( $id, 0, 2 ) === 'nm' ) ) ) {
			$return = $this->imdb( $type, $id );
		} else {
			$return = wp_send_json_error( 'Invalid input.' );
		}

		return $return;
	}

	/*
	 * Of the Day function
	 */
	public static function imdb( $type, $id ) {

		// Set params based on items...
		switch ( $type ) {
			case 'title':
				$post_type = 'post_type_shows';
				$meta_key  = 'lezshows_imdb';
				break;
			case 'name':
				$post_type = 'post_type_actors';
				$meta_key  = 'lezactors_imdb';
				break;
		}

		// WP Queery: We only want one match.
		$queery = new WP_Query ( array (
			'post_type'       => $post_type,
			'facetwp'         => false,
			'posts_per_page'  => 1,
			'meta_query'      => array(
				array(
					'key'     => $meta_key,
					'value'   => $id,
					'compare' => '=',
				),
			),
		) );
		
		// Do the needful
		while ( $queery->have_posts() ) {
			$queery->the_post();
			$post_id = get_the_ID();
		}
		wp_reset_postdata();

		// Base Array:
		$array = array(
			'id'     => $post_id,
			'name'   => get_the_title( $post_id ),
			'url'    => get_the_permalink( $post_id ),
			'score'  => ( get_post_meta( $post_id, 'lezshows_the_score' ) ) ? get_post_meta( $post_id, 'lezshows_the_score' ) : null,
			'queer'  => ( get_post_meta( $post_id, 'lezactors_queer' ) ) ? true : null,
		);

		return $array;
	}

}
new LWTV_IMDb_JSON();