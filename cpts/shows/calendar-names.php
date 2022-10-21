<?php
/**
 * Name: Calendar Names
 * Description: Sometimes we have weird names for the calendar.
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

class LWTV_Shows_Calendar {

	/**
	 * Check Show Name
	 *
	 * Since TV Maze sometimes uses different names than we do, we have to make
	 * a related array that can handle two names.
	 *
	 * @param  string $showname Display Name of the show
	 * @param  string $source   lwtv or tvmaze
	 * @return string           The display nem
	 */
	public function check_name( $showname, $source ) {

		// Default we're returning the name we were given.
		$return = $showname;

		// List of valid sources.
		$valid_source = array( 'lwtv', 'tvmaze' );
		$name_array   = array(
			// TV MAZE NAME                              => OUR NAME
			'Charmed'                                    => 'Charmed (2018)',
			'DC\'s Legends of Tomorrow'                  => 'Legends of Tomorrow',
			'Genera+ion'                                 => 'Generation', // this is using the SLUG.
			'Marvel\'s Runaways'                         => 'Runaways',
			'Marvel\'s Agents of S.H.I.E.L.D.'           => 'Agents of S.H.I.E.L.D.',
			'Mythic Quest: Raven\'s Banquet'             => 'Mythic Quest',
			'Marvel\'s M.O.D.O.K.'                       => 'M.O.D.O.K.',
			'Party of Five'                              => 'Party of Five (2020)',
			'Pennyworth: The Origin of Batman\'s Butler' => 'Pennyworth',
			'Queer as Folk'                              => 'Queer as Folk (2022)',
			'Shameless'                                  => 'Shameless (US)',
			'Ghosts'                                     => 'Ghosts (US)',
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
