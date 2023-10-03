<?php
/*
Name: REST-API - Alexa Skills - Common/Shared Tools
Description: There are some things that are used by multiple skills. In order to
minimize duplication, we store them here.
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * class LWTV_Alexa_Common
 */
class LWTV_Alexa_Common {

	public function search_posts( $post_type, $name = false ) {
		global $wpdb;

		// If there's no name or it's not a valid post type, bail.
		if ( ! $name || ! in_array( $post_type, array( 'actors', 'characters', 'shows' ), true ) ) {
			return false;
		}

		// IMDB is stupid.
		switch ( $name ) {
			case 'the l-word':
			case 'the 50 word':
				$name = 'the L word';
				break;
			case 'the 50 word generation Q':
				$name = 'the L word generation Q';
				break;
		}

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

		// Use SQL to find possible name matches, since WP_Query doesn't use "LIKE"
		// for post titles.
		// Ignoring warnings because this is a safe check.
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$possible_ids = $wpdb->get_col( "select ID from $wpdb->posts where post_title LIKE '%" . $name . "%' " );

		if ( $possible_ids ) {
			// If we have IDs, we'll get the posts based on that.
			$queery_args = array(
				'post__in'       => $possible_ids,
				'post_type'      => 'post_type_' . $post_type,
				'post_status'    => 'publish',
				'posts_per_page' => 15,
				'no_found_rows'  => true,
			);
		} else {
			// No IDs shouldn't be possible, but we want a failsafe.
			$queery_args = array(
				'title'          => $name,
				'post_type'      => 'post_type_' . $post_type,
				'post_status'    => 'publish',
				'posts_per_page' => 15,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'no_found_rows'  => true,
			);
		}

		// Return the arguments
		return $queery_args;
	}

	/**
	 * DEPRECATED: Rest API Callback for Bury Your Queers
	 * THIS IS OLD and can be removed once BYQ is retired, but let's not break things...
	 * This accepts POST data
	 */
	public function bury_your_queers_rest_api_callback( WP_REST_Request $request ) {
		$type   = ( isset( $request['request']['type'] ) ) ? $request['request']['type'] : false;
		$intent = ( isset( $request['request']['intent']['name'] ) ) ? $request['request']['intent']['name'] : false;
		$date   = ( isset( $request['request']['intent']['slots']['Date']['value'] ) ) ? $request['request']['intent']['slots']['Date']['value'] : false;
		$req_id = ( isset( $request['request']['session']['application']['applicationId'] ) ) ? $request['request']['session']['application']['applicationId'] : false;

		// Call the validation:
		require_once 'alexa/alexa-validate.php';
		$validate_alexa = ( new LWTV_Alexa_Validate() )->the_request( $request );

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
	 * DEPRECATED: Generate Bury Your Queers
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
						$data    = ( new LWTV_Stats_JSON() )->statistics( 'death', 'simple' );
						$whodied = 'A total of ' . $data['characters']['dead'] . ' queer female, non-binary, and trans characters have died on TV.';
					} elseif ( ! preg_match( '/^[0-9]{4}$/', $date ) ) {
						$whodied    = 'I\'m sorry. I don\'t know how to calculate deaths in anything but years right now. ' . $helptext;
						$endsession = false;
					} else {
						$data     = ( new LWTV_Stats_JSON() )->statistics( 'death', 'years' );
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
						$data    = ( new LWTV_BYQ_JSON() )->last_death();
						$name    = $data['name'];
						$whodied = 'The last queer female, non-binary, or trans character to die was ' . $name . ' on ' . gmdate( 'F j, Y', $data['died'] ) . '.';
					} elseif ( preg_match( '/^[0-9]{4}-(0[1-9]|1[0-2])$/', $date ) ) {
						$whodied    = 'I\'m sorry. I don\'t know how to calculate deaths in anything but days right now. ' . $helptext;
						$endsession = false;
					} else {
						$this_day = gmdate( 'm-d', $timestamp );
						$data     = ( new LWTV_BYQ_JSON() )->on_this_day( $this_day );
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
								++$deadcount;
							}
						}
						$whodied = $how_many . ' on ' . gmdate( 'F jS', $timestamp ) . '. ' . $the_dead;
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

new LWTV_Alexa_Common();
