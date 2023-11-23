<?php
/*
 * Weird Cache commands.
 * @version 1.0
 * @package lwtv-plugin
 */

class LWTV_Plugins_Cache {

	/**
	 * Collect the URLs we're going to flush for characters
	 * @param  int     $post_id ID of the character
	 * @return array   array of URLs
	 */
	public function collect_urls_for_characters( $post_id ) {

		// defaults:
		$clean_urls = array();

		// Generate list of shows to purge
		$shows = get_post_meta( $post_id, 'lezchars_show_group', true );
		if ( ! empty( $shows ) ) {
			foreach ( $shows as $show ) {

				// Remove the Array.
				if ( is_array( $show['show'] ) ) {
					$show['show'] = $show['show'][0];
				}

				// If the show is live, we'll flush it.
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

		$clean_urls = array_unique( $clean_urls );

		return $clean_urls;
	}

	/**
	 * Clean URLs
	 *
	 * It would be preferable to use the rp_nginx filter, however that runs every time
	 * any page is updated. This method is slower and uglier, but more precise.
	 *
	 * $purge_urls = apply_filters('rt_nginx_helper_purge_urls', $purge_urls, false);
	 *
	 * @return void
	 */
	public function clean_urls( $clear_urls ) {
		foreach ( $clear_urls as $url ) {
			// Change domain.com/path/to/url to domain.com/PURGE/path/to/url
			$url_parse    = wp_parse_url( $url );
			$url_to_purge = $url_parse['scheme'] . '//' . $url_parse['host'] . '/purge' . $url_parse['path'];

			// Reload the data by calling the page
			wp_remote_get( $url_to_purge );
		}
	}
}
