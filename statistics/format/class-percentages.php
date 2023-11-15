<?php

class LWTV_Stats_Percentages {
	/*
	 * Statistics Display Percentages
	 *
	 * Output the list of data usually from functions like self::meta_array
	 * It loops through the arrays and outputs data as needed
	 *
	 * @param string $subject    The content subject
	 * @param string $data       The data 'subject' - used to generate the URLs
	 * @param array  $data_array The array of data
	 * @param string $count      The count of posts
	 *
	 * @return Content
	 */
	public function generate( $subject, $data, $data_array, $count ) {
		$pieces = preg_split( '(_|-)', $data );
		if ( in_array( 'country', $pieces, true ) ) {
			$count = 0;
			foreach ( $data_array as $key => $item ) {
				$count += $item['count'];
			}
		} elseif ( in_array( 'dead', $pieces, true ) && ! in_array( 'shows', $pieces ) ) { // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
			$to_count = get_term_by( 'slug', 'dead', 'lez_cliches' );
			$count    = $to_count->count;

			if ( 'nations' === $pieces[1] || 'stations' === $pieces[1] ) {
				$second_title = 'Percent <br />of ' . ucfirst( $pieces[1] ) . '\'s Characters';
			}
		} elseif ( 'per-char' === $data ) {
			$count = wp_count_posts( 'post_type_characters' )->publish;
		}

		if ( ! in_array( 'dead', $pieces, true ) ) {
			// Reorder by item count
			usort(
				$data_array,
				function ( $a, $b ) {
					return $a['count'] - $b['count'];
				}
			);
		}

		?>
		<table id="<?php echo esc_attr( $subject ); ?>Table" class="tablesorter table table-striped table-hover">
			<thead>
				<tr>
					<th scope="col">&nbsp;</th>
					<th scope="col">
						Count
						<?php
						if ( isset( $second_title ) ) {
							echo '<br />of Dead Characters';
						}
						?>
					</th>
					<th scope="col">
						Percent
						<?php
						if ( isset( $second_title ) ) {
							echo '<br />of Dead Characters';
						}
						?>
					</th>
					<?php
					if ( isset( $second_title ) ) {
						echo '<th scope="col">' . wp_kses_post( $second_title ) . '</th>';
					}
					?>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ( $data_array as $item ) {
					if ( 0 !== $item['count'] ) {
						$first_count = round( ( ( $item['count'] / $count ) * 100 ), 1 );
						echo '<tr>';
							echo '<th scope="row"><a href="' . esc_url( $item['url'] ) . '">' . wp_kses_post( ucfirst( $item['name'] ) ) . '</a></th>';
							echo '<td>' . (int) $item['count'] . '</td>';
							echo '<td><div class="progress"><div class="progress-bar bg-info" role="progressbar" style="width: ' . esc_html( $first_count ) . '%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div></div>&nbsp;' . esc_html( $first_count ) . '%</td>';
						if ( isset( $second_title ) ) {
							// how many characters per station/nation?
							$second_count = round( ( ( $item['count'] / $item['characters'] ) * 100 ), 1 );
							echo '<td><div class="progress" style="height: 20px;"><div class="progress-bar bg-info" role="progressbar" style="width: ' . esc_html( $second_count ) . '%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div></div><center>' . esc_html( $second_count ) . '%</center></td>';
						}
						echo '</tr>';
					}
				}
				?>
			</tbody>
		</table>
		<?php
	}
}

new LWTV_Stats_Percentages();
