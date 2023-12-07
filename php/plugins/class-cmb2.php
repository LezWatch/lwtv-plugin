<?php
/*
 * Library: CMB2 Add Ons
 * Description: Addons for CMB2 that make life worth living
 * Version: 2.0.3
 */

namespace LWTV\Plugins;

use LWTV\Plugins\CMB2\Taxonomies;

class CMB2 {
	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'admin_init' ) );

		/**
		 * Forked Plugins
		 */
		// CMB2 Grid
		require_once LWTV_PLUGIN_PATH . '/plugins/cmb2-grid/Cmb2GridPluginLoad.php';
		// Select2
		require_once LWTV_PLUGIN_PATH . '/plugins/cmb-field-select2/cmb-field-select2.php';
		// Attached Posts
		require_once LWTV_PLUGIN_PATH . '/plugins/cmb2-attached-posts/cmb2-attached-posts-field.php';

		/* Support for Symbolicons */
		require_once __DIR__ . '/cmb2/class-symbolicons.php';

		/* Date Year Range */
		require_once __DIR__ . '/cmb2/class-year-range.php';

		/** Attached Posts */
		require_once __DIR__ . '/cmb2/class-attached-posts.php';
	}

	/**
	 * Init
	 */
	public function admin_init() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10 );
	}

	/**
	 * Get a list of terms
	 */
	public function get_cmb2_terms_list( $taxonomies, $query_args = '' ) {
		( new Taxonomies() )->get_cmb2_terms_list( $taxonomies, $query_args );
	}

	/**
	 * Get a list of terms
	 */
	public function get_select2_defaults( $postmeta, $taxonomy, $post_id = 0, $none = false ) {
		( new Taxonomies() )->get_select2_defaults( $postmeta, $taxonomy, $post_id, $none );
	}

	/**
	 * CSS tweaks
	 *
	 * @access public
	 * @param mixed $hook string - The filename of the page.
	 * @return void
	 */
	public function admin_enqueue_scripts( $hook ) {
		wp_register_style( 'cmb-styles', plugins_url( LWTV_PLUGIN_PATH . '/assets/css/cmb2.css', __FILE__ ), array(), LWTV_PLUGIN_VERSION );
		$post_array = array( 'edit-tags.php', 'post.php', 'post-new.php', 'term.php', 'page-new.php', 'page.php' );
		if ( in_array( $hook, $post_array, true ) ) {
			wp_enqueue_style( 'cmb-styles' );
		}
	}
}
