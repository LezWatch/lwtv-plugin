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
		$format = ( isset( $params['format'] ) && $params['format'] !== '' )? sanitize_title_for_query( $params['format'] ) : 'simple';

		$response = $this->statistics( $stat_type, $format );

		return $response;
	}

	/**
	 * Generate Statistics
	 *
	 * @return array with stats data
	 */
	public static function statistics( $stat_type = 'none', $format = 'simple' ) {

		if ( $stat_type == 'shows' ) {

			if ( $format == 'complex' ) {
				$stats_array = array();

				$showsloop = LWTV_Loops::post_type_query('post_type_shows');

				if ($showsloop->have_posts() ) {
					while ( $showsloop->have_posts() ) {
						$showsloop->the_post();

						$post    = get_post();
						$show_id = $post->ID;

						// Get character info
						$charactersloop = LWTV_Loops::post_meta_query( 'post_type_characters', 'lezchars_show', $show_id, 'LIKE' );
						$havecharcount  = 0;
						$deadcharcount  = 0;

						// Store as array to defeat some stupid with counting and prevent querying the database too many times
						if ($charactersloop->have_posts() ) {
							while ( $charactersloop->have_posts() ) {
								$charactersloop->the_post();

								$charpost    = get_post();
								$char_id     = $charpost->ID;
								$char_shows  = get_post_meta( $char_id, 'lezchars_show', true);
								$shows_array = ( !is_array ( $char_shows ) )? array( $char_shows ) : $char_shows;

								if ( in_array( $show_id, $shows_array  ) && get_post_status ( $char_id ) == 'publish' ) {
									if ( has_term( 'dead', 'lez_cliches', $char_id ) ) $deadcharcount++;
									$havecharcount++;
								}
							}
							wp_reset_query();
						}

						$trigger = ( get_post_meta( $show_id, "lezshows_triggerwarning", true ) )? true : false;
						$stars = ( get_post_meta( $show_id, "lezshows_stars", true ) )? get_post_meta( $show_id, "lezshows_stars", true ) : 'none';

						$stats_array[ get_the_title( $show_id ) ] = array(
							'characters' => $havecharcount,
							'dead'       => $deadcharcount,
							'thumb'      => get_post_meta( $show_id, "lezshows_worthit_rating", true ),
							'trigger'    => $trigger,
							'stars'      => $stars,
						);
					}
					wp_reset_query();
				}
			} else {
				$formats     = 	LWTV_Stats::generate( 'shows', 'formats', 'array');

				$stats_array = array(
					'total'    => wp_count_posts( 'post_type_shows' )->publish,
					'stations' => wp_count_terms( 'lez_stations' ),
					'formats'  => $formats,
				);
			}
		} elseif ( $stat_type == 'characters' ) {
			if ( $format == 'complex' ) {

				$stats_array = array();

				$charactersloop = LWTV_Loops::post_type_query('post_type_characters');

				if ($charactersloop->have_posts() ) {
					while ( $charactersloop->have_posts() ) {
						$charactersloop->the_post();

						$post = get_post();

						$dead = ( get_post_meta( $post->ID, 'lezchars_death_year', true ) )? true: false;
						$dod = get_post_meta( $post->ID, 'lezchars_death_year', true);
						$dod = ( !is_array( $dod) )? array( $dod) : $dod;

						$show_IDs = ( !is_array( get_post_meta( $post->ID, 'lezchars_show', true ) ) )? array( get_post_meta( $post->ID, 'lezchars_show', true ) ) : get_post_meta( $post->ID, 'lezchars_show', true );
						$shows = array();
						foreach ( $show_IDs as $show_ID ) {
							array_push( $shows, get_the_title( $show_ID ) );
						}

						$actors = get_post_meta( $post->ID, 'lezchars_actor', true);
						$actors = ( !is_array( $actors) )? array( $actors) : $actors;

						$stats_array[ get_the_title() ] = array(
							'dead'      => $dead,
							'date-died' => implode(', ', $dod ),
							'sexuality' => implode(', ', wp_get_post_terms($post->ID, 'lez_sexuality', array("fields" => "names") ) ),
							'gender'    => implode(', ', wp_get_post_terms($post->ID, 'lez_gender', array("fields" => "names") ) ),
							'actors'    => implode(', ', $actors ),
							'shows'     => implode(', ', $shows ),
						);
					}
					wp_reset_query();
				}
			} else {
				$dead_count = get_term_by( 'slug', 'dead', 'lez_cliches' );
				$sexuality  = LWTV_Stats::generate( 'characters', 'sexuality', 'array');
				$gender     = LWTV_Stats::generate( 'characters', 'gender', 'array');

				$stats_array = array(
					'total'     => wp_count_posts( 'post_type_characters' )->publish,
					'dead'      => $dead_count->count,
					'sexuality' => $sexuality,
					'gender'    => $gender,
				);

			}
		} elseif ( $stat_type == 'death' )  {

			if ( $format == 'complex' ) {
				$stats_array = array(
					'shows'     => LWTV_Stats::generate( 'characters', 'dead-shows', 'array' ),
					'sexuality' => LWTV_Stats::generate( 'characters', 'dead-sex', 'array' ),
					'gender'    => LWTV_Stats::generate( 'characters', 'dead-gender', 'array' ),
					'roles'     => LWTV_Stats::generate( 'characters', 'dead-roles', 'array' ),
				);

			} elseif ($format == 'years' ) {

				$stats_array = LWTV_Stats::generate( 'characters', 'dead-years', 'array' );

			} else {

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
			}

		} else {
			$stats_array = array(
				'shows'      => wp_count_posts( 'post_type_shows' )->publish,
				'characters' => wp_count_posts( 'post_type_characters' )->publish,
			);
		}

		$return = $stats_array;

		return $return;

	}


}
new LWTV_Stats_JSON();