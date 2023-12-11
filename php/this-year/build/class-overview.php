<?php

namespace LWTV\This_Year\Build;

use LWTV\This_Year\The_Array;

class Overview {

	/**
	 * Build the raw data for this year, which is hella recursive.
	 *
	 * @param string $this_year  Year for Data
	 *
	 * @return array
	 */
	public function make( $this_year ) {
		$this_year = ( isset( $this_year ) ) ? $this_year : gmdate( 'Y' );
		$array     = array(
			'characters' => ( new The_Array() )->make( $this_year, 'characters-on-air', false, true ),
			'dead'       => ( new The_Array() )->make( $this_year, 'dead-characters', false, true ),
			'shows'      => ( new The_Array() )->make( $this_year, 'shows-on-air', 'now', true ),
			'started'    => ( new The_Array() )->make( $this_year, 'new-shows', 'started', true ),
			'canceled'   => ( new The_Array() )->make( $this_year, 'canceled-shows', 'ended', true ),
		);

		return $array;
	}
}
