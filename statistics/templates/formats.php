<?php
/**
 * The template for displaying formats statistics
 *
 * @package LezWatch.TV
 */

// showform
$valid_showform = ( isset( $_GET['showform'] ) ) ? term_exists( $_GET['showform'], 'lez_formats' ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
$showform       = ( ! isset( $_GET['showform'] ) || ! is_array( $valid_showform ) ) ? 'all' : sanitize_title( $_GET['showform'] ); // phpcs:ignore WordPress.Security.NonceVerification

// Views
$valid_views = array(
	'overview'      => 'shows',
	'sexuality'     => 'characters',
	'gender'        => 'characters',
	'tropes'        => 'shows',
	'intersections' => 'shows',
);
$view        = ( ! isset( $_GET['view'] ) || ( ! array_key_exists( $_GET['view'], $valid_views ) ) ) ? 'overview' : sanitize_title( $_GET['view'] ); // phpcs:ignore WordPress.Security.NonceVerification

// Format
$valid_formats = array( 'bar', 'pie' );
$format        = ( ! isset( $_GET['format'] ) || ! in_array( $_GET['format'], $valid_formats, true ) ) ? 'bar' : sanitize_title( $_GET['format'] ); // phpcs:ignore WordPress.Security.NonceVerification

// Count
$showforms   = get_terms( 'lez_formats', array( 'hide_empty' => 0 ) );
$count       = wp_count_terms( 'lez_formats' );
$shows_count = LWTV_Stats::generate( 'shows', 'total', 'count' );

// Current URL
$current_url = add_query_arg( $_SERVER['QUERY_STRING'], '', home_url( $wp->request ) );

switch ( $showform ) {
	case 'all':
		$title_showform = 'All Show Formats (' . $count . ')';
		break;
	default:
		$characters     = LWTV_Stats::generate( 'characters', 'formats_' . $showform . '_all', 'count' );
		$shows          = LWTV_Stats::generate( 'shows', 'formats_' . $showform . '_all', 'count' );
		$showform_obj   = get_term_by( 'slug', $showform, 'lez_formats', 'ARRAY_A' );
		$title_showform = '<a href="' . home_url( '/format/' . $showform ) . '">' . $showform_obj['name'] . '</a> (' . $shows . ' Shows / ' . $characters . ' Characters)';
}

?>

<h2><?php echo wp_kses_post( $title_showform ); ?></h2>

<section id="toc" class="toc-container card-body">
	<nav class="breadcrumb">
		<form method="get" id="go" class="form-inline">
			<input type="hidden" name="view" value="<?php echo esc_html( $view ); ?>">
			<div class="form-group">
				<select name="showform" id="showform" class="form-control">
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
			<div class="form-group">
				<button type="submit" id="submit" class="btn btn-default">Go</button>
			</div>
		</form>
	</nav>
</section>

<ul class="nav nav-tabs">
	<?php
	foreach ( $valid_views as $the_view => $the_post_type ) {
		$active = ( $view === $the_view ) ? ' active' : '';
		echo '<li class="nav-item"><a class="nav-link' . esc_attr( $active ) . '" href="' . esc_attr( add_query_arg( 'view', $the_view, $current_url ) ) . '">' . esc_html( strtoupper( str_replace( '-', ' ', $the_view ) ) ) . '</a></li>';
	}
	?>
</ul>

<p>&nbsp;</p>

<?php
	$col_class = ( 'all' !== $showform && 'overview' !== $view ) ? 'col-sm-6' : 'col';
	$cpts_type = $valid_views[ $view ];
?>

<div class="container chart-container">

	<?php
	if ( 'all' !== $showform && 'overview' !== $view ) {
		echo wp_kses_post( lwtv_yikes_statistics_description( 'format', $cpts_type, $view ) );
	}
	?>

	<div class="row">
		<div class="<?php echo esc_attr( $col_class ); ?>">
		<?php

		// Reminder: format [subform] [view]
		$view     = ( 'overview' === $view ) ? '_all' : '_' . $view;
		$showform = ( 'overview' === $showform ) ? '_all' : '_' . $showform;
		$format   = ( '_all' === $view ) ? 'barchart' : 'piechart';

		if ( '_all' === $showform ) {
			if ( '_all' === $view ) {
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
						echo '<tr>
							<th scope="row"><a href="?view=overview&showform=' . esc_attr( $a_form->slug ) . '">' . esc_html( $a_form->name ) . '</a></th>
							<td>' . (int) $a_form->count . '</td>
							<td>' . esc_html( round( ( ( $a_form->count / $shows_count ) * 100 ), 1 ) ) . '%</td>
							<td>' . (int) LWTV_Stats::showcount( 'score', 'formats', $a_form->slug ) . '</td>
						</tr>';
					}
					?>
					</tbody>
				</table>
				<?php
			} else {
				$this_one_view = substr( $view, 1 );
				if ( 'shows' !== $valid_views[ $this_one_view ] ) {
					LWTV_Stats::generate( $cpts_type, 'formats' . $showform . $view, 'stackedbar' );
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
			$onair      = LWTV_Stats::showcount( 'onair', 'formats', ltrim( $showform, '_' ) );
			$allshows   = LWTV_Stats::showcount( 'total', 'formats', ltrim( $showform, '_' ) );
			$showscore  = LWTV_Stats::showcount( 'score', 'formats', ltrim( $showform, '_' ) );
			$onairscore = LWTV_Stats::showcount( 'onairscore', 'formats', ltrim( $showform, '_' ) );

			if ( '_all' === $view ) {
				echo wp_kses_post( '<p>Currently, ' . $onair . ' of ' . $allshows . ' shows are on air. The average score for all shows in this format is ' . $showscore . ', and ' . $onairscore . ' for shows currently on air (out of a possible 100).</p>' );
			}

			LWTV_Stats::generate( $cpts_type, 'formats' . $showform . $view, $format );
		}
		?>
		</div>

	<?php
	if ( '_all' !== $showform && '_all' !== $view ) {
		$format = ( 'shows' === $cpts_type ) ? 'list' : 'percentage';
		?>
		<div class="<?php echo esc_attr( $col_class ); ?>">
			<?php LWTV_Stats::generate( $cpts_type, 'formats' . $showform . $view, $format ); ?>
		</div>
		<?php
	}
	?>
	</div>
</div>
