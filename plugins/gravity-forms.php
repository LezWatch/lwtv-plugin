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

		// Loop and find the email:
		foreach ( $entry as $value => $key ) {
			if ( is_email( $key ) ) {
				$email = $key;
			}
		}

		// If the email is set ...
		if ( isset( $email ) && is_email( $email ) ) {
			// Check if they're a spammer.
			$is_spammer = LP_Find_Spammers::is_spammer( $email );
			if ( $is_spammer ) {
				return true;
			}
		}

		// If we got all the way down here, we're not spam!
		return false;
	}
}

new LWTV_Gravity_Forms();

// Include add-ons
require_once 'gravity-forms/class-gf-approvals.php';