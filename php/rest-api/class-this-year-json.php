<?php
/**
 * Powers the This-Year Rest API
 */

namespace LWTV\Rest_API;

use LWTV\This_Year\The_Array as This_Year_Array;

class This_Year_JSON {

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
	 *   - /lwtv/v1/this-year/[shows|characters|death]/[simple|complex|years]
	 */
	public function rest_api_init() {

		// Basic Stats
		register_rest_route(
			'lwtv/v1',
			'/this-year/',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_api_callback' ),
				'permission_callback' => '__return_true',
			)
		);

		// Types
		register_rest_route(
			'lwtv/v1',
			'/this-year/(?P<type>[a-zA-Z.\-]+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_api_callback' ),
				'permission_callback' => '__return_true',
			)
		);

		// Year
		register_rest_route(
			'lwtv/v1',
			'/this-year/(?P<type>[a-zA-Z.\-]+)/(?P<year>[\d]+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_api_callback' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Rest API Callback for This Year
	 *
	 * @param mixed $data - string.
	 * @return array
	 */
	public function rest_api_callback( $data ) {
		$params = $data->get_params();
		$type   = ( isset( $params['type'] ) && '' !== $params['type'] ) ? sanitize_title_for_query( $params['type'] ) : 'year';
		$year   = ( isset( $params['year'] ) && '' !== $params['year'] && ( $params['year'] >= LWTV_FIRST_YEAR && $params['year'] <= gmdate( 'Y' ) ) ) ? (int) $params['year'] : gmdate( 'Y' );

		switch ( $type ) {
			case 'year':
				$response = $this->this_year( 'year', $year );
				break;
			case 'ten-years':
				$response = $this->this_year( 'ten', $year );
				break;
		}

		if ( empty( $response ) ) {
			return new \WP_Error( 'not_found', 'Invalid year.' );
		}

		return $response;
	}

	/**
	 * Parse this year and call the data as needed
	 * @param  string  $type What kind of data for the year
	 * @param  int     $year What year
	 * @return array   Array of data
	 */
	public function this_year( $type, $year ) {

		// phpcs:disable
		// Remove <!--fwp-loop--> from output
		add_filter(
			'facetwp_is_main_query',
			function( $is_main_query, $query ) {
				return false;
			},
			10,
			2
		);
		// phpcs:enable

		$year  = ( isset( $year ) ) ? (int) $year : gmdate( 'Y' );
		$array = array();

		switch ( $type ) {
			case 'year':
				$array = self::one_year( $year );
				break;
			case 'ten':
				$array = self::ten_years( $year );
				break;
		}

		return $array;
	}

	/**
	 * Get one year of data
	 * @param  int   $year  Year
	 * @return array        Array of data from one year
	 */
	public function one_year( $year ) {
		$this_year = (string) $year;
		$array     = array(
			'year'       => (int) $this_year,
			'characters' => ( new This_Year_Array() )->make( $this_year, 'characters-on-air', true ),
			'dead'       => ( new This_Year_Array() )->make( $this_year, 'dead-characters', true ),
			'shows'      => ( new This_Year_Array() )->make( $this_year, 'shows-on-air', 'now', true ),
			'started'    => ( new This_Year_Array() )->make( $this_year, 'new-shows', 'started', true ),
			'canceled'   => ( new This_Year_Array() )->make( $this_year, 'canceled-shows', 'ended', true ),
		);

		return $array;
	}

	public function ten_years( $year ) {

		$array      = array();
		$end_year   = ( $year >= LWTV_FIRST_YEAR ) ? $year : LWTV_FIRST_YEAR;
		$end_year   = ( $year <= gmdate( 'Y' ) ) ? $year : gmdate( 'Y' );
		$start_year = $end_year - 10;

		while ( $start_year <= $end_year ) {
			if ( ( $start_year >= LWTV_FIRST_YEAR && $start_year <= gmdate( 'Y' ) ) ) {
				$array[ $start_year ] = array(
					'characters' => ( new This_Year_Array() )->make( $start_year, 'characters-on-air', false, true ),
					'dead'       => ( new This_Year_Array() )->make( $start_year, 'dead-characters', false, true ),
					'shows'      => ( new This_Year_Array() )->make( $start_year, 'shows-on-air', 'now', true ),
					'started'    => ( new This_Year_Array() )->make( $start_year, 'new-shows', 'started', true ),
					'canceled'   => ( new This_Year_Array() )->make( $start_year, 'canceled-shows', 'ended', true ),
				);
			}
			++$start_year;
		}

		return $array;
	}
}
