<?php

namespace LWTV\Theme;

class Actor_Birthday {
	/**
	 * Is today a birthday?
	 *
	 * @access public
	 *
	 * @param  string $the_id
	 * @return bool
	 */
	public function make( $the_id ) {
		$today_is = gmdate( 'm-d' );
		$birthday = substr( get_post_meta( $the_id, 'lezactors_birth', true ), 5 );
		if ( $birthday === $today_is ) {
			return true;
		}

		return false;
	}

	/**
	 * Output birthday
	 *
	 * @param  [type] $the_id
	 * @return void
	 */
	public function get( $the_id ) {
		if ( $this->make( $the_id ) && ! get_post_meta( $the_id, 'lezactors_death', true ) ) {
			$old = ' ';
			$end = array( 'th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th' );
			$age = lwtv_plugin()->get_actor_age( $the_id );
			$num = ( is_object( $age ) ) ? $age->format( '%y' ) : 0;

			// If their age is 0, something's wrong.
			if ( 0 === $num ) {
				return;
			}
			if ( ( $num % 100 ) >= 11 && ( $num % 100 ) <= 13 ) {
				$years_old = $num . 'th';
			} else {
				$years_old = $num . $end[ $num % 10 ];
			}
			$old = ' ' . $years_old . ' ';
			echo '<div class="alert alert-info" role="alert">Happy' . esc_html( $old ) . 'Birthday, ' . esc_html( get_the_title() ) . '!</div>';
		}
	}
}
