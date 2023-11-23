<?php
/**
 * Generator Wrapper for 'this year' data.
 *
 * @package LezWatch.TV
 */

class LWTV_This_Year_Generator {
	/**
	 * Generate data
	 *
	 * @param string $this_year  Year
	 * @param string $view       The context we're viewing (characters on air, shows on air, etc)
	 * @param mixed  $custom     Custom data we may (or may not) need.
	 *
	 * @return N/A
	 */
	public static function make( $this_year, $view, $custom = false ) {
		// Build Array.
		$build_data = ( new LWTV_This_Year_Array() )->make( $this_year, $view, $custom );

		// If the array is not empty, build
		if ( ! empty( $build_data ) ) {
			( new LWTV_This_Year_Output() )->make( $this_year, $view, $build_data, $custom );
		}
	}
}
