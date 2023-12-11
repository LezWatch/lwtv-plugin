<?php
/**
 * Build the array of data.
 *
 * @package LezWatch.TV
 */

namespace LWTV\This_Year;

use LWTV\_Components\This_Year;

class The_Array {
	/**
	 * Build the array we will use to process output.
	 *
	 * @param string $this_year The Year
	 * @param string $view      The context we're viewing (characters on air, shows on air, etc)
	 * @param mixed  $custom    Custom data we may (or may not) need.
	 * @param bool   $count     True/False, are we just counting?
	 *
	 * @return array            The build data.
	 */
	public function make( $this_year, $view, $custom = false, $count = false ) {
		// If there's no data match, return empty:
		if ( ! isset( This_Year::DATA_CLASS_MATCHER[ $view ] ) ) {
			return;
		}

		// Define Data Class.
		$data_class = This_Year::DATA_CLASS_MATCHER[ $view ];

		// Build Params, based on Data type.
		// This should go into a config file, but since it's co-dependant, not sure how.
		// Maybe a config folder config/default.php ?
		$build_params = array(
			'Characters_List' => array( $this_year, $count ), // get_list
			'Characters_Dead' => array( $this_year, $count ), // get_dead
			'Shows_List'      => array( $this_year, $view, $count ), // get_list
			'Overview'        => array( $this_year ),
		);

		// Use the class
		$namespace    = 'LWTV\\This_Year\Build\\' . $data_class;
		$build_params = $build_params[ $data_class ];

		if ( is_array( $build_params ) ) {
			$array = ( new $namespace() )->make( ...$build_params );
		} else {
			$array = ( new $namespace() )->make( $build_params );
		}

		return $array;
	}
}
