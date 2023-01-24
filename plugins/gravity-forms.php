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

		// Add Location to forms
		add_filter( 'gform_entry_post_save', array( $this, 'gform_entry_post_save_ip' ), 10, 2 );
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
		$bot_message  = 'Likely submitted by a bot or someone scripting.';
		$vpn_message  = 'Submitted via a VPN. This may be harmless, but it\'s also how people evade bans.';
		$is_spammer   = false;
		$is_bot       = false;
		$is_vpn       = false;

		// Loop and find the email:
		foreach ( $entry as $value => $key ) {
			if ( is_email( $key ) ) {
				$email      = $key;
				$is_spammer = LWTV_Find_Spammers::is_spammer( $email, 'email' );
			}
			if ( rest_is_ip_address( $key ) ) {
				$ip         = $key;
				$is_spammer = LWTV_Find_Spammers::is_spammer( $ip, 'ip' );
				$is_bot     = self::check_ip_location( $ip, 'hosting' );
				$is_vpn     = self::check_ip_location( $ip, 'proxy' );
			}
		}

		if ( false !== $is_bot ) {
			$add_note = GFAPI::add_note( $entry['id'], 0, 'LWTV Robot', $bot_message, 'warning', 'spam' );
		}

		if ( false !== $is_vpn ) {
			$add_note = GFAPI::add_note( $entry['id'], 0, 'LWTV Robot', $vpn_message, 'warning', 'spam' );
		}

		if ( $is_spammer ) {
			$message = $spam_message;

			if ( isset( $email ) ) {
				$message .= ' - Email';
			}
			if ( isset( $ip ) ) {
				$message .= ' - IP Address';
			}

			$result = GFAPI::add_note( $entry['id'], 0, 'LWTV Robot', $message, 'error', 'spam' );
			return true;
		}

		// If we got all the way down here, we're not spam!
		return false;
	}

	/**
	 * Update forms on save. If there's an IP, we're going to add a note with this check.
	 */
	public function gform_entry_post_save_ip( $entry, $form ) {
		if ( isset( $entry['ip'] ) ) {
			$location = 'Submitted from ' . self::check_ip_location( $entry['ip'] );

			// Update the entry
			$result = GFAPI::add_note( $entry['id'], 0, 'LWTV Robot', $location, 'tracking', 'location' );
		}

		return $entry;
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
					$return .= ( isset( $data->countryCode ) ) ? ' ' . $data->countryCode : ''; // phpcs:ignore
					$return .= ( isset( $data->countryCode ) ) ? ' - ' . $data->city : ''; // phpcs:ignore
					$return .= ( isset( $data->proxy ) && true === $data->proxy ) ? ' (VPN)' : ''; // phpcs:ignore
					break;
				case 'hosting':
					$return = ( isset( $data->hosting ) && true === $data->hosting ) ? 'likely-bot' : ''; // phpcs:ignore
					break;
				case 'proxy':
					$return .= ( isset( $data->proxy ) && true === $data->proxy ) ? 'is-vpn' : ''; // phpcs:ignore
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
