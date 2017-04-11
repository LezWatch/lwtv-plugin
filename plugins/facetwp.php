<?php
/*
Description:FacetWP Customizations
Version: 1.1
Author: Mika Epstein
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

		// Filter paged output
		add_filter( 'facetwp_pager_html', array( $this, 'facetwp_pager_html' ), 10, 2 );

		// Filter data before saving it
		add_filter( 'facetwp_index_row', array( $this, 'facetwp_index_row' ), 10, 2 );

		// Filter results count
		add_filter( 'facetwp_result_count', function( $output, $params ) {
		    $output = $params['total'];
		    return $output;
		}, 10, 2 );

		// Filter Facet output
		add_filter( 'facetwp_facet_html', function( $output, $params ) {
		    if ( 'show_airdates' == $params['facet']['name'] ) {
		        $output = str_replace( 'Min', 'First Year', $output );
		        $output = str_replace( 'Max', 'Last Year', $output );
		    }
		    return $output;
		}, 10, 2 );

	}

	/**
	 * Only show pagination if there's more than one page
	 * Credit: https://gist.github.com/mgibbs189/69176ef41fa4e26d1419
	 */
	public function facetwp_pager_html( $output, $params ) {

	    $output = '';
	    $page = (int) $params['page'];
	    $total_pages = (int) $params['total_pages'];

	    // Only show pagination when > 1 page
	    if ( 1 < $total_pages ) {

	        if ( 1 < $page ) {
	            $output .= '<a class="facetwp-page" data-page="' . ( $page - 1 ) . '">&laquo; Previous</a>';
	        }
	        if ( 3 < $page ) {
	            $output .= '<a class="facetwp-page first-page" data-page="1">1</a>';
	            $output .= ' <span class="dots">…</span> ';
	        }
	        for ( $i = 2; $i > 0; $i-- ) {
	            if ( 0 < ( $page - $i ) ) {
	                $output .= '<a class="facetwp-page" data-page="' . ($page - $i) . '">' . ($page - $i) . '</a>';
	            }
	        }

	        // Current page
	        $output .= '<a class="facetwp-page active" data-page="' . $page . '">' . $page . '</a>';

	        for ( $i = 1; $i <= 2; $i++ ) {
	            if ( $total_pages >= ( $page + $i ) ) {
	                $output .= '<a class="facetwp-page" data-page="' . ($page + $i) . '">' . ($page + $i) . '</a>';
	            }
	        }
	        if ( $total_pages > ( $page + 2 ) ) {
	            $output .= ' <span class="dots">…</span> ';
	            $output .= '<a class="facetwp-page last-page" data-page="' . $total_pages . '">' . $total_pages . '</a>';
	        }
	        if ( $page < $total_pages ) {
	            $output .= '<a class="facetwp-page" data-page="' . ( $page + 1 ) . '">Next &raquo;</a>';
	        }
	    }

	    return $output;
	}

	/**
	 * Filter Data before it's saved
	 * Useful for serialized data but also capitalizing stars
	 *
	 * @since 1.1
	 */
	function facetwp_index_row( $params, $class ) {

		// Stars
		// Capitalize
		if ( 'show_stars' == $params['facet_name'] ) {
			$params['facet_value'] = $params['facet_value'];
			$params['facet_display_value'] = ucfirst( $params['facet_display_value'] );
			$class->insert( $params );
			return false; // skip default indexing
	    }

		// Actors
		// Saves one value for each actor
		// a:1:{i:0;s:13:"Rachel Bilson";}
		if ( 'char_actors' == $params['facet_name'] ) {
			$values = (array) $params['facet_value'];
			foreach ( $values as $val ) {
				$params['facet_value'] = $val;
				$params['facet_display_value'] = $val;
				$class->insert( $params );
			}
			return false; // skip default indexing
	    }

		// Airdates
		// Saves two values for two sources (dude)
		// a:2:{s:5:"start";s:4:"1994";s:6:"finish";s:4:"2009";}
		if ( 'show_airdates' == $params['facet_name'] ) {
			$values = (array) $params['facet_value'];

			$start = ( isset( $values['start'] ) )? $values['start'] : '';
			$end   = ( isset( $values['finish'] ) && is_int( $values['finish'] ) )? $values['finish'] : date( 'Y' );

			$params['facet_value']         = $start;
			$params['facet_display_value'] = $start;
			$class->insert( $params );

			$params2 = $params;
			$params2['facet_value']         = $end;
			$params2['facet_display_value'] = $end;
			$class->insert( $params2 );

			return false; // skip default indexing
	    }

	    return $params;
	}
}

