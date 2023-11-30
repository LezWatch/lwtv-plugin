<?php
/*
Library: FacetWP Add Ons
Description: Addons for FacetWP that make life worth living
Version: 1.1.0
*/

/**
 * class LWTV_Plugins_FacetWP
 *
 * Customize FacetWP
 *
 * @since 1.0
 */
class LWTV_Plugins_FacetWP {

	/**
	 * Constructor
	 */
	public function __construct() {

		// Include extra Plugins
		require_once __DIR__ . '/facetwp/facetwp-cmb2/cmb2.php';
		require_once __DIR__ . '/facetwp/indexing.php';
		require_once __DIR__ . '/facetwp/pagination.php';

		// Reset Shortcode
		add_shortcode( 'facetwp-reset', array( $this, 'reset_shortcode' ) );
	}

	/*
	 * Reset Shortcode
	 *
	 * Echo reset button
	 *
	 * @since 1.1.0
	 */
	// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
	public function reset_shortcode( $atts ) {
		$reset = '<center><button class="facetwp-reset" onclick="FWP.reset()">Reset Filters</button></center>';
		return $reset;
	}
}
