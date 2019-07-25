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
		?>
		<h2><a name="died"><?php echo (int) $dead_loop->post_count; ?> Characters Died</a></h2>

		<?php
		// List all queers and the year they died
		if ( $dead_loop->have_posts() ) {
			foreach ( $queery as $char ) {
				// Since SOME characters have multiple shows, we force this to be an array
				$show_ids   = get_post_meta( $char, 'lezchars_show_group', true );
				$show_title = array();
				foreach ( $show_ids as $each_show ) {
					// if the show isn't published, no links
					if ( get_post_status( $each_show['show'] ) !== 'publish' ) {
						array_push( $show_title, '<em><span class="disabled-show-link">' . get_the_title( $each_show['show'] ) . '</span></em> (' . $each_show['type'] . ' character)' );
					} else {
						array_push( $show_title, '<em><a href="' . get_permalink( $each_show['show'] ) . '">' . get_the_title( $each_show['show'] ) . '</a></em> (' . $each_show['type'] . ' character)' );
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
				// Get the post slug
				$post_slug = get_post_field( 'post_name', get_post( $char ) );
				// Make a list
				$list_array[ $died ][ $post_slug ] = array(
					'name'  => get_the_title( $char ),
					'url'   => get_the_permalink( $char ),
					'shows' => $show_info,
				);
			}

			// Sort alphabetical
			ksort( $list_array );

			?>
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
						echo '<td>' . esc_html( date( 'd F', $date ) ) . '</td>';
						echo '<td>';
						foreach ( $chars as $char ) {
							echo '&bull; <a href="' . esc_url( $char['url'] ) . '">' . esc_html( $char['name'] ) . '</a> - ' . wp_kses_post( $char['shows'] ) . '<br />';
						}
						echo '</td>';
						echo '</tr>';
					}
					?>
				</tbody>
			</table>
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
		$list_array = array();

		?>
		<h2><a name="onair"><?php echo (int) $loop->post_count; ?> Characters On Air</a></h2>
		<?php

		if ( $loop->have_posts() ) {
			foreach ( $queery as $char ) {
				$show_ids   = get_post_meta( $char, 'lezchars_show_group', true );
				$show_title = array();
				foreach ( $show_ids as $each_show ) {
					// Make sure this show is in the year
					if ( in_array( $thisyear, $each_show['appears'], true ) ) {
						// if the show isn't published, no links
						if ( get_post_status( $each_show['show'] ) !== 'publish' ) {
							array_push( $show_title, '<em><span class="disabled-show-link">' . get_the_title( $each_show['show'] ) . '</span></em> (' . $each_show['type'] . ' character)' );
						} else {
							array_push( $show_title, '<em><a href="' . get_permalink( $each_show['show'] ) . '">' . get_the_title( $each_show['show'] ) . '</a></em> (' . $each_show['type'] . ' character)' );
						}
					}
					$show_info = implode( ', ', $show_title );
				}
				// If there are shows listed, let's make the character
				if ( isset( $show_info ) ) {
					$post_slug                = get_post_field( 'post_name', get_post( $char ) );
					$list_array[ $post_slug ] = array(
						'name'  => get_the_title( $char ),
						'url'   => get_the_permalink( $char ),
						'shows' => $show_info,
					);
				}
			}

			if ( ! empty( $list_array ) ) {
				// What would be BEST would be to sort alphabetical by show!
				// Can I combine? I did with dates!
				?>
				<table class="table table-md table-hover">
					<thead>
						<tr>
							<th scope="col">Name</th>
							<th scope="col">Shows</th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ( $list_array as $the_char ) {
							echo '<tr>';
							echo '<td><a href="' . esc_url( $the_char['url'] ) . '">' . esc_html( $the_char['name'] ) . '</a></td>';
							echo '<td>' . wp_kses_post( $the_char['shows'] ) . '</td>';
							echo '</tr>';
						}
						?>
					</tbody>
				</table>
				<?php
			} else {
				echo wp_kses_post( $fail_msg );
			}
		} else {
			echo wp_kses_post( $fail_msg );
		}
		wp_reset_query();
	}
}

new LWTV_This_Year_Chars();
