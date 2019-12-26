<?php
/*
Description: REST-API - Alexa Skills

For Amazon Alexa Skills

Version: 1.2
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
			'methods'  => 'GET',
			'callback' => array( $this, 'flash_briefing_rest_api_callback' ),
		) );

		// Bury Your Queers (legacy)
		register_rest_route( 'lwtv/v1', '/alexa-skills/byq/', array(
			'methods'  => [ 'GET', 'POST' ],
			'callback' => array( $this, 'bury_your_queers_rest_api_callback' ),
		) );

		// News Skill (rebranded BYQ)
		register_rest_route( 'lwtv/v2', '/alexa-skills/news/', array(
			'methods'  => [ 'GET', 'POST' ],
			'callback' => array( $this, 'news_rest_api_callback' ),
		) );
		// @codingStandardsIgnoreEnd
	}

	/**
	 * Rest API Callback for Flash Briefing
	 */
	public function flash_briefing_rest_api_callback( $data ) {
		require_once 'alexa/flash-brief.php';
		$response = LWTV_Alexa_Flash_Brief::flash_briefing();
		return $response;
	}

	/**
	 * Rest API Callback for Bury Your Queers
	 * THIS IS OLD and can be removed once BYQ is retired, but let's not break things...
	 * This accepts POST data
	 */
	public static function bury_your_queers_rest_api_callback( WP_REST_Request $request ) {
		$type   = ( isset( $request['request']['type'] ) ) ? $request['request']['type'] : false;
		$intent = ( isset( $request['request']['intent']['name'] ) ) ? $request['request']['intent']['name'] : false;
		$date   = ( isset( $request['request']['intent']['slots']['Date']['value'] ) ) ? $request['request']['intent']['slots']['Date']['value'] : false;
		$req_id = ( isset( $request['request']['session']['application']['applicationId'] ) ) ? $request['request']['session']['application']['applicationId'] : false;

		// Call the validation:
		require_once 'alexa/alexa-validate.php';
		$validate_alexa = LWTV_Alexa_Validate::the_request( $request );

		if ( 1 !== $validate_alexa['success'] ) {
			$response = array(
				'message' => $validate_alexa['message'],
				'data'    => array(
					'status' => 400,
				),
			);
			$error    = new WP_REST_Response( $response );
			$error->set_status( 400 );
			return $error;
		}
		$response = $this->bury_your_queers( $type, $intent, $date );
		return $response;
	}

	/**
	 * Rest API Callback for News - aka the general app
	 * This accepts POST data
	 */
	public function news_rest_api_callback( WP_REST_Request $request ) {

		$type      = ( isset( $request['request']['type'] ) ) ? sanitize_text_field( $request['request']['type'] ) : false;
		$intent    = ( isset( $request['request']['intent']['name'] ) ) ? sanitize_text_field( $request['request']['intent']['name'] ) : false;

		$req_id    = ( isset( $request['request']['session']['application']['applicationId'] ) ) ? sanitize_text_field( $request['request']['session']['application']['applicationId'] ) : false;

		// Values
		$value = array(
			'date'     => ( isset( $request['request']['intent']['slots']['Date']['value'] ) ) ? $request['request']['intent']['slots']['Date']['value'] : false,
			'actor'    => ( isset( $request['request']['intent']['slots']['actor']['value'] ) ) ? sanitize_text_field( $request['request']['intent']['slots']['actor']['value'] ) : '',
			'character' => ( isset( $request['request']['intent']['slots']['character']['value'] ) ) ? sanitize_text_field( $request['request']['intent']['slots']['character']['value'] ) : '',
			'show'      => ( isset( $request['request']['intent']['slots']['show']['value'] ) ) ? sanitize_text_field( $request['request']['intent']['slots']['show']['value'] ) : '',
		);

		// Call the validation:
		require_once 'alexa/alexa-validate.php';
		$validate_alexa = LWTV_Alexa_Validate::the_request( $request );
		if ( 1 !== $validate_alexa['success'] ) {
			$response = array(
				'message' => $validate_alexa['message'],
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
						$output = LWTV_Alexa_BYQ::how_many( 'simple' );
					} else {
						$output = LWTV_Alexa_BYQ::how_many( $value['date'] );
					}
					break;
				case 'CharOTD':
					$data   = LWTV_OTD_JSON::character_show( $value['date'], 'character' );
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
					$output = LWTV_Alexa_Newest::whats_new();
					break;
				case 'WhoDied':
					if ( ! $value['date'] ) {
						require_once 'alexa/newest.php';
						$output = 'The last character on LezWatch T. V. to die was ' . LWTV_Alexa_Newest::latest( 'death' ) . '.';
					} else {
						require_once 'alexa/byq.php';
						$output = LWTV_Alexa_BYQ::on_a_day( $value['date'] );
					}
					break;
				case 'WhatHappened':
					require_once 'alexa/this-year.php';
					$output = LWTV_Alexa_This_Year::what_happened( $value['date'] );
					break;
				case 'WhoAreYouActor':
					if ( isset( $value['actor'] ) ) {
						require_once 'alexa/who-are-you.php';
						$output = LWTV_Alexa_Who::actor( $value['actor'] );
					} else {
						$output     = 'I\'m sorry, I didn\'t quite catch what actor you\'re asking about. Can you please ask me again? I\'ll listen harder.';
						$endsession = false;
					}
					break;
				case 'WhoAreYouChar':
					if ( isset( $value['character'] ) ) {
						require_once 'alexa/who-are-you.php';
						$output = LWTV_Alexa_Who::character( $value['character'] );
					} else {
						$output     = 'I\'m sorry, I didn\'t quite catch what character you\'re asking about. Can you please ask me again? I\'ll listen harder.';
						$endsession = false;
					}
					break;
				case 'WhoAreYouShow':
					if ( isset( $value['show'] ) ) {
						require_once 'alexa/who-are-you.php';
						$output = LWTV_Alexa_Who::show( $value['show'] );
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
						$output = LWTV_Alexa_Who::is_gay( $value['actor'] );
					}
					break;
				case 'SimilarShow':
					if ( ! $value['show'] ) {
						$output     = 'I\'m sorry, I didn\'t quite catch the name of the television show you\'re asking about. Can you please ask me again? I\'ll listen harder.';
						$endsession = false;
					} else {
						require_once 'alexa/shows.php';
						$output = LWTV_Alexa_Shows::similar_to( $value['show'] );
					}
					break;
				case 'WhatsOn':
					$the_date = ( ! $value['date'] ) ? 'today' : $value['date'];
					require_once 'alexa/whats-on.php';
					$output = LWTV_Alexa_Whats_On::on_a_day( $the_date );
					break;
				case 'WhatsOnShows':
					if ( ! $value['show'] ) {
						$output     = 'I\'m sorry, I didn\'t quite catch the name of the television show you\'re asking about. Can you please ask me again? I\'ll listen harder.';
						$endsession = false;
					} else {
						require_once 'alexa/whats-on.php';
						$output = LWTV_Alexa_Whats_On::show( $value['show'] );
					}
					break;
				case 'RecommendShows':
					require_once 'alexa/shows.php';
					$output = LWTV_Alexa_Shows::recommended();
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

	/**
	 * Generate Bury Your Queers
	 * THIS IS OLD and can be removed once BYQ is retired, but let's not break things...
	 *
	 * @access public
	 * @return void
	 */
	public function bury_your_queers( $type = false, $intent = false, $date = false ) {

		$notice     = 'This skill is being retired. Please install "LezWatch TV News" for future development and additional features. ';
		$whodied    = '';
		$endsession = true;
		$timestamp  = ( false === strtotime( $date ) ) ? false : strtotime( $date );
		$helptext   = 'You can find out who died on specific dates by asking me questions like "who died" or "who died today" or "who died on March 3rd" or even "How many died in 2017." If no one died then, I\'ll let you know.';
		if ( 'LaunchRequest' === $type ) {
			$whodied    = 'Welcome to the LezWatch T. V. Bury Your Queers skill. ' . $helptext;
			$endsession = false;
		} else {
			switch ( $intent ) {
				case 'AMAZON.HelpIntent':
					$whodied    = 'This is the Bury Your Queers skill by LezWatch T. V., home of the world\'s greatest database of queer female, non-binary, and trans characters on TV. ' . $helptext;
					$endsession = false;
					break;
				case 'AMAZON.StopIntent':
				case 'AMAZON.CancelIntent':
					$endsession = false;
					break;
				case 'HowMany':
					if ( false === $date || false === $timestamp ) {
						$data    = LWTV_Stats_JSON::statistics( 'death', 'simple' );
						$whodied = 'A total of ' . $data['characters']['dead'] . ' queer female, non-binary, and trans characters have died on TV.';
					} elseif ( ! preg_match( '/^[0-9]{4}$/', $date ) ) {
						$whodied    = 'I\'m sorry. I don\'t know how to calculate deaths in anything but years right now. ' . $helptext;
						$endsession = false;
					} else {
						$data     = LWTV_Stats_JSON::statistics( 'death', 'years' );
						$count    = $data[ $date ]['count'];
						$how_many = 'No queer female, non-binary, or trans characters died on TV in ' . $date . '.';
						if ( $count > 0 ) {
							$how_many = $count . ' queer female, non-binary, or trans ' . _n( 'character', 'characters', $count ) . ' died on TV in ' . $date . '.';
						}
						$whodied = $how_many;
					}
					break;
				case 'WhoDied':
					if ( false === $date || false === $timestamp ) {
						$data    = LWTV_BYQ_JSON::last_death();
						$name    = $data['name'];
						$whodied = 'The last queer female, non-binary, or trans character to die was ' . $name . ' on ' . date( 'F j, Y', $data['died'] ) . '.';
					} elseif ( preg_match( '/^[0-9]{4}-(0[1-9]|1[0-2])$/', $date ) ) {
						$whodied    = 'I\'m sorry. I don\'t know how to calculate deaths in anything but days right now. ' . $helptext;
						$endsession = false;
					} else {
						$this_day = date( 'm-d', $timestamp );
						$data     = LWTV_BYQ_JSON::on_this_day( $this_day );
						$count    = ( 'none' === key( $data ) ) ? 0 : count( $data );
						$how_many = 'No queer female, non-binary, or trans characters died';
						$the_dead = '';
						if ( $count > 0 ) {
							$how_many  = $count . ' queer female, non-binary, or trans ' . _n( 'character', 'characters', $count ) . ' died';
							$deadcount = 1;
							foreach ( $data as $dead_character ) {
								if ( $deadcount === $count && 1 !== $count ) {
									$the_dead .= 'And ';
								}
								$the_dead .= $dead_character['name'] . ' in ' . $dead_character['died'] . '. ';
								$deadcount++;
							}
						}
						$whodied = $how_many . ' on ' . date( 'F jS', $timestamp ) . '. ' . $the_dead;
					}
					break;
				default:
					// We have a weird request...
					$whodied    = 'I\'m sorry, I don\'t understand that request. Please ask me something else.';
					$endsession = false;
					break;
			}
		}
		$response = array(
			'version'  => '1.0',
			'response' => array(
				'outputSpeech'     => array(
					'type' => 'PlainText',
					'text' => $notice . $whodied,
				),
				'shouldEndSession' => $endsession,
			),
		);
		return $response;
	}

}
new LWTV_Alexa_Skills();
