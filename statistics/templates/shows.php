<?php
/**
 * The template for displaying the shows stats page
 *
 * @package LezWatch.TV
 */


$valid_views = array( 'formats', 'tropes', 'genres', 'intersectionality', 'stars', 'triggers', 'on-air', 'worth-it', 'we-love-it' );
$sent_view       = get_query_var( 'view', 'overview' );
$view            = ( ! in_array( $sent_view, $valid_views, true ) ) ? 'overview' : $sent_view;

?>
<h2>
	<a href="/shows/">Total Shows</a> (<?php echo (int) ( new LWTV_Stats() )->generate( 'shows', 'total', 'count' ); ?>)
</h2>

<ul class="nav nav-tabs">
	<?php
	$baseurl = '/statistics/shows/';
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
	case 'overview':
		?>
		<div class="container">
			<div class="row">
				<div class="col">
					<div class="card text-center">
						<h3 class="card-header alert-success">Shows</h3>
						<div class="card-body bg-light">
							<h5 class="card-title"><?php echo (int) ( new LWTV_Stats() )->generate( 'shows', 'total', 'count' ); ?></h5>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="card text-center">
						<h3 class="card-header alert-info">Tropes</h3>
						<div class="card-body bg-light">
							<h5 class="card-title"><?php echo (int) wp_count_terms( 'lez_tropes' ); ?></h5>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="card text-center">
						<h3 class="card-header alert-warning">Genres</h3>
						<div class="card-body bg-light">
							<h5 class="card-title"><?php echo (int) wp_count_terms( 'lez_genres' ); ?></h5>
						</div>
					</div>
				</div>
			</div>
		</div>

		<p>&nbsp;<br/>The average show score is <strong><?php ( new LWTV_Stats() )->generate( 'shows', 'scores', 'average' ); ?></strong>. The lowest score is <strong><?php ( new LWTV_Stats() )->generate( 'shows', 'scores', 'low' ); ?></strong> and the highest is <strong><?php ( new LWTV_Stats() )->generate( 'shows', 'scores', 'high' ); ?></strong>.</p>

		<div class="container">
			<div class="row">
				<div class="col">
					<h4>Top Tropes</h4>
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th scope="col">Trope</th>
								<th scope="col">Shows</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$tropes = get_terms(
								'lez_tropes',
								array(
									'number'     => 10,
									'orderby'    => 'count',
									'hide_empty' => 0,
									'order'      => 'DESC',
								)
							);
							foreach ( $tropes as $trope ) {
								echo '<tr>
										<th scope="row"><a href="' . esc_url( site_url( '/trope/' . $trope->slug ) ) . '">' . esc_html( $trope->name ) . '</a></th>
										<td>' . (int) $trope->count . '</td>
									</tr>';
							}
							?>
						</tbody>
					</table>
					<a href="?view=tropes"><button type="button" class="btn btn-info btn-lg btn-block">All <?php echo (int) wp_count_terms( 'lez_tropes' ); ?> Tropes</button></a>
				</div>

				<div class="col">
					<h4>Top Genres</h4>
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th scope="col">Genre</th>
								<th scope="col">Show</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$genres = get_terms(
								'lez_genres',
								array(
									'number'     => 10,
									'orderby'    => 'count',
									'hide_empty' => 0,
									'order'      => 'DESC',
								)
							);
							foreach ( $genres as $genre ) {
								echo '<tr>
										<th scope="row"><a href="' . esc_url( site_url( '/genre/' . $genre->slug ) ) . '">' . esc_html( $genre->name ) . '</a></th>
										<td>' . (int) $genre->count . '</td>
									</tr>';
							}
							?>
						</tbody>
					</table>
					<a href="?view=genres"><button type="button" class="btn btn-info btn-lg btn-block">All <?php echo (int) wp_count_terms( 'lez_genres' ); ?> Genres</button></a>
				</div>
			</div>
		</div>
		<?php
		break;
	case 'tropes':
		?>
		<div class="container chart-container">
			<ul class="nav nav-pills nav-fill" id="v-pills-tab" role="tablist">
				<li class="nav-item"><a class="nav-link active" id="v-pills-barchart-tab" data-toggle="pill" href="#v-pills-barchart" role="tab" aria-controls="v-pills-barchart" aria-selected="true">Barchart</a></li>
				<li class="nav-item"><a class="nav-link" id="v-pills-list-tab" data-toggle="pill" href="#v-pills-list" role="tab" aria-controls="v-pills-list" aria-selected="false">List</a></li>
			</ul>
			<p>&nbsp;</p>
			<div class="tab-content" id="v-pills-tabContent">
				<div class="tab-pane fade show active" id="v-pills-barchart" role="tabpanel" aria-labelledby="v-pills-barchart-tab"><?php ( new LWTV_Stats() )->generate( 'shows', 'tropes', 'barchart' ); ?></div>
				<div class="tab-pane fade" id="v-pills-list" role="tabpanel" aria-labelledby="v-pills-list-tab"><?php ( new LWTV_Stats() )->generate( 'shows', 'tropes', 'list' ); ?></div>
			</div>
		</div>
		<?php
		break;
	case 'genres':
		?>
		<div class="container chart-container">
			<ul class="nav nav-pills nav-fill" id="v-pills-tab" role="tablist">
				<li class="nav-item"><a class="nav-link active" id="v-pills-barchart-tab" data-toggle="pill" href="#v-pills-barchart" role="tab" aria-controls="v-pills-barchart" aria-selected="true">Barchart</a></li>
				<li class="nav-item"><a class="nav-link" id="v-pills-list-tab" data-toggle="pill" href="#v-pills-list" role="tab" aria-controls="v-pills-list" aria-selected="false">List</a></li>
			</ul>
			<p>&nbsp;</p>
			<div class="tab-content" id="v-pills-tabContent">
				<div class="tab-pane fade show active" id="v-pills-barchart" role="tabpanel" aria-labelledby="v-pills-barchart-tab"><?php ( new LWTV_Stats() )->generate( 'shows', 'genres', 'barchart' ); ?></div>
				<div class="tab-pane fade" id="v-pills-list" role="tabpanel" aria-labelledby="v-pills-list-tab"><?php ( new LWTV_Stats() )->generate( 'shows', 'genres', 'list' ); ?></div>
			</div>
		</div>
		<?php
		break;
	case 'worth-it':
		?>
		<div class="container chart-container">
			<div class="row">
				<div class="col-sm-6">
					<?php ( new LWTV_Stats() )->generate( 'shows', 'thumbs', 'piechart' ); ?>
				</div>
				<div class="col-sm-6">
					<?php ( new LWTV_Stats() )->generate( 'shows', 'thumbs', 'percentage' ); ?>
				</div>
			</div>
		</div>
		<?php
		break;
	case 'stars':
		?>
		<div class="container chart-container">
			<div class="row">
				<div class="col-sm-6">
					<?php ( new LWTV_Stats() )->generate( 'shows', 'stars', 'piechart' ); ?>
				</div>
				<div class="col-sm-6">
					<?php ( new LWTV_Stats() )->generate( 'shows', 'stars', 'percentage' ); ?>
				</div>
			</div>
		</div>
		<?php
		break;

	case 'intersectionality':
		?>
		<div class="container chart-container">
			<ul class="nav nav-pills nav-fill" id="v-pills-tab" role="tablist">
				<li class="nav-item"><a class="nav-link active" id="v-pills-barchart-tab" data-toggle="pill" href="#v-pills-barchart" role="tab" aria-controls="v-pills-barchart" aria-selected="true">Barchart</a></li>
				<li class="nav-item"><a class="nav-link" id="v-pills-list-tab" data-toggle="pill" href="#v-pills-list" role="tab" aria-controls="v-pills-list" aria-selected="false">List</a></li>
			</ul>
			<p>&nbsp;</p>
			<div class="tab-content" id="v-pills-tabContent">
				<div class="tab-pane fade show active" id="v-pills-barchart" role="tabpanel" aria-labelledby="v-pills-barchart-tab"><?php ( new LWTV_Stats() )->generate( 'shows', 'intersections', 'barchart' ); ?></div>
				<div class="tab-pane fade" id="v-pills-list" role="tabpanel" aria-labelledby="v-pills-list-tab"><?php ( new LWTV_Stats() )->generate( 'shows', 'intersections', 'list' ); ?></div>
			</div>
		</div>
		<?php
		break;
	case 'formats':
		?>
		<div class="container chart-container">
			<div class="row">
				<div class="col-sm-6">
					<?php ( new LWTV_Stats() )->generate( 'shows', 'formats', 'piechart' ); ?>
				</div>
				<div class="col-sm-6">
					<?php ( new LWTV_Stats() )->generate( 'shows', 'formats', 'percentage' ); ?>
				</div>
			</div>
		</div>
		<?php
		break;
	case 'triggers':
		?>
		<div class="container chart-container">
			<div class="row">
				<div class="col-sm-6">
					<?php ( new LWTV_Stats() )->generate( 'shows', 'triggers', 'piechart' ); ?>
				</div>
				<div class="col-sm-6">
					<?php ( new LWTV_Stats() )->generate( 'shows', 'triggers', 'percentage' ); ?>
				</div>
			</div>
		</div>
		<?php
		break;
	case 'we-love-it':
		?>
		<div class="container chart-container">
			<div class="row">
				<div class="col-sm-6">
					<?php ( new LWTV_Stats() )->generate( 'shows', 'weloveit', 'piechart' ); ?>
				</div>
				<div class="col-sm-6">
					<?php ( new LWTV_Stats() )->generate( 'shows', 'weloveit', 'percentage' ); ?>
				</div>
			</div>
		</div>
		<?php
		break;
	case 'on-air':
		?>
		<div class="container chart-container">
			<div class="row">
				<div class="col-sm-6">
					<?php ( new LWTV_Stats() )->generate( 'shows', 'current', 'piechart' ); ?>
				</div>
				<div class="col-sm-6">
					<?php ( new LWTV_Stats() )->generate( 'shows', 'current', 'percentage' ); ?>
				</div>
			</div>
		</div>
		<?php
		break;
}
