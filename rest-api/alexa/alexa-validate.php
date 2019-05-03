<?php
/*
Description: REST-API - Alexa Skills - Validation

Validates the requests as coming from Amazon

Version: 1.0
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * class LWTV_Alexa_Validate
 */
class LWTV_Alexa_Validate {

	public static function the_request( $request ) {

		$chain_url = $request->get_header( 'signaturecertchainurl' );
		$timestamp = $request['request']['timestamp'];
		$signature = $request->get_header( 'signature' );

		// Validate that it even came from Amazon ...
		if ( ! isset( $chain_url ) ) {
			return array( 'success' => 0, 'message' => 'This request did not come from Amazon.' );
		}

		// Validate proper format of Amazon provided certificate chain url
		$valid_uri = self::key_chain_uri( $chain_url );
		if ( 1 !== $valid_uri ) {
			return array( 'success' => 0, 'message' => $valid_uri );
		}

		// Validate certificate signature
		$valid_cert = self::cert_and_sig( $request, $chain_url, $signature );
		if ( $valid_cert != 1 )
			return array ( 'success' => 0, 'message' => $valid_cert );

		// Validate time stamp
		if (time() - strtotime( $timestamp ) > 60)
			return array ( 'success' => 0, 'message' => 'Timestamp validation failure. Current time: ' . time() . ' vs. Timestamp: ' . strtotime( $timestamp ) );

		return array( 'success' => 1, 'message' => 'Success' );
	}

	/*
		Validate certificate chain URL
	*/
	function key_chain_uri( $keychainUri ){

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
	function cert_and_sig( $request, $chain_url, $signature ) {

		$md5pem      = get_temp_dir() . md5( $chain_url ) . '.pem';
		$echo_domain = 'echo-api.amazon.com';

		// If we haven't received a certificate with this URL before,
		// store it as a cached copy
		if ( ! file_exists( $md5pem ) ) {
			file_put_contents( $md5pem, file_get_contents( $chain_url ) );
		}

		$pem = file_get_contents( $md5pem );

		// Validate certificate chain and signature
		$ssl_check = openssl_verify( $request->get_body(), base64_decode( $signature ), $pem, 'sha1' );

		if ( 1 !== $ssl_check ) {
			return( openssl_error_string() );
		}

		// Parse certificate for validations below
		$parsed_certificate = openssl_x509_parse( $pem );
		if ( ! $parsed_certificate ) {
			return( 'x509 parsing failed' );
		}

		// Check that the domain echo-api.amazon.com is present in
		// the Subject Alternative Names (SANs) section of the signing certificate
		if ( strpos( $parsed_certificate['extensions']['subjectAltName'], $echo_domain ) === false ) {
			return( 'subjectAltName Check Failed' );
		}

		// Check that the signing certificate has not expired
		// (examine both the Not Before and Not After dates)
		$valid_from = $parsed_certificate['validFrom_time_t'];
		$valid_to   = $parsed_certificate['validTo_time_t'];
		$time       = time();

		if ( ! ( $valid_from <= $time && $time <= $valid_to ) ) {
			return( 'certificate expiration check failed' );
		}

		return 1;
	}

}

new LWTV_Alexa_Validate();
