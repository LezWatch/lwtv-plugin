<?php
/*
Description: FacetWP Customizations
Version: 1.1
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * class LWTV_FacetWP
 *
 * Custom FacetWP Functions
 * @since 1.0
 */
class LWTV_FacetWP {

	/**
	 * Constructor
	 */
	public function __construct() {

		// Filter data before saving it
		add_filter( 'facetwp_index_row', array( $this, 'facetwp_index_row' ), 10, 2 );

		// Filter sort options to add our own
		add_filter( 'facetwp_sort_options', array( $this, 'facetwp_sort_options' ), 10, 2 );

		// Filter Facet output
		add_filter( 'facetwp_facet_html', function( $output, $params ) {
			if ( 'show_airdates' === $params['facet']['name'] ) {
				$output = str_replace( 'Min', 'First Year', $output );
				$output = str_replace( 'Max', 'Last Year', $output );
			}
			return $output;
		}, 10, 2 );

		// Adding a weird filter...
		add_filter( 'facetwp_facet_sources', function( $sources ) {
			$sources['custom_fields']['choices']['cf/lwtv_data'] = 'lwtv_data';
			return $sources;
		});

		if ( is_admin() ) {
			// Don't output <!--fwp-loop--> on admin pages
			add_filter( 'facetwp_is_main_query', function( $is_main_query, $query ) {
				return false;
			}, 10, 2 );
		} else {
			// DO output on pages where the main-query is set to true anyway. Asshols
			add_filter( 'facetwp_is_main_query', array( $this, 'facetwp_is_main_query' ), 10, 2 );
		}

	}

	/**
	 * Force Facet to show sometimes
	 */
	public function facetwp_is_main_query( $is_main_query, $query ) {
		if ( isset( $query->query_vars['facetwp'] ) ) {
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
	public function facetwp_index_row( $params, $class ) {

		// Shows
		if ( 'post_type_shows' === get_post_type( $params['post_id'] ) ) {
			// Stars
			// Capitalize
			if ( 'show_stars' === $params['facet_name'] ) {
				$params['facet_value']         = $params['facet_value'];
				$params['facet_display_value'] = ucfirst( $params['facet_display_value'] );
				$class->insert( $params );
				return false; // skip default indexing
			}
			// Shows we Love
			// Change 'on' to 'yes'
			if ( 'show_loved' === $params['facet_name'] ) {
				$params['facet_value']         = ( 'on' === $params['facet_value'] ) ? 'yes' : 'no';
				$params['facet_display_value'] = ( 'on' === $params['facet_display_value'] ) ? 'Yes' : 'No';
				$class->insert( $params );
				return false; // skip default indexing
			}
			// Shows with Death
			// If the count is 1 or more, there's death
			if ( 'show_death' === $params['facet_name'] ) {
				$params['facet_value']         = ( $params['facet_value'] >= 1 ) ? 'yes' : 'no';
				$params['facet_display_value'] = ( $params['facet_display_value'] >= 1 ) ? 'Yes' : 'No';
				$class->insert( $params );
				return false; // skip default indexing
			}
			// Trigger Warning
			// If 'on' change to 'High', else capitalize
			if ( 'show_trigger_warning' === $params['facet_name'] ) {
				$params['facet_value']         = ( 'on' === $params['facet_display_value'] ) ? 'high' : $params['facet_display_value'];
				$params['facet_display_value'] = ( 'on' === $params['facet_display_value'] ) ? 'High' : ucfirst( $params['facet_display_value'] );
				$class->insert( $params );
				return false; // skip default indexing
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
				$class->insert( $params_start );

				// Add end date
				$params_end['facet_value']         = $end;
				$params_end['facet_display_value'] = $end;
				$class->insert( $params_end );

				// Extra check for is it currently on air
				$params_on_air = $params;
				$on_air        = 'no';
				if ( 'current' === lcfirst( $end ) || $end >= gmdate( 'Y' ) ) {
					$on_air = 'yes';
				}
				$params_on_air['facet_name']          = 'show_on_air';
				$params_on_air['facet_value']         = $on_air;
				$params_on_air['facet_display_value'] = ucfirst( $on_air );
				$class->insert( $params_on_air );

				return false; // skip default indexing
			}
			// Shows by Gender, Sexuality, Romance
			// If they have a count higher than 0, we flag them.
			// This is just going to list if they have that gender. We're not using it yet. We might.
			$show_char_array = array( 'show_char_gender', 'show_char_sexuality', 'show_char_romance' );
			if ( in_array( $params['facet_name'], $show_char_array, true ) ) {
				$values = (array) $params['facet_value'];
				foreach ( $values as $title => $val ) {
					if ( is_numeric( $val ) && $val > 0 ) {
						$params['facet_value']         = $title;
						$params['facet_display_value'] = ucfirst( $title );
						$class->insert( $params );
					}
				}
				return false; // skip default indexing
			}

			// Some extra weird things...
			// Becuase you can't store data for EMPTY fields so there's a 'fake'
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
					$class->insert( $params_loved );
				}
				// If there are no warnings
				$warn = get_the_terms( $params['post_id'], 'lez_triggers' );
				if ( empty( $warn ) ) {
					$params_warn                        = $params;
					$params_warn['facet_name']          = 'show_trigger_warning';
					$params_warn['facet_source']        = 'tax/lez_triggers';
					$params_warn['facet_value']         = 'none';
					$params_warn['facet_display_value'] = 'None';
					$class->insert( $params_warn );
				}
				// If there are no stars
				$stars = get_the_terms( $params['post_id'], 'lez_stars' );
				if ( empty( $stars ) ) {
					$params_stars                        = $params;
					$params_stars['facet_name']          = 'show_stars';
					$params_stars['facet_source']        = 'tax/lez_stars';
					$params_stars['facet_value']         = 'none';
					$params_stars['facet_display_value'] = 'None';
					$class->insert( $params_stars );
				}
				return false; // skip default indexing
			}
		}

		// Actors
		if ( 'post_type_actors' === get_post_type( $params['post_id'] ) ) {
			// Is Queer
			// Change 'on' to 'yes'
			if ( 'is_queer' === $params['facet_name'] ) {
				$params['facet_value']         = ( '1' === $params['facet_value'] ) ? 'yes' : 'no';
				$params['facet_display_value'] = ( '1' === $params['facet_display_value'] ) ? 'Is Queer' : 'Is Not Queer';
				$class->insert( $params );
				return false; // skip default indexing
			}
		}

		// Characters
		if ( 'post_type_characters' === get_post_type( $params['post_id'] ) ) {
			// Actors
			// Saves one value for each actor
			// a:1:{i:0;s:13:"Rachel Bilson";}
			if ( 'char_actors' === $params['facet_name'] ) {
				$values = (array) $params['facet_value'];
				foreach ( $values as $val ) {
					$params['facet_value']         = $val;
					$params['facet_display_value'] = get_the_title( $val );
					$class->insert( $params );
				}
				return false; // skip default indexing
			}
			// Shows
			// Saves one value for each show
			// a:1:{i:0;a:2:{s:4:"show";s:3:"655";s:4:"type";s:9:"recurring";}}
			if ( 'char_shows' === $params['facet_name'] ) {
				$values = (array) $params['facet_value'];
				foreach ( $values as $val ) {
						$params['facet_value']         = $val['show'];
						$params['facet_display_value'] = get_the_title( $val['show'] );
						$class->insert( $params );
				}
				return false; // skip default indexing
			}
			// User Roles
			// Saves one value for each show
			// a:1:{i:0;a:2:{s:4:"show";s:3:"655";s:4:"type";s:9:"recurring";}}
			if ( 'char_roles' === $params['facet_name'] ) {
				$values = (array) $params['facet_value'];
				foreach ( $values as $val ) {
						$params['facet_value']         = $val['type'];
						$params['facet_display_value'] = ucfirst( $val['type'] );
						$class->insert( $params );
				}
				return false; // skip default indexing
			}
		}

		return $params;
	}

