<?php
/*
Find Spammers -- this allows us to pick out only emails and reduce false negatives on killing them.
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * class LP_Find_Spammers
 * @since 1.0
 */
class LP_Find_Spammers {

	/**
	 * List of disallowed emails
	 *
	 * We omit anything that isn't an email address or has an @ in the string.
	 *
	 * @return array the list
	 */
	public static function list() {
		$disallowed_emails = array();
		$disallowed_array  = explode( "\n", get_option( 'disallowed_keys' ) );

		// Make a list of spammer emails and domains.
		foreach ( $disallowed_array as $spammer ) {
			if ( is_email( $spammer ) ) {
				// This is an email address, so it's valid.
				$disallowed_emails[] = $spammer;
			} elseif ( strpos( $spammer, '@' ) !== false ) {
				// This contains an @ so it's probably a whole domain.
				$disallowed_emails[] = $spammer;
			}
		}

		return $disallowed_emails;
	}

	/**
	 * Is someone a spammer...
	 * @param  string  $email_address The email address
	 * @param  string  $plugin        The plugin we're checking (default FALSE)
	 * @return boolean                True/False spammer
	 */
	public static function is_spammer( $email_address ) {

		// Default assume good people.
		$return = false;

		// Get disallowed keys & convert to array
		$disallowed = self::list();

		// Break apart email into parts
		$emailparts = explode( '@', $email_address );
		$username   = $emailparts[0];       // i.e. foobar
		$domain     = '@' . $emailparts[1]; // i.e. @example.com

		// Remove all periods (i.e. foo.bar > foobar )
		$clean_username = str_replace( '.', '', $username );

		// Remove everything AFTER a + sign (i.e. foobar+spamavoid > foobar )
		$clean_username = strstr( $clean_username, '+', true ) ? strstr( $clean_username, '+', true ) : $clean_username;

		// rebuild email now that it's clean.
		$email = $clean_username . '@' . $emailparts[1];

		// If the email OR the domain is an exact match in the array, then it's a spammer
		if ( in_array( $email, $disallowed, true ) || in_array( $domain, $disallowed, true ) ) {
			$return = true;
		}

		return $return;
	}

}

new LP_Find_Spammers();
