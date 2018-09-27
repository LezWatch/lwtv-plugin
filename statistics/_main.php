<?php
/**
 * Name: Statistics Code
 *
 * This file has the basic defines for all stats.
 * It's pretty much only called in /page-template/statistics.php
 */

class LWTV_Stats {

	/**
	 * Construct
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		add_action( 'init', array( &$this, 'init' ) );
	}

	/**
	 * Init
	 *
	 * @access public
	 * @return void
	 */
	public function init() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue Scripts
	 *
	 * @access public
	 * @return void
	 */
	public function enqueue_scripts() {

		if ( is_page( array( 'statistics' ) ) ) {
			$statistics = get_query_var( 'statistics', 'none' );
			$stat_view  = ( isset( $_GET['view'] ) ) ? esc_attr( $_GET['view'] ) : ''; // WPSC: CSRF ok.

			wp_enqueue_script( 'chartjs', plugin_dir_url( dirname( __FILE__ ) ) . 'assets/js/Chart.bundle.min.js', array( 'jquery' ), '2.7.2', false );
			wp_enqueue_script( 'chartjs-plugins', plugin_dir_url( dirname( __FILE__ ) ) . 'assets/js/Chart.plugins.js', array( 'chartjs' ), '1.0.0', false );
			wp_enqueue_script( 'palette', plugin_dir_url( dirname( __FILE__ ) ) . 'assets/js/palette.js', array(), '1.0.0', false );
			wp_enqueue_script( 'tablesorter', plugin_dir_url( dirname( __FILE__ ) ) . 'assets/js/jquery.tablesorter.js', array( 'jquery' ), '2.30.7', false );
			wp_enqueue_style( 'tablesorter', plugin_dir_url( dirname( __FILE__ ) ) . 'assets/css/theme.bootstrap_4.min.css', array(), '2.30.7', false );

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
					wp_add_inline_script( 'tablesorter', 'jQuery(document).ready(function($){ $("#charactersTable").tablesorter({ theme : "bootstrap", }); });' );
					break;
				case 'tropes':
					wp_add_inline_script( 'tablesorter', 'jQuery(document).ready(function($){ $("#showsTable").tablesorter({ theme : "bootstrap", }); });' );
			}
		}
	}

	/*
	 * Generate: Statistics Base Code
	 *
	 * @param string $subject 'characters' or 'shows'.
	 * @param string $data The stats being run.
	 * @param string $format The format of the output.
	 *
	 * @return array
	 */
	public static function generate( $subject, $data, $format ) {
		// Bail early if we're not an approved subject matter.
		if ( ! in_array( $subject, array( 'characters', 'shows', 'actors' ), true ) ) {
			return;
		}

		// Build Variables.
		$array     = array();
		$post_type = 'post_type_' . $subject;
		$count     = wp_count_posts( $post_type )->publish;
		$taxonomy  = 'lez_' . $data;

		// Calculate the array based on data.
		// This includes everything EXCEPT Death and some weeeeeird stuff.
		switch ( $data ) {
			case 'cliches':
			case 'sexuality':
			case 'gender':
			case 'tropes':
			case 'formats':
			case 'romantic':
			case 'actor_gender':
			case 'actor_sexuality':
			case 'genres':
			case 'intersections':
				// Simple taxonomy data.
				$array = LWTV_Stats_Arrays::taxonomy( $post_type, $taxonomy );
				break;
			case 'queer-irl':
			case 'triggers':
			case 'stars':
				// Complex Taxonomy Data.
				$array = LWTV_Stats_Arrays::complex_taxonomy( $count, $data, $subject );
				break;
			case 'role':
				$roles = array(
					'regular',
					'recurring',
					'guest',
				);
				$array = LWTV_Stats_Arrays::meta( $post_type, $role_array, 'lezchars_show_group', $data, 'LIKE' );
				break;
			case 'thumbs':
				$thumbs = array(
					'Yes',
					'No',
					'Meh',
					'TBD',
				);
				$array  = LWTV_Stats_Arrays::meta( $post_type, $thumbs, 'lezshows_worthit_rating', $data );
				break;
			case 'weloveit':
			case 'current':
				$array = LWTV_Stats_Arrays::yes_no( $post_type, $data, $count );
				break;
			case 'scores':
				// Show Scores.
				$array = LWTV_Stats_Arrays::scores( $post_type );
				break;
			case 'charroles':
				// show roles of character in each role.
				$array = LWTV_Stats_Arrays::show_roles();
				break;
			case 'per-char':
				// Custom call for actor/character.
				$array = LWTV_Stats_Arrays::actor_chars( 'characters' );
				break;
			case 'per-actor':
				// Custom call for character/actor.
				$array = LWTV_Stats_Arrays::actor_chars( 'actors' );
				break;
		}

		// Custom call for Nations & Stations.
		if ( 'country' === substr( $data, 0, 7 ) || 'stations' === substr( $data, 0, 8 ) ) {
			$array    = LWTV_Stats_Arrays::taxonomy_breakdowns( $count, $format, $data, $subject );
			$precount = $count;
			$count    = LWTV_Stats_Arrays::taxonomy_breakdowns( $precount, 'count', $data, $subject );
		}

		// And dead stats? IN-fucking-sane.
		// Everything gets a custom setup.
		if ( false !== strpos( $data, 'dead' ) ) {
			switch ( $data ) {
				case 'dead':
					$array = LWTV_Stats_Arrays::dead_basic( $subject, 'array' );
					$count = LWTV_Stats_Arrays::dead_basic( $subject, 'count' );
					break;
				case 'dead-sex':
					$array = LWTV_Stats_Arrays::dead_taxonomy( $post_type, 'lez_sexuality' );
					break;
				case 'dead-gender':
					$array = LWTV_Stats_Arrays::dead_taxonomy( $post_type, 'lez_gender' );
					break;
				case 'dead-role':
					$array = LWTV_Stats_Arrays::dead_role();
					break;
				case 'dead-shows':
					$array = LWTV_Stats_Arrays::dead_shows( 'simple' );
					break;
				case 'dead-years':
					$array = LWTV_Stats_Arrays::dead_year();
					break;
				case 'dead-stations':
					$array = LWTV_Stats_Arrays::dead_complex_taxonomy( 'stations' );
					break;
				case 'dead-nations':
					$array = LWTV_Stats_Arrays::dead_complex_taxonomy( 'country' );
					break;
			}
		}

		// Acutally output shit.
		switch ( $format ) {
			case 'barchart':
				LWTV_Stats_Output::barcharts( $subject, $data, $array );
				break;
			case 'stackedbar':
				LWTV_Stats_Output::stacked_barcharts( $subject, $data, $array );
				break;
			case 'piechart':
				LWTV_Stats_Output::piecharts( $subject, $data, $array );
				break;
			case 'trendline':
				LWTV_Stats_Output::trendline( $subject, $data, $array );
				break;
			case 'count':
				$return = $count;
				break;
			case 'list':
				LWTV_Stats_Output::lists( $subject, $data, $array, $count );
				break;
			case 'percentage':
				LWTV_Stats_Output::percentages( $subject, $data, $array, $count );
				break;
			case 'average':
				LWTV_Stats_Output::averages( $subject, $data, $array, $count, 'average' );
				break;
			case 'high':
				LWTV_Stats_Output::averages( $subject, $data, $array, $count, 'high' );
				break;
			case 'low':
				LWTV_Stats_Output::averages( $subject, $data, $array, $count, 'low' );
				break;
			case 'array':
				$return = $array;
				break;
		}

		if ( isset( $return ) ) {
			return $return;
		}
	}

	/*
	 * Count the number of shows along with some other weird things.
	 *
	 * @param string $type  Type of output (onair, total, score)
	 * @param string $tax   The taxonomy   (stations, nations, etc)
	 * @param string $term  The term       (amc, united-kingdom, etc)
	 *
	 * @return array
	 */
	public static function showcount( $type, $tax, $term ) {

		$queery = LWTV_Loops::tax_query( 'post_type_shows', 'lez_' . $tax, 'slug', $term );
		$return = 0;

		// Create the date with regards to timezones
		$tz        = 'America/New_York';
		$timestamp = time();
		$dt        = new DateTime( 'now', new DateTimeZone( $tz ) ); //first argument "must" be a string
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
								$onair++;
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
								$onair++;
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
}

new LWTV_Stats();

// Include sub files
require_once 'array.php';
require_once 'output.php';
