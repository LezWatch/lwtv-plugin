<?php
/**
 * Related Posts
 *
 * Code for Related Posts Plugins.
 *
 * https://keesiemeijer.wordpress.com/related-posts-by-taxonomy/templates/
 *
 * @package LezWatch.TV
 */

// if this file is called directly abort
if ( ! defined( 'WPINC' ) ) {
	die;
}

class LWTV_Plugins_Related_Posts_By_Taxonomy {
	public function __construct() {
		add_filter( 'related_posts_by_taxonomy_template', array( $this, 'lwtv_cards_format_template' ), 10, 3 );
		add_action( 'wp_loaded', array( $this, 'lwtv_cards_format' ), 11 );
		add_filter( 'related_posts_by_taxonomy_shortcode_atts', array( $this, 'lwtv_cards_args' ) ); // shortcode
		add_filter( 'related_posts_by_taxonomy_widget_args', array( $this, 'lwtv_cards_args' ) );    // widget
	}

	// Return the right template for the lwtv_cards format
	public function lwtv_cards_format_template( $template, $type, $format ) {
		if ( isset( $format ) && ( 'lwtv_cards' === $format ) ) {
			return 'related-posts-lwtv-cards.php';
		}
		return $template;
	}

	// Create new format thumbnail_excerpt for use in widget and shortcode
	public function lwtv_cards_format() {
		if ( ! class_exists( 'Related_Posts_By_Taxonomy_Defaults' ) ) {
			return;
		}

		$defaults = Related_Posts_By_Taxonomy_Defaults::get_instance();

		// Add the new format .
		$defaults->formats['lwtv_cards'] = __( 'LWTV Customized Display' );
	}

	// Return posts with post thumbnails for the thumbnail_excerpt format.
	public function lwtv_cards_args( $args ) {
		if ( 'thumbnail_excerpt' === $args['format'] ) {
			$args['post_thumbnail'] = true;
		}
		return $args;
	}
}
