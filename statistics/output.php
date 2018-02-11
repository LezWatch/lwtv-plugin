<?php
/**
 * Name: Output Statistics Code
 *
 * Output the stats
 */

class LWTV_Stats_Output {

	/*
	 * Statistics Display Lists
	 *
	 * Output the list of data usually from functions like self::meta_array
	 * It loops through the arrays and outputs data as needed
	 *
	 * @param string $subject The content subject
	 * @param string $data The data 'subject' - used to generate the URLs
	 * @param array $array The array of data
	 * @param string $count The count of posts
	 *
	 * @return Content
	 */
	static function lists( $subject, $data, $array, $count ) {
		?>
		<ul class="statistics lists <?php echo $data; ?>">
		<?php
		foreach ( $array as $item ) {
			$name = ( $item['name'] == 'Dead Lesbians (Dead Queers)' )? 'Dead' : $item['name'];
			echo '<li>';
				echo '<strong><a href="'.$item['url'].'">' . $name . '</a></strong> &mdash; ' . $item['count'] . ' ' . $subject .' - '. round( ( ( $item['count'] / $count ) * 100) , 1) .'%';
			echo '</li>';
		}
		?>
		</ul>
		<?php
	}

	/*
	 * Statistics Display Percentages
	 *
	 * Output the list of data usually from functions like self::meta_array
	 * It loops through the arrays and outputs data as needed
	 *
	 * @param string $subject The content subject
	 * @param string $data The data 'subject' - used to generate the URLs
	 * @param array $array The array of data
	 * @param string $count The count of posts
	 *
	 * @return Content
	 */
	static function percentages( $subject, $data, $array, $count ) {
		?>
		<ul class="statistics percentages <?php echo $data; ?>">
		<?php
		foreach ( $array as $item ) {
			if ( $item['count'] !== 0 ) {
				echo '<li>';
					echo '<strong><a href="'.$item['url'].'">'
					. $item['name'] . '</a></strong> &mdash; '
					. round( ( ( $item['count'] / $count ) * 100 ) , 1 ) .'%'
					. ' ('. $item['count'] . ' ' . $subject . ')';
				echo '</li>';
			}
		}
		?>
		</ul>
		<?php
	}

	/*
	 * Statistics Display Average
	 *
	 * Output the list of data usually from functions like self::meta_array
	 * It loops through the arrays and outputs data as needed
	 *
	 * @param string $subject The content subject (ex: dead)
	 * @param string $data The data 'subject' - used to generate the URLs
	 * @param array $array The array of data
	 * @param string $count The count of posts (usually all characters)
	 *
	 * @return Content
	 */
	static function averages( $subject, $data, $array, $count, $type = 'average' ) {

		$valid_types = array( 'high', 'low', 'average' );
		if ( !in_array( $type, $valid_types ) ) $type = 'average';

		switch ( $type ) {
			case 'average':
				$N   = count( $array );
				$sum = 0;
				foreach ( $array as $item ) { $sum = $sum + $item['count']; }
				$average = round ($sum / $N);
				$return  = $average;
				break;
			case 'high':
				$high = 0;
				foreach( $array as $key => $value ) {
					if( $value['count'] > $high ) {
						$high = $value['count'];
						if ( $subject = 'shows' ) {
							$high .= ' (<a href="' . $value['url'] . '">' . get_the_title( $value['id'] ) . '</a>)';
						}
					}
				}
				$return = $high;
				break;
			case 'low':
				$low = $number = 0;
				foreach( $array as $key => $value ) {
					if( $value['count'] == 0 ) {
						if ( $subject = 'shows' ) {
							$number++;
						}
					}
				}
				$return = $low . ' (<a href="/shows/?fwp_shows_scores=0%2C0">' . $number . ' shows total</a>)';
				break;
		}
		echo $return;
	}

