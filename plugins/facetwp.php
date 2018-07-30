<?php
/*
Library: FacetWP Add Ons
Description: Addons for FacetWP that make life worth living
Version: 1.1.0
*/

/**
 * class LWTV_FacetWP_Addons
 *
 * Customize FacetWP
 *
 * @since 1.0
 */
class LWTV_FacetWP_Addons {

	/**
	 * Constructor
	 */
	public function __construct() {

		// Include extra Plugins
		require_once dirname( __FILE__ ) . '/facetwp/facetwp-cmb2/cmb2.php';
		require_once dirname( __FILE__ ) . '/facetwp/lwtv.php';
		require_once dirname( __FILE__ ) . '/facetwp/facetwp-wp-cli/facetwp-wp-cli.php';

		// Filter paged output
		add_filter( 'facetwp_pager_html', array( $this, 'facetwp_pager_html' ), 10, 2 );

		// Javascript
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );

		// Reset Shortcode
		add_shortcode( 'facetwp-reset', array( $this, 'reset_shortcode' ) );
	}

	/**
	 * Enqueue Scripts
	 * @return void
	 */
	public function wp_enqueue_scripts() {
		wp_enqueue_script( 'facetwp-pagination', plugins_url( 'facetwp/facet.js', __FILE__ ), array(), '1.1', true );
	}

	/**
	 * Only show pagination if there's more than one page
	 * Credit: https://gist.github.com/mgibbs189/69176ef41fa4e26d1419
	 */
	public function facetwp_pager_html( $output, $params ) {
		$output      = '';
		$page        = (int) $params['page'];
		$total_pages = (int) $params['total_pages'];

		// Only show pagination when > 1 page
		if ( 1 < $total_pages ) {

			if ( 1 < $page ) {
				$output .= '<a class="facetwp-page" data-page="' . ( $page - 1 ) . '">&laquo; Previous</a>';
			}
			if ( 3 < $page ) {
				$output .= '<a class="facetwp-page first-page" data-page="1">1</a>';
				$output .= ' <span class="dots">…</span> ';
			}
			for ( $i = 2; $i > 0; $i-- ) {
				if ( 0 < ( $page - $i ) ) {
					$output .= '<a class="facetwp-page" data-page="' . ( $page - $i ) . '">' . ( $page - $i ) . '</a>';
				}
			}

			// Current page
			$output .= '<a class="facetwp-page active" data-page="' . $page . '">' . $page . '</a>';

			for ( $i = 1; $i <= 2; $i++ ) {
				if ( $total_pages >= ( $page + $i ) ) {
					$output .= '<a class="facetwp-page" data-page="' . ( $page + $i ) . '">' . ( $page + $i ) . '</a>';
				}
			}
			if ( $total_pages > ( $page + 2 ) ) {
				$output .= ' <span class="dots">…</span> ';
				$output .= '<a class="facetwp-page last-page" data-page="' . $total_pages . '">' . $total_pages . '</a>';
			}
			if ( $page < $total_pages ) {
				$output .= '<a class="facetwp-page" data-page="' . ( $page + 1 ) . '">Next &raquo;</a>';
			}
		}

		return $output;
	}

	/*
	 * Reset Shortcode
	 *
	 * Echo reset button
	 *
	 * @since 1.1.0
	 */
	public function reset_shortcode( $atts ) {
		$reset = '<center><button class="facetwp-reset" onclick="FWP.reset()">Reset Filters</button></center>';
		return $reset;
	}

}

if ( class_exists( 'FacetWP' ) ) {
	new LWTV_FacetWP_Addons();
}
