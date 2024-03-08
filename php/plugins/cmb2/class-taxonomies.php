<?php
/**
 * Custom filters for taxonomies so they display properly in meta boxes.
 */

namespace LWTV\Plugins\CMB2;

class Taxonomies {

	/**
	 * Get a list of terms
	 *
	 * Generic function to return an array of taxonomy terms formatted for CMB2.
	 * Simply pass in your get_terms arguments and get back a beautifully formatted
	 * CMB2 options array.
	 *
	 * Source: https://gist.github.com/mustardBees/9eb84e47e8afce5ecad2
	 *
	 * @param string|array $taxonomies Taxonomy name or list of Taxonomy names
	 * @param  array|string $query_args Optional. Array or string of arguments to get terms
	 * @return array CMB2 options array
	 */
	public function get_cmb2_terms_list( $taxonomies, $query_args = '' ) {
		$defaults    = array(
			'taxonomy'   => $taxonomies,
			'hide_empty' => false,
		);
		$args        = wp_parse_args( $query_args, $defaults );
		$terms       = get_terms( $args );
		$terms_array = array();
		if ( ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$terms_array[ $term->term_id ] = $term->name;
			}
		}
		return $terms_array;
	}

	/**
	 * Funky stuff done to save taxonomy data
	 *
	 * If there's post data, we assume whatever we're TRYING to save is correct.
	 * Otherwise, we're always going to nuke the terms and re-save as the post
	 * meta.
	 *
	 * Also we're going to check for the existence of the NONE taxonomy. If that
	 * exists, we remove it. If that empties the array, we'll add it back, but this
	 * prevents cases like clichés being 'none' and 'athlete'.
	 */
	public function select2_taxonomy_save( $post_id, $postmeta, $taxonomy ) {

		global $wpdb;

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$new_post_meta_data = ( isset( $_POST[ $postmeta ] ) ) ? $_POST[ $postmeta ] : ''; // Note: You cannot sanitize this here.
		$none_term          = get_term_by( 'slug', 'none', $taxonomy );
		if ( false !== $none_term && is_array( $new_post_meta_data ) ) {
			$the_post_meta_data = array_diff( $new_post_meta_data, array( $none_term->term_id ) );
		} else {
			$the_post_meta_data = $new_post_meta_data;
		}

		if ( isset( $the_post_meta_data ) && is_array( $the_post_meta_data ) && ! empty( $the_post_meta_data ) ) {
			// If we have postmeta, then we should set the terms
			wp_set_object_terms( $post_id, null, $taxonomy );
			$set_the_terms = array();

			$the_post_meta_data = array_map( 'intval', $the_post_meta_data );
			$the_post_meta_data = array_unique( $the_post_meta_data );

			foreach ( $the_post_meta_data as $term_id ) {
				$term            = get_term_by( 'id', $term_id, $taxonomy );
				$set_the_terms[] = $term->slug;
			}
			wp_set_object_terms( $post_id, $set_the_terms, $taxonomy );
		} elseif ( false !== $none_term ) {
			// If there's no postmeta, then set it 'none' if it exists
			// (this only impacts cliches and tropes at the moment)
			wp_set_object_terms( $post_id, $none_term->term_id, $taxonomy );
			update_post_meta( $post_id, $postmeta, array( $none_term->term_id ) );
		} else {
			wp_set_object_terms( $post_id, '', $taxonomy );
			update_post_meta( $post_id, $postmeta, '' );
		}
	}

	/**
	 * Get default data for some odd CMB2 things using select2
	 * @param  string  $postmeta the name of the postmeta used by CMB2
	 * @param  string  $taxonomy the name of the taxonomy we're using
	 * @param  integer $post_id  post ID
	 * @param  boolean $none     does it have a 'none'?
	 * @return array             An array of Term IDs
	 */
	public function get_select2_defaults( $postmeta, $taxonomy, $post_id = 0, $none = false ) {

		if ( 0 === $post_id ) {
			return;
		}

		$get_postmeta    = get_post_meta( $post_id, $postmeta, true );
		$postmeta_to_add = array();
		if ( ! array( $get_postmeta ) || empty( $get_postmeta ) ) {
			// If NONE is true, we have some extra
			if ( $none ) {
				$get_taxonomy = wp_get_post_terms( $post_id, $taxonomy );
				if ( ! empty( $get_taxonomy ) && ! is_wp_error( $get_taxonomy ) ) {
					foreach ( $get_taxonomy as $the_term ) {
						$postmeta_to_add[] = $the_term->term_id;
					}
					update_post_meta( $post_id, $postmeta, $postmeta_to_add );
				}
			}
		}
		return $postmeta_to_add;
	}
}