	/**
	 * Filter Sort Options.
	 *
	 * @access public
	 * @param mixed $options
	 * @param mixed $params
	 * @return void
	 */
	public function facetwp_sort_options( $options, $params ) {

		// Labels
		$options['default']['label']    = 'Default (Alphabetical)';
		$options['title_asc']['label']  = 'Name (A-Z)';
		$options['title_desc']['label'] = 'Name (Z-A)';

		// Valid taxes
		$char_taxonomies  = array( 'lez_cliches', 'lez_gender', 'lez_sexuality', 'lez_romantic' );
		$show_taxonomies  = array( 'lez_tropes', 'lez_stations', 'lez_formats', 'lez_genres', 'lez_nations', 'lez_stars', 'lez_triggers', 'lez_intersections' );
		$actor_taxonomies = array( 'lez_actor_gender', 'lez_actor_sexuality' );

		// CHARACTERS
		if ( is_post_type_archive( 'post_type_characters' ) || is_tax( $char_taxonomies ) ) {
			$options['death_desc'] = array(
				'label'      => 'Most Recent Death',
				'query_args' => array(
					'orderby'  => 'meta_value', // sort by numerical custom field
					'meta_key' => 'lezchars_last_death', // required when sorting by custom fields
					'order'    => 'DESC', // descending order
				),
			);
			$options['death_asc']  = array(
				'label'      => 'Oldest Death',
				'query_args' => array(
					'orderby'  => 'meta_value', // sort by numerical custom field
					'meta_key' => 'lezchars_last_death', // required when sorting by custom fields
					'order'    => 'ASC', // ascending order
				),
			);
		}

		// ACTORS
		if ( is_post_type_archive( 'post_type_actors' ) || is_tax( $actor_taxonomies ) ) {
			$options['birth_desc']  = array(
				'label'      => 'Most Recent Birth',
				'query_args' => array(
					'orderby'  => 'meta_value', // sort by numerical custom field
					'meta_key' => 'lezactors_birth', // required when sorting by custom fields
					'order'    => 'DESC', // descending order
				),
			);
			$options['birth_asc']   = array(
				'label'      => 'Oldest Birth',
				'query_args' => array(
					'orderby'  => 'meta_value', // sort by numerical custom field
					'meta_key' => 'lezactors_birth', // required when sorting by custom fields
					'order'    => 'ASC', // ascending order
				),
			);
			$options['death_desc']  = array(
				'label'      => 'Most Recent Death',
				'query_args' => array(
					'orderby'  => 'meta_value', // sort by numerical custom field
					'meta_key' => 'lezactors_death', // required when sorting by custom fields
					'order'    => 'DESC', // descending order
				),
			);
			$options['death_asc']   = array(
				'label'      => 'Oldest Death',
				'query_args' => array(
					'orderby'  => 'meta_value', // sort by numerical custom field
					'meta_key' => 'lezactors_death', // required when sorting by custom fields
					'order'    => 'ASC', // ascending order
				),
			);
			$options['most_chars']  = array(
				'label'      => 'Number of Characters (Descending)',
				'query_args' => array(
					'orderby'  => 'meta_value_num post_title', // sort by numerical custom field
					'meta_key' => 'lezactors_char_count', // required when sorting by custom fields
					'order'    => 'DESC', // descending order
				),
			);
			$options['least_chars'] = array(
				'label'      => 'Number of Characters (Ascending)',
				'query_args' => array(
					'orderby'  => 'meta_value_num post_title', // sort by numerical custom field
					'meta_key' => 'lezactors_char_count', // required when sorting by custom fields
					'order'    => 'ASC', // ascending order
				),
			);
			$options['most_dead']   = array(
				'label'      => 'Number of Dead Characters (Descending)',
				'query_args' => array(
					'orderby'  => 'meta_value_num', // sort by numerical custom field
					'meta_key' => 'lezactors_dead_count', // required when sorting by custom fields
					'order'    => 'DESC', // descending order
				),
			);
			$options['least_dead']  = array(
				'label'      => 'Number of Dead Characters (Ascending)',
				'query_args' => array(
					'orderby'  => 'meta_value_num', // sort by numerical custom field
					'meta_key' => 'lezactors_dead_count', // required when sorting by custom fields
					'order'    => 'ASC', // ascending order
				),
			);
		}

		// SHOWS
		if ( is_post_type_archive( 'post_type_shows' ) || is_tax( $show_taxonomies ) ) {
			$options['most_queers']  = array(
				'label'      => 'Number of Characters (Descending)',
				'query_args' => array(
					'orderby'  => 'meta_value_num', // sort by numerical custom field
					'meta_key' => 'lezshows_char_count', // required when sorting by custom fields
					'order'    => 'DESC', // descending order
				),
			);
			$options['least_queers'] = array(
				'label'      => 'Number of Characters (Ascending)',
				'query_args' => array(
					'orderby'  => 'meta_value_num', // sort by numerical custom field
					'meta_key' => 'lezshows_char_count', // required when sorting by custom fields
					'order'    => 'ASC', // ascending order
				),
			);
			$options['most_dead']    = array(
				'label'      => 'Number of Dead Characters (Descending)',
				'query_args' => array(
					'orderby'  => 'meta_value_num', // sort by numerical custom field
					'meta_key' => 'lezshows_dead_count', // required when sorting by custom fields
					'order'    => 'DESC', // descending order
				),
			);
			$options['least_dead']   = array(
				'label'      => 'Number of Dead Characters (Ascending)',
				'query_args' => array(
					'orderby'  => 'meta_value_num', // sort by numerical custom field
					'meta_key' => 'lezshows_dead_count', // required when sorting by custom fields
					'order'    => 'ASC', // ascending order
				),
			);
			$options['high_score']   = array(
				'label'      => 'Overall Score (Descending)',
				'query_args' => array(
					'orderby'  => 'meta_value_num', // sort by numerical custom field
					'meta_key' => 'lezshows_the_score', // required when sorting by custom fields
					'order'    => 'DESC', // descending order
				),
			);
			$options['low_score']    = array(
				'label'      => 'Overall Score (Ascending)',
				'query_args' => array(
					'orderby'  => 'meta_value_num', // sort by numerical custom field
					'meta_key' => 'lezshows_the_score', // required when sorting by custom fields
					'order'    => 'ASC', // ascending order
				),
			);
		}

		return $options;
	}

}

new LWTV_FacetWP();
