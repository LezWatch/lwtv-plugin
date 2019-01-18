<?php
/*
Description: REST-API - Slack Integration

Version: 1.0
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * class LWTV_Slack_Integration
 *
 * The basic constructor class that will set up our JSON API.
 */
class LWTV_Slack_Integration {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
		//add_action( 'init', array( $this, 'push_slack_death' ) );
	}

	/**
	 * Rest API init
	 *
	 * Creates callbacks
	 *   - /lwtv/v1/slack/death/
	 */
	public function rest_api_init() {

		// Death
		register_rest_route(
			'lwtv/v1',
			'/slack/last-death/',
			array(
				'methods'  => 'GET',
				'callback' => array( $this, 'death_rest_api_callback' ),
			)
		);
	}

	/**
	 * Rest API Callback for Death
	 * This is used as a slash command of /last-death
	 */
	public function death_rest_api_callback( $data ) {
		$death = LWTV_BYQ_JSON::last_death();
		$since = human_time_diff( $death['died'], current_time( 'timestamp' ) );

		// Get the shows
		$all_shows = array();
		foreach ( $death['shows'] as $show ) {
			$all_shows[] = get_the_title( $show );
		}

		$last = array_pop( $all_shows );
		if ( $all_shows ) {
			$shows_on = implode( ', ', $all_shows ) . ', and ' . $last;
		} else {
			$shows_on = $last;
		}

		$response = array(
			'text' => 'The last death was ' . $since . ' ago: ' . $death['name'] . ' from ' . $shows_on . '. - ' . $death['url'],
		);
		return $response;
	}

	/**
	 * Check the status on death and if there's been a change, report.
	 * @return boolean true if there's a change, false if not
	 */
	public function check_death_status() {
		$return  = false;
		$current = (int) get_option( 'lwtv_last_death' );
		$death   = LWTV_BYQ_JSON::last_death();

		// If there's no last death or the number doesn't match, update
		// the last death and report TRUE
		if ( ! $current || $current !== $death['id'] ) {
			update_option( 'lwtv_last_death', $death['id'] );
			$return = true;
		}

		return $return;
	}

	public function push_slack_death() {

		$status = self::check_death_status();

		// Bail if the status is false.
		if ( ! $status || LWTV_DEV_SITE ) {
			return;
		}

		// Get the data
		$death = LWTV_BYQ_JSON::last_death();

		// Calculate Death
		$tz        = 'America/New_York';
		$timestamp = $death['died'];
		$dt        = new DateTime( 'now', new DateTimeZone( $tz ) ); //first argument "must" be a string
		$dt->setTimestamp( $timestamp ); //adjust the object to correct timestamp
		$date = $dt->format( 'l, F n' );

		// Get the shows
		$all_shows = array();
		foreach ( $death['shows'] as $show ) {
			$all_shows[] = get_the_title( $show );
		}

		$last = array_pop( $all_shows );
		if ( $all_shows ) {
			$shows_on = implode( ', ', $all_shows ) . ', and ' . $last;
		} else {
			$shows_on = $last;
		}

		// Build it!
		$data = array(
			'payload' => wp_json_encode(
				array(
					'text' => 'Reset the counter. ' . $death['name'] . ' from ' . $shows_on . ' died on ' . $date . ' - ' . $death['url'],
				)
			),
		);

		// Post our data via the slack webhook endpoint using wp_remote_post
		$posting_to_slack = wp_remote_post(
			LWTV_SLACK_DEATH,
			array(
				'method'      => 'POST',
				'timeout'     => 30,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => array(),
				'body'        => $data,
				'cookies'     => array(),
			)
		);

	}

}

new LWTV_Slack_Integration();
