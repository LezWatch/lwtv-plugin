<?php

class LWTV_Statistics_Taxonomy_Build {

	/*
	 * Statistics Taxonomy Array
	 *
	 * Generate array to parse taxonomy content
	 *
	 * @param string $post_type Post Type to be search
	 * @param string $taxonomy Taxonomy to be searched
	 * @param string $terms The terms to be matched (default empty)
	 * @param string $operator Search operator (default IN)
	 *
	 * @return array
	 */
	public function make( $post_type, $taxonomy, $terms = '', $operator = 'IN' ) {

		$transient = 'taxonomy_' . $taxonomy . '_' . $terms;
		$array     = LWTV_Features_Transients::get_transient( $transient );

		if ( false === $array ) {

			$array = array();

			// If no term provided, use get_terms for the taxonomy.
			$taxonomies = ( '' === $terms ) ? get_terms( $taxonomy ) : array( $terms );

			foreach ( $taxonomies as $term ) {
				$term_obj          = ( '' !== $terms ) ? get_term_by( 'slug', $term, $taxonomy, 'ARRAY_A' ) : '';
				$term_link         = get_term_link( $term, $taxonomy );
				$term_slug         = ( '' === $terms ) ? $term->slug : $terms;
				$term_name         = ( '' === $terms ) ? $term->name : $term_obj['name'];
				$count_terms_query = ( new LWTV_Features_Loops() )->tax_query( $post_type, $taxonomy, 'slug', $term_slug, $operator );
				$term_count        = $count_terms_query->post_count;

				$array[ $term_slug ] = array(
					'count' => $term_count,
					'name'  => $term_name,
					'url'   => $term_link,
				);
			}

			// save array as transient for a reason.
			set_transient( $transient, $array, DAY_IN_SECONDS );
		}

		return $array;
	}
}
