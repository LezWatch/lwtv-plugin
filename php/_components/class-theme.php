<?php
/*
 * Themes
 */
namespace LWTV\_Components;

use LWTV\Theme\Actor_Birthday;
use LWTV\Theme\Content_Warning;
use LWTV\Theme\Data_Actor;
use LWTV\Theme\Data_Author;
use LWTV\Theme\Data_Character;
use LWTV\Theme\List_Characters;
use LWTV\Theme\Show_Stars;
use LWTV\Theme\Stats_Symbolicon;
use LWTV\Theme\Taxonomy_Archive_Title;
use LWTV\Theme\TVMaze;
use LWTV\Theme\Ways_To_Watch;

class Theme implements Component, Templater {

	/*
	 * Init
	 *
	 * Call the sub plugins
	 */
	public function init(): void {
		// Null
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
			'get_stats_symbolicon'      => array( $this, 'get_stats_symbolicon' ),
			'get_list_characters'       => array( $this, 'get_list_characters' ),
			'get_author_social'         => array( $this, 'get_author_social' ),
			'get_author_favorite_shows' => array( $this, 'get_author_favorite_shows' ),
			'get_tax_archive_title'     => array( $this, 'get_tax_archive_title' ),
			'get_show_stars'            => array( $this, 'get_show_stars' ),
			'get_show_content_warning'  => array( $this, 'get_show_content_warning' ),
			'get_character_data'        => array( $this, 'get_character_data' ),
			'get_actor_data'            => array( $this, 'get_actor_data' ),
			'is_actor_birthday'         => array( $this, 'is_actor_birthday' ),
			'get_ways_to_watch'         => array( $this, 'get_ways_to_watch' ),
			'get_tvmaze_episodes'       => array( $this, 'get_tvmaze_episodes' ),
		);
	}

	/**
	 * Symbolicon for Stats pages
	 */
	public function get_stats_symbolicon( $stat_type ) {
		return ( new Stats_Symbolicon() )->make( $stat_type );
	}

	/**
	 * List all characters
	 */
	public function get_list_characters( $post_id, $format ) {
		return ( new List_Characters() )->make( $post_id, $format );
	}

	/**
	 * Generate author social media details
	 */
	public function get_author_social( $author ) {
		return ( new Data_Author() )->social( $author );
	}

	/**
	 * Generate author favorite shows.
	 */
	public function get_author_favorite_shows( $author ) {
		return ( new Data_Author() )->favorite_shows( $author );
	}

	/**
	 * Take the data from the taxonomy to determine a dynamic title.
	 */
	public function get_tax_archive_title( $location, $post_type, $taxonomy ) {
		return ( new Taxonomy_Archive_Title() )->make( $location, $post_type, $taxonomy );
	}

	/**
	 * Get the stars for a show
	 */
	public function get_show_stars( $show_id ) {
		return ( new Show_Stars() )->make( $show_id );
	}

	/**
	 * Generate content warning
	 */
	public function get_show_content_warning( $show_id ) {
		return ( new Content_Warning() )->make( $show_id );
	}

	/**
	 * Generate character data
	 */
	public function get_character_data( $character_id, $data ) {
		return ( new Data_Character() )->make( $character_id, $data );
	}

	/**
	 * Generate actor data
	 */
	public function get_actor_data( $the_id, $data ) {
		return ( new Data_Actor() )->make( $the_id, $data );
	}

	/**
	 * Is actor birthday
	 *
	 * @param int $the_id Actor ID
	 *
	 * @return bool
	 */
	public function is_actor_birthday( $the_id ) {
		return ( new Actor_Birthday() )->make( $the_id );
	}

	/**
	 * Displays ways to watch this show
	 *
	 * @param  string $show_id
	 * @return string
	 */
	public function get_ways_to_watch( $show_id ) {
		return ( new Ways_To_Watch() )->ways_to_watch( $show_id );
	}

	/**
	 * Get TV Maze episode data
	 *
	 * @param  int $show_id
	 * @return string
	 */
	public function get_tvmaze_episodes( $show_id ) {
		return ( new TVMaze() )->episodes( $show_id );
	}
}
