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
		require_once( 'tvshow-calendar/calendar.php' );

		// Build out start and end dates.
		$tz    = new DateTimeZone( 'America/New_York' );
		$today = new DateTime( 'today', $tz );

		// Query Variables.
		$date_query = ( isset( $_GET['tvdate'] ) && ( $_GET['tvdate'] !== $today->format( 'Y-m-d' ) ) ) ? sanitize_text_field( $_GET['tvdate'] ) : 'today';

		// Get the dates
		$start_datetime = ( new LWTV_SSR_Calendar() )->start_datetime( $date_query, $tz );
		$end_datetime   = ( new LWTV_SSR_Calendar() )->end_datetime( $date_query, $tz );
		$prev_datetime  = ( new LWTV_SSR_Calendar() )->prev_datetime( $date_query, $tz );

		// Begin the return
		$return = '<h2 class="lwtv-calendar-week">Week of ' . $start_datetime->format( 'F d, Y' ) . ' - ' . $end_datetime->format( 'F d, Y' ) . ' </h2>';

		// Array
		$calendar = ( new LWTV_Whats_On_JSON() )->generate_tvshow_calendar( $start_datetime->format( 'Y-m-d' ) );

		if ( isset( $calendar['none'] ) || empty( $calendar ) ) {
			// We can't find anything listed
			$return .= '<p>There are no shows on the air for the week starting ' . $start_datetime->format( 'F d, Y' ) . '.</p>';

			if ( $end_datetime > $today ) {
				// End date is in the future
				$return .= '<p>We only project the calendar 2-4 weeks in advance. Future planned airings are subject to change without notice.<p>';
			} else {
				// It's the past
				$return .= '<p>We don\'t keep historical calendar records, so you won\'t be able to retrive listings from long ago. Sorry.</p>';
			}
		} else {
			$return .= '<p>All times are displayed as US/Eastern, but are reflective of their original airdate and time.</p>';
			$return .= '<p>Be advised, airdates and times are subject to change without notice. Always check your local listings.<p>';

			$return .= '<table class="table lwtvc table-hover">';

			foreach ( $calendar as $day => $shows ) {
				$hilight = ( $day === $today->format( 'Y-m-d' ) ) ? ' table-info' : '';
				$showday = new DateTime( $day, $tz );

				$today_date = $showday->format( 'F d, Y' );
				if ( $day === $today->format( 'Y-m-d' ) ) {
					$today_date .= '&nbsp;&nbsp;<button type="button" class="btn btn-info btn-sm" disabled><a name="today">Today</a></button>';
				}

				$return .= '<thead class="thead-light"><tr class="lwtvc-heading' . $hilight . '" data-date="' . $showday->format( 'Y-m-d' ) . '"><th colspan="3"><span class="ep-calendar-heading-date">' . $today_date . '</span><span class="ep-calendar-heading-weekday">' . $showday->format( 'l' ) . '</span></th></tr></thead><tbody>';

				foreach ( $shows as $show ) {
					// Episode Title(s)
					$show_name = ( new LWTV_SSR_Calendar() )->show_name( $show['show_name'] );

					// Build output
					$show_content = '<div class="ep-calendar-title">';
					if ( is_array( $show['title'] ) ) {
						$show_content .= '<em>' . $show_name . ' <span class="badge badge-secondary badge-pill">' . count( $show['title'] ) . '</span></em>';
						$show_content .= '<ul>';
						foreach ( $show['title'] as $one_show ) {
							$show_content .= '<li>' . $one_show . '</li>';
						}
						$show_content .= '</ul>';
					} else {
						$show_content .= '<em>' . $show_name . '</em> - ' . $show['title'];
					}
					$show_content .= '</div>';

					// Set the showtime from the timestamp
					$show_time = new DateTime( '@' . $show['timestamp'], $tz );

					// Return it all!
					$return .= '<tr class="ep-calendar-item' . $hilight . '"><td class="ep-calendar-item-time">' . $show_time->format( 'g:i A' ) . '</td><td class="ep-calendar-marker"><span class="ep-calendar-dot"></span></td><td class="ep-calendar-item-title">' . $show_content . '</td></tr>';
				}
			}
			$return .= '</tbody></table>';
		}

		// NAVIGATION
		// NEXT week: Since we set this to Saturday, we have to add a day for the links.
		$end_datetime->modify( '+1 day' );
		// Change today so we can check if the 'this week' button is needed
		$today->modify( 'last Sunday' );

		$navigation = ( new LWTV_SSR_Calendar() )->navigation( $date_query, $today->format( 'Y-m-d' ), $prev_datetime->format( 'Y-m-d' ), $end_datetime->format( 'Y-m-d' ) );
		$return    .= $navigation;

		return $return;

	}
}

new LWTV_ServerSideRendering();
