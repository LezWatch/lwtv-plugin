<?php
/**
 * Name: Actor Calculations
 * Description: Calculate various data points for actors
 */

if ( ! defined('WPINC' ) ) die;

/**
 * class LWTV_Actors_Calculate
 *
 * @since 2.1.0
 */

class LWTV_Actors_Calculate {

	/*
	 * Count Queers
	 *
	 * @param int $post_id The post ID.
	 */
	public static function count_queers( $post_id , $type = 'count' ) {

		// If this isn't a show post, return nothing
		if ( get_post_type( $post_id ) !== 'post_type_actors' ) return;

		// If there's no valid type, return nothing
		$type_array = array ( 'count', 'none', 'dead' );
		if ( !in_array( esc_attr( $type ), $type_array ) ) return;

		// Loop to get the list of characters
		$charactersloop = LWTV_Loops::post_meta_query( 'post_type_characters', 'lezchars_actor', $post_id, 'LIKE' );
		$queercount = $deadcount = 0;

		// Store as array to defeat some stupid with counting and prevent querying the database too many times
		if ($charactersloop->have_posts() ) {
			while ( $charactersloop->have_posts() ) {

				$charactersloop->the_post();
				$char_id      = get_the_ID();
				$actors_array = get_post_meta( $char_id, 'lezchars_actor', true);
				$is_dead      = has_term( 'dead', 'lez_cliches', $char_id);

				if ( $actors_array !== '' && get_post_status ( $char_id ) == 'publish' ) {
					foreach( $actors_array as $char_actor ) {
						if ( $char_actor == $post_id ) {
							$queercount++;
							if ( $is_dead == true ) $deadcount++;
						}
					}
				}
			}
			wp_reset_query();
		}

		// Return Queers!
		if ( $type == 'count' )     return $queercount;
		if ( $type == 'dead' )      return $deadcount;
	}

	/**
	 * do_the_math function.
	 *
	 * This will update the following metakeys on save:
	 *  - lezactors_char_count      Number of characters
	 *  - lezactors_dead_count      Number of dead characters
	 *
	 * @access public
	 * @param mixed $post_id
	 * @return void
	 */
	public static function do_the_math( $post_id ) {

		// Calculate Actor Data:
		update_post_meta( $post_id, 'lezactors_char_count', self::count_queers( $post_id, 'count' ) );
		update_post_meta( $post_id, 'lezactors_dead_count', self::count_queers( $post_id, 'dead' ) );

		// Is Queer?
		$is_queer     = ( LWTV_Loops::is_actor_queer( $post_id ) == 'yes' )? true : false;
		update_post_meta( $post_id, 'lezactors_queer', $is_queer );
	}
	
}
new LWTV_Actors_Calculate();