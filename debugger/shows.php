<?php
/*
 * Find all problems with Show pages.
 */

// if this file is called directly abort
if ( ! defined( 'WPINC' ) ) {
	die;
}

class LWTV_Debug_Shows {

	/**
	 * Find Shows with Problems
	 */
	public function find_shows_problems() {
		// Default
		$items = array();

		// Get all the shows
		$the_loop = ( new LWTV_Loops() )->post_type_query( 'post_type_shows' );

		if ( $the_loop->have_posts() ) {
			$shows = wp_list_pluck( $the_loop->posts, 'ID' );
			wp_reset_query();
		}

		foreach ( $shows as $show_id ) {
			$problems = array();

			// What we can check for
			$check = array(
				'chars'      => get_post_meta( $show_id, 'lezshows_char_count', true ),
				'thumb'      => get_post_meta( $show_id, 'lezshows_worthit_rating', true ),
				'screentime' => get_post_meta( $show_id, 'lezshows_screentime_rating', true ),
				'details'    => get_post_meta( $show_id, 'lezshows_worthit_details', true ),
				'realness'   => get_post_meta( $show_id, 'lezshows_realness_rating', true ),
				'quality'    => get_post_meta( $show_id, 'lezshows_quality_rating', true ),
				'rating'     => get_post_meta( $show_id, 'lezshows_screentime_rating', true ),
				'airdates'   => get_post_meta( $show_id, 'lezshows_airdates', true ),
				'imdb'       => get_post_meta( $show_id, 'lezshows_imdb', true ),
				'stations'   => get_the_terms( $show_id, 'lez_stations' ),
				'nations'    => get_the_terms( $show_id, 'lez_country' ),
				'formats'    => get_the_terms( $show_id, 'lez_formats' ),
				'genres'     => get_the_terms( $show_id, 'lez_genres' ),
				'tropes'     => get_the_terms( $show_id, 'lez_tropes' ),
			);

			// Check if there are characters.
			$charshowlist = get_post_meta( $show_id, 'lezshows_char_list', true );

			if ( false === $charshowlist ) {
				$list_count = max( 0, LWTV_Shows_Calculate::count_queers( $show_id, 'count' ) );
			} else {
				$list_count = count( $charshowlist );
			}

			if ( $check['chars'] !== $list_count ) {
				$check['chars'] = $list_count;
				update_post_meta( $show_id, 'lezshows_char_count', $list_count );
			}

			// If there's 0 screentime, it's okay there are no Characters
			if ( ( ! $check['chars'] || empty( $check['chars'] ) ) && $check['screentime'] > 1 ) {
				$problems[] = 'No characters listed.';
			}

			if ( ! array( $check['airdates'] ) ) {
				$problems[] = 'No airdates.';
			}

			if ( empty( $check['thumb'] ) ) {
				$problems[] = 'No worthit thumb.';
			}

			if ( empty( $check['details'] ) ) {
				$problems[] = 'No worthit details.';
			}

			if ( ! is_numeric( $check['realness'] ) ) {
				$problems[] = 'No realness rating.';
			}

			if ( ! is_numeric( $check['quality'] ) ) {
				$problems[] = 'No quality rating.';
			}

			if ( ! is_numeric( $check['screentime'] ) ) {
				$problems[] = 'No screentime rating.';
			}

			if ( ! empty( $check['imdb'] ) && ( new LWTV_Debug() )->validate_imdb( $check['imdb'] ) === false ) {
				$problems[] = 'IMDb ID is invalid (ex: tt12345).';
			}

			if ( ! has_term( 'web-series', 'lez_formats', $show_id ) ) {
				if ( empty( $check['imdb'] ) ) {
					$problems[] = 'IMDb ID is not set.';
				}
			}

			if ( ! $check['stations'] || is_wp_error( $check['stations'] ) ) {
				$problems[] = 'No stations.';
			}

			if ( ! $check['nations'] || is_wp_error( $check['nations'] ) ) {
				$problems[] = 'No country.';
			}

			if ( ! $check['formats'] || is_wp_error( $check['formats'] ) ) {
				$problems[] = 'No format.';
			}

			if ( ! $check['genres'] || is_wp_error( $check['genres'] ) ) {
				$problems[] = 'No genres.';
			}

			if ( ! $check['tropes'] || is_wp_error( $check['tropes'] ) ) {
				$problems[] = 'No tropes.';
			}

			// If we have problems, list them:
			if ( ! empty( $problems ) ) {
				$items[] = array(
					'url'     => get_permalink( $show_id ),
					'id'      => $show_id,
					'problem' => implode( ' ', $problems ),
				);
			}
		}

		$items_intersection = self::find_intersection_problems();

		$items = array_merge( $items, $items_intersection );

		// Save Transient
		set_transient( 'lwtv_debug_show_problems', $items, WEEK_IN_SECONDS );

		// Update Options
		$option                  = get_option( 'lwtv_debugger_status' );
		$option['show_problems'] = array(
			'name'  => 'Shows with Issues',
			'count' => ( ! empty( $items ) ) ? count( $items ) : 0,
			'last'  => time(),
		);
		$option['timestamp']     = time();
		update_option( 'lwtv_debugger_status', $option );

		return $items;
	}

	/**
	 * Check shows with intersectionality
	 * Ensure they have matching characters.
	 * @return [type] [description]
	 */
	public function find_intersection_problems() {

		$items = array();

		// list everything that has a DISABLED intersection tag
		$disabled_shows_loop = ( new LWTV_Loops() )->tax_query( 'post_type_shows', 'lez_intersections', 'slug', 'disabilities' );

		if ( $disabled_shows_loop->have_posts() ) {
			$shows = wp_list_pluck( $disabled_shows_loop->posts, 'ID' );
			wp_reset_query();
		}

		foreach ( $shows as $show_id ) {
			// Try to get the problems.
			$problems = self::check_disabled_characters( $show_id );

			// if there are problems, we put them in items.
			if ( ! empty( $problems ) ) {
				$items[] = array(
					'url'     => get_permalink( $show_id ),
					'id'      => $show_id,
					'problem' => implode( ' ', $problems ),
				);
			}
		}

		return $items;
	}

}

new LWTV_Debug_Shows();
