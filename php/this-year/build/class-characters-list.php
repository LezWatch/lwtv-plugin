<?php

namespace LWTV\This_Year\Build;

class Characters_List {

	/**
	 * Get a list of the characters for a year
	 * @param  int     $this_year The year
	 * @param  boolean $count     Just a count?
	 * @return array              All the data you need.
	 */
	public function make( $this_year, $count = false ) {

		$transient    = 'characters_list_' . $this_year;
		$transient   .= ( $count ) ? '_count' : '';
		$return_array = lwtv_plugin()->get_transient( $transient );

		// If we have an array saved, use it.
		if ( false !== $return_array && ! empty( $return_array ) ) {
			return $return_array;
		}

		// Get the loop
		$loop = lwtv_plugin()->queery_post_meta( 'post_type_characters', 'lezchars_show_group', $this_year, 'REGEXP' );
		wp_reset_query();

		if ( ! is_object( $loop ) || ! $loop->have_posts() ) {
			return;
		}

		$queery        = wp_list_pluck( $loop->posts, 'ID' );
		$counted_chars = 0;
		$show_array    = array();
		$char_array    = array();

		if ( isset( $queery ) && is_array( $queery ) ) {
			foreach ( $queery as $char ) {
				$show_ids   = get_post_meta( $char, 'lezchars_show_group', true );
				$show_title = array();
				foreach ( $show_ids as $each_show ) {
					// If it's an array, de-array it.
					if ( is_array( $each_show['show'] ) ) {
						$each_show['show'] = reset( $each_show['show'] );
					}

					// Make sure this show is in the year
					if ( array_key_exists( 'appears', $each_show ) && in_array( $this_year, $each_show['appears'], true ) ) {
						++$counted_chars;

						// If we're ONLY counting, we can bail now.
						if ( ! $count ) {
							// Get some defaults
							$char_slug = get_post_field( 'post_name', get_post( $char ) );
							$show_slug = get_post_field( 'post_name', get_post( $each_show['show'] ) );

							// if the show isn't already in the array, we create it
							if ( ! array_key_exists( $show_slug, $show_array ) ) {
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
					}
					if ( ! $count ) {
						$show_info = implode( ', ', $show_title );
					}
				}

				// If there are shows listed, let's add it to the character array
				if ( isset( $show_info ) && '' !== $show_info ) {
					$char_array[ $char_slug ] = array(
						'name'  => get_the_title( $char ),
						'url'   => get_the_permalink( $char ),
						'id'    => $char,
						'shows' => $show_info,
					);
				}
			}
		}

		// if we counted, just kick that back.
		if ( $count ) {
			$return_array = $counted_chars;
		} else {
			// Sort arrays
			ksort( $char_array );
			ksort( $show_array );

			$return_array = array(
				'count' => $counted_chars,
				'list'  => $char_array,
				'show'  => $show_array,
			);
		}

		return $return_array;
	}
}
