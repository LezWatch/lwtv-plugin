<?php
/**
 * Output the array of data.
 *
 * @package LezWatch.TV
 */

class LWTV_This_Year_Output {
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
		if ( ! isset( LWTV_This_Year::FORMAT_CLASS_MATCHER[ $view ] ) ) {
			return;
		}

		// Define match:
		$format_class_match = LWTV_This_Year::FORMAT_CLASS_MATCHER[ $view ];

		// Build Params, based on Data type.
		$format_params = array(
			'Characters' => array( $this_year, $count, $build_data ),
			'Dead'       => array( $this_year, $count, $build_data ),
			'Shows'      => array( $this_year, $build_data, $view ),
			'Default'    => array( $this_year, $build_data ),
			'Chart'      => array( $this_year, $custom, $build_data ),
		);

		$format_class  = 'LWTV_This_Year_' . $format_class_match . '_Format';
		$format_params = $format_params[ $format_class_match ];

		if ( is_array( $format_params ) ) {
			( new $format_class() )->make( ...$format_params );
		} else {
			( new $format_class() )->make( $format_params );
		}
	}
}
