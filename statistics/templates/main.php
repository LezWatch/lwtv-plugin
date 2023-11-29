<?php
/**
 * The template for displaying the main stats page
 *
 * @package LezWatch.TV
 */

$characters = ( new LWTV_Statistics() )->generate( 'characters', 'total', 'count' );
$shows      = ( new LWTV_Statistics() )->generate( 'shows', 'total', 'count' );
$actors     = ( new LWTV_Statistics() )->generate( 'actors', 'total', 'count' );
$dead_chars = ( new LWTV_Statistics() )->generate( 'characters', 'dead', 'count' );
?>
<h2><a name="overview">Overview</a></h2>

<div class="container">
	<div class="row">
		<div class="col">
			<div class="card text-center">
				<h3 class="card-header shows">Shows</h3>
				<div class="card-body bg-light">
					<h5 class="card-title"><?php echo (int) $shows; ?></h5>
					<a href="shows" class="btn btn-primary btn-sm">Show Statistics</a>
				</div>
			</div>
		</div>
		<div class="col">
			<div class="card text-center">
				<h3 class="card-header characters">Characters</h3>
				<div class="card-body bg-light">
					<h5 class="card-title"><?php echo (int) $characters; ?></h5>
					<a href="characters" class="btn btn-primary btn-sm">Character Statistics</a>
				</div>
			</div>
		</div>
		<div class="col">
			<div class="card text-center">
				<h3 class="card-header actors">Actors</h3>
				<div class="card-body bg-light">
					<h5 class="card-title"><?php echo (int) $actors; ?></h5>
					<a href="actors" class="btn btn-primary btn-sm">Actor Statistics</a>
				</div>
			</div>
		</div>
		<div class="col">
			<div class="card text-center">
				<h3 class="card-header dead-characters">Dead Characters</h3>
				<div class="card-body bg-light">
					<h5 class="card-title"><?php echo (int) $dead_chars; ?></h5>
					<a href="death" class="btn btn-primary btn-sm">Death Statistics</a>
				</div>
			</div>
		</div>
	</div>
</div>

<p>&nbsp;</p>

<div class="container">
	<div class="row">
		<div class="col">
			<h4>Top Ten Nations</h4>
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th scope="col">&nbsp;</th>
						<th scope="col">Shows</th>
						<th scope="col">Percent</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$nations = get_terms(
						array(
							'taxonomy'   => 'lez_country',
							'number'     => 10,
							'orderby'    => 'count',
							'hide_empty' => 0,
							'order'      => 'DESC',
						)
					);
					foreach ( $nations as $nation ) {
						$percent = round( ( ( $nation->count / $shows ) * 100 ), 1 );
						echo '<tr>
								<th scope="row"><a href="' . esc_url( site_url( 'statistics/nations/?country=' . $nation->slug ) ) . '">' . esc_html( $nation->name ) . '</a></th>
								<td>' . (int) $nation->count . '</td>
								<td><div class="progress"><div class="progress-bar" role="progressbar" style="width: ' . esc_html( $percent ) . '%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div></div>&nbsp;' . esc_html( $percent ) . '%</td>
							</tr>';
					}
					?>
				</tbody>
			</table>
			<a href="nations"><button type="button" class="btn btn-lg btn-block">All <?php echo (int) wp_count_terms( 'lez_country' ); ?> Nations</button></a>
		</div>

		<div class="col">
			<h4>Top Ten Stations and Networks</h4>
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th scope="col">&nbsp;</th>
						<th scope="col">Shows</th>
						<th scope="col">Percent</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$stations = get_terms(
						array(
							'taxonomy'   => 'lez_stations',
							'number'     => 10,
							'orderby'    => 'count',
							'hide_empty' => 0,
							'order'      => 'DESC',
						)
					);
					foreach ( $stations as $station ) {
						$percent = round( ( ( $station->count / $shows ) * 100 ), 1 );
						echo '<tr>
								<th scope="row"><a href="' . esc_url( site_url( 'statistics/stations/?station=' . $station->slug ) ) . '">' . esc_html( $station->name ) . '</a></th>
								<td>' . (int) $station->count . '</td>
								<td><div class="progress"><div class="progress-bar" role="progressbar" style="width: ' . esc_html( $percent ) . '%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div></div>&nbsp;' . esc_html( $percent ) . '%</td>
							</tr>';
					}
					?>
				</tbody>
			</table>
			<a href="stations"><button type="button" class="btn btn-lg btn-block">All <?php echo (int) wp_count_terms( 'lez_stations' ); ?> Stations</button></a>
		</div>
	</div>
</div>
