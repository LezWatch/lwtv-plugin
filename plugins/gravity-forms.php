<?php
/*
 * Gravity Forms for WordPress hooks
 * @package lwtv-plugin
 */

class LWTV_Gravity_Forms {

	public function __construct() {
		// https://docs.gravityforms.com/gform_disable_view_counter/#1-disable-for-all-forms
		add_filter( 'gform_disable_view_counter', '__return_true' );

		// Check all Gravity Forms ... forms for spammers.
		add_action( 'gform_entry_is_spam', array( $this, 'gform_entry_is_spam' ), 10, 3 );

		// Populate ip location
		add_filter( 'gform_field_value_lwtvlocation', array( $this, 'populate_lwtvlocation' ) );
	}

	/**
	 * Mark as spam
	 *
	 * If someone on our block-list emails, auto-mark as spam becuase we do
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
		$is_spammer   = false;
		$is_moderated = false;
		$is_bot       = false;
		$is_vpn       = false;

		// Loop and find the email:
		foreach ( $entry as $value => $key ) {

			// If it's empty, we're just going to return.
			if ( is_null( $key ) ) {
				return false;
			}

			if ( is_email( $key ) && ! $is_spammer ) {
				$email        = $key;
				$is_spammer   = LWTV_Find_Spammers::is_spammer( $email, 'email', 'disallowed_keys' );
				$is_moderated = LWTV_Find_Spammers::is_spammer( $email, 'email', 'moderated_keys' );
			}

			if ( rest_is_ip_address( $key ) && ! $is_spammer ) {
				$ip           = (string) $key;
				$is_spammer   = LWTV_Find_Spammers::is_spammer( $ip, 'ip', 'disallowed_keys' );
				$is_moderated = LWTV_Find_Spammers::is_spammer( $ip, 'ip', 'moderated_keys' );
				$is_bot       = self::check_ip_location( $ip, 'hosting' );
				$is_vpn       = self::check_ip_location( $ip, 'proxy' );
			}
		}

		// If this was a bot...
		if ( true === $is_bot ) {
			$warn_message .= 'Likely submitted by a bot or someone scripting. ';
		}

		// If a VPN...
		if ( true === $is_vpn ) {
			$warn_message .= 'Using a VPN. This may be harmless, but it\'s also how people evade bans. ';
		}

		// And if it's a spammer...
		if ( $is_spammer ) {
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
	public function check_ip_location( $ip, $format = 'full' ) {
		$return    = $ip;
		$localhost = array( '127.0.0.1', '::1', 'localhost' );

		if ( in_array( $ip, $localhost, true ) ) {
			$return = 'localhost';
		} else {
			$api     = 'http://ip-api.com/json/' . $ip;
			$request = wp_remote_get( $api );

			if ( is_wp_error( $request ) ) {
				return $ip; // Bail early
			}

			$body = wp_remote_retrieve_body( $request );
			$data = json_decode( $body );

			switch ( $format ) {
				case 'full':
					// Return: US - Chicago
					$return .= ( isset( $data->countryCode ) ) ? ' ' . $data->countryCode : ''; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$return .= ( isset( $data->countryCode ) ) ? ' - ' . $data->city : ''; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$return .= ( isset( $data->proxy ) && true === $data->proxy ) ? ' (VPN)' : '';
					break;
				case 'hosting':
					$return = ( isset( $data->hosting ) && true === $data->hosting ) ? true : false;
					break;
				case 'proxy':
					$return .= ( isset( $data->proxy ) && true === $data->proxy ) ? true : false;
					break;
			}
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
		$ip  = sanitize_text_field( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'] );
		$ip  = apply_filters( 'gform_ip_address', $ip );
		$ips = explode( ',', $ip );

		$real_ip  = $ips[0];
		$location = self::check_ip_location( $real_ip );

		return $location;
	}
}

new LWTV_Gravity_Forms();

// Include add-ons
if ( method_exists( 'GFForms', 'include_feed_addon_framework' ) ) {
	require_once 'gravity-forms/class-gf-approvals.php';
}
