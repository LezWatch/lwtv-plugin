<?php
/**
 * Name: Statistics Code
 * Author: Mika Epstein
 *
 * This file has the basic defines for all stats.
 * It's pretty much only called in /page-template/statistics.php
 */

class LWTV_Stats {

	/*
	 * Construct
	 *
	 * Actions to happen immediately
	 */
    public function __construct() {
        add_action( 'init', array( &$this, 'init' ) );
    }

	/**
	 * Init
	 */
	public function init() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/*
	 * Enqueues
	 *
	 * Custom enqueue scripts for chartJS
	 */
	function enqueue_scripts() {
		wp_enqueue_script( 'chart.js', plugin_dir_url( __FILE__ ) .'/js/Chart.bundle.min.js' , array( 'jquery' ), CHILD_THEME_VERSION );
	}


	/*
	 * Generate: Statistics Base Code
	 *
	 * @param string $subject 'characters' or 'shows'
	 * @param string $data The stats being run
	 * @param string $format The format of the output
	 *
	 * @return Content
	 */
	static function generate( $subject, $data, $format ) {
		// Bail early if we're not an approved subject matter
		if ( !in_array( $subject, array('characters', 'shows') ) ) exit;

		// Build Variables
		$array     = array();
		$post_type = 'post_type_'.$subject;
		$count     = wp_count_posts( $post_type )->publish;
		$taxonomy  = 'lez_'.$data;

		// The following are simple taxonomy arrays
		$simple_tax_array = array( 'cliches', 'sexuality', 'gender', 'tropes', 'formats' );
		if ( in_array( $data, $simple_tax_array ) ) $array = self::tax_array( $post_type, $taxonomy );

		// The following are simple meta arrays
		if ( $data == 'role' ) $array = self::meta_array( $post_type, array( 'regular', 'recurring', 'guest' ), 'lezchars_show_group', $data, 'LIKE' );
		if ( $data == 'thumbs' ) $array = self::meta_array( $post_type, array( 'Yes', 'No', 'Meh' ), 'lezshows_worthit_rating', $data );

		// The following are complicated taxonomy arrays
		if ( $data == 'queer-irl' ) {
			$array = self::tax_array( $post_type, 'lez_cliches', $data );

			$array['queer'] = array( 'count' => $array['queer-irl']['count'], 'name' => 'Queer', 'url' => site_url( '/cliche/queer-irl/' ) );
			$array['not-queer'] = array( 'count' => ( $count - $array['queer-irl']['count'] ), 'name' => 'Not Queer', 'url' => '' );
			unset($array['queer-irl']);
			$count = $array['queer']['count'];
		}

		// The following are complicated meta arrays
		if ( $data == 'stars' ) {
			$meta_array = array( 'none', 'silver', 'gold' );
			$array = self::meta_array( $post_type, $meta_array, 'lezshows_stars', $data );
			$array['none'] = array( 'count' => ( $count - $array['silver']['count'] - $array['gold']['count'] ), 'name' => 'No Stars', 'url' => '' );
		}

		if ( $data == 'trigger' ) {
			$meta_array = array( 'on', 'off' );
			$array = self::meta_array( $post_type, $meta_array, 'lezshows_triggerwarning', $data );
			$array['yes'] = array( 'count' => $array['on']['count'], 'name' => 'Trigger Warning', 'url' => '' );
			$array['no'] = array( 'count' => ( $count - $array['on']['count'] ), 'name' => 'No Warning', 'url' => '' );
			unset($array['on']);
			unset($array['off']);
		}

		if ( $data == 'current' ) {
			$meta_array = array( 'current', 'notcurrent' );
			$array = self::meta_array( $post_type, $meta_array, 'lezshows_airdates', $data, 'REGEXP' );
			$array['no'] = array( 'count' => ( $count - $array['current']['count'] ), 'name' => 'Not Airing', 'url' => '' );
			$array['yes'] = array( 'count' => $array['current']['count'], 'name' => 'Currently Airing', 'url' => '' ) ;
			unset($array['current']);
			unset($array['notcurrent']);
		}

		// Custom call for show roles of character in each role
		if ( $data == 'charroles' ) {
			$array = self::show_roles();
		}

		// And dead stats? IN-fucking-sane
		// Everything gets a custom setup
		if ( $data == 'dead' ) {
			if ( $subject == 'characters' ) {
				$array = self::tax_array( $post_type, 'lez_cliches', 'dead' );
				$array['dead'] = array( 'count' => ( $array['dead']['count']), 'name' => 'Dead Characters', 'url' => site_url( '/cliche/dead/' ) );
				$count = $array['dead']['count'];
			} elseif ( $subject == 'shows' ) {
				$array = self::tax_array( $post_type, 'lez_tropes', 'dead-queers' );
				$array['dead-queers'] = array( 'count' => ( $array['dead-queers']['count']), 'name' => 'Shows with Dead', 'url' => site_url( '/trope/dead-queers/' ) );
				$count = $array['dead-queers']['count'];
			}
		}
		if ( $data == 'dead-sex' )    $array = self::tax_dead_array( $post_type, 'lez_sexuality' );
		if ( $data == 'dead-gender' ) $array = self::tax_dead_array( $post_type, 'lez_gender' );
		if ( $data == 'dead-shows' )  $array = self::dead_shows( 'simple' );
		if ( $data == 'dead-years' )  $array = self::death_year();

		// Acutally output shit
		if ( $format == 'barchart' )   self::barcharts( $subject, $data, $array );
		if ( $format == 'piechart' )   self::piecharts( $subject, $data, $array );
		if ( $format == 'trendline' )  self::trendline( $subject, $data, $array );
		if ( $format == 'count' )      return $count;
		if ( $format == 'list' )       self::lists( $subject, $data, $array, $count );
		if ( $format == 'percentage' ) self::percentages( $subject, $data, $array, $count );
		if ( $format == 'average' )    self::averages( $subject, $data, $array, $count );
		if ( $format == 'array' )      return $array;
	}

