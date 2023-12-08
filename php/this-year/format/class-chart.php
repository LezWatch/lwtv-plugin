<?php

namespace LWTV\This_Year\Format;

class Chart {
	/**
	 * Chart of characters for the year.
	 *
	 * @access public
	 * @param mixed  $this_year
	 * @param string $format
	 * @return void
	 */
	public function make( $this_year, $format = 'sexuality', $loop_array = array() ) {
		// Defaults:
		$this_year    = ( isset( $this_year ) ) ? $this_year : gmdate( 'Y' );
		$valid_format = array( 'gender', 'sexuality ' );
		$format       = ( in_array( $format, $valid_format, true ) ) ? $format : 'sexuality';

		// Get array:
		$char_array = $loop_array['list'];

		// If the data isn't empty, we go!
		if ( ! empty( $char_array ) ) {
			lwtv_plugin()->generate_statistics( 'characters', $format . '_year_' . $this_year, 'piechart', '', $char_array );
		}
	}
}
