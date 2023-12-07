<?php

namespace LWTV\Statistics\Format;

class Trendline {

	/*
	 * Statistics Display Trendlines
	 *
	 * Output the list of data usually from functions like self::meta_array
	 * It loops through the arrays and outputs data as needed
	 *
	 * This relies on ChartJS existing and really is only useful for death by years
	 *
	 * @param string $subject The content subject
	 * @param string $data The data 'subject' - used to generate the URLs
	 * @param array $data_array The array of data
	 *
	 * @return Content
	 */
	public function make( $subject, $data, $data_array ) {

		$data_array = array_reverse( $data_array );
		$trend      = self::calculate_trendline( $data_array );

		// Strip hyphens because ChartJS doesn't like it.
		$cleandata = str_replace( '-', '_', $data );
		?>

		<div id="container" style="width: 100%;">
			<canvas
			id="trend<?php echo esc_attr( ucfirst( $cleandata ) ); ?>"
				width="700"
				aria-label="A trendline for stats on <?php echo esc_html( $subject ); ?>"
			/>
				<p>Your browser cannot display this trendline for stats on <?php echo esc_html( $subject ); ?>.</p>
			</canvas>
		</div>

		<script>
		var ctx = document.getElementById("trend<?php echo esc_attr( ucfirst( $cleandata ) ); ?>").getContext("2d");
		var trend<?php echo esc_attr( ucfirst( $cleandata ) ); ?> = new Chart(ctx, {
			data: {
				labels : [
					<?php
					foreach ( $data_array as $item ) {
						echo '"' . wp_kses_post( $item['name'] ) . ' (' . (int) $item['count'] . ')", ';
					}
					?>
				],
				datasets : [{
					type: 'bar',
					label: 'Number of <?php echo wp_kses_post( ucfirst( $subject ) ); ?>',
					backgroundColor: "rgba(255,99,132,0.2)",
					borderColor: "rgba(255,99,132,1)",
					borderWidth: 2,
					hoverBackgroundColor: "rgba(255,99,132,0.4)",
					hoverBorderColor: "rgba(255,99,132,1)",
					data : [
						<?php
						foreach ( $data_array as $item ) {
							echo '"' . (int) $item['count'] . '", ';
						}
						?>
					],
				}]
			},
			options : {
				plugins: {
					annotation: {
						annotations: {
							line1: {
								type: 'line',
								yMin: <?php echo (int) min( $trend ); ?>,
								yMax: <?php echo (int) end( $trend ); ?>,
								borderColor: 'rgba(75,192,192,1)',
								borderWidth: 2,
							}
						}
					}
				}
			}
		});
		</script>
		<?php
	}

	/**
	 * Calculate Trendlines
	 *
	 * @param array $data_array Array of Data to process.
	 *
	 * @return array $trend     Trendline array.
	 */
	public function calculate_trendline( $data_array ) {
		// Calculate Trend
		$names = array();
		$count = array();
		$trend = array();

		foreach ( $data_array as $item ) {
			$names[] = $item['name'];
			$count[] = $item['count'];
		}

		$trendarray = self::linear_regression( $names, $count );

		foreach ( $data_array as $item ) {
			$number  = ( $trendarray['slope'] * $item['name'] ) + $trendarray['intercept'];
			$trend[] = ( $number <= 0 ) ? 0 : $number;
		}

		return $trend;
	}

	/**
	 * Linear regression function.
	 *
	 * @param $x array x-coords
	 * @param $y array y-coords
	 *
	 * @return array() m=>slope, b=>intercept
	 */
	public function linear_regression( $x, $y ) {

		// calculate number points
		$n = count( $x );

		// ensure both arrays of points are the same size
		if ( count( $y ) !== $n ) {
			trigger_error( 'linear_regression(): Number of elements in coordinate arrays do not match.', E_USER_ERROR );
		}

		// calculate sums
		$x_sum = array_sum( $x );
		$y_sum = array_sum( $y );

		$xx_sum = 0;
		$xy_sum = 0;

		for ( $i = 0; $i < $n; $i++ ) {
			$xy_sum += ( $x[ $i ] * $y [ $i ] );
			$xx_sum += ( $x[ $i ] * $x[ $i ] );
		}

		// Pre-check for zeros...
		$divisor = ( ( $n * $xx_sum ) - ( $x_sum * $x_sum ) );
		if ( 0 !== $divisor ) {
			// calculate slope
			$slope = ( ( $n * $xy_sum ) - ( $x_sum * $y_sum ) ) / $divisor;
			// calculate intercept
			$intercept = ( $y_sum - ( $slope * $x_sum ) ) / $n;
		}

		// Sort return.
		$return = array(
			'slope'     => ( isset( $slope ) ) ? $slope : 0,
			'intercept' => ( isset( $intercept ) ) ? $intercept : 0,
		);
		return $return;
	}
}
