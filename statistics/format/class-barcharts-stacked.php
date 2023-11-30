<?php

class LWTV_Statistics_Barcharts_Stacked_Format {
	/*
	 * Statistics Stacked Barcharts
	 *
	 * Output the list of data usually from functions like self::meta_array
	 * It loops through the arrays and outputs data as needed
	 *
	 * This relies on ChartJS existing
	 *
	 * @param string $subject The content subject (shows, characters)
	 * @param string $data The data - used to generate the URLs
	 * @param array $data_array The array of data
	 *
	 * @return Content
	 */
	public function make( $subject, $data, $data_array ) {

		$count     = count( $data_array );
		$step_size = '5';
		$height    = max( ( $count * 20 ), 30 ) + 20;

		// [main-term-subtax]
		// [main taxonomy]-[term of main]-[subtaxonomy to parse]
		// ex: [country-all-gender]
		//     [station-abc-sexuality]
		//     [country-usa-all]
		$pieces      = explode( '_', $data );
		$data_main   = $pieces[0];
		$data_term   = ( isset( $pieces[1] ) ) ? $pieces[1] : 'all';
		$data_subtax = ( isset( $pieces[2] ) ) ? $pieces[2] : 'all';

		// Define our settings
		switch ( $data_subtax ) {
			case 'gender':
			case 'sexuality':
			case 'romantic':
			case 'tropes':
				$data_sets  = array();
				$term_array = array(
					'taxonomy'   => 'lez_' . $data_subtax,
					'orderby'    => 'count',
					'order'      => 'DESC',
					'hide_empty' => 0,
				);
				$terms      = get_terms( $term_array );
				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
					foreach ( $terms as $term ) {
						$data_sets[] = $term->slug;
					}
				}
				$counter = 'characters';
				break;
		}
		?>
		<div id="container" style="width: 100%;">
			<canvas
				id="barStacked<?php echo esc_attr( ucfirst( $subject ) ) . esc_attr( ucfirst( $data_main ) ); ?>"
				width="700" height="<?php echo (int) $height; ?>"
				aria-label="A Stacked Bar Chart for stats on <?php echo esc_html( $subject ); ?>"
			/>
				<p>Your browser cannot display this Stacked Bar Chart for stats on <?php echo esc_html( $subject ); ?>.</p>
			</canvas>
		</div>

		<?php
		// Build out the colors ...
		$colors_array   = array();
		$colors_hexcode = array( '781c81', '61187e', '531b7f', '4a2384', '442e8a', '413b93', '3f499d', '3f58a8', '4066b2', '4273bb', '447fc0', '488ac2', '4c94bf', '519cb8', '57a3ae', '5ea9a2', '65ae95', '6db388', '76b67d', '7fb972', '88bb69', '92bd60', '9cbe59', 'a7be53', 'b1be4e', 'babc49', 'c3ba45', 'ccb742', 'd3b33f', 'daad3c', 'dfa539', 'e39c37', 'e59134', 'e78432', 'e7752f', 'e6652d', 'e4542a', 'e14326', 'dd3123', 'd92120' );
			shuffle( $colors_hexcode );
		$color_count = 0;
		foreach ( $data_sets as $label ) {
			$name                  = ( 'undefined' === $label ) ? 'nundefined' : str_replace( array( '-', '-', '-' ), '', $label );
			$colors_array[ $name ] = '#' . $colors_hexcode[ $color_count ];
			++$color_count;
		}
		?>

		<script>
		// Defaults
		Chart.defaults.responsive = true;
		Chart.defaults.plugins.legend.display = false;

		// Bar Chart
		var barStacked<?php echo esc_attr( ucfirst( $subject ) ) . esc_attr( ucfirst( $data_main ) ); ?>Data = {
			labels : [
			<?php
			foreach ( $data_array as $item ) {
				$name = $item['name'];
				echo '"' . wp_kses_post( $name ) . ' (' . (int) $item[ $counter ] . ')", ';
			}
			?>
			],
			datasets: [
			<?php
			foreach ( $data_sets as $label ) {
				$color = ( 'undefined' === $label ) ? 'nundefined' : str_replace( array( '-', '-', '-' ), '', $label );
				?>
				{
					borderWidth: 1,
					label: '<?php echo wp_kses_post( ucfirst( $label ) ); ?>',
					stack: 'Stack',
					data : [
					<?php
					foreach ( $data_array as $name => $item ) {
						if ( isset( $item['dataset'][ $label ] ) ) {
							echo wp_kses_post( $item['dataset'][ $label ] ) . ',';
						} else {
							echo '0,';
						}
					}
					?>
					],
					backgroundColor:
					<?php
					echo '"' . esc_attr( $colors_array[ $color ] ) . '"';
					?>
					,
				},
				<?php
			}
			?>
			]
		};
		var ctx = document.getElementById("barStacked<?php echo esc_attr( ucfirst( $subject ) ) . esc_attr( ucfirst( $data_main ) ); ?>").getContext("2d");
		var barStacked<?php echo esc_attr( ucfirst( $subject ) ) . esc_attr( ucfirst( $data_main ) ); ?> = new Chart(ctx, {
			type: 'bar',
			data: barStacked<?php echo esc_attr( ucfirst( $subject ) ) . esc_attr( ucfirst( $data_main ) ); ?>Data,
			options: {
				indexAxis: 'y',
				scales: {
					x: { stacked: true },
					y: { stacked: true }
				},
				tooltips: {
					mode: 'index',
					intersect: false
				},
			}
		});
		</script>

		<?php
	}
}
