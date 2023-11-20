<?php

class LWTV_This_Year_Overview {

	/**
	 * Build the raw data for this year, which is hella recursive.
	 *
	 * @param string $this_year  Year for Data
	 *
	 * @return array
	 */
	public function build( $this_year ) {
		$this_year = ( isset( $this_year ) ) ? $this_year : gmdate( 'Y' );
		$array     = array(
			'characters' => ( new LWTV_This_Year() )->build_array( $this_year, 'characters-on-air', false, true ),
			'dead'       => ( new LWTV_This_Year() )->build_array( $this_year, 'dead-characters', false, true ),
			'shows'      => ( new LWTV_This_Year() )->build_array( $this_year, 'shows-on-air', 'now', true ),
			'started'    => ( new LWTV_This_Year() )->build_array( $this_year, 'new-shows', 'started', true ),
			'canceled'   => ( new LWTV_This_Year() )->build_array( $this_year, 'canceled-shows', 'ended', true ),
		);

		return $array;
	}
}

new LWTV_This_Year_Overview();
