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
}

new LWTV_ServerSideRendering();
