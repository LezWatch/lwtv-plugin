<?php
/**
 * Find Spammers -- this allows us to pick out only emails and reduce false negatives on killing them.
 */

namespace LWTV\Features;

class Spammers {

	/**
	 * List of disallowed Keys
	 *
	 * We check for emails, domains, and IPs.
	 *
	 * @return array the list
	 */
	public static function list( $keys = 'disallowed_keys' ) {

		// Preflight check:
		$valid_keys = array( 'disallowed_keys', 'moderation_keys' );
		$keys       = ( in_array( $keys, $valid_keys, true ) ) ? $keys : 'disallowed_keys';

		// Time for the show!
		$disallowed_keys  = array();
		$disallowed_array = explode( "\n", get_option( $keys ) );

		// Make a list of spammer emails and domains.
		foreach ( $disallowed_array as $spammer ) {
			if ( is_email( $spammer ) ) {
				// This is an email address, so it's valid.
				$disallowed_keys[] = $spammer;
			} elseif ( strpos( $spammer, '@' ) !== false ) {
				// This contains an @ so it's probably a whole domain.
				$disallowed_keys[] = $spammer;
			} elseif ( rest_is_ip_address( $spammer ) ) {
				// IP adresses are also spammery people.
				$disallowed_keys[] = $spammer;
			}
		}

		return $disallowed_keys;
	}

	/**
	 * Is someone a spammer...
	 * @param  string  $email_address The email address
	 * @param  string  $plugin        The plugin we're checking (default FALSE)
	 * @return boolean                True/False spammer
	 */
	public static function is_spammer( $to_check, $type = 'email', $keys = 'disallowed_keys' ) {

		// If nothing was passed through, we cannot check at all so bail.
		if ( empty( $to_check ) ) {
			return false;
		}

		// Get disallowed keys & convert to array
		$disallowed = self::list( $keys );

		if ( 'email' === $type ) {
			$email_address = $to_check;

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
				return true;
			}
		}

		if ( 'ip' === $type ) {
			$ip      = $to_check;
			$bad_ips = false;
			foreach ( $disallowed as $nope ) {
				// Only check the IPs:
				if ( rest_is_ip_address( $nope ) ) {
					if ( ( strpos( $ip, $nope ) !== false ) || $ip === $nope ) {
						// If it's a match, it's a spam, return true and end.
						return true;
					}
				}
			}
		}

		// If we got down here, we're not spam.
		return false;
	}
}
