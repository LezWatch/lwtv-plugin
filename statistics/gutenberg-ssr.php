<?php
/*
Description: Statistics Gutenberg - server side rendering.
Version: 1.0
Author: Mika Epstein

In a perfect world this would be 100% gutenized. It's not.
*/

class LWTV_Stats_SSR {

	/*
	 * Display statistics
	 *
	 * Usage:
	 *		[statistics page=[main|death]]
	 *
	 * @since 1.0
	 */
	public static function statistics( $atts ) {
		$attributes = shortcode_atts(
			array(
				'page' => 'main',
			),
			$atts
		);

		$valid_pages = array( 'main', 'actors', 'characters', 'death', 'formats', 'nations', 'shows', 'stations' );
		$the_page    = ( ! in_array( sanitize_text_field( $attributes['page'] ), $valid_pages, true ) ) ? 'main' : sanitize_text_field( $attributes['page'] );

		$output = self::get_include_contents( dirname( __FILE__ ) . '/templates/' . $the_page . '.php' );

		return '<div class="lwtv-stats">' . $output . '</div>';
	}

	public static function get_include_contents( $filename ) {
		if ( is_file( $filename ) ) {
			ob_start();
			include $filename;
			return ob_get_clean();
		}
		return false;
	}

}

new LWTV_Stats_SSR();
