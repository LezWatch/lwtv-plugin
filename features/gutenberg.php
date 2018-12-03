<?php
/*
 * Gutenberg
 *
 * @since 2.4
 */

// if this file is called directly abort
if ( ! defined( 'WPINC' ) ) {
	die;
}

class LWTV_Gutenberg {

	public $gutenfree = array();

	public function __construct() {
		$this->gutenfree = array( 'post_type_characters', 'post_type_actors', 'post_type_shows' );
		add_action( 'current_screen', array( $this, 'gutenberg_removal' ) );
	}

	public function gutenberg_removal() {

		// Intercept Post Type
		$current_screen    = get_current_screen();
		$current_post_type = $current_screen->post_type;

		// If this is one of our custom post types, we don't gutenize
		if ( in_array( $current_post_type, $this->gutenfree, true ) ) {
			remove_filter( 'replace_editor', 'gutenberg_init' );
			remove_action( 'load-post.php', 'gutenberg_intercept_edit_post' );
			remove_action( 'load-post-new.php', 'gutenberg_intercept_post_new' );
			remove_action( 'admin_init', 'gutenberg_add_edit_link_filters' );
			remove_filter( 'admin_url', 'gutenberg_modify_add_new_button_url' );
			remove_action( 'admin_print_scripts-edit.php', 'gutenberg_replace_default_add_new_button' );
			remove_action( 'admin_enqueue_scripts', 'gutenberg_editor_scripts_and_styles' );
			remove_filter( 'screen_options_show_screen', '__return_false' );
		}
	}

}

new LWTV_Gutenberg();
