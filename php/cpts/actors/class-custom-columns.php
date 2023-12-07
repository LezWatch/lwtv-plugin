<?php
/*
 * Name: Actor Columns
 * Desc: Custom columns on post-list pages for actors
 * Since: 3.0
 */

namespace LWTV\CPTs\Actors;

class Custom_Columns {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}

	/**
	 * Admin Init
	 */
	public function admin_init() {
		add_filter( 'manage_post_type_actors_posts_columns', array( $this, 'manage_posts_columns' ) );
		add_action( 'manage_post_type_actors_posts_custom_column', array( $this, 'manage_posts_custom_column' ), 10, 2 );
		add_filter( 'manage_edit-post_type_actors_sortable_columns', array( $this, 'manage_edit_sortable_columns' ) );

		add_action( 'pre_get_posts', array( $this, 'columns_sortability_simple' ) );
	}

	/*
	 * Create Custom Columns
	 * Used by quick edit, etc
	 */
	public function manage_posts_columns( $columns ) {
		$columns['actors-queer']     = '<span class="dashicons dashicons-smiley"><span class="screen-reader-text">Queer IRL</span></span>';
		$columns['actors-charcount'] = '<span class="dashicons dashicons-nametag"><span class="screen-reader-text">Characters</span></span>';
		return $columns;
	}

	/*
	 * Add Custom Column Content
	 */
	public function manage_posts_custom_column( $column, $post_id ) {
		switch ( $column ) {
			case 'actors-queer':
				$is_queer = get_post_meta( $post_id, 'lezactors_queer', true );
				$queer    = ( $is_queer ) ? 'Y' : 'N';
				echo esc_html( $queer );
				break;
			case 'actors-charcount':
				$charcount = get_post_meta( $post_id, 'lezactors_char_count', true );
				echo (int) $charcount;
				break;
		}
	}

	/*
	 * Make Custom Columns Sortable
	 */
	public function manage_edit_sortable_columns( $columns ) {
		$columns['actors-charcount'] = 'characters';  // Allow sort by queers
		return $columns;
	}

	/*
	 * Create Simple Columns Sortability
	 *
	 * Worth It, Char Count
	 */
	public function columns_sortability_simple( $query ) {
		if ( ! is_admin() ) {
			return;
		}

		if ( $query->is_main_query() && isset( $orderby ) && $orderby === $query->get( 'orderby' ) ) {
			switch ( $orderby ) {
				case 'characters':
					$query->set( 'meta_key', 'lezactors_char_count' );
					$query->set( 'orderby', 'meta_value_num' );
					break;
			}
		}
	}
}
