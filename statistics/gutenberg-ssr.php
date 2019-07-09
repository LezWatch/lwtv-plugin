<?php
/*
Description: Statistics Gutenberg - server side rendering.
Version: 1.0
Author: Mika Epstein

In a perfect world this would be 100% gutenized. It's not.
*/

class LWTV_Stats_SSR {

	/*
	 * Display statistics
	 *
	 * Usage:
	 *		[statistics page=[main|death]]
	 *
	 * @since 1.0
	 */
	public function statistics( $atts ) {

		$attributes  = shortcode_atts(
			array(
				'page' => 'main',
			),
			$atts
		);

		$valid_pages = array( 'main', 'actors', 'characters', 'death', 'formats', 'nations', 'shows', 'stations' );
		$the_page    = ( ! in_array( sanitize_text_field( $attributes['page'] ), $valid_pages, true ) ) ? 'main' : sanitize_text_field( $attributes['page'] );

		switch ( $the_page ) {
			case 'main':
				$output = self::main();
				break;
			case 'actors':
				$output = self::actors();
				break;
			case 'characters':
				$output = self::characters();
				break;
			case 'death':
				$output = self::death();
				break;
			case 'formats':
				$output = self::formats();
				break;
			case 'nations':
				$output = self::nations();
				break;
			case 'shows':
				$output = self::shows();
				break;
			case 'stations':
				$output = self::stations();
				break;
		}

		$output = '<div class="lwtv-stats">' . $output . '</div>';
		return $output;
	}

	public function main() {

		$characters = LWTV_Stats::generate( 'characters', 'total', 'count' );
		$shows      = LWTV_Stats::generate( 'shows', 'total', 'count' );
		$actors     = LWTV_Stats::generate( 'actors', 'total', 'count' );
		$dead_chars = LWTV_Stats::generate( 'characters', 'dead', 'count' );

		$nations     = get_terms(
			'lez_country',
			array(
				'number'     => 10,
				'orderby'    => 'count',
				'hide_empty' => 0,
				'order'      => 'DESC',
			)
		);
		$nation_table = '';
		foreach ( $nations as $nation ) {
			$nation_table .= '<tr>
					<th scope="row"><a href="' . esc_url( site_url( 'nations/?country=' . $nation->slug ) ) . '">' . esc_html( $nation->name ) . '</a></th>
					<td>' . (int) $nation->count . '</td>
					<td>' . esc_html( round( ( ( $nation->count / $shows ) * 100 ), 1 ) ) . '%</td>
				</tr>';
		}

		$stations      = get_terms(
			'lez_stations',
			array(
				'number'     => 10,
				'orderby'    => 'count',
				'hide_empty' => 0,
				'order'      => 'DESC',
			)
		);
		$station_table = '';
		foreach ( $stations as $station ) {
			$station_table .= '<tr>
					<th scope="row"><a href="' . esc_url( site_url( 'stations/?station=' . $station->slug ) ) . '">' . esc_html( $station->name ) . '</a></th>
					<td>' . (int) $station->count . '</td>
					<td>' . esc_html( round( ( ( $station->count / $shows ) * 100 ), 1 ) ) . '%</td>
				</tr>';
		}

		$output = '<h2><a name="overview">Overview</a></h2>

		<div class="container">
			<div class="row">
				<div class="col">
					<div class="alert alert-success" role="info"><center>
						<h3 class="alert-heading">Characters</h3>
						<h5><?php echo (int) $characters; ?></h5>
						<p>[<a href="characters">Character Statistics</a>]</p>
					</center></div>
				</div>
				<div class="col">
					<div class="alert alert-info" role="info"><center>
						<h3 class="alert-heading">Shows</h3>
						<h5><?php echo (int) $shows; ?></h5>
						<p>[<a href="shows">Show Statistics</a>]</p>
					</center></div>
				</div>
				<div class="col">
					<div class="alert alert-warning" role="info"><center>
						<h3 class="alert-heading">Actors</h3>
						<h5><?php echo (int) $actors; ?></h5>
						<p>[<a href="actors">Actor Statistics</a>]</p>
					</center></div>
				</div>
				<div class="col">
					<div class="alert alert-danger" role="info"><center>
						<h3 class="alert-heading">Dead Characters</h3>
						<h5><?php echo (int) $dead_chars; ?></h5>
						<p>[<a href="death">Death Statistics</a>]</p>
					</center></div>
				</div>
			</div>
		</div>

		<hr>

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
						<tbody>' . $nation_table . '</tbody>
					</table>
					<a href="nations"><button type="button" class="btn btn-info btn-lg btn-block">All ' . wp_count_terms( 'lez_country' ) . ' Nations</button></a>
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
						<tbody>' . $station_table . '</tbody>
					</table>
					<a href="stations"><button type="button" class="btn btn-info btn-lg btn-block">All ' . wp_count_terms( 'lez_stations' ) . ' Stations</button></a>
				</div>
			</div>
		</div>';

		return $output;

	}

