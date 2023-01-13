<?php
/**
 * Characters for The Year
 *
 * @package LezWatch.TV
 */

class LWTV_This_Year_Chars {

	/**
	 * Output the dead
	 * @param  [type] $thisyear [description]
	 * @return [type]           [description]
	 */
	public function dead( $thisyear ) {
		$thisyear   = ( isset( $thisyear ) ) ? $thisyear : gmdate( 'Y' );
		$char_array = self::get_dead( $thisyear );
		$list_array = $char_array['list'];
		$show_array = $char_array['show'];
		?>
		<h2><a name="died"><?php echo (int) $char_array['count']; ?> Characters Died</a></h2>

		<p>&nbsp;</p>

		<?php
		if ( ! empty( $list_array ) ) {
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
									echo '<td>' . esc_html( gmdate( 'd F', $date ) ) . ' (' . count( $chars ) . ')</td>';
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
									echo '<tr><td><em><a href="' . esc_url( $show['url'] ) . '">' . esc_html( $show['name'] ) . '</a></em> (' . count( $show['chars'] ) . ')';
									echo '<br /><small>(' . wp_kses_post( $show['country'] ) . ' - ' . wp_kses_post( $show['format'] ) . ')</small></td><td><ul>';
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
			echo '<p>No known characters died in ' . (int) $thisyear . '.</p>';
		}
	}

	/**
	 * List of characters for the year.
	 *
	 * @access public
	 * @param mixed $thisyear
	 * @return void
	 */
	public function list( $thisyear ) {
		$thisyear   = ( isset( $thisyear ) ) ? $thisyear : gmdate( 'Y' );
		$loop_array = self::get_list( $thisyear );
		$char_array = $loop_array['list'];
		$show_array = $loop_array['show'];
		?>

		<h2><?php echo (int) $loop_array['count']; ?> Characters On Air</h2>

		<p>&nbsp;</p>

		<?php
		// If the data isn't empty, we go!
		if ( ! empty( $char_array ) && ! empty( $show_array ) ) {

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
									echo '<tr><td><em><a href="' . esc_url( $show['url'] ) . '">' . esc_html( $show['name'] ) . '</a></em> (' . count( $show['chars'] ) . ')';
									echo '<br /><small>(' . wp_kses_post( $show['country'] ) . ' - ' . wp_kses_post( $show['format'] ) . ')</small></td><td><ul>';
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
			echo '<p>No characters were on air in ' . (int) $thisyear . '.</p>';
		}
	}

