<?php

namespace LWTV\Theme;

use LWTV\CPTs\Actors\Calculations as Actors;
use LWTV\CPTs\Shows\Calculations as Shows;
use LWTV\CPTs\Characters\Calculations as Characters;

class Do_Math {
	/**
	 * Do the Math for a specific show/char/actor
	 *
	 * @param string  $post_id  Post ID
	 *
	 * @return void
	 */
	public function make( $post_id ): void {
		$post_type = get_post_type( $post_id );

		switch ( $post_type ) {
			case 'post_type_shows':
				( new Shows() )->do_the_math( $post_id );
				break;
			case 'post_type_characters':
				( new Characters() )->do_the_math( $post_id );
				break;
			case 'post_type_actors':
				( new Actors() )->do_the_math( $post_id );
				break;
			default:
				break;
		}
	}
}
