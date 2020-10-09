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
		add_filter( 'algolia_searchable_post_shared_attributes', array( $this, 'algolia_reduce_attributes' ), 10, 2 );
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

		// Remove things we're not using to make it easier.
		$remove_array = array( 'taxonomies_hierarchical', 'post_excerpt', 'post_modified', 'comment_count', 'menu_order', 'taxonomies', 'post_author', 'post_mime_type' );
		foreach ( $remove_array as $remove_this ) {
			if ( isset( $attributes[ $remove_this ] ) ) {
				unset( $attributes[ $remove_this ] );
			}
		}

		// Remove trailing S to look better
		if ( isset( $attributes['post_type_label'] ) ) {
			$attributes['post_type_label'] = substr( $attributes['post_type_label'], 0, -1 );
		}

		// Add Data for individual ranking
		switch ( $post->post_type ) {
			case 'post_type_shows':
				$attributes['score'] = round( get_post_meta( $post->ID, 'lezshows_the_score', true ), 2 );
				break;
			case 'post_type_characters':
				$attributes['score'] = 150;
				// list all actors?
				// list all shows?
				break;
			case 'post_type_actors':
				$attributes['score'] = 150;
				// list all characters?
				break;
			default:
				$attributes['score'] = 0;
		}

		return $attributes;
	}

}

new LWTV_Algolia();