new LWTV_FacetWP();

/**
 * class FacetWP_Integration_CMB2
 *
 * Integrate FacetWP with CMB2
 * Source: https://github.com/FacetWP/facetwp-cmb2
 * @since 1.0
 */
class FacetWP_Integration_CMB2 {

	/**
	 * @var FacetWP_Integration_CMB2
	 */
	protected static $instance = null;

	protected function __construct() {}

	/**
	 * @return FacetWP_Integration_CMB2
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Hook class methods to FacetWP hooks.
	 *
	 * @since 1.0.0
	 */
	public function setup_hooks() {

		// If we don't have CMB2, then there's nothing to do.
		if ( ! defined( 'CMB2_LOADED' ) ) {
			return;
		}

		// Add CMB2 fields to the Data Sources dropdown
		add_filter( 'facetwp_facet_sources', array( $this, 'facet_sources' ) );

		// CMB2 field handler
		add_filter( 'facetwp_indexer_post_facet', array( $this, 'indexer_post_facet' ), 10, 2 );

		// Text fields that should be indexed
		add_filter( 'facetwp_cmb2_skip_index_text', array( $this, 'text_field_exceptions' ), 10, 2 );

		// Special handling for time/date fields
		add_filter( 'facetwp_cmb2_default_index', array( $this, 'time_date_indexing' ), 10, 3 );
	}


	/**
	 * Add CMB2 fields to the Data Sources dropdown.
	 *
	 * @since 1.0.0
	 *
	 * @param array $sources The current set of data sources.
	 * @return array The updated set of data sources.
	 */
	public function facet_sources( $sources ) {
		if ( ! defined( 'CMB2_LOADED' ) ) {
			return $sources;
		}

		$sources['cmb2'] = array(
			'label'   => 'CMB2',
			'choices' => array(),
		);

		// Get every CMB2 registered field as an array
		$fields = $this->get_cmb_fields();
		foreach ( $fields as $field ) {
			// The Field ID string is used later to determine the metabox and field ID
			$field_id_string = "cmb2/{$field['metabox_id']}/{$field['id']}";

			$sources['cmb2']['choices'][ $field_id_string ] = $field['label'];
		}

		return $sources;
	}

