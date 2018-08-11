<?php
/**
 * Name: Gutenberg Blocks
 * Description: Blocks for Gutenberg
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

class LWTV_Gutenblocks {

	protected static $directory;

	public function __construct() {
		self::$directory = dirname( __FILE__ );
		add_action( 'init', array( $this, 'screener' ) );
	}

	public function screener() {
		$index_js = 'screener/dist/blocks.build.js';
		wp_register_script(
			'screener-block-editor',
			plugins_url( $index_js, __FILE__ ),
			array( 'wp-editor', 'wp-blocks', 'wp-i18n', 'wp-element' ),
			filemtime( self::$directory . '/' . $index_js ),
			false
		);

		$editor_css = 'screener/dist/blocks.editor.build.css';
		wp_register_style(
			'screener-block-editor',
			plugins_url( $editor_css, __FILE__ ),
			array( 'wp-editor', 'wp-blocks' ),
			filemtime( self::$directory . '/' . $editor_css )
		);

		$style_css = 'screener/dist/blocks.style.build.css';
		wp_register_style(
			'screener-block',
			plugins_url( $style_css, __FILE__ ),
			array( 'wp-editor', 'wp-blocks' ),
			filemtime( self::$directory . '/' . $style_css )
		);

		register_block_type( 'lwtv/screener', array(
			'editor_script' => 'screener-block-editor',
			'editor_style'  => 'screener-block-editor',
			'style'         => 'screener-block',
		) );
	}
}

new LWTV_Gutenblocks();
