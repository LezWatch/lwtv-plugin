<?php
/*
 * Weird Cache commands.
 * @version 1.0
 * @package lwtv-plugin
 */

class LWTV_Cache {

	/**
	 * Collect the URLs we're going to flush for characters
	 * @param  int     $post_id ID of the character
	 * @return array   array of URLs
	 */
	public static function collect_urls_for_characters( $post_id ) {

		// defaults:
		$clean_urls = array();

		// Generate list of shows to purge
		$shows = get_post_meta( $post_id, 'lezchars_show_group', true );
		if ( ! empty( $shows ) ) {
			foreach ( $shows as $show ) {
				if ( isset( $show['show'] ) && 'publish' === get_post_status( $show['show'] ) ) {
					$clean_urls[] = get_permalink( $show['show'] );
				}
			}
		}

		// Generate List of Actors
		$actors = get_post_meta( $post_id, 'lezchars_actor', true );
		if ( ! is_array( $actors ) ) {
			$actors = array( get_post_meta( $post_id, 'lezchars_actor', true ) );
		}
		if ( ! empty( $actors ) ) {
			foreach ( $actors as $actor ) {
				$clean_urls[] = get_permalink( $actor );
			}
		}

		// If character was published within the last 15 minutes, flush home page
		$post_date = get_post_time( 'U', true, $post_id );
		$delta     = time() - $post_date;
		if ( $delta < ( 15 * 60 ) ) {
			$clean_urls[] = home_url();
		}

		return $clean_urls;
	}

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
				rocket_clean_files( $url );
			}
		}
	}
}

new LWTV_Cache();
