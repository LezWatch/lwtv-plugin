<?php
/**
 * Name: Calendar
 * Description: Code to display the calendar
 * Version: 1.0
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

class LWTV_SSR_Calendar {

	/**
	 * Start Date/Time
	 *
	 * This will set up the start of the week. It's always Sunday.
	 *
	 * @param  string $date The date
	 * @return object       DateTime object
	 */
	public function start_datetime( $date, $tz ) {
		$start_datetime = new DateTime( $date, $tz );

		// If it's not Sunday, we want the previous Sunday
		if ( 'Sun' !== $start_datetime->format( 'D' ) ) {
			$start_datetime->modify( 'last Sunday' );
		}

		return $start_datetime;
	}

	/**
	 * End Date/Time
	 *
	 * This will set up the End of the week. It's always Saturday.
	 *
	 * @param  string $date The date
	 * @return object       DateTime object
	 */
	public function end_datetime( $date, $tz ) {
		$end_datetime = new DateTime( $date, $tz );

		// If it's not Saturday, we want to jump to the next one
		if ( 'Sat' !== $end_datetime->format( 'D' ) ) {
			$end_datetime->modify( 'next Saturday' );
		}

		return $end_datetime;
	}

	/**
	 * Previous Date/Time
	 *
	 * This will set up the start of the PREVIOUS week. It's always Sunday.
	 *
	 * @param  string $date The date
	 * @return object       DateTime object
	 */
	public function prev_datetime( $date, $tz ) {
		$prev_datetime = new DateTime( $date, $tz );

		// If it's not Sunday, we want the previous Sunday
		if ( 'Sun' !== $prev_datetime->format( 'D' ) ) {
			$prev_datetime->modify( 'last Sunday' );
		}

		// Now we need to jump back to the previous week...
		$prev_datetime->modify( '1 week ago' );

		return $prev_datetime;
	}

	/**
	 * Generate correct show name in order to be linked
	 * @param  string $name Pretty name of show
	 * @return string       Pretty Name with URL (if exists)
	 */
	public function show_name( $name ) {

		// Defaults
		$displayname = $name;
		$show_name   = $name;

		// Call the namer to try and sort out different names.
		require_once dirname( __DIR__ ) . '/cpts/shows/calendar-names.php';
		$name = ( new LWTV_Shows_Calendar() )->check_name( $name, 'tvmaze' );

		// Find the show based on the LezWatch name
		$show_page_obj = get_page_by_path( sanitize_title( $name ), OBJECT, 'post_type_shows' );

		// If we have a show, we will link.
		if ( isset( $show_page_obj->ID ) && 0 !== $show_page_obj->ID && 'publish' === get_post_status( $show_page_obj->ID ) ) {
			$show_name = '<a href="' . get_permalink( $show_page_obj->ID ) . '">' . $displayname . '</a>';
		}

		return $show_name;
	}

	/**
	 * Generate navigation
	 *
	 * All dates are 'Y-m-d'
	 *
	 * @param  string $date  The date we're building nav for
	 * @param  string $today Today's date
	 * @param  string $last  Last week
	 * @param  string $next  Next week
	 * @return string       HTML output for the navigation
	 */
	public function navigation( $date, $today, $last, $next ) {

		// echo previous and next links:
		$last_week      = add_query_arg( 'tvdate', $last, get_permalink() );
		$last_week_icon = ( new LWTV_Functions() )->symbolicons( 'caret-left-circle.svg', 'fa-chevron-circle-left' );
		$next_week      = add_query_arg( 'tvdate', $next, get_permalink() );
		$next_week_icon = ( new LWTV_Functions() )->symbolicons( 'caret-right-circle.svg', 'fa-chevron-circle-right' );

		$navigation = '<nav aria-label="Calendar Navigation" role="navigation" class="yikes-pagination"><ul class="pagination justify-content-center"><li class="page-item first me-auto"><a href="' . $last_week . '" class="page-link">' . $last_week_icon . ' Last Week</a></li>';

		// ... We only show 'this week' when it's NOT this week
		if ( 'today' !== $date && $today !== $date ) {
			$navigation .= '<li class="page-item"><a href="/calendar/" class="page-link">This Week</a></li>';
		}

		$navigation .= '<li class="page-item last ms-auto"><a href="' . $next_week . '" class="page-link">Next Week ' . $next_week_icon . ' </a></li></ul></nav>';

		return $navigation;
	}

	public function output() {
		// Build out start and end dates.
		$tz    = new DateTimeZone( 'America/New_York' );
		$today = new DateTime( 'today', $tz );

		// Query Variables.
		$get_tvdate = isset( $_GET['tvdate'] ) ? sanitize_text_field( $_GET['tvdate'] ) : 'today'; // phpcs:ignore WordPress.Security.NonceVerification
		$date_query = ( ( strtotime( $get_tvdate ) !== false ) && ( $get_tvdate !== $today->format( 'Y-m-d' ) ) ) ? $get_tvdate : 'today';

		// @todo: If 'date_query' is 2 weeks ago, or 2 weeks ahead, we don't show.

		// Get the dates
		$start_datetime = self::start_datetime( $date_query, $tz );
		$end_datetime   = self::end_datetime( $date_query, $tz );
		$prev_datetime  = self::prev_datetime( $date_query, $tz );

		// Begin the return
		$return = '<h2 class="lwtv-calendar-week">Week of ' . $start_datetime->format( 'F d, Y' ) . ' - ' . $end_datetime->format( 'F d, Y' ) . ' </h2>';

		// Array
		$calendar = ( new LWTV_Whats_On_JSON() )->generate_tvshow_calendar( $start_datetime->format( 'Y-m-d' ) );

		if ( isset( $calendar['none'] ) || empty( $calendar ) ) {
			// We can't find anything listed
			$return .= '<p>There are no shows on the air for the week starting ' . $start_datetime->format( 'F d, Y' ) . '.</p>';

			if ( $end_datetime > $today ) {
				// End date is in the future
				$return .= '<p>We only project the calendar 2-4 weeks in advance. Future planned airings are subject to change without notice.</p>';
			} else {
				// It's the past
				$return .= '<p>We don\'t keep historical calendar records, so you won\'t be able to retrieve listings from long ago. Sorry.</p>';
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
					$show_name = self::show_name( $show['show_name'] );

					// Build output
					$show_content = '<div class="ep-calendar-title">';
					if ( is_array( $show['title'] ) ) {
						$show_content .= '<em>' . $show_name . ' <span class="badge text-bg-secondary badge-pill">' . count( $show['title'] ) . '</span></em>';
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

		$navigation = self::navigation( $date_query, $today->format( 'Y-m-d' ), $prev_datetime->format( 'Y-m-d' ), $end_datetime->format( 'Y-m-d' ) );
		$return    .= $navigation;

		// Powered by
		$return .= '<p><small><a href="https://www.tvmaze.com" target="_new">Powered by TVMaze.</a></small></p>';

		return '<div class="lwtv-calendar-block">' . $return . '</div>';
	}
}

new LWTV_SSR_Calendar();
