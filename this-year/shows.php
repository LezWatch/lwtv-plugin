<?php
/**
 * Shows for The Year
 *
 * @package LezWatch.TV
 */

class LWTV_This_Year_Shows {

	public function toc() {
		?>
		<section id="toc" class="toc-container card-body">
			<nav class="breadcrumb">
				<h4 class="toc-title">Go to:</h4>
				<a class="breadcrumb-item smoothscroll" href="#showsonair">Shows On Air</a>
				<a class="breadcrumb-item smoothscroll" href="#showsstart">Shows That Began</a>
				<a class="breadcrumb-item smoothscroll" href="#showsend">Shows That Ended</a>
			</nav>
		</section>
		<?php
	}

	/**
	 * List of shows for the year.
	 *
	 * @access public
	 * @param mixed $thisyear
	 * @return void
	 */
	public function list( $thisyear ) {
		$fail_msg      = '<p>No shows were on air this year.</p>';
		$thisyear      = ( isset( $thisyear ) ) ? $thisyear : gmdate( 'Y' );
		$show_array    = self::get_list( $thisyear, 'now' );
		$shows_current = $show_array['current'];
		$shows_formats = $show_array['formats'];
		$shows_country = $show_array['country'];

		if ( count( $shows_current ) > 0 ) {
			$has_shows = true;
		}
		?>

		<h2><a name="showsonair"><?php echo count( $shows_current ); ?> Shows On Air </a></h2>

		<p>&nbsp;</p>

		<ul class="nav nav-pills nav-fill" id="v-pills-tab" role="tablist">
			<?php
			if ( ! empty( $shows_current ) ) {
				echo '<li class="nav-item"><a class="nav-link active" id="v-pills-byname-tab" data-toggle="pill" href="#v-pills-byname" role="tab" aria-controls="v-pills-byname" aria-selected="true">By Name</a></li>';
			}
			if ( ! empty( $shows_formats ) ) {
				echo '<li class="nav-item"><a class="nav-link" id="v-pills-byformat-tab" data-toggle="pill" href="#v-pills-byformat" role="tab" aria-controls="v-pills-byformat" aria-selected="true">By Format</a></li>';
			}
			if ( ! empty( $shows_country ) ) {
				echo '<li class="nav-item"><a class="nav-link" id="v-pills-bycountry-tab" data-toggle="pill" href="#v-pills-bycountry" role="tab" aria-controls="v-pills-bycountry" aria-selected="true">By Country</a></li>';
			}
			?>

		</ul>
		<p>&nbsp;</p>
		<div class="tab-content" id="v-pills-tabContent">
			<?php
			if ( ! empty( $shows_current ) ) {
				ksort( $shows_current );
				?>
				<div class="tab-pane fade show active" id="v-pills-byname" role="tabpanel" aria-labelledby="v-pills-byname-tab">
					<ul class="this-year-shows showsonair">
					<?php
					foreach ( $shows_current as $s_show ) {
						$show_s_tooltip = ( $s_show['airdates']['start'] === $s_show['airdates']['finish'] ) ? $s_show['airdates']['start'] : $s_show['airdates']['start'] . '-' . $s_show['airdates']['finish'];
						echo '<li><a href="' . esc_url( $s_show['url'] ) . '" data-toggle="tooltip" data-placement="top" title="On air ' . wp_kses_post( $show_s_tooltip ) . '">' . esc_html( $s_show['name'] ) . '</a> <small>(' . esc_html( $s_show['country'] ) . ' - ' . esc_html( $s_show['format'] ) . ')</small></li>';
					}
					?>
					</ul>
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
								echo '<tr><td>' . esc_html( $format ) . ' (' . count( $f_shows ) . ')</td><td><ul class="this-year-shows showsonair">';
								foreach ( $f_shows as $f_show ) {
									$show_f_tooltip = ( $f_show['airdates']['start'] === $f_show['airdates']['finish'] ) ? $f_show['airdates']['start'] : $f_show['airdates']['start'] . '-' . $f_show['airdates']['finish'];
									echo '<li><a href="' . esc_url( $f_show['url'] ) . '" data-toggle="tooltip" data-placement="top" title="On air ' . wp_kses_post( $show_f_tooltip ) . '">' . esc_html( $f_show['name'] ) . '</a> <small>(' . esc_html( $f_show['country'] ) . ')</small></li>';
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
								echo '<tr><td>' . esc_html( $nation ) . ' (' . count( $n_shows ) . ')</td><td><ul class="this-year-shows showsonair">';
								foreach ( $n_shows as $n_show ) {
									$show_n_output = ( $n_show['airdates']['start'] === $n_show['airdates']['finish'] ) ? $n_show['airdates']['start'] : $n_show['airdates']['start'] . '-' . $n_show['airdates']['finish'];
									echo '<li><a href="' . esc_url( $n_show['url'] ) . '" data-toggle="tooltip" data-placement="top" title="On air ' . wp_kses_post( $show_n_output ) . '">' . esc_html( $n_show['name'] ) . '</a> <small>(' . esc_html( $n_show['format'] ) . ')</small></li>';
								}
								echo '</ul></td></tr>';
							}
							?>
						</tbody>
					</table>
				</div>
				<?php
			}

			if ( ! $has_shows ) {
				echo wp_kses_post( $fail_msg );
			}
			?>
		</div> <!-- Pills -->
		<?php
	}

