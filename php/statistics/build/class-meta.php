<?php

namespace LWTV\Statistics\Build;

class Meta {

	/*
	 * Statistics Simple Meta Array
	 *
	 * Generate array to parse post meta data
	 *
	 * @param string $post_type Post Type to be search
	 * @param array $meta_array Meta terms to loop through
	 * @param string $key Post Meta Key name (i.e. lezchars_gender)
	 * @param string $data The data 'subject' - used to generate the URLs
	 * @param string $compare The type of comparison (default =)
	 *
	 * @return array
	 */
	public function make( $post_type, $meta_array, $key, $data, $compare = '=' ) {
		$transient = 'stats_meta_' . $key;
		$array     = lwtv_plugin()->get_transient( $transient );

		if ( false === $array ) {

			$array = array();
			foreach ( $meta_array as $value ) {
				$meta_query      = lwtv_plugin()->queery_post_meta( $post_type, $key, $value, $compare );
				$array[ $value ] = array(
					'count' => $meta_query->post_count,
					'name'  => ucfirst( $value ),
					'url'   => home_url( '/' . $data . '/' . lcfirst( $value ) . '/' ),
				);
			}

			// save array as transient for a reason.
			set_transient( $transient, $array, DAY_IN_SECONDS );
		}

		return $array;
	}
}
