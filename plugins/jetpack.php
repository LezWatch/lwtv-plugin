<?php
/*
 * Jetpack for WordPress hooks
 * @package lwtv-plugin
 */

class LWTV_Jetpack {

	public function __construct() {
		add_action( 'init', array( $this, 'init_jetpack_search_filters' ) );
	}

	/**
	 * Jetpack Search Filters
	 * We want to make sure we get post types in there.
	 */
	public function init_jetpack_search_filters() {
		if ( class_exists( 'Jetpack_Search' ) ) {
			Jetpack_Search::instance()->set_filters( [
				'Content Type' => [
					'type'  => 'post_type',
					'count' => 10,
				],
				'Categories'   => [
					'type'     => 'taxonomy',
					'taxonomy' => 'category',
					'count'    => 10,
				],
				'Tags'         => [
					'type'     => 'taxonomy',
					'taxonomy' => 'post_tag',
					'count'    => 10,
				],
			] );
		}
	}
}

new LWTV_Jetpack();
