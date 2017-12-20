<?php
/*
Description: REST-API - Stats output

So other people can

The code that runs the Stats API service
  - Shows
  - Characters

Version: 1.0
Author: Mika Epstein
*/

if ( ! defined('WPINC' ) ) die;

/**
 * class LWTV_Stats_JSON
 *
 * The basic constructor class that will set up our JSON API.
 */
class LWTV_Stats_JSON {

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
	 *   - /lwtv/v1/stats/[shows|characters|death]/[simple|complex|years]
	 */
	public function rest_api_init() {

		// Basic Stats
		register_rest_route( 'lwtv/v1', '/stats/', array(
			'methods' => 'GET',
			'callback' => array( $this, 'stats_rest_api_callback' ),
		) );

		// Stat Types
		register_rest_route( 'lwtv/v1', '/stats/(?P<type>[a-zA-Z0-9-]+)', array(
			'methods' => 'GET',
			'callback' => array( $this, 'stats_rest_api_callback' ),
		) );

		// Stat Types and Format
		register_rest_route( 'lwtv/v1', '/stats/(?P<type>[a-zA-Z0-9-]+)/(?P<format>[a-zA-Z0-9-]+)', array(
			'methods' => 'GET',
			'callback' => array( $this, 'stats_rest_api_callback' ),
		) );

	}

	/**
	 * Rest API Callback for Statistics
	 */
	public function stats_rest_api_callback( $data ) {
		$params = $data->get_params();

		$stat_type = ( isset( $params['type'] ) && $params['type'] !== '' )? sanitize_title_for_query( $params['type'] ) : 'none';
		$format    = ( isset( $params['format'] ) && $params['format'] !== '' )? sanitize_title_for_query( $params['format'] ) : 'simple';

		$response  = $this->statistics( $stat_type, $format );

		return $response;
	}

	/**
	 * Generate Statistics
	 *
	 * @return array with stats data
	 */
	public static function statistics( $stat_type = 'characters', $format = 'simple' ) {

		// Remove <!--fwp-loop--> from output
		add_filter( 'facetwp_is_main_query', function( $is_main_query, $query ) { return false; }, 10, 2 );

		// Valid Data
		$valid_type   = array( 'characters', 'actors', 'shows', 'death', 'first-year' );
		$valid_format = array( 'simple', 'complex', 'years', 'cliches', 'tropes', 'worth-it', 'stars', 'formats', 'triggers', 'loved', 'nations', 'sexuality', 'gender', 'romantic', 'genres' );

		// Sanity Check
		if ( !in_array( $stat_type, $valid_type ) || !in_array( $format, $valid_format ) ) 
			return wp_send_json_error( 'No route was found matching the URL and request method.', 404 );

		switch ( $stat_type ) {
			case 'first-year':
				$stats_array = array ( 'first' => FIRST_LWTV_YEAR );
				break;
			case 'shows':
				$stats_array = self::get_shows( $format );
				break;
			case 'characters':
				$stats_array = self::get_characters( $format );
				break;
			case 'actors':
				$stats_array = self::get_actors( $format );
				break;
			case 'death':
				$stats_array = self::get_death( $format );
				break;
			default:
				$stats_array = self::get_characters( 'simple' );
		}

		return $stats_array;
	}

	/**
	 * get_actors function.
	 * 
	 * @access public
	 * @static
	 * @param string $format (default: 'simple')
	 * @return array
	 */
	static function get_actors( $format = 'simple' ) {

		// Remove <!--fwp-loop--> from output
		add_filter( 'facetwp_is_main_query', function( $is_main_query, $query ) { return false; }, 10, 2 );

		// Validate Data
		$valid_format = array( 'simple', 'complex', 'sexuality', 'gender' );

		// Sanity Check
		if ( !in_array( $format, $valid_format ) ) 
			return wp_send_json_error( 'No route was found matching the URL and request method.', 404 );

		$stats_array  = array();

		switch ( $format ) {
			case 'sexuality':
				$stats_array = LWTV_Stats::generate( 'actors', 'actor_sexuality', 'array' );
				break;
			case 'gender':
				$stats_array = LWTV_Stats::generate( 'actors', 'actor_gender', 'array' );
				break;
			case 'complex':
				$the_loop = LWTV_Loops::post_type_query('post_type_actors');

				if ( $the_loop->have_posts() ) {
					while ( $the_loop->have_posts() ) {
						$the_loop->the_post();
						$post = get_post();
						$stats_array[ get_the_title( $post->ID ) ] = array(
							'id'         => $post->ID,
							'characters' => get_post_meta( $post->ID, 'lezactors_char_count', true ),
							'dead_chars' => get_post_meta( $post->ID, 'lezactors_dead_count', true ),
							'url'        => get_the_permalink( $post->ID ),
						);
					}
					wp_reset_query();
				}
				break;
			case 'simple':
				$stats_array  = array(
					'total'     => wp_count_posts( 'post_type_actors' )->publish,
					'gender'    => wp_count_terms( 'lez_actor_gender' ),
					'sexuality' => wp_count_terms( 'lez_actor_sexuality' ),
				);
				break;
		}
		
		return $stats_array;
	}