	/*
	 * Statistics Taxonomy Array
	 *
	 * Generate array to parse taxonomy content
	 *
	 * @param string $post_type Post Type to be search
	 * @param string $taxonomy Taxonomy to be searched
	 * @param string $terms The terms to be matched (default empty)
	 * @param string $operator Search operator (default IN)
	 *
	 * @return array
	 */
	static function tax_array( $post_type, $taxonomy, $terms = '', $operator = 'IN' ) {
		$array = array();

		// If no term provided, use get_terms for the taxonomy
		$taxonomies = ( $terms == '' )? get_terms( $taxonomy ) : array($terms);

		foreach ( $taxonomies as $term ) {
			$term_link = get_term_link( $term );
			$term_slug = ( $terms == '' )? $term->slug : $terms;
			$term_name = ( $terms == '' )? $term->name : $terms;
			$count_terms_query = LWTV_Loops::tax_query( $post_type, $taxonomy, 'slug', $term_slug, $operator );
			$term_count = $count_terms_query->post_count;
			$array[$term_slug] = array( 'count' => $term_count, 'name' => $term_name, 'url' => $term_link );
		}
		return $array;
	}

	/*
	 * Statistics Taxonomy Array for DEAD
	 *
	 * Generate array to parse taxonomy content for death
	 *
	 * @param string $post_type Post Type to be searched
	 * @param string $taxonomy Taxonomy to be searched
	 *
	 * @return array
	 */
	static function tax_dead_array( $post_type, $taxonomy ) {
		$array = array();
		$taxonomies = get_terms( $taxonomy );

		foreach ( $taxonomies as $term ) {
			$query = LWTV_Loops::tax_two_query(
				$post_type,
				$taxonomy, 'slug', $term->slug,
				'lez_cliches', 'slug', 'dead'
			);

			$array[$term->slug] = array( 'count' => $query->post_count, 'name'  => $term->name, 'url' => get_term_link( $term ) );
		}
		return $array;
	}

	/*
	 * Statistics Meta and Taxonomy Array
	 *
	 * Generate array to parse taxonomy content as it relates to post metas
	 *
	 * @param string $post_type Post Type to be search
	 * @param array $meta_array Meta terms to loop through
	 * @param string $key Post Meta Key name (i.e. lezchars_gender)
	 * @param string $taxonomy Taxonomy to restrict to (default lez_cliches)
	 * @param string $field Taxonomy to restrict to (default lez_cliches)
	 *
	 * @return array
	 */
	static function meta_tax_dead_array( $post_type, $meta_array, $key, $taxonomy = 'lez_cliches', $field = 'dead' ) {
		$array = array();

		foreach ( $meta_array as $value ) {
			$query = LWTV_Loops::post_meta_and_tax_query( $post_type, $key, $value, $taxonomy, 'slug', $field );
			$array[$value] = array(
				'count' => $query->post_count,
				'name'  => ucfirst($value),
				'url' => site_url( '/role/'.$value ),
			);
		}
		return $array;
	}

