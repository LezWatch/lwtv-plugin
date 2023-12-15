<?php

namespace LWTV\This_Year\Build;

class Characters_Dead {
	/**
	 * Get a list of the dead
	 * @param  int     $this_year The year
	 * @param  boolean $count     Just a count?
	 *
	 * @return array              All the data you need.
	 */
	public function make( $this_year, $count = false ) {

		$transient    = 'characters_dead_' . $this_year;
		$transient   .= ( $count ) ? '_count' : '';
		$return_array = lwtv_plugin()->get_transient( $transient );

		// If we have an array saved, use it.
		if ( false !== $return_array && ! empty( $return_array ) ) {
			return $return_array;
		}

		// Otherwise we have no array and must build.
		$dead_loop = lwtv_plugin()->queery_post_meta( 'post_type_characters', 'lezchars_death_year', $this_year, 'REGEXP' );

		if ( ! is_object( $dead_loop ) || ! $dead_loop->have_posts() ) {
			return;
		}

		$queery     = wp_list_pluck( $dead_loop->posts, 'ID' );
		$dead_count = count( $queery );
		$show_array = array();
		$list_array = array();

		// List all queers and the year they died
		if ( isset( $queery ) && is_array( $queery ) ) {
			foreach ( $queery as $char ) {
				$show_ids_raw = get_post_meta( $char, 'lezchars_show_group', true );
				$show_title   = array();
				$show_ids     = array();

				// If the character is in a show this year, we'll add it.
				foreach ( $show_ids_raw as $each_show ) {

					if ( array_key_exists( 'appears', $each_show ) && in_array( $this_year, $each_show['appears'], true ) ) {
						$show_ids[] = $each_show;
					}
				}

				// If we didn't add anything, use everything.
				if ( empty( $show_ids ) ) {
					$show_ids = $show_ids_raw;
				}

				foreach ( $show_ids as $each_show ) {
					// If this is somehow an array, grab the first item.
					if ( is_array( $each_show['show'] ) ) {
						$each_show['show'] = $each_show['show'][0];
					}

					// Get some defaults
					$char_slug = get_post_field( 'post_name', get_post( $char ) );
					$show_slug = get_post_field( 'post_name', get_post( $each_show['show'] ) );

					// if the show isn't already in the array, we create it
					if ( ! empty( $show_slug ) && ! array_key_exists( $show_slug, $show_array ) ) {
						$show_array[ $show_slug ] = array(
							'name'    => get_the_title( $each_show['show'] ),
							'url'     => get_the_permalink( $each_show['show'] ),
							'country' => get_the_term_list( $each_show['show'], 'lez_country', '', ', ', '' ),
							'format'  => get_the_term_list( $each_show['show'], 'lez_formats' ),
							'chars'   => array(),
						);
					}

					// Add the character to the show array
					$show_array[ $show_slug ]['chars'][ $char_slug ] = array(
						'name' => get_the_title( $char ),
						'url'  => get_the_permalink( $char ),
						'type' => $each_show['type'],
					);

					// if the show isn't published, no links
					if ( get_post_status( $each_show['show'] ) !== 'publish' ) {
						array_push( $show_title, '<em><span class="disabled-show-link">' . get_the_title( $each_show['show'] ) . '</span></em> <small>(' . $each_show['type'] . ' character)</small>' );
					} else {
						array_push( $show_title, '<em><a href="' . get_permalink( $each_show['show'] ) . '">' . get_the_title( $each_show['show'] ) . '</a></em> <small>(' . $each_show['type'] . ' character)</small>' );
					}
				}
				$show_info = implode( ', ', $show_title );
				// Only extract the date for this year and convert to unix time
				// SARA LANCE, I hope no one dies twice in the same year ...
				$died_date = get_post_meta( $char, 'lezchars_death_year', true );
				foreach ( $died_date as $date ) {
					if ( (int) substr( $date, 0, 4 ) === (int) substr( $date, 0, 4 ) ) {
						$died_year  = substr( $date, 0, 4 );
						$died_array = date_parse_from_format( 'Y-m-d', $date );
					} else {
						$died_year  = substr( $date, -4 );
						$died_array = date_parse_from_format( 'm/d/Y', $date );
					}

					if ( $died_year === $this_year ) {
						$died = mktime( $died_array['hour'], $died_array['minute'], $died_array['second'], $died_array['month'], $died_array['day'], $died_array['year'] );
					}
				}

				if ( isset( $died ) ) {
					// Make a list
					$list_array[ $died ][ $char_slug ] = array(
						'name'  => get_the_title( $char ),
						'url'   => get_the_permalink( $char ),
						'shows' => $show_info,
					);
				}
			}

			// Sort alphabetical
			ksort( $list_array );
			ksort( $show_array );
		}

		if ( isset( $queery ) ) {
			// if we counted, just kick that back.
			if ( $count ) {
				$return_array = $dead_count;
			} else {
				$return_array = array(
					'count' => $dead_count,
					'list'  => $list_array,
					'show'  => $show_array,
				);
			}
		}

		return $return_array;
	}
}
