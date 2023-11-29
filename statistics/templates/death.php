<?php
/**
 * The template for displaying the death stats page
 *
 * @package LezWatch.TV
 */

$deadchars = ( new LWTV_Statistics() )->generate( 'characters', 'dead', 'count' );
$allchars  = ( new LWTV_Statistics() )->generate( 'characters', 'all', 'count' );
$deadshows = ( new LWTV_Statistics() )->generate( 'shows', 'dead', 'count' );
$allshows  = ( new LWTV_Statistics() )->generate( 'shows', 'all', 'count' );

$deadchar_percent = round( ( $deadchars / $allchars ) * 100, 2 );
$deadshow_percent = round( ( $deadshows / $allshows ) * 100, 2 );

$valid_views = array( 'characters', 'shows', 'stations', 'nations', 'years', 'list' );
$sent_view   = get_query_var( 'view', 'overview' );
$view        = ( ! in_array( $sent_view, $valid_views, true ) ) ? 'overview' : $sent_view;
?>
<ul class="nav nav-tabs">
	<?php
	$baseurl = '/statistics/death/';

	echo '<li class="nav-item"><a class="nav-link' . esc_attr( ( 'overview' === $view ) ? ' active' : '' ) . '" href="' . esc_url( $baseurl ) . '">OVERVIEW</a></li>';
	foreach ( $valid_views as $the_view ) {
		$active = ( $view === $the_view ) ? ' active' : '';
		echo '<li class="nav-item"><a class="nav-link' . esc_attr( $active ) . '" href="' . esc_url( $baseurl . $the_view . '/' ) . '">' . esc_html( strtoupper( str_replace( '-', ' ', $the_view ) ) ) . '</a></li>';
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
						<h3 class="card-header characters">Characters</h3>
						<div class="card-body bg-light">
							<h5 class="card-title"><?php echo esc_html( $deadchar_percent ); ?>% (<?php echo esc_html( $deadchars ); ?>)</h5>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="card text-center">
						<h3 class="card-header shows">Shows</h3>
						<div class="card-body bg-light">
							<h5 class="card-title"><?php echo esc_html( $deadshow_percent ); ?>% (<?php echo esc_html( $deadshows ); ?>)</h5>
						</div>
					</div>
				</div>
			</div>
		</div>

		<p>&nbsp;<br/>On average, <strong><?php ( new LWTV_Statistics() )->generate( 'characters', 'dead-years', 'average' ); ?></strong> characters die per year (including years where no queers died).</p>

		<div class="container">
			<div class="row">
				<div class="col">
					<?php ( new LWTV_Statistics() )->generate( 'characters', 'dead-years', 'trendline' ); ?>
				</div>
			</div>
		</div>
		<?php
		break;
	case 'characters':
		?>
		<h3>Death By Character Sexual Orientation</h3>
		<div class="container chart-container">
			<div class="row">
				<div class="col-sm-6">
					<?php ( new LWTV_Statistics() )->generate( 'characters', 'dead-sex', 'piechart' ); ?>
				</div>
				<div class="col-sm-6">
					<?php ( new LWTV_Statistics() )->generate( 'characters', 'dead-sex', 'percentage' ); ?>
				</div>
			</div>
		</div>
		<h3>Death By Character Gender Identity</h3>
		<div class="container chart-container">
			<div class="row">
				<div class="col-sm-6">
					<?php ( new LWTV_Statistics() )->generate( 'characters', 'dead-gender', 'piechart' ); ?>
				</div>
				<div class="col-sm-6">
					<?php ( new LWTV_Statistics() )->generate( 'characters', 'dead-gender', 'percentage' ); ?>
				</div>
			</div>
		</div>
		<h3>Death By Character Role</h3>
		<div class="container chart-container">
			<div class="row">
				<div class="col-sm-6">
					<?php ( new LWTV_Statistics() )->generate( 'characters', 'dead-role', 'piechart' ); ?>
				</div>
				<div class="col-sm-6">
					<?php ( new LWTV_Statistics() )->generate( 'characters', 'dead-role', 'percentage' ); ?>
				</div>
			</div>
		</div>
		<?php
		break;
	case 'shows':
		?>
		<h3>Death per Show Breakdown</h3>
		<div class="container chart-container">
			<div class="row">
				<div class="col-sm-6">
					<?php ( new LWTV_Statistics() )->generate( 'characters', 'dead-shows', 'piechart' ); ?>
				</div>
				<div class="col-sm-6">
					<?php ( new LWTV_Statistics() )->generate( 'shows', 'dead-shows', 'percentage' ); ?>
				</div>
			</div>
		</div>
		<?php
		break;
	case 'stations':
		?>
		<h3>Death per Station/Network Breakdown</h3>
		<div class="container chart-container">
			<div class="row">
				<div class="col">
					<?php ( new LWTV_Statistics() )->generate( 'shows', 'dead-stations', 'barchart' ); ?>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<?php ( new LWTV_Statistics() )->generate( 'shows', 'dead-stations', 'percentage' ); ?>
				</div>
			</div>
		</div>
		<?php
		break;
	case 'nations':
		?>
		<h3>Death per Country Breakdown</h3>
		<div class="container chart-container">
			<div class="row">
				<div class="col">
					<?php ( new LWTV_Statistics() )->generate( 'shows', 'dead-nations', 'barchart' ); ?>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<?php ( new LWTV_Statistics() )->generate( 'shows', 'dead-nations', 'percentage' ); ?>
				</div>
			</div>
		</div>
		<?php
		break;
	case 'years':
		?>
		<h3>Death per Year Breakdown</h3>
		<p>On average, <strong><?php ( new LWTV_Statistics() )->generate( 'characters', 'dead-years', 'average' ); ?></strong> characters die per year (including years where no queers died).</p>

		<div class="container chart-container">
			<div class="row">
				<div class="col">
					<?php ( new LWTV_Statistics() )->generate( 'characters', 'dead-years', 'barchart' ); ?>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<?php ( new LWTV_Statistics() )->generate( 'characters', 'dead-years', 'percentage' ); ?>
				</div>
			</div>
		</div>
		<?php
		break;
	case 'list':
		$time_since = ( new LWTV_Statistics() )->generate( 'characters', 'dead-list', 'time' );
		$time_start = '<a href="#' . $time_since['start'] . '">' . $time_since['start'] . '</a>';
		$time_end   = '<a href="#' . $time_since['end'] . '">' . $time_since['end'] . '</a>';
		?>
		<h3>List of All Dead Characters</h3>

		<p>The longest time span between character deaths is <strong><?php echo (int) $time_since['time']; ?> days</strong> (<?php echo wp_kses_post( $time_start ); ?> to <?php echo wp_kses_post( $time_end ); ?>). The shortest timespan is <strong>0 days</strong> (multiple characters have died on the same day).</p>

		<div class="container">
			<div class="row">
				<div class="col">
					<table id="DeadcharactersTable" class="tablesorter table table-striped table-hover">
						<thead class="thead-dark">
							<tr>
								<th style="width: 150px;" scope="col">Date</th>
								<th style="width: 125px;" scope="col">Days Since</th>
								<th scope="col">Character(s)</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$list_array = ( new LWTV_Statistics() )->generate( 'characters', 'dead-list', 'array' );
							foreach ( $list_array as $date => $list ) {
								?>
								<tr>
									<td><a name="<?php echo esc_html( $date ); ?>"><?php echo esc_html( $date ); ?></a></td>
									<td>
									<?php
										$since = ( isset( $list['since'] ) ) ? $list['since'] : 'n/a';
										echo esc_html( $since );
									?>
									</td>
									<td><ul>
										<?php
										foreach ( $list['chars'] as $char ) {
											echo '<li><a href="' . esc_url( $char['url'] ) . '">' . esc_html( $char['name'] ) . '</a></li>';
										}
										?>
									</ul></td>
								</tr>
								<?php
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<?php
		break;
}
