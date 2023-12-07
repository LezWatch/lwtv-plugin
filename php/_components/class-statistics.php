<?php
/**
 * Name: Statistics Code
 *
 * This file has the basic defines for all stats.
 * It's pretty much only called in /page-template/statistics.php
 */
namespace LWTV\_Components;

use LWTV\Statistics\Gutenberg_SSR;
use LWTV\Statistics\Matcher;
use LWTV\Statistics\Query_Vars;
use LWTV\Statistics\The_Array;
use LWTV\Statistics\The_Output;
use LWTV\Statistics\Build\Dead_Basic as Build_Dead_Basic;
use LWTV\Statistics\Build\Taxonomy_Breakdowns as Build_Taxonomy_Breakdowns;

class Statistics implements Component, Templater {

	/*
	 * Init
	 */
	public function init(): void {
		new Query_Vars();
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Gets tags to expose as methods accessible through `lwtv_plugin()`.
	 *
	 * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
	 *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
	 *               adding support for further arguments in the future.
	 */
	public function get_template_tags(): array {
		return array(
			'generate_statistics'        => array( $this, 'generate' ),
			'generate_shows_count'       => array( $this, 'count_shows' ),
			'generate_stats_block'       => array( $this, 'generate_stats_block' ),
			'generate_stats_block_actor' => array( $this, 'generate_stats_block_actor' ),
		);
	}

	/**
	 * Enqueue Scripts
	 *
	 * @access public
	 * @return void
	 */
	public function enqueue_scripts() {

		// If it's not any of our pages, return.
		if ( ! is_page( array( 'statistics' ) ) && 'post_type_actors' !== get_post_type() && ! is_page( array( 'this-year' ) ) ) {
			return;
		}

		// Enqueue files shared:
		wp_enqueue_script( 'chartjs', plugin_dir_url( dirname( __DIR__, 1 ) ) . 'assets/js/chart.js', array( 'jquery' ), '4.4.1', false );
		wp_enqueue_script( 'chartjs-plugin-annotation', plugin_dir_url( dirname( __DIR__, 1 ) ) . 'assets/js/chartjs-plugin-annotation.min.js', array( 'chartjs' ), '2.2.1', false );
		wp_enqueue_script( 'palette', plugin_dir_url( dirname( __DIR__, 1 ) ) . 'assets/js/palette.js', array(), '1.0.0', false );

		// Custom extra for stats pages:
		if ( is_page( array( 'statistics' ) ) ) {
			wp_enqueue_script( 'tablesorter', plugin_dir_url( dirname( __DIR__, 1 ) ) . 'assets/js/jquery.tablesorter.js', array( 'jquery' ), '2.31.3', false );
			wp_enqueue_style( 'tablesorter', plugin_dir_url( dirname( __DIR__, 1 ) ) . 'assets/css/theme.bootstrap_4.min.css', array(), '2.31.1', false );

			$statistics = get_query_var( 'statistics', 'none' );
			$stat_view  = get_query_var( 'view', 'main' );

			switch ( $statistics ) {
				case 'nations':
				case 'stations':
					wp_add_inline_script( 'tablesorter', 'jQuery(document).ready(function($){ $("#' . $statistics . 'Table").tablesorter({ theme : "bootstrap", }); });' );
					break;
				case 'death':
					wp_add_inline_script( 'tablesorter', 'jQuery(document).ready(function($){ $("#charactersTable").tablesorter({ theme : "bootstrap", }); });' );
					wp_add_inline_script( 'tablesorter', 'jQuery(document).ready(function($){ $("#showsTable").tablesorter({ theme : "bootstrap", }); });' );
					break;
			}

			switch ( $stat_view ) {
				case 'gender':
				case 'sexuality':
				case 'cliches':
					wp_add_inline_script( 'tablesorter', 'jQuery(document).ready(function($){ $("#charactersTable").tablesorter({ theme : "bootstrap", }); });' );
					break;
				case 'tropes':
				case 'genres':
					wp_add_inline_script( 'tablesorter', 'jQuery(document).ready(function($){ $("#showsTable").tablesorter({ theme : "bootstrap", }); });' );
					break;
			}
		}
	}

	/*
	 * Generate: Statistics Base Code
	 *
	 * @param string $subject      'actors', 'characters', or 'shows'.
	 * @param string $data         The type stats being run.
	 * @param string $format       The format of the output.
	 * @param int    $post_id      Post ID (optional)
	 * @param array  $custom_array Extra array of data (optional)
	 *
	 * @return mixed/na -- Value or the formatted output.
	 */
	public function generate( $subject, $data, $format, $post_id = false, $custom_array = array() ) {
		// Bail early if we're not an approved subject matter.
		if ( ! in_array( $subject, array( 'characters', 'shows', 'actors' ), true ) ) {
			return;
		}

		/**
		 * Count may change based on what we're counting.
		 *
		 * Default is the number of posts.
		 * Dead is number of dead.
		 * Deep data is just weird.
		 */

		// Default
		$count         = wp_count_posts( 'post_type_' . $subject )->publish;
		$data_original = null;

		// If dead ...
		if ( 'dead' === $data ) {
			$count = ( new Build_Dead_Basic() )->make( $subject, 'count' );
		}

		// If there isn't an EXACT match for the data, we may have DEEP data.
		if ( ! isset( Matcher::BUILD_CLASS_MATCHER[ $data ] ) ) {
			// Data for if this is complex and weird.
			$maybe_deep = $this->maybe_deep( $data, $format, $count, $subject );
			if ( false !== $maybe_deep ) {
				$data_original = $data;
				$data          = $maybe_deep['data'];
				$count         = $maybe_deep['count'];
			}

			// Data if this is a year:
			$maybe_year = $this->maybe_year( $data );

			if ( false !== $maybe_year ) {
				$data_original = $data;
				$data          = 'this-year';
			}
		} else {
			$maybe_deep = false;
		}

		// Return early if count:
		if ( 'count' === $format ) {
			return $count;
		}

		// Build Array.
		$build_array = ( new The_Array() )->make( $subject, $data, $format, $post_id, $custom_array, $count, $maybe_deep, $data_original );

		// If the array is empty, bail.
		if ( empty( $build_array ) || ! is_array( $build_array ) ) {
			return;
		}

		// Return Array if array is format.
		// Also if we're dead-list and time. It's just a thing.
		if ( 'array' === $format || ( 'time' === $format && 'dead-list' === $data ) ) {
			return $build_array;
		}

		// Otherwise, build it!
		( new The_Output() )->make( $subject, $data, $build_array, $count, $format, $data_original );
	}

	/**
	 * Custom check for years. Since that comes as 'sexuality_year_YYYY' we need to check:
	 *
	 * 1. Is this between LWTV_FIRST_YEAR and this year?
	 * 2. Is the data in our approved subsets?
	 *
	 * If so, yes.
	 *
	 * @param string $data Data to check
	 *
	 * @return bool
	 */
	public function maybe_year( $data ) {
		$maybe_year = substr( $data, -4 );

		if ( $maybe_year <= gmdate( 'Y' ) && $maybe_year >= LWTV_FIRST_YEAR ) {
			$years_array = array( 'sexuality_year', 'gender_year' );
			$maybe_case  = substr( $data, 0, -5 );
			if ( in_array( $maybe_case, $years_array, true ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Deep Dive for custom data that is extra complex.
	 *
	 * @param string $data   Data we're looking for.
	 * @param string $format Output format.
	 *
	 * @return array Customized array.
	 */
	public function maybe_deep( $data, $format, $count, $subject ) {
		$details = explode( '_', $data );
		$valid   = array( 'nations', 'stations', 'formats', 'country' );

		// If the details don't match what we know to be true, we are false.
		if ( ! in_array( $details[0], $valid, true ) ) {
			return false;
		}

		$minor = $details[1]; // station or nation name.

		if ( 'trendline' === $format && 'on-air' === $details[2] ) {
			$run_data = 'on-air';

			// Build Pre-Array based on station or nation
			switch ( $details[0] ) {
				case 'stations':
					$prearray = lwtv_plugin()->queery_taxonomy( 'post_type_shows', 'lez_stations', 'slug', $minor );
					break;
				case 'country':
					$prearray = lwtv_plugin()->queery_taxonomy( 'post_type_shows', 'lez_country', 'slug', $minor );
					break;
			}

			$count = $this->count_shows( 'total', 'stations', $minor );
		} else {
			$run_data = 'taxonomy_breakdowns';
			$precount = $count;
			$count    = ( new Build_Taxonomy_Breakdowns() )->make( $precount, 'count', $data, $subject );
		}

		return array(
			'data'     => $run_data,
			'minor'    => $minor,
			'prearray' => isset( $prearray ) ? $prearray : false,
			'count'    => $count,
		);
	}

	/*
	 * Count the number of shows along with some other weird things.
	 *
	 * @param string $type  Type of output (onair, total, score)
	 * @param string $tax   The taxonomy   (stations, nations, etc)
	 * @param string $term  The term       (amc, united-kingdom, etc)
	 *
	 * @return array        [total number, on-air, total score, on-air score]
	 */
	public function count_shows( $type, $tax, $term ) {
		$queery = lwtv_plugin()->queery_taxonomy( 'post_type_shows', 'lez_' . $tax, 'slug', $term );
		$return = 0;

		if ( ! is_object( $queery ) ) {
			return 0;
		}

		// Create the date with regards to timezones
		$tz        = 'America/New_York';
		$timestamp = time();
		$dt        = new \DateTime( 'now', new \DateTimeZone( $tz ) ); //first argument "must" be a string
		$dt->setTimestamp( $timestamp ); //adjust the object to correct timestamp
		$date = $dt->format( 'Y' );

		if ( $queery->have_posts() ) {
			switch ( $type ) {
				case 'onair':
					// How many shows are on air.
					$onair = 0;
					foreach ( $queery->posts as $show ) {
						if ( get_post_meta( $show->ID, 'lezshows_airdates', true ) ) {
							$airdates = get_post_meta( $show->ID, 'lezshows_airdates', true );
							$end      = $airdates['finish'];
							if ( 'current' === lcfirst( $end ) || $end >= $date ) {
								++$onair;
							}
						}
					}
					$return = $onair;
					break;
				case 'score':
					// What's the average show score for the shows we're calculating.
					$score = 0;
					foreach ( $queery->posts as $show ) {
						if ( get_post_meta( $show->ID, 'lezshows_the_score', true ) ) {
							$this_score = get_post_meta( $show->ID, 'lezshows_the_score', true );
							$score     += $this_score;
						}
					}
					$score = ( $score / $queery->post_count );

					$return = round( $score, 2 );
					break;
				case 'onairscore':
					// What's the average show score for shows on air?
					$score = 0;
					$onair = 0;
					foreach ( $queery->posts as $show ) {
						if ( get_post_meta( $show->ID, 'lezshows_the_score', true ) ) {
							$this_score = get_post_meta( $show->ID, 'lezshows_the_score', true );
							$airdates   = get_post_meta( $show->ID, 'lezshows_airdates', true );
							$end        = $airdates['finish'];
							if ( 'current' === lcfirst( $end ) || $end >= $date ) {
								$score += $this_score;
								++$onair;
							}
						}
					}
					$score  = ( 0 !== $onair ) ? ( $score / $onair ) : $onair;
					$return = round( $score, 2 );
					break;
				default:
					// How many shows are there?
					$return = $queery->post_count;
			}
		}
		return $return;
	}

	/**
	 * Display statistics
	 *
	 *  @param array $attributes
	 *
	 * @return string
	 */
	public function generate_stats_block( $attributes ) {
		return ( new Gutenberg_SSR() )->statistics( $attributes );
	}

	/**
	 * Display Actor stats block
	 *
	 * @param  array $attributes
	 *
	 * @return string
	 */
	public function generate_stats_block_actor( $attributes ) {
		return ( new Gutenberg_SSR() )->mini_stats( $attributes );
	}
}
