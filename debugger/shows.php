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
				'stations'   => get_the_terms( $show_id, 'lez_stations' ),
				'nations'    => get_the_terms( $show_id, 'lez_country' ),
				'formats'    => get_the_terms( $show_id, 'lez_formats' ),
				'genres'     => get_the_terms( $show_id, 'lez_genres' ),
				'tropes'     => get_the_terms( $show_id, 'lez_tropes' ),
				'duplicate'  => get_post_field( 'post_name', $show_id ),
				'imdb'       => get_post_meta( $show_id, 'lezshows_imdb', true ),
			);

			// Check if there are characters. -- Trying to auto-fix crashes :(
			$charshowlist = get_post_meta( $show_id, 'lezshows_char_list', true );
			if ( ( false === $charshowlist || ! $check['chars'] || empty( $check['chars'] ) ) && $check['screentime'] > 1 ) {
				$problems[] = 'No characters listed.';
			}

			// Make sure there are airdates.
			if ( ! array( $check['airdates'] ) ) {
				$problems[] = 'No airdates.';
			}

			// If there's no Worthit, set it to TBD.
			if ( empty( $check['thumb'] ) ) {
				update_post_meta( $show_id, 'lezshows_worthit_rating', 'TBD' );
			}

			// Check Worthit details.
			if ( empty( $check['details'] ) ) {
				$problems[] = 'No worthit details.';
			}

			// Check Realness details.
			if ( ! is_numeric( $check['realness'] ) ) {
				$problems[] = 'No realness rating.';
			}

			// Check Quality details.
			if ( ! is_numeric( $check['quality'] ) ) {
				$problems[] = 'No quality rating.';
			}

			// Check Screentime details.
			if ( ! is_numeric( $check['screentime'] ) ) {
				$problems[] = 'No screentime rating.';
			}

			// Make sure at least ONE TV Station exists.
			if ( ! $check['stations'] || is_wp_error( $check['stations'] ) ) {
				$problems[] = 'No stations.';
			}

			// Make sure a nation/country was selected.
			if ( ! $check['nations'] || is_wp_error( $check['nations'] ) ) {
				$problems[] = 'No country.';
			}

			// This should be impossible, but confirm there is a show-format.
			if ( ! $check['formats'] || is_wp_error( $check['formats'] ) ) {
				$problems[] = 'No format.';
			}

			// Make sure there are genres.
			if ( ! $check['genres'] || is_wp_error( $check['genres'] ) ) {
				$problems[] = 'No genres.';
			}

			// If there are no tropes, add NONE.
			if ( ! $check['tropes'] || is_wp_error( $check['tropes'] ) ) {
				$term = get_term_by( 'name', 'none', 'lez_tropes' );
				wp_set_object_terms( $show_id, $term->ID, 'lez_tropes', true );
			}

			// - Duplicate Show check - shouldn't end in -[NUMBER].
			$permalink_array = explode( '-', $check['duplicate'] );
			$ends_with       = end( $permalink_array );
			// If it ends in a number, we have to check.
			if ( is_numeric( $ends_with ) ) {
				// See if an existing page without the -NUMBER exists (someone could rename themselves with numbers...).
				$possible = get_page_by_path( str_replace( '-' . $ends_with, '', $check['duplicate'] ), OBJECT, 'post_type_shows' );
				if ( is_object( $possible ) && false !== $possible ) {
					// The 90210 Loop
					// Make sure we didn't find ourselves (because some shows are number-named...)
					if ( $possible->ID !== $show_id ) {
						$pos_imdb = get_post_meta( $possible->ID, 'lezshows_imdb', true );
						if ( isset( $pos_imdb ) && $pos_imdb === $check['imdb'] ) {
							$problems[] = 'Likely Dupe - Another Show has this name AND the same IMDb data.';
						}
					}
				}
			}

			// If we have problems, list them:
			if ( ! empty( $problems ) ) {
				$items[] = array(
					'url'     => get_permalink( $show_id ),
					'id'      => $show_id,
					'problem' => implode( '</br>', $problems ),
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
			$problems = ( new LWTV_Debug_Characters() )->check_disabled_characters( $show_id );

			// if there are problems, we put them in items.
			if ( ! empty( $problems ) ) {
				$items[] = array(
					'url'     => get_permalink( $show_id ),
					'id'      => $show_id,
					'problem' => implode( '</br>', $problems ),
				);
			}
		}

		return $items;
	}


	/**
	 * Find all shows without IMDb Settings.
	 *
	 * @return array $problems - array of problems. Can be empty.
	 */
	public function find_shows_no_imdb() {
		// Default
		$items = array();

		// Get all the Shows
		$the_loop = ( new LWTV_Loops() )->post_type_query( 'post_type_shows' );

		if ( $the_loop->have_posts() ) {
			$shows = wp_list_pluck( $the_loop->posts, 'ID' );
			wp_reset_query();
		}

		foreach ( $shows as $show_id ) {

			$problems = array();

			$imdb = get_post_meta( $show_id, 'lezshows_imdb', true );

			if ( empty( $imdb ) ) {
				// Check for IMDb existing at all, unless it's a webseries
				if ( ! has_term( 'web-series', 'lez_formats', $show_id ) ) {
					$problems[] = 'IMDb ID is not set.';
				}
			} elseif ( ( new LWTV_Debug() )->validate_imdb( $imdb, 'show' ) === false ) {
				// - IMDb IDs should be valid for the space they're in, e.g. "nm"
				// and digits for people (props Jamie).
				$problems[] = 'IMDb ID is invalid (ex: tt12345) -- ' . $imdb;
			}

			// If we added any problems, loop and add.
			if ( ! empty( $problems ) ) {
				$items[] = array(
					'url'     => get_permalink( $show_id ),
					'id'      => $show_id,
					'problem' => implode( '</br>', $problems ),
				);
			}
		}

		// Save Transient
		set_transient( 'lwtv_debug_show_imdb', $items, WEEK_IN_SECONDS );

		// Update Options
		$option              = get_option( 'lwtv_debugger_status' );
		$option['show_imdb'] = array(
			'name'  => 'Shows without IMDb',
			'count' => ( ! empty( $items ) ) ? count( $items ) : 0,
			'last'  => time(),
		);
		$option['timestamp'] = time();
		update_option( 'lwtv_debugger_status', $option );

		return $items;
	}
}

new LWTV_Debug_Shows();