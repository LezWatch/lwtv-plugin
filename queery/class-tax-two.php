<?php
/**
 * class LWTV_Queery_Tax_Two
 *
 * @since 5.0
 */

class LWTV_Queery_Tax_Two {

	/*
	 * Taxonomy Two Array
	 *
	 * This check is used for generating a query of posts that are in two taxonomies.
	 * For example you can use it to loop through dead queers who are non-binary
	 *
	 * @param string $post_type i.e 'posts' or 'post_type_characters'
	 * @param string $taxonomy1 The taxonomy being searched
	 * @param array  $term1 The term being searched for.
	 * @param string $taxonomy2 The taxonomy being searched
	 * @param array  $term2 The term being searched for.
	 * @param string $operator1 Search operator. Default IN.
	 * @param string $operator2 Search operator. Default IN.
	 *
	 * @return array The WP_Query Array
	 */
	public function make( $post_type, $taxonomy1, $field1, $terms1, $taxonomy2, $field2, $terms2, $operator1 = 'IN', $operator2 = 'IN', $relation = 'AND' ) {
		$count = wp_count_posts( $post_type )->publish;
		$query = new WP_Query(
			array(
				'post_type'              => $post_type,
				'posts_per_page'         => $count,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'post_status'            => array( 'publish' ),
				'relation'               => $relation,
				'tax_query'              => array(
					array(
						'taxonomy' => $taxonomy1,
						'field'    => $field1,
						'terms'    => $terms1,
						'operator' => $operator1,
					),
					array(
						'taxonomy' => $taxonomy2,
						'field'    => $field2,
						'terms'    => $terms2,
						'operator' => $operator2,
					),
				),
			)
		);
		wp_reset_query();
		return $query;
	}
}
