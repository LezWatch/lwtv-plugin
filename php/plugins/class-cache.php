<?php
/*
 * Weird Cache commands.
 *
 * @package lwtv-plugin
 */

namespace LWTV\Plugins;

use LWTV\CPTs\Characters;

class Cache {

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

		$clean_urls = array_unique( $clean_urls );

		return $clean_urls;
	}

	/**
	 * Collect the URLs we're going to flush for shows or actors
	 * @param  int     $post_id ID of the show or actor
	 * @return array   array of URLs
	 */
	public function collect_cache_urls_for_actors_or_shows( $post_id ) {
		$clean_urls = array();

		$shadow_chars = \Shadow_Taxonomy\Core\get_the_posts( $post_id, Characters::SHADOW_TAXONOMY, Characters::SLUG );

		foreach ( $shadow_chars as $shadow => $item ) {
			if ( isset( $item->ID ) && ! empty( $item->ID ) ) {
				$characters[] = $item->ID;
			}
		}

		foreach ( $characters as $character ) {
			$clean_urls[] = get_permalink( $character );
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
	 * @param  int    $post_id    - ID of the post
	 * @param  array  $clear_urls - Arrays of URLs to clean
	 * @return void
	 */
	public function clean_urls( $post_id, $clear_urls ) {
		// If published within the last 15 minutes, flush home page
		$post_date = get_post_time( 'U', true, $post_id );
		$delta     = time() - $post_date;
		if ( $delta < ( 15 * 60 ) ) {
			$clear_urls[] = home_url();
		}

		foreach ( $clear_urls as $url ) {
			// Nginx Helper.
			if ( is_plugin_active( 'nginx-helper/nginx-helper.php' ) ) {
				wp_remote_get( $url );
			}

			// WP Rocket.
			if ( function_exists( 'rocket_clean_post' ) ) {
				rocket_clean_post( $url );
			}
		}
	}
}
