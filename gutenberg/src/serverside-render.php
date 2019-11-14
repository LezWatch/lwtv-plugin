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

		// CPT Stuff: This is a proto-attempt to include CMB2 in the post.
		register_block_type(
			'lez-library/cpt-meta',
			array(
				'attributes'      => array( 'post_id' => array( 'type' => 'int' ) ),
				'render_callback' => array( $this, 'render_cpt_meta' ),
			)
		);

	}

	/**
	 * Render the calendar
	 * TO DO: Make this navigatable...
	 */
	public function render_tvshow_calendar() {

		// Build out start and end dates.
		$tz = new DateTimeZone( 'America/New_York' );

		$start_datetime = new DateTime( 'today', $tz );
		if ( 'Sun' !== $start_datetime->format( 'D' ) ) {
			$start_datetime = new DateTime( 'last Sunday', $tz );
		}

		$end_datetime = new DateTime( 'today', $tz );
		$end_datetime->modify( '+1 week' );

		// Begin the return
		$return = '<h2 class="lwtv-calendar-week">Week of ' . $start_datetime->format( 'F d, Y' ) . ' - ' . $end_datetime->format( 'F d, Y' ) . ' </h2>';

		// Array
		$calendar = LWTV_Whats_On_JSON::generate_tvshow_calendar( gmdate( 'Y-m-d' ) );

		if ( isset( $calendar['none'] ) ) {
			$return .= '<p>There are no shows on the air.</p>';
		} else {
			$return .= '<ul class="lwtv-calendar-list">';

			foreach ( $calendar as $day => $shows ) {
				$return .= '<li><h3 class="lwtv-calendar-date">' . $day . '</h3><ul>';
				foreach ( $shows as $show ) {

					// Show name
					$return .= '<li><h4>' . $show['show_name'] . '</h4>';

					// Show Time
					$return .= '<div><span class="time">' . $show['airtime'] . ' US/Eastern</span></div>';

					// Episode Title(s)
					$return .= '<div class="lwtv-calendar-episode-title">' . $show['title'] . '</div>';

					$return .= '</li>';
				}
				$return .= '</ul></li>';
			}
			$return .= '</ul>';
		}

		return $return;

	}

	/**
	 * Render Custom Post Meta
	 * @param  [type] $atts [description]
	 * @return [type]       [description]
	 */
	public function render_cpt_meta( $atts ) {

		// Don't show on the front end.
		if ( is_single() && ! isset( $atts['post_id'] ) ) {
			return;
		}

		switch ( get_post_type( $atts['post_id'] ) ) {
			case 'post_type_shows':
				$boxes  = '<h2>TV Show Details</h2>';
				$boxes .= cmb2_get_metabox_form( 'show_details_metabox' );
				//$boxes .= '<h3>Plots and Relationship Details</h3>';
				//$boxes .= cmb2_get_metabox_form( 'shows_metabox' );
				break;
			case 'post_type_characters':
				$boxes  = '<h2>Character Sexuality and Orientation</h2>';
				$boxes .= cmb2_get_metabox_form( 'chars_metabox_grid' );
				//$boxes .= '<h3>General Details</h3>';
				//$boxes .= cmb2_get_metabox_form( 'chars_metabox_main' );
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
