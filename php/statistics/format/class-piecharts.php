<?php

namespace LWTV\Statistics\Format;

class Piecharts {
	/*
	 * Statistics Display Piecharts
	 *
	 * Output the list of data usually from functions like self::meta_array
	 * It loops through the arrays and outputs data as needed
	 *
	 * This relies on ChartJS existing
	 *
	 * @param string $subject The content subject
	 * @param string $data The data 'subject' - used to generate the URLs
	 * @param array $data_array The array of data
	 *
	 * @return Content
	 */
	public function make( $subject, $data, $data_array ) {
		// Strip extra word(s) to make the chart key readable
		switch ( $data ) {
			case 'sexuality':
			case 'dead-sex':
				$fixname = 'sexual';
				$count   = lwtv_plugin()->generate_statistics( 'characters', 'all', 'count' );
				$center  = $count . ' Characters';
				break;
			case 'gender':
			case 'dead-gender':
				$fixname = 'gender';
				$count   = lwtv_plugin()->generate_statistics( 'characters', 'all', 'count' );
				$center  = $count . ' Characters';
				break;
			case 'dead-shows':
				$fixname = 'queers are dead';
				$count   = lwtv_plugin()->generate_statistics( 'shows', 'all', 'count' );
				$center  = $count . ' Shows';
				break;
			default:
				$fixname = '';
				$center  = '';
				break;
		}

		// We show empty sets for these:
		$show_zero = array( 'actor_char_dead', 'actor_char_roles' );

		// Top Bar
		$show_top = array( 'gender_year', 'sexuality_year' );
		$data_top = substr( $data, 0, -5 );

		// Strip hypens becuase ChartJS doesn't like it.
		$data = str_replace( '-', '', $data );

		if ( ! is_int( $data ) || ! in_array( $data, $show_zero, true ) ) {
			// @codingStandardsIgnoreStart
			// Reorder by item count
			usort( $data_array, function( $a, $b ) {
				return $a['count'] - $b['count'];
			} );
			// @codingStandardsIgnoreEnd
		}

		$check_count = 0;
		foreach ( $data_array as $item ) {
			if ( 0 !== $item['count'] ) {
				$check_count = $check_count + (int) $item['count'];
			}

			if ( in_array( $data, $show_zero, true ) ) {
				++$check_count;
			}
		}

		if ( 0 === $check_count ) {
			echo '<p><em>Coming Soon</em></p>';
			return;
		}

		?>

		<canvas
			id="pie<?php echo esc_attr( ucfirst( $data ) ); ?>"
			width="500px" height="500px"
			aria-label="A piechart for stats on <?php echo esc_html( $subject ); ?>"
		/>
			<p>Your browser cannot display this piechart for stats on <?php echo esc_html( $subject ); ?>.</p>
		</canvas>

		<script>
			// Piechart for stats
			var pie<?php echo esc_attr( ucfirst( $data ) ); ?>Dataset = [
				<?php
				foreach ( $data_array as $item ) {
					if ( 0 !== $item['count'] || in_array( $data, $show_zero, true ) ) {
						echo '"' . (int) $item['count'] . '", ';
					}
				}
				?>
				];
			var pie<?php echo esc_attr( ucfirst( $data ) ); ?>data = {
				labels : [
					<?php
					foreach ( $data_array as $item ) {
						if ( 0 !== $item['count'] || in_array( $data, $show_zero, true ) ) {
							$name = ucfirst( str_replace( $fixname, '', $item['name'] ) );
							echo '"' . wp_kses_post( $name ) . ' (' . (int) $item['count'] . ')", ';
						}
					}
					?>
				],
				datasets : [{
					data : pie<?php echo esc_attr( ucfirst( $data ) ); ?>Dataset,
					<?php
					if ( in_array( $data, $show_zero, true ) ) {
						?>
						backgroundColor: [
							'#5cb85c',
							'#06c',
							'#c0392b'
						],
						<?php
					} else {
						?>
						backgroundColor: palette('tol-rainbow', pie<?php echo esc_attr( ucfirst( $data ) ); ?>Dataset.length).map(function(hex) { return '#' + hex; }),
						<?php
					}
					?>
				}]
			};

			var ctx = document.getElementById("pie<?php echo esc_attr( ucfirst( $data ) ); ?>").getContext("2d");
			var pie<?php echo esc_attr( ucfirst( $data ) ); ?> = new Chart(ctx,{
				type:'doughnut',
				data: pie<?php echo esc_attr( ucfirst( $data ) ); ?>data,
				options: {
					elements: {
						center: {
							text: '<?php echo esc_html( $center ); ?>',
						}
					},
					tooltips: {
						callbacks: {
							label: function(tooltipItem, data) {
								return data.labels[tooltipItem.index];
							}
						},
					},
					plugins: {
						legend: {
							<?php
							if ( in_array( $data, $show_zero, true ) ) {
								?>
								position: 'bottom',
								<?php
							} elseif ( in_array( $data_top, $show_top, true ) ) {
								?>
								position: 'top',
								<?php
							} else {
								// Everything else has a sidebar so we don't need both... Do we?
								?>
								display: false,
								<?php
							}
							?>
							labels: {
								boxWidth: 10,
							}
						}
					}
				}
			});
		</script>
		<?php
	}
}
