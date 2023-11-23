<?php
/**
 * Build the array of data.
 *
 * @package LezWatch.TV
 */

class LWTV_This_Year_Array {
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
		if ( ! isset( LWTV_This_Year::DATA_CLASS_MATCHER[ $view ] ) ) {
			return;
		}

		// Define Data Class.
		$data_class = LWTV_This_Year::DATA_CLASS_MATCHER[ $view ];

		// Build Params, based on Data type.
		// This should go into a config file, but since it's co-dependant, not sure how.
		// Maybe a config folder config/default.php ?
		$build_params = array(
			'Characters_List' => array( $this_year, $count ), // get_list
			'Characters_Dead' => array( $this_year, $count ), // get_dead
			'Shows_List'      => array( $this_year, $view, $count ), // get_list
			'Overview'        => array( $this_year ),
		);

		$build_class     = 'LWTV_This_Year_' . $data_class . '_Build';
		$build_params    = $build_params[ $data_class ];
		$build_class_var = new $build_class();

		// If this is an array, use call_user_func_array().
		if ( is_array( $build_params ) ) {
			$array = call_user_func_array( array( $build_class_var, 'make' ), $build_params );
		} else {
			$array = $build_class_var->make( $build_params );
		}

		return $array;
	}
}
