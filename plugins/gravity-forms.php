<?php
/*
 * Gravity Forms for WordPress hooks
 * @package lwtv-plugin
 */

class LWTV_Gravity_Forms {

	public function __construct() {
		// https://docs.gravityforms.com/gform_disable_view_counter/#1-disable-for-all-forms
		add_filter( 'gform_disable_view_counter', '__return_true' );

		// Check all Gravity Forms ... forms for jerks.
		add_action( 'gform_entry_is_spam', array( $this, 'gform_entry_is_spam' ), 10, 3 );

		// Set some Defaults
		add_action( 'gform_editor_js_set_default_values', array( $this, 'set_defaults' ) );

		// Populate ip location
		add_filter( 'gform_field_value_lwtvlocation', array( $this, 'populate_lwtvlocation' ) );

		// Add Location to forms
		add_filter( 'gform_entry_post_save', array( $this, 'gform_entry_post_save' ), 10, 2 );
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

		$message = 'Failed internal spam checks';

		// Loop and find the email:
		foreach ( $entry as $value => $key ) {
			if ( is_email( $key ) ) {
				$email      = $key;
				$is_spammer = LWTV_Find_Spammers::is_spammer( $email, 'email' );
			}
			if ( rest_is_ip_address( $key ) ) {
				$ip         = $key;
				$is_spammer = LWTV_Find_Spammers::is_spammer( $ip, 'ip' );
			}
		}

		if ( $is_spammer ) {
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
	public function gform_entry_post_save( $entry, $form ) {
		if ( isset( $entry['ip'] ) ) {
			$location = 'Submitted from ' . self::check_ip_location( $entry['ip'] );

			// Update the entry
			$result = GFAPI::add_note( $entry['id'], 0, 'LWTV Robot', $location );
		}

		return $entry;
	}

	/**
	 * IP Checker
	 */
	public function check_ip_location( $ip ) {
		$return = $ip;

		$api     = 'http://ip-api.com/json/' . $ip;
		$request = wp_remote_get( $api );

		if ( is_wp_error( $request ) ) {
			return $ip; // Bail early
		}

		$body = wp_remote_retrieve_body( $request );
		$data = json_decode( $body );

		// Return: US - Chicago
		$return = $data->countryCode . ' - ' . $data->city;

		return $return;
	}

	/**
	 * Get the IP and process it.
	 */
	public function populate_lwtvlocation( $value ) {

		$ip  = rgar( $_SERVER, 'REMOTE_ADDR' );
		$ip  = apply_filters( 'gform_ip_address', $ip );
		$ips = explode( ',', $ip );

		$real_ip  = $ips[0];

		$real_ip = '94.66.59.129';
		$location = self::check_ip_location( $real_ip );

		return $location;
	}

	public function set_defaults() {
		?>
		//this hook is fired in the middle of a switch statement,
		//so we need to add a case for our new field type
		case "lwtvlocation" :
			field.label = "Location Tracker (Hidden)";
		break;
		<?php
	}
}

new LWTV_Gravity_Forms();

// Include add-ons
require_once 'gravity-forms/class-field-location.php';
require_once 'gravity-forms/class-gf-approvals.php';
