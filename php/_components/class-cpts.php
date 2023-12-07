<?php
/**
 * Name: Custom Post Types
 *
 */

namespace LWTV\_Components;

use LWTV\CPTs\Post_Meta;
use LWTV\CPTs\Related_Posts;
use LWTV\CPTs\Actors;
use LWTV\CPTs\Characters;
use LWTV\CPTs\Shows;
use LWTV\CPTs\Shows\Shows_Like_This;

/**
 * Controls for all CPTs.
 */
class CPTs implements Component, Templater {

	/**
	 * Constructor
	 */
	public function init() {
		add_filter( 'bulk_actions-edit-member', array( $this, 'remove_member_bulk_actions' ), 10, 2 );
		add_filter( 'post_row_actions', array( $this, 'remove_quick_edit' ), 10, 2 );

		// Init what we'll need.
		new Post_Meta();
		new Related_Posts();
		new Actors();
		new Characters();
		new Shows();
	}

	/**
	 * Gets tags to expose as methods accessible through `lwtv_plugin()`.
	 *
	 * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
	 *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
	 *               adding support for further arguments in the future.
	 */
	public function get_template_tags(): array {
		return array(
			'calculate_actor_data'       => array( $this, 'calculate_actor_data' ),
			'calculate_character_data'   => array( $this, 'calculate_character_data' ),
			'calculate_show_data'        => array( $this, 'calculate_show_data' ),
			'get_chars_for_show'         => array( $this, 'get_chars_for_show' ),
			'get_characters_list'        => array( $this, 'get_characters_list' ),
			'get_related_archive_header' => array( $this, 'get_related_archive_header' ),
			'has_cpt_related_posts'      => array( $this, 'has_cpt_related_posts' ),
			'get_cpt_related_posts'      => array( $this, 'get_cpt_related_posts' ),
			'get_shows_like_this_show'   => array( $this, 'get_shows_like_this_show' ),
		);
	}

	/**
	 * Calculate Actor Data
	 *
	 * @param  int $post_id
	 * @return void
	 */
	public function calculate_actor_data( $post_id ) {
		( new Actors() )->do_the_math( $post_id );
	}

	/**
	 * Calculate Character Data
	 *
	 * @param  int $post_id
	 * @return void
	 */
	public function calculate_character_data( $post_id ) {
		( new Characters() )->do_the_math( $post_id );
	}

	/**
	 * Calculate Show Data
	 *
	 * @param  int $post_id
	 * @return void
	 */
	public function calculate_show_data( $post_id ) {
		( new Shows() )->do_the_math( $post_id );
	}

	/**
	 * Get characters list for Show
	 *
	 * @param  int    $show_id
	 * @param  mixed  $havecharcount
	 * @param  string $role
	 * @return void
	 */
	public function get_chars_for_show( $show_id, $havecharcount, $role = 'regular' ) {
		return ( new Characters() )->get_chars_for_show( $show_id, $havecharcount, $role );
	}

	/**
	 * Get list of characters
	 *
	 * @param  int    $show_id
	 * @param  string $output
	 * @return void
	 */
	public function get_characters_list( $show_id, $output = 'query' ) {
		return ( new Characters() )->list_characters( $show_id, $output );
	}

	/**
	 * Related Content Archive
	 *
	 * @param  int $tag_id
	 * @return string
	 */
	public function get_related_archive_header( $tag_id ) {
		return ( new Related_Posts() )->related_archive_header( $tag_id );
	}

	/**
	 * Does a CPT have related posts?
	 *
	 * @param  string $slug
	 * @return bool
	 */
	public function has_cpt_related_posts( $slug ) {
		return ( new Related_Posts() )->are_there_posts( $slug );
	}

	/**
	 * Get the related posts
	 *
	 * @param  string $slug
	 * @return void
	 */
	public function get_cpt_related_posts( $slug ) {
		return ( new Related_Posts() )->related_posts( $slug );
	}

	/**
	 * Get Shows Like this show
	 *
	 * @param  [type] $post_id
	 * @return void
	 */
	public function get_shows_like_this_show( $post_id ) {
		return ( new Shows_Like_This() )->make( $post_id );
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
