<?php

class LWTV_Stats_Dead_Meta_Tax {

	/*
	 * Dead Statistics Meta and Taxonomy Array
	 *
	 * Generate array to parse taxonomy content as it relates to post metas
	 *
	 * @param string $post_type Post Type to be search
	 * @param array $meta_array Meta terms to loop through
	 * @param string $key Post Meta Key name (i.e. lezchars_gender)
	 * @param string $taxonomy Taxonomy to restrict to (default lez_cliches)
	 * @param string $field Taxonomy to restrict to (default dead)
	 *
	 * @return array
	 */
	public function build( $post_type, $meta_array, $key, $taxonomy = 'lez_cliches', $field = 'dead' ) {

		$transient = 'dead_meta_tax_' . $post_type . '_' . $taxonomy . '_' . $field;
		$array     = LWTV_Transients::get_transient( $transient );

		if ( false === $array ) {
			$array = array();

			foreach ( $meta_array as $value ) {
				$query           = ( new LWTV_Loops() )->post_meta_and_tax_query( $post_type, $key, $value, $taxonomy, 'slug', $field );
				$array[ $value ] = array(
					'count' => $query->post_count,
					'name'  => ucfirst( $value ),
					'url'   => home_url( '/cliche/' . $value ),
				);
			}

			// save array as transient for a reason.
			set_transient( $transient, $array, DAY_IN_SECONDS );
		}

		return $array;
	}
}

new LWTV_Stats_Dead_Meta_Tax();
