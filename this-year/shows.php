<?php
/**
 * Shows for The Year
 *
 * @package LezWatch.TV
 */

class LWTV_This_Year_Shows {

	public static function toc() {
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
	public static function list( $thisyear ) {
		$fail_msg      = '<p>No shows were on air this year.</p>';
		$thisyear      = ( isset( $thisyear ) ) ? $thisyear : date( 'Y' );
		$shows_current = array();
		$shows_formats = array();
		$shows_country = array();
		$shows_queery  = LWTV_Loops::post_type_query( 'post_type_shows' );

		if ( $shows_queery->have_posts() ) {
			$has_shows = true;
			while ( $shows_queery->have_posts() ) {
				$shows_queery->the_post();
				$show_id   = get_the_ID();
				$show_name = preg_replace( '/\s*/', '', get_the_title( $show_id ) );
				$show_name = strtolower( $show_name );
				if ( get_post_meta( $show_id, 'lezshows_airdates', true ) && 'publish' === get_post_status( $show_id ) ) {
					$airdates  = get_post_meta( $show_id, 'lezshows_airdates', true );
					$countries = get_the_term_list( $show_id, 'lez_country', '', ', ', '' );
					$format    = get_the_term_list( $show_id, 'lez_formats' );
					// Shows Currently Airing
					if (
						( 'current' === $airdates['finish'] && date( 'Y' ) === $thisyear ) // Still Current and it's NOW
						|| ( $airdates['finish'] >= $thisyear && $airdates['start'] <= $thisyear ) // Airdates between
					) {
						$shows_current[ $show_name ] = array(
							'url'     => get_permalink( $show_id ),
							'name'    => get_the_title( $show_id ),
							'country' => wp_strip_all_tags( $countries ),
							'format'  => wp_strip_all_tags( $format ),
						);

						// If there are formats, we add the show to the format
						if ( ! empty( wp_strip_all_tags( $format ) ) ) {
							$shows_formats[ wp_strip_all_tags( $format ) ][ $show_name ] = array(
								'url'     => get_permalink( $show_id ),
								'name'    => get_the_title( $show_id ),
								'country' => wp_strip_all_tags( $countries ),
							);
						}

						// If there are countries (heh), we add the show to the countries
						if ( ! empty( wp_strip_all_tags( $countries ) ) ) {
							$these_countries = explode( ', ', wp_strip_all_tags( $countries ) );
							foreach ( $these_countries as $country ) {
								$shows_country[ $country ][ $show_name ] = array(
									'url'    => get_permalink( $show_id ),
									'name'   => get_the_title( $show_id ),
									'format' => wp_strip_all_tags( $format ),
								);
							}
						}
					}
				}
			}
		}

		wp_reset_query();

		?>
		<h2><a name="showsonair"><?php echo count( $shows_current ); ?> Shows On Air </a></h2>

		<p>&nbsp;</p>

		<div id="accordion">

			<p>
			<?php
			if ( ! empty( $shows_current ) ) {
				echo '<a class="btn btn-primary" data-toggle="collapse" href="#collapseName" role="button" aria-expanded="true" aria-controls="collapseName">By Name</a>&nbsp;';
			}
			if ( ! empty( $shows_formats ) ) {
				echo '<a class="btn btn-primary" data-toggle="collapse" href="#collapseFormat" role="button" aria-expanded="false" aria-controls="collapseFormat">By Format</a> ';
			}
			if ( ! empty( $shows_country ) ) {
				echo '<a class="btn btn-primary" data-toggle="collapse" href="#collapseCountry" role="button" aria-expanded="false" aria-controls="collapseCountry">By Country</a>';
			}
			?>
			</p>

		<?php
		if ( ! empty( $shows_current ) ) {
			ksort( $shows_current );
			?>
			<div class="card">
				<div id="collapseName" class="collapse show" aria-labelledby="headingName" data-parent="#accordion">
					<div class="card-body">
						<ul class="this-year-shows showsonair">
						<?php
						foreach ( $shows_current as $c_show ) {
							$show_c_output = '<a href="' . $c_show['url'] . '">' . $c_show['name'] . '</a> <small>(' . $c_show['country'] . ' - ' . $c_show['format'] . ')</small>';
							echo '<li>' . wp_kses_post( $show_c_output ) . '</li>';
						}
						?>
						</ul>
					</div>
				</div>
			</div>
			<?php
		} else {
			$has_shows = false;
		}

		if ( ! empty( $shows_formats ) ) {
			ksort( $shows_formats );
			?>
			<div class="card">
				<div id="collapseFormat" class="collapse" aria-labelledby="headingFormat" data-parent="#accordion">
					<div class="card-body">
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
										echo '<li><a href="' . esc_url( $f_show['url'] ) . '">' . esc_html( $f_show['name'] ) . '</a> <small>(' . esc_html( $f_show['country'] ) . ')</small></li>';
									}
									echo '</ul></td></tr>';
								}
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<?php
		} else {
			$has_shows = false;
		}

