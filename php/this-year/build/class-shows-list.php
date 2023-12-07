<?php

namespace LWTV\This_Year\Build;

class Shows_List {
	/**
	 * get all the shows that were active for a year
	 * @return array massive array of everything.
	 */
	public function make( $this_year, $type = 'now', $count = false ) {

		// Combine alt names to ensure we have ONE transient.
		switch ( $type ) {
			case 'new-shows':
			case 'started':
				$type = 'started';
				break;
			case 'canceled-shows':
			case 'ended':
				$type = 'ended';
				break;
			default:
				$type = 'now';
				break;
		}

		$transient    = 'shows_list_' . $type . '_' . $this_year;
		$transient   .= ( $count ) ? '_count' : '';
		$return_array = lwtv_plugin()->get_transient( $transient );

		// If we have an array saved, use it.
		if ( false !== $return_array && ! empty( $return_array ) ) {
			return $return_array;
		}

		// Otherwise we build it all.
		$shows_current = array();
		$shows_formats = array();
		$shows_country = array();
		$counted_shows = 0;
		$shows_queery  = lwtv_plugin()->queery_post_type( 'post_type_shows' );

		if ( $shows_queery->have_posts() ) {
			$shows_array = wp_list_pluck( $shows_queery->posts, 'ID' );
			wp_reset_query();
		}

		if ( is_array( $shows_array ) ) {
			foreach ( $shows_array as $show_id ) {
				$show_name = preg_replace( '/\s*/', '', get_the_title( $show_id ) );
				$show_name = strtolower( $show_name );
				$yes_count = false;
				if ( get_post_meta( $show_id, 'lezshows_airdates', true ) && 'publish' === get_post_status( $show_id ) ) {
					$airdates  = get_post_meta( $show_id, 'lezshows_airdates', true );
					$countries = get_the_term_list( $show_id, 'lez_country', '', ', ', '' );
					$format    = get_the_term_list( $show_id, 'lez_formats' );

					switch ( $type ) {
						case 'now':
						case 'shows-on-air':
							if (
								( 'current' === $airdates['finish'] && gmdate( 'Y' ) === $this_year ) // Still Current and it's NOW
								|| ( $airdates['finish'] >= $this_year && $airdates['start'] <= $this_year ) // Airdates between
							) {
								$yes_count = true;
							}
							break;
						case 'started':
						case 'new-shows':
							if ( $airdates['start'] === $this_year ) {
								$yes_count = true;
							}
							break;
						case 'ended':
						case 'canceled-shows':
							if ( $airdates['finish'] === $this_year ) {
								$yes_count = true;
							}
							break;
					}

					// If the show passed whatever checks we have...
					if ( $yes_count ) {
						++$counted_shows;

						// If we're ONLY counting, we don't have to do the rest.
						if ( ! $count ) {
							// Build the first character
							$first_char = substr( $show_name, 0, 1 );
							if ( is_numeric( $first_char ) ) {
								$marker = '#';
							} elseif ( ctype_alnum( $first_char ) ) {
								$marker = $first_char;
							} else {
								$marker = '-';
							}

							// Build the array
							$shows_current[ $marker ][ $show_name ] = array(
								'url'      => get_permalink( $show_id ),
								'name'     => get_the_title( $show_id ),
								'country'  => wp_strip_all_tags( $countries ),
								'format'   => wp_strip_all_tags( $format ),
								'airdates' => $airdates,
							);

							// If there are formats, we add the show to the format
							if ( ! empty( wp_strip_all_tags( $format ) ) ) {
								$shows_formats[ wp_strip_all_tags( $format ) ][ $show_name ] = array(
									'url'      => get_permalink( $show_id ),
									'name'     => get_the_title( $show_id ),
									'country'  => wp_strip_all_tags( $countries ),
									'airdates' => $airdates,
								);
							}

							// If there are countries (heh), we add the show to the countries
							if ( ! empty( wp_strip_all_tags( $countries ) ) ) {
								$these_countries = explode( ', ', wp_strip_all_tags( $countries ) );
								foreach ( $these_countries as $country ) {
									$shows_country[ $country ][ $show_name ] = array(
										'url'      => get_permalink( $show_id ),
										'name'     => get_the_title( $show_id ),
										'format'   => wp_strip_all_tags( $format ),
										'airdates' => $airdates,
									);
								}
							}
						}
					}
				}
			}
		}

		// if we counted, just kick that back.
		if ( $count ) {
			$return_array = $counted_shows;
		} else {
			$return_array = array(
				'count'   => $counted_shows,
				'current' => $shows_current,
				'formats' => $shows_formats,
				'country' => $shows_country,
			);
		}

		return $return_array;
	}
}
