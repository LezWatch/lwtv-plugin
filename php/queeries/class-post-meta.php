<?php
/**
 * namespace LWTV\Queeries;
 *
 * @since 5.0
 */

namespace LWTV\Queeries;

class Post_Meta {
	/*
	 * Post Meta Array
	 *
	 * For when you need the whole post data
	 *
	 * @param string $post_type i.e 'posts' or 'post_type_characters'
	 * @param string $key The post meta key being searched for.
	 * @param string $value The post meta VALUE being searched for.
	 * @param string $compare Search operator. Default =
	 *
	 * @return array The WP_Query Array
	 */
	public function make( $post_type, $key, $value, $compare = '=' ) {
		$count = wp_count_posts( $post_type )->publish;
		if ( '' !== $value ) {
			$query = new \WP_Query(
				array(
					'post_type'              => $post_type,
					'post_status'            => array( 'publish' ),
					'orderby'                => 'title',
					'order'                  => 'ASC',
					'posts_per_page'         => $count,
					'no_found_rows'          => true,
					'update_post_term_cache' => false,
					'meta_query'             => array(
						array(
							'key'     => $key,
							'value'   => $value,
							'compare' => $compare,
						),
					),
				)
			);
		} else {
			$query = new \WP_Query(
				array(
					'post_type'              => $post_type,
					'post_status'            => array( 'publish' ),
					'orderby'                => 'title',
					'order'                  => 'ASC',
					'posts_per_page'         => $count,
					'no_found_rows'          => true,
					'update_post_term_cache' => false,
					'meta_query'             => array(
						array(
							'key'     => $key,
							'compare' => $compare,
						),
					),
				)
			);
		}

		wp_reset_query();
		return $query;
	}
}
