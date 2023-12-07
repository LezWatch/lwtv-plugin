<?php
/**
 * namespace LWTV\Queeries;
 *
 * @since 5.0
 */

namespace LWTV\Queeries;

class Is_Actor_Queer {

	/**
	 * Determine if an actor is queer
	 *
	 * There are multiple ways someone can be queer:
	 * sexuality, gender, pronouns, romantic orientation
	 *
	 * There's also an override.
	 *
	 * @access public
	 * @param  mixed $the_id
	 * @return bool
	 */
	public function make( $the_id ) {

		// If we're not an actor, return null.
		if ( ! isset( $the_id ) || 'post_type_actors' !== get_post_type( $the_id ) ) {
			return;
		}

		// Check the override.
		$override = get_post_meta( $the_id, 'lezactors_queer_override', true );
		if ( isset( $override ) && ! empty( $override ) && 'undefined' !== $override ) {
			$is_queer = ( 'is_queer' === $override ) ? 'yes' : 'no';
			return $is_queer;
		}

		// If we're private, we aren't queer no matter what to protect identities.
		if ( 'private' === get_post_status( $the_id ) ) {
			$is_queer = 'no';
			return $is_queer;
		}

		/**
		 * If we got here, we have some checking to do!
		 *
		 * This part gets weird, but we start out with the concept that all people are
		 * queer. Then we check their genders/pronouns etc to see if they're NOT.
		 *
		 * Queerness is conditional, though. If someone is a trans man who is heterosexual,
		 * then they're queer. This is why, at the end, if ANY of the possible flags are
		 * set YES, then they are queer.
		 *
		 * This is why there's an override.
		 */

		// Build our defaults. We want to believe everyone is queer.
		$check    = array(
			'gender'    => 'yes',
			'sexuality' => 'yes',
			'pronouns'  => 'yes',
			'romantic'  => 'yes',
		);
		$is_queer = 'no';

		// Gender: Are they NOT queer because of their gender?
		$straight_genders = array( 'cis-man', 'cis-woman', 'cisgender', 'undefined', 'unknown' );
		$gender_terms     = get_the_terms( $the_id, 'lez_actor_gender', true );
		if ( ! $gender_terms || is_wp_error( $gender_terms ) || has_term( $straight_genders, 'lez_actor_gender', $the_id ) ) {
			$check['gender'] = 'no';
		}

		// Sexuality: Are they NOT queer because of their sexuality?
		$straight_sexuality = array( 'heterosexual', 'unknown' );
		$sexuality_terms    = get_the_terms( $the_id, 'lez_actor_sexuality', true );
		if ( ! $sexuality_terms || is_wp_error( $sexuality_terms ) || has_term( $straight_sexuality, 'lez_actor_sexuality', $the_id ) ) {
			$check['sexuality'] = 'no';
		}

		// Pronouns: Are they NOT queer because of pronouns?
		$straight_pronouns = array( 'he-him', 'she-her' );
		$pronoun_terms     = get_the_terms( $the_id, 'lez_actor_pronouns', true );
		if ( ! $pronoun_terms || is_wp_error( $pronoun_terms ) ) {
			$check['pronouns'] = 'no';
		} elseif ( has_term( $straight_pronouns, 'lez_actor_pronouns', $the_id ) ) {
			// At this point, we're probably NOT queer per pronouns, but we have a check:
			$pronoun_terms_count = 0;
			foreach ( $pronoun_terms as $a_pronoun ) {
				++$pronoun_terms_count;
			}
			// If they have more than one pronoun choice, they're queer (i.e. he/him AND she/her is queer)
			if ( 1 === $pronoun_terms_count ) {
				$check['pronouns'] = 'no';
			}
		}

		// Romantic Orientation: Are they NOT queer because of romantic orientation?
		$straight_romantics = array( 'heteroromantic' );
		$romantic_terms     = get_the_terms( $the_id, 'lez_actor_romantic', true );
		if ( ! $romantic_terms || is_wp_error( $romantic_terms ) ) {
			$check['romantic'] = 'no';
		} elseif ( has_term( $straight_romantics, 'lez_actor_romantic', $the_id ) ) {
			$check['pronouns'] = 'no';
		}

		// If ANY of the options are a yes, we have a queerio!
		foreach ( $check as $to_check ) {
			if ( 'yes' === $to_check ) {
				$is_queer = 'yes';
				break 1;
			}
		}

		return $is_queer;
	}
}
