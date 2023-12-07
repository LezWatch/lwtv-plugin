<?php

namespace LWTV\Statistics\Build;

class Dead_List {

	/**
	 * List of dead characters
	 *
	 * @param  string $format [all|YEAR]
	 * @return array          All the dead, yo
	 */
	public function make( $format = 'array' ) {
		$transient = 'dead_list_' . $format;
		$array     = lwtv_plugin()->get_transient( $transient );

		if ( false === $array ) {
			$array     = array();
			$dead_loop = lwtv_plugin()->queery_post_meta( 'post_type_characters', 'lezchars_death_year', '', '!=' );

			if ( $dead_loop->have_posts() ) {
				$queery = wp_list_pluck( $dead_loop->posts, 'ID' );
				wp_reset_query();
			}

			foreach ( $queery as $char ) {
				$died = get_post_meta( $char, 'lezchars_death_year', true );

				foreach ( $died as $died_date ) {
					// If there's no entry, add it.
					if ( ! isset( $array[ $died_date ] ) ) {
						$array[ $died_date ] = array(
							'date' => $died_date,
						);
					}

					$array[ $died_date ]['chars'][ $char ] = array(
						'name' => get_the_title( $char ),
						'url'  => get_the_permalink( $char ),
					);
				}
			}
		}

		// sort by date (newest first)
		krsort( $array );

		// calculate time since last death
		$keys      = array_keys( $array );
		$key_count = count( $keys ) - 1;
		for ( $i = 0; $i < $key_count; $i++ ) {
			// Check the diff
			$date1 = date_create( $keys[ $i ] );
			$date2 = date_create( $keys[ $i + 1 ] );
			$diff  = date_diff( $date1, $date2 );
			$days  = $diff->format( '%a' );

			// Add the time since last death
			$array[ $keys[ $i ] ]['since'] = $days;
		}

		// Change what we output...
		switch ( $format ) {
			case 'array':
				$return = $array;
				break;
			case 'time':
				$diff_since = array(
					'time' => max( array_column( $array, 'since' ) ),
				);
				for ( $i = 0; $i < $key_count; $i++ ) {
					if ( $diff_since['time'] === $array[ $keys[ $i ] ]['since'] ) {
						$diff_since['end']   = $keys[ $i ];
						$diff_since['start'] = $keys[ $i + 1 ];
					}
				}
				$return = array(
					'time'  => $diff_since['time'],
					'start' => $diff_since['start'],
					'end'   => $diff_since['end'],
				);
				break;
		}

		return $return;
	}
}
