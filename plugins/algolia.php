<?php
/*
 * Algolia for WordPress hooks
 * @package lwtv-plugin
 */

class LWTV_Algolia {

	public function __construct() {
		add_filter( 'algolia_should_index_user', array( $this, 'algolia_never_index' ) );
		add_filter( 'algolia_should_index_term', array( $this, 'algolia_never_index' ) );
		add_filter( 'algolia_post_shared_attributes', array( $this, 'algolia_attributes' ), 10, 2 );
		add_filter( 'algolia_searchable_post_shared_attributes', array( $this, 'algolia_attributes' ), 10, 2 );
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
	 * Tweak attributes to store as records.
	 *
	 * We remove the ones we never use for search and we add two more:
	 * scores    - used for ranking and sorting
	 * lwtv_meta - used for extra data
	 *
	 * @param  array   $attributes array of shared attributes
	 * @param  WP_Post $post       The Post object
	 * @return array               Cleaned array of attributes
	 */
	public function algolia_attributes( array $attributes, WP_Post $post ) {

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
				$attributes['score'] = 175;
				break;
			case 'post_type_characters':
				$attributes['score'] = 150;

				// Create meta
				$meta_array  = array();
				// Add All Shows to attribute LWTV_META
				$shows_group = get_post_meta( $post->ID, 'lezchars_show_group', true );
				if ( '' !== $shows_group && is_array( $shows_group ) ) {
					foreach ( $shows_group as $each_show ) {
						$meta_array[] = get_the_title( $each_show['show'] );
					}
				}

				// All all actors
				$actor_group = get_post_meta( $post->ID, 'lezchars_actor', true );
				if ( ! is_array( $actor_group ) ) {
					// This shouldn't be needed anymore but...
					$actor_group = array( get_post_meta( $post->ID, 'lezchars_actor', true ) );
				}
				if ( '' !== $actor_group && is_array( $actor_group ) ) {
					foreach ( $actor_group as $each_actor ) {
						if ( 'private' !== get_post_status( $each_actor ) && 'Unknown' !== get_the_title( $each_actor ) ) {
							$meta_array[] = get_the_title( $each_actor );
						}
					}
				}

				// If we have a meta array, we build it.
				if ( is_array( $meta_array ) && ! empty( $meta_array ) ) {
					$attributes['lwtv_meta'] = implode( ', ', array_unique( $meta_array ) );
				}

				break;
			case 'post_type_actors':
				if ( 'Unknown' === get_the_title( $post->ID ) ) {
					$attributes['score'] = 0;
				} else {
					$attributes['score'] = 125;

					// Default
					$meta_array = array();

					// Meta by name, meta by nature
					$name_array = explode( ' ', get_the_title( $post->ID ) );
					foreach ( $name_array as $name ) {
						$meta_array[] = $name;
					}

					// list all characters
					$char_group = get_post_meta( $post->ID, 'lezactors_char_list', true );
					if ( '' !== $char_group && is_array( $char_group ) ) {
						foreach ( $char_group as $each_char ) {
							$meta_array[] = get_the_title( $each_char );
						}
					}

					// list all shows
					$show_group = get_post_meta( $post->ID, 'lezactors_show_list', true );
					if ( '' !== $show_group && is_array( $show_group ) ) {
						foreach ( $show_group as $each_show ) {
							$meta_array[] = get_the_title( $each_show );
						}
					}

					// If we have a meta array, we build it.
					if ( is_array( $meta_array ) && ! empty( $meta_array ) ) {
						$attributes['lwtv_meta'] = implode( ', ', array_unique( $meta_array ) );
					}
				}
				break;
			default:
				$attributes['score'] = 10;
		}

		return $attributes;
	}

}

new LWTV_Algolia();
