<?php
/*
 * Name: Actor Privacy
 * Desc: Set actor Privacy details.
 * Since: 6.1.0
 */

namespace LWTV\CPTs\Actors;

class Privacy {

	/**
	 * Make Private
	 *
	 * @param  int $post_id
	 * @return void
	 */
	public function make( $post_id, $set ) {
		$privacy     = get_post_meta( $post_id, 'lezactors_make_option_private', true );
		$post_object = get_post( $post_id );

		if ( 'check' === $set ) {
			if ( is_array( $privacy ) && in_array( 'hide_all', $privacy, true ) && 'private' !== get_post_status( $post_id ) ) {
				$post_object->post_status = 'private';
			} elseif ( 'private' === get_post_status( $post_id ) ) {
				$post_object->post_status = 'publish';
			}
		} elseif ( true === $set ) {
			$post_object->post_status = 'private';
		} else {
			$post_object->post_status = 'publish';
		}

		wp_update_post( $post_object );
	}

	/**
	 * Hide sections
	 *
	 * @param  int    $post_id
	 * @param  string $type
	 * @return bool
	 */
	public function hide( $post_id, $type ): bool {
		$privacy = get_post_meta( $post_id, 'lezactors_make_option_private', true );

		if ( ! is_array( $privacy ) ) {
			return false;
		}

		switch ( $type ) {
			case 'dob':
				if ( in_array( 'hide_dob', $privacy, true ) ) {
					return true;
				}
				break;
			case 'socials':
				if ( in_array( 'hide_socials', $privacy, true ) ) {
					return true;
				}
				break;
			case 'all':
				if ( in_array( 'hide_all', $privacy, true ) ) {
					return true;
				}
				break;
		}

		return false;
	}

	public function get_warning( $post_id ) {
		// if we're not logged in, return.
		if ( ! is_user_logged_in() ) {
			return;
		}

		$private_note = get_post_meta( $post_id, 'lezactors_make_option_private_notes', true );
		$privacy      = get_post_meta( $post_id, 'lezactors_make_option_private', true );

		// If Privacy is not an array, early return.
		if ( ! is_array( $privacy ) ) {
			return;
		}

		$amount_of_privacy = ( in_array( 'hide_all', $privacy, true ) ) ? 'all' : 'some';

		if ( 'all' === $amount_of_privacy && 'private' !== get_post_status( $post_id ) ) {
			$this->make( $post_id, true );
		} elseif ( 'some' === $amount_of_privacy && 'publish' !== get_post_status( $post_id ) ) {
			$this->make( $post_id, false );
		}

		echo '<div class="wp-block-lez-library-private-note alert alert-warning" role="alert">';
		echo '<p><strong>Privacy Notice:</strong> This actor has requested that <em>' . esc_html( $amount_of_privacy ) . '</em> of their personal information be hidden from public view.';

		if ( 'some' === $amount_of_privacy ) {
			echo '<br/><br/>Hidden: ';

			if ( in_array( 'hide_dob', $privacy, true ) ) {
				echo '<br/>&bull; Birthday';
			}

			if ( in_array( 'hide_socials', $privacy, true ) ) {
				echo '<br/>&bull; Social Media';
			}
		}

		if ( $private_note ) {
			echo '<br/><br/>' . esc_html( $private_note );
		}

		echo '</p>';

		echo '</div>';
	}
}
