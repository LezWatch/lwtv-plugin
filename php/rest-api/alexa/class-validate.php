<?php
/*
Name: REST-API - Alexa Skills - Validation
Description: Validates the requests as coming from Amazon
*/

namespace LWTV\Rest_API\Alexa;

class Validate {

	public function the_request( $request ) {

		$chain_url = $request->get_header( 'signaturecertchainurl' );
		$timestamp = $request['request']['timestamp'];
		$signature = $request->get_header( 'signature' );

		// Validate that it even came from Amazon ...
		if ( ! isset( $chain_url ) ) {
			$fail_chain_url = array(
				'success' => 0,
				'message' => 'This request did not come from Amazon.',
			);
			return $fail_chain_url;
		}

		// Validate proper format of Amazon provided certificate chain url
		$valid_uri = self::key_chain_uri( $chain_url );
		if ( 1 !== $valid_uri ) {
			$fail_valid_uri = array(
				'success' => 0,
				'message' => $valid_uri,
			);
			return $fail_valid_uri;
		}

		// Validate certificate signature
		$valid_cert = self::cert_and_sig( $request, $chain_url, $signature );
		if ( 1 !== $valid_cert ) {
			$fail_valid_cert = array(
				'success' => 0,
				'message' => $valid_cert,
			);
			return $fail_valid_cert;
		}

		// Validate time stamp
		if ( time() - strtotime( $timestamp ) > 60 ) {
			$fail_timestamp = array(
				'success' => 0,
				'message' => 'Timestamp validation failure. Current time: ' . time() . ' vs. Timestamp: ' . strtotime( $timestamp ),
			);
			return $fail_timestamp;
		}

		// If we got here, we're a success!
		$success = array(
			'success' => 1,
			'message' => 'Success',
		);
		return $success;
	}

	/*
		Validate certificate chain URL
	*/
	public function key_chain_uri( $keychain_uri ) {

		$uri_parts = wp_parse_url( $keychain_uri );

		// If the host is not amazon, fail.
		if ( strcasecmp( $uri_parts['host'], 's3.amazonaws.com' ) !== 0 ) {
			return ( 'The host for the Certificate provided in the header is invalid' );
		}

		// If the path is not echo.api, fail.
		if ( strpos( $uri_parts['path'], '/echo.api/' ) !== 0 ) {
			return ( 'The URL path for the Certificate provided in the header is invalid' );
		}

		// If it's not HTTPS, we fail.
		if ( strcasecmp( $uri_parts['scheme'], 'https' ) !== 0 ) {
			return ( 'The URL is using an unsupported scheme. Should be https' );
		}

		// If the port is wrong, we fail.
		if ( array_key_exists( 'port', $uri_parts ) && '443' !== $uri_parts['port'] ) {
			return ( 'The URL is using an unsupported https port' );
		}

		return 1;
	}

	/*
	 * Validate that the certificate and signature are valid
	 */
	public function cert_and_sig( $request, $chain_url, $signature ) {

		$md5pem      = get_temp_dir() . md5( $chain_url ) . '.pem';
		$echo_domain = 'echo-api.amazon.com';

		// If we haven't received a certificate with this URL before,
		// store it as a cached copy
		if ( ! file_exists( $md5pem ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
			file_put_contents( $md5pem, file_get_contents( $chain_url ) );
		}

		// Save the pem file as a variable
		$pem = file_get_contents( $md5pem );

		// Validate certificate chain and signature
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
		$ssl_check = openssl_verify( $request->get_body(), base64_decode( $signature ), $pem, 'sha1' );

		// Verify SSL
		if ( 1 !== $ssl_check ) {
			return( openssl_error_string() );
		}

		// Parse certificate for validations below
		$parsed_cert = openssl_x509_parse( $pem );
		if ( ! $parsed_cert ) {
			return( 'x509 parsing failed' );
		}

		// Check that the domain echo-api.amazon.com is present in
		// the Subject Alternative Names (SANs) section of the signing certificate
		if ( strpos( $parsed_cert['extensions']['subjectAltName'], $echo_domain ) === false ) {
			return( 'subjectAltName Check Failed' );
		}

		// Check that the signing certificate has not expired
		// (examine both the Not Before and Not After dates)
		$valid_from = $parsed_cert['validFrom_time_t'];
		$valid_to   = $parsed_cert['validTo_time_t'];
		$time       = time();

		if ( ! ( $valid_from <= $time && $time <= $valid_to ) ) {
			return( 'certificate expiration check failed' );
		}

		return 1;
	}
}
