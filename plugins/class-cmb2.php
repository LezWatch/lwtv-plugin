<?php
/*
 * Library: CMB2 Add Ons
 * Description: Addons for CMB2 that make life worth living
 * Version: 2.0.3
 */

class LWTV_Plugins_CMB2 {

	public $version; // Plugin version

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->version = '2.0.3';
		add_action( 'admin_init', array( $this, 'admin_init' ) );

		/* Support for Symbolicons */
		require_once __DIR__ . '/cmb2/symbolicons.php';

		/* Tweaks for Taxonomies */
		require_once __DIR__ . '/cmb2/taxonomies.php';

		/* ADDON: CMB2 Grid -- FORKED */
		require_once __DIR__ . '/cmb2/cmb2-grid/Cmb2GridPluginLoad.php';

		/* ADDON: Select2 -- FORKED */
		if ( ! class_exists( 'LWTV_Fork_CMB2_Field_Select2' ) ) {
			require_once __DIR__ . '/cmb2/cmb-field-select2/cmb-field-select2.php';
		}

		/* Date Year Range */
		require_once __DIR__ . '/cmb2/year-range.php';

		/* ADDON: Attached Posts -- FORKED */
		require_once __DIR__ . '/cmb2/cmb2-attached-posts/cmb2-attached-posts-field.php';
		require_once __DIR__ . '/cmb2/attached-posts.php';
	}

	/**
	 * Init
	 */
	public function admin_init() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10 );
	}

	/**
	 * CSS tweaks
	 *
	 * @access public
	 * @param mixed $hook string - The filename of the page.
	 * @return void
	 */
	public function admin_enqueue_scripts( $hook ) {
		wp_register_style( 'cmb-styles', plugins_url( 'cmb2/cmb2.css', __FILE__ ), array(), $this->version );
		$post_array = array( 'edit-tags.php', 'post.php', 'post-new.php', 'term.php', 'page-new.php', 'page.php' );
		if ( in_array( $hook, $post_array, true ) ) {
			wp_enqueue_style( 'cmb-styles' );
		}
	}
}
