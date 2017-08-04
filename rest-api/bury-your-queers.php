<?php
/*
Description: REST-API: Bury Your Queers

The code that runs the Bury Your Queers API service
  - Last Death - "It has been X days since the last WLW Death"
  - On This Day - "On this day, X died"

Version: 1.2
Author: Mika Epstein
*/

if ( ! defined('WPINC' ) ) die;

/**
 * class LWTV_BYQ_JSON
 *
 * The basic constructor class that will set up our JSON API.
 */
class LWTV_BYQ_JSON {

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
	 *   - /lwtv/v1/last-death/
	 *   - /lwtv/v1/on-this-day/
	 */
	public function rest_api_init() {

		register_rest_route( 'lwtv/v1', '/last-death', array(
			'methods' => 'GET',
			'callback' => array( $this, 'last_death_rest_api_callback' ),
		) );

		register_rest_route( 'lwtv/v1', '/on-this-day/', array(
			'methods' => 'GET',
			'callback' => array( $this, 'on_this_day_rest_api_callback' ),
		) );

		register_rest_route( 'lwtv/v1', '/on-this-day/(?P<date>[\d]{2}-[\d]{2})', array(
			'methods' => 'GET',
			'callback' => array( $this, 'on_this_day_rest_api_callback' ),
		) );

		register_rest_route( 'lwtv/v1', '/when-died/', array(
			'methods' => 'GET',
			'callback' => array( $this, 'when_died_rest_api_callback' ),
		) );

		register_rest_route( 'lwtv/v1', '/when-died/(?P<name>[a-zA-Z0-9-]+)', array(
			'methods' => 'GET',
			'callback' => array( $this, 'when_died_rest_api_callback' ),
		) );

	}

	/**
	 * Rest API Callback for Last Death
	 */
	public function last_death_rest_api_callback( $data ) {
		$response = $this->last_death();
		return $response;
	}

	/**
	 * Rest API Callback for On This Day
	 */
	public function on_this_day_rest_api_callback( $data ) {
		$params = $data->get_params();
		$this_day = ( isset( $params['date'] ) && $params['date'] !== '' )? $params['date'] : 'today';
		$response = $this->on_this_day( $this_day );
		return $response;
	}

	/**
	 * Rest API Callback for When someone Died
	 */
	public function when_died_rest_api_callback( $data ) {
		$params   = $data->get_params();
		$name     = ( isset( $params['name'] ) && $params['name'] !== '' )? $params['name'] : 'no-name';
		$response = $this->when_died( $name );
		return $response;
	}

	/**
	 * Generate the massive list of all the dead
	 *
	 * This is a separate function becuase otherwise I use the same call twice
	 * and that's stupid
	 */
	public static function list_of_dead_characters( $dead_chars_loop ) {

		$death_list_array = array();

		if ( $dead_chars_loop->have_posts() ) {
			// Loop through characters to build our list
			foreach( $dead_chars_loop->posts as $dead_char ) {
				// Date(s) character died
				$died_date = get_post_meta( $dead_char->ID, 'lezchars_death_year', true);
				$died_date_array = array();

				if ( !is_array( $died_date ) ) $died_date = array( $died_date );

				// For each death date, create an item in an array with the unix timestamp
				foreach ( $died_date as $date ) {
					$date_parse = date_parse_from_format( 'm/d/Y' , $date);
					$died_date_array[] = mktime( $date_parse['hour'], $date_parse['minute'], $date_parse['second'], $date_parse['month'], $date_parse['day'], $date_parse['year'] );
				}

				// Grab the highest date (aka most recent)
				$died = max( $died_date_array );

				// Get the post slug
				$post_slug = get_post_field( 'post_name', get_post( $dead_char ) );

				// Add this character to the array
				$death_list_array[$post_slug] = array(
					'id'   => $dead_char->ID,
					'slug' => $post_slug,
					'name' => get_the_title( $dead_char ),
					'url'  => get_the_permalink( $dead_char ),
					'died' => $died,
				);
			}

			// Reorder all the dead to sort by DoD
			uasort($death_list_array, function($a, $b) {
				
				// Spaceship doesn't work
				// return $a['died'] <=> $b['died'];
				
				$return = '0';
				if ( $a['died'] < $b['died'] ) $return = '-1';
				if ( $a['died'] > $b['died'] ) $return = '1';
				return $return;
			});
		}

		return $death_list_array;
	}

	/**
	 * Generate List of Dead
	 *
	 * @return array with last dead character data
	 */
	public static function last_death() {
		// Get all our dead queers
		$dead_chars_loop  = LWTV_Loops::tax_query( 'post_type_characters' , 'lez_cliches', 'slug', 'dead');
		$death_list_array = self::list_of_dead_characters( $dead_chars_loop );

		// Extract the last death
		$last_death = array_slice($death_list_array, -1, 1, true);
		$last_death = array_shift($last_death);

		// Calculate the difference between then and now
		$diff = abs( time() - $last_death['died'] );
		$last_death['since'] = $diff;

		$return = $last_death;

		return $return;
	}

