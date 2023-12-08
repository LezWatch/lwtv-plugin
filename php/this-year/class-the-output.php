<?php
/**
 * Output the array of data.
 *
 * @package LezWatch.TV
 */
namespace LWTV\This_Year;

use LWTV\_Components\This_Year;

class The_Output {
	/**
	 * Process the output!
	 *
	 * @param string $this_year  The Year
	 * @param string $view       The context we're viewing (characters on air, shows on air, etc)
	 * @param array  $build_data The Array with all our data
	 * @param mixed  $custom     Custom data we may (or may not) need.
	 * @param bool   $count      True/False, are we just counting?
	 *
	 * @return N/A child function outputs
	 */
	public function make( $this_year, $view, $build_data, $custom = false, $count = false ) {

		// If there's no valid format, bail.
		if ( ! isset( This_Year::FORMAT_CLASS_MATCHER[ $view ] ) ) {
			return;
		}

		// Define match:
		$format_class_match = This_Year::FORMAT_CLASS_MATCHER[ $view ];

		// Build Params, based on Data type.
		$format_params = array(
			'Characters' => array( $this_year, $count, $build_data ),
			'Dead'       => array( $this_year, $count, $build_data ),
			'Shows'      => array( $this_year, $build_data, $view ),
			'Overview'   => array( $this_year, $build_data ),
			'Chart'      => array( $this_year, $custom, $build_data ),
		);

		// Use the class
		$namespace     = 'LWTV\\This_Year\Format\\' . $format_class_match;
		$format_params = $format_params[ $format_class_match ];

		if ( is_array( $format_params ) ) {
			( new $namespace() )->make( ...$format_params );
		} else {
			( new $namespace() )->make( $format_params );
		}
	}
}
