<?php
/**
 * Add support for Symbolicons to CMB2
 *
 * @since 1.0
 */

class LWTV_CMB2_Symbolicons {

	public $icon_taxonomies; // Taxonomies that have an icon
	public $symbolicon_path; // Path to symbolicons

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->icon_taxonomies = array( 'lez_cliches', 'lez_tropes', 'lez_formats', 'lez_genres', 'lez_intersections' );

		// Register metaboxes.
		add_action( 'cmb2_admin_init', array( $this, 'register_taxonomy_metabox' ) );

		// Add all filters and actions to show icons on tax list page
		foreach ( $this->icon_taxonomies as $tax_name ) {
			add_filter( 'manage_edit-' . $tax_name . '_columns', array( $this, 'terms_column_header' ) );
			add_action( 'manage_' . $tax_name . '_custom_column', array( $this, 'terms_column_content' ), 10, 3 );
		}
	}

	/**
	 * Add metabox to custom taxonomies to show icon
	 *
	 * $this->icon_taxonomies       array of taxonomies to show icons on.
	 *
	 * register_taxonomy_metabox()  CMB2 mextabox code
	 * before_field_icon()          Show an icon if that exists
	 *
	 * @param  array                $field_args  Array of field parameters
	 * @param  CMB2_Field object    $field    Field object
	 * @return string - post content
	 */
	public function register_taxonomy_metabox() {
		$prefix = 'lez_termsmeta_';

		$cmb_term_general = \new_cmb2_box(
			array(
				'id'               => $prefix . 'general',
				'title'            => 'General Metabox',  // Does not display on Terms.
				'object_types'     => array( 'term' ),
				'taxonomies'       => array( 'post_tag' ),
				'new_term_section' => true,
			)
		);

		$cmb_term_general->add_field(
			array(
				'name'            => 'Related Actor/Show (Optional)',
				'desc'            => 'If this tag relates to an actor or show, you can link them here.',
				'id'              => $prefix . 'linked_post',
				'type'            => 'post_search_text', // This field type
				'post_type'       => array( 'post_type_shows', 'post_type_actors' ),
				'select_type'     => 'radio',
				'select_behavior' => 'replace',
				'on_front'        => false,
			)
		);

		// If we don't have symbolicons, there's not a reason to register the taxonomy box...
		if ( defined( 'LWTV_SYMBOLICONS_PATH' ) ) {
			$imagepath      = LWTV_SYMBOLICONS_PATH;
			$icon_array     = array();
			$symbolicon_url = admin_url( 'themes.php?page=symbolicons' );

			foreach ( glob( $imagepath . '*' ) as $filename ) {
				$filename                = str_replace( '.svg', '', str_replace( LWTV_SYMBOLICONS_PATH, '', $filename ) );
				$icon_array[ $filename ] = $filename;
			}

			$cmb_term_symbolicons = \new_cmb2_box(
				array(
					'id'               => $prefix . 'edit',
					'title'            => 'Symbolicon Metabox', // Does not display on Terms.
					'object_types'     => array( 'term' ),
					'taxonomies'       => $this->icon_taxonomies,
					'new_term_section' => true,
				)
			);

			$cmb_term_symbolicons->add_field(
				array(
					'name'             => 'Custom Icon',
					'desc'             => 'Select the icon you want to use. Once saved, it will show on the left.<br />If you need help visualizing, check out the <a href=' . $symbolicon_url . '>Symbolicons List</a>.',
					'id'               => $prefix . 'icon',
					'type'             => 'select',
					'show_option_none' => true,
					'default'          => 'custom',
					'options'          => $icon_array,
					'before_field'     => array( $this, 'before_field_icon' ),
				)
			);
		}
	}

	/**
	 * Add before field icon display.
	 */
	public function before_field_icon( $field_args, $field ) {
		$icon = $field->value;

		// Bail early if empty
		if ( empty( $icon ) || ! defined( 'LWTV_SYMBOLICONS_PATH' ) ) {
			return;
		}

		if ( ! file_exists( LWTV_SYMBOLICONS_PATH . $icon . '.svg' ) ) {
			$content = 'N/A';
		} else {
			$filename = LWTV_SYMBOLICONS_URL . $icon . '.svg';
			$content  = '<span class="cmb2-icon" role="img">' . file_get_contents( $filename ) . '</span>';
		}

		return $content;
	}

	/**
	 * Add column header for icon.
	 */
	public function terms_column_header( $columns ) {
		$columns['icon'] = 'Icon';
		return $columns;
	}

	/**
	 * Column content for symbolicons.
	 */
	public function terms_column_content( $value, $content, $term_id ) {
		$icon = get_term_meta( $term_id, 'lez_termsmeta_icon', true );

		// Bail early if empty
		if ( empty( $icon ) || ! defined( 'LWTV_SYMBOLICONS_PATH' ) ) {
			return;
		}

		if ( ! file_exists( LWTV_SYMBOLICONS_PATH . $icon . '.svg' ) ) {
			$content = 'N/A';
		} else {
			$filename = LWTV_SYMBOLICONS_URL . $icon . '.svg';
			$content  = '<span class="cmb2-icon" role="img">' . file_get_contents( $filename ) . '</span>';
		}

		return $content;
	}
}

new LWTV_CMB2_Symbolicons();
