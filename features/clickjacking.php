<?php
/*
 * ClickJacking
 *
 * https://meta.trac.wordpress.org/browser/sites/trunk/wordcamp.org/public_html/wp-content/mu-plugins/security.php?rev=5517
 *
 * Core does this automatically for wp-admin, and it's usefulness is debatable on the front-end. If nothing
 * else, though, it cuts down on the number of HackerOne reports we get from researchers looking for
 * low-hanging fruit.
 *
 * @version 1.0
 * @package library
*/

class LezWatch_ClickJacking {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'wp_headers', array( $this, 'modify_front_end_http_headers' ), 10, 2 );
	}

	/**
	 * Modify the response HTTP headers for front-end requests
	 *
	 * @param array $headers
	 * @param WP    $wp
	 *
	 * @return array
	 */
	public function modify_front_end_http_headers( $headers, $wp ) {
		// The oEmbed endpoints should remain embedable.
		if ( ! isset( $wp->query_vars['embed'] ) || ! $wp->query_vars['embed'] ) {
			$headers['X-Frame-Options'] = 'SAMEORIGIN';
		}

		return $headers;
	}

}

new LezWatch_ClickJacking();
