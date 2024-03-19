<?php
/**
 * Name: Character Calculations
 * Description: Calculate various data points for characters
 */

namespace LWTV\CPTs\Characters;

use LWTV\CPTs\Actors;
use LWTV\CPTs\Characters;
use LWTV\CPTs\Shows;

class Calculations {

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
	 * Sync Shows
	 *
	 * Sync the shadow taxonomy for shows with the character.
	 *
	 * @param  int  $post_id
	 * @return void
	 */
	public function sync_shows( $post_id, $shadow_character ) {
		$show_group = get_post_meta( $post_id, 'lezchars_show_group', true );

		if ( $show_group ) {
			foreach ( $show_group as $each_show ) {
				// Remove the Array.
				if ( is_array( $each_show['show'] ) ) {
					$each_show['show'] = $each_show['show'][0];
				}

				if ( get_post_status( $each_show['show'] ) !== 'publish' ) {
					continue;
				}

				// Add the tax for the character to the show.
				wp_add_object_terms( (int) $each_show['show'], (int) $shadow_character->term_id, Characters::SHADOW_TAXONOMY );

				Shows::do_the_math( $each_show['show'] );
			}
		}
	}

	/**
	 * Sync Actors
	 *
	 * Sync the shadow taxonomy for actors with the character.
	 *
	 * @param  int  $post_id
	 * @return void
	 */
	public function sync_actors( $post_id, $shadow_character ) {
		$actors = get_post_meta( $post_id, 'lezchars_actor', true );
		if ( $actors ) {
			$actors = ( ! is_array( $actors ) ) ? array( $actors ) : $actors;

			foreach ( $actors as $actor ) {

				if ( get_post_status( $actor ) !== 'publish' ) {
					continue;
				}

				// Add the tax for the character to the actor.
				wp_add_object_terms( (int) $actor, (int) $shadow_character->term_id, Characters::SHADOW_TAXONOMY );

				Actors::do_the_math( $actor );
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

		// Get the shadow tax ID
		$shadow_character = \Shadow_Taxonomy\Core\get_associated_term( $post_id, Characters::SHADOW_TAXONOMY );

		// Update Show data
		self::sync_shows( $post_id, $shadow_character );

		// Update Actor data
		self::sync_actors( $post_id, $shadow_character );
	}
}
