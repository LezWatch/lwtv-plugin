<?php
/**
 * Name: Statistics Matcher
 */

namespace LWTV\Statistics;

class Matcher {
	/**
	 * Array of Data types and their associated classes.
	 */
	const BUILD_CLASS_MATCHER = array(
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
}
