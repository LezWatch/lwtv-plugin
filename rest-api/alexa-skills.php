<?php
/*
Name: REST-API - Alexa Skills
Description: Base code for Amazon Alexa Skills
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

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
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
	}

	/**
	 * Rest API init
	 *
	 * Creates callbacks
	 *   - /lwtv/v1/alexa-skills/flash-briefing/
	 *   - /lwtv/v1/alexa-skills/byq/
	 *   - /lwtv/v1/alexa-skills/news/
	 *   - /lwtv/v1/alexa-skills/shows-like-this/
	 */
	public function rest_api_init() {
		// @codingStandardsIgnoreStart
		// Briefing
		register_rest_route( 'lwtv/v1', '/alexa-skills/briefing/', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'flash_briefing_rest_api_callback' ),
			'permission_callback' => '__return_true',
		) );

		// News Skill (rebranded BYQ)
		register_rest_route( 'lwtv/v2', '/alexa-skills/news/', array(
			'methods'             => [ 'GET', 'POST' ],
			'callback'            => array( $this, 'news_rest_api_callback' ),
			'permission_callback' => '__return_true',
		) );

		// DEPRECATED: Bury Your Queers
		register_rest_route( 'lwtv/v1', '/alexa-skills/byq/', array(
			'methods'             => [ 'GET', 'POST' ],
			'callback'            => array( 'LWTV_Alexa_Common', 'bury_your_queers_rest_api_callback' ),
			'permission_callback' => '__return_true',
		) );
		// @codingStandardsIgnoreEnd
	}

	/**
	 * Rest API Callback for Flash Briefing
	 */
	public function flash_briefing_rest_api_callback() {
		require_once 'alexa/flash-brief.php';
		$response = ( new LWTV_Alexa_Flash_Brief() )->flash_briefing();
		return $response;
	}

	/**
	 * Rest API Callback for News - aka the general app
	 * This accepts POST data
	 */
	public function news_rest_api_callback( WP_REST_Request $request ) {

		$type   = ( isset( $request['request']['type'] ) ) ? sanitize_text_field( $request['request']['type'] ) : false;
		$intent = ( isset( $request['request']['intent']['name'] ) ) ? sanitize_text_field( $request['request']['intent']['name'] ) : false;

		$req_id = ( isset( $request['request']['session']['application']['applicationId'] ) ) ? sanitize_text_field( $request['request']['session']['application']['applicationId'] ) : false;

		// Values
		$value = array(
			'date'      => ( isset( $request['request']['intent']['slots']['Date']['value'] ) ) ? $request['request']['intent']['slots']['Date']['value'] : false,
			'actor'     => ( isset( $request['request']['intent']['slots']['actor']['value'] ) ) ? sanitize_text_field( $request['request']['intent']['slots']['actor']['value'] ) : '',
			'character' => ( isset( $request['request']['intent']['slots']['character']['value'] ) ) ? sanitize_text_field( $request['request']['intent']['slots']['character']['value'] ) : '',
			'show'      => ( isset( $request['request']['intent']['slots']['show']['value'] ) ) ? sanitize_text_field( $request['request']['intent']['slots']['show']['value'] ) : '',
		);

		// Call the validation:
		require_once 'alexa/_validate.php';
		$validate = ( new LWTV_Alexa_Validate() )->the_request( $request );
		if ( 1 !== $validate['success'] ) {
			$response = array(
				'message' => $validate['message'],
				'data'    => array(
					'status' => 400,
				),
			);
			$error    = new WP_REST_Response( $response );
			$error->set_status( 400 );
			return $error;
		}
		$response = $this->news_skill( $type, $intent, $value );

		return $response;
	}

	/**
	 * Generate News (formerly Bury Your Queers)
	 *
	 * @access public
	 * @return void
	 */
	public function news_skill( $type = false, $intent = false, $value = false ) {

		$helptext   = 'You can ask me for information on queer female, non-binary, or transgender characters or television shows on LezWatch T. V.. Try asking me "What happened this year?" or "What shows are like Batwoman" or "Who is the character of the day?" or "Who is Laverne Cox?" or "Is Ali Liebert queer?" or even "Who died on March 3rd?" -- I\'ll let you know what I\'ve found.';
		$output     = 'I\'m sorry, I don\'t understand that request. Please ask me something else. ' . $helptext;
		$endsession = true;

		if ( false !== $value['date'] && is_numeric( substr( $value['date'], 0, 4 ) ) && substr( $value['date'], 0, 4 ) < FIRST_LWTV_YEAR ) {
			$output     = 'There were no known queer female, non-binary, or transgender characters on T. V. prior to ' . FIRST_LWTV_YEAR . '. Would you like to ask me something else? ' . $helptext;
			$endsession = false;
		} elseif ( 'LaunchRequest' === $type ) {
			$output     = 'Welcome to the LezWatch T. V. skill. ' . $helptext;
			$endsession = false;
		} else {
			switch ( $intent ) {
				case 'HowMany':
					require_once 'alexa/byq.php';
					if ( false === $value['date'] ) {
						$output = ( new LWTV_Alexa_BYQ() )->how_many( 'simple' );
					} else {
						$output = ( new LWTV_Alexa_BYQ() )->how_many( $value['date'] );
					}
					break;
				case 'CharOTD':
					$data   = ( new LWTV_Of_The_Day() )->character_show( $value['date'], 'character' );
					$name   = $data['name'];
					$show   = $data['shows'];
					$output = 'The LezWatch T. V. character of the day is ' . $name . ' from ' . $show . '.';
					break;
				case 'ShowOTD':
					$data    = get_option( 'lwtv_otd' );
					$post_id = $data['show']['post'];
					$output  = 'The LezWatch T. V. show of the day is ' . get_the_title( $post_id ) . '.';
					break;
				case 'WhatsNew':
					require_once 'alexa/newest.php';
					$output = ( new LWTV_Alexa_Newest() )->whats_new();
					break;
				case 'WhoDied':
					if ( ! $value['date'] ) {
						require_once 'alexa/newest.php';
						$output = 'The last character on LezWatch T. V. to die was ' . ( new LWTV_Alexa_Newest() )->latest( 'death' ) . '.';
					} else {
						require_once 'alexa/byq.php';
						$output = ( new LWTV_Alexa_BYQ() )->on_a_day( $value['date'] );
					}
					break;
				case 'WhatHappened':
					require_once 'alexa/this-year.php';
					$output = ( new LWTV_Alexa_This_Year() )->what_happened( $value['date'] );
					break;
				case 'WhoAreYouActor':
					if ( isset( $value['actor'] ) ) {
						require_once 'alexa/who-are-you.php';
						$output = ( new LWTV_Alexa_Who() )->actor( $value['actor'] );
					} else {
						$output     = 'I\'m sorry, I didn\'t quite catch what actor you\'re asking about. Can you please ask me again? I\'ll listen harder.';
						$endsession = false;
					}
					break;
				case 'WhoAreYouChar':
					if ( isset( $value['character'] ) ) {
						require_once 'alexa/who-are-you.php';
						$output = ( new LWTV_Alexa_Who() )->character( $value['character'] );
					} else {
						$output     = 'I\'m sorry, I didn\'t quite catch what character you\'re asking about. Can you please ask me again? I\'ll listen harder.';
						$endsession = false;
					}
					break;
				case 'WhoAreYouShow':
					if ( isset( $value['show'] ) ) {
						require_once 'alexa/who-are-you.php';
						$output = ( new LWTV_Alexa_Who() )->show( $value['show'] );
					} else {
						$output     = 'I\'m sorry, I didn\'t quite catch what television show you\'re asking about. Can you please ask me again? I\'ll listen harder. ';
						$endsession = false;
					}
					break;
				case 'IsQueer':
					if ( ! $value['actor'] ) {
						$output     = 'I\'m sorry, I didn\'t quite catch the name of the actor you\'re asking about. Can you please ask me again? I\'ll listen harder.';
						$endsession = false;
					} else {
						require_once 'alexa/who-are-you.php';
						$output = ( new LWTV_Alexa_Who() )->is_gay( $value['actor'] );
					}
					break;
				case 'SimilarShow':
					if ( ! $value['show'] ) {
						$output     = 'I\'m sorry, I didn\'t quite catch the name of the television show you\'re asking about. Can you please ask me again? I\'ll listen harder.';
						$endsession = false;
					} else {
						require_once 'alexa/shows.php';
						$output = ( new LWTV_Alexa_Shows() )->similar_to( $value['show'] );
					}
					break;
				case 'WhatsOn':
					$the_date = ( ! $value['date'] ) ? 'today' : $value['date'];
					require_once 'alexa/whats-on.php';
					$output = ( new LWTV_Alexa_Whats_On() )->on_a_day( $the_date );
					break;
				case 'WhatsOnShows':
					if ( ! $value['show'] ) {
						$output     = 'I\'m sorry, I didn\'t quite catch the name of the television show you\'re asking about. Can you please ask me again? I\'ll listen harder.';
						$endsession = false;
					} else {
						require_once 'alexa/whats-on.php';
						$output = ( new LWTV_Alexa_Whats_On() )->show( $value['show'] );
					}
					break;
				case 'RecommendShows':
					require_once 'alexa/shows.php';
					$output = ( new LWTV_Alexa_Shows() )->recommended();
					break;
				case 'AMAZON.HelpIntent':
					$output     = 'This is the News skill by LezWatch T. V. News, home of the world\'s greatest database of queer female, non-binary and transgender characters on international television. ' . $helptext;
					$endsession = false;
					break;
				case 'AMAZON.StopIntent':
				case 'AMAZON.CancelIntent':
					$output = '';
					break;
				default:
					$output     = 'I\'m sorry, I didn\'t quite understand you\'re asking about. Can you please ask me again? I\'ll listen harder.';
					$endsession = false;
					break;
			}
		}

		// Return response
		$response = array(
			'version'  => '1.0',
			'response' => array(
				'outputSpeech'     => array(
					'type' => 'PlainText',
					'text' => $output,
				),
				'shouldEndSession' => $endsession,
			),
		);
		return $response;
	}
}
new LWTV_Alexa_Skills();
