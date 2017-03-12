<?php
/*
Description: SEO Customizations

Tweaks for SEO that don't work out of the box.
See also /plugins/yoast-seo.php

Version: 1.0
Author: Mika Epstein
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
	 * Setting OpenGraph image for taxonomies
	 * This uses the default image set for the taxonomy via that symbolicons stuff
	 *
	 * @since 1.0
	 */
	public function opengraph_image() {

		// If it's not a taxonomy, die.
		if ( !is_tax() )  return;

		$term_id = get_queried_object_id();
		$icon = get_term_meta( $term_id, 'lwtv_termsmeta_icon', true );
		$iconpath = LWTV_SYMBOLICONS_PATH.'/png/'.$icon.'.png';
		if ( empty($icon) || !file_exists( $iconpath ) )  $icon = 'square';
		$image = LWTV_SYMBOLICONS_URL.'/png/'.$icon.'.png';
		echo '<meta property="og:image" content="'.$image.'" /><meta name="twitter:image" content="'.$image.'" />';
	}
}
new LWTV_SEO();




