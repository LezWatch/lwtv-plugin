<?php
/**
 * The template for displaying station statistics
 *
 * @package LezWatch.TV
 */

// Stations
$sent_station  = get_query_var( 'station', '' );
$valid_station = term_exists( $sent_station, 'lez_stations' );
$station       = ( '' === $sent_station || ! is_array( $valid_station ) ) ? 'all' : sanitize_title( $sent_station );

// Views
$valid_views = array(
	'sexuality' => 'characters',
	'gender'    => 'characters',
	'tropes'    => 'shows',
	// for now we're removing this: 'intersections' => 'shows',
	'formats'   => 'shows',
);
$sent_view   = get_query_var( 'view', 'overview' );
$view        = ( ! array_key_exists( $sent_view, $valid_views ) ) ? 'overview' : $sent_view;

// Count
$all_stations = get_terms( 'lez_stations', array( 'hide_empty' => 0 ) );
$count        = wp_count_terms( 'lez_stations' );
$shows_count  = LWTV_Stats::generate( 'shows', 'total', 'count' );

// Title
switch ( $station ) {
	case 'all':
		$title_station = 'All Stations (' . $count . ')';
		break;
	default:
		$characters     = LWTV_Stats::generate( 'characters', 'stations_' . $station . '_all', 'count' );
		$shows          = LWTV_Stats::generate( 'shows', 'stations_' . $station . '_all', 'count' );
		$station_object = get_term_by( 'slug', $station, 'lez_stations', 'ARRAY_A' );
		$title_station  = '<a href="' . home_url( '/station/' . $station ) . '">' . $station_object['name'] . '</a> (' . $shows . ' Shows / ' . $characters . ' Characters)';
}
?>

<h2><?php echo wp_kses_post( $title_station ); ?></h2>

<section id="toc" class="toc-container card-body">
	<nav class="breadcrumb">
		<form method="get" id="go" class="form-inline">
			<div class="form-group">
				<select name="station" id="station" class="form-control">
					<option value="all">All Stations</option>
					<?php
					foreach ( $all_stations as $the_station ) {
						$selected = ( $station === $the_station->slug ) ? 'selected=selected' : '';
						echo '<option value="' . esc_attr( $the_station->slug ) . '" ' . esc_html( $selected ) . '>' . esc_html( $the_station->name ) . '</option>';
					}
					?>
				</select>
			</div>
			<div class="form-group">
				<button type="submit" id="submit" class="btn btn-default">Go</button>
				<?php
				if ( 'all' !== $station ) {
					echo '<a class="btn btn-default" href="/statistics/stations/" role="button">Reset</a>';
				}
				?>
			</div>
		</form>
	</nav>
</section>

<ul class="nav nav-tabs">
	<?php
	$baseurl   = '/statistics/stations/';
	$query_arg = array();
	if ( 'all' !== $station ) {
		$query_arg['station'] = $station;
	}

	echo '<li class="nav-item"><a class="nav-link' . esc_attr( ( 'overview' === $view ) ? ' active' : '' ) . '" href="' . esc_url( add_query_arg( $query_arg, $baseurl ) ) . '">OVERVIEW</a></li>';
	foreach ( $valid_views as $the_view => $the_post_type ) {
		$active = ( $view === $the_view ) ? ' active' : '';
		echo '<li class="nav-item"><a class="nav-link' . esc_attr( $active ) . '" href="' . esc_url( add_query_arg( $query_arg, $baseurl . $the_view . '/' ) ) . '">' . esc_html( strtoupper( str_replace( '-', ' ', $the_view ) ) ) . '</a></li>';
	}
	?>
</ul>

<p>&nbsp;</p>

<?php
	$col_class = ( 'all' !== $station && 'overview' !== $view ) ? 'col-sm-6' : 'col';
	$cpts_type = ( 'overview' === $view ) ? 'shows' : $valid_views[ $view ];
?>

<div class="container">
	<div class="row">
		<div class="<?php echo esc_attr( $col_class ); ?>">
		<?php
		$view = ( 'overview' === $view && 'all' !== $station ) ? 'all' : $view;
		// Remember: station [substation] [view]
		$view    = ( 'overview' === $view ) ? '_all' : '_' . $view;
		$station = ( 'overview' === $station ) ? '_all' : '_' . $station;

		if ( '_all' === $station ) {
			if ( '_all' === $view ) {
				?>
				<p>For more information on individual stations, please use the dropdown menu, or click on a station listed below.</p>
				<table id="stationsTable" class="tablesorter table table-striped table-hover">
					<thead>
						<tr>
							<th scope="col">Station Name</th>
							<th scope="col">Total Shows</th>
							<th scope="col">Percentage (of all shows)</th>
							<th scope="col">Avg Score</th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ( $all_stations as $the_station ) {
							$percent = round( ( ( $the_station->count / $shows_count ) * 100 ), 1 );
							echo '<tr>
									<th scope="row"><a href="?station=' . esc_attr( $the_station->slug ) . '">' . esc_html( $the_station->name ) . '</a></th>
									<td>' . (int) $the_station->count . '</td>
									<td><div class="progress"><div class="progress-bar bg-info" role="progressbar" style="width: ' . esc_html( $percent ) . '%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">&nbsp;' . esc_html( $percent ) . '%</div></div></td>
									<td>' . (int) LWTV_Stats::showcount( 'score', 'stations', $the_station->slug ) . '</td>
								</tr>';
						}
						?>
					</tbody>
				</table>
				<?php
			} else {
				$this_one_view = substr( $view, 1 );
				if ( 'shows' !== $valid_views[ $this_one_view ] ) {
					LWTV_Stats::generate( $cpts_type, 'stations' . $station . $view, 'stackedbar' );
				} else {
					?>
					<div class="row">
						<div class="col-sm-6">
							<?php LWTV_Stats::generate( 'shows', $this_one_view, 'piechart' ); ?>
						</div>
						<div class="col-sm-6">
							<?php LWTV_Stats::generate( 'shows', $this_one_view, 'percentage' ); ?>
						</div>
					</div>
					<?php
				}
			}
		} else {
			$format = 'piechart';

			if ( '_all' !== $station ) {

				$onair      = LWTV_Stats::showcount( 'onair', 'stations', ltrim( $station, '_' ) );
				$allshows   = LWTV_Stats::showcount( 'total', 'stations', ltrim( $station, '_' ) );
				$showscore  = LWTV_Stats::showcount( 'score', 'stations', ltrim( $station, '_' ) );
				$onairscore = LWTV_Stats::showcount( 'onairscore', 'stations', ltrim( $station, '_' ) );

				if ( '_all' === $view ) {
					echo wp_kses_post( '<p>Currently, ' . $onair . ' of ' . $allshows . ' shows are on air. The average score for all shows in this station is ' . $showscore . ', and ' . $onairscore . ' for shows currently on air (out of a possible 100).</p>' );
					$format = 'barchart';
				}
			}

			LWTV_Stats::generate( $cpts_type, 'stations' . $station . $view, $format );
		}
		?>
		</div>

	<?php
	if ( '_all' !== $station && '_all' !== $view ) {
		$format = ( 'shows' === $cpts_type ) ? 'list' : 'percentage';
		?>
		<div class="<?php echo esc_attr( $col_class ); ?>">
			<?php LWTV_Stats::generate( $cpts_type, 'stations' . $station . $view, $format ); ?>
		</div>
		<?php
	}
	?>

	</div>
</div>
