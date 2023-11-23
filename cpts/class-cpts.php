<?php
/**
 * Name: Custom Post Types
 *
 */

// Include the base files
require_once 'actors/_main.php';
require_once 'characters/_main.php';
require_once 'shows/_main.php';

new LWTV_CPTs_Post_Meta();
new LWTV_CPTs_Related_Posts();

/**
 * Controls for all CPTs.
 */
class LWTV_CPTs {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'bulk_actions-edit-member', array( $this, 'remove_member_bulk_actions' ), 10, 2 );
		add_filter( 'post_row_actions', array( $this, 'remove_quick_edit' ), 10, 2 );
	}

	/**
	 * Remove Quick Edit if it's one of our CPTs.
	 *
	 * @param array  $actions The potential actions on the page.
	 * @param object $post    Post Object
	 *
	 * @return array $actions Modified actions.
	 */
	public function remove_quick_edit( $actions, $post ) {
		$cpts = array( 'post_type_actors', 'post_type_characters', 'post_type_shows' );
		if ( in_array( get_post_type( $post->ID ), $cpts, true ) ) {
			unset( $actions['inline hide-if-no-js'] );
		}
		return $actions;
	}

	/**
	 * Remove Quick Edit if it's one of our CPTs.
	 *
	 * @param array  $actions The potential actions on the page.
	 * @param object $post    Post Object
	 *
	 * @return array $actions Modified actions.
	 */
	public function remove_member_bulk_actions( $actions, $post ) {
		$cpts = array( 'post_type_actors', 'post_type_characters', 'post_type_shows' );
		if ( in_array( get_post_type( $post->ID ), $cpts, true ) ) {
			unset( $actions['edit'] );
		}
		return $actions;
	}
}
