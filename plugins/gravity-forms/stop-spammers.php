<?php
/*
 * Stop Spammers from using Gravity Forms.
 */

class LWTV_Gravity_Forms_Spam {

	public function __construct() {
		// Check all Gravity Forms ... forms for spammers.
		add_action( 'gform_entry_is_spam', array( $this, 'gform_entry_is_spam' ), 10, 3 );

		// Populate ip location
		add_filter( 'gform_field_value_lwtvlocation', array( $this, 'populate_lwtvlocation' ) );
	}

	/**
	 * Mark as spam
	 *
	 * If someone on our block-list emails, auto-mark as spam because we do
	 * not want to hear from them, but we don't want them to know they were rejected
	 * and thus encourage them to try other methods. Aren't assholes fun?
	 *
	 * @param  boolean  $is_spam  -- Is this already spam or not?
	 * @param  array    $form     -- All the form info
	 * @param  array    $entry    -- All info from the entry
	 * @return boolean            true/false if it's "spam"
	 */
	public function gform_entry_is_spam( $is_spam, $form, $entry ) {

		// If this is already spam, we're gonna return and be done.
		if ( $is_spam ) {
			return $is_spam;
		}

		$spam_message = 'Failed internal spam checks';
		$warn_message = '';
		$is_naughty   = array(
			'spam' => false,
			'mod'  => false,
			'bot'  => false,
			'vpn'  => false,
		);

		// Loop and find the email:
		foreach ( $entry as $value => $key ) {

			// If it's empty, let's continue.
			if ( is_null( $key ) ) {
				continue;
			}

			// If we're already a spammer, break.
			if ( $is_naughty['spam'] ) {
				break;
			}

			if ( is_email( $key ) ) {
				$email              = $key;
				$is_naughty['spam'] = LWTV_Find_Spammers::is_spammer( $email, 'email', 'disallowed_keys' );
				$is_naughty['mod']  = LWTV_Find_Spammers::is_spammer( $email, 'email', 'moderated_keys' );
			}

			if ( rest_is_ip_address( (string) $key ) ) {
				$ip                 = (string) $key;
				$check_ip_address   = self::check_ip_location( $ip );
				$is_naughty['spam'] = LWTV_Find_Spammers::is_spammer( $ip, 'ip', 'disallowed_keys' );
				$is_naughty['mod']  = LWTV_Find_Spammers::is_spammer( $ip, 'ip', 'moderated_keys' );
				$is_naughty['bot']  = $check_ip_address['bot'];
				$is_naughty['vpn']  = $check_ip_address['vpn'];
			}
		}

		// If this was a bot...
		if ( $is_naughty['bot'] ) {
			$warn_message .= 'Likely submitted by a bot or someone scripting. ';
		}

		// If a VPN...
		if ( $is_naughty['vpn'] ) {
			$warn_message .= 'Using a VPN. This may be harmless, but it\'s also how people evade bans. ';
		}

		// If the person is Moderated...
		if ( $is_naughty['mod'] ) {
			$warn_message .= 'Flagged for post moderation. Probably okay, but may be a pill later. ';
		}

		// And if it's a spammer...
		if ( $is_naughty['spam'] ) {
			$message = $spam_message;

			if ( isset( $email ) ) {
				$message .= ' - Email ( ' . $email . ' )';
			}
			if ( isset( $ip ) ) {
				$message .= ' - IP Address ( ' . $ip . ' )';
			}

			$result = GFAPI::add_note( $entry['id'], 0, 'LWTV Robot', $message, 'error', 'spam' );
			return true;
		} elseif ( ! empty( $warn_message ) ) {
			$add_note = GFAPI::add_note( $entry['id'], 0, 'LWTV Robot', $warn_message, 'warning', 'spam' );
		}

		// If we got all the way down here, we're not spam!
		return false;
	}

	/**
	 * IP Checker
	 */
	public function check_ip_location( $ip ) {
		$return    = array(
			'ip'   => $ip,
			'bot'  => false,
			'vpn'  => false,
			'full' => $ip,
		);
		$localhost = array( '127.0.0.1', '::1', 'localhost' );

		if ( in_array( $ip, $localhost, true ) ) {
			$return['full'] = 'localhost';
		} else {
			$api     = 'http://ip-api.com/json/' . $ip;
			$request = wp_remote_get( $api );

			if ( is_wp_error( $request ) ) {
				return $return; // Bail early
			}

			$body = wp_remote_retrieve_body( $request );
			$data = json_decode( $body );

			$country = ( isset( $data->countryCode ) ) ? ' - ' . $data->countryCode : ''; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$city    = ( isset( $data->countryCode ) ) ? ' / ' . $data->city : ''; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

			$return['full'] = $ip . ' ' . $country . $city;
			$return['bot']  = ( isset( $data->hosting ) && true === $data->hosting ) ? true : false;
			$return['vpn']  = ( isset( $data->proxy ) && true === $data->proxy ) ? true : false;
		}

		return $return;
	}

	/**
	 * Get the IP and process it.
	 *
	 * To actually use this, though, you need a HIDDEN field, which has the following:
	 *
	 * 1. Set ALLOW FIELD TO BE POPULATED DYNAMICALLY
	 * 2. Parameter Name == lwtvlocation
	 */
	// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
	public function populate_lwtvlocation( $value ) {
		if ( array_key_exists( 'HTTP_X_FORWARDED_FOR', $_SERVER ) ) {
			$ip = (string) $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif ( array_key_exists( 'REMOTE_ADDR', $_SERVER ) ) {
			$ip = (string) $_SERVER['REMOTE_ADDR'];
		} elseif ( array_key_exists( 'HTTP_CLIENT_IP', $_SERVER ) ) {
			$ip = (string) $_SERVER['HTTP_CLIENT_IP'];
		} else {
			// Honestly one of those should exist...
			return;
		}

		// If there's a comma we have multiple proxies and want to grab the first. If not, trust.
		if ( str_contains( $ip, ',' ) ) {
			$ips     = array_values( array_filter( explode( ',', $ip ) ) );
			$real_ip = reset( $ips );
		} else {
			$real_ip = $ip;
		}

		$location = self::check_ip_location( $real_ip );

		return $location['full'];
	}
}

new LWTV_Gravity_Forms_Spam();
