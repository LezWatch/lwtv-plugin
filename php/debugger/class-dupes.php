<?php
/*
 * Find all Duplicates.
 */

namespace LWTV\Debugger;

class Dupes {
	/**
	 * Find Duplicates
	 *
	 * Find all posts that end in -2
	 *
	 * @param array $items - array of Posts
	 */
	public function find_duplicates( $items = array() ): array {
		$duplicates     = array();
		$items_to_check = array();

		// Are we a full scan or a recheck?
		if ( ! empty( $items ) && is_array( $items ) ) {
			foreach ( $items as $item ) {
				$items_to_check[] = $item['id'];
			}
		} else {
			$items_to_check = $this->get_dupes();
		}

		foreach ( $items_to_check as $maybe_dupe ) {
			$check_dupe = $this->compare_duplicates( $maybe_dupe );
			if ( false !== $check_dupe ) {
				$duplicates[] = array(
					'url'     => get_permalink( $maybe_dupe ),
					'id'      => $maybe_dupe,
					'name'    => get_the_title( $maybe_dupe ),
					'problem' => $check_dupe,
				);
			}
		}

		// Save Transient
		set_transient( 'lwtv_debug_duplicates', $duplicates, WEEK_IN_SECONDS );

		// Update Options
		$option               = get_option( 'lwtv_debugger_status' );
		$option['duplicates'] = array(
			'name'  => 'Duplicate Actors/Shows',
			'count' => ( ! empty( $duplicates ) ) ? count( $duplicates ) : 0,
			'last'  => time(),
		);
		$option['timestamp']  = time();
		update_option( 'lwtv_debugger_status', $option );

		return $duplicates;
	}

	/**
	 * Get Duplicates
	 *
	 * Get all posts that end in -2
	 *
	 * @return array
	 */
	public function get_dupes() {
		global $wpdb;

		$queery = "SELECT ID FROM {$wpdb->prefix}posts WHERE post_status='publish' AND post_type IN ('post_type_shows', 'post_type_actors') AND post_name LIKE '%-2'";
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$final_queery = $wpdb->prepare( $queery );

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$all_posts      = $wpdb->get_results( $final_queery );
		$all_post_array = array_unique( wp_list_pluck( $all_posts, 'ID' ) );

		return $all_post_array;
	}

	/**
	 * Compare Duplicates
	 *
	 * If the duplicate has the same IMDb as the original, and isn't
	 * using an override, return true.
	 *
	 * @param int   $post_id - Post ID to check
	 * @return bool|string
	 */
	public function compare_duplicates( $post_id ) {
		$slugs     = array(
			'duplicate' => get_post_field( 'post_name', $post_id ),
			'original'  => substr( get_post_field( 'post_name', $post_id ), 0, -2 ),
		);
		$duplicate = array(
			'id'        => $post_id,
			'slug'      => $slugs['duplicate'],
			'post_type' => get_post_type( $post_id ),
		);
		$original  = array(
			'id'   => lwtv_plugin()->get_id_from_slug( $slugs['original'] ),
			'slug' => $slugs['original'],
		);

		if ( empty( $original['id'] ) ) {
			return false;
		}

		switch ( $duplicate['post_type'] ) {
			case 'post_type_shows':
				$duplicate['imdb']     = get_post_meta( $duplicate['id'], 'lezshows_imdb', true );
				$duplicate['override'] = get_post_meta( $duplicate['id'], 'lezshows_dupe_override', true );
				$original['imdb']      = get_post_meta( $original['id'], 'lezshows_imdb', true );
				break;
			case 'post_type_actors':
				$duplicate['imdb']     = get_post_meta( $duplicate['id'], 'lezactors_imdb', true );
				$duplicate['override'] = get_post_meta( $duplicate['id'], 'lezactors_dupe_override', true );
				$original['imdb']      = get_post_meta( $original['id'], 'lezactors_imdb', true );
				break;
		}

		// If the IMDb IDs are the same, and the override is false, return true.
		if ( $duplicate['imdb'] === $original['imdb'] && true !== $duplicate['override'] ) {
			$is_dupe = '' . get_the_title( $post_id ) . ' is a duplicate of <a href="' . get_permalink( $original['id'] ) . '">' . get_the_title( $original['id'] ) . '</a>';
			return $is_dupe;
		}

		return false;
	}
}
