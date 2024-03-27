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
use LWTV\CPTs\TVMaze;
use LWTV\CPTs\Shows\Shows_Like_This;

/**
 * Controls for all CPTs.
 */
class CPTs implements Component, Templater {

	/**
	 * Our Post Types
	 */
	const POST_TYPES = array( 'post_type_actors', 'post_type_characters', 'post_type_shows' );

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
		new TVMaze();
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
			'get_related_archive_header' => array( $this, 'get_related_archive_header' ),
			'has_cpt_related_posts'      => array( $this, 'has_cpt_related_posts' ),
			'get_cpt_related_posts'      => array( $this, 'get_cpt_related_posts' ),
			'get_shows_like_this_show'   => array( $this, 'get_shows_like_this_show' ),
			'hide_actor_data'            => array( $this, 'hide_actor_data' ),
			'the_actor_privacy_warning'  => array( $this, 'the_actor_privacy_warning' ),
		);
	}

	/**
	 * Calculate Actor Data
	 *
	 * @param  int $post_id
	 * @return void
	 */
	public function calculate_actor_data( $post_id ): void {
		( new Actors() )->do_the_math( $post_id );
	}

	/**
	 * Calculate Character Data
	 *
	 * @param  int $post_id
	 * @return void
	 */
	public function calculate_character_data( $post_id ): void {
		( new Characters() )->do_the_math( $post_id );
	}

	/**
	 * Calculate Show Data
	 *
	 * @param  int $post_id
	 * @return void
	 */
	public function calculate_show_data( $post_id ): void {
		( new Shows() )->do_the_math( $post_id );
	}

	/**
	 * Related Content Archive
	 *
	 * @param  int $tag_id
	 * @return string
	 */
	public function get_related_archive_header( $tag_id ): string {
		return ( new Related_Posts() )->related_archive_header( $tag_id );
	}

	/**
	 * Does a CPT have related posts?
	 *
	 * @param  mixed $slug
	 * @return bool
	 */
	public function has_cpt_related_posts( $slug ): bool {
		if ( is_numeric( $slug ) ) {
			$slug = get_post_field( 'post_name', get_post( $slug ) );
		}

		return ( new Related_Posts() )->are_there_posts( $slug );
	}

	/**
	 * Get the related posts
	 *
	 * @param  mixed $slug
	 * @return void
	 */
	public function get_cpt_related_posts( $slug ): string {
		if ( is_numeric( $slug ) ) {
			$slug = get_post_field( 'post_name', get_post( $slug ) );
		}

		return ( new Related_Posts() )->related_posts( $slug );
	}

	/**
	 * Get Shows Like this show
	 *
	 * @param  int $post_id
	 * @return void
	 */
	public function get_shows_like_this_show( $post_id ): mixed {
		return ( new Shows_Like_This() )->make( $post_id );
	}

	/**
	 * Hide actor data
	 *
	 * @param  int    $post_id
	 * @param  string $type     - type of data to hide
	 * @return bool
	 */
	public function hide_actor_data( $post_id, $type ): bool {
		return ( new Actors() )->hide_data( $post_id, $type );
	}

	/**
	 * The Actor Privacy Warning
	 *
	 * @param  int $post_id
	 * @return void
	 */
	public function the_actor_privacy_warning( $post_id ): void {
		( new Actors() )->privacy_warning( $post_id );
	}

	/**
	 * Remove Quick Edit if it's one of our CPTs.
	 *
	 * @param array  $actions The potential actions on the page.
	 * @param object $post    Post Object
	 *
	 * @return array $actions Modified actions.
	 */
	public function remove_quick_edit( $actions, $post ): array {
		if ( in_array( get_post_type( $post->ID ), self::POST_TYPES, true ) ) {
			unset( $actions['inline hide-if-no-js'] );
		}
		return $actions;
	}

	/**
	 * Remove Bulk Actions if it's one of our CPTs.
	 *
	 * @param array  $actions The potential actions on the page.
	 * @param object $post    Post Object
	 *
	 * @return array $actions Modified actions.
	 */
	public function remove_member_bulk_actions( $actions, $post ): array {
		if ( in_array( get_post_type( $post->ID ), self::POST_TYPES, true ) ) {
			unset( $actions['edit'] );
		}
		return $actions;
	}
}