	/**
	 * linear regression function
	 * @param $x array x-coords
	 * @param $y array y-coords
	 * @returns array() m=>slope, b=>intercept
	 */
	static function linear_regression($x, $y) {

		// calculate number points
		$n = count($x);

		// ensure both arrays of points are the same size
		if ($n != count($y)) {
			trigger_error("linear_regression(): Number of elements in coordinate arrays do not match.", E_USER_ERROR);
		}

		// calculate sums
		$x_sum = array_sum($x);
		$y_sum = array_sum($y);

		$xx_sum = 0;
		$xy_sum = 0;

		for($i = 0; $i < $n; $i++) {
			$xy_sum+=($x[$i]*$y[$i]);
			$xx_sum+=($x[$i]*$x[$i]);
		}

		// calculate slope
		$slope = (($n * $xy_sum) - ($x_sum * $y_sum)) / (($n * $xx_sum) - ($x_sum * $x_sum));

		// calculate intercept
		$intercept = ($y_sum - ($slope * $x_sum)) / $n;

		return array("slope"=>$slope, "intercept"=>$intercept);
	}

	/*
	 * Statistics Display Barcharts
	 *
	 * Output the list of data usually from functions like self::meta_array
	 * It loops through the arrays and outputs data as needed
	 *
	 * This relies on ChartJS existing
	 *
	 * @param string $subject The content subject
	 * @param string $data The data 'subject' - used to generate the URLs
	 * @param array $array The array of data
	 *
	 * @return Content
	 */
	static function barcharts( $subject, $data, $array ) {
		$height   = '250';
		$stepSize = '5';
		switch ( $data ) {
			case 'per-char':
				$subject = 'actorsPerChar';
				$height = '100';
				break;
			case 'cliches':
				$height = '550';
				break;
			case 'country-all-overview':
				$height = '550';
				break;
			case 'per-actor':
			case ( preg_match( '/country-.*-sexuality/', $data ) ? true : false ):
			case ( preg_match( '/country-.*-gender/', $data ) ? true : false ):
				$subject  = 'charsPerActor';
				$height   = '150';
				$stepSize = '2';
				break;
			case 'tropes':
			case 'dead-years':
				$height = '350';
				break;
			case 'stations':
				$subject = 'showsPerStation';
				$height  = '1500';
				break;
		}
		?>
		<div id="container" style="width: 100%;">
			<canvas id="bar<?php echo ucfirst( $subject ); ?>" width="700" height="<?php echo $height; ?>"></canvas>
		</div>

		<script>
		// Defaults
		Chart.defaults.global.responsive = true;
		Chart.defaults.global.legend.display = false;

		// Bar Chart
		var bar<?php echo ucfirst( $subject ); ?>Data = {
			labels : [<?php
				foreach ( $array as $item ) {
					if ( $item['count'] !== 0 ) {
						switch ( $item['name'] ) {
							case '0':
								$name = '0';
							case 'Dead Lesbians (Dead Queers)':
								$name = 'Dead';
							default:
								$name = wp_specialchars_decode( $item['name'] );
						}
						echo '"'. $name . ' ('.$item['count'].')", ';
					}
				}
			?>],
			datasets : [
				{
					backgroundColor: "rgba(255,99,132,0.2)",
					borderColor: "rgba(255,99,132,1)",
					borderWidth: 1,
					hoverBackgroundColor: "rgba(255,99,132,0.4)",
					hoverBorderColor: "rgba(255,99,132,1)",
					data : [<?php
						foreach ( $array as $item ) {
							if ( $item['count'] !== 0 ) {
								echo '"'.$item['count'].'", ';
							}
						}
					?>],
				}
			]

		};
		var ctx = document.getElementById("bar<?php echo ucfirst( $subject ); ?>").getContext("2d");
		var bar<?php echo ucfirst( $subject ); ?> = new Chart(ctx, {
			type: 'horizontalBar',
			data: bar<?php echo ucfirst( $subject ); ?>Data,
			options: {
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
							stepSize: <?php echo $stepSize; ?>,
						}
					}]
				},
			}
		});

		</script>
		<?php
	}

	/*
	 * Statistics Display Barcharts
	 *
	 * Output the list of data usually from functions like self::meta_array
	 * It loops through the arrays and outputs data as needed
	 *
	 * This relies on ChartJS existing
	 *
	 * @param string $subject The content subject (shows, characters)
	 * @param string $data The data - used to generate the URLs
	 * @param array $array The array of data
	 *
	 * @return Content
	 */
	static function stacked_barcharts( $subject, $data, $array ) {

		// [main-term-subtax]
		// [main taxonomy]-[term of main]-[subtaxonomy to parse]
		// ex: [country-all-gender]
		//     [station-abc-sexuality]
		//     [country-usa-all]
		$pieces  = explode( '-', $data);
		$data_main   = $pieces[0];
		$data_term   = ( isset( $pieces[1] ) )? $pieces[1] : 'all';
		$data_subtax = ( isset( $pieces[2] ) )? $pieces[2] : 'all';

		// Defaults
		$height     = '550';

		// Define our settings
		switch ( $data_subtax ) {
			case 'gender':
			case 'sexuality':
			case 'romantic':
				$datasets = array();
				$terms    = get_terms( 'lez_' . $data_subtax, array( 'orderby' => 'count', 'order' => 'DESC', 'hide_empty' => 0 ) );
				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
					foreach ( $terms as $term ) $datasets[] = $term->slug;
				}
				$counter  = 'characters';
				$height   = '400';
				break;
		}
		?>
		<div id="container" style="width: 100%;">
			<canvas id="barStacked<?php echo ucfirst( $subject ) . ucfirst( $data_main ); ?>" width="700" height="<?php echo $height; ?>"></canvas>
		</div>

		<script>
		// Defaults
		Chart.defaults.global.responsive = true;
		Chart.defaults.global.legend.display = false;

		// Bar Chart
		var barStacked<?php echo ucfirst( $subject ) . ucfirst( $data_main ); ?>Data = {
			labels : [
			<?php
				foreach ( $array as $item ) {
					if ( $item[$counter] !== 0 ) {
						$name = esc_html( $item['name'] );
					}
					echo '"'. $name .' ('.$item[$counter].')", ';
				}
			?>
			],
			datasets: [
			<?php
			foreach ( $datasets as $label ) {
				$color = ( $label == 'undefined' )? 'nundefined' : str_replace( ["-", "–","-"], "", $label );
				?>
				{
					borderWidth: 1,
					backgroundColor: window.chartColors.<?php echo $color; ?>,
					label: '<?php echo ucfirst( $label ); ?>',
					stack: 'Stack',
					data : [<?php
						foreach ( $array as $name => $item ) {
							echo $item[ 'dataset' ][ $label ] . ',';
						}
					?>],
				},
				<?php
			}
			?>
			]
		};
		var ctx = document.getElementById("barStacked<?php echo ucfirst( $subject ) . ucfirst( $data_main ); ?>").getContext("2d");
		var barStacked<?php echo ucfirst( $subject ) . ucfirst( $data_main ); ?> = new Chart(ctx, {
			type: 'horizontalBar',
			data: barStacked<?php echo ucfirst( $subject ) . ucfirst( $data_main ); ?>Data,
			options: {
				scales: {
					xAxes: [{ stacked: true }],
					yAxes: [{ stacked: true }]
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
	 * @param array $array The array of data
	 *
	 * @return Content
	 */
	static function piecharts( $subject, $data, $array ) {
		// Strip extra word(s) to make the chart key readable
		$fixname = '';
		if ( $data == 'sexuality' || $data == 'dead-sex' ) $fixname = 'sexual';
		if ( $data == 'gender' || $data == 'dead-gender' ) $fixname = 'gender';
		if ( $data == 'dead-shows' ) $fixname = 'queers are dead';

		// Strip hypens becuase ChartJS doesn't like it.
		$data = str_replace('-','',$data);

		// Reorder by item count
		usort( $array, function( $a, $b ) { return $a['count'] - $b['count']; } );
		
		?>
		<canvas id="pie<?php echo ucfirst( $data ); ?>" width="500px" height="500px"></canvas>

		<script>
			// Piechart for stats
			var pie<?php echo ucfirst( $data ); ?>Dataset = [<?php foreach ( $array as $item ) { if ( $item['count'] !== 0 ) { echo '"' . $item['count'] . '", '; } } ?>];
			var pie<?php echo ucfirst( $data ); ?>data = {
				labels : [<?php
					foreach ( $array as $item ) {
						if ( $item['count'] !== 0 ) {
							$name = str_replace( $fixname, '', $item['name'] );
							echo '"' . $name .' (' . $item['count'] . ')", ';
						}
					}
				?>],
				datasets : [{
					data : pie<?php echo ucfirst( $data ); ?>Dataset,
					backgroundColor: palette('tol-rainbow', pie<?php echo ucfirst( $data ); ?>Dataset.length).map(function(hex) { return '#' + hex; }),
				}]
			};

			var ctx = document.getElementById("pie<?php echo ucfirst( $data ); ?>").getContext("2d");
			var pie<?php echo ucfirst( $data ); ?> = new Chart(ctx,{
				type:'doughnut',
				data: pie<?php echo ucfirst( $data ); ?>data,
				options: {
					tooltips: {
						callbacks: {
							label: function(tooltipItem, data) {
								return data.labels[tooltipItem.index];
							}
						},
					},
				}
			});
		</script>
		<?php
	}

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
	 * @param array $array The array of data
	 *
	 * @return Content
	 */
	static function trendline( $subject, $data, $array ) {

		$array = array_reverse( $array );

		// Calculate Trend
		$names = array();
		$count = array();
		foreach( $array as $item ) {
			$names[] = $item['name'];
			$count[] = $item['count'];
		}

		$trendarray = self::linear_regression( $names, $count );

		// Strip hypens becuase ChartJS doesn't like it.
		$cleandata = str_replace('-','',$data);
		?>

		<div id="container" style="width: 100%;">
			<canvas id="trend<?php echo ucfirst( $cleandata ); ?>" width="700" height="550"></canvas>
		</div>

		<script>
		var ctx = document.getElementById("trend<?php echo ucfirst( $cleandata ); ?>").getContext("2d");
		var trend<?php echo ucfirst( $cleandata ); ?> = new Chart(ctx, {
			type: 'bar',
			data: {
				labels : [
					<?php
					foreach ( $array as $item ) {
						echo '"'. esc_html( $item['name'] ) .' ('.$item['count'].')", ';
					}
					?>
				],
				datasets : [ 
					{
						type: 'line',
						label: 'Number of <?php echo ucfirst( $subject ); ?>',
						backgroundColor: "rgba(255,99,132,0.2)",
						borderColor: "rgba(255,99,132,1)",
						borderWidth: 2,
						hoverBackgroundColor: "rgba(255,99,132,0.4)",
						hoverBorderColor: "rgba(255,99,132,1)",
						data : [<?php
							foreach ( $array as $item ) {
								echo '"'.$item['count'].'", ';
							}
						?>],
					},
					{
						type: 'line',
						label: 'Trendline',
						pointRadius: 0,
						borderColor: "rgba(75,192,192,1)",
						borderWidth: 2,
						fill: false,
						data: [
							<?php 
							foreach ( $array as $item ) {
								$number = ( $trendarray['slope'] * $item['name'] ) + $trendarray['intercept'];
								$number = ( $number <= 0 )? 0 : $number;
								echo '"'.$number.'", ';
							}
							?>
						],
					}
				]
			}
		});
		</script>

	<?php
	}
}

new LWTV_Stats_Output();