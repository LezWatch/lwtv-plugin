<?php
/**
 * The template for displaying the character stats page
 *
 * @package LezWatch.TV
 */

$valid_views     = array( 'cliches', 'gender', 'sexuality', 'queer-irl', 'roles', 'on-air' );
$sent_view       = get_query_var( 'view', 'overview' );
$view            = ( ! in_array( $sent_view, $valid_views, true ) ) ? 'overview' : $sent_view;
$character_count = ( new LWTV_Stats() )->generate( 'characters', 'total', 'count' );
?>

<h2>
	<a href="/characters/">Total Characters</a> (<?php echo ( new LWTV_Stats() )->generate( 'characters', 'total', 'count' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>)
</h2>

<ul class="nav nav-tabs">
	<?php
	$baseurl = '/statistics/characters/';
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
						<h3 class="card-header alert-success">Characters</h3>
						<div class="card-body bg-light">
							<h5 class="card-title"><?php echo (int) $character_count; ?></h5>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="card text-center">
						<h3 class="card-header alert-info">Sexual Orientations</h3>
						<div class="card-body bg-light">
							<h5 class="card-title"><?php echo (int) wp_count_terms( 'lez_sexuality' ); ?></h5>
						</div>
					</div>
				</div>
				<div class="col">
					<div class="card text-center">
						<h3 class="card-header alert-warning">Gender Identities</h3>
						<div class="card-body bg-light">
							<h5 class="card-title"><?php echo (int) wp_count_terms( 'lez_gender' ); ?></h5>
						</div>
					</div>
				</div>
			</div>
		</div>

		<p>&nbsp;</p>

		<div class="container">
			<div class="row">
				<div class="col">
					<h4>Top Clichés</h4>
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th scope="col"></th>
								<th scope="col">Characters</th>
								<th scope="col">Percent</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$cliches = get_terms(
								'lez_cliches',
								array(
									'number'     => 14,
									'orderby'    => 'count',
									'hide_empty' => 0,
									'order'      => 'DESC',
								)
							);
							foreach ( $cliches as $cliche ) {
								$percent = round( ( ( $cliche->count / $character_count ) * 100 ), 1 );
								echo '<tr>
										<th scope="row"><a href="' . esc_url( site_url( '/cliche/' . $cliche->slug ) ) . '">' . esc_html( $cliche->name ) . '</a></th>
										<td>' . (int) $cliche->count . '</td>
										<td><div class="progress"><div class="progress-bar bg-info" role="progressbar" style="width: ' . esc_html( $percent ) . '%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div></div>&nbsp;' . esc_html( $percent ) . '%</td>
									</tr>';
							}
							?>
						</tbody>
					</table>
					<a href="?view=cliches"><button type="button" class="btn btn-info btn-lg btn-block">All <?php echo (int) wp_count_terms( 'lez_cliches' ); ?> Clichés</button></a>
				</div>

				<div class="col">
					<h4>Top Sexual Orientations</h4>
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th scope="col"></th>
								<th scope="col">Characters</th>
								<th scope="col">Percent</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$sexualities = get_terms(
								'lez_sexuality',
								array(
									'number'     => 5,
									'orderby'    => 'count',
									'hide_empty' => 0,
									'order'      => 'DESC',
								)
							);
							foreach ( $sexualities as $sexuality ) {
								$percent = round( ( ( $sexuality->count / $character_count ) * 100 ), 1 );
								echo '<tr>
										<th scope="row"><a href="' . esc_url( site_url( '/sexuality/' . $sexuality->slug ) ) . '">' . esc_html( $sexuality->name ) . '</a></th>
										<td>' . (int) $sexuality->count . '</td>
										<td><div class="progress"><div class="progress-bar bg-info" role="progressbar" style="width: ' . esc_html( $percent ) . '%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div></div>&nbsp;' . esc_html( $percent ) . '%</td>
									</tr>';
							}
							?>
						</tbody>
					</table>
					<a href="?view=sexuality"><button type="button" class="btn btn-info btn-lg btn-block">All <?php echo (int) wp_count_terms( 'lez_sexuality' ); ?> Sexual Orientations</button></a>

					<p>&nbsp;<br/>&nbsp;</p>

					<h4>Top Gender Identities</h4>
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th scope="col"></th>
								<th scope="col">Characters</th>
								<th scope="col">Percent</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$genders = get_terms(
								'lez_gender',
								array(
									'number'     => 5,
									'orderby'    => 'count',
									'hide_empty' => 0,
									'order'      => 'DESC',
								)
							);
							foreach ( $genders as $gender ) {
								$percent = round( ( ( $gender->count / $character_count ) * 100 ), 1 );
								echo '<tr>
										<th scope="row"><a href="' . esc_url( site_url( '/gender/' . $gender->slug ) ) . '">' . esc_html( $gender->name ) . '</a></th>
										<td>' . (int) $gender->count . '</td>
										<td><div class="progress"><div class="progress-bar bg-info" role="progressbar" style="width: ' . esc_html( $percent ) . '%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div></div>&nbsp;' . esc_html( $percent ) . '%</td>
									</tr>';
							}
							?>
						</tbody>
					</table>
					<a href="?view=gender"><button type="button" class="btn btn-info btn-lg btn-block">All <?php echo (int) wp_count_terms( 'lez_gender' ); ?> Gender Identities</button></a>

				</div>
			</div>
		</div>
		<?php
		break;
	case 'cliches':
		?>
		<h3>Cliché Demographics</h3>
		<div class="container chart-container">
			<ul class="nav nav-pills nav-fill" id="v-pills-tab" role="tablist">
				<li class="nav-item"><a class="nav-link active" id="v-pills-barchart-tab" data-toggle="pill" href="#v-pills-barchart" role="tab" aria-controls="v-pills-barchart" aria-selected="true">Barchart</a></li>
				<li class="nav-item"><a class="nav-link" id="v-pills-list-tab" data-toggle="pill" href="#v-pills-list" role="tab" aria-controls="v-pills-list" aria-selected="false">List</a></li>
			</ul>
			<p>&nbsp;</p>
			<div class="tab-content" id="v-pills-tabContent">
				<div class="tab-pane fade show active" id="v-pills-barchart" role="tabpanel" aria-labelledby="v-pills-barchart-tab"><?php ( new LWTV_Stats() )->generate( 'characters', 'cliches', 'barchart' ); ?></div>
				<div class="tab-pane fade" id="v-pills-list" role="tabpanel" aria-labelledby="v-pills-list-tab"><?php ( new LWTV_Stats() )->generate( 'characters', 'cliches', 'list' ); ?></div>
			</div>
		</div>
		<?php
		break;
	case 'sexuality':
		?>
		<h3>Character Sexuality Breakdown</h3>
		<div class="container chart-container">
			<div class="row">
				<div class="col-sm-6">
					<?php ( new LWTV_Stats() )->generate( 'characters', 'sexuality', 'piechart' ); ?>
				</div>

				<div class="col-sm-6">
					<?php ( new LWTV_Stats() )->generate( 'characters', 'sexuality', 'percentage' ); ?>
				</div>
			</div>
		</div>
		<?php
		break;
	case 'gender':
		?>
		<h3>Character Breakdown By Gender</h3>
		<div class="container chart-container">
			<div class="row">
				<div class="col-sm-6">
					<?php ( new LWTV_Stats() )->generate( 'characters', 'gender', 'piechart' ); ?>
				</div>

				<div class="col-sm-6">
					<?php ( new LWTV_Stats() )->generate( 'characters', 'gender', 'percentage' ); ?>
				</div>
			</div>
		</div>
		<?php
		break;
	case 'queer-irl':
		?>
		<h3>Characters Played by Queer Actors</h3>
		<div class="container chart-container">
			<div class="row">
				<div class="col-sm-6">
					<?php ( new LWTV_Stats() )->generate( 'characters', 'queer-irl', 'piechart' ); ?>
				</div>

				<div class="col-sm-6">
					<?php ( new LWTV_Stats() )->generate( 'characters', 'queer-irl', 'percentage' ); ?>
				</div>
			</div>
		</div>
		<?php
		break;
	case 'roles':
		?>
		<h3>Character/Actor Comparisons</h3>
		<div class="container chart-container">
			<div class="row">
				<div class="col">
					<h4>Actors per Character</h4>
					<?php ( new LWTV_Stats() )->generate( 'actors', 'per-char', 'barchart' ); ?>
					<p>&nbsp;<br />The above chart displays the number of actors who play each character. So for example, "11 Actors (1)" means there's one character who has eleven (11) actors (and yes, there is one).</p>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<h4>Characters per Actor</h4>
					<?php ( new LWTV_Stats() )->generate( 'actors', 'per-actor', 'barchart' ); ?>
					<p>&nbsp;<br />The above chart displays the number of characters each actor plays. The actor with the highest number of characters played is the 'unknown' actor.</p>
				</div>
			</div>
		</div>
		<?php
		break;
	case 'on-air':
		?>
		<h3>Number of Characters On-Air per Year</h3>
		<div class="container">
			<div class="row">
				<div class="col">
					<?php ( new LWTV_Stats() )->generate( 'characters', 'on-air', 'trendline' ); ?>
				</div>
			</div>
		</div>
		<?php
		break;
}
