<?php
/*
Description: REST-API - Alexa Skills

For Amazon Alexa Skills

Version: 1.0
Author: Mika Epstein
*/

if ( ! defined('WPINC' ) ) die;

/**
 * class LWTV_Alexa_Skills
 *
 * The basic constructor class that will set up our JSON API.
 */
class LWTV_Alexa_Skills {

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
	 *   - /lwtv/v1/flash-briefing
	 */
	public function rest_api_init() {

		// Skills
		register_rest_route( 'lwtv/v1', '/alexa-skills/briefing/', array(
			'methods' => 'GET',
			'callback' => array( $this, 'flash_briefing_rest_api_callback' ),
		) );

		// Skills
		register_rest_route( 'lwtv/v1', '/alexa-skills/byq/', array(
			'methods' => [ 'GET', 'POST' ],
			'callback' => array( $this, 'bury_your_queers_rest_api_callback' ),
		) );


	}

	/**
	 * Rest API Callback for Flash Briefing
	 */
	public function flash_briefing_rest_api_callback( $data ) {
		$response = $this->flash_briefing();
		return $response;
	}

	/**
	 * Rest API Callback for Bury Your Queers
	 * This accepts POST data
	 */
	public function bury_your_queers_rest_api_callback( WP_REST_Request $request ) {


		return $request;

		$body = $request->get_body();
		$id   = absint( $request->get_param( 'id' ) );

		$application_id = 'amzn1.ask.skill.b1b4f1ce-de9c-48cb-ad65-caa6467e6e8c';


		$response = $this->bury_your_queers();
		return $response;
	}


	/**
	 * Generate the Flash Briefing output
	 *
	 * @access public
	 * @return void
	 */
	public function flash_briefing() {

		$query = new WP_Query( array( 'numberposts' => '10' ) );
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$response = array(
					'uid'            => get_the_permalink(),
					'updateDate'     => get_post_modified_time( 'Y-m-d\TH:i:s.\0\Z' ),
					'titleText'      => get_the_title(),
					'mainText'       => get_the_excerpt(),
					'redirectionUrl' => home_url(),
				);

				$responses[] = $response;
			}
			wp_reset_postdata();
		}

		if ( count( $responses ) === 1 ) {
			$responses = $responses[0];
		}

		return $responses;

	}


	/**
	 * Generate Bury Your Queers
	 *
	 * @access public
	 * @return void
	 */
	public function bury_your_queers( $date = 'none' , $when = 'none' ) {

		if ( $date == 'none' ) {
			$data    = LWTV_BYQ_JSON::last_death();
			$name    = $data['name'];
			$date    = date('F j, Y', $data['died']);
			$whodied = 'The last queer female to die was '. $name .' on '. $date;
		} else {

		/*
			1. Accept the POST with the data
			2. Spit back the right info based on the post?

		*/

		}

		$response = array(
			'version'  => '1.0',
			'response' => array (
				'outputSpeech' => array (
					'type' => 'PlainText',
					'text' => $whodied,
				),
				'shouldEndSession' => true,
			)
		);

		return $response;

	}

}
new LWTV_Alexa_Skills();