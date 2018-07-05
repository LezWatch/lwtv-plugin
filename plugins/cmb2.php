<?php
/*
Library: CMB2 Add Ons
Description: Addons for CMB2 that make life worth living
Version: 1.0
*/


/**
 * class LWTV_CMB2_Addons
 *
 * Customize CMB2
 *
 * @since 1.0
 */
class LWTV_CMB2_Addons {

	/**
	 * Constructor
	 */
	public function __construct() {

		/* LWTV weird stuff */
		require_once dirname( __FILE__ ) . '/cmb2/lwtv.php';

		/* CMB2 Grid */
		require_once dirname( __FILE__ ) . '/cmb2/CMB2-grid/Cmb2GridPluginLoad.php';

		/* Select2 */
		require_once dirname( __FILE__ ) . '/cmb2/cmb-field-select2/cmb-field-select2.php';

		/* Date Year Range */
		require_once dirname( __FILE__ ) . '/cmb2/year-range.php';
	}

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
	public static function select2_get_options_array_tax( $taxonomies, $query_args = '' ) {
		$defaults    = array(
			'hide_empty' => false,
		);
		$args        = wp_parse_args( $query_args, $defaults );
		$terms       = get_terms( $taxonomies, $args );
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
	 */
	public static function select2_taxonomy_save( $post_id, $postmeta, $taxonomy ) {

		global $wpdb;

		$get_post_meta = get_post_meta( $post_id, $postmeta, true );
		$get_the_terms = wp_get_post_terms( $post_id, $taxonomy );

		if ( is_array( $get_post_meta ) ) {
			// If we already have the post meta, then we should set the terms
			$get_post_meta = array_map( 'intval', $get_post_meta );
			$get_post_meta = array_unique( $get_post_meta );
			$set_the_terms = array();

			foreach ( $get_post_meta as $term_id ) {
				$term = get_term_by( 'id', $term_id, $taxonomy );
				array_push( $set_the_terms, $term->slug );
			}

			wp_set_object_terms( $post_id, $set_the_terms, $taxonomy );
		} elseif ( $get_the_terms && ! is_wp_error( $get_the_terms ) ) {
			foreach ( $get_the_terms as $term ) {
				wp_remove_object_terms( $post_id, $term->term_id, $taxonomy );
			}
		}
	}

}

new LWTV_CMB2_Addons();
