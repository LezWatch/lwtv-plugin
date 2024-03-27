<?php
/*
 * Description: FacetWP Indexing
 */

namespace LWTV\Plugins\FacetWP;

class Indexing {

	/**
	 * Constructor
	 */
	public function __construct() {

		// Filter data before saving it
		add_filter( 'facetwp_index_row', array( $this, 'facetwp_index_row' ), 10, 2 );

		// Filter Facet output
		add_filter(
			'facetwp_facet_html',
			function ( $output, $params ) {
				if ( 'show_airdates' === $params['facet']['name'] ) {
					$output = str_replace( 'Min', 'First Year', $output );
					$output = str_replace( 'Max', 'Last Year', $output );
				}
				return $output;
			},
			10,
			2,
		);

		// Adding a weird filter...
		add_filter(
			'facetwp_facet_sources',
			function ( $sources ) {
				$sources['custom_fields']['choices']['cf/lwtv_data'] = 'lwtv_data';
				return $sources;
			}
		);

		add_filter( 'facetwp_is_main_query', array( $this, 'facetwp_is_main_query' ), 10, 2 );
	}

	/**
	 * Force Facet to show sometimes
	 */
	public function facetwp_is_main_query( $is_main_query, $query ) {
		if ( is_admin() ) {
			$is_main_query = false;
		} elseif ( isset( $query->query_vars['facetwp'] ) ) {
			$is_main_query = true;
		}
		return $is_main_query;
	}

