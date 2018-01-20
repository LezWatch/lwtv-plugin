<?php
/**
 * Name: Display Statistics
 *
 * Outputs the display
 */

/**
 * LWTV_Stats_Display class.
 */
class LWTV_Stats_Display {

	// Common defines
	public static $iconpath, $intro;

	/**
	 * Determine icon for each stats page
	 * 
	 * @access public
	 * @static
	 * @param string $type (default: 'main')
	 * @return void
	 */
	public static function iconpath( $type = 'main' ) {

		$return = '';
		if ( defined( 'LP_SYMBOLICONS_PATH' ) ) {
			if ( $type == 'main' )       $return = LP_SYMBOLICONS_PATH . 'bar_graph.svg';
			if ( $type == 'death' )      $return = LP_SYMBOLICONS_PATH . 'rip_gravestone.svg';
			if ( $type == 'characters' ) $return = LP_SYMBOLICONS_PATH . 'users.svg';
			if ( $type == 'shows' )      $return = LP_SYMBOLICONS_PATH . 'tv_retro.svg';
			if ( $type == 'lists' )      $return = LP_SYMBOLICONS_PATH . 'bar_graph_alt.svg';
			if ( $type == 'trends' )     $return = LP_SYMBOLICONS_PATH . 'line_graph.svg';
			if ( $type == 'trends' )     $return = LP_SYMBOLICONS_PATH . 'globe.svg';
		}

		return $return;
	}

	/**
	 * Determine Title for each stats page
	 * 
	 * @access public
	 * @static
	 * @param string $type (default: 'main')
	 * @return void
	 */
	public static function title( $type = 'main' ) {

		if ( $type == 'main' )       $return = 'Statistics of Queer Females on TV';
		if ( $type == 'death' )      $return = 'Statistics on Queer Female Deaths';
		if ( $type == 'characters' ) $return = 'Statistics on Queer Female Characters';
		if ( $type == 'shows' )      $return = 'Statistics on Shows with Queer Females';
		if ( $type == 'lists' )      $return = 'Statistics in the form of Lists';
		if ( $type == 'trends' )     $return = 'Statistics in the form of Trendlines';
		if ( $type == 'nations' )    $return = 'Statistics on Nations with Shows with Queer Females';

		return $return;
	}

	/**
	 * Determine archive intro for each stats page
	 * 
	 * @access public
	 * @static
	 * @param string $type (default: 'main')
	 * @return void
	 */
	public static function intro( $type = 'main' ) {
		if ( $type == 'main' )       $return = '';
		if ( $type == 'death' )      $return = 'For a pure list of all dead, we have <a href="https://lezwatchtv.com/trope/dead-queers/">shows where characters died</a> as well as <a href="https://lezwatchtv.com/cliche/dead/">characters who have died</a> (aka the <a href="https://lezwatchtv.com/cliche/dead/">Dead Lesbians</a> list).';
		if ( $type == 'characters' ) $return = 'Statistics specific to characters (sexuality, gender IDs, role types, etc).';
		if ( $type == 'shows' )      $return = 'Statistics specific to shows.';
		if ( $type == 'lists' )      $return = 'Raw statistics.';
		if ( $type == 'trends' )     $return = 'Trendlines and predictions.';
		if ( $type == 'nations' )    $return = 'Data specific to queer representation on shows by nation.';

		return $return;
	}
}

new LWTV_Stats_Display();