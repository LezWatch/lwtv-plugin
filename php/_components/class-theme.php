<?php
/*
 * Theme Components
 *
 * In general these are used in the front-end of the theme. Other functions
 * may also use them, however.
 */
namespace LWTV\_Components;

use LWTV\Theme\Actor_Age;
use LWTV\Theme\Actor_Birthday;
use LWTV\Theme\Actor_Characters;
use LWTV\Theme\Actor_Pronouns;
use LWTV\Theme\Actor_Terms;
use LWTV\Theme\Character_Relationships;
use LWTV\Theme\Content_Warning;
use LWTV\Theme\Data_Author;
use LWTV\Theme\Data_Character;
use LWTV\Theme\Do_Math;
use LWTV\Theme\Show_Characters;
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
			'get_characters_list'       => array( $this, 'get_characters_list' ),
			'get_list_characters'       => array( $this, 'get_characters_list' ), // THIS IS DEPRECATED!!
			'get_chars_for_show'        => array( $this, 'get_chars_for_show' ),
			'get_chars_relationships'   => array( $this, 'get_chars_relationships' ),
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
			'get_actor_pronouns'        => array( $this, 'get_actor_pronouns' ),
			'get_actor_gender'          => array( $this, 'get_actor_gender' ),
			'get_actor_sexuality'       => array( $this, 'get_actor_sexuality' ),
			'get_actor_characters'      => array( $this, 'get_actor_characters' ),
			'get_actor_dead'            => array( $this, 'get_actor_dead' ),
			'get_actor_age'             => array( $this, 'get_actor_age' ),
			'get_actor_birthday'        => array( $this, 'get_actor_birthday' ),
			'get_admin_tools'           => array( $this, 'get_admin_tools' ),
		);
	}

	/**
	 * Symbolicon for Stats pages
	 *
	 * @param string $stat_type
	 *
	 * @return string Custom SVG Icon
	 */
	public function get_stats_symbolicon( $stat_type ) {
		return ( new Stats_Symbolicon() )->make( $stat_type );
	}

	/**
	 * List all characters for shows
	 *
	 * @param  int    $post_id
	 * @param  string $format
	 *
	 * @return array  List of characters
	 */
	public function get_characters_list( $post_id, $format ) {
		return ( new Show_Characters() )->make( $post_id, $format );
	}

	/**
	 * Output all the characters for the show
	 *
	 * @param  int    $post_id
	 * @param  string $role
	 *
	 * @return array  List of characters
	 */
	public function get_chars_for_show( $show_id, $role ) {
		return ( new Show_Characters() )->make( $show_id, 'query', $role );
	}

	/**
	 * Output all the characters this character has relationships with.
	 *
	 * @param  int    $char_id
	 *
	 * @return array  List of characters by ID.
	 */
	public function get_chars_relationships( $char_id ): array {
		return ( new Character_Relationships() )->make( $char_id );
	}

	/**
	 * Generate author social media details
	 *
	 * @param  string $author
	 *
	 * @return string Output with Social
	 */
	public function get_author_social( $author ) {
		return ( new Data_Author() )->social( $author );
	}

	/**
	 * Generate author favorite shows.
	 *
	 * @param  string $author
	 *
	 * @return string Fav shows
	 */
	public function get_author_favorite_shows( $author ) {
		return ( new Data_Author() )->favorite_shows( $author );
	}

	/**
	 * Take the data from the taxonomy to determine a dynamic title.
	 *
	 * @param  string $location
	 * @param  string $post_type
	 * @param  string $taxonomy
	 *
	 * @return string
	 */
	public function get_tax_archive_title( $location, $post_type, $taxonomy ) {
		return ( new Taxonomy_Archive_Title() )->make( $location, $post_type, $taxonomy );
	}

	/**
	 * Get the stars for a show
	 *
	 * @param  int $show_id
	 *
	 * @return string
	 */
	public function get_show_stars( $show_id ) {
		return ( new Show_Stars() )->make( $show_id );
	}

	/**
	 * Generate content warning
	 *
	 * @param int    $show_id Actor ID
	 *
	 * @return string
	 */
	public function get_show_content_warning( $show_id ) {
		return ( new Content_Warning() )->make( $show_id );
	}

	/**
	 * Generate character data
	 *
	 * @param int    $character_id Character ID
	 * @param string $format       Type of output
	 *
	 * @return string
	 */
	public function get_character_data( $character_id, $format ) {
		return ( new Data_Character() )->make( $character_id, $format );
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
	 * Is actor birthday
	 *
	 * @param int $the_id Actor ID
	 *
	 * @return bool
	 */
	public function get_actor_birthday( $the_id ) {
		return ( new Actor_Birthday() )->get( $the_id );
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

	/**
	 * Get Actor Pronouns
	 *
	 * @param  int $actor_id
	 * @return string
	 */
	public function get_actor_pronouns( $actor_id ) {
		return ( new Actor_Pronouns() )->make( $actor_id );
	}

	/**
	 * Get Actor Sexuality
	 *
	 * @param  int $actor_id
	 * @return string
	 */
	public function get_actor_sexuality( $actor_id ) {
		return ( new Actor_Terms() )->make( $actor_id, 'sexuality' );
	}

	/**
	 * Get Actor Gender
	 *
	 * @param  int $actor_id
	 * @return string
	 */
	public function get_actor_gender( $actor_id ) {
		return ( new Actor_Terms() )->make( $actor_id, 'gender' );
	}

	/**
	 * Get Actor Characters
	 *
	 * @param  int $actor_id
	 * @return mixed
	 */
	public function get_actor_characters( $actor_id ) {
		return ( new Actor_Characters() )->make( $actor_id, 'all' );
	}

	/**
	 * Get Actor Dead Characters
	 *
	 * @param  int $actor_id
	 * @return mixed
	 */
	public function get_actor_dead( $actor_id ) {
		return ( new Actor_Characters() )->make( $actor_id, 'dead' );
	}

	/**
	 * Get Actor Age
	 *
	 * @param  int $actor_id
	 * @return string
	 */
	public function get_actor_age( $actor_id ) {
		return ( new Actor_Age() )->make( $actor_id );
	}

	/**
	 * Do the Math
	 *
	 * Creates an editor only button to refresh cache and recalculate a page.
	 *
	 * @param  int $post_id
	 * @return void
	 */
	public function get_admin_tools( $post_id ): void {
		// If you're logged in and can edit posts, you can refresh the scores.
		if ( is_user_logged_in() && current_user_can( 'edit_posts' ) ) {
			if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'lwtv-update-math' ) ) {
				( new Do_Math() )->make( $post_id );
				sleep( 5 );
				wp_safe_redirect( get_the_permalink( $post_id ) );
				exit;
			}

			?>
			<section id="editor-tools" class="widget widget_editor_tools">
				<div class="card">
					<div class="card-header"><h4>Editor Tools</h4></div>
					<form id="update_math" name="update_math" method="post">
						</br>
						<center><button type="submit" class="btn btn-primary btn-block" id="submit" name="submit">
							Refresh Data
						</button></center>
						<?php wp_nonce_field( 'lwtv-update-math' ); ?>
						</br>
					</form>
				</div>
			</section>
			<?php
		}
	}
}
