<?php

namespace LWTV\Statistics\Build;

class Dead_Role {

	/*
	 * Statistics Array for DEAD by ROLE
	 *
	 * Generate array to parse content for death by character role
	 *
	 * @return array
	 */
	public function make() {

		$transient = 'dead_role_stats';
		$array     = lwtv_plugin()->get_transient( $transient );

		if ( false === $array ) {
			$array        = array();
			$all_the_dead = lwtv_plugin()->queery_taxonomy( 'post_type_characters', 'lez_cliches', 'slug', 'dead' );
			$by_role      = array(
				'regular'   => 0,
				'guest'     => 0,
				'recurring' => 0,
			);

			if ( is_object( $all_the_dead ) && $all_the_dead->have_posts() ) {
				$dead_chars = wp_list_pluck( $all_the_dead->posts, 'ID' );
			}

			foreach ( $dead_chars as $dead_id ) {
				$all_shows = get_post_meta( $dead_id, 'lezchars_show_group', true );
				if ( is_array( $all_shows ) ) {
					foreach ( $all_shows as $each_show ) {
						if ( 'regular' === $each_show['type'] ) {
							++$by_role['regular'];
						}
						if ( 'guest' === $each_show['type'] ) {
							++$by_role['guest'];
						}
						if ( 'recurring' === $each_show['type'] ) {
							++$by_role['recurring'];
						}
					}
				}
			}

			$array = array(
				'regular'   => array(
					'count' => $by_role['regular'],
					'name'  => 'Regular',
					'url'   => home_url( '/characters/?fwp_char_roles=regular' ),
				),
				'guest'     => array(
					'count' => $by_role['guest'],
					'name'  => 'Guest',
					'url'   => home_url( '/characters/?fwp_char_roles=guest' ),
				),
				'recurring' => array(
					'count' => $by_role['recurring'],
					'name'  => 'Recurring',
					'url'   => home_url( '/characters/?fwp_char_roles=recurring' ),
				),
			);

			// save array as transient for a reason.
			set_transient( $transient, $array, DAY_IN_SECONDS );
		}

		return $array;
	}
}
