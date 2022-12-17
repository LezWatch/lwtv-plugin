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

		add_action( 'enqueue_block_assets', array( $this, 'block_assets' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'block_editor_assets' ) );

		// Hook server side rendering into render callback
		register_block_type(
			'lez-library/private-note',
			array(
				'render_callback' => array( $this, 'render_private_blocks' ),
			)
		);
	}

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
				'affiliate_default_image_url' => plugins_url( 'affiliate-grid.png', __FILE__ ),
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

	public function render_private_blocks( $attributes, $content ) {

		if ( is_admin() ) {
			return $content;
		}

		$dom = new \DomDocument();
		$dom->loadXML( $content );

		$finder             = new \DomXPath( $dom );
		$secure_class       = 'wp-block-lez-library-private-note';
		$secure_content     = $finder->query( "//div[contains(@class, '$secure_class')]" );
		$secure_content_dom = new \DOMDocument();

		foreach ( $secure_content as $node ) {
			$secure_content_dom->appendChild( $secure_content_dom->importNode( $node, true ) );
		}

		$secure_content = trim( $secure_content_dom->saveHTML() );

		// Only people who can edit published posts (author, editor, admin) can see this.
		if ( is_user_logged_in() && current_user_can( 'edit_published_posts' ) ) {
			return $secure_content;
		}
	}

}

new LWTV_Blocks();

// Call Serverside Renders.
require_once 'serverside.php';
