<?php
/**
 * Name: Blocks
 * Description: Blocks for LezWatchTV
 *
 * Using https://developer.wordpress.org/block-editor/handbook/tutorials/create-block/
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

class LWTV_Blocks {

	protected static $directory;

	public function __construct() {
		self::$directory = dirname( __FILE__ );

		// Add a block category
		add_filter(
			'block_categories_all',
			function( $categories, $post ) {
				return array_merge(
					$categories,
					array(
						array(
							'slug'  => 'lezwatch',
							'title' => 'LezWatch Library',
						),
					)
				);
			},
			10,
			2
		);

		// Enqueues.
		add_action( 'enqueue_block_assets', array( $this, 'block_assets' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'block_editor_assets' ) );
	}

	/**
	 * Block FRONT END Assets
	 */
	public function block_assets() {
		// Styles.
		$build_css = 'build/style-index.css';
		wp_enqueue_style(
			'lwtv-plugin-gutenberg-style', // Handle.
			plugins_url( $build_css, __FILE__ ),
			array( 'wp-editor' ),
			filemtime( self::$directory . '/' . $build_css )
		);
	}

	/**
	 * Block Editor Assets
	 */
	public function block_editor_assets() {
		// Scripts.
		$build_js = 'build/index.js';
		wp_enqueue_script(
			'lwtv-plugin-gutenberg-blocks', // Handle.
			plugins_url( $build_js, __FILE__ ),
			array( 'wp-editor', 'wp-i18n', 'wp-element' ),
			filemtime( self::$directory . '/' . $build_js ),
			true
		);

		wp_localize_script(
			'lwtv-plugin-gutenberg-blocks',
			'js_data',
			array(
				'affiliate_default_image_url' => plugins_url( 'assets/images/affiliate-grid.png', dirname( __FILE__ ) ),
			)
		);

		// Styles.
		$editor_css = 'build/index.css';
		wp_enqueue_style(
			'lwtv-plugin-gutenberg-editor', // Handle.
			plugins_url( $editor_css, __FILE__ ),
			array( 'wp-editor' ),
			filemtime( self::$directory . '/' . $editor_css )
		);
	}

}

new LWTV_Blocks();

// Call Serverside Renders.
require_once 'serverside.php';