	/**
	 * get_characters function.
	 * 
	 * @access public
	 * @static
	 * @param string $format (default: 'simple')
	 * @return array
	 */
	static function get_characters( $format = 'simple' ) {

		// Remove <!--fwp-loop--> from output
		add_filter( 'facetwp_is_main_query', function( $is_main_query, $query ) { return false; }, 10, 2 );

		// Validate Data
		$valid_format = array( 'simple', 'complex', 'sexuality', 'gender', 'romantic', 'cliches' );

		// Sanity Check
		if ( !in_array( $format, $valid_format ) ) 
			return wp_send_json_error( 'No route was found matching the URL and request method.', 404 );

		$stats_array  = array();

		switch ( $format ) {
			case 'cliches':
				$stats_array = LWTV_Stats::generate( 'characters', 'cliches', 'array' );
				break;
			case 'sexuality':
				$stats_array = LWTV_Stats::generate( 'characters', 'sexuality', 'array' );
				break;
			case 'gender':
				$stats_array = LWTV_Stats::generate( 'characters', 'gender', 'array' );
				break;
			case 'romantic':
				$stats_array = LWTV_Stats::generate( 'characters', 'romantic', 'array' );
				break;
			case 'complex':
				$stats_array    = array();
				$charactersloop = LWTV_Loops::post_type_query('post_type_characters');
				if ( $charactersloop->have_posts() ) {
					while ( $charactersloop->have_posts() ) {
						$charactersloop->the_post();

						$post   = get_post();
						$died   = get_post_meta( $post->ID, 'lezchars_death_year', true);
						$died   = ( !is_array( $died ) )? array( $died ) : $died;
						$shows  = count( get_post_meta( $post->ID, 'lezchars_show_group', true ) );
						$actors = count( get_post_meta( $post->ID, 'lezchars_actor', true) );

						$stats_array[ get_the_title() ] = array(
							'id'        => $post->ID,
							'died'      => $died,
							'actors'    => $actors,
							'shows'     => $shows,
							'url'       => get_the_permalink(),
						);
					}
					wp_reset_query();
				}
				break;
			case 'simple':
				$dead_count = get_term_by( 'slug', 'dead', 'lez_cliches' );
				$stats_array  = array(
					'total'       => wp_count_posts( 'post_type_characters' )->publish,
					'dead'        => $dead_count->count,
					'genders'     => wp_count_terms( 'lez_gender' ),
					'sexualities' => wp_count_terms( 'lez_sexuality' ),
					'romantic_o'  => wp_count_terms( 'lez_romantic' ),
					'cliches'     => wp_count_terms( 'lez_cliches' ),
				);
				break;
		}

		return $stats_array;
	}