		if ( ! empty( $shows_country ) ) {
			ksort( $shows_country );
			?>
			<div class="card">
				<div id="collapseCountry" class="collapse" aria-labelledby="headingCountry" data-parent="#accordion">
					<div class="card-body">
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
										echo '<li><a href="' . esc_url( $n_show['url'] ) . '">' . esc_html( $n_show['name'] ) . '</a> <small>(' . esc_html( $n_show['format'] ) . ')</small></li>';
									}
									echo '</ul></td></tr>';
								}
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<?php
		} else {
			$has_shows = false;
		}

		if ( ! $has_shows ) {
			echo wp_kses_post( $fail_msg );
		}
		?>
		</div> <!-- accordion -->
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
		$thisyear      = ( isset( $thisyear ) ) ? $thisyear : date( 'Y' );
		$shows_started = array();
		$shows_formats = array();
		$shows_country = array();
		$shows_queery  = LWTV_Loops::post_type_query( 'post_type_shows' );

		if ( $shows_queery->have_posts() ) {
			$has_shows = true;
			while ( $shows_queery->have_posts() ) {
				$shows_queery->the_post();
				$show_id   = get_the_ID();
				$show_name = strtolower( preg_replace( '/\s*/', '', get_the_title( $show_id ) ) );

				// If there are airdates saved...
				if ( get_post_meta( $show_id, 'lezshows_airdates', true ) ) {
					$airdates  = get_post_meta( $show_id, 'lezshows_airdates', true );
					$countries = get_the_term_list( $show_id, 'lez_country', '', ', ', '' );
					$format    = get_the_term_list( $show_id, 'lez_formats' );

					// Shows that STARTED this year
					if ( $airdates['start'] === $thisyear ) {
						$shows_started[ $show_name ] = array(
							'url'     => get_permalink( $show_id ),
							'name'    => get_the_title( $show_id ),
							'status'  => get_post_status( $show_id ),
							'country' => wp_strip_all_tags( $countries ),
							'format'  => wp_strip_all_tags( $format ),
						);

						// If there are formats, we add the show to the format
						if ( ! empty( wp_strip_all_tags( $format ) ) ) {
							$shows_formats[ wp_strip_all_tags( $format ) ][ $show_name ] = array(
								'url'     => get_permalink( $show_id ),
								'name'    => get_the_title( $show_id ),
								'country' => wp_strip_all_tags( $countries ),
							);
						}

						// If there are countries (heh), we add the show to the countries
						if ( ! empty( wp_strip_all_tags( $countries ) ) ) {
							$these_countries = explode( ', ', wp_strip_all_tags( $countries ) );
							foreach ( $these_countries as $country ) {
								$shows_country[ $country ][ $show_name ] = array(
									'url'    => get_permalink( $show_id ),
									'name'   => get_the_title( $show_id ),
									'format' => wp_strip_all_tags( $format ),
								);
							}
						}
					}
				}
			}
		}
		wp_reset_query();
		?>
		<h2><a name="showsonair"><?php echo count( $shows_started ); ?> Shows Began</a></h2>

		<p>&nbsp;</p>

		<div id="accordion">
			<p>
			<?php
			if ( ! empty( $shows_started ) ) {
				echo '<a class="btn btn-primary" data-toggle="collapse" href="#collapseName" role="button" aria-expanded="true" aria-controls="collapseName">By Name</a>&nbsp;';
			}
			if ( ! empty( $shows_formats ) ) {
				echo '<a class="btn btn-primary" data-toggle="collapse" href="#collapseFormat" role="button" aria-expanded="false" aria-controls="collapseFormat">By Format</a> ';
			}
			if ( ! empty( $shows_country ) ) {
				echo '<a class="btn btn-primary" data-toggle="collapse" href="#collapseCountry" role="button" aria-expanded="false" aria-controls="collapseCountry">By Country</a>';
			}
			?>
			</p>

			<?php
			if ( ! empty( $shows_started ) ) {
				ksort( $shows_started );
				?>
				<div class="card">
					<div id="collapseName" class="collapse show" aria-labelledby="headingName" data-parent="#accordion">
						<div class="card-body">
							<ul class="this-year-shows showsstart">
							<?php
							foreach ( $shows_started as $s_show ) {
								$show_s_output = '<em><a href="' . $s_show['url'] . '">' . $s_show['name'] . '</a></em> <small>(' . $s_show['country'] . ' - ' . $s_show['format'] . ')</small>';
								echo '<li>' . wp_kses_post( $show_s_output ) . '</li>';
							}
							?>
							</ul>
						</div>
					</div>
				</div>
				<?php
			} else {
				$has_shows = false;
			}

			if ( ! empty( $shows_formats ) ) {
				ksort( $shows_formats );
				?>
				<div class="card">
					<div id="collapseFormat" class="collapse" aria-labelledby="headingFormat" data-parent="#accordion">
						<div class="card-body">
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
											echo '<li><em><a href="' . esc_url( $f_show['url'] ) . '">' . esc_html( $f_show['name'] ) . '</a></em> <small>(' . esc_html( $f_show['country'] ) . ')</small></li>';
										}
										echo '</ul></td></tr>';
									}
									?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<?php
			} else {
				$has_shows = false;
			}

			if ( ! empty( $shows_country ) ) {
				ksort( $shows_country );
				?>
				<div class="card">
					<div id="collapseCountry" class="collapse" aria-labelledby="headingCountry" data-parent="#accordion">
						<div class="card-body">
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
											echo '<li><em><a href="' . esc_url( $n_show['url'] ) . '">' . esc_html( $n_show['name'] ) . '</a></em> <small>(' . esc_html( $n_show['format'] ) . ')</small></li>';
										}
										echo '</ul></td></tr>';
									}
									?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<?php
			} else {
				$has_shows = false;
			}

			if ( ! $has_shows ) {
				echo wp_kses_post( $fail_msg );
			}
			?>
		</div> <!-- accordion -->
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
		$thisyear      = ( isset( $thisyear ) ) ? $thisyear : date( 'Y' );
		$shows_ended   = array();
		$shows_formats = array();
		$shows_country = array();
		$shows_queery  = LWTV_Loops::post_type_query( 'post_type_shows' );

		if ( $shows_queery->have_posts() ) {
			$has_shows = true;
			while ( $shows_queery->have_posts() ) {
				$shows_queery->the_post();
				$show_id   = get_the_ID();
				$show_name = strtolower( preg_replace( '/\s*/', '', get_the_title( $show_id ) ) );

				// If there are airdates saved...
				if ( get_post_meta( $show_id, 'lezshows_airdates', true ) ) {
					$airdates  = get_post_meta( $show_id, 'lezshows_airdates', true );
					$countries = get_the_term_list( $show_id, 'lez_country', '', ', ', '' );
					$format    = get_the_term_list( $show_id, 'lez_formats' );

					// Shows that ENDED this year
					if ( $airdates['finish'] === $thisyear ) {
						$shows_ended[ $show_name ] = array(
							'url'     => get_permalink( $show_id ),
							'name'    => get_the_title( $show_id ),
							'status'  => get_post_status( $show_id ),
							'country' => wp_strip_all_tags( $countries ),
							'format'  => wp_strip_all_tags( $format ),
						);

						// If there are formats, we add the show to the format
						if ( ! empty( wp_strip_all_tags( $format ) ) ) {
							$shows_formats[ wp_strip_all_tags( $format ) ][ $show_name ] = array(
								'url'     => get_permalink( $show_id ),
								'name'    => get_the_title( $show_id ),
								'country' => wp_strip_all_tags( $countries ),
							);
						}

						// If there are countries (heh), we add the show to the countries
						if ( ! empty( wp_strip_all_tags( $countries ) ) ) {
							$these_countries = explode( ', ', wp_strip_all_tags( $countries ) );
							foreach ( $these_countries as $country ) {
								$shows_country[ $country ][ $show_name ] = array(
									'url'    => get_permalink( $show_id ),
									'name'   => get_the_title( $show_id ),
									'format' => wp_strip_all_tags( $format ),
								);
							}
						}
					}
				}
			}
		}
		wp_reset_query();
		?>
		<h2><a name="showsonair"><?php echo count( $shows_ended ); ?> Shows Canceled</a></h2>

		<p>&nbsp;</p>

		<div id="accordion">
			<p>
			<?php
			if ( ! empty( $shows_ended ) ) {
				echo '<a class="btn btn-primary" data-toggle="collapse" href="#collapseName" role="button" aria-expanded="true" aria-controls="collapseName">By Name</a>&nbsp;';
			}
			if ( ! empty( $shows_formats ) ) {
				echo '<a class="btn btn-primary" data-toggle="collapse" href="#collapseFormat" role="button" aria-expanded="false" aria-controls="collapseFormat">By Format</a> ';
			}
			if ( ! empty( $shows_country ) ) {
				echo '<a class="btn btn-primary" data-toggle="collapse" href="#collapseCountry" role="button" aria-expanded="false" aria-controls="collapseCountry">By Country</a>';
			}
			?>
			</p>

			<?php
			if ( ! empty( $shows_ended ) ) {
				ksort( $shows_ended );
				?>
				<div class="card">
					<div id="collapseName" class="collapse show" aria-labelledby="headingName" data-parent="#accordion">
						<div class="card-body">
							<ul class="this-year-shows showsstart">
							<?php
							foreach ( $shows_ended as $s_show ) {
								$show_s_output = '<em><a href="' . $s_show['url'] . '">' . $s_show['name'] . '</a></em> <small>(' . $s_show['country'] . ' - ' . $s_show['format'] . ')</small>';
								echo '<li>' . wp_kses_post( $show_s_output ) . '</li>';
							}
							?>
							</ul>
						</div>
					</div>
				</div>
				<?php
			} else {
				$has_shows = false;
			}

			if ( ! empty( $shows_formats ) ) {
				ksort( $shows_formats );
				?>
				<div class="card">
					<div id="collapseFormat" class="collapse" aria-labelledby="headingFormat" data-parent="#accordion">
						<div class="card-body">
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
											echo '<li><em><a href="' . esc_url( $f_show['url'] ) . '">' . esc_html( $f_show['name'] ) . '</a></em> <small>(' . esc_html( $f_show['country'] ) . ')</small></li>';
										}
										echo '</ul></td></tr>';
									}
									?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<?php
			} else {
				$has_shows = false;
			}

			if ( ! empty( $shows_country ) ) {
				ksort( $shows_country );
				?>
				<div class="card">
					<div id="collapseCountry" class="collapse" aria-labelledby="headingCountry" data-parent="#accordion">
						<div class="card-body">
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
											echo '<li><em><a href="' . esc_url( $n_show['url'] ) . '">' . esc_html( $n_show['name'] ) . '</a></em> <small>(' . esc_html( $n_show['format'] ) . ')</small></li>';
										}
										echo '</ul></td></tr>';
									}
									?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<?php
			} else {
				$has_shows = false;
			}

			if ( ! $has_shows ) {
				echo wp_kses_post( $fail_msg );
			}
			?>
		</div> <!-- accordion -->
		<?php
	}
}

new LWTV_This_Year_Shows();
