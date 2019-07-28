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
	 * Output the dead
	 * @param  [type] $thisyear [description]
	 * @return [type]           [description]
	 */
	public static function dead( $thisyear ) {
		$thisyear   = ( isset( $thisyear ) ) ? $thisyear : date( 'Y' );
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
	public static function list( $thisyear ) {
		$thisyear   = ( isset( $thisyear ) ) ? $thisyear : date( 'Y' );
		$loop_array = self::get_list( $thisyear );
		$char_array = $loop_array['list'];
		$show_array = $loop_array['show'];
		?>

		<h2><?php echo (int) $loop_array['count']; ?> Characters On Air</h2>

		<p>&nbsp;</p>

		<div class="alert alert-warning" role="alert"><strong>Notice:</strong> The data generated here is currently curently incomplete and not fully representative of all characters on air at this time. We are working to improve accuracy and hope to be finished soon.</div>

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
			echo '<p>No characters were on air in ' . (int) $thisyear . '.</p>';
		}
	}

	/**
	 * Get a list of the dead
	 * @param  int     $thisyear The year
	 * @param  boolean $count    Just a count?
	 * @return array             All the data you need.
	 */
	public static function get_dead( $thisyear, $count = false ) {
		$thisyear  = ( isset( $thisyear ) ) ? $thisyear : date( 'Y' );
		$dead_loop = LWTV_Loops::post_meta_query( 'post_type_characters', 'lezchars_death_year', $thisyear, 'REGEXP' );
		$queery    = wp_list_pluck( $dead_loop->posts, 'ID' );

		// List all queers and the year they died
		if ( $dead_loop->have_posts() && ! $count ) {
			foreach ( $queery as $char ) {
				$show_ids_raw = get_post_meta( $char, 'lezchars_show_group', true );
				$show_title   = array();
				$show_ids     = array();

				// If the character is in a show this year, we'll add it.
				foreach ( $show_ids_raw as $each_show ) {
					if ( isset( $each_show['appears'] ) && in_array( $thisyear, $each_show['appears'], true ) ) {
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
					if ( ! isset( $show_array[ $show_slug ] ) ) {
						$show_array[ $show_slug ] = array(
							'name'  => get_the_title( $each_show['show'] ),
							'url'   => get_the_permalink( $each_show['show'] ),
							'chars' => array(),
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

				// Make a list
				$list_array[ $died ][ $char_slug ] = array(
					'name'  => get_the_title( $char ),
					'url'   => get_the_permalink( $char ),
					'shows' => $show_info,
				);
			}

			// Sort alphabetical
			ksort( $list_array );
			ksort( $show_array );
		}

		wp_reset_query();

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

		return $return_array;

	}

	/**
	 * Get a list of the characters for a year
	 * @param  int     $thisyear The year
	 * @param  boolean $count    Just a count?
	 * @return array             All the data you need.
	 */
	public static function get_list( $thisyear, $count = false ) {
		$thisyear      = ( isset( $thisyear ) ) ? $thisyear : date( 'Y' );
		$loop          = LWTV_Loops::post_meta_query( 'post_type_characters', 'lezchars_show_group', $thisyear, 'REGEXP' );
		$queery        = wp_list_pluck( $loop->posts, 'ID' );
		$counted_chars = 0;

		if ( $loop->have_posts() && ! $count ) {
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
		}

		wp_reset_query();

		// if we counted, just kick that back.
		if ( $count ) {
			$return_array = count( $queery );
		} else {
			// Sort arrays
			ksort( $char_array );
			ksort( $show_array );

			$return_array = array(
				'count' => count( $queery ),
				'list'  => $char_array,
				'show'  => $show_array,
			);
		}

		return $return_array;
	}

}

new LWTV_This_Year_Chars();
