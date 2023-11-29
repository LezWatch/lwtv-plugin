<?php
/**
 * class LWTV_Queery_Post_Meta_And_Tax
 *
 * @since 5.0
 */

class LWTV_Queery_Post_Meta_And_Tax {

	/*
	 * Post Meta AND Taxonomy Query
	 *
	 * Function to generate an array of posts that have a specific post meta AND
	 * a specific taxonomy value. Useful for getting a list of all dead queers
	 * who are main characters (for example).
	 *
	 * @param string $post_type i.e 'posts' or 'post_type_characters'
	 * @param string $key The post meta key being searched for.
	 * @param string $value The post meta VALUE being searched for.
	 * @param string $taxonomy The taxonomy being searched
	 * @param array $term The term being searched for.
	 * @param string $compare Search operator for meta_query. Default =
	 * @param string $operator Search operator for tax_query. Default IN.
	 *
	 * @return array The WP_Query Array
	 */

	public function make( $post_type, $key, $value, $taxonomy, $field, $terms, $compare = '=', $operator = 'IN' ) {
		$count = wp_count_posts( $post_type )->publish;
		$query = new WP_Query(
			array(
				'post_type'      => $post_type,
				'posts_per_page' => $count,
				'no_found_rows'  => true,
				'post_status'    => array( 'publish' ),
				'meta_query'     => array(
					array(
						'key'     => $key,
						'value'   => $value,
						'compare' => $compare,
					),
				),
				'tax_query'      => array(
					array(
						'taxonomy' => $taxonomy,
						'field'    => $field,
						'terms'    => $terms,
						'operator' => $operator,
					),
				),
			)
		);

		wp_reset_query();
		return $query;
	}
}
