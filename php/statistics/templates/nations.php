<?php
/**
 * The template for displaying national statistics
 *
 * @package LezWatch.TV
 */

// Country
$sent_country  = get_query_var( 'country', '' );
$valid_country = term_exists( $sent_country, 'lez_country' );
$country       = ( '' === $sent_country || ! is_array( $valid_country ) ) ? 'all' : sanitize_title( $sent_country );

$valid_views = array(
	'sexuality'     => 'characters',
	'gender'        => 'characters',
	'tropes'        => 'shows',
	'intersections' => 'shows',
	'formats'       => 'shows',
	'on-air'        => 'shows',
);
$sent_view   = get_query_var( 'view', 'overview' );
$view        = ( ! array_key_exists( $sent_view, $valid_views ) ) ? 'overview' : $sent_view;

// Format
$valid_formats = array( 'bar', 'pie' );
$sent_format   = get_query_var( 'format', 'bar' );
$format        = ( ! in_array( $sent_format, $valid_formats, true ) ) ? 'bar' : $sent_format;

// Count
$nations     = get_terms(
	array(
		'taxonomy'   => 'lez_country',
		'hide_empty' => 0,
	)
);
$count       = wp_count_terms( 'lez_country' );
$shows_count = lwtv_plugin()->generate_statistics( 'shows', 'total', 'count' );

switch ( $country ) {
	case 'all':
		$title_country = 'All Countries (' . $count . ')';
		break;
	default:
		$characters     = lwtv_plugin()->generate_statistics( 'characters', 'country_' . $country . '_all', 'count' );
		$shows          = lwtv_plugin()->generate_statistics( 'shows', 'country_' . $country . '_all', 'count' );
		$country_object = get_term_by( 'slug', $country, 'lez_country', 'ARRAY_A' );
		$title_country  = '<a href="' . home_url( '/country/' . $country ) . '">' . $country_object['name'] . '</a> (' . $shows . ' Shows / ' . $characters . ' Characters)';
}

?>
<h2><?php echo wp_kses_post( $title_country ); ?></h2>

<form method="get" id="go">
	<div class="container-fluid text-center">
		<div class="row">
			<div class="col-8">
				<select name="country" id="country">
					<option value="all">Country (All)</option>
					<?php
					foreach ( $nations as $nation ) {
						$selected = ( $country === $nation->slug ) ? 'selected=selected' : '';
						$shows    = _n( 'Show', 'Shows', $nation->count );
						echo '<option value="' . esc_attr( $nation->slug ) . '" ' . esc_html( $selected ) . '>' . esc_html( $nation->name ) . '</option>';
					}
					?>
				</select>
			</div>
			<div class="col-2">
				<button type="submit" id="submit" class="btn btn-default btn-outline-primary">Go</button>
				<?php
				if ( 'all' !== $country ) {
					echo '&nbsp;<a class="btn btn-default btn-outline-primary" href="/statistics/nations/" role="button">Reset</a>';
				}
				?>
			</div>
		</div>
	</div>
</form>

<ul class="nav nav-tabs">
	<?php
	$baseurl   = '/statistics/nations/';
	$query_arg = array();
	if ( 'all' !== $country ) {
		$query_arg['country'] = $country;
	}

	echo '<li class="nav-item"><a class="nav-link' . esc_attr( ( 'overview' === $view ) ? ' active' : '' ) . '" href="' . esc_url( add_query_arg( $query_arg, $baseurl ) ) . '">OVERVIEW</a></li>';

	if ( 'all' !== $country ) {
		foreach ( $valid_views as $the_view => $the_post_type ) {
			$active = ( $view === $the_view ) ? ' active' : '';
			echo '<li class="nav-item"><a class="nav-link' . esc_attr( $active ) . '" href="' . esc_url( add_query_arg( $query_arg, $baseurl . $the_view . '/' ) ) . '">' . esc_html( strtoupper( str_replace( '-', ' ', $the_view ) ) ) . '</a></li>';
		}
	}
	?>