	/**
	 * Index CMB2 field data
	 *
	 * @param bool $return
	 * @param array $params
	 *
	 * @return bool
	 */
	public function indexer_post_facet( $return, $params ) {
		$defaults = $params['defaults'];
		$facet    = $params['facet'];

		/**
		 * Filter to enable debugging.
		 *
		 * @since 1.0.0
		 *
		 * @param bool $debugging Whether to enable debugging. Defaults to false./
		 */
		if ( apply_filters( 'facetwp_cmb2_debugging', false ) ) {
			error_log( "Param info: " . print_r( $params, true ) . PHP_EOL, 3, WP_CONTENT_DIR . '/facet.log' );
		}

		// Split up the facet source
		$source = explode( '/', $facet['source'] );

		// Maybe return early. Includes class check, just in case
		if ( 'cmb2' !== $source[0] || count( $source ) < 3 || ! class_exists( 'CMB2_boxes', false ) ) {
			return $return;
		}

		// Initial var setup
		$metabox_id = $source[1];
		$field_id   = $source[2];

		// Make sure we can retrieve the Metabox
		$cmb = CMB2_Boxes::get( $metabox_id );
		if ( ! $cmb ) {
			return $return;
		}

		// Make sure the field can be retrieved.
		$field = $cmb->get_field( $field_id );
		if ( ! $field ) {
			return $return;
		}

		$field_type = $field->type();

		/**
		 * Filter the CMB2 field types that do not need to be indexed by FacetWP.
		 *
		 * @since 1.0.0
		 *
		 * @param array $fields Array of field types that do not need to be indexed.
		 */
		$skip_index = apply_filters( 'facetwp_cmb2_skip_index', array( 'title', 'group' ) );
		if ( in_array( $field->type(), $skip_index ) ) {
			return true;
		}

		/**
		 * Filter to skip indexing text fields.
		 *
		 * By default, skip indexing text fields, because data is likely to be unique. This filter provides access
		 * to the field type, so that more granular control can be achieved.
		 *
		 * @since 1.0.0
		 *
		 * @param bool   $skip       Whether to skip indexing text fields. Default: true.
		 * @param string $field_type The field type.
		 */
		$skip_index_text = apply_filters( 'facetwp_cmb2_skip_index_text', true, $field_type );
		if ( false !== strpos( $field->type(), 'text' ) && $skip_index_text ) {
			return false;
		}

		/**
		 * Filter to skip indexing WYSIWYG fields.
		 *
		 * Similar to text fields, skip indexing by default because data is likely to be unique.
		 *
		 * @since 1.0.0
		 *
		 * @param bool $skip Whether to skip indexing WYSIWYG fields.
		 */
		$skip_index_wysiwyg = apply_filters( 'facetwp_cmb2_skip_index_wysiwyg', true );
		if ( 'wysiwyg' == $field_type && $skip_index_wysiwyg ) {
			return false;
		}

		// Checkboxes are either on or off. Only index the "on" value.
		if ( 'checkbox' == $field_type ) {
			if ( 'on' == $field->value() ) {
				$this->index_field( $field, $defaults );
			} else {
				return true;
			}
		}

		/**
		 * Filter whether to do the default indexing.
		 *
		 * @since 1.0.0
		 *
		 * @param bool                     $index    Whether to use the default indexing.
		 * @param CMB2_Field               $field    The CMB2_Field object.
		 * @param array                    $defaults The array of defaults.
		 * @param FacetWP_Integration_CMB2 $obj      The current class object.
		 */
		$default_index = apply_filters( 'facetwp_cmb2_default_index', true, $field, $defaults, $this );
		if ( $default_index ) {
			$this->index_field_values( $field, $defaults );
		}

		return true;
	}

	/**
	 * Index a field based on the field object settings.
	 *
	 * @since 1.0.0
	 *
	 * @param CMB2_Field $field    Field object.
	 * @param array      $defaults Array of default values.
	 */
	public function index_field( $field, $defaults ) {
		$index = array(
			'facet_value'         => $field->args( 'name' ),
			'facet_display_value' => $field->args( 'desc' ) ?: $field->args( 'name' ),
		);
		$this->index_row( $index, $defaults );
	}

	/**
	 * Index a field based on the stored value(s).
	 *
	 * @since 1.0.0
	 *
	 * @param CMB2_Field $field    Field object.
	 * @param array      $defaults Array of default values.
	 */
	public function index_field_values( $field, $defaults ) {
		$index  = array();
		$values = (array) $field->escaped_value();

		foreach ( $values as $value ) {
			if ( empty( $value ) ) {
				continue;
			}
			$index[] = array(
				'facet_value'         => $value,
				'facet_display_value' => $field->args( 'name' ),
			);
		}
		$this->index_multiple( $index, $defaults );
	}

	/**
	 * Index a single value.
	 *
	 * @since 1.0.0
	 *
	 * @param array $value    The array of values to index. Available keys in the array are: post_id, facet_name,
	 *                        facet_source, facet_value, facet_display_value, term_id, parent_id, and depth. For more
	 *                        information, see @link https://facetwp.com/documentation/facetwp_index_row/
	 * @param array $defaults Default values to use when indexing.
	 */
	public function index_row( $value, $defaults ) {
		FWP()->indexer->index_row( wp_parse_args( $value, $defaults ) );
	}

	/**
	 * Helper function to index an array of values.
	 *
	 * @since 1.0.0
	 *
	 * @param array $values   Multidimensional array of values to index.
	 * @param array $defaults Default values to use when indexing.
	 */
	public function index_multiple( $values, $defaults ) {
		// Loop through each value and index it
		foreach ( $values as $value ) {
			$this->index_row( $value, $defaults );
		}
	}

