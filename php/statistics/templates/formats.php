<?php
/**
 * The template for displaying formats statistics
 *
 * @package LezWatch.TV
 */

// showform
$sent_form      = get_query_var( 'showform', '' );
$valid_showform = term_exists( $sent_form, 'lez_formats' );
$showform       = ( '' === $sent_form || ! is_array( $valid_showform ) ) ? 'all' : sanitize_title( $sent_form );

// Views
$valid_views = array(
	// taxonomy     => CPT
	'sexuality'     => 'characters',
	'gender'        => 'characters',
	'tropes'        => 'shows',
	'intersections' => 'shows',
);
$sent_view   = get_query_var( 'view', 'overview' );
$view        = ( ! array_key_exists( $sent_view, $valid_views ) ) ? 'overview' : $sent_view;

// Format
$valid_formats = array( 'bar', 'pie' );
$sent_format   = get_query_var( 'format', 'bar' );
$format        = ( ! in_array( $sent_format, $valid_formats, true ) ) ? 'bar' : $sent_format;

// Count
$showforms   = get_terms(
	array(
		'taxonomy'   => 'lez_formats',
		'hide_empty' => 0,
	)
);
$count       = wp_count_terms( 'lez_formats' );
$shows_count = lwtv_plugin()->generate_statistics( 'shows', 'total', 'count' );

switch ( $showform ) {
	case 'all':
		$title_showform = 'All Show Formats (' . $count . ')';
		break;
	default:
		$characters     = lwtv_plugin()->generate_statistics( 'characters', 'formats_' . $showform . '_all', 'count' );
		$shows          = lwtv_plugin()->generate_statistics( 'shows', 'formats_' . $showform . '_all', 'count' );
		$showform_obj   = get_term_by( 'slug', $showform, 'lez_formats', 'ARRAY_A' );
		$title_showform = '<a href="/format/' . $showform . '">' . $showform_obj['name'] . '</a> (' . $shows . ' Shows / ' . $characters . ' Characters)';
}

?>
<h2><?php echo wp_kses_post( $title_showform ); ?></h2>

<form method="get" id="go">
	<div class="container-fluid text-center">
		<div class="row">
			<div class="col-8">
				<select name="showform" id="showform">
					<option value="all">Show Formats (All)</option>
					<?php
					foreach ( $showforms as $a_form ) {
						$selected = ( $showform === $a_form->slug ) ? 'selected=selected' : '';
						$shows    = _n( 'Show', 'Shows', $a_form->count );
						echo '<option value="' . esc_attr( $a_form->slug ) . '" ' . esc_html( $selected ) . '>' . esc_html( $a_form->name ) . '</option>';
					}
					?>
				</select>
			</div>
			<div class="col-2">
				<button type="submit" id="submit" class="btn btn-default btn-outline-primary">Go</button>
				<?php
				if ( 'all' !== $showform ) {
					echo '&nbsp;<a class="btn btn-default btn-outline-primary" href="/statistics/formats/" role="button">Reset</a>';
				}
				?>
			</div>
		</div>
	</div>
</form>

<ul class="nav nav-tabs">
	<?php
	$baseurl   = '/statistics/formats/';
	$query_arg = array();
	if ( 'all' !== $showform ) {
		$query_arg['showform'] = $showform;
	}

	echo '<li class="nav-item"><a class="nav-link' . esc_attr( ( 'overview' === $view ) ? ' active' : '' ) . '" href="' . esc_url( add_query_arg( $query_arg, $baseurl ) ) . '">OVERVIEW</a></li>';
	if ( 'all' !== $showform ) {
		foreach ( $valid_views as $the_view => $the_post_type ) {
			$active = ( $view === $the_view ) ? ' active' : '';
			echo '<li class="nav-item"><a class="nav-link' . esc_attr( $active ) . '" href="' . esc_url( add_query_arg( $query_arg, $baseurl . $the_view . '/' ) ) . '">' . esc_html( strtoupper( str_replace( '-', ' ', $the_view ) ) ) . '</a></li>';
		}
	}
	?>
</ul>

<p>&nbsp;</p>