	/**
	 * Get a list of the dead
	 * @param  int     $thisyear The year
	 * @param  boolean $count    Just a count?
	 * @return array             All the data you need.
	 */
	public function get_dead( $thisyear, $count = false ) {
		$return_array = array();
		$thisyear     = ( isset( $thisyear ) ) ? $thisyear : gmdate( 'Y' );
		$dead_loop    = ( new LWTV_Loops() )->post_meta_query( 'post_type_characters', 'lezchars_death_year', $thisyear, 'REGEXP' );

		if ( $dead_loop->have_posts() ) {
			$queery = wp_list_pluck( $dead_loop->posts, 'ID' );
			wp_reset_query();
		}

		$show_array = array();
		$list_array = array();

		// List all queers and the year they died
		if ( isset( $queery ) && is_array( $queery ) ) {
			foreach ( $queery as $char ) {
				$show_ids_raw = get_post_meta( $char, 'lezchars_show_group', true );
				$show_title   = array();
				$show_ids     = array();

				// If the character is in a show this year, we'll add it.
				foreach ( $show_ids_raw as $each_show ) {
					if ( array_key_exists( 'appears', $each_show ) && in_array( $thisyear, $each_show['appears'], true ) ) {
						$show_ids[] = $each_show;
					}
				}

				// If we didn't add anything, use everything.
				if ( empty( $show_ids ) ) {
					$show_ids = $show_ids_raw;
				}

				foreach ( $show_ids as $each_show ) {

					// Get some defaults
					$char_slug = get_post_field( 'post_name', get_post( $char ) );
					$show_slug = get_post_field( 'post_name', get_post( $each_show['show'] ) );

					// if the show isn't already in the array, we create it
					if ( ! empty( $show_slug ) && ! array_key_exists( $show_slug, $show_array ) ) {
						$show_array[ $show_slug ] = array(
							'name'    => get_the_title( $each_show['show'] ),
							'url'     => get_the_permalink( $each_show['show'] ),
							'country' => get_the_term_list( $each_show['show'], 'lez_country', '', ', ', '' ),
							'format'  => get_the_term_list( $each_show['show'], 'lez_formats' ),
							'chars'   => array(),
						);
					}

					// Add the character to the show array
					$show_array[ $show_slug ]['chars'][ $char_slug ] = array(
						'name' => get_the_title( $char ),
						'url'  => get_the_permalink( $char ),
						'type' => $each_show['type'],
					);

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

				if ( isset( $died ) ) {
					// Make a list
					$list_array[ $died ][ $char_slug ] = array(
						'name'  => get_the_title( $char ),
						'url'   => get_the_permalink( $char ),
						'shows' => $show_info,
					);
				}
			}

			// Sort alphabetical
			ksort( $list_array );
			ksort( $show_array );
		}

		if ( isset( $queery ) ) {
			// if we counted, just kick that back.
			if ( $count ) {
				$return_array = count( $queery );
			} else {
				$return_array = array(
					'count' => count( $queery ),
					'list'  => $list_array,
					'show'  => $show_array,
				);
			}
		}

		return $return_array;

	}

	/**
	 * Get a list of the characters for a year
	 * @param  int     $thisyear The year
	 * @param  boolean $count    Just a count?
	 * @return array             All the data you need.
	 */
	public function get_list( $thisyear, $count = false ) {
		$thisyear      = ( isset( $thisyear ) ) ? $thisyear : gmdate( 'Y' );
		$loop          = ( new LWTV_Loops() )->post_meta_query( 'post_type_characters', 'lezchars_show_group', $thisyear, 'REGEXP' );

		if ( $loop->have_posts() ) {
			$queery = wp_list_pluck( $loop->posts, 'ID' );
			wp_reset_query();
		}

		$counted_chars = 0;
		$show_array    = array();
		$char_array    = array();

		if ( is_array( $queery ) ) {
			foreach ( $queery as $char ) {
				$show_ids   = get_post_meta( $char, 'lezchars_show_group', true );
				$show_title = array();
				foreach ( $show_ids as $each_show ) {
					// Make sure this show is in the year
					if ( array_key_exists( 'appears', $each_show ) && in_array( $thisyear, $each_show['appears'], true ) ) {
						$counted_chars++;

						// If we're ONLY counting, we can bail now.
						if ( ! $count ) {
							// Get some defaults
							$char_slug = get_post_field( 'post_name', get_post( $char ) );
							$show_slug = get_post_field( 'post_name', get_post( $each_show['show'] ) );

							// if the show isn't already in the array, we create it
							if ( ! array_key_exists( $show_slug, $show_array ) ) {
								$show_array[ $show_slug ] = array(
									'name'    => get_the_title( $each_show['show'] ),
									'url'     => get_the_permalink( $each_show['show'] ),
									'country' => get_the_term_list( $each_show['show'], 'lez_country', '', ', ', '' ),
									'format'  => get_the_term_list( $each_show['show'], 'lez_formats' ),
									'chars'   => array(),
								);
							}

							// Add the character to the show array
							$show_array[ $show_slug ]['chars'][ $char_slug ] = array(
								'name' => get_the_title( $char ),
								'url'  => get_the_permalink( $char ),
								'type' => $each_show['type'],
							);

							// if the show isn't published, no links
							if ( get_post_status( $each_show['show'] ) !== 'publish' ) {
								array_push( $show_title, '<em><span class="disabled-show-link">' . get_the_title( $each_show['show'] ) . '</span></em> <small>(' . $each_show['type'] . ' character)</small>' );
							} else {
								array_push( $show_title, '<em><a href="' . get_permalink( $each_show['show'] ) . '">' . get_the_title( $each_show['show'] ) . '</a></em> <small>(' . $each_show['type'] . ' character)</small>' );
							}
						}
					}
					if ( ! $count ) {
						$show_info = implode( ', ', $show_title );
					}
				}

				// If there are shows listed, let's add it to the character array
				if ( isset( $show_info ) && '' !== $show_info ) {
					$char_array[ $char_slug ] = array(
						'name'  => get_the_title( $char ),
						'url'   => get_the_permalink( $char ),
						'shows' => $show_info,
					);
				}
			}
		}

		// if we counted, just kick that back.
		if ( $count ) {
			$return_array = $counted_chars;
		} else {
			// Sort arrays
			ksort( $char_array );
			ksort( $show_array );

			$return_array = array(
				'count' => $counted_chars,
				'list'  => $char_array,
				'show'  => $show_array,
			);
		}

		return $return_array;
	}

}

new LWTV_This_Year_Chars();