	/**
	 * Filter the text fields that should be skipped.
	 *
	 * This also serves as an example for how to use the 'facetwp_cmb2_skip_index_text' filter.
	 *
	 * @since 1.0.0
	 *
	 * @param bool   $index      Whether to skip indexing this field.
	 * @param string $field_type The type of field.
	 *
	 * @return bool
	 */
	public function text_field_exceptions( $index, $field_type ) {
		$exception = array(
			'text_date',
			'text_time',
			'text_date_timestamp',
			'text_datetime_timestamp',
			'text_datetime_timestamp_timezone',
		);

		if ( in_array( $field_type, $exception ) ) {
			return false;
		}

		return $index;
	}

	/**
	 * Handle indexing the various date/time fields.
	 *
	 * This method also serves as an example of the 'facetwp_cmb2_default_index' filter, although it should be noted
	 * that since it is part of the class, it does not use the $obj class object, but makes method calls directly.
	 * For use outside of this class, be sure to use $obj->index_row() or $obj->index_multiple().
	 *
	 * @since 1.0.0
	 *
	 * @param bool       $filter   Continue with normal indexing.
	 * @param CMB2_Field $field    The field object.
	 * @param array      $defaults Array of default data.
	 *
	 * @return bool Whether to continue with the normal indexing.
	 */
	public function time_date_indexing( $filter, $field, $defaults ) {
		$date_format = 'Y-m-d';
		$extended_format = "{$date_format} H:i:s";
		$index = array(
			'facet_display_value' => $field->args( 'name' ),
		);

		// Check for special field types
		if ( 'text_data' == $field->type() ) {
			$index['facet_value'] = date( $date_format, strtotime( $field->value() ) );
			$this->index_row( $index, $defaults );

			return false;
		} elseif ( 'text_date_timestamp' == $field->type() ) {
			$index['facet_value'] = date( $date_format, $field->value() );
			$this->index_row( $index, $defaults );

			return false;
		} elseif ( 'text_datetime_timestamp' == $field->type() ) {
			$index['facet_value'] = date( $extended_format, $field->value() );
			$this->index_row( $index, $defaults );

			return false;
		} elseif ( 'text_datetime_timestamp_timezone' == $field->type() ) {
			$value = maybe_unserialize( $field->value() );
			if ( $value instanceof DateTime ) {
				$index['facet_value'] = $value->format( $extended_format );
				$this->index_row( $index, $defaults );

				return false;
			}
		}

		return $filter;
	}

	/**
	 * Get registered CMB2 fields.
	 *
	 * @return array Multidimensional array of field data. Each array item contains 'id', 'label',
	 *               and 'metabox_id' keys.
	 */
	protected function get_cmb_fields() {
		$return = array();
		if ( ! class_exists( 'CMB2_Boxes', false ) ) {
			return $return;
		}

		$boxes  = CMB2_Boxes::get_all();
		foreach ( $boxes as $cmb ) {
			// Secret override method to skip indexing a metabox's fields
			if ( $cmb->prop( 'no_facetwp_index', false ) ) {
				continue;
			}

			/**
			 * Filter to skip metaboxes with no default hookup.
			 *
			 * Typically "hookup" => false is used by metaboxes that are on option pages, or are dispalyed on the front
			 * end.
			 *
			 * @since 1.0.0
			 *
			 * @param bool $skip_false_hookup Whether to skip metaboxes with hookup => false.
			 */
			$skip_false_hookup = apply_filters( 'facetwp_cmb2_skip_false_hookup', true );
			if ( $skip_false_hookup && false === $cmb->prop( 'hookup' ) ) {
				continue;
			}

			$fields = $cmb->prop( 'fields', array() );

			foreach ( $fields as $field ) {

				/**
				 * Filter to skip indexing hidden fields.
				 *
				 * @since 1.0.0
				 *
				 * @param bool $skip_hidden_fields Whether to skip indexing hidden fields.
				 */
				$skip_hidden_fields = apply_filters( 'facetwp_cmb2_skip_hidden_fields', true );
				if ( $skip_hidden_fields && 'hidden' == $field['type'] ) {
					continue;
				}

				$return[] = array(
					'id'         => $field['id'],
					'label'      => isset( $field['name'] ) ? $field['name'] : $field['id'],
					'metabox_id' => $cmb->cmb_id,
				);
			}
		}

		return $return;
	}
}

$instance = FacetWP_Integration_CMB2::instance();
add_action( 'plugins_loaded', array( $instance, 'setup_hooks' ) );