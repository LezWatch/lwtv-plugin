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

		$type   = ( isset( $request['request']['type'] ) )? $request['request']['type'] : false;
		$date   = ( isset( $request['request']['intent']['slots']['Date']['value'] ) )? $request['request']['intent']['slots']['Date']['value'] : false;
		$req_id = ( isset( $request['request']['session']['application']['applicationId'] ) )? $request['request']['session']['application']['applicationId'] : false;

		$validate_alexa = $this->alexa_validate_request( $request );

		if ( $validate_alexa['success'] != 1 ) {
			$error = new WP_REST_Response( array( 'message' => $validate_alexa['message'], 'data' => array( 'status' => 400 ) ) );
			$error->set_status( 400 );
			return $error;
		}

		$response = $this->bury_your_queers( $type, $date );
		return $response;
	}


	function alexa_validate_request( $request ) {

		$app_id    = 'amzn1.ask.skill.b1b4f1ce-de9c-48cb-ad65-caa6467e6e8c';
		$chain_url = $request->get_header( 'signaturecertchainurl' );
		$timestamp = $request['request']['timestamp'];
		$signature = $request->get_header( 'signature' );

	    // Validate that it even came from Amazon ...
	    if ( !isset( $chain_url ) )
	    	return array( 'success' => 0, 'message' => 'This request did not come from Amazon.' );

	    // Validate proper format of Amazon provided certificate chain url
	    $valid_uri = $this->alexa_valid_key_chain_uri( $chain_url );
	    if ( $valid_uri != 1 )
	    	return array( 'success' => 0, 'message' => $valid_uri );

	    // Validate certificate signature
	    $valid_cert = $this->alexa_valid_cert( $request, $chain_url, $signature );
	    if ( $valid_cert != 1 )
	    	return array ( 'success' => 0, 'message' => $valid_cert );

	    // Validate time stamp
		if (time() - strtotime( $timestamp ) > 60)
			return array ( 'success' => 0, 'message' => 'Timestamp validation failure. Current time: ' . time() . ' vs. Timestamp: ' . $timestamp );

	    return array( 'success' => 1, 'message' => 'Success' );
	}

	/*
		Validate certificate chain URL
	*/
	function alexa_valid_key_chain_uri( $keychainUri ){

	    $uriParts = parse_url( $keychainUri );

	    if (strcasecmp( $uriParts['host'], 's3.amazonaws.com' ) != 0 )
	        return ( 'The host for the Certificate provided in the header is invalid' );

	    if (strpos( $uriParts['path'], '/echo.api/' ) !== 0 )
	        return ( 'The URL path for the Certificate provided in the header is invalid' );

	    if (strcasecmp( $uriParts['scheme'], 'https' ) != 0 )
	        return ( 'The URL is using an unsupported scheme. Should be https' );

	    if (array_key_exists( 'port', $uriParts ) && $uriParts['port'] != '443' )
	        return ( 'The URL is using an unsupported https port' );

	    return 1;
	}

	/*
	    Validate that the certificate and signature are valid
	*/
	function alexa_valid_cert( $request, $chain_url, $signature ) {

		$md5pem     = get_temp_dir() . md5( $chain_url ) . '.pem';
	    $echoDomain = 'echo-api.amazon.com';

	    // If we haven't received a certificate with this URL before,
	    // store it as a cached copy
	    if ( !file_exists( $md5pem ) ) {
		    file_put_contents( $md5pem, file_get_contents( $chain_url ) );
		}

	    $pem = file_get_contents( $md5pem );

	    // Validate certificate chain and signature
	    $ssl_check = openssl_verify( $request->get_body() , base64_decode( $signature ), $pem, 'sha1' );

	    if ($ssl_check != 1 ) {
		    return( openssl_error_string() );
		}

	    // Parse certificate for validations below
	    $parsedCertificate = openssl_x509_parse( $pem );
	    if ( !$parsedCertificate ) return( 'x509 parsing failed' );

	    // Check that the domain echo-api.amazon.com is present in
	    // the Subject Alternative Names (SANs) section of the signing certificate
	    if(strpos( $parsedCertificate['extensions']['subjectAltName'], $echoDomain) === false) {
	        return( 'subjectAltName Check Failed' );
	    }

	    // Check that the signing certificate has not expired
	    // (examine both the Not Before and Not After dates)
	    $validFrom = $parsedCertificate['validFrom_time_t'];
	    $validTo   = $parsedCertificate['validTo_time_t'];
	    $time      = time();

	    if ( !( $validFrom <= $time && $time <= $validTo ) ) {
	        return( 'certificate expiration check failed' );
	    }

	    return 1;
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
	public function bury_your_queers( $type = false, $date = false ) {

		if ( $type == 'LaunchRequest' ) {
			$whodied = 'Welcome to the LezWatch TV Bury Your Queers skill. To find out what queer females died, and when, you can ask me things like "who died" or "who died today" or "who died on March 3rd." If no one died then, I\'ll let you know.';
			$endsession = false;
		} else {
			// Check the timestamp
			$timestamp = ( strtotime( $date ) == false )? false : strtotime( $date ) ;

			if ( $date == false || $timestamp == false ) {
				$data    = LWTV_BYQ_JSON::last_death();
				$name    = $data['name'];
				$date    = date( 'F j, Y', $data['died'] );
				$whodied = 'The last queer female to die was '. $name .' on '. $date;
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
				$sendsession = true;
			}

		}
		$response = array(
			'version'  => '1.0',
			'response' => array (
				'outputSpeech' => array (
					'type' => 'PlainText',
					'text' => $whodied,
				),
				'shouldEndSession' => $endsession,
			)
		);

		return $response;

	}

}
new LWTV_Alexa_Skills();