<?php
/**
 * Name: CMB2 Metaboxes
 */

namespace LWTV\CPTs\Characters;

use LWTV\CPTs\Shows;

class CMB2_Metaboxes {
	public $character_roles;
	public $years_array;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->character_roles = array(
			'regular'   => 'Regular/Main Character',
			'recurring' => 'Recurring Character',
			'guest'     => 'Guest Character',
		);

		$all_years   = range( LWTV_FIRST_YEAR, gmdate( 'Y' ) + 1 );
		$years_array = array();
		foreach ( $all_years as $a_year ) {
			$years_array[ $a_year ] = $a_year;
		}
		$this->years_array = array_reverse( $years_array, true );

		add_action( 'cmb2_init', array( $this, 'cmb2_metaboxes' ) );
		add_action( 'admin_menu', array( $this, 'remove_metaboxes' ) );
	}

	/*
	 * CMB2 Metaboxes
	 */
	public function cmb2_metaboxes() {
		// prefix for all custom fields
		$prefix = 'lezchars_';

		// @codingStandardsIgnoreStart
		// Get the post ID
		$post_id = null;
		if ( isset( $_GET['post'] ) ) {
			$post_id = (int) $_GET['post'];
		} elseif ( isset( $_POST['post_ID'] ) ) {
			$post_id = (int) $_POST['post_ID'];
		}
		// @codingStandardsIgnoreEnd

		// MetaBox Group: Character Top Grid
		$cmb_char_grid = \new_cmb2_box(
			array(
				'id'           => 'chars_metabox_grid',
				'title'        => 'Character Sexuality and Orientation',
				'object_types' => array( 'post_type_characters' ),
				'context'      => 'normal',
				'priority'     => 'high',
				'show_in_rest' => true,
				'show_names'   => true, // Show field names on the left
			)
		);
		// Field: Character Gender Identity
		$field_gender = $cmb_char_grid->add_field(
			array(
				'name'             => 'Gender',
				'desc'             => 'Gender identity',
				'id'               => $prefix . 'gender',
				'taxonomy'         => 'lez_gender',
				'type'             => 'taxonomy_select',
				'default'          => 'cisgender',
				'show_option_none' => false,
				'remove_default'   => 'true',
			)
		);
		// Field: Character Sexual Orientation
		$field_sexuality = $cmb_char_grid->add_field(
			array(
				'name'             => 'Sexuality',
				'desc'             => 'Sexual orientation',
				'id'               => $prefix . 'sexuality',
				'taxonomy'         => 'lez_sexuality',
				'type'             => 'taxonomy_select',
				'default'          => 'homosexual',
				'show_option_none' => false,
				'remove_default'   => 'true',
			)
		);
		// Field: Character Romantic Orientation
		$field_romantic = $cmb_char_grid->add_field(
			array(
				'name'             => 'Romantic',
				'desc'             => 'Romantic orientation',
				'id'               => $prefix . 'romantic',
				'taxonomy'         => 'lez_romantic',
				'type'             => 'taxonomy_select',
				'default'          => 'none',
				'show_option_none' => true,
				'remove_default'   => 'true',
			)
		);
		// Character Sidebar Grid
		if ( ! is_admin() ) {
			return;
		} else {
			$grid_char = new \Forked\CMB2\CMB2Grid\Grid\Cmb2Grid( $cmb_char_grid );
			$row1      = $grid_char->addRow();
			$row2      = $grid_char->addRow();
			$row1->addColumns( array( $field_gender, $field_sexuality ) );
			$row2->addColumns( array( $field_romantic ) );
		}

		// MetaBox Group: Character Main Data
		$cmb_characters = \new_cmb2_box(
			array(
				'id'           => 'chars_metabox_main',
				'title'        => 'General Character Details',
				'object_types' => array( 'post_type_characters' ),
				'context'      => 'normal',
				'priority'     => 'high',
				'show_in_rest' => true,
				'show_names'   => true, // Show field names on the left
			)
		);
		// Field: Character Clichés
		$cmb_characters->add_field(
			array(
				'name'              => 'Character Clichés',
				'id'                => $prefix . 'cliches',
				'taxonomy'          => 'lez_cliches',
				'type'              => 'pw_multiselect',
				'select_all_button' => false,
				'remove_default'    => 'true',
				'options'           => lwtv_plugin()->get_cmb2_terms_list( 'lez_cliches' ),
				'default'           => lwtv_plugin()->get_select2_defaults( 'lezchars_cliches', 'lez_cliches', $post_id, true ),
				'attributes'        => array(
					'placeholder' => 'Common clichés ...',
				),
			)
		);
		// Field: Year of Death (if applicable)
		$cmb_characters->add_field(
			array(
				'name'        => 'Date of Death',
				'desc'        => 'If the character is dead, select when they died.',
				'id'          => $prefix . 'death_year',
				'type'        => 'text_date',
				'date_format' => 'Y-m-d',
				'repeatable'  => true,
			)
		);
		// Field: Actor Name(s)
		$cmb_characters->add_field(
			array(
				'name'    => 'Actor Name(s)',
				'id'      => $prefix . 'actor',
				'desc'    => 'Drag actors from the left column to the right column to attach them to this page. You can rearrange the order of the posts in the right column by dragging and dropping. The most recent actor should be on top (so \'Caity Lotz\' for Sara Lance).',
				'type'    => 'custom_attached_posts',
				'options' => array(
					'query_args' => array(
						'posts_per_page' => 5,
						'post_type'      => 'post_type_actors',
					), // override the get_posts args
				),
			)
		);

		// Field Group: Alternate Character Images
		$group_alt_images = $cmb_characters->add_field(
			array(
				'id'         => $prefix . 'character_image_group',
				'type'       => 'group',
				'repeatable' => true,
				'options'    => array(
					'group_title'   => 'Alternate Images',
					'add_button'    => 'Add Another Image',
					'remove_button' => 'Remove Image',
					'sortable'      => true,
				),
			)
		);
		// Field: Source Name
		$cmb_characters->add_group_field(
			$group_alt_images,
			array(
				'name' => 'From ...',
				'desc' => 'Where is the image from (i.e. "Crossover", "Flashback", "Cartoon", "Live Action" etc...)',
				'id'   => 'alt_image_text',
				'type' => 'text_small',
			)
		);
		// Field: Image
		$cmb_characters->add_group_field(
			$group_alt_images,
			array(
				'name'         => 'Image File',
				'desc'         => 'Upload an alternate image for this character.',
				'id'           => 'alt_image_file',
				'type'         => 'file',
				'options'      => array(
					'url' => false,
				),
				'text'         => array(
					'add_upload_file_text' => 'Add Image',
				),
				'preview_size' => array( 50, 50 ),
			)
		);

		// Field Group: Character Show information
		$group_shows = $cmb_characters->add_field(
			array(
				'id'         => $prefix . 'show_group',
				'type'       => 'group',
				'repeatable' => true,
				'options'    => array(
					'group_title'   => 'Show #{#}',
					'add_button'    => 'Add Another Show',
					'remove_button' => 'Remove Show',
					'sortable'      => true,
				),
			)
		);
		// Field: Show Name
		/**
		$cmb_characters->add_group_field(
			$group_shows,
			array(
				'name'       => 'TV Show',
				'id'         => 'show',
				'desc'       => 'Select one show. If there are additional shows, scroll down and use the \'Add Another Show\' button.',
				'type'       => 'custom_attached_posts', // This field type
				'post_type'  => 'post_type_shows',
				'options'    => array(
					'query_args' => array(
						'posts_per_page' => 2,
						'post_type'      => 'post_type_shows',
					), // override the get_posts args
				),
				'attributes' => array(
					'data-max-items' => 1,
				),
			)
		);
		**/

		$cmb_characters->add_group_field(
			$group_shows,
			array(
				'name'           => 'TV Show',
				'id'             => 'show',
				'desc'           => 'Select a show. If there are additional shows, scroll down and use the \'Add Another Show\' button.',
				'type'           => 'taxonomy_select', // This field type
				'taxonomy'       => Shows::SHADOW_TAXONOMY,
				'remove_default' => 'true', // Removes the default metabox provided by WP core.
			)
		);

		// Field: Character Type
		$cmb_characters->add_group_field(
			$group_shows,
			array(
				'name'             => 'Character Type',
				'desc'             => 'Mains are in credits. Recurring have their own plots. Guests show up once or twice. Pick what\'s most appropriate.',
				'id'               => 'type',
				'type'             => 'select',
				'show_option_none' => true,
				'default'          => 'custom',
				'options'          => $this->character_roles,
			)
		);
		// Field: Character Years Appears
		$cmb_characters->add_group_field(
			$group_shows,
			array(
				'name'              => 'Years Appears',
				'desc'              => 'Add what years the character appears on this show.',
				'id'                => 'appears',
				'type'              => 'pw_multiselect',
				'select_all_button' => false,
				'remove_default'    => 'true',
				'options'           => $this->years_array,
				'attributes'        => array(
					'placeholder' => 'Years ...',
				),
			)
		);
	}

	/*
	 * Remove Metaboxes we use elsewhere
	 */
	public function remove_metaboxes() {
		remove_meta_box( 'authordiv', 'post_type_characters', 'normal' );
		remove_meta_box( 'postexcerpt', 'post_type_characters', 'normal' );
	}
}
