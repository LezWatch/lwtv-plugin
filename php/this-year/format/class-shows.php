<?php

namespace LWTV\This_Year\Format;

class Shows {
	/**
	 * List of shows for the year.
	 *
	 * @access public
	 * @param string $this_year
	 *
	 * @return void
	 */
	public function make( $this_year, $build_data, $view = 'shows-on-air' ) {
		$fail_msg      = '<p>No shows were new this year.</p>';
		$this_year     = ( isset( $this_year ) ) ? $this_year : gmdate( 'Y' );
		$show_array    = $build_data;
		$shows_count   = $build_data['count'];
		$shows_listed  = $show_array['current'];
		$shows_formats = $show_array['formats'];
		$shows_country = $show_array['country'];

		switch ( $view ) {
			case 'new-shows':
				$header = 'Shows Started in ' . $this_year;
				break;
			case 'canceled-shows':
				$header = 'Shows Canceled in ' . $this_year;
				break;
			default:
				$header = 'Shows On Air in ' . $this_year;
				break;
		}

		if ( empty( $shows_listed ) && empty( $shows_formats ) && empty( $shows_country ) ) {
			return;
		}
		?>
		<h2><a name="showsonair"><?php echo (int) $shows_count; ?> <?php echo esc_html( $header ); ?></a></h2>

		<p>&nbsp;</p>

		<ul class="nav nav-pills nav-fill" id="v-pills-tab" role="tablist">
			<?php
			if ( ! empty( $shows_listed ) ) {
				echo '<li class="nav-item"><a class="nav-link active" id="v-pills-byname-tab" data-bs-toggle="pill" href="#v-pills-byname" role="tab" aria-controls="v-pills-byname" aria-selected="true">By Name</a></li>';
			}
			if ( ! empty( $shows_formats ) ) {
				echo '<li class="nav-item"><a class="nav-link" id="v-pills-byformat-tab" data-bs-toggle="pill" href="#v-pills-byformat" role="tab" aria-controls="v-pills-byformat" aria-selected="true">By Format</a></li>';
			}
			if ( ! empty( $shows_country ) ) {
				echo '<li class="nav-item"><a class="nav-link" id="v-pills-bycountry-tab" data-bs-toggle="pill" href="#v-pills-bycountry" role="tab" aria-controls="v-pills-bycountry" aria-selected="true">By Country</a></li>';
			}
			?>
		</ul>

		<p>&nbsp;</p>

		<div class="tab-content" id="v-pills-tabContent">
			<?php
			if ( ! empty( $shows_listed ) ) {
				ksort( $shows_listed );
				?>
				<div class="tab-pane fade show active" id="v-pills-byname" role="tabpanel" aria-labelledby="v-pills-byname-tab">
					<table class="table table-md table-hover table-striped">
						<thead class="thead-light">
							<tr>
								<th style="width: 200px;" scope="col">Letter</th>
								<th scope="col">Show(s)</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ( $shows_listed as $letter => $s_shows ) {
								echo '<tr><td><h4>' . esc_html( strtoupper( $letter ) ) . ' (' . count( $s_shows ) . ')</h4></td><td><ul class="this-year-shows showsonair">';
								foreach ( $s_shows as $s_show ) {
									$show_s_tooltip = ( $s_show['airdates']['start'] === $s_show['airdates']['finish'] ) ? $s_show['airdates']['start'] : $s_show['airdates']['start'] . '-' . $s_show['airdates']['finish'];
									echo '<li><a href="' . esc_url( $s_show['url'] ) . '" data-bs-toggle="tooltip" data-placement="top" title="On air ' . wp_kses_post( $show_s_tooltip ) . '">' . esc_html( $s_show['name'] ) . '</a> <small>(' . esc_html( $s_show['country'] ) . ' - ' . esc_html( $s_show['format'] ) . ')</small></li>';
								}
								echo '</ul></td></tr>';
							}
							?>
						</tbody>
					</table>
				</div>
				<?php
			}

			if ( ! empty( $shows_formats ) ) {
				ksort( $shows_formats );
				?>
				<div class="tab-pane fade" id="v-pills-byformat" role="tabpanel" aria-labelledby="v-pills-byformat-tab">
					<table class="table table-md table-hover table-striped">
						<thead class="thead-light">
							<tr>
								<th style="width: 200px;" scope="col">Format</th>
								<th scope="col">Show(s)</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ( $shows_formats as $format => $f_shows ) {
								echo '<tr><td><h4>' . esc_html( $format ) . ' (' . count( $f_shows ) . ')</h4></td><td><ul class="this-year-shows showsonair">';
								foreach ( $f_shows as $f_show ) {
									$show_f_tooltip = ( $f_show['airdates']['start'] === $f_show['airdates']['finish'] ) ? $f_show['airdates']['start'] : $f_show['airdates']['start'] . '-' . $f_show['airdates']['finish'];
									echo '<li><a href="' . esc_url( $f_show['url'] ) . '" data-bs-toggle="tooltip" data-placement="top" title="On air ' . wp_kses_post( $show_f_tooltip ) . '">' . esc_html( $f_show['name'] ) . '</a> <small>(' . esc_html( $f_show['country'] ) . ')</small></li>';
								}
								echo '</ul></td></tr>';
							}
							?>
						</tbody>
					</table>
				</div>
				<?php
			}

			if ( ! empty( $shows_country ) ) {
				ksort( $shows_country );
				?>
				<div class="tab-pane fade" id="v-pills-bycountry" role="tabpanel" aria-labelledby="v-pills-bycountry-tab">
					<table class="table table-md table-hover table-striped">
						<thead class="thead-light">
							<tr>
								<th style="width: 200px;" scope="col">Country</th>
								<th scope="col">Show(s)</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ( $shows_country as $nation => $n_shows ) {
								echo '<tr><td><h4>' . esc_html( $nation ) . ' (' . count( $n_shows ) . ')</h4></td><td><ul class="this-year-shows showsonair">';
								foreach ( $n_shows as $n_show ) {
									$show_n_output = ( $n_show['airdates']['start'] === $n_show['airdates']['finish'] ) ? $n_show['airdates']['start'] : $n_show['airdates']['start'] . '-' . $n_show['airdates']['finish'];
									echo '<li><a href="' . esc_url( $n_show['url'] ) . '" data-bs-toggle="tooltip" data-placement="top" title="On air ' . wp_kses_post( $show_n_output ) . '">' . esc_html( $n_show['name'] ) . '</a> <small>(' . esc_html( $n_show['format'] ) . ')</small></li>';

								}
								echo '</ul></td></tr>';
							}
							?>
						</tbody>
					</table>
				</div>
				<?php
			}
			?>
		</div> <!-- Pills -->
		<?php
	}
}