	/*
	 * Statistics Meta Array
	 *
	 * Generate array to parse post meta data
	 *
	 * @param string $post_type Post Type to be search
	 * @param array $meta_array Meta terms to loop through
	 * @param string $key Post Meta Key name (i.e. lezchars_gender)
	 * @param string $data The data 'subject' - used to generate the URLs
	 * @param string $compare The type of comparison (default =)
	 *
	 * @return array
	 */
	static function meta_array( $post_type, $meta_array, $key, $data, $compare = '=' ) {
		$array = array();
		foreach ( $meta_array as $value ) {
			$meta_query = LWTV_Loops::post_meta_query( $post_type, $key, $value, $compare );
			$array[$value] = array( 'count' => $meta_query->post_count, 'name' => ucfirst($value), 'url' => site_url( '/'. $data .'/'. lcfirst($value) .'/' ) ) ;
		}
		return $array;
	}

	/*
	 * Statistics Death By Year
	 *
	 * Death is insane. This is just looping a lot of things to sort
	 * out who died in what year, so we can use it by other functions
	 *
	 * @return array
	 */
	static function death_year() {
		// Death by year
		$year_first = 1961;
		$year_deathlist_array = array();
		foreach (range(date('Y'), $year_first) as $x) {
			$year_deathlist_array[$x] = $x;
		}

		$year_death_array = array();
		foreach ( $year_deathlist_array as $year ) {
			$year_death_query = LWTV_Loops::post_meta_and_tax_query( 'post_type_characters', 'lezchars_death_year', $year, 'lez_cliches', 'slug', 'dead', 'REGEXP' );

			$year_death_array[$year] = array(
				'name' => $year,
				'count' => $year_death_query->post_count,
				'url' => site_url( '/this-year/'.$year.'/')
			);
		}
		return $year_death_array;
	}

	/*
	 * Statistics Death on Shows
	 *
	 * Death is insane. This is how to figure out who died on what show.
	 * We can use it to determine how many shows have ALL dead queers, etc.
	 * It's fucked up. I'm sorry.
	 *
	 * @param string $format The format of our output
	 *
	 * @return array
	 */
	static function dead_shows( $format ) {

		// Dead Queers Query
		$dead_queers_query = LWTV_Loops::tax_query( 'post_type_characters', 'lez_cliches', 'slug', 'dead' );

		// Shows With Dead Query
		$dead_shows_query = LWTV_Loops::tax_query( 'post_type_shows', 'lez_tropes', 'slug', 'dead-queers' );

		// Shows With NO Dead Query
		$alive_shows_query = LWTV_Loops::tax_query( 'post_type_shows', 'lez_tropes', 'slug', 'dead-queers', 'NOT IN' );

		// Predef Arrays
		$noneshow_death_array = array();
		$fullshow_death_array = array();
		$someshow_death_array = array();

		// Shows with no deaths
		if ( $alive_shows_query->have_posts() ) {
			while ( $alive_shows_query->have_posts() ) {
				$alive_shows_query->the_post();
				$show_id = get_the_ID();

				$show_name = preg_replace('/\s*/', '', get_the_title( $show_id ));
				$show_name = strtolower( $show_name );

				$noneshow_death_array[$show_name] = array(
					'url'    => get_permalink( $show_id ),
					'name'   => get_the_title( $show_id ),
					'status' => get_post_status( $show_id ),
				);
			}
			wp_reset_query();
		}

		// Shows with deaths
		if ( $dead_shows_query->have_posts() ) {
			while ( $dead_shows_query->have_posts() ) {
				$dead_shows_query->the_post();
				$show_id = get_the_ID();

				$show_name = preg_replace('/\s*/', '', get_the_title( $show_id ));
				$show_name = strtolower( $show_name );

				// Loop of characters who MIGHT be in this show
				$this_show_characters_query = LWTV_Loops::post_meta_query( 'post_type_characters', 'lezchars_show_group', $show_id, 'LIKE' );

				$fulldeathcount = '0';
				$chardeathcount = '0';

				// Begin Character query
				if ( $this_show_characters_query->have_posts() ) {
					while ( $this_show_characters_query->have_posts() ) {
						$this_show_characters_query->the_post();
						$char_id = get_the_ID();
						$shows_array = get_post_meta( $char_id, 'lezchars_show_group', true );

						if ( $shows_array !== '' ) {
							foreach( $shows_array as $char_show ) {
								if ( $char_show['show'] == $show_id ) {
									// If the character is really in this show, +1
									$chardeathcount++;

									// If the character is dead, bump the full death count
									if ( has_term( 'dead', 'lez_cliches', $char_id ) ) $fulldeathcount++;
								}
							}
						}

					}
					wp_reset_query();
				}
				// End Character Loop

				if ( $fulldeathcount == $chardeathcount ) {
					$fullshow_death_array[$show_name] = array(
						'url'    => get_permalink( $show_id ),
						'name'   => get_the_title( $show_id ),
						'status' => get_post_status( $show_id ),
					);
				} elseif ( $fulldeathcount <= $chardeathcount ) {
					$someshow_death_array[$show_name] = array(
						'url'    => get_permalink( $show_id ),
						'name'   => get_the_title( $show_id ),
						'status' => get_post_status( $show_id ),
					);
				}

			}
			wp_reset_query();
		}

		if ( $format == 'simple' ) {
			$array = array (
				"all"  => array( 'name' => 'All queers are dead', 'count' => count( $fullshow_death_array ), 'url' => '' ),
				"some" => array( 'name' => 'Some queers are dead', 'count' => count( $someshow_death_array ), 'url' => '' ),
				"none" => array( 'name' => 'None queers are dead', 'count' => $alive_shows_query->post_count, 'url' => '' ),
			);
		}

		return $array;
	}

