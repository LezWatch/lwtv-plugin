<?php
/**
 * Name: Calendar Names
 * Description: Sometimes we have weird names for the calendar.
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

class LWTV_Shows_Calendar {

	public function check_name( $showname, $source ) {

		// Default we're returning the name we were given.
		$return = $showname;

		// List of valid sources.
		$valid_source = array( 'lwtv', 'tvmaze' );
		$name_array   = array(
			'Charmed'                          => 'Charmed (2018)',
			'Party of Five'                    => 'Party of Five (2020)',
			'Shameless'                        => 'Shameless (US)',
			'DC\'s Legends of Tomorrow'        => 'Legends of Tomorrow',
			'Marvel\'s Runaways'               => 'Runaways',
			'Marvel\'s Agents of S.H.I.E.L.D.' => 'Agents of S.H.I.E.L.D.',
			'Genera+ion'                       => 'Generation', // this is using the SLUG.
		);

		$search_array = $name_array;
		if ( 'lwtv' === $source ) {
			// this will be faster.
			$search_array = array_flip( $name_array );
		}

		if ( isset( $search_array[ $showname ] ) ) {
			$return = $search_array[ $showname ];
		}

		return $return;
	}

}
