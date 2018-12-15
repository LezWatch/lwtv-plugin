<?php
/**
 * Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 *
 * @since   1.0.0
 * @package CGB
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LWTV_Gutenblocks {

	protected static $directory;

	public function __construct() {
		self::$directory = dirname( dirname( __FILE__ ) );

		add_action( 'enqueue_block_assets', array( $this, 'block_assets' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'block_editor_assets' ) );

		/**
		 * Register Block Types -- Required for ServerSideRender:
		 *  - author-box
		 */
		register_block_type(
			'lwtv/author-box',
			array(
				'attributes'      => array(
					'users'  => array(
						'type' => 'string',
					),
					'format' => array(
						'type' => 'string',
					)
				),
				'render_callback' => array( 'LWTV_Shortcodes', 'author_box' ),
			)
		);

		/**
		 * Register Block Types -- Required for ServerSideRender:
		 *  - glossary
		 */
		register_block_type(
			'lez-library/glossary',
			array(
				'attributes'      => array(
					'taxonomy' => array(
						'type' => 'string',
					),
				),
				'render_callback' => array( 'LWTV_Shortcodes', 'glossary' ),
			)
		);
	}

	public function block_assets() {
		// Styles.
		$build_css = 'dist/blocks.style.build.css';
		wp_enqueue_style(
			'lwtv-plugin-gutenberg-style', // Handle.
			plugins_url( $build_css, dirname( __FILE__ ) ),
			array( 'wp-editor' ),
			filemtime( self::$directory . '/' . $build_css )
		);
	}

	public function block_editor_assets() {
		// Scripts.
		$build_js = 'dist/blocks.build.js';
		wp_enqueue_script(
			'lwtv-plugin-gutenberg-blocks', // Handle.
			plugins_url( $build_js, dirname( __FILE__ ) ),
			array( 'wp-editor', 'wp-i18n', 'wp-element' ),
			filemtime( self::$directory . '/' . $build_js ),
			true
		);

		// Styles.
		$editor_css = 'dist/blocks.editor.build.css';
		wp_enqueue_style(
			'lwtv-plugin-gutenberg-editor', // Handle.
			plugins_url( $editor_css, dirname( __FILE__ ) ),
			array( 'wp-editor' ),
			filemtime( self::$directory . '/' . $editor_css )
		);
	}
}

new LWTV_Gutenblocks();
