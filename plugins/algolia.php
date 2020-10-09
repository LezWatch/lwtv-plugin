<?php
/*
 * Algolia for WordPress hooks
 * @package lwtv-plugin
 */

class LWTV_Algolia {

	public function __construct() {
		add_filter( 'algolia_should_index_user', array( $this, 'algolia_never_index' ) );
		add_filter( 'algolia_should_index_term', array( $this, 'algolia_never_index' ) );
		add_filter( 'algolia_post_shared_attributes', array( $this, 'algolia_reduce_attributes' ), 10, 2 );
		add_filter( 'algolia_post_post_type_actors_shared_attributes', array( $this, 'algolia_reduce_attributes' ), 10, 2 );
		add_filter( 'algolia_post_post_type_shows_shared_attributes', array( $this, 'algolia_reduce_attributes' ), 10, 2 );
		add_filter( 'algolia_post_post_type_characters_shared_attributes', array( $this, 'algolia_reduce_attributes' ), 10, 2 );
	}

	/**
	 * For things Algolia should never index.
	 *
	 * @return boolval false
	 */
	public function algolia_never_index() {
		return false;
	}

	/**
	 * Reduce attributes to store as records.
	 *
	 * @param  array   $attributes array of shared attributes
	 * @param  WP_Post $post       The Post object
	 * @return array               Cleaned array of attributes
	 */
	public function algolia_reduce_attributes( array $attributes, WP_Post $post ) {
		$remove_array = array( 'taxonomies_hierarchical', 'post_excerpt', 'post_modified', 'comment_count', 'menu_order', 'taxonomies', 'post_author', 'post_mime_type' );

		foreach ( $remove_array as $remove_this ) {
			if ( isset( $attributes[ $remove_this ] ) ) {
				unset( $attributes[ $remove_this ] );
			}
		}

		return $attributes;
	}

}

new LWTV_Algolia();
