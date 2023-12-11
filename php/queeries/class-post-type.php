<?php
/**
 * namespace LWTV\Queeries;
 *
 * @since 5.0
 */

namespace LWTV\Queeries;

class Post_Type {
	/*
	 * Post Type Array
	 *
	 * Generate an array of all posts in a specific post type.
	 *
	 * @param string $post_type i.e 'posts' or 'post_type_characters'
	 *
	 * @return array The WP_Query Array
	 */

	public function make( $post_type, $page = 0 ) {

		// If the post type does not exist, bail.
		if ( ! post_type_exists( $post_type ) ) {
			return;
		}

		if ( 0 === $page ) {
			$count  = wp_count_posts( $post_type )->publish;
			$offset = 0;
		} else {
			$count  = 100;
			$offset = ( 100 * $page ) - 100;
		}
		$qarray = array(
			'post_type'              => $post_type,
			'posts_per_page'         => $count,
			'offset'                 => $offset,
			'orderby'                => 'title',
			'order'                  => 'ASC',
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'post_status'            => array( 'publish' ),
		);
		$queery = new \WP_Query( $qarray );
		wp_reset_query();
		return $queery;
	}
}
