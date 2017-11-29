<?php
/*
Description: REST-API: X Of The Day

The code that runs the X Of the Day API service
Every 24 hours, a new character and show of the day are spawned

Version: 1.0.0
*/

if ( ! defined('WPINC' ) ) die;

/**
 * class LWTV_OTD_JSON
 *
 * The basic constructor class that will set up our JSON API.
 */
class LWTV_OTD_JSON {

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
	 *   - /lwtv/v1/otd/
	 */
	public function rest_api_init() {

		register_rest_route( 'lwtv/v1', '/of-the-day/', array(
			'methods' => 'GET',
			'callback' => array( $this, 'otd_rest_api_callback' ),
		) );

		register_rest_route( 'lwtv/v1', '/of-the-day/(?P<type>[a-zA-Z0-9-]+)', array(
			'methods' => 'GET',
			'callback' => array( $this, 'otd_rest_api_callback' ),
		) );

	}

	/**
	 * Rest API Callback for Of The Day
	 */
	public function otd_rest_api_callback( $data ) {
		$params = $data->get_params();
		$type   = ( isset( $params['type'] ) && $params['type'] !== '' )? sanitize_title_for_query( $params['type'] ) : 'unknown';
		$response = $this->of_the_day( $type );
		return $response;
	}
	
	public static function of_the_day( $type = 'unknown' ) {

		// Valid types of 'of the day':
		$valid_types = array( 'character', 'show' );

		// If there's no known type, we'll assume character
		if ( $type == 'unknown' || !in_array( $type, $valid_types ) ) {
			$type = 'character';
		}

		if ( false === ( $id = get_transient( 'lwtv_otd_' . $type ) ) ) {
			// Grab a random post
			$args = array( 
				'post_type'      => 'post_type_' . $type . 's',
				'orderby'        => 'rand', 
				'posts_per_page' =>'1',
			);
			$post = new WP_Query( $args );

			// Do the needful
			while ( $post->have_posts() ) {
				$post->the_post(); 
				
				$id = get_the_ID();
			}
			wp_reset_postdata();

			set_transient( 'lwtv_otd_' . $type, $id, DAY_IN_SECONDS );
		}

		// Generate Array
		switch( $type ) {
			case 'character':
				$all_shows        = lwtv_yikes_chardata( $id, 'shows' );
				if ( $all_shows !== '' ) {
					$show_title = array();
					foreach ( $all_shows as $each_show ) {
						array_push( $show_title, get_the_title( $each_show['show'] ) );
					}
				}
				$shows_i_am_on    = ( empty( $show_title ) )? ' None' : implode( ', ', $show_title );
				$dead_or_alive    = ( has_term( 'dead', 'lez_cliches' , $id ) )? 'dead' : 'alive';
				$of_the_day_array = array(
					'name'   => get_the_title( $id ),
					'url'    => get_permalink( $id ),
					'shows'  => $shows_i_am_on,
					'status' => $dead_or_alive,
				);
				break;
			case 'show':
				$show_we_love     = ( get_post_meta( $id, 'lezshows_worthit_show_we_love', true ) )? 'yes' : 'no';
				$of_the_day_array = array(
					'name'  => get_the_title( $id ),
					'url'   => get_permalink( $id ),
					'loved' => $show_we_love,
				);
				break;
		}

		return $of_the_day_array;
	}

}
new LWTV_OTD_JSON();