	/**
	 * get_death function.
	 * 
	 * @access public
	 * @static
	 * @param string $format (default: 'simple')
	 * @return void
	 */
	static function get_death( $format = 'simple' ) {
		// Remove <!--fwp-loop--> from output
		add_filter( 'facetwp_is_main_query', function( $is_main_query, $query ) { return false; }, 10, 2 );

		// Validate Data
		$valid_format = array( 'simple', 'complex', 'years' );
		$format       = ( !in_array( $format, $valid_format ) )? 'simple' : $format;
		$stats_array  = array();

		switch ( $format ) {
			case 'complex':
				$stats_array = array(
					'shows'     => LWTV_Stats::generate( 'characters', 'dead-shows', 'array' ),
					'sexuality' => LWTV_Stats::generate( 'characters', 'dead-sex', 'array' ),
					'gender'    => LWTV_Stats::generate( 'characters', 'dead-gender', 'array' ),
					// Current broken :(
					//'roles'     => LWTV_Stats::generate( 'characters', 'dead-roles', 'array' ),
				);
				break;
			case 'years':
				$stats_array = LWTV_Stats::generate( 'characters', 'dead-years', 'array' );
				break;
			case 'simple':
				$dead_chars  = get_term_by( 'slug', 'dead', 'lez_cliches' );
				$dead_shows  = get_term_by( 'slug', 'dead-queers', 'lez_tropes' );
				$stats_array = array(
					'characters'   => array(
						'dead'     => $dead_chars->count,
						'alive'    => ( wp_count_posts( 'post_type_characters' )->publish - $dead_chars->count ),
					),
					'shows'        => array(
						'death'    => $dead_shows->count,
						'no-death' => ( wp_count_posts( 'post_type_shows' )->publish - $dead_shows->count ),
					),
				);
				break;
		}
		
		return $stats_array;
	}

	/**
	 * get_shows function.
	 * 
	 * @access public
	 * @static
	 * @param string $format (default: 'simple')
	 * @return array
	 */
	static function get_shows( $format = 'simple' ) {
		
		global $wpdb;

		// Remove <!--fwp-loop--> from output
		add_filter( 'facetwp_is_main_query', function( $is_main_query, $query ) { return false; }, 10, 2 );

		// Validate Data
		$valid_format = array( 'simple', 'complex', 'nations', 'formats', 'stars', 'triggers', 'loved', 'worth-it', 'tropes', 'genres' );

		// Sanity Check
		if ( !in_array( $format, $valid_format ) ) 
			return wp_send_json_error( 'No route was found matching the URL and request method.', 404 );

		$stats_array  = array();

		switch ( $format ) {
			case 'tropes':
				$stats_array = LWTV_Stats::generate( 'shows', 'tropes', 'array' );
				break;
			case 'nations':
				$stats_array = LWTV_Stats::generate( 'shows', 'country', 'array' );
				break;
			case 'genres':
				$stats_array = LWTV_Stats::generate( 'shows', 'genres', 'array' );
				break;
			case 'triggers':
				$stats_array = LWTV_Stats::generate( 'shows', 'triggers', 'array' );
				break;
			case 'formats':
				$stats_array = LWTV_Stats::generate( 'shows', 'formats', 'array' );
				break;
			case 'stars':
				$stats_array = LWTV_Stats::generate( 'shows', 'stars', 'array' );
				break;
			case 'loved':
				$stats_array = LWTV_Stats::generate( 'shows', 'weloveit', 'array' );
				break;
			case 'worth-it':
				$stats_array = LWTV_Stats::generate( 'shows', 'thumbs', 'array' );
				break;
				case 'complex':
				$showsloop = LWTV_Loops::post_type_query('post_type_shows');

				if ($showsloop->have_posts() ) {
					while ( $showsloop->have_posts() ) {
						$showsloop->the_post();
						$post = get_post();
						$stats_array[ get_the_title( $post->ID ) ] = array(
							'id'         => $post->ID,
							'characters' => get_post_meta( $post->ID, 'lezshows_char_count', true ),
							'dead'       => get_post_meta( $post->ID, 'lezshows_dead_count', true ),
							'thumb'      => get_post_meta( $post->ID, 'lezshows_worthit_rating', true ),
							'trigger'    => implode(', ', wp_get_post_terms( $post->ID, 'lez_triggers', array( 'fields' => 'names' ) ) ),
							'star'       => implode(', ', wp_get_post_terms( $post->ID, 'lez_stars', array( 'fields' => 'names' ) ) ),
							'loved'      => ( ( get_post_meta( $post->ID, 'lezshows_worthit_show_we_love', true ) )? 'yes' : 'no' ),
							'url'        => get_the_permalink( $post->ID ),
						);
					}
					wp_reset_query();
				}
				break;
			case 'simple':
				$stats_array  = array(
					'total'    => wp_count_posts( 'post_type_shows' )->publish,
					'stations' => wp_count_terms( 'lez_stations' ),
					'nations'  => wp_count_terms( 'lez_country' ),
					'formats'  => wp_count_terms( 'lez_formats' ),
					'genres'   => wp_count_terms( 'lez_genres' ),
				);
				break;
		}
		
		return $stats_array;
	}


}
new LWTV_Stats_JSON();