	/**
	 * Generate On This Day
	 *
	 * @return array with character data
	 */
	public static function on_this_day( $this_day = 'today' ) {

		if ( $this_day == 'today' ) {
			$this_day = date('m-d');
		}

		// Get all our dead queers
		$dead_chars_loop  = LWTV_Loops::post_meta_query( 'post_type_characters', 'lezchars_death_year', '', 'EXISTS' );
		$death_list_array = self::list_of_dead_characters( $dead_chars_loop );

		$died_today_array = array();

		foreach ( $death_list_array as $the_dead ) {
			if ( $this_day == date('m-d', $the_dead['died'] ) ) {
				$died_today_array[ $the_dead['slug'] ] = array(
					'id'   => $the_dead['id'],
					'name' => $the_dead['name'],
					'url'  => $the_dead['url'],
					'died' => date( 'Y', $the_dead['died'] ),
				);
			}
		}

		if ( empty( $died_today_array ) ) {
			$died_today_array[ 'none' ] = array(
				'id'   => 0,
				'name' => 'No One',
				'url'  => site_url( '/cliche/dead/' ),
				'died' => date('m-d'),
			);
		}

		$return = $died_today_array;

		return $return;
	}


	/**
	 * Change search to only work by title
	 *
	 * @access public
	 * @param mixed $search
	 * @param mixed &$wp_query
	 * @return void
	 */
	public static function search_by_title_only( $search, &$wp_query ) {
		global $wpdb;
		if ( empty( $search ) )
			return $search; // skip processing - no search term in query

		$q = $wp_query->query_vars;
		$n = ! empty( $q['exact'] ) ? '' : '%';
		$search =
		$searchand = '';
		foreach ( (array) $q['search_terms'] as $term ) {
			$term = esc_sql( like_escape( $term ) );
			$search .= "{$searchand}($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";
			$searchand = ' AND ';
		}
		if ( ! empty( $search ) ) {
			$search = " AND ({$search}) ";
			if ( ! is_user_logged_in() )
				$search .= " AND ($wpdb->posts.post_password = '') ";
		}
		return $search;
	}

	/**
	 * Generate when a character died
	 *
	 * If no name is passed, kick back last death
	 *
	 * @return array with character data
	 */
	public static function when_died( $name = 'no-name' ) {

		// Remove <!--fwp-loop--> from output
		add_filter( 'facetwp_is_main_query', function( $is_main_query, $query ) { return false; }, 10, 2 );

		// Force to search ONLY by title
		add_filter( 'posts_search', function( $search, &$wp_query ) {
			global $wpdb;
			if ( empty( $search ) )
				return $search; // skip processing - no search term in query

			$q = $wp_query->query_vars;
			$n = ! empty( $q['exact'] ) ? '' : '%';
			$search =
			$searchand = '';
			foreach ( (array) $q['search_terms'] as $term ) {
				$term = esc_sql( like_escape( $term ) );
				$search .= "{$searchand}($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";
				$searchand = ' AND ';
			}
			if ( ! empty( $search ) ) {
				$search = " AND ({$search}) ";
				if ( ! is_user_logged_in() )
					$search .= " AND ($wpdb->posts.post_password = '') ";
			}
			return $search;
		} , 500, 2 );

		$noname = array(
			'id'    => 0,
			'name'  => 'No Name',
			'shows' => 'None',
			'url'   => 'None',
			'died'  => 'None',
		);

		$name = str_replace( '-', ' ', $name);

		if ( $name == 'no-name' ) {
			$when_died_array[ 'none' ] = $noname;
		} else {
			$args = array(
				's'              => $name,
				'post_type'      => 'post_type_characters',
				'post_status'    => 'publish',
				'posts_per_page' => 100,
			);

			$the_character = new WP_Query( $args );

			if ( $the_character->have_posts() ) {

				while ( $the_character->have_posts() ) {

					$the_character->the_post();

					$died = 'alive';
					if ( get_post_meta( get_the_ID(), 'lezchars_death_year', true) ) {
						$died = get_post_meta( get_the_ID(), 'lezchars_death_year', true);
						if ( !is_array ( $died ) ) $died = array( $died );
						$died = implode(", ", $died );
					}

					$shows_all = get_post_meta( get_the_ID(), 'lezchars_show_group', true );
					$shows = '';
					foreach ( $shows_all as $show ) {
						$shows .= get_the_title( $show['show'] ) .', ';
					}
					$shows = rtrim( $shows , ', ');

					$when_died_array[ get_post_field( 'post_name' ) ] = array(
						'id'    => get_the_id(),
						'name'  => get_the_title(),
						'shows' => $shows,
						'url'   => get_the_permalink(),
						'died'  => $died,
					);

				}
				wp_reset_postdata();
			} else {
				$when_died_array[ 'none' ] = $noname;
			}
		}

		// If there was an exact match, use that one
		if( array_key_exists( $name, $when_died_array ) ) {
			$when_died_array = $when_died_array[ $name ];
		}

		$return = $when_died_array;

		return $return;
	}

}
new LWTV_BYQ_JSON();