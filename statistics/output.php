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
	public function lists( $subject, $data, $array, $count ) {
		?>
		<table id="<?php echo esc_html( $subject ); ?>Table" class="tablesorter table table-striped table-hover">
			<thead>
				<tr>
					<th scope="col">&nbsp;</th>
					<th scope="col">Count</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ( $array as $item ) {
					$name = ( 'Dead Lesbians (Dead Queers)' === $item['name'] ) ? 'Dead' : $item['name'];
					if ( 0 !== $item['count'] ) {
						echo '<tr>';
							echo '<th scope="row"><a href="' . esc_url( $item['url'] ) . '">' . esc_html( $name ) . '</a></th>';
							echo '<td>' . (int) $item['count'] . '</td>';
						echo '</tr>';
					}
				}
				?>
			</tbody>
		</table>
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
	public function percentages( $subject, $data, $array, $count ) {
		$pieces = preg_split( '(_|-)', $data );
		if ( in_array( 'country', $pieces, true ) ) {
			$count = 0;
			foreach ( $array as $key => $item ) {
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

		// @codingStandardsIgnoreStart
		if ( ! in_array( 'dead', $pieces, true ) ) {
			// Reorder by item count
			usort( $array, function( $a, $b ) {
				return $a['count'] - $b['count'];
			} );
		}
		// @codingStandardsIgnoreEnd
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
				foreach ( $array as $item ) {
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
	public function averages( $subject, $data, $array, $count, $type = 'average' ) {

		$valid_types = array( 'high', 'low', 'average' );
		if ( ! in_array( $type, $valid_types, true ) ) {
			$type = 'average';
		}

		switch ( $type ) {
			case 'average':
				$n   = ( 'dead-years' === $data ) ? ( gmdate( 'Y' ) - FIRST_LWTV_YEAR ) : $count;
				$sum = 0;
				foreach ( $array as $item ) {
					// phpcs:ignore WordPress.PHP.TypeCasts.DoubleRealFound
					$sum = $sum + (float) $item['count'];
				}
				$average = round( $sum / $n );
				$return  = $average;
				break;
			case 'high':
				$high = 0;
				foreach ( $array as $key => $value ) {
					// phpcs:ignore WordPress.PHP.TypeCasts.DoubleRealFound
					if ( (float) $value['count'] > (float) $high ) {
						// phpcs:ignore WordPress.PHP.TypeCasts.DoubleRealFound
						$high = (float) $value['count'];
						if ( 'shows' === $subject ) {
							$high .= ' (<a href="' . $value['url'] . '">' . get_the_title( $value['id'] ) . '</a>)';
						}
					}
				}
				$return = $high;
				break;
			case 'low':
				$low = 20;
				foreach ( $array as $key => $value ) {
					// phpcs:ignore WordPress.PHP.TypeCasts.DoubleRealFound
					if ( (float) $low > (float) $value['count'] ) {
						// phpcs:ignore WordPress.PHP.TypeCasts.DoubleRealFound
						$low = (float) $value['count'];
						if ( 'shows' === $subject ) {
							$low .= ' (<a href="' . $value['url'] . '">' . get_the_title( $value['id'] ) . '</a>)';
						}
					}
				}
				$return = $low;
				break;
		}
		echo (float) $return;
	}

	/**
	 * Calculate Trendlines
	 */
	public function calculate_trendline( $array ) {
		// Calculate Trend
		$names = array();
		$count = array();
		$trend = array();

		foreach ( $array as $item ) {
			$names[] = $item['name'];
			$count[] = $item['count'];
		}

		$trendarray = self::linear_regression( $names, $count );

		foreach ( $array as $item ) {
			$number  = ( $trendarray['slope'] * $item['name'] ) + $trendarray['intercept'];
			$trend[] = ( $number <= 0 ) ? 0 : $number;
		}

		return $trend;
	}

	/**
	 * linear regression function
	 * @param $x array x-coords
	 * @param $y array y-coords
	 * @returns array() m=>slope, b=>intercept
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
	public function barcharts( $subject, $data, $array ) {

		// Remove the zeros
		foreach ( $array as $key => $value ) {
			if ( 0 === $value['count'] ) {
				unset( $array[ $key ] );
			}
		}

		$count     = count( $array );
		$step_size = '5';
		$height    = max( ( $count * 20 ), 30 ) + 20;
		$rand      = substr( md5( microtime() ), wp_rand( 0, 26 ), 5 );
		$bar_id    = ucfirst( $subject ) . $rand;
		?>
		<div id="container" style="width: 100%;">
			<canvas id="bar<?php echo esc_attr( $bar_id ); ?>" width="700" height="<?php echo (int) $height; ?>"></canvas>
		</div>

		<script>
		// Defaults
		Chart.defaults.responsive = true;
		Chart.defaults.plugins.legend.display = false;

		// Bar Chart
		var bar<?php echo esc_attr( $bar_id ); ?>Data = {
			labels : [
				<?php
				foreach ( $array as $item ) {
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
						foreach ( $array as $item ) {
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
	 * @param array $array The array of data
	 *
	 * @return Content
	 */
	public function stacked_barcharts( $subject, $data, $array ) {

		$count     = count( $array );
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
				$datasets = array();
				$termarry = array(
					'orderby'    => 'count',
					'order'      => 'DESC',
					'hide_empty' => 0,
				);
				$terms    = get_terms( 'lez_' . $data_subtax, $termarry );
				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
					foreach ( $terms as $term ) {
						$datasets[] = $term->slug;
					}
				}
				$counter = 'characters';
				break;
		}
		?>
		<div id="container" style="width: 100%;">
			<canvas id="barStacked<?php echo esc_attr( ucfirst( $subject ) ) . esc_attr( ucfirst( $data_main ) ); ?>" width="700" height="<?php echo (int) $height; ?>"></canvas>
		</div>

		<?php
		// Build out the colors ...
		$colors_array = array();
		$colors_tolra = array( '781c81', '61187e', '531b7f', '4a2384', '442e8a', '413b93', '3f499d', '3f58a8', '4066b2', '4273bb', '447fc0', '488ac2', '4c94bf', '519cb8', '57a3ae', '5ea9a2', '65ae95', '6db388', '76b67d', '7fb972', '88bb69', '92bd60', '9cbe59', 'a7be53', 'b1be4e', 'babc49', 'c3ba45', 'ccb742', 'd3b33f', 'daad3c', 'dfa539', 'e39c37', 'e59134', 'e78432', 'e7752f', 'e6652d', 'e4542a', 'e14326', 'dd3123', 'd92120' );
			shuffle( $colors_tolra );
		$color_count = 0;
		foreach ( $datasets as $label ) {
			$name                  = ( 'undefined' === $label ) ? 'nundefined' : str_replace( [ '-', '-', '-' ], '', $label );
			$colors_array[ $name ] = '#' . $colors_tolra[ $color_count ];
			$color_count++;
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
			foreach ( $array as $item ) {
				$name = $item['name'];
				echo '"' . wp_kses_post( $name ) . ' (' . (int) $item[ $counter ] . ')", ';
			}
			?>
			],
			datasets: [
			<?php
			foreach ( $datasets as $label ) {
				$color = ( 'undefined' === $label ) ? 'nundefined' : str_replace( [ '-', '-', '-' ], '', $label );
				?>
				{
					borderWidth: 1,
					label: '<?php echo wp_kses_post( ucfirst( $label ) ); ?>',
					stack: 'Stack',
					data : [
					<?php
					foreach ( $array as $name => $item ) {
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
					// @codingStandardsIgnoreStart
					echo '"' . $colors_array[ $color ] . '"';
					// @codingStandardsIgnoreEnd
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
				}
				tooltips: {
					mode: 'index',
					intersect: false
				},
			}
		});

		</script>

		????/
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
	public function piecharts( $subject, $data, $array ) {
		// Strip extra word(s) to make the chart key readable
		switch ( $data ) {
			case 'sexuality':
			case 'dead-sex':
				$fixname = 'sexual';
				$count   = ( new LWTV_Stats() )->generate( 'characters', 'all', 'count' );
				$center  = $count . ' Characters';
				break;
			case 'gender':
			case 'dead-gender':
				$fixname = 'gender';
				$count   = ( new LWTV_Stats() )->generate( 'characters', 'all', 'count' );
				$center  = $count . ' Characters';
				break;
			case 'dead-shows':
				$fixname = 'queers are dead';
				$count   = ( new LWTV_Stats() )->generate( 'shows', 'all', 'count' );
				$center  = $count . ' Shows';
				break;
			default:
				$fixname = '';
				$center  = '';
				break;
		}

		// Strip hypens becuase ChartJS doesn't like it.
		$data = str_replace( '-', '', $data );

		// @codingStandardsIgnoreStart
		// Reorder by item count
		usort( $array, function( $a, $b ) {
			return $a['count'] - $b['count'];
		} );
		// @codingStandardsIgnoreEnd

		?>
		<canvas id="pie<?php echo esc_attr( ucfirst( $data ) ); ?>" width="500px" height="500px"></canvas>

		<script>
			// Piechart for stats
			var pie<?php echo esc_attr( ucfirst( $data ) ); ?>Dataset = [
				<?php
				foreach ( $array as $item ) {
					if ( 0 !== $item['count'] ) {
						echo '"' . (int) $item['count'] . '", ';
					}
				}
				?>
				];
			var pie<?php echo esc_attr( ucfirst( $data ) ); ?>data = {
				labels : [
					<?php
					foreach ( $array as $item ) {
						if ( 0 !== $item['count'] ) {
							$name = ucfirst( str_replace( $fixname, '', $item['name'] ) );
							echo '"' . wp_kses_post( $name ) . ' (' . (int) $item['count'] . ')", ';
						}
					}
					?>
				],
				datasets : [{
					data : pie<?php echo esc_attr( ucfirst( $data ) ); ?>Dataset,
					backgroundColor: palette('tol-rainbow', pie<?php echo esc_attr( ucfirst( $data ) ); ?>Dataset.length).map(function(hex) { return '#' + hex; }),
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
	public function trendline( $subject, $data, $array ) {

		$array = array_reverse( $array );
		$trend = self::calculate_trendline( $array );

		// Strip hyphens because ChartJS doesn't like it.
		$cleandata = str_replace( '-', '_', $data );
		?>

		<div id="container" style="width: 100%;">
			<canvas id="trend<?php echo esc_attr( ucfirst( $cleandata ) ); ?>" width="700"></canvas>
		</div>

		<script>
		var ctx = document.getElementById("trend<?php echo esc_attr( ucfirst( $cleandata ) ); ?>").getContext("2d");
		var trend<?php echo esc_attr( ucfirst( $cleandata ) ); ?> = new Chart(ctx, {
			data: {
				labels : [
					<?php
					foreach ( $array as $item ) {
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
						foreach ( $array as $item ) {
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
}

new LWTV_Stats_Output();
