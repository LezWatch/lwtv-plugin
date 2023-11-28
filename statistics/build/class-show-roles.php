<?php

class LWTV_Statistics_Show_Roles_Build {

	/**
	 * Statistics Roles on Shows
	 *
	 * @access public
	 * @static
	 * @param string $type (default: 'dead')
	 * @return void
	 */
	public function make( $type = 'dead' ) {

		$transient = 'show_roles_' . $type;
		$array     = LWTV_Features_Transients::get_transient( $transient );

		if ( false === $array ) {
			$array = array();

			// List of shows
			$all_shows_query = ( new LWTV_Queery_Post_Type() )->make( 'post_type_shows' );

			if ( $all_shows_query->have_posts() ) {
				$show_array = wp_list_pluck( $all_shows_query->posts, 'ID' );
				wp_reset_query();
			}

			$guest_alive_array     = array();
			$recurring_alive_array = array();
			$main_alive_array      = array();
			$guest_dead_array      = array();
			$recurring_dead_array  = array();
			$main_dead_array       = array();

			if ( is_array( $show_array ) ) {
				foreach ( $show_array as $show_id ) {

					$show_name = preg_replace( '/\s*/', '', get_the_title( $show_id ) );
					$show_name = strtolower( $show_name );

					$role_loop = ( new LWTV_Queery_Post_Meta() )->make( 'post_type_characters', 'lezchars_show_group', $show_id, 'LIKE' );

					if ( $role_loop->have_posts() ) {

						$guest     = array(
							'alive' => 0,
							'dead'  => 0,
						);
						$regular   = array(
							'alive' => 0,
							'dead'  => 0,
						);
						$recurring = array(
							'alive' => 0,
							'dead'  => 0,
						);

						$char_id     = get_the_id();
						$shows_array = get_post_meta( $char_id, 'lezchars_show_group', true );

						if ( '' !== $shows_array ) {

							foreach ( $shows_array as $char_show ) {
								if ( 'guest' === $char_show['type'] ) {
									++$guest['alive'];
									if ( has_term( 'dead', 'lez_cliches', $char_id ) ) {
										++$guest['dead'];
									}
								}
								if ( 'regular' === $char_show['type'] ) {
									++$regular['alive'];
									if ( has_term( 'dead', 'lez_cliches', $char_id ) ) {
										++$regular['dead'];
									}
								}
								if ( 'recurring' === $char_show['type'] ) {
									++$recurring['alive'];
									if ( has_term( 'dead', 'lez_cliches', $char_id ) ) {
										++$recurring['dead'];
									}
								}
							}
						}

						// Make Alive Query
						if ( 0 === $regular['alive'] && 0 !== $recurring['alive'] && 0 === $guest['alive'] ) {
							$recurring_alive_array[ $show_name ] = array(
								'url'    => get_permalink( $show_id ),
								'name'   => get_the_title( $show_id ),
								'status' => get_post_status( $show_id ),
							);
						}
						if ( 0 === $regular['alive'] && 0 === $recurring['alive'] && 0 !== $guest['alive'] ) {
							$guest_alive_array[ $show_name ] = array(
								'url'    => get_permalink( $show_id ),
								'name'   => get_the_title( $show_id ),
								'status' => get_post_status( $show_id ),
							);
						}
						if ( 0 !== $regular['alive'] && 0 === $guest['alive'] && 0 === $recurring['alive'] ) {
							$main_alive_array[ $show_name ] = array(
								'url'    => get_permalink( $show_id ),
								'name'   => get_the_title( $show_id ),
								'status' => get_post_status( $show_id ),
							);
						}

						// Make Dead Data
						if ( 0 === $regular['dead'] && 0 !== $recurring['dead'] && 0 === $guest['dead'] ) {
							$recurring_dead_array[ $show_name ] = array(
								'url'    => get_permalink( $show_id ),
								'name'   => get_the_title( $show_id ),
								'status' => get_post_status( $show_id ),
							);
						}
						if ( 0 === $regular['dead'] && 0 === $recurring['dead'] && 0 !== $guest['dead'] ) {
							$guest_dead_array[ $show_name ] = array(
								'url'    => get_permalink( $show_id ),
								'name'   => get_the_title( $show_id ),
								'status' => get_post_status( $show_id ),
							);
						}
						if ( 0 !== $regular['dead'] && 0 === $guest['dead'] && 0 === $recurring['dead'] ) {
							$main_dead_array[ $show_name ] = array(
								'url'    => get_permalink( $show_id ),
								'name'   => get_the_title( $show_id ),
								'status' => get_post_status( $show_id ),
							);
						}
					}
				}
			}

			$alive_array = array(
				'guest'     => array(
					'name'  => 'Only Guests',
					'count' => count( $guest_alive_array ),
					'url'   => home_url( '/role/guest/' ),
				),
				'main'      => array(
					'name'  => 'Only Main',
					'count' => count( $main_alive_array ),
					'url'   => home_url( '/role/regular/' ),
				),
				'recurring' => array(
					'name'  => 'Only Recurring',
					'count' => count( $recurring_alive_array ),
					'url'   => home_url( '/role/recurring/' ),
				),
			);

			$dead_array = array(
				'guest'     => array(
					'name'  => 'Only Guests',
					'count' => $guest['dead'],
					'url'   => home_url( '/role/guest/' ),
				),
				'main'      => array(
					'name'  => 'Only Main',
					'count' => $regular['dead'],
					'url'   => home_url( '/role/regular/' ),
				),
				'recurring' => array(
					'name'  => 'Only Recurring',
					'count' => $recurring['dead'],
					'url'   => home_url( '/role/recurring/' ),
				),
			);

			if ( 'dead' === $type ) {
				$array = $dead_array;
			} else {
				$array = $alive_array;
			}

			set_transient( $transient, $array, DAY_IN_SECONDS );
		}

		return $array;
	}
}
