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
		require_once dirname( __FILE__ ) . '/cmb2/cmb2-grid/Cmb2GridPluginLoad.php';
		/* Select2 */
		if ( ! class_exists( 'PW_CMB2_Field_Select2' ) ) {
			require_once dirname( __FILE__ ) . '/cmb2/cmb-field-select2/cmb-field-select2.php';
		}
		/* Date Year Range */
		require_once dirname( __FILE__ ) . '/cmb2/year-range.php';
		/* Attached Posts */
		require_once dirname( __FILE__ ) . '/cmb2/cmb2-attached-posts/cmb2-attached-posts-field.php';

		// Filter to allow show_on to limit by role
		add_filter( 'cmb2_show_on', array( $this, 'cmb_show_meta_to_chosen_roles' ), 10, 2 );

		// Filter attached posts titles.
		add_filter( 'cmb2_attached_posts_title_filter', array( $this, 'cmb_filter_title_attached_posts' ), 10, 2 );
		// Filter allowed Post Statues.
		add_filter( 'cmb2_attached_posts_status_filter', array( $this, 'cmb_filter_post_status_attached_posts' ), 10, 2 );
		// Filter allowed Post Statues.
		add_filter( 'cmb2_attached_posts_per_page_filter', array( $this, 'cmb_filter_post_perpage_attached_posts' ), 10, 2 );

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
	public function select2_get_options_array_tax( $taxonomies, $query_args = '' ) {
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
		$new_post_meta_data = ( isset( $_POST[ $postmeta ] ) ) ? $_POST[ $postmeta ] : '';
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
		} else {
			// If there's no postmeta, then set it 'none' if it exists
			// (this only impacts cliches and tropes at the moment)
			if ( false !== $none_term ) {
				wp_set_object_terms( $post_id, $none_term->term_id, $taxonomy );
				update_post_meta( $post_id, $postmeta, array( $none_term->term_id ) );
			} else {
				wp_set_object_terms( $post_id, '', $taxonomy );
				update_post_meta( $post_id, $postmeta, '' );
			}
		}
	}

	/**
	 * Display metabox for only certain user roles.
	 * @author @Mte90
	 * @link   https://github.com/CMB2/CMB2/wiki/Adding-your-own-show_on-filters
	 *
	 * @param  bool  $display  Whether metabox should be displayed or not.
	 * @param  array $meta_box Metabox config array
	 * @return bool            (Modified) Whether metabox should be displayed or not.
	 */
	public function cmb_show_meta_to_chosen_roles( $display, $meta_box ) {
		if ( ! isset( $meta_box['show_on']['key'], $meta_box['show_on']['value'] ) ) {
			return $display;
		}

		if ( 'role' !== $meta_box['show_on']['key'] ) {
			return $display;
		}

		$user = wp_get_current_user();

		// No user found, return
		if ( empty( $user ) ) {
			return false;
		}

		$roles = (array) $meta_box['show_on']['value'];

		foreach ( $user->roles as $role ) {
			// Does user have role.. check if array
			if ( is_array( $roles ) && in_array( $role, $roles ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Filter to edit post title.
	 *
	 * For CMB2-Attached-Posts
	 */
	public function cmb_filter_title_attached_posts( $post_id, $post_title ) {
		$additional = array();
		$status     = get_post_status( $post_id );
		$is_queer   = get_post_meta( $post_id, 'lezactors_queer', true );

		if ( false !== $status && 'publish' !== $status ) {
			$additional[] = 'draft';
		}

		if ( ! empty( $is_queer ) && $is_queer ) {
			$additional[] = 'queer';
		}

		if ( ! empty( $additional ) ) {
			$post_title .= ' (' . implode( ', ', $additional ) . ')';
		}

		return $post_title;
	}

	/**
	 * Filter to change allowed post statues.
	 *
	 * For CMB2-Attached-Posts
	 */
	public function cmb_filter_post_status_attached_posts( $post_status ) {
		$post_status = array( 'publish', 'pending', 'draft', 'future', 'private', 'inherit' );

		return $post_status;
	}

	/**
	 * Filter to search over 100 pages (we have a lot!)
	 *
	 * For CMB2-Attached-Posts
	 */
	public function cmb_filter_post_perpage_attached_posts( $number ) {
		return 100;
	}

}

new LWTV_CMB2_Addons();
