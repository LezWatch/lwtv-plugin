<?php
/**
 * Name: Character Calculations
 * Description: Calculate various data points for characters
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * class LWTV_Characters_Calculate
 *
 * @since 3.0
 */

class LWTV_Characters_Calculate {

	/**
	 * Calculate the most recent death
	 * This has to happen because Sara Lance.
	 *
	 * @param  int   $post_id
	 * @return N/A   No return, just update
	 */
	public function death( $post_id ) {
		// get the most recent death and save it as a new meta
		$character_death = get_post_meta( $post_id, 'lezchars_death_year', true );
		$last_char_death = get_post_meta( $post_id, 'lezchars_last_death', true );
		$newest_death    = '0000-00-00';
		if ( '' !== $character_death ) {
			foreach ( $character_death as $death ) {
				if ( $death > $newest_death ) {
					$newest_death = $death;
				}
			}
			// If there's a newest death AND it isn't equal last death, save it
			if ( '0000-00-00' !== $newest_death && $newest_death !== $last_char_death ) {
				update_post_meta( $post_id, 'lezchars_last_death', $newest_death );
			}
		}
	}

	/**
	 * Update the related shows.
	 * In order to reduce load, we only run this on saves.
	 *
	 * @param  int   $post_id
	 * @return N/A   No return, just update show calculation
	 */
	public function shows( $post_id ) {
		// Generate list of shows to purge
		$shows = get_post_meta( $post_id, 'lezchars_show_group', true );
		if ( ! empty( $shows ) ) {
			foreach ( $shows as $show_id ) {
				if ( isset( $show_id['show'] ) ) {

					// Add character to list for show
					$characters = get_post_meta( $show_id['show'], 'lezshows_char_list', true );
					if ( empty( $characters ) ) {
						$characters = array();
					}
					$characters[] = $post_id;
					$characters   = array_unique( $characters );
					update_post_meta( $show_id['show'], 'lezshows_char_list', $characters );

					( new LWTV_Shows_Calculate() )->do_the_math( $show_id['show'] );
				}
			}
		}
	}

	/**
	 * Update the Actors.
	 * In order to reduce load, we only run this on saves.
	 *
	 * @param  int   $post_id, Post ID of character
	 * @return N/A   No return, just update actor calculation
	 */
	public function actors( $post_id ) {
		// Array of Actors saved to post
		$actors = get_post_meta( $post_id, 'lezchars_actor', true );
		if ( ! is_array( $actors ) ) {
			$actors = array( $actors );
		}
/*
		if ( empty( $actors ) ) {
			// Loop for all actors who have this character post ID in their "char list" meta:
			$actor_loop = ( new LWTV_Loops() )->post_meta_query( 'post_type_actors', 'lezactors_char_list', $post_id, 'LIKE' );

			// Extract the IDs
			if ( $actor_loop->have_posts() ) {
				$actor_ids = wp_list_pluck( $actor_loop->posts, 'ID' );
			}

			// Reset to end.
			wp_reset_query();

			// For each actor, double check since numbers and WP and like are weird.
			foreach ( $actor_ids as $one_actor_id ) {
				$char_array = get_post_meta( $one_actor_id, 'lezactors_char_list', true );
				if ( in_array( $post_id, $char_array, TRUE ) ) {
					$actors[] = $one_actor_id;
				}
			}
		}
*/
		if ( ! empty( $actors ) ) {
			foreach ( $actors as $actor_id ) {

				// Add array of characters by ID to actor as post meta
				$characters = get_post_meta( $actor_id, 'lezactors_char_list', true );
				if ( empty( $characters ) ) {
					$characters = array();
				}

				$characters[] = $post_id;
				$characters   = array_unique( $characters );
				update_post_meta( $actor_id, 'lezactors_char_list', $characters );

				( new LWTV_Actors_Calculate() )->do_the_math( $actor_id );
			}
		}
	}

	/**
	 * Does the Math
	 * @param  int $post_id Post ID of character
	 * @return n/a
	 */
	public function do_the_math( $post_id ) {

		// Calculate Death
		self::death( $post_id );

		// Update shows
		self::shows( $post_id );

		// Update Actors
		self::actors( $post_id );
	}

}

new LWTV_Characters_Calculate();
