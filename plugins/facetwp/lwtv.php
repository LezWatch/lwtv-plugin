<?php
/*
Description: FacetWP Customizations
Version: 1.1
*/

if ( ! defined('WPINC' ) ) die;

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
			if ( 'show_airdates' == $params['facet']['name'] ) {
				$output = str_replace( 'Min', 'First Year', $output );
				$output = str_replace( 'Max', 'Last Year', $output );
			}
			return $output;
		}, 10, 2 );

		if ( is_admin() ) {
			// Don't output <!--fwp-loop--> on admin pages
			add_filter( 'facetwp_is_main_query', function( $is_main_query, $query ) { return false; }, 10, 2 );
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
	function facetwp_index_row( $params, $class ) {

		// Actors
		if ( get_post_type( $params['post_id'] ) == 'post_type_shows' ) {
			// Stars
			// Capitalize
			if ( 'show_stars' == $params['facet_name'] ) {
				$params['facet_value']         = $params['facet_value'];
				$params['facet_display_value'] = ucfirst( $params['facet_display_value'] );
				$class->insert( $params );
				return false; // skip default indexing
			}
			// Shows we Love
			// Change 'on' to 'yes' 
			if ( 'show_loved' == $params['facet_name'] ) {
				$params['facet_value']         = $params['facet_value'];
				$params['facet_display_value'] = ( $params['facet_display_value'] == 'on' )? 'Yes' : 'No';
				$class->insert( $params );
				return false; // skip default indexing
			}
			// Trigger Warning
			// If 'on' change to 'High', else capitalize
			if ( 'show_trigger_warning' == $params['facet_name'] ) {
				$params['facet_value']         = ( $params['facet_display_value'] == 'on' )? 'high' : $params['facet_display_value'];
				$params['facet_display_value'] = ( $params['facet_display_value'] == 'on' )? 'High' : ucfirst( $params['facet_display_value'] );
				$class->insert( $params );
				return false; // skip default indexing
			}

			// Airdates
			// Saves two values for two sources (dude)
			// a:2:{s:5:"start";s:4:"1994";s:6:"finish";s:4:"2009";}
			if ( 'show_airdates' == $params['facet_name'] ) {
				$values = (array) $params['facet_value'];
				$start  = ( isset( $values['start'] ) )? $values['start'] : '';
				$end    = ( isset( $values['finish'] ) && is_int( $values['finish'] ) )? $values['finish'] : date( 'Y' );
				$params['facet_value']         = $start;
				$params['facet_display_value'] = $start;
				$class->insert( $params );
				$params2 = $params;
				$params2['facet_value']         = $end;
				$params2['facet_display_value'] = $end;
				$class->insert( $params2 );
				return false; // skip default indexing
			}
			// Shows by Gender, Sexuality, Romance
			// If they have a count higher than 0, we flag them.
			$show_char_array = array( 'show_char_gender', 'show_char_sexuality', 'show_char_romance' );
			if ( in_array( $params['facet_name'], $show_char_array ) ) {
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
		}

		// Actors
		if ( get_post_type( $params['post_id'] ) == 'post_type_actors' ) {
			// Is Queer
			// Change 'on' to 'yes' 
			if ( 'is_queer' == $params['facet_name'] ) {
				$params['facet_value']         = ( $params['facet_value'] == '1' )? 'yes' : 'no';
				$params['facet_display_value'] = ( $params['facet_display_value'] == '1' )? 'Yes' : 'No';
				$class->insert( $params );
				return false; // skip default indexing
			}
		}

		// Characters
		if ( get_post_type( $params['post_id'] ) == 'post_type_characters' ) {
			// Actors
			// Saves one value for each actor
			// a:1:{i:0;s:13:"Rachel Bilson";}
			if ( 'char_actors' == $params['facet_name'] ) {
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
			if ( 'char_shows' == $params['facet_name'] ) {
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
			if ( 'char_roles' == $params['facet_name'] ) {
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
	function facetwp_sort_options( $options, $params ) {

		$options['default']['label']    = 'Default (Alphabetical)';
		$options['title_asc']['label']  = 'Name (A-Z)';
		$options['title_desc']['label'] = 'Name (Z-A)';

		if ( is_post_type_archive( 'post_type_actors' ) ) {
			$options['most_chars'] = array(
				'label' => 'Number of Characters (Descending)',
				'query_args' => array(
					'orderby'  => 'meta_value_num post_title', // sort by numerical custom field
					'meta_key' => 'lezactors_char_count', // required when sorting by custom fields
					'order'    => 'DESC', // descending order
				)
			);
			$options['least_chars'] = array(
				'label' => 'Number of Characters (Ascending)',
				'query_args' => array(
					'orderby'  => 'meta_value_num post_title', // sort by numerical custom field
					'meta_key' => 'lezactors_char_count', // required when sorting by custom fields
					'order'    => 'ASC', // ascending order
				)
			);

			$options['most_dead'] = array(
				'label' => 'Number of Dead Characters (Descending)',
				'query_args' => array(
					'orderby'  => 'meta_value_num', // sort by numerical custom field
					'meta_key' => 'lezactors_dead_count', // required when sorting by custom fields
					'order'    => 'DESC', // descending order
				)
			);

			$options['least_dead'] = array(
				'label' => 'Number of Dead Characters (Ascending)',
				'query_args' => array(
					'orderby'  => 'meta_value_num', // sort by numerical custom field
					'meta_key' => 'lezactors_dead_count', // required when sorting by custom fields
					'order'    => 'ASC', // ascending order
				)
			);
		}

		if ( is_post_type_archive( 'post_type_shows' ) ) {
			$options['most_queers'] = array(
				'label' => 'Number of Characters (Descending)',
				'query_args' => array(
					'orderby'  => 'meta_value_num', // sort by numerical custom field
					'meta_key' => 'lezshows_char_count', // required when sorting by custom fields
					'order'    => 'DESC', // descending order
				)
			);

			$options['least_queers'] = array(
				'label' => 'Number of Characters (Ascending)',
				'query_args' => array(
					'orderby'  => 'meta_value_num', // sort by numerical custom field
					'meta_key' => 'lezshows_char_count', // required when sorting by custom fields
					'order'    => 'ASC', // ascending order
				)
			);

			$options['most_dead'] = array(
				'label' => 'Number of Dead Characters (Descending)',
				'query_args' => array(
					'orderby'  => 'meta_value_num', // sort by numerical custom field
					'meta_key' => 'lezshows_dead_count', // required when sorting by custom fields
					'order'    => 'DESC', // descending order
				)
			);

			$options['least_dead'] = array(
				'label' => 'Number of Dead Characters (Ascending)',
				'query_args' => array(
					'orderby'  => 'meta_value_num', // sort by numerical custom field
					'meta_key' => 'lezshows_dead_count', // required when sorting by custom fields
					'order'    => 'ASC', // ascending order
				)
			);

			$options['high_score'] = array(
				'label' => 'Overall Score (Descending)',
				'query_args' => array(
					'orderby'  => 'meta_value_num', // sort by numerical custom field
					'meta_key' => 'lezshows_the_score', // required when sorting by custom fields
					'order'    => 'DESC', // descending order
				)
			);

			$options['low_score'] = array(
				'label' => 'Overall Score (Ascending)',
				'query_args' => array(
					'orderby'  => 'meta_value_num', // sort by numerical custom field
					'meta_key' => 'lezshows_the_score', // required when sorting by custom fields
					'order'    => 'ASC', // ascending order
				)
			);

		}

		return $options;
	}

}

new LWTV_FacetWP();