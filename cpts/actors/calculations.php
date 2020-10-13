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

		// Loop to get the list of characters
		$charactersloop = ( new LWTV_Loops() )->post_meta_query( 'post_type_characters', 'lezchars_actor', $post_id, 'LIKE' );
		$queercount     = 0;
		$deadcount      = 0;

		// Store as array to defeat some stupid with counting and prevent querying the database too many times
		if ( $charactersloop->have_posts() ) {
			while ( $charactersloop->have_posts() ) {

				$charactersloop->the_post();
				$char_id      = get_the_ID();
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
			wp_reset_query();
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
	 * lists
	 *
	 * Can generate lists of character or show IDs associated with this actor.
	 * We save them as IDs so we can live render the display names (just in case
	 * anything changed and we don't want to re-save)
	 *
	 * @param  int    $post_id post ID
	 * @param  string $type    what type of data we want
	 * @return array          array of generated IDs
	 */
	public function list( $post_id, $type ) {
		$type_array = array( 'characters', 'shows' );

		// If this isn't an actor post or a valid request, return nothing
		if ( 'post_type_actors' !== get_post_type( $post_id ) || ! in_array( esc_attr( $type ), $type_array, true ) ) {
			return;
		}

		// Empty return for now
		$output     = '';
		$char_array = array();
		$show_array = array();

		// Get a loop of all the characters that MIGHT belong to this actor, based on actor postID
		// This will have some falsies, so hang on...
		$loop = ( new LWTV_Loops() )->post_meta_query( 'post_type_characters', 'lezchars_actor', $post_id, 'LIKE' );
		if ( $loop->have_posts() ) {
			while ( $loop->have_posts() ) {
				$loop->the_post();
				$char_id      = get_the_ID();

				// Get the array of actors per character
				$actors_array = get_post_meta( $char_id, 'lezchars_actor', true );
				if ( '' !== $actors_array && 'publish' === get_post_status( $char_id ) ) {
					foreach ( $actors_array as $char_actor ) {
						// To compensate for MAYBE character situations, we need this loose
						// phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
						if ( $char_actor == $post_id ) {
							// There is a hit for the actor ID based on the character's list
							// This is a POSITIVE! Save the ID
							$char_array[] = $char_id;

							// Loop through all shows for the character and save that as $show_array
							// Save the ID
							$shows_group = get_post_meta( $char_id, 'lezchars_show_group', true );
							if ( '' !== $shows_group && is_array( $shows_group ) ) {
								foreach ( $shows_group as $each_show ) {
									$show_array[] = $each_show['show'];
								}
							}
						}
					}
				}
			}
			wp_reset_query();
		}

		// Let us return data!
		switch ( $type ) {
			case 'characters':
				$output = $char_array;
				break;
			case 'shows':
				$output = $show_array;
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

		// Update lists
		update_post_meta( $post_id, 'lezactors_char_list', self::list( $post_id, 'characters' ) );
		update_post_meta( $post_id, 'lezactors_show_list', self::list( $post_id, 'shows' ) );

		// Is Queer?
		$is_queer = ( 'yes' === ( new LWTV_Loops() )->is_actor_queer( $post_id ) ) ? true : false;
		update_post_meta( $post_id, 'lezactors_queer', $is_queer );
	}

}
new LWTV_Actors_Calculate();