	/**
	 * Filter Data before it's saved
	 * Useful for serialized data but also capitalizing stars
	 *
	 * @since 1.1
	 */
	public function facetwp_index_row( $params, $facet_class ) {

		// Shows
		if ( 'post_type_shows' === get_post_type( $params['post_id'] ) ) {
			// Stars
			// Capitalize
			if ( 'show_stars' === $params['facet_name'] ) {
				$params['facet_value']         = $params['facet_value'];
				$params['facet_display_value'] = ucfirst( $params['facet_display_value'] );
				$facet_class->insert( $params );
				// skip default indexing
				$params['facet_value'] = '';
				return $params;
			}
			// Shows we Love
			// Change 'on' to 'yes'
			if ( 'show_loved' === $params['facet_name'] ) {
				$params['facet_value']         = ( 'on' === $params['facet_value'] ) ? 'yes' : 'no';
				$params['facet_display_value'] = ( 'on' === $params['facet_display_value'] ) ? 'Yes' : 'No';
				$facet_class->insert( $params );
				// skip default indexing
				$params['facet_value'] = '';
				return $params;
			}
			// Shows with Death
			// If the count is 1 or more, there's death
			if ( 'show_death' === $params['facet_name'] ) {
				$params['facet_value']         = ( $params['facet_value'] >= 1 ) ? 'yes' : 'no';
				$params['facet_display_value'] = ( $params['facet_display_value'] >= 1 ) ? 'Yes' : 'No';
				$facet_class->insert( $params );
				// skip default indexing
				$params['facet_value'] = '';
				return $params;
			}
			// Trigger Warning
			// If 'on' change to 'High', else capitalize
			if ( 'show_trigger_warning' === $params['facet_name'] ) {
				$params['facet_value']         = ( 'on' === $params['facet_display_value'] ) ? 'high' : $params['facet_display_value'];
				$params['facet_display_value'] = ( 'on' === $params['facet_display_value'] ) ? 'High' : ucfirst( $params['facet_display_value'] );
				$facet_class->insert( $params );
				// skip default indexing
				$params['facet_value'] = '';
				return $params;
			}
			// Airdates
			// Saves two values for two sources
			// Also saves on_air as yes or no
			// a:2:{s:5:"start";s:4:"1994";s:6:"finish";s:4:"2009";}
			if ( 'show_airdates' === $params['facet_name'] ) {

				// Parse start and end dates  (use 'now' if 'current' or empty)
				$values = (array) $params['facet_value'];
				$start  = ( isset( $values['start'] ) ) ? $values['start'] : '';
				$end    = ( isset( $values['finish'] ) && lcfirst( $values['finish'] ) !== 'current' ) ? $values['finish'] : gmdate( 'Y' );

				$params_start = $params;
				$params_end   = $params;

				// Add start date
				$params_start['facet_value']         = $start;
				$params_start['facet_display_value'] = $start;
				$facet_class->insert( $params_start );

				// Add end date
				$params_end['facet_value']         = $end;
				$params_end['facet_display_value'] = $end;
				$facet_class->insert( $params_end );

				// Extra check for is it currently on air
				$params_on_air = $params;
				$on_air        = 'no';
				$on_air_meta   = get_post_meta( $params['post_id'], 'lezshows_on_air', true );
				// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
				if ( isset( $on_air_meta ) && in_array( $on_air_meta, array( 'yes', 'no' ) ) ) {
					$on_air = $on_air_meta;
				} elseif ( 'current' === lcfirst( $end ) || $end > gmdate( 'Y' ) ) {
					$on_air = 'yes';
				}
				$params_on_air['facet_name']          = 'show_on_air';
				$params_on_air['facet_value']         = $on_air;
				$params_on_air['facet_display_value'] = ucfirst( $on_air );
				$facet_class->insert( $params_on_air );

				// skip default indexing
				$params['facet_value'] = '';
				return $params;
			}

			// Some extra weird things...
			// Because you can't store data for EMPTY fields so there's a 'fake'
			// facet called 'all_the_missing' and we use it to pass through data
			if ( 'all_the_missing' === $params['facet_name'] ) {
				// If we do not love the show...
				$loved = get_post_meta( $params['post_id'], 'lezshows_worthit_show_we_love', true );
				if ( empty( $loved ) ) {
					$params_loved                        = $params;
					$params_loved['facet_name']          = 'show_loved';
					$params_loved['facet_source']        = 'cf/lezshows_worthit_show_we_love';
					$params_loved['facet_value']         = 'no';
					$params_loved['facet_display_value'] = 'No';
					$facet_class->insert( $params_loved );
				}
				// If there are no warnings
				$warn = get_the_terms( $params['post_id'], 'lez_triggers' );
				if ( empty( $warn ) ) {
					$params_warn                        = $params;
					$params_warn['facet_name']          = 'show_trigger_warning';
					$params_warn['facet_source']        = 'tax/lez_triggers';
					$params_warn['facet_value']         = 'none';
					$params_warn['facet_display_value'] = 'None';
					$facet_class->insert( $params_warn );
				}
				// If there are no stars
				$stars = get_the_terms( $params['post_id'], 'lez_stars' );
				if ( empty( $stars ) ) {
					$params_stars                        = $params;
					$params_stars['facet_name']          = 'show_stars';
					$params_stars['facet_source']        = 'tax/lez_stars';
					$params_stars['facet_value']         = 'none';
					$params_stars['facet_display_value'] = 'None';
					$facet_class->insert( $params_stars );
				}
				// skip default indexing
				$params['facet_value'] = '';
				return $params;
			}
		}

		// Actors
		if ( 'post_type_actors' === get_post_type( $params['post_id'] ) ) {
			// Is Queer
			// Change 'on' to 'yes'
			if ( 'is_queer' === $params['facet_name'] ) {
				$params['facet_value']         = ( '1' === $params['facet_value'] ) ? 'yes' : 'no';
				$params['facet_display_value'] = ( '1' === $params['facet_display_value'] ) ? 'Is Queer' : 'Is Not Queer';
				$facet_class->insert( $params );
				// skip default indexing
				$params['facet_value'] = '';
				return $params;
			}
		}

		// Characters
		if ( 'post_type_characters' === get_post_type( $params['post_id'] ) ) {
			// Sorting Actors - Saves one value for each actor
			// EXAMPLE: a:1:{i:0;s:13:"Rachel Bilson";}
			if ( 'char_actors' === $params['facet_name'] ) {
				$values = (array) $params['facet_value'];
				foreach ( $values as $val ) {
					if ( empty( $val ) ) {
						continue;
					}
					$params['facet_value']         = $val;
					$params['facet_display_value'] = get_the_title( $val );
					$facet_class->insert( $params );
				}
				// skip default indexing
				$params['facet_value'] = '';
				return $params;
			}
			// Sorting Shows - Saves one value for each show
			// EXAMPLE: a:1:{i:0;a:3:{s:4:"show";s:3:"655";s:4:"type";s:9:"recurring";s:7:"appears";a:1:{i:0;s:4:"2017";}}}
			if ( 'char_shows' === $params['facet_name'] ) {
				$values = (array) $params['facet_value'];
				foreach ( $values as $val ) {
					if ( ! isset( $val['show'] ) ) {
						continue;
					}
					$params['facet_value']         = $val['show'];
					$params['facet_display_value'] = get_the_title( $val['show'] );
					$facet_class->insert( $params );
				}
				// skip default indexing
				$params['facet_value'] = '';
				return $params;
			}
			// Sorting Roles - Saves one value for each show.
			// a:1:{i:0;a:3:{s:4:"show";s:3:"655";s:4:"type";s:9:"recurring";s:7:"appears";a:1:{i:0;s:4:"2017";}}}
			if ( 'char_roles' === $params['facet_name'] ) {
				$values = (array) $params['facet_value'];
				foreach ( $values as $val ) {
					if ( ! isset( $val['type'] ) ) {
						continue;
					}
					$params['facet_value']         = $val['type'];
					$params['facet_display_value'] = ucfirst( $val['type'] );
					$facet_class->insert( $params );
				}
				// skip default indexing
				$params['facet_value'] = '';
				return $params;
			}

			// Years
			// Saves one value for each year
			// a:1:{i:0;a:3:{s:4:"show";s:3:"655";s:4:"type";s:9:"recurring";s:7:"appears";a:1:{i:0;s:4:"2017";}}}
			// Years is a sub array of the array, because I was thinking clever and forgot what a metric PITA this is.
			if ( 'char_years' === $params['facet_name'] ) {
				$values = (array) $params['facet_value'];
				foreach ( $values as $val ) {
					if ( ! isset( $val['appears'] ) ) {
						continue;
					}
					foreach ( $val['appears'] as $year ) {
						$params['facet_value']         = $year;
						$params['facet_display_value'] = $year;
					}
					$facet_class->insert( $params );
				}
				// skip default indexing
				$params['facet_value'] = '';
				return $params;
			}
		}

		return $params;
	}
}
