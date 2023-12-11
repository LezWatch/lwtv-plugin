<?php
/**
 * The template for displaying the actor stats page
 *
 * @package LezWatch.TV
 */

// if this file is called directly abort
if ( ! defined( 'WPINC' ) ) {
	die;
}

$valid_views = array( 'gender', 'sexuality', 'roles' );
$sent_view   = get_query_var( 'view', 'overview' );
$view        = ( ! in_array( $sent_view, $valid_views, true ) ) ? 'overview' : $sent_view;
?>
<h2>
	<a href="/actors/">Total Actors</a> (<?php echo lwtv_plugin()->generate_statistics( 'actors', 'total', 'count' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>)
</h2>

<ul class="nav nav-tabs">
	<?php
	$baseurl = '/statistics/actors/';
	echo '<li class="nav-item"><a class="nav-link' . esc_attr( ( 'overview' === $view ) ? ' active' : '' ) . '" href="' . esc_attr( $baseurl ) . '">OVERVIEW</a></li>';
	foreach ( $valid_views as $the_view ) {
		$active = ( $view === $the_view ) ? ' active' : '';
		echo '<li class="nav-item"><a class="nav-link' . esc_attr( $active ) . '" href="' . esc_attr( $baseurl . $the_view ) . '/">' . esc_html( strtoupper( str_replace( '-', ' ', $the_view ) ) ) . '</a></li>';
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
						<h3 class="card-header actors">Actors</h3>
						<div class="card-body bg-light">
							<h5 class="card-title"><?php echo (int) lwtv_plugin()->generate_statistics( 'actors', 'total', 'count' ); ?></h5>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="card text-center">
						<h3 class="card-header sexuality">Sexual Orientation</h3>
						<div class="card-body bg-light">
							<h5 class="card-title"><?php echo (int) wp_count_terms( 'lez_actor_sexuality' ); ?></h5>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="card text-center">
						<h3 class="card-header gender">Gender Identities</h3>
						<div class="card-body bg-light">
							<h5 class="card-title"><?php echo (int) wp_count_terms( 'lez_actor_gender' ); ?></h5>
						</div>
					</div>
				</div>
			</div>
		</div>

		<p>&nbsp;</p>

		<div class="container">
			<div class="row">
				<div class="col">
					<h4>Top Sexual Orientations</h4>
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th scope="col">Sexuality</th>
								<th scope="col">Actors</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$sexualities = get_terms(
								array(
									'taxonomy'   => 'lez_actor_sexuality',
									'number'     => 5,
									'orderby'    => 'count',
									'hide_empty' => 0,
									'order'      => 'DESC',
								)
							);
							foreach ( $sexualities as $sexuality ) {
								echo '<tr>
										<th scope="row"><a href="/sexuality/' . esc_attr( $sexuality->slug ) . '">' . esc_html( $sexuality->name ) . '</a></th>
										<td>' . (int) $sexuality->count . '</td>
									</tr>';
							}
							?>
						</tbody>
					</table>
					<a href="?view=sexuality"><button type="button" class="btn btn-info btn-lg btn-block">All <?php echo (int) wp_count_terms( 'lez_actor_sexuality' ); ?> Sexual Orientations</button></a>
				</div>

				<div class="col">
					<h4>Top Gender Identities</h4>
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th scope="col">Gender</th>
								<th scope="col">Actors</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$genders = get_terms(
								array(
									'taxonomy'   => 'lez_actor_gender',
									'number'     => 5,
									'orderby'    => 'count',
									'hide_empty' => 0,
									'order'      => 'DESC',
								)
							);
							foreach ( $genders as $gender ) {
								echo '<tr>
										<th scope="row"><a href="/gender/' . esc_attr( $gender->slug ) . '">' . esc_html( $gender->name ) . '</a></th>
										<td>' . (int) $gender->count . '</td>
									</tr>';
							}
							?>
						</tbody>
					</table>
					<a href="?view=gender"><button type="button" class="btn btn-info btn-lg btn-block">All <?php echo (int) wp_count_terms( 'lez_actor_gender' ); ?> Gender Identities</button></a>

				</div>
			</div>
		</div>
		<?php
		break;
	case 'sexuality':
		?>
		<h3>Actor Sexuality Demographics</h3>
		<div class="container chart-container">
			<div class="row">
				<div class="col-sm-6">
					<?php lwtv_plugin()->generate_statistics( 'actors', 'actor_sexuality', 'piechart' ); ?>
				</div>

				<div class="col-sm-6">
					<?php lwtv_plugin()->generate_statistics( 'actors', 'actor_sexuality', 'percentage' ); ?>
				</div>
			</div>
		</div>
		<?php
		break;
	case 'gender':
		?>
		<h3>Actor Gender Identity Demographics</h3>
		<div class="container chart-container">
			<div class="row">
				<div class="col-sm-6">
					<?php lwtv_plugin()->generate_statistics( 'actors', 'actor_gender', 'piechart' ); ?>
				</div>

				<div class="col-sm-6">
					<?php lwtv_plugin()->generate_statistics( 'actors', 'actor_gender', 'percentage' ); ?>
				</div>
			</div>
		</div>
		<?php
		break;
	case 'roles':
		?>
		<h3>Actor Role Breakdown</h3>
		<div class="container chart-container">
			<div class="row">
				<div class="col">
					<h4>Actors per Character</h4>
					<p>This chart displays the number of actors who play each character. For example, "11 Actors (1)" means there's one character who has 11 actors (and yes, there is one).</p>
					<?php lwtv_plugin()->generate_statistics( 'actors', 'per-char', 'barchart' ); ?>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6">
					<?php lwtv_plugin()->generate_statistics( 'actors', 'per-char', 'piechart' ); ?>
				</div>

				<div class="col-sm-6">
					<?php lwtv_plugin()->generate_statistics( 'actors', 'per-char', 'percentage' ); ?>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<h4>Characters per Actor</h4>
					<p>This chart displays the number of characters each actor plays. The actor with the highest number of characters played is the 'unknown' actor.</p>
					<?php lwtv_plugin()->generate_statistics( 'actors', 'per-actor', 'barchart' ); ?>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6">
					<?php lwtv_plugin()->generate_statistics( 'actors', 'per-actor', 'piechart' ); ?>
				</div>

				<div class="col-sm-6">
					<?php lwtv_plugin()->generate_statistics( 'actors', 'per-actor', 'percentage' ); ?>
				</div>
			</div>
		</div>
		<?php
		break;
}
?>
