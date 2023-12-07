<?php
/**
 * Symbolicons
 *
 * Shows the symbolicons settings page, based on the contents in
 * /wp-content/uploads/lezpress-icons
 *
 * @version 2.0
 * @package library
 */

namespace LWTV\_Components;

class Symbolicons implements Component, Templater {

	/*
	 * Init
	 */
	public function init(): void {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_shortcode( 'symbolicon', array( $this, 'shortcode' ) );
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
			'get_icon_svg'   => array( $this, 'get_icon_svg' ),
			'get_symbolicon' => array( $this, 'get_symbolicon' ),
		);
	}

	/*
	 * admin_enqueue_scripts
	 */
	public function admin_enqueue_scripts() {
		wp_register_style( 'symbolicons-admin', plugins_url( 'assets/css/symbolicons-admin.css', dirname( __DIR__, 1 ) ), array(), LWTV_PLUGIN_VERSION );
		wp_enqueue_style( 'symbolicons-admin' );
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
		add_theme_page( 'Symbolicons', 'Symbolicons', 'edit_posts', 'symbolicons', array( $this, 'settings_page' ) );
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

	/**
	 * Get the icon as SVG.
	 *
	 * Forked from Yoast SEO
	 *
	 * @access public
	 * @param bool   $base64 (default: true) - Use SVG, true/false.
	 * @param string $icon_color - What color to use.
	 * @return string
	 */
	public function get_icon_svg( $base64 = true, $icon_color = false ) {

		$fill = ( false !== $icon_color ) ? sanitize_hex_color( $icon_color ) : '#82878c';

		$svg = '<svg width="100%" height="100%" version="1.1" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="fill:' . $fill . '"><path d="M4,10c0,-4.411 3.589,-8 8,-8c4.411,0 8,3.589 8,8v2.08c0.706,0.102 1.378,0.308 2,0.605v-2.685c0,-5.514 -4.486,-10 -10,-10c-5.514,0 -10,4.486 -10,10v2.685c0.622,-0.297 1.294,-0.503 2,-0.605Zm8,-6c-3.309,0 -6,2.691 -6,6v1.025c0.578,-0.772 1.294,-1.43 2.112,-1.929c0.412,-1.77 1.994,-3.096 3.888,-3.096c1.894,0 3.476,1.326 3.888,3.096c0.819,0.499 1.534,1.157 2.112,1.929v-1.025c0,-3.309 -2.691,-6 -6,-6Zm7,10h-1.712c-0.654,-2.307 -2.771,-4 -5.288,-4c-2.517,0 -4.634,1.693 -5.288,4h-1.712c-2.761,0 -5,2.239 -5,5c0,2.761 2.239,5 5,5h14c2.761,0 5,-2.239 5,-5c0,-2.761 -2.239,-5 -5,-5Z" transform="scale(0.666667)" fill="' . $fill . '"></path></svg>';

		if ( $base64 ) {
			return 'data:image/svg+xml;base64,' . base64_encode( $svg );
		}

		return $svg;
	}

	/**
	 * Symbolicons Output.
	 *
	 * Echos the default outputtable symbolicon, based on the SVG and FA icon passed to it.
	 *
	 * @access public
	 * @param string $svg         (default: 'square.svg') - SVG name.
	 * @param string $fontawesome (default: 'fa-square')  - Font-Awesome icon name.
	 * @param string $svg_class   (default: 'symbolicon') - SVG styling class name.
	 * @return icon
	 */
	public function get_symbolicon( $svg = 'square.svg', $fontawesome = 'fa-square', $svg_class = 'symbolicon' ) {

		$return = '<i class="fas ' . $fontawesome . ' fa-fw" aria-hidden="true"></i>';
		$square = get_template_directory() . '/images/square.svg';

		if ( ! empty( $svg ) && defined( 'LWTV_SYMBOLICONS_PATH' ) && file_exists( LWTV_SYMBOLICONS_PATH . $svg ) ) {
			$icon = LWTV_SYMBOLICONS_PATH . $svg;
		} elseif ( ! wp_style_is( 'fontawesome', 'enqueued' ) ) {
			$icon = $square;
		}

		if ( isset( $icon ) ) {
			// @codingStandardsIgnoreStart
			$return = '<span class="' . $svg_class . '" role="img">' . file_get_contents( $icon ) . '</span>';
			// @codingStandardsIgnoreEnd
		}

		return $return;
	}
}
