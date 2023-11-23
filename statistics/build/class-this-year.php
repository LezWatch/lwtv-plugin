<?php

class LWTV_Statistics_This_Year_Build {

	/**
	 * Stats for This Year
	 *
	 * @param string $data
	 * @param array  $year_array
	 *
	 * @return array
	 */
	public function make( $data, $year_array = array() ) {

		// loop through array and rebuild into format for charts.
		$transient = 'this_year_' . $data;
		$array     = LWTV_Features_Transients::get_transient( $transient );
		$taxonomy  = substr( $data, 0, -10 );      // Remove _year_XXXX from the end.

		// If the array is empty, we want to rebuild it.
		if ( false === $array || empty( $array ) ) {
			$array = array();

			// Use get_terms for the taxonomy.
			$taxonomies = get_terms( 'lez_' . $taxonomy );

			// Build the array we need
			foreach ( $taxonomies as $term ) {
				$array[ $term->slug ] = array(
					'name'  => $term->name,
					'url'   => $term->link,
					'count' => 0,
				);
			}

			if ( ! empty( $year_array ) ) {
				foreach ( $year_array as $character ) {
					$terms = get_the_terms( $character['id'], 'lez_' . $taxonomy );
					foreach ( $terms as $term ) {
						++$array[ $term->slug ]['count'];
					}
				}
			}

			// save array as transient for a reason.
			set_transient( $transient, $array, DAY_IN_SECONDS );
		}

		return $array;
	}
}
