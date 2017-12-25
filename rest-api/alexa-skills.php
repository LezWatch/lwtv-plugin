<?php
/*
Description: REST-API - Alexa Skills

For Amazon Alexa Skills

Version: 1.0
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

		// News Skill (rebranded BYQ)
		register_rest_route( 'lwtv/v2', '/alexa-skills/news/', array(
			'methods' => [ 'GET', 'POST' ],
			'callback' => array( $this, 'news_rest_api_callback' ),
		) );
	}

	/**
	 * Rest API Callback for Flash Briefing
	 */
	public function flash_briefing_rest_api_callback( $data ) {
		include_once( 'alexa/flash-brief.php' );
		$response = LWTV_Alexa_Flash_Brief::flash_briefing();
		return $response;
	}

	/**
	 * Rest API Callback for Bury Your Queers
	 * THIS IS OLD and can be removed once BYQ is retired, but let's not break things...
	 * This accepts POST data
	 */
	public function bury_your_queers_rest_api_callback( WP_REST_Request $request ) {
		$type   = ( isset( $request['request']['type'] ) )? $request['request']['type'] : false;
		$intent = ( isset( $request['request']['intent']['name'] ) )? $request['request']['intent']['name'] : false;
		$date   = ( isset( $request['request']['intent']['slots']['Date']['value'] ) )? $request['request']['intent']['slots']['Date']['value'] : false;
		$req_id = ( isset( $request['request']['session']['application']['applicationId'] ) )? $request['request']['session']['application']['applicationId'] : false;

		// Call the validation:
		include_once( 'alexa/alexa-validate.php' );
		$validate_alexa = LWTV_Alexa_Validate::the_request( $request );

		if ( $validate_alexa['success'] != 1 ) {
			$error = new WP_REST_Response( array( 'message' => $validate_alexa['message'], 'data' => array( 'status' => 400 ) ) );
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

		$type   = ( isset( $request['request']['type'] ) )? $request['request']['type'] : false;
		$intent = ( isset( $request['request']['intent']['name'] ) )? sanitize_text_field( $request['request']['intent']['name'] ) : false;
		$date   = ( isset( $request['request']['intent']['slots']['Date']['value'] ) )? $request['request']['intent']['slots']['Date']['value'] : false;
		$actor  = ( isset( $request['request']['intent']['slots']['actor']['value'] ) )? sanitize_text_field( $request['request']['intent']['slots']['actor']['value'] ) : false;
		$req_id = ( isset( $request['request']['session']['application']['applicationId'] ) )? sanitize_text_field( $request['request']['session']['application']['applicationId'] ) : false;

		// Call the validation:
		include_once( 'alexa/alexa-validate.php' );
		$validate_alexa = LWTV_Alexa_Validate::the_request( $request );
		if ( $validate_alexa['success'] != 1 ) {
			$error = new WP_REST_Response( array( 'message' => $validate_alexa['message'], 'data' => array( 'status' => 400 ) ) );
			$error->set_status( 400 );
			return $error;
		}
		$response = $this->news_skill( $type, $intent, $date, $actor );

		return $response;
	}

	/**
	 * Generate News (formerly Bury Your Queers)
	 *
	 * @access public
	 * @return void
	 */
	public function news_skill( $type = false, $intent = false, $date = false, $actor = false ) {

		$helptext   = 'You can ask me what happened or for information on queer female and characters or shows on Lez Watch T. V.. Try asking me "What happened this year?" or "What happened in 1989?" or "Who is the character of the day?" or "Who is Laverne Cox?" or "Is Ali Liebert queer?" or even "Who died on March 3rd?" -- I\'ll let you know what I\'ve found.';
		$output = 'I\'m sorry, I don\'t understand that request. Please ask me something else. ' . $helptext;
		$endsession = true;

		if ( $date !== false && is_numeric( substr( $date, 0, 4 ) ) && substr( $date, 0, 4 ) < FIRST_LWTV_YEAR ) {
			$output     = 'There were no queer female or trans characters on T. V. prior to ' . FIRST_LWTV_YEAR . '. Would you like to ask me something else? ' . $helptext;
			$endsession = false;
		} elseif ( $type == 'LaunchRequest' ) {
			$output     = 'Welcome to the Lez Watch T. V. skill. ' . $helptext;
			$endsession = false;
		} else {
			switch ( $intent ) {
				case 'HowMany':
					include_once( 'alexa/byq.php' );
					if ( $date == false ) {
						$output = LWTV_Alexa_BYQ::how_many( 'simple' );
					} else {
						$output = LWTV_Alexa_BYQ::how_many( $date );
					}
					break;
				case 'CharOTD':
					$data       = get_option( 'lwtv_otd' );
					$post_id    = $data[ 'character' ][ 'post' ];
					$output     = 'The Lez Watch T. V. character of the day is '. get_the_title( $post_id ) .'.';
					break;
				case 'ShowOTD':
					$data       = get_option( 'lwtv_otd' );
					$post_id    = $data[ 'show' ][ 'post' ];
					$output     = 'The Lez Watch T. V. show of the day is '. get_the_title( $post_id ) .'.';
					break;
				case 'WhatsNew':
					include_once( 'alexa/newest.php' );
					$output     = LWTV_Alexa_Newest::whats_new();
					break;
				case 'WhoDied':
					if ( !$date ) {
						include_once( 'alexa/newest.php' );
						$output = 'The last character on Lez Watch T. V. to die was ' . LWTV_Alexa_Newest::death() . '.';
					} else {
						include_once( 'alexa/byq.php' );
						$output = LWTV_Alexa_BYQ::on_a_day( $timestamp );
					}
					break;
				case 'WhatHappened':
					include_once( 'alexa/this-year.php' );
					$output     = LWTV_Alexa_This_Year::what_happened( $date );
					break;
				case 'WhoAreYou':
					if ( !$actor ) {
						$output = 'I\'m sorry, I didn\'t quite catch the name of the actor you\'re asking about. Can you please ask me again? I\'ll listen harder.';
					} else {
						include_once( 'alexa/who-are-you.php' );
						$output     = LWTV_Alexa_Who::who_is( $actor );
					}
					break;
				case 'IsQueer':
					if ( !$actor ) {
						$output = 'I\'m sorry, I didn\'t quite catch the name of the actor you\'re asking about. Can you please ask me again? I\'ll listen harder.';
					} else {
						include_once( 'alexa/who-are-you.php' );
						$output     = LWTV_Alexa_Who::is_gay( $actor );
					}
					break;
				case 'AMAZON.HelpIntent':
					$output     = 'This is the News skill by Lez Watch T. V. News, home of the world\'s greatest database of queer female and trans characters on TV. ' . $helptext;
					$endsession = false;
					break;
				case 'AMAZON.StopIntent':
				case 'AMAZON.CancelIntent':
					$output     = '';
					break;
			}
		}

		// Return response
		$response = array(
			'version'  => '1.0',
			'response' => array (
				'outputSpeech' => array (
					'type' => 'PlainText',
					'text' => $output,
				),
				'shouldEndSession' => $endsession,
			)
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
		$timestamp  = ( strtotime( $date ) == false )? false : strtotime( $date ) ;
		$helptext   = 'You can find out who died on specific dates by asking me questions like "who died" or "who died today" or "who died on March 3rd" or even "How many died in 2017." If no one died then, I\'ll let you know.';
		if ( $type == 'LaunchRequest' ) {
			$whodied = 'Welcome to the Lez Watch T. V. Bury Your Queers skill. ' . $helptext;
			$endsession = false;
		} else {
			if ( $intent == 'AMAZON.HelpIntent' ) {
				$whodied = 'This is the Bury Your Queers skill by Lez Watch T. V., home of the world\'s greatest database of queer female on TV. ' . $helptext;
				$endsession = false;
			} elseif ( $intent == 'AMAZON.StopIntent' || $intent == 'AMAZON.CancelIntent' ) {
				// Do nothing
			} elseif ( $intent == 'HowMany' ) {
				if ( $date == false || $timestamp == false ) {
					$data     = LWTV_Stats_JSON::statistics( 'death', 'simple' );
					$whodied  = 'A total of '. $data['characters']['dead'] .' queer female characters have died on TV.';
				} elseif ( !preg_match( '/^[0-9]{4}$/' , $date ) ) {
					$whodied    = 'I\'m sorry. I don\'t know how to calculate deaths in anything but years right now. ' . $helptext;
					$endsession = false;
				} else {
					$data     = LWTV_Stats_JSON::statistics( 'death', 'years' );
					$count    = $data[$date]['count'];
					$how_many = 'No queer female characters died on TV in ' . $date . '.';
					if ( $count > 0 ) {
						$how_many = $count .' queer female ' . _n( 'character', 'characters', $count ) . ' died on TV in ' . $date . '.';
					}
					$whodied  = $how_many;
				}
			} elseif ( $intent == 'WhoDied' ) {
				if ( $date == false || $timestamp == false ) {
					$data    = LWTV_BYQ_JSON::last_death();
					$name    = $data['name'];
					$whodied = 'The last queer female to die was '. $name .' on '. date( 'F j, Y', $data['died'] ) .'.';
				} elseif ( preg_match( '/^[0-9]{4}-(0[1-9]|1[0-2])$/' , $date ) ) {
					$whodied    = 'I\'m sorry. I don\'t know how to calculate deaths in anything but days right now. ' . $helptext;
					$endsession = false;
				} else {
					$this_day = date('m-d', $timestamp );
					$data     = LWTV_BYQ_JSON::on_this_day( $this_day );
					$count    = ( key( $data ) == 'none' )? 0 : count( $data ) ;
					$how_many = 'No queer females died';
					$the_dead = '';
					if ( $count > 0 ) {
						$how_many  = $count . ' queer female ' . _n( 'character', 'characters', $count ) . ' died';
						$deadcount = 1;
						foreach ( $data as $dead_character ) {
							if ( $deadcount == $count && $count !== 1 ) $the_dead .= 'And ';
							$the_dead .= $dead_character['name'] . ' in ' . $dead_character['died'] . '. ';
							$deadcount++;
						}
					}
					$whodied = $how_many . ' on '. date('F jS', $timestamp ) . '. ' . $the_dead;
				}
			} else {
				// We have a weird request...
				$whodied = 'I\'m sorry, I don\'t understand that request. Please ask me something else.';
				$endsession = false;
			}
		}
		$response = array(
			'version'  => '1.0',
			'response' => array (
				'outputSpeech' => array (
					'type' => 'PlainText',
					'text' => $notice . $whodied,
				),
				'shouldEndSession' => $endsession,
			)
		);
		return $response;
	}

}
new LWTV_Alexa_Skills();