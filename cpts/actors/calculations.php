<?php
/**
 * Name: Actor Calculations
 * Description: Calculate various data points for actors
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * class LWTV_Actors_Calculate
 *
 * @since 2.1.0
 */

class LWTV_Actors_Calculate {

	/*
	 * Count
	 *
	 * @param int $post_id The post ID.
	 */
	public function count( $post_id, $type = 'count' ) {

		$type_array = array( 'count', 'none', 'dead' );

		// If this isn't an actor post or a valid request, return nothing
		if ( 'post_type_actors' !== get_post_type( $post_id ) || ! in_array( esc_attr( $type ), $type_array, true ) ) {
			return;
		}

		// Get array of characters (by ID)
		$characters = get_post_meta( $post_id, 'lezactors_char_list', true );

		// If the character list is empty, we must build it
		if ( empty( $characters ) ) {
			// Loop to get the list of characters
			$charactersloop = ( new LWTV_Loops() )->post_meta_query( 'post_type_characters', 'lezchars_actor', $post_id, 'LIKE' );

			if ( $charactersloop->have_posts() ) {
				$characters = wp_list_pluck( $charactersloop->posts, 'ID' );
			}

			update_post_meta( $post_id, 'lezactors_char_list', $characters );

			// Reset to end
			wp_reset_query();
		}

		// Process character counts:
		$queercount = 0;
		$deadcount  = 0;

		foreach ( $characters as $char_id ) {
			$actors_array = get_post_meta( $char_id, 'lezchars_actor', true );
			$is_dead      = has_term( 'dead', 'lez_cliches', $char_id );

			if ( '' !== $actors_array && 'publish' === get_post_status( $char_id ) ) {
				foreach ( $actors_array as $char_actor ) {
					// To compensate for maybe character situations, we need this loose
					// phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
					if ( $char_actor == $post_id ) {
						$queercount++;
						if ( $is_dead ) {
							$deadcount++;
						}
					}
				}
			}
		}

		// Let us return Queers!
		switch ( $type ) {
			case 'count':
				$output = $queercount;
				break;
			case 'dead':
				$output = $deadcount;
				break;
		}

		return $output;
	}

	/**
	 * do_the_math function.
	 *
	 * This will update the following metakeys on save:
	 *  - lezactors_char_count      Number of characters
	 *  - lezactors_dead_count      Number of dead characters
	 *  - lezactors_queer           Are they queer? True or false
	 *
	 * @access public
	 * @param mixed $post_id
	 * @return void
	 */
	public function do_the_math( $post_id ) {

		// Update counts
		update_post_meta( $post_id, 'lezactors_char_count', self::count( $post_id, 'count' ) );
		update_post_meta( $post_id, 'lezactors_dead_count', self::count( $post_id, 'dead' ) );

		// Is Queer?
		$is_queer = ( 'yes' === ( new LWTV_Loops() )->is_actor_queer( $post_id ) ) ? true : false;
		update_post_meta( $post_id, 'lezactors_queer', $is_queer );
	}

}
new LWTV_Actors_Calculate();
