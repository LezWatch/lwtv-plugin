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
	 */
	public function render_tvshow_calendar() {

		$date_query = isset( $_GET['tvdate'] ) ? sanitize_text_field( $_GET['tvdate'] ) : 'today';
		// Build out start and end dates.
		$tz = new DateTimeZone( 'America/New_York' );

		// This is for figuring out today
		$today = new DateTime( 'today', $tz );

		// Calculating the dates three times, which is weird
		// but every time I try to do it via modify, it
		// overwrites.
		if ( 'today' === $date_query ) {
			$start_datetime = new DateTime( 'today', $tz );
			$end_datetime   = new DateTime( 'today', $tz );
			$prev_datetime  = new DateTime( 'today', $tz );
		} else {
			$start_datetime = new DateTime( $date_query, $tz );
			$end_datetime   = new DateTime( $date_query, $tz );
			$prev_datetime  = new DateTime( $date_query, $tz );
		}

		// Start on Sunday
		if ( 'Sun' !== $start_datetime->format( 'D' ) ) {
			$start_datetime->modify( 'last Sunday' );
		}
		$end_datetime->modify( 'next Saturday' );
		$prev_datetime->modify( 'last Sunday' );

		// Begin the return
		$return = '<h2 class="lwtv-calendar-week">Week of ' . $start_datetime->format( 'F d, Y' ) . ' - ' . $end_datetime->format( 'F d, Y' ) . ' </h2>';

		// Array
		$calendar = LWTV_Whats_On_JSON::generate_tvshow_calendar( $start_datetime->format( 'Y-m-d' ) );

		if ( isset( $calendar['none'] ) ) {
			$return .= '<p>There are no shows on the air.</p>';
		} else {
			$return .= '<ul class="lwtv-calendar-list list-group">';

			foreach ( $calendar as $day => $shows ) {
				$hilight = ( $day === $today->format( 'l F d, Y' ) ) ? ' list-group-item-info' : '';

				$return .= '<li class="list-group-item' . $hilight . '"><h3 class="lwtv-calendar-date">' . $day . '</h3><ul class="list-group">';
				foreach ( $shows as $show ) {

					// Show name
					$return .= '<li class="list-group-item' . $hilight . '"><h4>' . $show['show_name'] . '</h4>';

					// Show Time
					$return .= '<div><span class="time">' . $show['airtime'] . ' US/Eastern</span></div>';

					// Episode Title(s)
					$return .= '<div class="lwtv-calendar-episode-title">';
					if ( is_array( $show['title'] ) ) {
						$return .= '<ul>';
						foreach ( $show['title'] as $one_show ) {
							$return .= '<li>' . $one_show . '</li>';
						}
						$return .= '</ul>';
					} else {
						$return .= $show['title'];
					}
					$return .= '</div>';

					$return .= '</li>';
				}
				$return .= '</ul></li>';
			}
			$return .= '</ul>';

			// NAVIGATION:

			// Since we set this to Saturday, we have to add a day for the links.
			$end_datetime->modify( '+1 day ' );

			// echo previous and next links:
			$prev_week = add_query_arg( 'tvdate', $prev_datetime->format( 'Y-m-d' ), get_permalink() );
			$prev_icon = LWTV_Functions::symbolicons( 'caret-left-circle.svg', 'fa-chevron-circle-left' );
			$next_week = add_query_arg( 'tvdate', $end_datetime->format( 'Y-m-d' ), get_permalink() );
			$next_icon = LWTV_Functions::symbolicons( 'caret-right-circle.svg', 'fa-chevron-circle-right' );

			$return .= '<nav aria-label="This Year navigation" role="navigation"><ul class="pagination justify-content-center"><li class="page-item first mr-auto"><a href="' . $prev_week . '" class="page-link">' . $prev_icon . ' Last Week</a></li><li class="page-item last ml-auto"><a href="' . $next_week . '" class="page-link">' . $next_icon . ' Next Week</a></li></ul></nav>';
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
