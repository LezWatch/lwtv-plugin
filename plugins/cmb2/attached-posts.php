<?php
/**
 * CMB2 filters and hooks for Attached Posts.
 */

class LWTV_CMB2_Attached_Posts {

	/**
	 * Constructor
	 */
	public function __construct() {
		// Filter: Attached posts titles.
		add_filter( 'cmb2_attached_posts_title_filter', array( $this, 'filter_title_attached_posts' ), 10, 2 );

		// Filter: Allowed Post Statues.
		add_filter( 'cmb2_attached_posts_status_filter', array( $this, 'filter_post_status_attached_posts' ), 10, 2 );

		// Filter: Allowed Post per page for search.
		add_filter( 'cmb2_attached_posts_per_page_filter', array( $this, 'filter_post_per_page_attached_posts' ), 10, 2 );
	}

	/**
	 * Filter to edit post title.
	 *
	 * @param string $post_title - Post title.
	 * @param int    $post_id    - Post ID.
	 *
	 * @return string            - Modified post title.
	 */
	public function filter_title_attached_posts( $post_title, $post_id ) {
		$additional = array();
		$status     = get_post_status( $post_id );
		$is_queer   = get_post_meta( $post_id, 'lezactors_queer', true );

		if ( false !== $status && 'publish' !== $status && ! str_contains( $post_title, 'Draft' ) ) {
			$additional[] = 'Draft';
		}

		if ( ! empty( $is_queer ) && $is_queer && ! str_contains( $post_title, 'Queer' ) ) {
			$additional[] = 'Queer';
		}

		if ( ! empty( $additional ) ) {
			$post_title .= ' (' . implode( ', ', array_unique( $additional, SORT_REGULAR ) ) . ')';
		}

		return $post_title;
	}

	/**
	 * Filter to change allowed post statues.
	 *
	 * @param array $post_status - current array of post statuses
	 *
	 * @return array             - Forcibly changed array.
	 */
	public function filter_post_status_attached_posts( $post_status ) {
		$post_status = array( 'publish', 'pending', 'draft', 'future', 'private', 'inherit' );

		return $post_status;
	}

	/**
	 * Filter to search over 100 pages (we have a lot!)
	 *
	 * @param int $number - number of posts (default 100)
	 *
	 * @return int        - number of posts per page
	 */
	public function filter_post_per_page_attached_posts( $number = 100 ) {
		return $number;
	}
}

new LWTV_CMB2_Attached_Posts();