	static function show_roles() {
		// List of shows
		$all_shows_query = LWTV_Loops::post_type_query( 'post_type_shows' );

		$show_guestonly_array = array();
		$show_recurringonly_array = array();
		$show_mainonly_array = array();

		if ($all_shows_query->have_posts() ) {

			while ( $all_shows_query->have_posts() ) {
				$all_shows_query->the_post();
				$show_id = get_the_id();

				$show_name = preg_replace('/\s*/', '', get_the_title( $show_id ));
				$show_name = strtolower($show_name);

				$role_loop = LWTV_Loops::post_meta_query( 'post_type_characters', 'lezchars_show_group', $show_id, 'LIKE' );

				if ($role_loop->have_posts() ) {

					$guestbiancount = 0;
					$maincharcount  = 0;
					$recurringcharcount = 0;

					$char_id = get_the_id();
					$shows_array = get_post_meta( $char_id, 'lezchars_show_group', true);

					if ( $shows_array !== '' ) {
						foreach( $shows_array as $each_show ) {
							if ( $char_show['type'] == 'guest' )     $guestbiancount++;
							if ( $char_show['type'] == 'regular' )   $maincharcount++;
							if ( $char_show['type'] == 'recurring' ) $recurringcharcount++;
						}
					}

					if ( $maincharcount == '0' && $recurringcharcount != '0' && $guestbiancount == '0' ) {
						$show_recurringonly_array[$show_name] = array(
							'url'    => get_permalink( $show_id ),
							'name'   => get_the_title( $show_id ),
							'status' => get_post_status( $show_id ),
						);
					}
					if ( $maincharcount == '0' && $recurringcharcount == '0' && $guestbiancount != '0' ) {
						$show_guestonly_array[$show_name] = array(
							'url'    => get_permalink( $show_id ),
							'name'   => get_the_title( $show_id ),
							'status' => get_post_status( $show_id ),
						);
					}
					if ( $maincharcount !== '0' && $guestbiancount == '0' && $recurringcharcount == '0' ) {
						$show_mainonly_array[$show_name] = array(
							'url'    => get_permalink( $show_id ),
							'name'   => get_the_title( $show_id ),
							'status' => get_post_status( $show_id ),
						);
					}
					wp_reset_query();
				}
			}
			wp_reset_query();
		}

		$array = array (
			"guest"  => array( 'name' => 'Only Guests',  'count' => count( $show_guestonly_array ), 'url' => site_url( '/role/guest/' ) ),
			"main" => array( 'name' => 'Only Main', 'count' => count( $show_mainonly_array ), 'url' => site_url( '/role/regular/' ) ),
			"recurring" => array( 'name' => 'Only Recurring', 'count' => count( $show_recurringonly_array ), 'url' => site_url( '/role/recurring/' ) ),
		);

		return $array;
	}

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
		// Format Clichés properly
		if ( $data == 'cliches' ) $data = 'clichés';

