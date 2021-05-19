<?php
/**
 * Name: Varnish Filters
 *
 * To purge extra pages
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

class LWTV_Varnish {

	public function __construct() {
		add_filter( 'vhp_purge_urls', array( $this, 'varnish_urls' ) );
	}

	public function varnish_urls( $urls, $postid ) {
		$myurls = array(
			'https://lezwatchtv.com/characters/',
			'https://lezwatchtv.com/actors/',
			'https://lezwatchtv.com/shows/',
		);

		if ( ! empty( $myurls ) ) {
			foreach ( $myurls as $url ) {
				array_push( $urls, $url );
			}
		}
		return $urls;
	}
}

new LWTV_Varnish();
