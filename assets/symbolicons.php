<?php
/**
 * Symbolicons
 *
 * Shows the symbolicons settings page, based on the contents on
 * /mu-plugins/symbolicons
 *
 * @version 2.0
 * @package library
 */

// if this file is called directly abort
if ( ! defined( 'WPINC' ) ) {
	die;
}

$upload_dir = wp_upload_dir();

if ( ! defined( 'LWTV_SYMBOLICONS_PATH' ) ) {
	define( 'LWTV_SYMBOLICONS_PATH', $upload_dir['basedir'] . '/lezpress-icons/symbolicons/' );
}

if ( ! defined( 'LWTV_SYMBOLICONS_URL' ) ) {
	define( 'LWTV_SYMBOLICONS_URL', $upload_dir['baseurl'] . '/lezpress-icons/symbolicons/' );
}

class LWTV_SymboliconsSettings {

	protected static $version;

	/*
	 * Construct
	 *
	 * Actions to happen immediately
	 */
	public function __construct() {
		add_action( 'init', array( &$this, 'init' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ) );
		self::$version = '2.0';
	}

	/*
	 * Init
	 *
	 * Actions to happen on WP init
	 * - add settings page
	 * - establish shortcode
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_shortcode( 'symbolicon', array( $this, 'shortcode' ) );
	}

	/*
	 * admin_enqueue_scripts
	 */
	public function admin_enqueue_scripts() {
		wp_register_style( 'symbolicons-admin', plugins_url( 'assets/css/symbolicons-admin.css', dirname( __FILE__ ) ), array(), self::$version );
	}

	/*
	 * Shortcode
	 *
	 * Generate the Symbolicon via shortcode
	 *
	 * @param array $atts Attributes for the shortcode
	 *   - file: Filename
	 *   - title: Title to use (for A11y)
	 *   - url: URL to link to (optional)
	 * @return SVG icon of awesomeness
	 */
	public function shortcode( $atts ) {
		$svg = shortcode_atts(
			array(
				'file'  => '',
				'title' => '',
				'url'   => '',
			),
			$atts
		);

		// Default to the square if nothing is there
		if ( ! file_exists( LWTV_SYMBOLICONS_PATH . $svg['file'] . '.svg' ) ) {
			$svg['file'] = 'square';
		}

		$the_icon = '<span class="symbolicon" role="img" aria-label="' . sanitize_text_field( $svg['title'] ) . '" title="' . sanitize_text_field( $svg['title'] ) . '" class="svg-shortcode ' . sanitize_text_field( $svg['title'] ) . '">' . file_get_contents( LWTV_SYMBOLICONS_PATH . $svg['file'] . '.svg' ) . '</span>';

		if ( ! empty( $svg['url'] ) ) {
			$iconpath = '<a href=' . esc_url( $svg['url'] ) . '> ' . $the_icon . ' </a>';
		} else {
			$iconpath = $the_icon;
		}

		return $iconpath;
	}

	/*
	 * Settings
	 *
	 * Create our settings page
	 */
	public function add_settings_page() {
		$page = add_theme_page( 'Symbolicons', 'Symbolicons', 'edit_posts', 'symbolicons', array( $this, 'settings_page' ) );
	}

	/*
	 * Settings Page Content
	 *
	 * A list of all the Symbolicons and how to use them. Kind of.
	 */
	public function settings_page() {
		?>
		<div class="wrap">

		<h2>Symbolicons</h2>

		<?php
		echo '<p>The following are all the symbolicons you have to chose from and their file names. Let this help you be more better with your iconing.</p>';

		foreach ( glob( LWTV_SYMBOLICONS_PATH . '*' ) as $filename ) {
			$name = str_replace( LWTV_SYMBOLICONS_PATH, '', $filename );
			$name = str_replace( '.svg', '', $name );
			// @codingStandardsIgnoreStart
			echo '<span class="cmb2-icon" role="img">' . file_get_contents( $filename ) . esc_html( $name ) . '</span>';
			// @codingStandardsIgnoreEnd
		}
		?>
		</div>
		<?php
	}

}
new LWTV_SymboliconsSettings();
