<?php

namespace LWTV\Statistics\Build;

class Dead_Taxonomy {

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
			$queery = lwtv_plugin()->queery_tax_two( $post_type, $taxonomy, 'slug', $term->slug, 'lez_cliches', 'slug', 'dead' );

			$array[ $term->slug ] = array(
				'count' => ( is_object( $queery ) ) ? $queery->post_count : 0,
				'name'  => $term->name,
				'url'   => get_term_link( $term ),
			);
		}
		return $array;
	}
}
