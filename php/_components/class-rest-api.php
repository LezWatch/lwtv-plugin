<?php
/*
 * Rest API
 */
namespace LWTV\_Components;

use LWTV\Rest_API\Whats_On_JSON;
use LWTV\Rest_API\What_Happened_JSON;
use LWTV\Rest_API\This_Year_JSON;
use LWTV\Rest_API\Stats_JSON;
use LWTV\Rest_API\Shows_Like_JSON;
use LWTV\Rest_API\OTD_JSON;
use LWTV\Rest_API\List_JSON;
use LWTV\Rest_API\IMDb_JSON;
use LWTV\Rest_API\Fresh_JSON;
use LWTV\Rest_API\Export_JSON;
use LWTV\Rest_API\BYQ;
use LWTV\Rest_API\Alexa_Skills;

class Rest_API implements Component, Templater {

	/*
	 * Init
	 */
	public function init(): void {
		new Alexa_Skills();
		new BYQ();
		new Export_JSON();
		new Fresh_JSON();
		new IMDb_JSON();
		new List_JSON();
		new OTD_JSON();
		new Shows_Like_JSON();
		new Stats_JSON();
		new This_Year_JSON();
		new What_Happened_JSON();
		new Whats_On_JSON();
	}

	/**
	 * Gets tags to expose as methods accessible through `lwtv_plugin()`.
	 *
	 * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
	 *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
	 *               adding support for further arguments in the future.
	 */
	public function get_template_tags(): array {
		return array(
			'generate_tvshow_calendar'  => array( $this, 'generate_tvshow_calendar' ),
			'get_whats_on_date'         => array( $this, 'get_whats_on_date' ),
			'get_whats_on_show'         => array( $this, 'get_whats_on_show' ),
			'get_what_happened_on_date' => array( $this, 'get_what_happened_on_date' ),
			'get_json_statistics'       => array( $this, 'get_json_statistics' ),
			'get_json_similar_show'     => array( $this, 'get_json_similar_show' ),
			'get_json_export'           => array( $this, 'get_json_export' ),
			'get_json_last_death'       => array( $this, 'get_json_last_death' ),
		);
	}

	/**
	 * Generate TV Shows Calendar
	 *
	 * @param  string $date
	 * @return void
	 */
	public static function generate_tvshow_calendar( $date ) {
		return ( new Whats_On_JSON() )->generate_tvshow_calendar( $date );
	}

	/**
	 * Get whats on TV for a date
	 *
	 * @param mixed $date
	 * @return array
	 */
	public function get_whats_on_date( $date ) {
		return ( new Whats_On_JSON() )->whats_on_date( $date );
	}

	/**
	 * Get when a show is on
	 *
	 * @param mixed $date
	 * @return array
	 */
	public function get_whats_on_show( $show ) {
		return ( new Whats_On_JSON() )->whats_on_show( $show );
	}

	/**
	 * Get What Happened on a date
	 *
	 * @param bool $date
	 * @return \WP_Error|array
	 */
	public function get_what_happened_on_date( $date = false ) {
		return ( new What_Happened_JSON() )->what_happened( $date );
	}

	/**
	 * Generate Statistics
	 *
	 * @param string $stat_type
	 * @param string $format
	 * @param int    $page
	 *
	 * @return array with stats data
	 */
	public function get_json_statistics( $stat_type = 'characters', $format = 'simple', $page = 1 ) {
		return ( new Stats_JSON() )->statistics( $stat_type, $format, $page );
	}

	/**
	 * Similar Show
	 *
	 * @param string $show_slug
	 *
	 * @return mixed
	 */
	public function get_json_similar_show( $show_slug ) {
		return ( new Shows_Like_JSON() )->similar_show( $show_slug );
	}

	/**
	 * Export custom JSON
	 *
	 * @param  string $type
	 * @param  string $item
	 * @param  string $tax
	 * @param  string $term
	 *
	 * @return array|string|WP_Error
	 */
	public function get_json_export( $type = 'actor', $item = 'unknown', $tax = '', $term = '' ) {
		return ( new Export_JSON() )->export( $type, $item, $tax, $term );
	}

	/**
	 * Generate List of Dead
	 *
	 * @return array with last dead character data
	 */
	public function get_json_last_death() {
		return ( new BYQ() )->last_death();
	}
}
