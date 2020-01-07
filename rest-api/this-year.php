<?php
/*
Description: REST-API - This Year
Version: 1.0
Author: Mika Epstein
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * class LWTV_This_Year_JSON
 *
 * The basic constructor class that will set up our JSON API.
 */
class LWTV_This_Year_JSON {

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
	 *   - /lwtv/v1/stats/[shows|characters|death]/[simple|complex|years]
	 */
	public function rest_api_init() {

		// Basic Stats
		register_rest_route(
			'lwtv/v1',
			'/this-year/',
			array(
				'methods'  => 'GET',
				'callback' => array(
					$this,
					'rest_api_callback',
				),
			)
		);

		// Types
		register_rest_route(
			'lwtv/v1',
			'/this-year/(?P<type>[a-zA-Z.\-]+)',
			array(
				'methods'  => 'GET',
				'callback' => array(
					$this,
					'rest_api_callback',
				),
			)
		);

		// Year
		register_rest_route(
			'lwtv/v1',
			'/this-year/(?P<type>[a-zA-Z.\-]+)/(?P<year>[\d]+)',
			array(
				'methods'  => 'GET',
				'callback' => array(
					$this,
					'rest_api_callback',
				),
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
		$year   = ( isset( $params['year'] ) && '' !== $params['year'] && ( $params['year'] >= FIRST_LWTV_YEAR && $params['year'] <= gmdate( 'Y' ) ) ) ? (int) $params['year'] : gmdate( 'Y' );

		switch ( $type ) {
			case 'year':
				$response = $this->this_year( 'year', $year );
				break;
			case 'ten-years':
				$response = $this->this_year( 'ten', $year );
				break;
		}

		if ( empty( $response ) ) {
			return wp_send_json_error( 'Invalid year.', 404 );
		}

		return $response;
	}

	/**
	 * Parse this year and call the data as needed
	 * @param  string  $type What kind of data for the year
	 * @param  int     $year What year
	 * @return array   Array of data
	 */
	public static function this_year( $type, $year ) {

		// Remove <!--fwp-loop--> from output
		// phpcs:ignore
		add_filter( 'facetwp_is_main_query', function( $is_main_query, $query ) { return false; }, 10, 2 );

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
	public static function one_year( $year ) {
		$year  = (string) $year;
		$array = array(
			'year'       => (int) $year,
			'characters' => LWTV_This_Year_Chars::get_list( $year, true ),
			'dead'       => LWTV_This_Year_Chars::get_dead( $year, true ),
			'shows'      => LWTV_This_Year_Shows::get_list( $year, 'now', true ),
			'started'    => LWTV_This_Year_Shows::get_list( $year, 'started', true ),
			'canceled'   => LWTV_This_Year_Shows::get_list( $year, 'ended', true ),
		);

		return $array;
	}

	public static function ten_years( $year ) {

		$array      = array();
		$end_year   = ( $year >= FIRST_LWTV_YEAR ) ? $year : FIRST_LWTV_YEAR;
		$end_year   = ( $year <= gmdate( 'Y' ) ) ? $year : gmdate( 'Y' );
		$start_year = $end_year - 10;

		while ( $start_year <= $end_year ) {
			if ( ( $start_year >= FIRST_LWTV_YEAR && $start_year <= gmdate( 'Y' ) ) ) {
				$array[ $start_year ] = array(
					'characters' => LWTV_This_Year_Chars::get_list( (string) $start_year, true ),
					'dead'       => LWTV_This_Year_Chars::get_dead( (string) $start_year, true ),
					'shows'      => LWTV_This_Year_Shows::get_list( (string) $start_year, 'now', true ),
					'started'    => LWTV_This_Year_Shows::get_list( (string) $start_year, 'started', true ),
					'canceled'   => LWTV_This_Year_Shows::get_list( (string) $start_year, 'ended', true ),
				);
			}
			$start_year++;
		}

		return $array;
	}

}

new LWTV_This_Year_JSON();
