<?php
/**
 * namespace LWTV\Queeries;
 *
 * @since 6.0.1
 */

namespace LWTV\Queeries;

class Get_ID_From_Slug {

	/**
	 * Get Post ID from slug
	 *
	 * @access public
	 * @param  mixed $the_slug - Post Slug
	 * @return string
	 */
	public function make( $the_slug ): string {
		$args   = array(
			'post_type'      => array( 'post_type_shows', 'post_type_actors' ),
			'posts_per_page' => 1,
			'post_name__in'  => array( $the_slug ),
			'fields'         => 'ids',
		);
		$queery = get_posts( $args );

		if ( empty( $queery ) && ! isset( $queery[0] ) ) {
			return '';
		}

		return $queery[0];
	}
}
