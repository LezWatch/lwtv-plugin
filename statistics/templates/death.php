<?php
/**
 * The template for displaying the death stats page
 *
 * @package LezWatch.TV
 */

$deadchars = LWTV_Stats::generate( 'characters', 'dead', 'count' );
$allchars  = LWTV_Stats::generate( 'characters', 'all', 'count' );
$deadshows = LWTV_Stats::generate( 'shows', 'dead', 'count' );
$allshows  = LWTV_Stats::generate( 'shows', 'all', 'count' );

$deadchar_percent = round( ( $deadchars / $allchars ) * 100, 2 );
$deadshow_percent = round( ( $deadshows / $allshows ) * 100, 2 );

$valid_views = array( 'overview', 'characters', 'shows', 'stations', 'nations', 'years' );
$view        = ( ! isset( $_GET['view'] ) || ! in_array( $_GET['view'], $valid_views, true ) ) ? 'overview' : $_GET['view']; // phpcs:ignore WordPress.Security.NonceVerification
?>

<ul class="nav nav-tabs">
	<?php
	foreach ( $valid_views as $the_view ) {
		$active = ( $view === $the_view ) ? ' active' : '';
		echo '<li class="nav-item"><a class="nav-link' . esc_attr( $active ) . '" href="' . esc_url( add_query_arg( 'view', $the_view, '/statistics/death/' ) ) . '">' . esc_html( strtoupper( str_replace( '-', ' ', $the_view ) ) ) . '</a></li>';
	}
	?>
</ul>

<p>&nbsp;</p>

<?php

switch ( $view ) {
	case 'overview':
		?>
		<div class="container">
			<div class="row">
				<div class="col">
					<div class="card text-center">
						<h3 class="card-header alert-info">Characters</h3>
						<div class="card-body bg-light">
							<h5 class="card-title"><?php echo esc_html( $deadchar_percent ); ?>% (<?php echo esc_html( $deadchars ); ?>)</h5>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="card text-center">
						<h3 class="card-header alert-danger">Shows</h3>
						<div class="card-body bg-light">
							<h5 class="card-title"><?php echo esc_html( $deadshow_percent ); ?>% (<?php echo esc_html( $deadshows ); ?>)</h5>
						</div>
					</div>
				</div>
			</div>
		</div>

		<p>&nbsp;<br/>On average, <strong><?php LWTV_Stats::generate( 'characters', 'dead-years', 'average' ); ?></strong> characters die per year (including years where no queers died).</p>

		<div class="container">
			<div class="row">
				<div class="col">
					<?php LWTV_Stats::generate( 'characters', 'dead-years', 'trendline' ); ?>
				</div>
			</div>
		</div>
		<?php
		break;
	case 'characters':
		?>
		<h3>By Character Sexual Orientation</h3>
		<div class="container chart-container">
			<div class="row">
				<div class="col-sm-6">
					<?php LWTV_Stats::generate( 'characters', 'dead-sex', 'piechart' ); ?>
				</div>
				<div class="col-sm-6">
					<?php LWTV_Stats::generate( 'characters', 'dead-sex', 'percentage' ); ?>
				</div>
			</div>
		</div>
		<h3>By Character Gender Identity</h3>
		<div class="container chart-container">
			<div class="row">
				<div class="col-sm-6">
					<?php LWTV_Stats::generate( 'characters', 'dead-gender', 'piechart' ); ?>
				</div>
				<div class="col-sm-6">
					<?php LWTV_Stats::generate( 'characters', 'dead-gender', 'percentage' ); ?>
				</div>
			</div>
		</div>
		<h3>By Character Role</h3>
		<div class="container chart-container">
			<div class="row">
				<div class="col-sm-6">
					<?php LWTV_Stats::generate( 'characters', 'dead-role', 'piechart' ); ?>
				</div>
				<div class="col-sm-6">
					<?php LWTV_Stats::generate( 'characters', 'dead-role', 'percentage' ); ?>
				</div>
			</div>
		</div>
		<?php
		break;
	case 'shows':
		?>
		<div class="container chart-container">
			<div class="row">
				<div class="col-sm-6">
					<?php LWTV_Stats::generate( 'characters', 'dead-shows', 'piechart' ); ?>
				</div>
				<div class="col-sm-6">
					<?php LWTV_Stats::generate( 'shows', 'dead-shows', 'percentage' ); ?>
				</div>
			</div>
		</div>
		<?php
		break;
	case 'stations':
		?>
		<div class="container chart-container">
			<div class="row">
				<div class="col">
					<?php LWTV_Stats::generate( 'shows', 'dead-stations', 'barchart' ); ?>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<?php LWTV_Stats::generate( 'shows', 'dead-stations', 'percentage' ); ?>
				</div>
			</div>
		</div>
		<?php
		break;
	case 'nations':
		?>
		<div class="container chart-container">
			<div class="row">
				<div class="col">
					<?php LWTV_Stats::generate( 'shows', 'dead-nations', 'barchart' ); ?>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<?php LWTV_Stats::generate( 'shows', 'dead-nations', 'percentage' ); ?>
				</div>
			</div>
		</div>
		<?php
		break;
	case 'years':
		?>
		<p>On average, <strong><?php LWTV_Stats::generate( 'characters', 'dead-years', 'average' ); ?></strong> characters die per year (including years where no queers died).</p>

		<div class="container chart-container">
			<div class="row">
				<div class="col">
					<?php LWTV_Stats::generate( 'characters', 'dead-years', 'barchart' ); ?>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<?php LWTV_Stats::generate( 'characters', 'dead-years', 'percentage' ); ?>
				</div>
			</div>
		</div>
		<?php
		break;
}
