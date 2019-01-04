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
			'lez-library/actor-cpt',
			array(
				'attributes'      => array(
					'lezactors_imdb'      => array(
						'type' => 'string',
					),
					'lezactors_wikipedia' => array(
						'type' => 'url',
					),
				),
				'render_callback' => array( $this, 'render_author_cpt' ),
			)
		);
	}

	public function render_author_cpt( $atts ) {
		if ( is_admin() ) {
			return cmb2_get_metabox_form( 'actors_metabox' );
		} else {
			return;
		}
	}
}

new LWTV_ServerSideRendering();
