<?php
/**
 * Characters for The Year
 *
 * @package LezWatch.TV
 */

class LWTV_This_Year_Chars {

	public static function toc() {
		?>
		<section id="toc" class="toc-container card-body">
			<nav class="breadcrumb">
				<h4 class="toc-title">Go to:</h4>
				<a class="breadcrumb-item smoothscroll" href="#onair">Characters On Air</a>
				<a class="breadcrumb-item smoothscroll" href="#died">Characters Who Died</a>
			</nav>
		</section>
		<?php
	}

	/**
	 * List the dead
	 * @param  [type] $thisyear [description]
	 * @return [type]           [description]
	 */
	public static function dead( $thisyear ) {
		$thisyear   = ( isset( $thisyear ) ) ? $thisyear : date( 'Y' );
		$dead_loop  = LWTV_Loops::post_meta_query( 'post_type_characters', 'lezchars_death_year', $thisyear, 'REGEXP' );
		$queery     = wp_list_pluck( $dead_loop->posts, 'ID' );
		$list_array = array();
		$show_array = array();
		?>
		<h2><a name="died"><?php echo (int) $dead_loop->post_count; ?> Characters Died</a></h2>

		<p>&nbsp;</p>

		<?php
		// List all queers and the year they died
		if ( $dead_loop->have_posts() ) {
			foreach ( $queery as $char ) {
				// Since SOME characters have multiple shows, we force this to be an array
				$show_ids   = get_post_meta( $char, 'lezchars_show_group', true );
				$show_title = array();

				foreach ( $show_ids as $each_show ) {

					// Get some defaults
					$char_slug = get_post_field( 'post_name', get_post( $char ) );
					$show_slug = get_post_field( 'post_name', get_post( $each_show['show'] ) );

					if ( isset( $each_show['appears'] ) && in_array( $thisyear, $each_show['appears'] ) ) {
						// if the show isn't already in the array, we create it
						if ( ! isset( $show_array[ $show_slug ] ) ) {
							$show_array[ $show_slug ] = array(
								'name'  => get_the_title( $each_show['show'] ),
								'url'   => get_the_permalink( $each_show['show'] ),
								'chars' => array(),
							);
						}

						// Add the character to the show array
						$show_array[ $show_slug ]['chars'][ $char_slug ] = array(
							'name'  => get_the_title( $char ),
							'url'   => get_the_permalink( $char ),
							'type'  => $each_show['type'],
						);
					}

					// if the show isn't published, no links
					if ( get_post_status( $each_show['show'] ) !== 'publish' ) {
						array_push( $show_title, '<em><span class="disabled-show-link">' . get_the_title( $each_show['show'] ) . '</span></em> <small>(' . $each_show['type'] . ' character)</small>' );
					} else {
						array_push( $show_title, '<em><a href="' . get_permalink( $each_show['show'] ) . '">' . get_the_title( $each_show['show'] ) . '</a></em> <small>(' . $each_show['type'] . ' character)</small>' );
					}
				}
				$show_info = implode( ', ', $show_title );
				// Only extract the date for this year and convert to unix time
				// Jesus, I hope no one dies twice in the same year ... SARA
				$died_date = get_post_meta( $char, 'lezchars_death_year', true );
				foreach ( $died_date as $date ) {
					if ( (int) substr( $date, 0, 4 ) === (int) substr( $date, 0, 4 ) ) {
						$died_year  = substr( $date, 0, 4 );
						$died_array = date_parse_from_format( 'Y-m-d', $date );
					} else {
						$died_year  = substr( $date, -4 );
						$died_array = date_parse_from_format( 'm/d/Y', $date );
					}
					if ( $died_year === $thisyear ) {
						$died = mktime( $died_array['hour'], $died_array['minute'], $died_array['second'], $died_array['month'], $died_array['day'], $died_array['year'] );
					}
				}

				// Make a list
				$list_array[ $died ][ $char_slug ] = array(
					'name'  => get_the_title( $char ),
					'url'   => get_the_permalink( $char ),
					'shows' => $show_info,
				);
			}

			// Sort alphabetical
			ksort( $list_array );

			?>
			<div class="container">
				<div class="row">
					<div class="col">
						<h3>By Date</h3>

						<table class="table table-md table-hover table-striped">
							<thead class="thead-dark">
								<tr>
									<th style="width: 150px;" scope="col">Date</th>
									<th scope="col">Character(s)</th>
								</tr>
							</thead>
							<tbody>
								<?php
								foreach ( $list_array as $date => $chars ) {
									echo '<tr>';
									echo '<td>' . esc_html( date( 'd F', $date ) ) . ' (' . count( $chars ) . ')</td>';
									echo '<td><ul>';
									foreach ( $chars as $char ) {
										echo '<li><a href="' . esc_url( $char['url'] ) . '">' . esc_html( $char['name'] ) . '</a> - ' . wp_kses_post( $char['shows'] ) . '</li>';
									}
									echo '</ul></td>';
									echo '</tr>';
								}
								?>
							</tbody>
						</table>
					</div>
					<div class="col">
						<h3>By Show</h3>
						<table class="table table-md table-hover table-striped">
							<thead class="thead-dark">
								<tr>
									<th style="width: 200px;" scope="col">Show</th>
									<th scope="col">Character(s)</th>
								</tr>
							</thead>
							<tbody>
								<?php
								foreach ( $show_array as $show ) {
									echo '<tr><td><em><a href="' . esc_url( $show['url'] ) . '">' . esc_html( $show['name'] ) . '</a></em> (' . count( $show['chars'] ) . ')</td><td><ul>';
									foreach ( $show['chars'] as $char ) {
										echo '<li><a href="' . esc_url( $char['url'] ) . '">' . esc_html( $char['name'] ) . '</a> <small>(' . esc_html( $char['type'] ) . ' character)</small></li>';
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
			?>
			<p>No known characters died in <?php echo (int) $thisyear; ?>.</p>
			<?php
		}
		wp_reset_query();
	}

	/**
	 * List of characters for the year.
	 *
	 * @access public
	 * @param mixed $thisyear
	 * @return void
	 */
	public static function list( $thisyear ) {
		$fail_msg   = '<p>No characters were on air in ' . $thisyear . '.</p>';
		$thisyear   = ( isset( $thisyear ) ) ? $thisyear : date( 'Y' );
		$loop       = LWTV_Loops::post_meta_query( 'post_type_characters', 'lezchars_show_group', $thisyear, 'REGEXP' );
		$queery     = wp_list_pluck( $loop->posts, 'ID' );
		$char_array = array();
		$show_array = array();
		?>

		<h2><?php echo (int) $loop->post_count; ?> Characters On Air</h2>

		<p>&nbsp;</p>

		<?php

		if ( $loop->have_posts() ) {
			$has_chars = true;
			foreach ( $queery as $char ) {
				$show_ids   = get_post_meta( $char, 'lezchars_show_group', true );
				$show_title = array();
				foreach ( $show_ids as $each_show ) {
					// Make sure this show is in the year
					if ( in_array( $thisyear, $each_show['appears'], true ) ) {
						// Get some defaults
						$char_slug = get_post_field( 'post_name', get_post( $char ) );
						$show_slug = get_post_field( 'post_name', get_post( $each_show['show'] ) );

						// if the show isn't already in the array, we create it
						if ( ! isset( $show_array[ $show_slug ] ) ) {
							$show_array[ $show_slug ] = array(
								'name'  => get_the_title( $each_show['show'] ),
								'url'   => get_the_permalink( $each_show['show'] ),
								'chars' => array(),
							);
						}

						// Add the character to the show array
						$show_array[ $show_slug ]['chars'][ $char_slug ] = array(
							'name'  => get_the_title( $char ),
							'url'   => get_the_permalink( $char ),
							'type'  => $each_show['type'],
						);

						// if the show isn't published, no links
						if ( get_post_status( $each_show['show'] ) !== 'publish' ) {
							array_push( $show_title, '<em><span class="disabled-show-link">' . get_the_title( $each_show['show'] ) . '</span></em> <small>(' . $each_show['type'] . ' character)</small>' );
						} else {
							array_push( $show_title, '<em><a href="' . get_permalink( $each_show['show'] ) . '">' . get_the_title( $each_show['show'] ) . '</a></em> <small>(' . $each_show['type'] . ' character)</small>' );
						}
					}
					$show_info = implode( ', ', $show_title );
				}

				// If there are shows listed, let's add it to the character array
				if ( isset( $show_info ) ) {
					$char_array[ $char_slug ] = array(
						'name'  => get_the_title( $char ),
						'url'   => get_the_permalink( $char ),
						'shows' => $show_info,
					);
				}
			}

			// If the data isn't empty, we go!
			if ( ! empty( $char_array ) && ! empty( $show_array ) ) {

				// Sort arrays
				ksort( $char_array );
				ksort( $show_array );

				// List everyone by character
				?>
				<div class="container">
					<div class="row">
						<div class="col">
							<h3>By Character Name</h3>
							<table class="table table-md table-hover table-striped">
								<thead class="thead-light">
									<tr>
										<th scope="col">Name</th>
										<th scope="col">Show(s)</th>
									</tr>
								</thead>
								<tbody>
									<?php
									foreach ( $char_array as $the_char ) {
										echo '<tr>';
										echo '<td><a href="' . esc_url( $the_char['url'] ) . '">' . esc_html( $the_char['name'] ) . '</a></td>';
										echo '<td>' . wp_kses_post( $the_char['shows'] ) . '</td>';
										echo '</tr>';
									}
									?>
								</tbody>
							</table>
						</div>
						<div class="col">
							<h3>By Show</h3>

							<table class="table table-md table-hover table-striped">
								<thead class="thead-light">
									<tr>
										<th style="width: 200px;" scope="col">Show</th>
										<th scope="col">Character(s)</th>
									</tr>
								</thead>
								<tbody>
									<?php
									foreach ( $show_array as $show ) {
										echo '<tr><td><em><a href="' . esc_url( $show['url'] ) . '">' . esc_html( $show['name'] ) . '</a></em> (' . count( $show['chars'] ) . ')</td><td><ul>';
										foreach ( $show['chars'] as $char ) {
											echo '<li><a href="' . esc_url( $char['url'] ) . '">' . esc_html( $char['name'] ) . '</a> <small>(' . esc_html( $char['type'] ) . ' character)</small></li>';
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
				$has_chars = false;
			}
		} else {
			$has_chars = false;
		}

		// If we got here, we have no characters today. Whomp whomp.
		if ( ! $has_chars ) {
			echo wp_kses_post( $fail_msg );
		}

		wp_reset_query();
	}
}

new LWTV_This_Year_Chars();
