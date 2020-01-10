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
	public function start_datetime( $date ) {
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
	public function end_datetime( $date ) {
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
	public function prev_datetime( $date ) {
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

		switch ( $name ) {
			case 'Charmed':
				$name = 'Charmed (2018)';
				break;
			case 'Party of Five':
				$name = 'Party of Five (2020)';
				break;
			case 'Shameless':
				$name = 'Shameless (US)';
				break;
			case 'DC\'s Legends of Tomorrow':
				$name = 'Legends of Tomorrow';
				break;
			case 'Marvel\'s Runaways':
				$name = 'Runaways';
				break;
		}

		$show_page_obj = get_page_by_path( sanitize_title( $name ), OBJECT, 'post_type_shows' );

		if ( isset( $show_page_obj->ID ) && 0 !== $show_page_obj->ID && 'publish' === get_post_status( $show_page_obj->ID ) ) {
			$show_name = '<a href="' . get_permalink( $show_page_obj->ID ) . '">' . $name . '</a>';
		} else {
			$show_name = $name;
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
		$last_week_icon = LWTV_Functions::symbolicons( 'caret-left-circle.svg', 'fa-chevron-circle-left' );
		$next_week      = add_query_arg( 'tvdate', $next, get_permalink() );
		$next_week_icon = LWTV_Functions::symbolicons( 'caret-right-circle.svg', 'fa-chevron-circle-right' );

		$navigation = '<nav aria-label="This Year navigation" role="navigation"><ul class="pagination justify-content-center"><li class="page-item first mr-auto"><a href="' . $last_week . '" class="page-link">' . $last_week_icon . ' Last Week</a></li>';

		// ... We only show 'this week' when it's NOT this week
		if ( 'today' !== $date && $today !== $date ) {
			$navigation .= '<li class="page-item active"><a href="/calendar/" class="page-link">This Week</a></li>';
		}

		$navigation .= '<li class="page-item last ml-auto"><a href="' . $next_week . '" class="page-link">' . $next_week_icon . ' Next Week</a></li></ul></nav>';

		return $navigation;
	}

}

new LWTV_SSR_Calendar();
