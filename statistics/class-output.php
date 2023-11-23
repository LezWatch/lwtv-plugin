<?php
/**
 * Output the array of data.
 *
 * @package LezWatch.TV
 */

class LWTV_Statistics_Output {
	/**
	 * Build out the format based on the build params.
	 *
	 * @param string $subject       'actors', 'characters', or 'shows'.
	 * @param string $data          The stats being run.
	 * @param array  $build_array   Array of data to parse
	 * @param string $count         Number of items
	 * @param string $format        The format of the output.
	 *
	 * @return void                 Calls function which outputs.
	 */
	public function make( $subject, $data, $build_array, $count, $format, $data_original = null ) {

		// If there's no valid format, bail.
		if ( ! isset( LWTV_Statistics::FORMAT_CLASS_MATCHER[ $format ] ) ) {
			return;
		}

		// Define match:
		$format_class_match = LWTV_Statistics::FORMAT_CLASS_MATCHER[ $format ];

		// Reset Data:
		$data = ( ! is_null( $data_original ) ) ? $data_original : $data;

		// Assign parameters to format:
		$format_params = array(
			'Averages'          => array( $subject, $data, $build_array, $count, $format ),
			'Barcharts'         => array( $subject, $data, $build_array ),
			'Lists'             => array( $subject, $data, $build_array, $count ),
			'Percentages'       => array( $subject, $data, $build_array, $count ),
			'Piecharts'         => array( $subject, $data, $build_array ),
			'Stacked_Barcharts' => array( $subject, $data, $build_array ),
			'Trendline'         => array( $subject, $data, $build_array ),
		);

		// Require our file:
		$format_class  = 'LWTV_Statistics_' . $format_class_match . '_Format';
		$format_params = $format_params[ $format_class_match ];

		// return formatted output.
		if ( is_array( $format_params ) ) {
			$format_class_var = new $format_class();
			call_user_func_array( array( $format_class_var, 'make' ), $format_params );
		} else {
			( new $format_class() )->make( $format_params );
		}
	}
}
