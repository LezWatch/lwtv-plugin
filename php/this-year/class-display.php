<?php
/**
 * Displays 'this year' data.
 *
 * @package LezWatch.TV
 */

namespace LWTV\This_Year;

use LWTV\This_Year\Generator;

class Display {
	/**
	 * Build the stuff for this year
	 *
	 * @param  int    $this_year the year.
	 *
	 * @return n/a    outputs everything
	 */
	public function make( $this_year ) {
		$this_year   = ( isset( $this_year ) ) ? $this_year : gmdate( 'Y' );
		$valid_views = array( 'characters-on-air', 'dead-characters', 'shows-on-air', 'new-shows', 'canceled-shows' );
		$view        = get_query_var( 'view', 'overview' );
		$baseurl     = ( gmdate( 'Y' ) !== $this_year ) ? '/this-year/' . $this_year . '/' : '/this-year/';

		?>
		<div class="container">
			<ul class="nav nav-tabs">
				<?php
				echo '<li class="nav-item"><a class="nav-link' . esc_attr( ( 'overview' === $view ) ? ' active' : '' ) . '" href="' . esc_url( $baseurl ) . '">OVERVIEW</a></li>';
				foreach ( $valid_views as $the_view ) {
					$active = ( $view === $the_view ) ? ' active' : '';
					echo '<li class="nav-item"><a class="nav-link' . esc_attr( $active ) . '" href="' . esc_url( $baseurl . $the_view ) . '/">' . esc_html( strtoupper( str_replace( '-', ' ', $the_view ) ) ) . '</a></li>';
				}
				?>
			</ul>

			<p>&nbsp;</p>

			<?php
			if ( ! in_array( $view, $valid_views, true ) ) {
				$view = 'overview';
			}

			// Generate data
			Generator::make( $this_year, $view );

			// Navigation
			self::navigation( $this_year, $view );

			?>
		</div>
		<?php
	}

	/**
	 * Navigation for the year. This changes a little based on your sub pages.
	 *
	 * @param string  $this_year
	 * @param string  $view
	 *
	 * @return N/A
	 */
	public function navigation( $this_year, $view ) {
		$start_year = LWTV_FIRST_YEAR;
		$baseurl    = '/this-year/';
		$view       = ( 'overview' === $view ) ? '' : $view;
		?>

		<nav aria-label="This Year Navigation" role="navigation" class="yikes-pagination">
			<ul class="pagination justify-content-center">

				<?php
				// If it's not the oldest year there were queers, we can show the first year we have queers.
				if ( $this_year !== $start_year ) {
					?>
					<li class="page-item first me-auto"><a href="<?php echo esc_url( $baseurl . $start_year . '/' . $view ); ?>" class="page-link"><?php echo lwtv_plugin()->get_symbolicon( 'caret-left-circle.svg', 'fa-chevron-circle-left' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> First (<?php echo (int) $start_year; ?>)</a></li>
					<li class="page-item"><a href="<?php echo esc_url( $baseurl . ( $this_year - 1 ) . '/' . $view ); ?>" title="previous year" class="page-link"><?php echo lwtv_plugin()->get_symbolicon( 'caret-left.svg', 'fa-chevron-left' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> Previous</a></li>
					<li class="page-item"><a href="<?php echo esc_url( $baseurl . ( $this_year - 2 ) . '/' . $view ); ?>" class="page-link"><?php echo (int) ( $this_year - 2 ); ?></a></li>
					<li class="page-item"><a href="<?php echo esc_url( $baseurl . ( $this_year - 1 ) . '/' . $view ); ?>" class="page-link"><?php echo (int) ( $this_year - 1 ); ?></a></li>
					<?php
				}
				?>

				<li class="page-item active"><span class="active page-link"><?php echo (int) $this_year; ?></span></li>

				<?php
				if ( gmdate( 'Y' ) !== $this_year ) {
					?>
					<li class="page-item"><a href="<?php echo esc_url( $baseurl . ( $this_year + 1 ) . '/' . $view ); ?>" class="page-link"><?php echo (int) ( $this_year + 1 ); ?></a></li>
					<li class="page-item"><a href="<?php echo esc_url( $baseurl . ( $this_year + 1 ) . '/' . $view ); ?>" class="page-link" title="next year">Next <?php echo lwtv_plugin()->get_symbolicon( 'caret-right.svg', 'fa-chevron-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></li>
					<li class="page-item last ms-auto"><a href="<?php echo esc_url( $baseurl . gmdate( 'Y' ) . '/' . $view ); ?>" class="page-link">Last (<?php echo (int) gmdate( 'Y' ); ?>) <?php echo lwtv_plugin()->get_symbolicon( 'caret-right-circle.svg', 'fa-chevron-circle-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></li>
					<?php
				}
				?>
			</ul>
		</nav><!-- .navigation -->
		<?php
	}
}
