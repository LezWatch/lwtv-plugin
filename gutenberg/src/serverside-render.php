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

		// Query Variables.
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
			$return .= '<p>There are no shows on the air for this week.</p>';
		} else {
			$return .= '<p>All times are displayed as US/Eastern, but are reflective of their original airdate and time.</p>';
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
					switch ( $show['show_name'] ) {
						case 'Charmed':
							$show['show_name'] = 'Charmed (2018)';
							break;
						case 'Party of Five':
							$show['show_name'] = 'Party of Five (2020)';
							break;
						case 'Shameless':
							$show['show_name'] = 'Shameless (US)';
							break;
					}

					$show_page_obj = get_page_by_path( sanitize_title( $show['show_name'] ), OBJECT, 'post_type_shows' );

					if ( isset( $show_page_obj->ID ) && 0 !== $show_page_obj->ID ) {
						$show_name = '<a href="' . get_permalink( $show_page_obj->ID ) . '">' . $show['show_name'] . '</a>';
					} else {
						$show_name = $show['show_name'];
					}

					// Build output
					$show_content = '<div class="ep-calendar-title">';
					if ( is_array( $show['title'] ) ) {
						$show_content .= '<em>' . $show_name . '</em>';
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

			// NAVIGATION:

			// Since we set this to Saturday, we have to add a day for the links.
			$end_datetime->modify( '+1 day ' );

			// echo previous and next links:
			$prev_week = add_query_arg( 'tvdate', $prev_datetime->format( 'Y-m-d' ), get_permalink() );
			$prev_icon = LWTV_Functions::symbolicons( 'caret-left-circle.svg', 'fa-chevron-circle-left' );
			$next_week = add_query_arg( 'tvdate', $end_datetime->format( 'Y-m-d' ), get_permalink() );
			$next_icon = LWTV_Functions::symbolicons( 'caret-right-circle.svg', 'fa-chevron-circle-right' );

			$return .= '<nav aria-label="This Year navigation" role="navigation"><ul class="pagination justify-content-center"><li class="page-item first mr-auto"><a href="' . $prev_week . '" class="page-link">' . $prev_icon . ' Last Week</a></li><li class="page-item active"><a href="/calendar/" class="page-link">This Week</a></li><li class="page-item last ml-auto"><a href="' . $next_week . '" class="page-link">' . $next_icon . ' Next Week</a></li></ul></nav>';
		}

		return $return;

	}
}

new LWTV_ServerSideRendering();
