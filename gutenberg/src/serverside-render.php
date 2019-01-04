<?php
/**
 * Register Block Types
 *
 * All this is needed for server side render.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LWTV_ServerSideRendering {

	public function __construct() {
		// author-box
		register_block_type(
			'lwtv/author-box',
			array(
				'attributes'      => array(
					'users'  => array(
						'type' => 'string',
					),
					'format' => array(
						'type' => 'string',
					),
				),
				'render_callback' => array( 'LWTV_Shortcodes', 'author_box' ),
			)
		);

		// glossary
		register_block_type(
			'lez-library/glossary',
			array(
				'attributes'      => array(
					'taxonomy' => array(
						'type' => 'string',
					),
				),
				'render_callback' => array( 'LWTV_Shortcodes', 'glossary' ),
			)
		);

		// Author CPT Stuff
		register_block_type(
			'lez-library/cpt-meta',
			array(
				'attributes'      => array( 'post_id' => array( 'type' => 'int' ) ),
				'render_callback' => array( $this, 'render_cpt_meta' ),
			)
		);
	}

	public function render_cpt_meta( $atts ) {

		// Don't show on the front end.
		if ( is_single() && ! isset( $atts['post_id'] ) ) {
			return;
		}

		switch ( get_post_type( $atts['post_id'] ) ) {
			case 'post_type_shows':
				$boxes  = '<h2>TV Show Details</h2>';
				$boxes .= cmb2_get_metabox_form( 'show_details_metabox' );
				$boxes  = '<h3>Plots and Relationship Details</h3>';
				$boxes .= cmb2_get_metabox_form( 'shows_metabox' );
				break;
			case 'post_type_characters':
				$boxes  = '<h2>Character Details</h2>';
				$boxes .= cmb2_get_metabox_form( 'chars_metabox' );
				break;
			case 'post_type_actors':
				$boxes  = '<h2>Actor Details</h2>';
				$boxes .= cmb2_get_metabox_form( 'actors_metabox' );
				break;
			default:
				$boxes = 'This feature is only available on Actors, Characters, or Shows pages. Also you shouldn\'t be able to insert it, so how did you get here?';
		}

		if ( isset( $boxes ) ) {
			return $boxes;
		}
	}
}

new LWTV_ServerSideRendering();
