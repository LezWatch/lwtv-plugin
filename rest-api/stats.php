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
		$valid_type   = array( 'characters', 'actors', 'shows', 'death' );
		$valid_format = array( 'simple', 'complex', 'years' );

		// Sanity Check
		$stat_type = ( !in_array( $stat_type, $valid_type ) )? 'characters' : $stat_type;
		$format    = ( !in_array( $format, $valid_format ) )? 'simple' : $format;

		switch ( $stat_type ) {
			case 'first-year':
				$stats_array = FIRST_LWTV_YEAR;
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
		$valid_format = array( 'simple', 'complex' );
		$format       = ( !in_array( $format, $valid_format ) )? 'simple' : $format;
		$stats_array  = array();

		switch ( $format ) {
			case 'complex':
				$showsloop = LWTV_Loops::post_type_query('post_type_actors');

				if ($showsloop->have_posts() ) {
					while ( $showsloop->have_posts() ) {
						$showsloop->the_post();

						$post     = get_post();
						$actor_id = $post->ID;

						// Get character info
						$charactersloop = LWTV_Loops::post_meta_query( 'post_type_characters', 'lezchars_actor', $actor_id, 'LIKE' );
						$havecharcount  = 0;
						$deadcharcount  = 0;

						// Store as array to defeat some stupid with counting and prevent querying the database too many times
						if ($charactersloop->have_posts() ) {
							while ( $charactersloop->have_posts() ) {
								$charactersloop->the_post();

								$charpost     = get_post();
								$char_id      = $charpost->ID;
								$actors_array = get_post_meta( $char_id, 'lezchars_actor', true);

								if ( $actors_array !== '' && get_post_status ( $char_id ) == 'publish' ) {
									foreach( $actors_array as $char_actor ) {
										if ( $char_actor == $actor_id ) {
											$havecharcount++;
											if ( has_term( 'dead', 'lez_cliches', $char_id ) ) $deadcharcount++;
										}
									}
								}
							}
							wp_reset_query();
						}

						$stats_array[ get_the_title( $actor_id ) ] = array(
							'id'         => $actor_id,
							'characters' => $havecharcount,
							'dead'       => $deadcharcount,
							'url'        => get_the_permalink( $actor_id ),
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
		$valid_format = array( 'simple', 'complex' );
		$format       = ( !in_array( $format, $valid_format ) )? 'simple' : $format;
		$stats_array  = array();

		switch ( $format ) {
			case 'complex':
				$stats_array = array();

				$charactersloop = LWTV_Loops::post_type_query('post_type_characters');

				if ($charactersloop->have_posts() ) {
					while ( $charactersloop->have_posts() ) {
						$charactersloop->the_post();

						$post = get_post();

						$dead = ( get_post_meta( $post->ID, 'lezchars_death_year', true ) )? true: false;
						$dod = get_post_meta( $post->ID, 'lezchars_death_year', true);
						$dod = ( !is_array( $dod) )? array( $dod) : $dod;

						$show_IDs = get_post_meta( $post->ID, 'lezchars_show_group', true );
						$shows = array();
						if ( $show_IDs !== '' ) {
							foreach ( $show_IDs as $each_show ) {
								array_push( $shows, get_the_title( $each_show['show'] ) );
							}
						}

						$actors_IDs = get_post_meta( $post->ID, 'lezchars_actor', true);
						$actors = array();
						if ( $actors_IDs !== '' ) {
							foreach ( $actors_IDs as $each_actor ) {
								array_push( $actors, get_the_title( $each_actor ) );
							}
						}

						$stats_array[ get_the_title() ] = array(
							'id'        => $post->ID,
							'dead'      => $dead,
							'date-died' => implode(', ', $dod ),
							'sexuality' => implode(', ', wp_get_post_terms($post->ID, 'lez_sexuality', array( 'fields' => 'names' ) ) ),
							'gender'    => implode(', ', wp_get_post_terms($post->ID, 'lez_gender', array( 'fields' => 'names' ) ) ),
							'romantic'  => implode(', ', wp_get_post_terms($post->ID, 'lez_romantic', array( 'fields' => 'names' ) ) ),
							'actors'    => implode(', ', $actors ),
							'shows'     => implode(', ', $shows ),
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

		// Remove <!--fwp-loop--> from output
		add_filter( 'facetwp_is_main_query', function( $is_main_query, $query ) { return false; }, 10, 2 );

		// Validate Data
		$valid_format = array( 'simple', 'complex' );
		$format       = ( !in_array( $format, $valid_format ) )? 'simple' : $format;
		$stats_array  = array();

		switch ( $format ) {
			case 'complex':
				$showsloop = LWTV_Loops::post_type_query('post_type_shows');

				if ($showsloop->have_posts() ) {
					while ( $showsloop->have_posts() ) {
						$showsloop->the_post();

						$post    = get_post();
						$show_id = $post->ID;

						// Get character info
						$charactersloop = LWTV_Loops::post_meta_query( 'post_type_characters', 'lezchars_show_group', $show_id, 'LIKE' );
						$havecharcount  = 0;
						$deadcharcount  = 0;

						// Store as array to defeat some stupid with counting and prevent querying the database too many times
						if ($charactersloop->have_posts() ) {
							while ( $charactersloop->have_posts() ) {
								$charactersloop->the_post();

								$charpost    = get_post();
								$char_id     = $charpost->ID;
								$shows_array = get_post_meta( $char_id, 'lezchars_show_group', true);

								if ( $shows_array !== '' && get_post_status ( $char_id ) == 'publish' ) {
									foreach( $shows_array as $char_show ) {
										if ( $char_show['show'] == $show_id ) {
											$havecharcount++;
											if ( has_term( 'dead', 'lez_cliches', $char_id ) ) $deadcharcount++;
										}
									}
								}
							}
							wp_reset_query();
						}

						$loved   = ( get_post_meta( $show_id, 'lezshows_worthit_show_we_love', true ) )? 'yes' : 'no';

						$stats_array[ get_the_title( $show_id ) ] = array(
							'id'         => $show_id,
							'characters' => $havecharcount,
							'dead'       => $deadcharcount,
							'thumb'      => get_post_meta( $show_id, 'lezshows_worthit_rating', true ),
							'trigger'    => implode(', ', wp_get_post_terms( $show_id, 'lez_triggers', array( 'fields' => 'names' ) ) ),
							'star'       => implode(', ', wp_get_post_terms( $show_id, 'lez_stars', array( 'fields' => 'names' ) ) ),
							'loved'      => $loved,
							'url'        => get_the_permalink( $show_id ),
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