<?php
/**
 * Name: Statistics Code
 *
 * This file has the basic defines for all stats.
 * It's pretty much only called in /page-template/statistics.php
 */

class LWTV_Stats {

	/*
	 * Construct
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
		wp_enqueue_script( 'chartjs', plugin_dir_url( dirname( __FILE__ ) ) .'/assets/js/Chart.bundle.min.js' , array( 'jquery' ), '2.7.1', false );
		wp_enqueue_script( 'chartjs-colors', plugin_dir_url( dirname( __FILE__ ) ) .'/assets/js/Chart.colors.js' , array( 'chartjs' ), '1.0.0', false );
	}

	/*
	 * Generate: Statistics Base Code
	 *
	 * @param string $subject 'characters' or 'shows'
	 * @param string $data The stats being run
	 * @param string $format The format of the output
	 *
	 * @return Content based on $format
	 */
	static function generate( $subject, $data, $format ) {
		// Bail early if we're not an approved subject matter
		if ( !in_array( $subject, array( 'characters', 'shows', 'actors' ) ) ) exit;

		// Build Variables
		$array     = array();
		$post_type = 'post_type_'.$subject;
		$count     = wp_count_posts( $post_type )->publish;
		$taxonomy  = 'lez_'.$data;

		// Simple Taxonomy Arrays
		$simple_tax_array = array( 'cliches', 'sexuality', 'gender', 'tropes', 'formats', 'triggers', 'stars', 'romantic', 'actor_gender', 'actor_sexuality', 'genres' );
		if ( in_array( $data, $simple_tax_array ) ) $array = LWTV_Stats_Arrays::taxonomy( $post_type, $taxonomy );

		// Complicated Taxonomy Array
		if ( $data == 'queer-irl' ) $array = LWTV_Stats_Arrays::queer( $count, $subject );

		// Meta arrays
		if ( $data == 'role' )   $array = LWTV_Stats_Arrays::meta( $post_type, array( 'regular', 'recurring', 'guest' ), 'lezchars_show_group', $data, 'LIKE' );
		if ( $data == 'thumbs' ) $array = LWTV_Stats_Arrays::meta( $post_type, array( 'Yes', 'No', 'Meh' ), 'lezshows_worthit_rating', $data );

		// Yes/No arrays
		if ( $data == 'weloveit' ) $array = LWTV_Stats_Arrays::yes_no( $post_type, $data, $count );
		if ( $data == 'current' )  $array = LWTV_Stats_Arrays::yes_no( $post_type, $data, $count );

		// SUPER FUCKING COMPLICATED
		// Custom call for Show Scores
		if ( $data == 'scores' )    $array = LWTV_Stats_Arrays::scores( $post_type );
		// Custom call for show roles of character in each role
		if ( $data == 'charroles' ) $array = LWTV_Stats_Arrays::show_roles();
		// Custom call for actor/character 
		if ( $data == 'per-char' )  $array = LWTV_Stats_Arrays::actor_chars( 'characters' );
		if ( $data == 'per-actor' ) $array = LWTV_Stats_Arrays::actor_chars( 'actors' );
		// Custom call for Nations or Stations
		if ( substr( $data, 0, 7) == 'country' || substr( $data, 0, 8) == 'stations' ) {
			$array    = LWTV_Stats_Arrays::characters_details_shows( $count, $format, $data );
			// Stupid counting shit ...
			$precount = $count;
			$count    = LWTV_Stats_Arrays::characters_details_shows( $count, $format, $data );
			if ( $format == 'percentage' ) $count = $precount;
		}

		// And dead stats? IN-fucking-sane
		// Everything gets a custom setup
		if ( $data == 'dead' ) {
			$array = LWTV_Stats_Arrays::dead_basic( $subject, 'array' );
			$count = LWTV_Stats_Arrays::dead_basic( $subject, 'count' );
		}
		if ( $data == 'dead-sex' )    $array = LWTV_Stats_Arrays::dead_taxonomy( $post_type, 'lez_sexuality' );
		if ( $data == 'dead-gender' ) $array = LWTV_Stats_Arrays::dead_taxonomy( $post_type, 'lez_gender' );
		if ( $data == 'dead-role' )   $array = LWTV_Stats_Arrays::dead_role();
		if ( $data == 'dead-shows' )  $array = LWTV_Stats_Arrays::dead_shows( 'simple' );
		if ( $data == 'dead-years' )  $array = LWTV_Stats_Arrays::dead_year();

		// Acutally output shit
		if ( $format == 'barchart' )   LWTV_Stats_Output::barcharts( $subject, $data, $array );
		if ( $format == 'stackedbar' ) LWTV_Stats_Output::stacked_barcharts( $subject, $data, $array );
		if ( $format == 'piechart' )   LWTV_Stats_Output::piecharts( $subject, $data, $array );
		if ( $format == 'trendline' )  LWTV_Stats_Output::trendline( $subject, $data, $array );
		if ( $format == 'count' )      return $count;
		if ( $format == 'list' )       LWTV_Stats_Output::lists( $subject, $data, $array, $count );
		if ( $format == 'percentage' ) LWTV_Stats_Output::percentages( $subject, $data, $array, $count );
		if ( $format == 'average' )    LWTV_Stats_Output::averages( $subject, $data, $array, $count, 'average' );
		if ( $format == 'high' )       LWTV_Stats_Output::averages( $subject, $data, $array, $count, 'high' );
		if ( $format == 'low' )        LWTV_Stats_Output::averages( $subject, $data, $array, $count, 'low' );
		if ( $format == 'array' )      return $array;
	}
}

new LWTV_Stats();