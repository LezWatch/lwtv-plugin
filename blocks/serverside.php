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
					'users'  => array( 'type' => 'string' ),
					'format' => array( 'type' => 'string' ),
				),
				'render_callback' => array( 'LWTV_Shortcodes', 'author_box' ),
			)
		);

		// glossary
		register_block_type(
			'lez-library/glossary',
			array(
				'attributes'      => array( 'taxonomy' => array( 'type' => 'string' ) ),
				'render_callback' => array( 'LWTV_Shortcodes', 'glossary' ),
			)
		);

		// TV Show Calendar
		register_block_type(
			'lwtv/tvshow-calendar',
			array(
				'render_callback' => array( $this, 'render_tvshow_calendar' ),
			)
		);

		// Private Notes
		register_block_type(
			'lez-library/private-note',
			array(
				'render_callback' => array( $this, 'render_private_blocks' ),
			)
		);
	}

	/**
	 * Render the calendar
	 */
	public function render_tvshow_calendar() {
		// Require the calendar file
		require_once 'calendar.php';
		$return = ( new LWTV_SSR_Calendar() )->output();
		return $return;
	}

	/**
	 * Render private blocks
	 *
	 * This requires some Dom Massaging.
	 */
	public function render_private_blocks( $attributes, $content ) {

		if ( is_admin() ) {
			return $content;
		}

		$dom = new \DomDocument();
		$dom->loadXML( $content );

		$finder             = new \DomXPath( $dom );
		$secure_class       = 'wp-block-lez-library-private-note';
		$secure_content     = $finder->query( "//div[contains(@class, '$secure_class')]" );
		$secure_content_dom = new \DOMDocument();

		foreach ( $secure_content as $node ) {
			$secure_content_dom->appendChild( $secure_content_dom->importNode( $node, true ) );
		}

		$secure_content = trim( $secure_content_dom->saveHTML() );

		// Only people who can edit published posts (author, editor, admin) can see this.
		if ( is_user_logged_in() && current_user_can( 'edit_published_posts' ) ) {
			return $secure_content;
		}
	}
}

new LWTV_ServerSideRendering();
