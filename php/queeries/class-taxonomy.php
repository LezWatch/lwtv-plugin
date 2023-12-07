<?php
/**
 * namespace LWTV\Queeries;
 *
 * @since 5.0
 */

namespace LWTV\Queeries;

class Taxonomy {

	/*
	 * Taxonomy Array
	 *
	 * @param string $post_type i.e 'posts' or 'post_type_characters'
	 * @param string $taxonomy The taxonomy being searched
	 * @param array $term The term being searched for.
	 * @param string $operator Search operator. Default IN.
	 *
	 * @return array The WP_Query Array
	 */
	public function make( $post_type, $taxonomy, $field, $term, $operator = 'IN' ) {
		$count  = wp_count_posts( $post_type )->publish;
		$queery = new \WP_Query(
			array(
				'post_type'              => $post_type,
				'posts_per_page'         => $count,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'post_status'            => array( 'publish' ),
				'tax_query'              => array(
					array(
						'taxonomy' => $taxonomy,
						'field'    => $field,
						'terms'    => $term,
						'operator' => $operator,
					),
				),
			)
		);

		wp_reset_query();

		return $queery;
	}
}