</ul>

<p>&nbsp;</p>

<?php
	$col_class = ( 'all' !== $country && 'overview' !== $view && 'on-air' !== $view ) ? 'col-sm-6' : 'col';
	$cpts_type = ( 'overview' === $view ) ? 'shows' : $valid_views[ $view ];
?>

<div class="container chart-container">

	<div class="row">
		<div class="<?php echo esc_attr( $col_class ); ?>">
		<?php

		// Reminder: country [subcountry] [view]
		$view    = ( 'overview' === $view ) ? '_all' : '_' . $view;
		$country = ( 'overview' === $country ) ? '_all' : '_' . $country;
		$format  = ( '_all' === $view ) ? 'barchart' : 'piechart';

		if ( '_all' === $country ) {
			// Always show the same thing here.
			$all_count = lwtv_plugin()->generate_shows_count( 'score', 'country', $nation->slug );
			?>
			<p>For more information on individual nations, please use the dropdown menu, or click on a nation listed below.</p>
			<table id="nationsTable" class="tablesorter table table-striped table-hover">
				<thead>
					<tr>
						<th scope="col">Country Name</th>
						<th scope="col">Shows</th>
						<th scope="col">Percentage (of all shows)</th>
						<th scope="col">Avg Score</th>
					</tr>
				</thead>
				<tbody>
				<?php
				foreach ( $nations as $nation ) {
					$percent = round( ( ( $nation->count / $shows_count ) * 100 ), 1 );
					echo '<tr>
						<th scope="row"><a href="?country=' . esc_attr( $nation->slug ) . '">' . esc_html( $nation->name ) . '</a></th>
						<td>' . (int) $nation->count . '</td>
						<td><div class="progress"><div class="progress-bar bg-info" role="progressbar" style="width: ' . esc_html( $percent ) . '%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div></div>&nbsp;' . esc_html( $percent ) . '%</td>
						<td>' . (int) $all_count . '</td>
					</tr>';
				}
				?>
				</tbody>
			</table>
			<?php
		} else {
			// We have a specific nation, let's show it.
			$format     = 'piechart';
			$onair      = lwtv_plugin()->generate_shows_count( 'onair', 'country', ltrim( $country, '_' ) );
			$allshows   = lwtv_plugin()->generate_shows_count( 'total', 'country', ltrim( $country, '_' ) );
			$showscore  = lwtv_plugin()->generate_shows_count( 'score', 'country', ltrim( $country, '_' ) );
			$onairscore = lwtv_plugin()->generate_shows_count( 'onairscore', 'country', ltrim( $country, '_' ) );

			if ( '_all' === $view ) {
				echo wp_kses_post( '<p>Currently, ' . $onair . ' of ' . $allshows . ' shows are on air. The average score for all shows in this country is ' . $showscore );

				if ( 0 !== $onair ) {
					echo wp_kses_post( ', and ' . $onairscore . ' for shows currently on air' );
				}

				echo wp_kses_post( ' (out of a possible 100).</p>' );

				$format = 'barchart';
			}

			if ( '_on-air' === $view ) {
				$format = 'trendline';
				echo wp_kses_post( '<h3>Shows On-Air Per Year</h3>' );
			}

			lwtv_plugin()->generate_statistics( $cpts_type, 'country' . $country . $view, $format );
		}
		?>
		</div>

	<?php
	if ( '_all' !== $country && '_all' !== $view && '_on-air' !== $view ) {
		$format = ( 'shows' === $cpts_type ) ? 'list' : 'percentage';
		?>
		<div class="<?php echo esc_attr( $col_class ); ?>">
			<?php lwtv_plugin()->generate_statistics( $cpts_type, 'country' . $country . $view, $format ); ?>
		</div>
		<?php
	}
	?>
	</div>
</div>
