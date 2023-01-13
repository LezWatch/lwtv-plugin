<?php
/**
 * Functions that power the "This Year" pages
 *
 * @package LezWatch.TV
 */

require 'query_vars.php';
require 'characters.php';
require 'shows.php';

class LWTV_This_Year {

	/**
	 * Display the stuff :D
	 * @param  int    $thisyear the year.
	 * @return n/a    outputs everything
	 */
	public function display( $thisyear ) {
		$thisyear    = ( isset( $thisyear ) ) ? $thisyear : gmdate( 'Y' );
		$valid_views = array( 'characters-on-air', 'dead-characters', 'shows-on-air', 'new-shows', 'canceled-shows' );
		$view        = get_query_var( 'view', 'overview' );
		$baseurl     = ( gmdate( 'Y' ) !== $thisyear ) ? '/this-year/' . $thisyear . '/' : '/this-year/';

		?>
		<div class="thisyear-container">
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
			switch ( $view ) {
				case 'characters-on-air':
					( new LWTV_This_Year_Chars() )->list( $thisyear );
					break;
				case 'dead-characters':
					( new LWTV_This_Year_Chars() )->dead( $thisyear );
					break;
				case 'shows-on-air':
					( new LWTV_This_Year_Shows() )->list( $thisyear );
					break;
				case 'new-shows':
					( new LWTV_This_Year_Shows() )->new( $thisyear );
					break;
				case 'canceled-shows':
					( new LWTV_This_Year_Shows() )->canceled( $thisyear );
					break;
				default:
					self::overview( $thisyear );
					break;
			}

			self::navigation( $thisyear, $view );
			?>
		</div>
		<?php
	}

	public function overview( $thisyear ) {
		$thisyear = ( isset( $thisyear ) ) ? $thisyear : gmdate( 'Y' );
		$array    = array(
			'shows'      => ( new LWTV_This_Year_Shows() )->get_list( $thisyear, 'now', true ),
			'characters' => ( new LWTV_This_Year_Chars() )->get_list( $thisyear, true ),
			'dead'       => ( new LWTV_This_Year_Chars() )->get_dead( $thisyear, true ),
			'started'    => ( new LWTV_This_Year_Shows() )->get_list( $thisyear, 'started', true ),
			'canceled'   => ( new LWTV_This_Year_Shows() )->get_list( $thisyear, 'ended', true ),
		);
		?>

		<div class="container">
			<div class="row">
				<div class="col">
					<div class="card text-center">
						<h3 class="card-header alert-success">Characters on Air</h3>
						<div class="card-body bg-light">
							<h5 class="card-title"><?php echo (int) $array['characters']; ?></h5>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="card text-center">
						<h3 class="card-header alert-danger">Dead Characters</h3>
						<div class="card-body bg-light">
							<h5 class="card-title"><?php echo (int) $array['dead']; ?></h5>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<div class="card text-center">
						<h3 class="card-header alert-primary">Shows on Air</h3>
						<div class="card-body bg-light">
							<h5 class="card-title"><?php echo (int) $array['shows']; ?></h5>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="card text-center">
						<h3 class="card-header alert-info">New Shows</h3>
						<div class="card-body bg-light">
							<h5 class="card-title"><?php echo (int) $array['started']; ?></h5>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="card text-center">
						<h3 class="card-header alert-warning">Canceled Shows</h3>
						<div class="card-body bg-light">
							<h5 class="card-title"><?php echo (int) $array['canceled']; ?></h5>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php
	}

	/**
	 * Navigation for the year
	 * @param  [type]  $thisyear
	 * @return boolean           [description]
	 */
	public function navigation( $thisyear, $view ) {
		$lastyear = FIRST_LWTV_YEAR;
		$baseurl  = '/this-year/';
		$view     = ( 'overview' === $view ) ? '' : $view;
		?>

		<nav aria-label="This Year Navigation" role="navigation" class="yikes-pagination">
			<ul class="pagination justify-content-center">

				<?php
				// If it's not 1961, we can show the first year we have queers
				if ( $thisyear !== $lastyear ) {
					?>
					<li class="page-item first mr-auto"><a href="<?php echo esc_url( $baseurl . $lastyear . '/' . $view ); ?>" class="page-link"><?php echo ( new LWTV_Functions() )->symbolicons( 'caret-left-circle.svg', 'fa-chevron-circle-left' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> First (<?php echo (int) $lastyear; ?>)</a></li> 
					<li class="page-item"><a href="<?php echo esc_url( $baseurl . ( $thisyear - 1 ) . '/' . $view ); ?>" title="previous year" class="page-link"><?php echo ( new LWTV_Functions() )->symbolicons( 'caret-left.svg', 'fa-chevron-left' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> Previous</a></li>
					<li class="page-item"><a href="<?php echo esc_url( $baseurl . ( $thisyear - 2 ) . '/' . $view ); ?>" class="page-link"><?php echo (int) ( $thisyear - 2 ); ?></a></li>
					<li class="page-item"><a href="<?php echo esc_url( $baseurl . ( $thisyear - 1 ) . '/' . $view ); ?>" class="page-link"><?php echo (int) ( $thisyear - 1 ); ?></a></li>
					<?php
				}
				?>

				<li class="page-item active"><span class="active page-link"><?php echo (int) $thisyear; ?></span></li>

				<?php
				if ( gmdate( 'Y' ) !== $thisyear ) {
					?>
					<li class="page-item"><a href="<?php echo esc_url( $baseurl . ( $thisyear + 1 ) . '/' . $view ); ?>" class="page-link"><?php echo (int) ( $thisyear + 1 ); ?></a></li>
					<li class="page-item"><a href="<?php echo esc_url( $baseurl . ( $thisyear + 1 ) . '/' . $view ); ?>" class="page-link" title="next year">Next <?php echo ( new LWTV_Functions() )->symbolicons( 'caret-right.svg', 'fa-chevron-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></li>
					<li class="page-item last ml-auto"><a href="<?php echo esc_url( $baseurl . gmdate( 'Y' ) . '/' . $view ); ?>" class="page-link">Last (<?php echo (int) gmdate( 'Y' ); ?>) <?php echo ( new LWTV_Functions() )->symbolicons( 'caret-right-circle.svg', 'fa-chevron-circle-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></li>
					<?php
				}
				?>
			</ul>
		</nav><!-- .navigation -->
		<?php
	}

}

new LWTV_This_Year();
