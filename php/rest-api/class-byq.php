<?php
/**
 * Description: REST-API: Bury Your Queers
 *
 * The code that runs the Bury Your Queers API service
 * - Last Death - "It has been X days since the last WLW Death"
 * - On This Day - "On this day, X died"
 *
 */

namespace LWTV\Rest_API;

class BYQ {

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
	 *   - /lwtv/v1/last-death/
	 *   - /lwtv/v1/on-this-day/
	 *   - /lwtv/v1/when-died/
	 */
	public function rest_api_init() {

		register_rest_route(
			'lwtv/v1',
			'/last-death',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'last_death_rest_api_callback' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'lwtv/v1',
			'/on-this-day/',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'on_this_day_rest_api_callback' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'lwtv/v1',
			'/on-this-day/(?P<date>[\d]{2}-[\d]{2})',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'on_this_day_rest_api_callback' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'lwtv/v1',
			'/when-died/',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'when_died_rest_api_callback' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'lwtv/v1',
			'/when-died/(?P<name>[a-zA-Z0-9-]+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'when_died_rest_api_callback' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Rest API Callback for Last Death
	 */
	public function last_death_rest_api_callback() {
		$response = $this->last_death();
		return $response;
	}

	/**
	 * Rest API Callback for On This Day
	 *v1
	 */
	public function on_this_day_rest_api_callback( $data ) {
		$params   = $data->get_params();
		$this_day = ( isset( $params['date'] ) && '' !== $params['date'] ) ? $params['date'] : 'today';
		$response = $this->on_this_day( $this_day, 'json' );
		return $response;
	}

	/**
	 * Rest API Callback for When someone Died
	 */
	public function when_died_rest_api_callback( $data ) {
		$params   = $data->get_params();
		$name     = ( isset( $params['name'] ) && '' !== $params['name'] ) ? $params['name'] : 'no-name';
		$response = $this->when_died( $name );
		return $response;
	}

	/**
	 * Generate the massive list of all the dead
	 *
	 * This is a separate function because otherwise I use the same call twice
	 * and that's stupid
	 */
	public function list_of_dead_characters( $dead_chars_loop ) {

		$death_list_array = array();

		if ( $dead_chars_loop->have_posts() ) {
			// Loop through characters to build our list
			foreach ( $dead_chars_loop->posts as $dead_char ) {
				// Date(s) character died
				$died_date       = get_post_meta( $dead_char->ID, 'lezchars_death_year', true );
				$died_date_array = array();

				if ( ! is_array( $died_date ) ) {
					$died_date = array( $died_date );
				}

				// For each death date, create an item in an array with the unix timestamp
				// We default to 8pm for prime-time reasons.
				foreach ( $died_date as $date ) {
					$date_parse        = date_parse_from_format( 'Y-m-d', $date );
					$died_date_array[] = mktime( '20', $date_parse['minute'], $date_parse['second'], $date_parse['month'], $date_parse['day'], $date_parse['year'] );
				}

				// Grab the highest date (aka most recent)
				$died = max( $died_date_array );

				// Get the post slug
				$post_slug = get_post_field( 'post_name', get_post( $dead_char ) );

				// Get the shows
				$all_shows = get_post_meta( $dead_char->ID, 'lezchars_show_group', true );
				$show_ids  = array();
				foreach ( $all_shows as $show ) {
					// Remove the Array.
					if ( is_array( $show['show'] ) ) {
						$show['show'] = $show['show'][0];
					}

					$show_ids[] = $show['show'];
				}

				// Add this character to the array
				$death_list_array[ $post_slug ] = array(
					'id'    => $dead_char->ID,
					'slug'  => $post_slug,
					'name'  => get_the_title( $dead_char ),
					'url'   => get_the_permalink( $dead_char ),
					'shows' => $show_ids,
					'died'  => $died,
					'date'  => $died_date,
				);
			}

			// phpcs:disable
			// Reorder all the dead to sort by DoD
			uasort( $death_list_array, function( $a, $b ) {

				// Spaceship Needs PHP 7.1+
				return $a['died'] <=> $b['died'];
			});
			// phpcs:enable
		}

		return $death_list_array;
	}

	/**
	 * Generate List of Dead
	 *
	 * @return array with last dead character data
	 */
	public function last_death() {
		$return = '';
		// Get all our dead queers
		$dead_chars_loop  = lwtv_plugin()->queery_taxonomy( 'post_type_characters', 'lez_cliches', 'slug', 'dead' );
		$death_list_array = self::list_of_dead_characters( $dead_chars_loop );

		// Extract the last death
		$last_death = array_slice( $death_list_array, -1, 1, true );
		$last_death = array_shift( $last_death );

		// Calculate the difference between then and now
		if ( isset( $last_death['died'] ) && ! is_null( $last_death['died'] ) ) {
			$diff                = abs( time() - $last_death['died'] );
			$last_death['since'] = $diff;
			$return              = $last_death;
		}

		return $return;
	}

	/**
	 * Generate On This Day
	 *
	 * @return array with character data
	 */
	public function on_this_day( $this_day = 'today', $type = 'json' ) {

		// Default to today
		if ( 'today' === $this_day ) {
			// Create the date with regards to timezones
			$timestamp = time();
			$dt        = new \DateTime( 'now', new \DateTimeZone( LWTV_TIMEZONE ) ); //first argument "must" be a string
			$dt->setTimestamp( $timestamp ); //adjust the object to correct timestamp
			$this_day = $dt->format( 'm-d' );
		}

		// Default to JSON (i.e. what the plugin uses)
		$valid_types = array( 'json', 'tweet' );
		$type        = ( ! in_array( $type, $valid_types, true ) ) ? 'json' : $type;

		// Get all our dead queers
		$dead_chars_loop  = lwtv_plugin()->queery_post_meta( 'post_type_characters', 'lezchars_death_year', '', 'EXISTS' );
		$death_list_array = self::list_of_dead_characters( $dead_chars_loop );

		$died_today_array = array();

		switch ( $type ) {
			case 'tweet':
				$the_dead_array = array();
				foreach ( $death_list_array as $the_dead ) {
					if ( gmdate( 'm-d', $the_dead['died'] ) === $this_day ) {
						$data = $the_dead['name'] . ' (' . gmdate( 'Y', $the_dead['died'] ) . ') -- ' . $the_dead['url'];
						array_push( $the_dead_array, $data );
					}
				}
				if ( empty( $the_dead_array ) ) {
					$content = 'NONE';
				} else {
					$the_dead_string = implode( '\n', $the_dead_array );
					$count_the_dead  = count( $the_dead_array );
					// translators: %s is the number of characters
					$characters = sprintf( _n( '%s character', '%s characters', $count_the_dead ), $count_the_dead );
					$content    = 'On ' . $this_day . ', the following ' . $characters . ' died: \n' . $the_dead_string;
				}
				$died_today_array['content'] = $content;
				break;
			case 'json':
				foreach ( $death_list_array as $the_dead ) {
					if ( gmdate( 'm-d', $the_dead['died'] ) === $this_day ) {
						$died_today_array[ $the_dead['slug'] ] = array(
							'id'   => $the_dead['id'],
							'name' => $the_dead['name'],
							'url'  => $the_dead['url'],
							'died' => gmdate( 'Y', $the_dead['died'] ),
						);
					}
				}
				if ( empty( $died_today_array ) ) {
					$died_today_array['none'] = array(
						'id'   => 0,
						'name' => 'No One',
						'url'  => site_url( '/cliche/dead/' ),
						'died' => 'n/a',
					);
				}
				break;
			default:
				$died_today_array = new \WP_Error( 'invalid', 'An unexpected error has occurred.' );
		}

		return $died_today_array;
	}

	/**
	 * Change search to only work by title
	 *
	 * @access public
	 * @param mixed $search
	 * @param mixed &$wp_query
	 * @return void
	 */
	public function search_by_title_only( $search, &$wp_query ) {
		global $wpdb;
		if ( empty( $search ) ) {
			return $search; // skip processing - no search term in query
		}

		$q         = $wp_query->query_vars;
		$n         = ! empty( $q['exact'] ) ? '' : '%';
		$search    = '';
		$searchand = '';
		foreach ( (array) $q['search_terms'] as $term ) {
			$term      = esc_sql( \wpdb::esc_like( $term ) );
			$search   .= "{$searchand}($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";
			$searchand = ' AND ';
		}
		if ( ! empty( $search ) ) {
			$search = " AND ({$search}) ";
			if ( ! is_user_logged_in() ) {
				$search .= " AND ($wpdb->posts.post_password = '') ";
			}
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
	public function when_died( $name = 'no-name' ) {

		// phpcs:disable
		// Remove <!--fwp-loop--> from output
		add_filter( 'facetwp_is_main_query', function( $is_main_query, $query ) {
			return false;
		}, 10, 2 );


		// Force to search ONLY by title
		add_filter( 'posts_search', function( $search, &$wp_query ) {
			global $wpdb;
			if ( empty( $search ) ) {
				return $search; // skip processing - no search term in query
			}

			$q         = $wp_query->query_vars;
			$n         = ! empty( $q['exact'] ) ? '' : '%';
			$search    = '';
			$searchand = '';
			foreach ( (array) $q['search_terms'] as $term ) {
				$term      = esc_sql( \wpdb::esc_like( $term ) );
				$search   .= "{$searchand}($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";
				$searchand = ' AND ';
			}
			if ( ! empty( $search ) ) {
				$search = " AND ({$search}) ";
				if ( ! is_user_logged_in() ) {
					$search .= " AND ($wpdb->posts.post_password = '') ";
				}
			}
			return $search;
		}, 500, 2 );
		// phpcs:enable

		$noname = array(
			'id'    => 0,
			'name'  => 'No Name',
			'shows' => 'None',
			'url'   => 'None',
			'died'  => 'None',
		);

		$name = str_replace( '-', ' ', $name );

		if ( 'no-name' === $name ) {
			$when_died_array['none'] = $noname;
		} else {
			$args = array(
				's'              => $name,
				'post_type'      => 'post_type_characters',
				'post_status'    => 'publish',
				'posts_per_page' => 100,
				'no_found_rows'  => true,
			);

			$the_character = new \WP_Query( $args );

			if ( $the_character->have_posts() ) {

				while ( $the_character->have_posts() ) {

					$the_character->the_post();

					$died = 'alive';
					if ( get_post_meta( get_the_ID(), 'lezchars_death_year', true ) ) {
						$died = get_post_meta( get_the_ID(), 'lezchars_death_year', true );
						if ( ! is_array( $died ) ) {
							$died = array( $died );
						}
						$died = implode( ', ', $died );
					}

					$shows_all = get_post_meta( get_the_ID(), 'lezchars_show_group', true );
					$shows     = '';
					foreach ( $shows_all as $show ) {
						// Remove the Array.
						if ( is_array( $show['show'] ) ) {
							$show['show'] = $show['show'][0];
						}

						$shows .= get_the_title( $show['show'] ) . ', ';
					}
					$shows = rtrim( $shows, ', ' );

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
				$when_died_array['none'] = $noname;
			}
		}

		// If there was an exact match, use that one
		if ( array_key_exists( $name, $when_died_array ) ) {
			$when_died_array = $when_died_array[ $name ];
		}

		$return = $when_died_array;

		return $return;
	}
}
