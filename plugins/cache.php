<?php
/*
 * Weird Cache commands.
 * @version 1.0
 * @package lwtv-plugin
 */

class LWTV_Cache {

	/**
	 * Clean URLs
	 * @return void
	 */
	public static function clean_urls( $clear_urls ) {
		foreach ( $clear_urls as $url ) {
			// Reload the data by calling the page
			$request = wp_remote_get( $url . '/?nocache' );

			// Proxy Cache Purge (Varnish)
			if ( class_exists( 'VarnishPurger' ) ) {
				VarnishPurger::purge_url( $url );
			}

			// Rocket Cache
			if ( function_exists( 'is_wp_rocket_active' ) && is_wp_rocket_active() ) {
				rocket_clean_files( $clear_urls );
			}
		}
	}
}

new LWTV_Cache();
