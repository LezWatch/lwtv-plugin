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
	 *   - /lwtv/v1/stats/[shows|characters]
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
	 * Generate Array
	 *
	 * @return array with data
	 */
	static function generate_array( $subject, $data ) {

		// Bail early if we're not an approved subject matter
		if ( !in_array( $subject, array('characters', 'shows') ) ) exit;

		// Build Variables
		$array = array();
		$post_type = 'post_type_'.$subject;
		$count = wp_count_posts( $post_type )->publish + wp_count_posts( $post_type )->draft;
		$taxonomy = 'lez_'.$data;

		// The following are simple taxonomy arrays
		if ( $data == 'cliches' ) $array = self::tax_array( $post_type, $taxonomy );
		if ( $data == 'sexuality' ) $array = self::tax_array( $post_type, $taxonomy );
		if ( $data == 'gender' ) $array = self::tax_array( $post_type, $taxonomy );
		if ( $data == 'tropes' ) $array = self::tax_array( $post_type, $taxonomy );
		if ( $data == 'formats' ) $array = self::tax_array( $post_type, $taxonomy );

		if ( $data == 'dead-sex' ) $array = self::tax_dead_array( $post_type, 'lez_sexuality' );
		if ( $data == 'dead-gender' ) $array = self::tax_dead_array( $post_type, 'lez_gender' );
		if ( $data == 'dead-roles' ) $array = self::meta_tax_dead_array( $post_type, array( 'regular', 'recurring', 'guest' ), 'lezchars_type' );
		if ( $data == 'dead-shows' ) $array = self::dead_shows();
		if ( $data == 'dead-years' ) $array = self::death_year();


		// The following are simple meta arrays
		if ( $data == 'roles' ) $array = self::meta_array( $post_type, array( 'regular', 'recurring', 'guest' ), 'lezchars_type', $data );
		if ( $data == 'thumbs' ) $array = self::meta_array( $post_type, array( 'Yes', 'No', 'Meh' ), 'lezshows_worthit_rating', $data );

		return $array;
	}

	/*
	 * Statistics Taxonomy Array
	 *
	 * Generate array to parse taxonomy content
	 *
	 * @param string $post_type Post Type to be search
	 * @param string $taxonomy Taxonomy to be searched
	 * @param string $terms The terms to be matched (default empty)
	 * @param string $operator Search operator (default IN)
	 *
	 * @return array
	 */
	static function tax_array( $post_type, $taxonomy, $terms = '', $operator = 'IN' ) {
		$array = array();

		// If no term provided, use get_terms for the taxonomy
		$taxonomies = ( $terms == '' )? get_terms( $taxonomy ) : array($terms);

		foreach ( $taxonomies as $term ) {
			$term_link = get_term_link( $term );
			$term_slug = ( $terms == '' )? $term->slug : $terms;
			$term_name = ( $terms == '' )? $term->name : $terms;
			$count_terms_query = LWTV_Loops::tax_query( $post_type, $taxonomy, 'slug', $term_slug, $operator );
			$term_count = $count_terms_query->post_count;
			$array[$term_name] = $term_count;
		}
		return $array;
	}

	/*
	 * Statistics Meta Array
	 *
	 * Generate array to parse post meta data
	 *
	 * @param string $post_type Post Type to be search
	 * @param array $meta_array Meta terms to loop through
	 * @param string $key Post Meta Key name (i.e. lezchars_type)
	 * @param string $data The data 'subject' - used to generate the URLs
	 * @param string $compare The type of comparison (default =)
	 *
	 * @return array
	 */
	static function meta_array( $post_type, $meta_array, $key, $data, $compare = '=' ) {
		$array = array();
		foreach ( $meta_array as $value ) {
			$meta_query = LWTV_Loops::post_meta_query( $post_type, $key, $value, $compare );
			$array[ucfirst($value)] = $meta_query->post_count;
		}
		return $array;
	}

	/*
	 * Statistics Taxonomy Array for DEAD
	 *
	 * Generate array to parse taxonomy content for
	 *
	 * If
	 *
	 * @param string $post_type Post Type to be search
	 * @param string $taxonomy1 Taxonomy to be searched - PRIMARY
	 * @param string $terms The terms to be matched (default empty)
	 * @param string $operator Search operator (default IN)
	 *
	 * @return array
	 */
	static function tax_dead_array( $post_type, $taxonomy ) {
		$array = array();
		$taxonomies = get_terms( $taxonomy );

		foreach ( $taxonomies as $term ) {
			$query = LWTV_Loops::tax_two_query( $post_type, $taxonomy, 'slug', $term->slug, 'lez_cliches', 'slug', 'dead' );
			$array[$term->name] = $term->count;
		}
		return $array;
	}

	/*
	 * Statistics Meta and Taxonomy Array for DEAD
	 *
	 * Generate array to parse taxonomy content as it relates to post metas
	 *
	 * @param string $post_type Post Type to be search
	 * @param array $meta_array Meta terms to loop through
	 * @param string $key Post Meta Key name (i.e. lezchars_type)
	 * @param string $taxonomy Taxonomy to restrict to (default lez_cliches)
	 * @param string $field Taxonomy to restrict to (default lez_cliches)
	 *
	 * @return array
	 */
	static function meta_tax_dead_array( $post_type, $meta_array, $key, $taxonomy = 'lez_cliches', $field = 'dead' ) {
		$array = array();

		foreach ( $meta_array as $value ) {
			$query = LWTV_Loops::post_meta_and_tax_query( $post_type, $key, $value, $taxonomy, 'slug', $field );
			$array[ucfirst($value)] =  $query->post_count;
		}
		return $array;
	}

	/*
	 * Statistics Death on Shows
	 *
	 * Death is insane. This is how to figure out who died on what show.
	 * We can use it to determine how many shows have ALL dead queers, etc.
	 * It's fucked up. I'm sorry.
	 *
	 * @param string $format The format of our output
	 *
	 * @return array
	 */
	static function dead_shows( ) {

		// Dead Queers Query
		$dead_queers_query = LWTV_Loops::tax_query( 'post_type_characters', 'lez_cliches', 'slug', 'dead' );

		// Shows With Dead Query
		$dead_shows_query = LWTV_Loops::tax_query( 'post_type_shows', 'lez_tropes', 'slug', 'dead-queers' );

		// Shows With NO Dead Query
		$alive_shows_query = LWTV_Loops::tax_query( 'post_type_shows', 'lez_tropes', 'slug', 'dead-queers', 'NOT IN' );

		// Starting Stats
		$alldead  = 0;
		$somedead = 0;
		$nonedead = $alive_shows_query->post_count;

		if ($dead_shows_query->have_posts() ) {
			while ( $dead_shows_query->have_posts() ) {
				$dead_shows_query->the_post();

				$show_id = get_the_ID();

				$death_loop = LWTV_Loops::post_meta_query( 'post_type_characters', 'lezchars_show', $show_id, 'LIKE' );
				if ($death_loop->have_posts() ) {

					$fulldeathcount = 0;
					$chardeathcount = 0;

					while ($death_loop->have_posts()) {
						$death_loop->the_post();
						if ( !is_array (get_post_meta(get_the_ID(), 'lezchars_show', true)) ) {
							$shows_array = array( get_post_meta(get_the_ID(), 'lezchars_show', true) );
						} else {
							$shows_array = get_post_meta(get_the_ID(), 'lezchars_show', true);
						}

						// Because shows are arrays, we have to check if the person REALLY belongs to this show
						if ( in_array( $show_id, $shows_array ) ) {
							$chardeathcount++;
						}

						// If they really belong to the show AND are really most sincerly dead, here you go
						if ( has_term( 'dead', 'lez_cliches', get_the_ID() ) && in_array( $show_id, $shows_array ) ) {
							$fulldeathcount++;
						}
					}

					if ( $fulldeathcount == $chardeathcount ) {
						$alldead++;
					} elseif ( $fulldeathcount <= $chardeathcount ) {
						$somedead++;
					}

					wp_reset_query();
				}
			}
			wp_reset_query();
		}

		$array = array (
			"all"  => $alldead,
			"some" => $somedead,
			"none" => $nonedead,
		);

		return $array;
	}

	/*
	 * Statistics Death By Year
	 *
	 * Death is insane. This is just looping a lot of things to sort
	 * out who died in what year, so we can use it by other functions
	 *
	 * @return array
	 */
	static function death_year() {
		// Death by year
		$year_first = 1931;
		$year_deathlist_array = array();
		foreach (range(date('Y'), $year_first) as $x) {
			$year_deathlist_array[$x] = $x;
		}

		$year_death_array = array();
		foreach ( $year_deathlist_array as $year ) {
			$year_death_query = LWTV_Loops::post_meta_and_tax_query( 'post_type_characters', 'lezchars_death_year', $year, 'lez_cliches', 'slug', 'dead', 'REGEXP' );

			if ( $year_death_query->post_count >= '1' ) {
				$year_death_array[$year] = $year_death_query->post_count;
			}
		}
		return $year_death_array;
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
				$formats     = self::generate_array( 'shows', 'formats');

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
				$sexuality  = self::generate_array( 'characters', 'sexuality' );
				$gender     = self::generate_array( 'characters', 'gender' );

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
					'shows'     => self::generate_array( 'characters', 'dead-shows' ),
					'sexuality' => self::generate_array( 'characters', 'dead-sex' ),
					'gender'    => self::generate_array( 'characters', 'dead-gender' ),
					'roles'     => self::generate_array( 'characters', 'dead-roles' ),
				);

			} elseif ($format == 'years' ) {

				$stats_array = self::generate_array( 'characters', 'dead-years' );

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