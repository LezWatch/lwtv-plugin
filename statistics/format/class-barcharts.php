<?php

class LWTV_Stats_Barcharts {

	/*
	 * Statistics Display Barcharts
	 *
	 * Output the list of data usually from functions like self::meta_array
	 * It loops through the arrays and outputs data as needed
	 *
	 * This relies on ChartJS.
	 *
	 * @param string $subject    The content subject
	 * @param string $data       The data 'subject' - used to generate the URLs
	 * @param array  $data_array The array of data
	 *
	 * @return Content
	 */
	public function barcharts( $subject, $data, $data_array ) {

		// Remove the zeros
		foreach ( $data_array as $key => $value ) {
			if ( 0 === $value['count'] ) {
				unset( $data_array[ $key ] );
			}
		}

		$count     = count( $data_array );
		$step_size = '5';
		$height    = max( ( $count * 20 ), 30 ) + 20;
		$rand      = substr( md5( microtime() ), wp_rand( 0, 26 ), 5 );
		$bar_id    = ucfirst( $subject ) . $rand;
		?>
		<div id="container" style="width: 100%;">
			<canvas
				id="bar<?php echo esc_attr( $bar_id ); ?>"
				width="700" height="<?php echo (int) $height; ?>"
				aria-label="A Bar Chart for stats on <?php echo esc_html( $subject ); ?>"
			/>
				<p>Your browser cannot display this Bar Chart for stats on <?php echo esc_html( $subject ); ?>.</p>
			</canvas>
		</div>

		<script>
		// Defaults
		Chart.defaults.responsive = true;
		Chart.defaults.plugins.legend.display = false;

		// Bar Chart
		var bar<?php echo esc_attr( $bar_id ); ?>Data = {
			labels : [
				<?php
				foreach ( $data_array as $item ) {
					if ( 0 !== $item['count'] ) {
						switch ( $item['name'] ) {
							case '0':
								$name = '0';
								break;
							case 'Dead Lesbians (Dead Queers)':
								$name = 'Dead';
								break;
							default:
								$name = str_replace( '&amp;', ' and ', $item['name'] );
								break;
						}
						echo '"' . wp_kses_post( $name ) . ' (' . (int) $item['count'] . ')", ';
					}
				}
				?>
			],
			datasets : [
				{
					backgroundColor: "rgba(255,99,132,0.2)",
					borderColor: "rgba(255,99,132,1)",
					borderWidth: 1,
					hoverBackgroundColor: "rgba(255,99,132,0.4)",
					hoverBorderColor: "rgba(255,99,132,1)",
					data : [
						<?php
						foreach ( $data_array as $item ) {
							echo '"' . (int) $item['count'] . '", ';
						}
						?>
					],
				}
			]

		};
		var ctx = document.getElementById("bar<?php echo esc_attr( $bar_id ); ?>").getContext("2d");
		var bar<?php echo esc_attr( $bar_id ); ?> = new Chart(ctx, {
			type: 'bar',
			data: bar<?php echo esc_attr( $bar_id ); ?>Data,
			options: {
				responsive: true,
				indexAxis: 'y',
				tooltips: {
					callbacks: {
						title: function(tooltipItems, data) {
						},
						label: function(tooltipItems, data) {
							return tooltipItems.yLabel;
						},
					}
				},
				scales: {
					xAxes: [{
						ticks: {
							beginAtZero: true,
							stepSize: <?php echo (int) $step_size; ?>,
						}
					}]
				},
			}
		});

		</script>
		<?php
	}
}

new LWTV_Stats_Barcharts();
