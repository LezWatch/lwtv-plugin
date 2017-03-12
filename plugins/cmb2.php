<?php
/*
Description: Customizations for CMB2
Version: 1.0
Author: Mika Epstein
*/

if ( ! defined('WPINC' ) ) die;

/**
 * class LWTV_CMB2
 *
 * Customize CMB2
 *
 * @since 1.0
 */
class LWTV_CMB2 {

	public $icon_taxonomies;
	public $symbolicon_path;

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'admin_init') );
		add_action( 'cmb2_admin_init', array( $this, 'register_taxonomy_metabox' ) );

		$this->icon_taxonomies = array( 'lez_cliches', 'lez_tropes', 'lez_gender', 'lez_sexuality', 'lez_formats' );
		$this->symbolicon_path = LWTV_SYMBOLICONS_PATH.'/svg/';

		// Add all filters and actions to show icons on tax list page
		foreach ( $this->icon_taxonomies as $tax_name ) {
			add_filter( 'manage_edit-'.$tax_name. '_columns', array( $this, 'terms_column_header' ) );
			add_action( 'manage_'.$tax_name. '_custom_column', array( $this, 'terms_column_content' ), 10, 3 );
		}

		add_filter( 'cmb2_types_esc_date_year_range', array( $this, 'jt_cmb2_esc_date_year_range' ), 10, 4 );
		add_filter( 'cmb2_sanitize_date_year_range', array( $this, 'jt_cmb2_sanitize_date_year_range' ), 10, 5 );
		add_filter( 'cmb2_override_meta_value', array( $this, 'jt_cmb2_date_year_range_get_split_values' ), 10, 4 );
		add_filter( 'cmb2_sanitize_date_year_range', array( $this, 'jt_cmb2_date_year_range_split_values' ), 12, 4 );
		add_filter( 'cmb2_render_date_year_range', array( $this, 'jt_cmb2_date_year_range' ), 10, 5 );
	}

	/**
	 * Init
	 */
	public function admin_init() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts'), 10 );
	}

	/**
	 * Extra Get post options.
	 */
	public static function get_post_options( $query_args ) {
	    $args = wp_parse_args( $query_args, array(
	        'post_type'   => 'post',
	        'numberposts' => wp_count_posts( 'post' )->publish,
	        'post_status' => array('publish'),
	    ) );

	    $posts = get_posts( $args );

	    $post_options = array();
	    if ( $posts ) {
	        foreach ( $posts as $post ) {
	          $post_options[ $post->ID ] = $post->post_title;
	        }
	    }

	    asort($post_options);
	    return $post_options;
	}

	/**
	 * CSS tweaks
	 */
	public function admin_enqueue_scripts( $hook ) {
		wp_register_style( 'cmb-styles', plugins_url('cmb2.css', __FILE__ ) );
		if ( $hook == 'post.php' || $hook == 'post-new.php' || $hook == 'edit-tags.php' || $hook == 'term.php' || $hook == 'page-new.php' || $hook == 'page.php' ) {
			wp_enqueue_style( 'cmb-styles' );
		}
	}

	/**
	 * Add metabox to custom taxonomies to show icon
	 *
	 * $this->icon_taxonomies   array of taxonomies to show icons on.
	 * $this->symbolicon_path   location of Symbolicons
	 *
	 * register_taxonomy_metabox()  CMB2 mextabox code
	 * before_field_icon()          Show an icon if that exists
	 *
	 * @param  array              $field_args  Array of field parameters
	 * @param  CMB2_Field object  $field       Field object
	 */
	public function register_taxonomy_metabox() {
		$prefix = 'lez_termsmeta_';

		$icon_array = array();
		foreach (glob( $this->symbolicon_path.'*.svg' ) as $file) {
			$icon_array[ basename($file, '.svg') ] = basename($file);
		}

		$symbolicon_url = admin_url( 'themes.php?page=symbolicons' );

		$cmb_term = new_cmb2_box( array(
			'id'				=> $prefix . 'edit',
			'title'				=> 'Category Metabox',
			'object_types'		=> array( 'term' ),
			'taxonomies'		=> $this->icon_taxonomies,
			'new_term_section'	=> true,
		) );

		// Only load the icons IF the icon folder is there. This will prevent weird theme switching errors
		if ( file_exists( $this->symbolicon_path ) && is_dir( $this->symbolicon_path ) ) {
			$cmb_term->add_field( array(
				'name'				=> 'Icon',
				'desc'				=> 'Select the icon you want to use. Once saved, it will show on the left.<br />If you need help visualizing, check out the <a href='.$symbolicon_url.'>Symbolicons List</a>.',
				'id'				=> $prefix . 'icon',
			    'type'				=> 'select',
			    'show_option_none'	=> true,
			    'default'			=> 'custom',
			    'options'			=> $icon_array,
				'before_field'		=> array( $this, 'before_field_icon' ),
			) );
		}
	}

	// Add before field icon display
	public function before_field_icon( $field_args, $field ) {
		$icon = $field->value;
		$iconpath = $this->symbolicon_path.$icon.'.svg';
		if ( !empty($icon) || file_exists( $iconpath ) ) {
			echo '<span role="img" class="cmb2-icon">'.file_get_contents( $iconpath ).'</span>';
		}
	}

	// Tax list column header
	public function terms_column_header($columns){
	    $columns['icon'] = 'Icon';
	    return $columns;
	}

	// Tax list column content
	public function terms_column_content($value, $content, $term_id){
		$icon = get_term_meta( $term_id, 'lez_termsmeta_icon', true );
		$iconpath = $this->symbolicon_path.$icon.'.svg';
		if ( empty($icon) || !file_exists( $iconpath ) ) {
			$content = 'N/A';
		} else {
			$content = '<span role="img" class="cmb2-icon">'.file_get_contents($iconpath).'</span>';
		}
	    return $content;
	}

	/**
	 * Render 'date_year_range' custom field type
	 *
	 * @since 0.1.0
	 *
	 * @param array  $field        The passed in `CMB2_Field` object
	 * @param mixed  $value        The value of this field escaped.
	 *                             It defaults to `sanitize_text_field`.
	 *                             If you need the unescaped value, you can access it
	 *                             via `$field->value()`
	 * @param int    $object_id    The ID of the current object
	 * @param string $object_type  The type of object you are working with.
	 *                             Most commonly, `post` (this applies to all post-types),
	 *                             but could also be `comment`, `user` or `options-page`.
	 * @param object $type_object The `CMB2_Types` object
	 */
	public function jt_cmb2_date_year_range( $field, $value, $object_id, $object_type, $type_object ) {
		$earliest = $field->options( 'earliest' );
		$earliest = $earliest ? absint( $earliest ) : 1900;

		$start_reverse_sort = $field->options( 'start_reverse_sort' );
		$start_reverse_sort = $start_reverse_sort ? true : false;

		$finish_reverse_sort = $field->options( 'finish_reverse_sort' );
		$finish_reverse_sort = $finish_reverse_sort ? true : false;

		$value = wp_parse_args( $value, array(
			'start'  => '',
			'finish' => '',
		) );

		$desc = $field->args( 'description' );
		$field->args['description'] = '';
		$type_object->type = new CMB2_Type_Select( $type_object );

		echo '<em>'. $type_object->_text( 'start_label', 'Starting Year' ) . '</em> ';

		$start_options = $this->jt_cmb2_date_year_range_options( $type_object, $earliest, $value['start'], $start_reverse_sort );
		echo $type_object->select( array(
			'name'    => $type_object->_name( '[start]' ),
			'id'      => $type_object->_id( '_start' ),
			'value'   => $value['start'],
			'class'   => 'cmb2_select cmb2-year-range-start',
			'options' => $start_options,
			'desc'    => '',
		) );

		echo $type_object->_text( 'separator', ' &mdash; ' );

		$end_options = $this->jt_cmb2_date_year_range_options( $type_object, $earliest, $value['finish'], $finish_reverse_sort  );
		echo $type_object->select( array(
			'name'    => $type_object->_name( '[finish]' ),
			'id'      => $type_object->_id( '_finish' ),
			'value'   => $value['finish'],
			'class'   => 'cmb2_select cmb2-year-range-end',
			'options' => $end_options,
			'desc'    => '',
		) );
		echo ' <em>'. $type_object->_text( 'finish_label', 'Final Year' ) . '</em>';

		$field->args['description'] = $desc;

		$type_object->_desc( true, true );

		add_action( is_admin() ? 'admin_footer' : 'wp_footer', array( $this, 'jt_cmb2_date_year_range_js' ) );

	}

	public function jt_cmb2_date_year_range_js() {
		static $done = false;
		if ( ! $done ) {
			$done = true;
		}
		?>
		<script type="text/javascript">
			jQuery( function( $ ) {
				var $options = {};

				function makeSelected( $newoptions, selected ) {
					var $selected = $newoptions.filter( '[value="'+ selected +'"]' );

					if ( ! $selected.length ) {
						$selected = $newoptions.filter( '[value=""]' );
					}

					// Clear out previous selected
					$newoptions.filter( ':selected' ).prop( 'selected', false );

					$selected.prop( 'selected', true );

					return $newoptions;
				}

				function getNewOptions( $options, start, selected ) {
					if ( start ) {
						$options = $options.filter( function() {
							var val = $( this ).val();
							return'current' === start ? 'current' === val || '' === val : ( val >= start || ! val );
						} );
					}

					return makeSelected( $options.clone(), selected );
				}

				$( document.body ).on( 'change', '.cmb2-year-range-start', function() {
					var $this        = $( this );
					var start        = $this.find( ':selected' ).val();
					var $endPicker   = $this.parent().find( '.cmb2-year-range-end' );
					var id           = $endPicker.attr( 'id' );
					var selectedEnd  = $endPicker.find( ':selected' ).val();

					if ( ! $options[ id ] ) {
						// Store a cached version of the unmodified options.
						$options[ id ] = $endPicker.find( 'option' ).clone();
					}

					$endPicker.html( getNewOptions( $options[ id ], start, selectedEnd ) );
				} );

				// Kick it off.
				$( '.cmb2-year-range-start' ).trigger( 'change' );
			});
		</script>
		<?php
	}

	public function jt_cmb2_date_year_range_options( $type_object, $earliest, $value, $reverse = false ) {
		$options = array();

		$not_set = array(
			'value' => '',
			'label' => __( 'Not Set' ),
		);

		if ( cmb2_utils()->isempty( $value ) ) {
			$not_set['checked'] = 'checked';
		}

		for ( $i = $earliest; $i <= date( 'Y' ); $i++ ) {

			$a = array( 'value' => $i, 'label' => $i );
			if ( absint( $value ) === $i ) {
				$a['checked'] = 'checked';
			}

			$options[] = $a;
		}

		$a = array(
			'value' => 'current',
			'label' => __( 'Current' ),
		);

		if ( 'current' === $value ) {
			$a['checked'] = 'checked';
		}

		$options[] = $a;

		if ( $reverse ) {
			$options = array_reverse( $options );
		}

		array_unshift( $options, $not_set );

		return implode( "\n", array_map( array( $type_object, 'select_option' ), $options ) );
	}

	/**
	 * Optionally save the values into separate fields.
	 */
	public function jt_cmb2_date_year_range_split_values( $override_value, $value, $object_id, $field_args ) {
		if ( ! isset( $field_args['split_values'] ) || ! $field_args['split_values'] ) {
			// Don't do the override
			return $override_value;
		}

		$keys = array( 'start', 'finish' );

		foreach ( $keys as $key ) {
			if ( ! empty( $value[ $key ] ) ) {
				update_post_meta( $object_id, $field_args['id'] . '_'. $key, $value[ $key ] );
			}
		}

		// Tell CMB2 we already did the update
		return true;
	}

	/**
	 * Optionally fetch the values from separate fields as well.
	 */
	public function jt_cmb2_date_year_range_get_split_values( $no_override, $object_id, $args, $field ) {
		if ( 'date_year_range' !== $field->args( 'type' ) || ! $field->args( 'split_values' ) ) {
			return $no_override;
		}

		$value = array(
			'start'  => get_post_meta( $object_id, $args['field_id'] . '_start', 1 ),
			'finish' => get_post_meta( $object_id, $args['field_id'] . '_finish', 1 ),
		);

		return $value;
	}

	/**
	 * The following snippets are required for allowing the date_year_range field
	 * to work as a repeatable field, or in a repeatable group
	 */

	public function jt_cmb2_sanitize_date_year_range( $check, $meta_value, $object_id, $field_args, $sanitizer ) {

		// if not repeatable, bail out.
		if ( ! is_array( $meta_value ) || ! $field_args['repeatable'] ) {
			return $check;
		}

		foreach ( $meta_value as $key => $val ) {
			$meta_value[ $key ] = array_filter( array_map( 'sanitize_text_field', $val ) );
		}

		return array_filter( $meta_value );
	}

	public function jt_cmb2_esc_date_year_range( $check, $meta_value, $field_args, $field_object ) {

		// if not repeatable, bail out.
		if ( ! is_array( $meta_value ) || ! $field_args['repeatable'] ) {
			return $check;
		}

		foreach ( $meta_value as $key => $val ) {
			$meta_value[ $key ] = array_filter( array_map( 'esc_attr', $val ) );
		}

		return array_filter( $meta_value );
	}
}
new LWTV_CMB2();
