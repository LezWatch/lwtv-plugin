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

	/**
	 * Register any needed hooks/filters.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'action_register_blocks' ) );
		add_filter( 'block_categories_all', array( $this, 'action_add_block_category' ), 10, 2 );

		// Enqueues.
		add_action( 'enqueue_block_assets', array( $this, 'block_assets' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'block_editor_assets' ) );
	}

	/**
	 * Get the absolute path to the asset file.
	 *
	 * @param string $path_relative Path relative to this plugin directory root.
	 *
	 * @return string Absolute path to the file.
	 */
	public function path_to( $path_relative ) {
		self::$directory = dirname( __FILE__ );
		return sprintf( '%s/%s', self::$directory, ltrim( $path_relative, '/\\' ) );
	}

	/**
	 * Register all blocks living in the "/blocks/" folder in the theme.
	 */
	public function action_register_blocks() {
		$folders = glob( $this->path_to( '/build/*' ) );

		foreach ( $folders as $folder ) {
			$block_json_file = sprintf( '%s/block.json', $folder );

			if ( file_exists( $block_json_file ) ) {
				$args = array();
				$type = register_block_type( $block_json_file, $args );
			}
		}
	}

	/**
	 * Adding a new (custom) block category.
	 *
	 * @param   array                   $block_categories       Array of categories for block types.
	 * @param   WP_Block_Editor_Context $block_editor_context   The current block editor context.
	 */
	public function action_add_block_category( $block_categories, $block_editor_context ) {
		return array_merge(
			$block_categories,
			array(
				array(
					'slug'  => 'lezwatch',
					'title' => 'LezWatch.TV Library',
				),
			)
		);
	}

	/**
	 * Block FRONT END Assets
	 */
	public function block_assets() {

		$folders = glob( $this->path_to( '/build/*' ) );

		foreach ( $folders as $folder ) {
			$local_css_file = sprintf( '%s/style-block.css', $folder );

			if ( file_exists( $local_css_file ) ) {
				$css_folder = basename( dirname( $local_css_file ) );

				wp_enqueue_style(
					'lwtv-gutenberg-style-' . $css_folder, // Handle.
					plugins_url( 'lwtv-plugin/blocks/build/' . $css_folder . '/style-block.css' ),
					array( 'wp-editor' ),
					filemtime( $local_css_file )
				);
			}
		}
	}

	/**
	 * Block Editor Assets
	 */
	public function block_editor_assets() {

		$folders = glob( $this->path_to( '/build/*' ) );

		foreach ( $folders as $folder ) {
			$local_css_file = sprintf( '%s/block.css', $folder );

			if ( file_exists( $local_css_file ) ) {
				$css_folder = basename( dirname( $local_css_file ) );

				wp_enqueue_style(
					'lwtv-gutenberg-style-' . $css_folder, // Handle.
					plugins_url( 'lwtv-plugin/blocks/build/' . $css_folder . '/block.css' ),
					array( 'wp-editor' ),
					filemtime( $local_css_file )
				);
			}
		}
	}

}

new LWTV_Blocks();

// Call Serverside Renders.
require_once 'serverside.php';
