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
		add_filter( 'vhp_purge_urls', array( $this, 'varnish_urls' ), 10, 2 );
	}

	public function varnish_urls( $urls, $postid ) {

		$myurls = array();

		switch ( get_post_type( $postid ) ) {
			case 'post_type_characters':
				$myurls[] = 'https://lezwatchtv.com/characters/';
				break;
			case 'post_type_actors':
				$myurls[] = 'https://lezwatchtv.com/actors/';
				break;
			case 'post_type_shows':
				$myurls[] = 'https://lezwatchtv.com/shows/';
		}

		if ( ! empty( $myurls ) ) {
			foreach ( $myurls as $url ) {
				array_push( $urls, $url );
			}
		}
		return $urls;
	}
}

new LWTV_Varnish();