		// Set title
		$title = ucfirst( substr($subject, 0, -1) ). ' ' . ucfirst( $data );
		?>
		<h3><?php echo $title; ?></h3>
		<ul>
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
		<ul>
		<?php
		foreach ( $array as $item ) {
			if ( $item['count'] !== 0 ) {
				echo '<li>';
					echo '<strong><a href="'.$item['url'].'">'
					. $item['name'] . '</a></strong> &mdash; '
					. round( ( ( $item['count'] / $count ) * 100) , 1) .'%'
					. ' ('. $item['count'] . ' ' . $subject .')';
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
	static function averages( $subject, $data, $array, $count ) {
		$N = count($array);
		$sum = 0;

		foreach ( $array as $item ) {
			$sum = $sum + $item['count'];
		}

		$average = round ($sum / $N);

		echo $average;
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
		$m = (($n * $xy_sum) - ($x_sum * $y_sum)) / (($n * $xx_sum) - ($x_sum * $x_sum));

		// calculate intercept
		$b = ($y_sum - ($m * $x_sum)) / $n;

		return array("slope"=>$m, "intercept"=>$b);
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
		// Format Clichés properly
		if ( $data == 'cliches' ) $data = 'clichés';

		// Set title
		$title = ( $data == 'dead-years' )? 'Dead Characters by Year' : ucfirst( substr($subject, 0, -1) ). ' ' . ucfirst( $data );

		?>
		<h3><?php echo $title; ?></h3>
		<div id="container" style="width: 100%;">
			<canvas id="bar<?php echo ucfirst( $subject ); ?>" width="700" height="550"></canvas>
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
						$name = ( $item['name'] == 'Dead Lesbians (Dead Queers)' )? 'Dead' : esc_html( $item['name'] );
						echo '"'. $name .' ('.$item['count'].')", ';
					}
				}
			?>],
			datasets : [
				{
		            backgroundColor: "rgba(255,99,132,0.2)",
		            borderColor: "rgba(255,99,132,1)",
		            borderWidth: 2,
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
				            // return "Bob " + tooltipItems.data;
				            // This is undefined?
				        },
				        label: function(tooltipItems, data) {
				            return tooltipItems.yLabel;
				        },
				    }
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
		$data = str_replace('-','',$data)
		?>
		<canvas id="pie<?php echo ucfirst( $data ); ?>" width="200" height="200"></canvas>

		<script>
			// Piechart for stats
			var pie<?php echo ucfirst( $data ); ?>data = {
				labels : [<?php
					foreach ( $array as $item ) {
						$name = str_replace( $fixname, '', $item['name'] );
						echo '"'. $name.' ('.$item['count'].')", ';
					}
				?>],
				datasets : [
					{
						data : [<?php
							foreach ( $array as $item ) {
								echo '"'.$item['count'].'", ';
							}
						?>],
			            backgroundColor: [
				            "#FF6384",
				            "#4BC0C0",
				            "#FFCE56",
				            "#36A2EB",
				            "#E7E9ED"
			            ]
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

		if ( $data != 'dead-years' ) return;

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
		$cleandata = str_replace('-','',$data)
		?>

		<div id="container" style="width: 100%;">
			<canvas id="trend<?php echo ucfirst( $cleandata ); ?>" width="700" height="550"></canvas>
		</div>

		<script>
		var ctx = document.getElementById("trend<?php echo ucfirst( $cleandata ); ?>").getContext("2d");
		var trend<?php echo ucfirst( $cleandata ); ?> = new Chart(ctx, {
		    type: 'bar',
		    data: {
				labels : [<?php
					foreach ( $array as $item ) {
						echo '"'. esc_html( $item['name'] ) .' ('.$item['count'].')", ';
					}
				?>],
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
		                data: [<?php
		                	foreach ( $array as $item ) {
			                	$number = ( $trendarray['slope'] * $item['name'] ) + $trendarray['intercept'];
			                	$number = ( $number <= 0 )? 0 : $number;
			                	echo '"'.$number.'", ';
			                }
		                ?>],
		            }
		        ]
		    }
		});
		</script>

	<?php
	}

}

new LWTV_Stats();