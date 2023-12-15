<?php
/**
 * Description: REST-API: What Happened
 *
 * The code that runs the What Happened API service
 * - What Happened: Outputs data based on what happened in a given year.
 */

namespace LWTV\Rest_API;

class What_Happened_JSON {

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
	 *   - /lwtv/v1/what-happened/
	 *   - /lwtv/v1/what-happened/YYYY-MM-DD
	 *   - /lwtv/v1/what-happened/YYYY-MM
	 *   - /lwtv/v1/what-happened/YYYY
	 */
	public function rest_api_init() {
		register_rest_route(
			'lwtv/v1',
			'/what-happened/',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'what_happened_rest_api_callback' ),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			'lwtv/v1',
			'/what-happened/(?P<date>[\d]{4}-[\d]{2}-[\d]{2})',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'what_happened_rest_api_callback' ),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			'lwtv/v1',
			'/what-happened/(?P<date>[\d]{4}-[\d]{2})',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'what_happened_rest_api_callback' ),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			'lwtv/v1',
			'/what-happened/(?P<date>[\d]{4})',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'what_happened_rest_api_callback' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Rest API Callback for What Happened
	 */
	public function what_happened_rest_api_callback( $data ) {

		// Create the date with regards to timezones
		$timestamp = time();
		$dt        = new \DateTime( 'now', new \DateTimeZone( LWTV_TIMEZONE ) ); //first argument "must" be a string
		$dt->setTimestamp( $timestamp ); //adjust the object to correct timestamp

		$params   = $data->get_params();
		$the_date = ( isset( $params['date'] ) && '' !== $params['date'] ) ? $params['date'] : $dt->format( 'Y' );
		$response = $this->what_happened( $the_date );
		return $response;
	}

	public function what_happened( $date = false ) {

		// Create the date with regards to timezones
		$timestamp = time();
		$dt        = new \DateTime( 'now', new \DateTimeZone( LWTV_TIMEZONE ) ); //first argument "must" be a string
		$dt->setTimestamp( $timestamp ); //adjust the object to correct timestamp

		$date        = ( ! $date ) ? $dt->format( 'Y' ) : $date;
		$count_array = array();

		// Figure out what date we're working with here...
		if ( preg_match( '/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $date ) ) {
			$format   = 'day';
			$datetime = $dt->createFromFormat( 'Y-m-d', $date );
		}
		if ( preg_match( '/^[0-9]{4}-[0-9]{2}$/', $date ) ) {
			$format   = 'month';
			$datetime = $dt->createFromFormat( 'Y-m', $date );
		}
		if ( preg_match( '/^[0-9]{4}$/', $date ) ) {
			$format   = 'year';
			$datetime = $dt->createFromFormat( 'Y', $date );
		}

		// If it's the future, be smarter than Alexa...
		if ( $datetime->format( 'Y' ) > gmdate( 'Y' ) ) {
			$datetime->modify( '-1 year' );
		}

		// If it's before LWTV_FIRST_YEAR then we have issues....
		if ( $datetime->format( 'Y' ) < LWTV_FIRST_YEAR ) {
			return new \WP_Error( 'too_soon', 'This year is before the first year any queers were on TV.' );
		}

		// Calculate death
		$death_query_year         = lwtv_plugin()->queery_post_meta_and_tax( 'post_type_characters', 'lezchars_death_year', $datetime->format( 'Y' ), 'lez_cliches', 'slug', 'dead', 'REGEXP' );
		$count_array['dead_year'] = ( is_object( $death_query_year ) ) ? $death_query_year->post_count : 0;

		switch ( $format ) {
			case 'year':
				$count_array['dead'] = $count_array['dead_year'];
				break;
			case 'month':
				$death_query       = lwtv_plugin()->queery_post_meta_and_tax( 'post_type_characters', 'lezchars_death_year', $datetime->format( 'Y' ), 'lez_cliches', 'slug', 'dead', 'REGEXP' );
				$death_list_array  = lwtv_plugin()->list_of_dead_characters( $death_query );
				$death_query_count = 0;
				foreach ( $death_list_array as $the_dead ) {
					if ( $datetime->format( 'm' ) === gmdate( 'm', $the_dead['died'] ) ) {
						++$death_query_count;
					}
				}
				$count_array['dead'] = $death_query_count;
				break;
			case 'day':
				$death_query         = lwtv_plugin()->queery_post_meta_and_tax( 'post_type_characters', 'lezchars_death_year', $datetime->format( 'Y-m-d' ), 'lez_cliches', 'slug', 'dead', 'LIKE' );
				$count_array['dead'] = ( is_object( $death_query ) ) ? $death_query->post_count : 0;
				break;
			default:
				$count_array['dead'] = 0;
		}

		// This is calculating how much content we've added since the site started.
		if ( $datetime->format( 'Y' ) > LWTV_CREATED_YEAR ) {
			// Calculate characters and shows
			$valid_post_types = array(
				'posts'      => 'post',
				'shows'      => 'post_type_shows',
				'characters' => 'post_type_characters',
				'actors'     => 'post_type_actors',
			);

			switch ( $format ) {
				case 'day':
					$date_args = array(
						'year'  => $datetime->format( 'Y' ),
						'month' => $datetime->format( 'm' ),
						'day'   => $datetime->format( 'd' ),
					);
					break;
				case 'month':
					$date_args = array(
						'year'  => $datetime->format( 'Y' ),
						'month' => $datetime->format( 'm' ),
					);
					break;
				default:
					$date_args = array(
						'year' => $datetime->format( 'Y' ),
					);
					break;
			}

			foreach ( $valid_post_types as $name => $type ) {
				$post_args            = array(
					'post_type'      => $type,
					'posts_per_page' => '-1',
					'orderby'        => 'date',
					'order'          => 'DESC',
					'date_query'     => array( $date_args ),
					'no_found_rows'  => true,
				);
				$queery               = new \WP_Query( $post_args );
				$count_array[ $name ] = $queery->post_count;
				wp_reset_postdata();
			}
		}

		// Information for shows
		$show_data             = self::count_shows( $datetime->format( 'Y' ) );
		$count_array['on_air'] = array(
			'current' => $show_data['current'],
			'started' => $show_data['started'],
			'ended'   => $show_data['ended'],
		);

		return $count_array;
	}

	/**
	 * count_shows function.
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	public function count_shows( $thisyear = false ) {

		// Create the date with regards to timezones
		$timestamp = time();
		$dt        = new \DateTime( 'now', new \DateTimeZone( LWTV_TIMEZONE ) ); //first argument "must" be a string
		$dt->setTimestamp( $timestamp ); //adjust the object to correct timestamp

		$thisyear        = ( ! $thisyear ) ? $dt->format( 'Y' ) : $thisyear;
		$shows_queery    = lwtv_plugin()->queery_post_type( 'post_type_shows' );
		$shows_this_year = array(
			'current' => 0,
			'ended'   => 0,
			'started' => 0,
		);

		if ( ! is_object( $shows_queery ) || ! $shows_queery->have_posts() ) {
			return $shows_this_year;
		}

		while ( $shows_queery->have_posts() ) {
			$shows_queery->the_post();

			$show_id = get_the_ID();

			// Shows Currently Airing
			if ( get_post_meta( $show_id, 'lezshows_airdates', true ) ) {
				$airdates = get_post_meta( $show_id, 'lezshows_airdates', true );

				if (
					( 'current' === $airdates['finish'] && $thisyear === $dt->format( 'Y' ) )
					|| ( $airdates['finish'] >= $thisyear && $airdates['start'] <= $thisyear ) // Airdates between
				) {
					// Currently Airing Shows shows for the current year only
					++$shows_this_year['current'];
				}

				// Shows that ended this year
				if ( $airdates['finish'] === $thisyear ) {
					++$shows_this_year['ended'];
				}

				// Shows that STARTED this year
				if ( $airdates['start'] === $thisyear ) {
					++$shows_this_year['started'];
				}
			}
		}

		return $shows_this_year;
	}
}
