<?php

namespace LWTV\Statistics\Build;

class Dead_Shows {

	/*
	 * Statistics Death on Shows
	 *
	 * Death is insane. This is how to figure out who died on what show.
	 * We can use it to determine how many shows have ALL dead queers, etc.
	 * It's fucked up. I'm sorry.
	 *
	 * @param string $format The format of our output
	 *
	 * @return array
	 */
	public function make( $format ) {

		$transient = 'dead_shows_' . $format;
		$array     = lwtv_plugin()->get_transient( $transient );

		if ( false === $array ) {

			// Shows With Dead Query
			$dead_shows_query = lwtv_plugin()->queery_taxonomy( 'post_type_shows', 'lez_tropes', 'slug', 'dead-queers' );
			if ( is_object( $dead_shows_query ) && $dead_shows_query->have_posts() ) {
				$dead_shows = wp_list_pluck( $dead_shows_query->posts, 'ID' );
			}

			// Shows With NO Dead Query
			$alive_shows_query = lwtv_plugin()->queery_taxonomy( 'post_type_shows', 'lez_tropes', 'slug', 'dead-queers', 'NOT IN' );
			if ( is_object( $alive_shows_query ) && $alive_shows_query->have_posts() ) {
				$alive_shows = wp_list_pluck( $alive_shows_query->posts, 'ID' );
			}

			// Predef Arrays
			$noneshow_death_array = array();
			$fullshow_death_array = array();
			$someshow_death_array = array();

			// Shows with no deaths
			if ( is_array( $alive_shows ) ) {
				foreach ( $alive_shows as $show_id ) {
					$show_name = preg_replace( '/\s*/', '', get_the_title( $show_id ) );
					$show_name = strtolower( $show_name );

					$noneshow_death_array[ $show_name ] = array(
						'url'    => get_permalink( $show_id ),
						'name'   => get_the_title( $show_id ),
						'status' => get_post_status( $show_id ),
					);
				}
			}

			// Shows with deaths
			if ( is_array( $dead_shows ) ) {
				foreach ( $dead_shows as $show_id ) {
					$show_name = preg_replace( '/\s*/', '', get_the_title( $show_id ) );
					$show_name = strtolower( $show_name );

					// Loop of characters who MIGHT be in this show
					$this_show_characters_query = lwtv_plugin()->queery_post_meta( 'post_type_characters', 'lezchars_show_group', $show_id, 'LIKE' );

					$fulldeathcount = get_post_meta( $show_id, 'lezshows_dead_count', true );
					$allcharcount   = get_post_meta( $show_id, 'lezshows_char_count', true );

					if ( $fulldeathcount === $allcharcount ) {
						$fullshow_death_array[ $show_name ] = array(
							'url'    => get_permalink( $show_id ),
							'name'   => get_the_title( $show_id ),
							'status' => get_post_status( $show_id ),
						);
					} else {
						$someshow_death_array[ $show_name ] = array(
							'url'    => get_permalink( $show_id ),
							'name'   => get_the_title( $show_id ),
							'status' => get_post_status( $show_id ),
						);
					}
				}
			}

			if ( 'simple' === $format ) {
				$array = array(
					'all'  => array(
						'name'  => 'All characters are dead',
						'count' => count( $fullshow_death_array ),
						'url'   => '',
					),
					'some' => array(
						'name'  => 'Some characters are dead',
						'count' => count( $someshow_death_array ),
						'url'   => '',
					),
					'none' => array(
						'name'  => 'No characters are dead',
						'count' => count( $noneshow_death_array ),
						'url'   => '',
					),
				);
			}

			// save array as transient for a reason.
			set_transient( $transient, $array, DAY_IN_SECONDS );
		}

		return $array;
	}
}