<?php
	$col_class = ( 'all' !== $showform && 'overview' !== $view ) ? 'col-sm-6' : 'col';
	$cpts_type = ( 'overview' === $view ) ? 'shows' : $valid_views[ $view ];
?>

<div class="container chart-container">

	<div class="row">
		<div class="<?php echo esc_attr( $col_class ); ?>">
		<?php

		// Reminder: format [subform] [view]
		$view     = ( 'overview' === $view ) ? '_all' : '_' . $view;
		$showform = ( 'overview' === $showform ) ? '_all' : '_' . $showform;
		$format   = ( '_all' === $view ) ? 'barchart' : 'piechart';

		if ( '_all' === $showform ) {
			if ( '_all' === $view ) {
				$all_count = lwtv_plugin()->generate_shows_count( 'score', 'formats', $a_form->slug );
				?>
				<p>For more information on individual show formats, please use the dropdown menu, or click on a format type listed below.</p>
				<table id="formatTable" class="tablesorter table table-striped table-hover">
					<thead>
						<tr>
							<th scope="col">Format</th>
							<th scope="col">Shows</th>
							<th scope="col">Percentage (of all shows)</th>
							<th scope="col">Avg Score</th>
						</tr>
					</thead>
					<tbody>
					<?php
					foreach ( $showforms as $a_form ) {
						$percent = round( ( ( $a_form->count / $shows_count ) * 100 ), 1 );
						echo '<tr>
							<th scope="row"><a href="?showform=' . esc_attr( $a_form->slug ) . '">' . esc_html( $a_form->name ) . '</a></th>
							<td>' . (int) $a_form->count . '</td>
							<td><div class="progress"><div class="progress-bar bg-info" role="progressbar" style="width: ' . esc_html( $percent ) . '%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div></div>&nbsp;' . esc_html( $percent ) . '%</td>
							<td>' . (int) $all_count . '</td>
						</tr>';
					}
					?>
					</tbody>
				</table>
				<?php
			} else {
				$this_one_view = substr( $view, 1 );
				if ( 'shows' !== $valid_views[ $this_one_view ] ) {
					lwtv_plugin()->generate_statistics( $cpts_type, 'formats' . $showform . $view, 'stackedbar' );
				} else {
					?>
					<div class="row">
						<div class="col-sm-6">
							<?php lwtv_plugin()->generate_statistics( 'shows', $this_one_view, 'piechart' ); ?>
						</div>
						<div class="col-sm-6">
							<?php lwtv_plugin()->generate_statistics( 'shows', $this_one_view, 'percentage' ); ?>
						</div>
					</div>
					<?php
				}
			}
		} else {
			$onair      = lwtv_plugin()->generate_shows_count( 'onair', 'formats', ltrim( $showform, '_' ) );
			$allshows   = lwtv_plugin()->generate_shows_count( 'total', 'formats', ltrim( $showform, '_' ) );
			$showscore  = lwtv_plugin()->generate_shows_count( 'score', 'formats', ltrim( $showform, '_' ) );
			$onairscore = lwtv_plugin()->generate_shows_count( 'onairscore', 'formats', ltrim( $showform, '_' ) );
			$type_name  = ( ! str_ends_with( $showform_obj['name'], 's' ) ) ? $showform_obj['name'] . 's' : $showform_obj['name'];

			if ( '_all' === $view ) {
				echo wp_kses_post( '<p>Currently, ' . $onair . ' of ' . $allshows . ' shows identified as ' . $type_name . ' are on air. Their average score is ' . $showscore . ', and ' . $onairscore . ' for ' . $showform_obj['name'] . 's currently on air (out of a possible 100).</p>' );
			}

			lwtv_plugin()->generate_statistics( $cpts_type, 'formats' . $showform . $view, $format );
		}
		?>
		</div>

	<?php
	if ( '_all' !== $showform && '_all' !== $view ) {
		$format = ( 'shows' === $cpts_type ) ? 'list' : 'percentage';
		?>
		<div class="<?php echo esc_attr( $col_class ); ?>">
			<?php lwtv_plugin()->generate_statistics( $cpts_type, 'formats' . $showform . $view, $format ); ?>
		</div>
		<?php
	}
	?>
	</div>
</div>
