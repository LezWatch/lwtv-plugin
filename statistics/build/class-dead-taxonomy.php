<?php

class LWTV_Statistics_Dead_Taxonomy_Build {

	/*
	 * Statistics Taxonomy Array for DEAD
	 *
	 * Generate array to parse taxonomy content for death
	 *
	 * @param string $post_type Post Type to be searched
	 * @param string $taxonomy Taxonomy to be searched
	 *
	 * @return array
	 */
	public function make( $post_type, $taxonomy ) {

		$array      = array();
		$taxonomies = get_terms( $taxonomy );

		foreach ( $taxonomies as $term ) {
			$queery = ( new LWTV_Queery_Tax_Two() )->make( $post_type, $taxonomy, 'slug', $term->slug, 'lez_cliches', 'slug', 'dead' );

			$array[ $term->slug ] = array(
				'count' => $queery->post_count,
				'name'  => $term->name,
				'url'   => get_term_link( $term ),
			);
		}
		return $array;
	}
}
