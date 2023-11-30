<?php
/**
 * class LWTV_Queery_Is_Actor_Trans
 *
 * @since 5.0
 */

class LWTV_Queery_Is_Actor_Trans {

	/**
	 * Determine if an actor is transgender IRL
	 *
	 * @access public
	 * @param  int $the_id - Post ID
	 * @return bool
	 */
	public function make( $the_id ) {

		if ( ! isset( $the_id ) || 'post_type_actors' !== get_post_type( $the_id ) ) {
			return;
		}

		// Defaults
		$is_trans  = 'no';
		$the_terms = '';

		// The gender terms this actor uses:
		$gender_terms = get_the_terms( $the_id, 'lez_actor_gender', true );

		// If there are terms, let's add the slugs to a list.
		if ( ! empty( $gender_terms ) && ! is_wp_error( $gender_terms ) ) {
			$the_terms = implode( ' ', wp_list_pluck( $gender_terms, 'slug' ) );
		}

		// If the string has 'trans' anywhere in it, we're trans!
		if ( false !== strpos( $the_terms, 'trans' ) ) {
			$is_trans = 'yes';
		}

		if ( 'private' === get_post_status( $the_id ) ) {
			$is_trans = 'no';
		}

		return $is_trans;
	}
}