	public function actors() {
		$valid_views = array( 'overview', 'gender', 'sexuality', 'roles' );
		$view        = ( ! isset( $_GET['view'] ) || ! in_array( $_GET['view'], $valid_views, true ) ) ? 'overview' : $_GET['view']; // phpcs:ignore WordPress.Security.NonceVerification

		?>
		<h2>
			<a href="/actors/">Total Actors</a></strong> (<?php echo LWTV_Stats::generate( 'actors', 'total', 'count' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>)
		</h2>

		<ul class="nav nav-tabs">
			<?php
			foreach ( $valid_views as $the_view ) {
				$active = ( $view === $the_view ) ? ' active' : '';
				echo '<li class="nav-item"><a class="nav-link' . esc_attr( $active ) . '" href="' . esc_url( add_query_arg( 'view', $the_view, '/statistics/actors/' ) ) . '">' . esc_html( strtoupper( str_replace( '-', ' ', $the_view ) ) ) . '</a></li>';
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
							<div class="alert alert-success" role="info"><center>
								<h3 class="alert-heading">Actors</h3>
								<h5><?php echo LWTV_Stats::generate( 'actors', 'total', 'count' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h5>
							</center></div>
						</div>
						<div class="col">
							<div class="alert alert-info" role="info"><center>
								<h3 class="alert-heading">Sexual Orientations</h3>
								<h5><?php echo (int) wp_count_terms( 'lez_actor_sexuality' ); ?></h5>
							</center></div>
						</div>
						<div class="col">
							<div class="alert alert-warning" role="info"><center>
								<h3 class="alert-heading">Gender Identities</h3>
								<h5><?php echo (int) wp_count_terms( 'lez_actor_gender' ); ?></h5>
							</center></div>
						</div>
					</div>
				</div>

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
									$stations = get_terms(
										'lez_actor_sexuality',
										array(
											'number'     => 5,
											'orderby'    => 'count',
											'hide_empty' => 0,
											'order'      => 'DESC',
										)
									);
									foreach ( $stations as $station ) {
										echo '<tr>
												<th scope="row"><a href="/sexuality/' . esc_url( $station->slug ) . '">' . esc_html( $station->name ) . '</a></th>
												<td>' . (int) $station->count . '</td>
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
										'lez_actor_gender',
										array(
											'number'     => 5,
											'orderby'    => 'count',
											'hide_empty' => 0,
											'order'      => 'DESC',
										)
									);
									foreach ( $genders as $gender ) {
										echo '<tr>
												<th scope="row"><a href="/gender/' . esc_url( $gender->slug ) . '">' . esc_html( $gender->name ) . '</a></th>
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
				<div class="container chart-container">
					<div class="row">
						<div class="col-sm-6">
							<?php LWTV_Stats::generate( 'actors', 'actor_sexuality', 'piechart' ); ?>
						</div>

						<div class="col-sm-6">
							<?php LWTV_Stats::generate( 'actors', 'actor_sexuality', 'percentage' ); ?>
						</div>
					</div>
				</div>
				<?php
				break;
			case 'gender':
				?>
				<div class="container chart-container">
					<div class="row">
						<div class="col-sm-6">
							<?php LWTV_Stats::generate( 'actors', 'actor_gender', 'piechart' ); ?>
						</div>

						<div class="col-sm-6">
							<?php LWTV_Stats::generate( 'actors', 'actor_gender', 'percentage' ); ?>
						</div>
					</div>
				</div>
				<?php
				break;
			case 'roles':
				?>
				<div class="container chart-container">
					<div class="row">
						<div class="col">
							<h3>Actors per Character</h3>
							<p>This chart displays the number of actors who play each character. So for example, "11 Actors (1)" means there's one character who has 11 actors (and yes, there is one).</p>
							<?php LWTV_Stats::generate( 'actors', 'per-char', 'barchart' ); ?>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<?php LWTV_Stats::generate( 'actors', 'per-char', 'piechart' ); ?>
						</div>

						<div class="col-sm-6">
							<?php LWTV_Stats::generate( 'actors', 'per-char', 'percentage' ); ?>
						</div>
					</div>
					<div class="row">
						<div class="col">
							<h3>Characters per Actor</h3>
							<p>This chart displays the number of characters each actor plays. The actor with the highest number of characters played is the 'unknown' actor.</p>
							<?php LWTV_Stats::generate( 'actors', 'per-actor', 'barchart' ); ?>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<?php LWTV_Stats::generate( 'actors', 'per-actor', 'piechart' ); ?>
						</div>

						<div class="col-sm-6">
							<?php LWTV_Stats::generate( 'actors', 'per-actor', 'percentage' ); ?>
						</div>
					</div>
				</div>
				<?php
				break;
		}

	}

}

new LWTV_Stats_SSR();