	/**
	 * List of new shows for the year.
	 *
	 * @access public
	 * @param mixed $thisyear
	 * @return void
	 */
	public function new( $thisyear ) {
		$fail_msg      = '<p>No shows were new this year.</p>';
		$thisyear      = ( isset( $thisyear ) ) ? $thisyear : gmdate( 'Y' );
		$show_array    = self::get_list( $thisyear, 'started' );
		$shows_started = $show_array['current'];
		$shows_formats = $show_array['formats'];
		$shows_country = $show_array['country'];
		?>
		<h2><a name="showsonair"><?php echo count( $shows_started ); ?> Shows Began</a></h2>

		<p>&nbsp;</p>

		<ul class="nav nav-pills nav-fill" id="v-pills-tab" role="tablist">
			<?php
			if ( ! empty( $shows_started ) ) {
				echo '<li class="nav-item"><a class="nav-link active" id="v-pills-byname-tab" data-toggle="pill" href="#v-pills-byname" role="tab" aria-controls="v-pills-byname" aria-selected="true">By Name</a></li>';
			}
			if ( ! empty( $shows_formats ) ) {
				echo '<li class="nav-item"><a class="nav-link" id="v-pills-byformat-tab" data-toggle="pill" href="#v-pills-byformat" role="tab" aria-controls="v-pills-byformat" aria-selected="true">By Format</a></li>';
			}
			if ( ! empty( $shows_country ) ) {
				echo '<li class="nav-item"><a class="nav-link" id="v-pills-bycountry-tab" data-toggle="pill" href="#v-pills-bycountry" role="tab" aria-controls="v-pills-bycountry" aria-selected="true">By Country</a></li>';
			}
			?>
		</ul>
		<p>&nbsp;</p>
		<div class="tab-content" id="v-pills-tabContent">
			<?php
			if ( ! empty( $shows_started ) ) {
				ksort( $shows_started );
				?>
				<div class="tab-pane fade show active" id="v-pills-byname" role="tabpanel" aria-labelledby="v-pills-byname-tab">
					<ul class="this-year-shows showsonair">
					<?php
					foreach ( $shows_started as $s_show ) {
						$show_s_tooltip = ( $s_show['airdates']['start'] === $s_show['airdates']['finish'] ) ? $s_show['airdates']['start'] : $s_show['airdates']['start'] . '-' . $s_show['airdates']['finish'];
						echo '<li><a href="' . esc_url( $s_show['url'] ) . '" data-toggle="tooltip" data-placement="top" title="On air ' . wp_kses_post( $show_s_tooltip ) . '">' . esc_html( $s_show['name'] ) . '</a> <small>(' . esc_html( $s_show['country'] ) . ' - ' . esc_html( $s_show['format'] ) . ')</small></li>';

					}
					?>
					</ul>
				</div>
				<?php
			} else {
				$has_shows = false;
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
								echo '<tr><td>' . esc_html( $format ) . ' (' . count( $f_shows ) . ')</td><td><ul class="this-year-shows showsonair">';
								foreach ( $f_shows as $f_show ) {
									$show_f_tooltip = ( $f_show['airdates']['start'] === $f_show['airdates']['finish'] ) ? $f_show['airdates']['start'] : $f_show['airdates']['start'] . '-' . $f_show['airdates']['finish'];
									echo '<li><a href="' . esc_url( $f_show['url'] ) . '" data-toggle="tooltip" data-placement="top" title="On air ' . wp_kses_post( $show_f_tooltip ) . '">' . esc_html( $f_show['name'] ) . '</a> <small>(' . esc_html( $f_show['country'] ) . ')</small></li>';
								}
								echo '</ul></td></tr>';
							}
							?>
						</tbody>
					</table>
				</div>
				<?php
			} else {
				$has_shows = false;
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
								echo '<tr><td>' . esc_html( $nation ) . ' (' . count( $n_shows ) . ')</td><td><ul class="this-year-shows showsonair">';
								foreach ( $n_shows as $n_show ) {
									$show_n_output = ( $n_show['airdates']['start'] === $n_show['airdates']['finish'] ) ? $n_show['airdates']['start'] : $n_show['airdates']['start'] . '-' . $n_show['airdates']['finish'];
									echo '<li><a href="' . esc_url( $n_show['url'] ) . '" data-toggle="tooltip" data-placement="top" title="On air ' . wp_kses_post( $show_n_output ) . '">' . esc_html( $n_show['name'] ) . '</a> <small>(' . esc_html( $n_show['format'] ) . ')</small></li>';

								}
								echo '</ul></td></tr>';
							}
							?>
						</tbody>
					</table>
				</div>
				<?php
			} else {
				$has_shows = false;
			}
			if ( ! $has_shows ) {
				echo wp_kses_post( $fail_msg );
			}
			?>
		</div> <!-- Pills -->
		<?php
	}

	/**
	 * List of canceled shows for the year.
	 *
	 * @access public
	 * @param mixed $thisyear
	 * @return void
	 */
	public function canceled( $thisyear ) {
		$fail_msg      = '<p>No shows were canceled this year.</p>';
		$thisyear      = ( isset( $thisyear ) ) ? $thisyear : gmdate( 'Y' );
		$show_array    = self::get_list( $thisyear, 'ended' );
		$shows_ended   = $show_array['current'];
		$shows_formats = $show_array['formats'];
		$shows_country = $show_array['country'];
		?>
		<h2><a name="showsonair"><?php echo count( $shows_ended ); ?> Shows Canceled</a></h2>

		<p>&nbsp;</p>

		<ul class="nav nav-pills nav-fill" id="v-pills-tab" role="tablist">
			<?php
			if ( ! empty( $shows_ended ) ) {
				echo '<li class="nav-item"><a class="nav-link active" id="v-pills-byname-tab" data-toggle="pill" href="#v-pills-byname" role="tab" aria-controls="v-pills-byname" aria-selected="true">By Name</a></li>';
			}
			if ( ! empty( $shows_formats ) ) {
				echo '<li class="nav-item"><a class="nav-link" id="v-pills-byformat-tab" data-toggle="pill" href="#v-pills-byformat" role="tab" aria-controls="v-pills-byformat" aria-selected="true">By Format</a></li>';
			}
			if ( ! empty( $shows_country ) ) {
				echo '<li class="nav-item"><a class="nav-link" id="v-pills-bycountry-tab" data-toggle="pill" href="#v-pills-bycountry" role="tab" aria-controls="v-pills-bycountry" aria-selected="true">By Country</a></li>';
			}
			?>
		</ul>
		<p>&nbsp;</p>
		<div class="tab-content" id="v-pills-tabContent">
			<?php
			if ( ! empty( $shows_ended ) ) {
				$has_shows = true;
				ksort( $shows_ended );
				?>
				<div class="tab-pane fade show active" id="v-pills-byname" role="tabpanel" aria-labelledby="v-pills-byname-tab">
					<ul class="this-year-shows showsonair">
					<?php
					foreach ( $shows_ended as $s_show ) {
						$show_s_tooltip = ( $s_show['airdates']['start'] === $s_show['airdates']['finish'] ) ? $s_show['airdates']['start'] : $s_show['airdates']['start'] . '-' . $s_show['airdates']['finish'];
						echo '<li><a href="' . esc_url( $s_show['url'] ) . '" data-toggle="tooltip" data-placement="top" title="On air ' . wp_kses_post( $show_s_tooltip ) . '">' . esc_html( $s_show['name'] ) . '</a> <small>(' . esc_html( $s_show['country'] ) . ' - ' . esc_html( $s_show['format'] ) . ')</small></li>';

					}
					?>
					</ul>
				</div>
				<?php
			} else {
				$has_shows = false;
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
								echo '<tr><td>' . esc_html( $format ) . ' (' . count( $f_shows ) . ')</td><td><ul class="this-year-shows showsonair">';
								foreach ( $f_shows as $f_show ) {
									$show_f_tooltip = ( $f_show['airdates']['start'] === $f_show['airdates']['finish'] ) ? $f_show['airdates']['start'] : $f_show['airdates']['start'] . '-' . $f_show['airdates']['finish'];
									echo '<li><a href="' . esc_url( $f_show['url'] ) . '" data-toggle="tooltip" data-placement="top" title="On air ' . wp_kses_post( $show_f_tooltip ) . '">' . esc_html( $f_show['name'] ) . '</a> <small>(' . esc_html( $f_show['country'] ) . ')</small></li>';

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
								echo '<tr><td>' . esc_html( $nation ) . ' (' . count( $n_shows ) . ')</td><td><ul class="this-year-shows showsonair">';
								foreach ( $n_shows as $n_show ) {
									$show_n_output = ( $n_show['airdates']['start'] === $n_show['airdates']['finish'] ) ? $n_show['airdates']['start'] : $n_show['airdates']['start'] . '-' . $n_show['airdates']['finish'];
									echo '<li><a href="' . esc_url( $n_show['url'] ) . '" data-toggle="tooltip" data-placement="top" title="On air ' . wp_kses_post( $show_n_output ) . '">' . esc_html( $n_show['name'] ) . '</a> <small>(' . esc_html( $n_show['format'] ) . ')</small></li>';
								}
								echo '</ul></td></tr>';
							}
							?>
						</tbody>
					</table>
				</div>
				<?php
			}

			if ( ! $has_shows ) {
				echo wp_kses_post( $fail_msg );
			}
			?>
		</div> <!-- Pills -->
		<?php
	}

	/**
	 * get all the shows that were active for a year
	 * @return array massive array of everything.
	 */
	public function get_list( $thisyear, $type = 'now', $count = false ) {
		$thisyear      = ( isset( $thisyear ) ) ? $thisyear : gmdate( 'Y' );
		$valid_types   = array( 'now', 'started', 'ended' );
		$type          = ( ! in_array( $type, $valid_types, true ) ) ? 'now' : $type;
		$shows_current = array();
		$shows_formats = array();
		$shows_country = array();
		$counted_shows = 0;
		$shows_queery  = ( new LWTV_Loops() )->post_type_query( 'post_type_shows' );

		if ( $shows_queery->have_posts() ) {
			while ( $shows_queery->have_posts() ) {
				$shows_queery->the_post();
				$show_id   = get_the_ID();
				$show_name = preg_replace( '/\s*/', '', get_the_title( $show_id ) );
				$show_name = strtolower( $show_name );
				$yes_count = false;
				if ( get_post_meta( $show_id, 'lezshows_airdates', true ) && 'publish' === get_post_status( $show_id ) ) {
					$airdates  = get_post_meta( $show_id, 'lezshows_airdates', true );
					$countries = get_the_term_list( $show_id, 'lez_country', '', ', ', '' );
					$format    = get_the_term_list( $show_id, 'lez_formats' );

					switch ( $type ) {
						case 'now':
							if (
								( 'current' === $airdates['finish'] && gmdate( 'Y' ) === $thisyear ) // Still Current and it's NOW
								|| ( $airdates['finish'] >= $thisyear && $airdates['start'] <= $thisyear ) // Airdates between
							) {
								$yes_count = true;
							}
							break;
						case 'started':
							if ( $airdates['start'] === $thisyear ) {
								$yes_count = true;
							}
							break;
						case 'ended':
							if ( $airdates['finish'] === $thisyear ) {
								$yes_count = true;
							}
							break;
					}

					// If the show pased whatever checks we have...
					if ( $yes_count ) {
						$counted_shows++;

						// If we're ONLY counting, we don't have to do the rest.
						if ( ! $count ) {
							$shows_current[ $show_name ] = array(
								'url'      => get_permalink( $show_id ),
								'name'     => get_the_title( $show_id ),
								'country'  => wp_strip_all_tags( $countries ),
								'format'   => wp_strip_all_tags( $format ),
								'airdates' => $airdates,
							);

							// If there are formats, we add the show to the format
							if ( ! empty( wp_strip_all_tags( $format ) ) ) {
								$shows_formats[ wp_strip_all_tags( $format ) ][ $show_name ] = array(
									'url'      => get_permalink( $show_id ),
									'name'     => get_the_title( $show_id ),
									'country'  => wp_strip_all_tags( $countries ),
									'airdates' => $airdates,
								);
							}

							// If there are countries (heh), we add the show to the countries
							if ( ! empty( wp_strip_all_tags( $countries ) ) ) {
								$these_countries = explode( ', ', wp_strip_all_tags( $countries ) );
								foreach ( $these_countries as $country ) {
									$shows_country[ $country ][ $show_name ] = array(
										'url'      => get_permalink( $show_id ),
										'name'     => get_the_title( $show_id ),
										'format'   => wp_strip_all_tags( $format ),
										'airdates' => $airdates,
									);
								}
							}
						}
					}
				}
			}
		}

		wp_reset_query();

		// if we counted, just kick that back.
		if ( $count ) {
			$return_array = $counted_shows;
		} else {
			$return_array = array(
				'count'   => $counted_shows,
				'current' => $shows_current,
				'formats' => $shows_formats,
				'country' => $shows_country,
			);
		}

		return $return_array;
	}
}

new LWTV_This_Year_Shows();
