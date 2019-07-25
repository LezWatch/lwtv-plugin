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
							$these_countries = explode( ',', wp_strip_all_tags( $countries ) );
							foreach ( $these_countries as $country ) {
								$shows_country[ $country ][ $show_name ] = array(
									'url'     => get_permalink( $show_id ),
									'name'    => get_the_title( $show_id ),
									'format'  => wp_strip_all_tags( $format ),
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

		<?php
		if ( ! empty( $shows_current ) ) {
			?>
			<div class="card">
				<div class="card-header" id="headingName">
					<h5><button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseName" aria-expanded="true" aria-controls="collapseName">By Name</button></h5>
				</div>

				<div id="collapseName" class="collapse" aria-labelledby="headingName" data-parent="#accordion">
					<div class="card-body">
						<ul class="this-year-shows showsonair">
						<?php
						foreach ( $shows_current as $show ) {
							$show_output = '<a href="' . $show['url'] . '">' . $show['name'] . '</a> <small>(' . $show['country'] . ' - ' . $show['format'] . ')</small>';
							echo '<li>' . wp_kses_post( $show_output ) . '</li>';
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
			?>
			<div class="card">
				<div class="card-header" id="headingFormat">
					<h5><button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseFormat" aria-expanded="false" aria-controls="collapseFormat">By Format</button></h5>
				</div>

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
								foreach ( $shows_formats as $format => $shows ) {
									echo '<tr><td>' . esc_html( $format ) . ' (' . count( $shows ) . ')</td><td><ul class="this-year-shows showsonair">';
									foreach ( $shows as $show ) {
										echo '<li><a href="' . esc_url( $show['url'] ) . '">' . esc_html( $show['name'] ) . '</a> <small>(' . esc_html( $show['country'] ) . ')</small></li>';
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
			?>
			<div class="card">
				<div class="card-header" id="headingCountry">
					<h5><button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseCountry" aria-expanded="false" aria-controls="collapseCountry">By Country</button></h5>
				</div>

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
								foreach ( $shows_country as $nation => $shows ) {
									echo '<tr><td>' . esc_html( $nation ) . ' (' . count( $shows ) . ')</td><td><ul class="this-year-shows showsonair">';
									foreach ( $shows as $show ) {
										echo '<li><a href="' . esc_url( $show['url'] ) . '">' . esc_html( $show['name'] ) . '</a> <small>(' . esc_html( $show['format'] ) . ')</small></li>';
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
		$thisyear      = ( isset( $thisyear ) ) ? $thisyear : date( 'Y' );
		$shows_started = array();
		$shows_queery  = LWTV_Loops::post_type_query( 'post_type_shows' );

		if ( $shows_queery->have_posts() ) {
			while ( $shows_queery->have_posts() ) {
				$shows_queery->the_post();
				$show_id   = get_the_ID();
				$show_name = preg_replace( '/\s*/', '', get_the_title( $show_id ) );
				$show_name = strtolower( $show_name );
				// If there are airdates saved...
				if ( get_post_meta( $show_id, 'lezshows_airdates', true ) ) {
					$airdates  = get_post_meta( $show_id, 'lezshows_airdates', true );
					$countries = get_the_term_list( $show_id, 'lez_country' );
					$formats   = get_the_term_list( $show_id, 'lez_formats' );

					// Shows that STARTED this year
					if ( $airdates['start'] === $thisyear ) {
						$shows_started[ $show_name ] = array(
							'url'     => get_permalink( $show_id ),
							'name'    => get_the_title( $show_id ),
							'status'  => get_post_status( $show_id ),
							'country' => wp_strip_all_tags( $countries ),
							'format'  => wp_strip_all_tags( $formats ),
						);
					}
				}
			}
		}
		?>

		<h3><a name="showsstart">Shows That Started This Year (<?php echo (int) $shows_this_year['started']; ?>)</a></h3>

		<?php
		if ( ! empty( $shows_started ) ) {
			echo '<ul class="this-year-shows showsstart">';
			foreach ( $shows_started as $show ) {
				$show_output = $show['name'];
				if ( 'publish' === $show['status'] ) {
					$show_output = '<a href="' . $show['url'] . '">' . $show['name'] . '</a> <small>(' . $show['country'] . ' - ' . $show['format'] . ')</small>';
				}
				echo '<li>' . wp_kses_post( $show_output ) . '</li>';
			}
			echo '</ul>';
		} else {
			echo '<p>No qualifying shows aired in ' . (int) $thisyear . '.</p>';
		}
		?>

		<h3><a name="showsend">Shows That Ended This Year (<?php echo (int) $shows_this_year['ended']; ?>)</a></h3>

		<?php
		if ( 0 !== $shows_this_year['ended'] ) {
			echo '<ul class="this-year-shows showsend">';
			foreach ( $shows_ended as $show ) {
				$show_output = $show['name'];
				if ( 'publish' === $show['status'] ) {
					$show_output = '<a href="' . $show['url'] . '">' . $show['name'] . '</a> <small>(' . $show['country'] . ' - ' . $show['format'] . ')</small>';
				}
				echo '<li>' . wp_kses_post( $show_output ) . '</li>';
			}
			echo '</ul>';
		} else {
			echo '<p>No qualifying shows aired in ' . (int) $thisyear . '.</p>';
		}
		wp_reset_query();
	}

	/**
	 * List of canceled shows for the year.
	 *
	 * @access public
	 * @param mixed $thisyear
	 * @return void
	 */
	public function canceled( $thisyear ) {
		$thisyear = ( isset( $thisyear ) ) ? $thisyear : date( 'Y' );
		// Constants
		$shows_this_year = array(
			'current' => 0,
			'ended'   => 0,
			'started' => 0,
		);
		$shows_current   = array();
		$shows_started   = array();
		$shows_ended     = array();
		$shows_queery    = LWTV_Loops::post_type_query( 'post_type_shows' );
		if ( $shows_queery->have_posts() ) {
			while ( $shows_queery->have_posts() ) {
				$shows_queery->the_post();
				$show_id   = get_the_ID();
				$show_name = preg_replace( '/\s*/', '', get_the_title( $show_id ) );
				$show_name = strtolower( $show_name );
				// Shows Currently Airing
				if ( get_post_meta( $show_id, 'lezshows_airdates', true ) ) {
					$airdates  = get_post_meta( $show_id, 'lezshows_airdates', true );
					$countries = get_the_term_list( $show_id, 'lez_country' );
					$formats   = get_the_term_list( $show_id, 'lez_formats' );
					if (
						( 'current' === $airdates['finish'] && date( 'Y' ) === $thisyear ) // Still Current and it's NOW
						|| ( $airdates['finish'] >= $thisyear && $airdates['start'] <= $thisyear ) // Airdates between
					) {
						// Currently Airing Shows shows for the current year only
						$shows_current[ $show_name ] = array(
							'url'     => get_permalink( $show_id ),
							'name'    => get_the_title( $show_id ),
							'status'  => get_post_status( $show_id ),
							'country' => wp_strip_all_tags( $countries ),
							'format'  => wp_strip_all_tags( $formats ),
						);
						$shows_this_year['current']++;
					}
					// Shows that ended this year
					if ( $airdates['finish'] === $thisyear ) {
						$shows_ended[ $show_name ] = array(
							'url'     => get_permalink( $show_id ),
							'name'    => get_the_title( $show_id ),
							'status'  => get_post_status( $show_id ),
							'country' => wp_strip_all_tags( $countries ),
							'format'  => wp_strip_all_tags( $formats ),
						);
						$shows_this_year['ended']++;
					}
					// Shows that STARTED this year
					if ( $airdates['start'] === $thisyear ) {
						$shows_started[ $show_name ] = array(
							'url'     => get_permalink( $show_id ),
							'name'    => get_the_title( $show_id ),
							'status'  => get_post_status( $show_id ),
							'country' => wp_strip_all_tags( $countries ),
							'format'  => wp_strip_all_tags( $formats ),
						);
						$shows_this_year['started']++;
					}
				}
			}
		}
		?>

		<h3><a name="showsonair">Shows Aired This Year (<?php echo (int) $shows_this_year['current']; ?>)</a></h3>

		<?php
		if ( 0 !== $shows_this_year['current'] ) {
			echo '<ul class="this-year-shows showsonair">';
			foreach ( $shows_current as $show ) {
				$show_output = $show['name'];
				if ( 'publish' === $show['status'] ) {
					$show_output = '<a href="' . $show['url'] . '">' . $show['name'] . '</a> <small>(' . $show['country'] . ' - ' . $show['format'] . ')</small>';
				}
				echo '<li>' . wp_kses_post( $show_output ) . '</li>';
			}
			echo '</ul>';
		} else {
			echo '<p>No qualifying shows aired in ' . (int) $thisyear . '.</p>';
		}
		?>

		<hr>

		<h3><a name="showsstart">Shows That Started This Year (<?php echo (int) $shows_this_year['started']; ?>)</a></h3>

		<?php
		if ( 0 !== $shows_this_year['started'] ) {
			echo '<ul class="this-year-shows showsstart">';
			foreach ( $shows_started as $show ) {
				$show_output = $show['name'];
				if ( 'publish' === $show['status'] ) {
					$show_output = '<a href="' . $show['url'] . '">' . $show['name'] . '</a> <small>(' . $show['country'] . ' - ' . $show['format'] . ')</small>';
				}
				echo '<li>' . wp_kses_post( $show_output ) . '</li>';
			}
			echo '</ul>';
		} else {
			echo '<p>No qualifying shows aired in ' . (int) $thisyear . '.</p>';
		}
		?>

		<h3><a name="showsend">Shows That Ended This Year (<?php echo (int) $shows_this_year['ended']; ?>)</a></h3>

		<?php
		if ( 0 !== $shows_this_year['ended'] ) {
			echo '<ul class="this-year-shows showsend">';
			foreach ( $shows_ended as $show ) {
				$show_output = $show['name'];
				if ( 'publish' === $show['status'] ) {
					$show_output = '<a href="' . $show['url'] . '">' . $show['name'] . '</a> <small>(' . $show['country'] . ' - ' . $show['format'] . ')</small>';
				}
				echo '<li>' . wp_kses_post( $show_output ) . '</li>';
			}
			echo '</ul>';
		} else {
			echo '<p>No qualifying shows aired in ' . (int) $thisyear . '.</p>';
		}
		wp_reset_query();
	}
}

new LWTV_This_Year_Shows();
