<?php
/**
 * Name: Statistics Code
 *
 * This file has the basic defines for all stats.
 * It's pretty much only called in /page-template/statistics.php
 */

// if this file is called directly abort
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Include sub files
new LWTV_Statistics_Query_Vars();
new LWTV_Statistics_Gutenberg_SSR();

class LWTV_Statistics {

	/**
	 * Array of Data types and their associated classes.
	 */
	const DATA_CLASS_MATCHER = array(
		'actor_char_roles'    => 'Actor_Char_Role',
		'actor_char_dead'     => 'Actor_Char_Dead',
		'actor_gender'        => 'Taxonomy',
		'actor_sexuality'     => 'Taxonomy',
		'cliches'             => 'Taxonomy',
		'dead'                => 'Dead_Basic',
		'dead-gender'         => 'Dead_Taxonomy',
		'dead-list'           => 'Dead_List',
		'dead-nations'        => 'Dead_Complex_Taxonomy',
		'dead-role'           => 'Dead_Role',
		'dead-sex'            => 'Dead_Taxonomy',
		'dead-shows'          => 'Dead_Shows',
		'dead-stations'       => 'Dead_Complex_Taxonomy',
		'dead-years'          => 'Dead_Year',
		'formats'             => 'Taxonomy',
		'gender'              => 'Taxonomy',
		'genres'              => 'Taxonomy',
		'intersections'       => 'Taxonomy',
		'on-air'              => 'On_Air',
		'per-actor'           => 'Actor_Chars',
		'per-char'            => 'Actor_Chars',
		'roles'               => 'Meta',
		'romantic'            => 'Taxonomy',
		'sexuality'           => 'Taxonomy',
		'stars'               => 'Complex_Taxonomy',
		'taxonomy_breakdowns' => 'Taxonomy_Breakdowns',
		'thumbs'              => 'Meta',
		'this-year'           => 'This_Year',
		'triggers'            => 'Complex_Taxonomy',
		'tropes'              => 'Taxonomy',
		'queer-irl'           => 'Complex_Taxonomy',
		'weloveit'            => 'Yes_No',
	);

	// Array of Formats and their classes:
	const FORMAT_CLASS_MATCHER = array(
		'average'    => 'Averages',
		'barchart'   => 'Barcharts',
		'high'       => 'Averages',
		'list'       => 'Lists',
		'low'        => 'Averages',
		'percentage' => 'Percentages',
		'piechart'   => 'Piecharts',
		'stackedbar' => 'Stacked_Barcharts',
		'trendline'  => 'Trendline',
	);

	// Array of custom meta data used.
	const META_PARAMS = array(
		'roles'  => array(
			'array'   => array( 'regular', 'recurring', 'guest' ),
			'key'     => 'lezchars_show_group',
			'compare' => 'LIKE',
		),
		'thumbs' => array(
			'array'   => array( 'Yes', 'No', 'Meh', 'TBD' ),
			'key'     => 'lezshows_worthit_rating',
			'compare' => null,
		),
	);

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

		// If it's not any of our pages, return.
		if ( ! is_page( array( 'statistics' ) ) && 'post_type_actors' !== get_post_type() && ! is_page( array( 'this-year' ) ) ) {
			return;
		}

		// Enqueue files shared:
		wp_enqueue_script( 'chartjs', plugin_dir_url( __DIR__ ) . 'assets/js/chart.js', array( 'jquery' ), '4.4.0', false );
		wp_enqueue_script( 'chartjs-plugin-annotation', plugin_dir_url( __DIR__ ) . 'assets/js/chartjs-plugin-annotation.min.js', array( 'chartjs' ), '2.2.1', false );
		wp_enqueue_script( 'palette', plugin_dir_url( __DIR__ ) . 'assets/js/palette.js', array(), '1.0.0', false );

		// Custom extra for stats pages:
		if ( is_page( array( 'statistics' ) ) ) {
			wp_enqueue_script( 'tablesorter', plugin_dir_url( __DIR__ ) . 'assets/js/jquery.tablesorter.js', array( 'jquery' ), '2.31.3', false );
			wp_enqueue_style( 'tablesorter', plugin_dir_url( __DIR__ ) . 'assets/css/theme.bootstrap_4.min.css', array(), '2.31.1', false );

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
			$count = ( new LWTV_Statistics_Dead_Basic_Build() )->make( $subject, 'count' );
		}

		// If there isn't an EXACT match for the data, we may have DEEP data.
		if ( ! isset( self::DATA_CLASS_MATCHER[ $data ] ) ) {
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
		$build_array = ( new LWTV_Statistics_Array() )->make( $subject, $data, $format, $post_id, $custom_array, $count, $maybe_deep, $data_original );

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
		( new LWTV_Statistics_Output() )->make( $subject, $data, $build_array, $count, $format, $data_original );
	}

	/**
	 * Custom check for years. Since that comes as 'sexuality_year_YYYY' we need to check:
	 *
	 * 1. Is this between FIRST_LWTV_YEAR and this year?
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

		if ( $maybe_year <= gmdate( 'Y' ) && $maybe_year >= FIRST_LWTV_YEAR ) {
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
		$return  = false;
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
					$prearray = ( new LWTV_Features_Loops() )->tax_query( 'post_type_shows', 'lez_stations', 'slug', $minor );
					break;
				case 'country':
					$prearray = ( new LWTV_Features_Loops() )->tax_query( 'post_type_shows', 'lez_country', 'slug', $minor );
					break;
			}

			$count = $this->count_shows( 'total', 'stations', $minor );
		} else {
			$run_data = 'taxonomy_breakdowns';
			$precount = $count;
			$count    = ( new LWTV_Statistics_Taxonomy_Breakdowns_Build() )->make( $precount, 'count', $data, $subject );
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
		$queery = ( new LWTV_Features_Loops() )->tax_query( 'post_type_shows', 'lez_' . $tax, 'slug', $term );
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
}
