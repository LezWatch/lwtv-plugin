<?php
/**
 * Register Block Types
 *
 * All this is needed for server side render.
 */

namespace LWTV\Blocks;

use LWTV\Calendar\Blocks as CalendarBlocks;
use LWTV\Features\Shortcodes;

class Serverside {

	// Directory
	protected static $directory;

	/**
	 * Constructor
	 */
	public function __construct() {
		new Shortcodes();
		self::$directory = __DIR__;

		// Register SSR blocks.
		// author-box
		register_block_type(
			'lwtv/author-box',
			array(
				'attributes'      => array(
					'api_version' => 3,
					'users'       => array( 'type' => 'string' ),
					'format'      => array( 'type' => 'string' ),
				),
				'render_callback' => array( $this, 'render_author_box' ),
			)
		);

		// glossary
		register_block_type(
			'lez-library/glossary',
			array(
				'attributes'      => array(
					'api_version' => 3,
					'taxonomy'    => array( 'type' => 'string' ),
				),
				'render_callback' => array( $this, 'render_glossary' ),
			)
		);

		// TV Show Calendar
		register_block_type(
			'lwtv/tvshow-calendar',
			array(
				'attributes'      => array(
					'api_version' => 3,
				),
				'render_callback' => array( $this, 'render_tvshow_calendar' ),
			)
		);

		// Private Notes
		register_block_type(
			'lez-library/private-note',
			array(
				'attributes'      => array(
					'api_version' => 3,
				),
				'render_callback' => array( $this, 'render_private_blocks' ),
			)
		);
	}

	/**
	 * Render the Author Box
	 */
	public function render_author_box( $attributes ) {
		return ( new Shortcodes() )->author_box( $attributes );
	}

	/**
	 * Render the Glossary
	 */
	public function render_glossary( $attributes ) {
		return ( new Shortcodes() )->glossary( $attributes );
	}

	/**
	 * Render the calendar
	 */
	public function render_tvshow_calendar() {
		// Require the calendar file
		$return = ( new CalendarBlocks() )->make();
		return $return;
	}

	/**
	 * Render private blocks
	 *
	 * This requires some Dom Massaging.
	 *
	 * @TODO: Convert to new HTML API?
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
