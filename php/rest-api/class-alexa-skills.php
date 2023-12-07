<?php
/**
 * Base code for Amazon Alexa Skills
 */

namespace LWTV\Rest_API;

use LWTV\Rest_API\Alexa\BYQ;
use LWTV\Rest_API\Alexa\Flash_Brief;
use LWTV\Rest_API\Alexa\Newest;
use LWTV\Rest_API\Alexa\Shows;
use LWTV\Rest_API\Alexa\This_Year;
use LWTV\Rest_API\Alexa\Validate;
use LWTV\Rest_API\Alexa\Whats_On;
use LWTV\Rest_API\Alexa\Who_Are_You;

use LWTV\_Components\Of_The_Day;

class Alexa_Skills {

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
		$response = ( new Flash_Brief() )->flash_briefing();
		return $response;
	}

	/**
	 * Rest API Callback for News - aka the general app
	 * This accepts POST data
	 */
	public function news_rest_api_callback( \WP_REST_Request $request ) {

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
		$validate = ( new Validate() )->the_request( $request );
		if ( 1 !== $validate['success'] ) {
			$response = array(
				'message' => $validate['message'],
				'data'    => array(
					'status' => 400,
				),
			);
			$error    = new \WP_REST_Response( $response );
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

		if ( false !== $value['date'] && is_numeric( substr( $value['date'], 0, 4 ) ) && substr( $value['date'], 0, 4 ) < LWTV_FIRST_YEAR ) {
			$output     = 'There were no known queer female, non-binary, or transgender characters on T. V. prior to ' . LWTV_FIRST_YEAR . '. Would you like to ask me something else? ' . $helptext;
			$endsession = false;
		} elseif ( 'LaunchRequest' === $type ) {
			$output     = 'Welcome to the LezWatch T. V. skill. ' . $helptext;
			$endsession = false;
		} else {
			switch ( $intent ) {
				case 'HowMany':
					if ( false === $value['date'] ) {
						$output = ( new BYQ() )->how_many( 'simple' );
					} else {
						$output = ( new BYQ() )->how_many( $value['date'] );
					}
					break;
				case 'CharOTD':
					$data   = ( new Of_The_Day() )->character_show( $value['date'], 'character' );
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
					$output = ( new Newest() )->whats_new();
					break;
				case 'WhoDied':
					if ( ! $value['date'] ) {
						$output = 'The last character on LezWatch T. V. to die was ' . ( new Newest() )->latest( 'death' ) . '.';
					} else {
						$output = ( new BYQ() )->on_a_day( $value['date'] );
					}
					break;
				case 'WhatHappened':
					$output = ( new This_Year() )->what_happened( $value['date'] );
					break;
				case 'WhoAreYouActor':
					if ( isset( $value['actor'] ) ) {
						$output = ( new Who_Are_You() )->actor( $value['actor'] );
					} else {
						$output     = 'I\'m sorry, I didn\'t quite catch what actor you\'re asking about. Can you please ask me again? I\'ll listen harder.';
						$endsession = false;
					}
					break;
				case 'WhoAreYouChar':
					if ( isset( $value['character'] ) ) {
						$output = ( new Who_Are_You() )->character( $value['character'] );
					} else {
						$output     = 'I\'m sorry, I didn\'t quite catch what character you\'re asking about. Can you please ask me again? I\'ll listen harder.';
						$endsession = false;
					}
					break;
				case 'WhoAreYouShow':
					if ( isset( $value['show'] ) ) {
						$output = ( new Who_Are_You() )->show( $value['show'] );
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
						$output = ( new Who_Are_You() )->is_gay( $value['actor'] );
					}
					break;
				case 'SimilarShow':
					if ( ! $value['show'] ) {
						$output     = 'I\'m sorry, I didn\'t quite catch the name of the television show you\'re asking about. Can you please ask me again? I\'ll listen harder.';
						$endsession = false;
					} else {
						$output = ( new Shows() )->similar_to( $value['show'] );
					}
					break;
				case 'WhatsOn':
					$the_date = ( ! $value['date'] ) ? 'today' : $value['date'];
					$output   = ( new Whats_On() )->on_a_day( $the_date );
					break;
				case 'WhatsOnShows':
					if ( ! $value['show'] ) {
						$output     = 'I\'m sorry, I didn\'t quite catch the name of the television show you\'re asking about. Can you please ask me again? I\'ll listen harder.';
						$endsession = false;
					} else {
						$output = ( new Whats_On() )->show( $value['show'] );
					}
					break;
				case 'RecommendShows':
					$output = ( new Shows() )->recommended();
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
