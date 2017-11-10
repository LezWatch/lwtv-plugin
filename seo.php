<?php
/*
Description: SEO Customizations

Tweaks for SEO that don't work out of the box.
See also /plugins/yoast-seo.php

Version: 1.0
*/

if ( ! defined('WPINC' ) ) die;

/**
 * class LWTV_SEO
 */
class LWTV_SEO {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action('wp_head', array( $this, 'opengraph_image' ), 5);
	}

	/*
	 * Setting OpenGraph image for posts without featured images
	 *
	 * @since 1.0
	 */
	public function opengraph_image() {

		// If there is no thumbnail, bail early
		if ( !has_post_thumbnail() ) return;

		$image = plugins_url( 'assets/images/toaster.png', __FILE__ );
		echo '<meta property="og:image" content="' . $image . '" /><meta name="twitter:image" content="' . $image . '" />';
	}
}

new LWTV_SEO();