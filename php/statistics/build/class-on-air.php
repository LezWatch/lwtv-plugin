<?php

namespace LWTV\Statistics\Build;

class On_Air {

	/*
	 * Statistics On Air
	 *
	 * Trying to do math of who's on what year.
	 *
	 * @param string $post_type  Post Type of data (show or character)
	 * @param array  $data       Array of data to loop at.
	 * @param string $minor      String for if this is a subset (like station or nation)
	 *
	 * @return array
	 */
	public function make( $post_type, $data = false, $minor = false ) {

		$transient = 'on_air_stats_' . $post_type;
		$transient = ( false !== $minor ) ? $transient . '_' . $minor : $transient;
		$array     = lwtv_plugin()->get_transient( $transient );

		if ( false === $array ) {

			$array = array();

			// Create the date with regards to timezones
			$tz        = 'America/New_York';
			$timestamp = time();
			$dt        = new \DateTime( 'now', new \DateTimeZone( $tz ) ); //first argument "must" be a string
			$dt->setTimestamp( $timestamp ); //adjust the object to correct timestamp
			$this_year = $dt->format( 'Y' );

			// Array of Years
			$year_first = LWTV_FIRST_YEAR;
			$year_array = array();
			foreach ( range( $this_year, $year_first ) as $x ) {
				$year_array[ $x ] = $x;
			}
			foreach ( $year_array as $year ) {
				switch ( $post_type ) {
					case 'post_type_characters':
						// It doesn't matter which show they're on, just that they're on that year.
						$year_queery = ( false === $data ) ? lwtv_plugin()->queery_post_meta( 'post_type_characters', 'lezchars_show_group', $year, 'LIKE' ) : $data;
						break;
					case 'post_type_shows':
						$year_queery = 0;
						$show_queery = ( false === $data ) ? lwtv_plugin()->queery_post_type( 'post_type_shows' ) : $data;
						$allshows    = array();

						if ( $show_queery->have_posts() ) {
							$allshows = wp_list_pluck( $show_queery->posts, 'ID' );
							wp_reset_query();
							$allshows = ( ! is_array( $allshows ) ) ? array( $allshows ) : $allshows;
						}

						foreach ( $allshows as $post_id ) {
							$on_air = lwtv_plugin()->is_show_on_air( $post_id, $year );
							if ( false !== $on_air ) {
								++$year_queery;
							}
						}
						break;
				}

				// If we have values for $year_queery we add to the array
				if ( ! empty( $year_queery ) ) {
					// Shows spits back a number, characters an object
					if ( is_numeric( $year_queery ) ) {
						$count = $year_queery;
					} else {
						$count = $year_queery->post_count;
					}

					$array[ $year ] = array(
						'name'  => $year,
						'count' => $count,
						'url'   => home_url( '/this-year/' . $year . '/' ),
					);
				}
			}

			if ( empty( $array ) ) {
				// If we're empty, delete the transients.
				delete_transient( $transient );
			} else {
				// Otherwise save array as transient for 14 hours
				set_transient( $transient, $array, DAY_IN_SECONDS );
			}
		}

		// Fallback if empty so we don't throw errors.
		// It should be impossible to get here, but ...
		if ( empty( $array ) ) {
			$array[ $this_year ] = array(
				'name'  => $this_year,
				'count' => 0,
				'url'   => home_url( '/this-year/' ),
			);
		}

		return $array;
	}
}
