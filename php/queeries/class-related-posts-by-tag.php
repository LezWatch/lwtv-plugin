<?php
/**
 * namespace LWTV\Queeries;
 *
 * @since 5.0
 */

namespace LWTV\Queeries;

class Related_Posts_By_Tag {

	/**
	 * Related Posts by Tags.
	 *
	 * @access public
	 * @static
	 * @param string $post_type i.e 'posts' or 'post_type_characters'
	 * @param string $slug i.e. the slug of the post we're trying to relate to
	 * @return WP_Query
	 */
	public function make( $post_type, $slug ) {
		$term = term_exists( $slug, 'post_tag' );
		if ( 0 === $term || null === $term ) {
			return;
		}

		$query = new \WP_Query(
			array(
				'post_type'     => $post_type,
				'no_found_rows' => true,
				'post_status'   => array( 'publish' ),
				'tag'           => $slug,
				'orderby'       => 'date',
				'order'         => 'DESC',
			)
		);
		wp_reset_query();
		return $query;
	}
}
