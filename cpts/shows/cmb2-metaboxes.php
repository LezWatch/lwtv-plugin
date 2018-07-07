<?php
/**
 * Name: CMB2 Metaboxes
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * class LWTV_Shows_CMB2
 *
 * @since 2.1.0
 */

class LWTV_Shows_CMB2 {

	public $ratings_array;
	public $affiliates_array;

	public function __construct() {
		add_action( 'cmb2_init', array( $this, 'cmb2_metaboxes' ) );

		// Array of Valid Ratings
		$this->ratings_array = array(
			'1' => '1',
			'2' => '2',
			'3' => '3',
			'4' => '4',
			'5' => '5',
		);
	}

	/*
	 * CMB2 Metaboxes
	 */
	public function cmb2_metaboxes() {
		// prefix for all custom fields
		$prefix = 'lezshows_';

		// Metabox Group: Must See
		$cmb_mustsee = new_cmb2_box( array(
			'id'           => 'mustsee_metabox',
			'title'        => 'Must See Details',
			'object_types' => array( 'post_type_shows' ),
			'context'      => 'normal',
			'priority'     => 'high',
			'show_in_rest' => true,
			'show_names'   => true, // Show field names on the left
		) );
		// Field: Tropes
		$field_tropes = $cmb_mustsee->add_field( array(
			'name'              => 'Trope Plots',
			'id'                => $prefix . 'tropes',
			'taxonomy'          => 'lez_tropes',
			'type'              => 'pw_multiselect',
			'select_all_button' => false,
			'remove_default'    => 'true',
			'options'           => LWTV_CMB2_Addons::select2_get_options_array_tax( 'lez_tropes' ),
			'attributes'        => array(
				'placeholder' => 'Common tropes ...',
			),
		) );
		// Field: Worth It?
		$field_worththumb = $cmb_mustsee->add_field( array(
			'name'    => 'Worth It?',
			'id'      => $prefix . 'worthit_rating',
			'desc'    => 'Is the show worth watching?',
			'type'    => 'radio_inline',
			'options' => array(
				'Yes' => 'Yes',
				'Meh' => 'Meh',
				'No'  => 'No',
			),
		) );
		// Field: Worth It Details
		$field_worthdetails = $cmb_mustsee->add_field( array(
			'name'       => 'Worth It Details',
			'id'         => $prefix . 'worthit_details',
			'type'       => 'textarea_small',
			'attributes' => array(
				'placeholder' => 'Why is this show worth (or not) watching?',
			),
		) );
		// Field: Worth It - We Love This Shit
		$field_worthshowwelove = $cmb_mustsee->add_field( array(
			'name'    => 'Show We Love',
			'desc'    => 'Above all else, this is a show everyone loves. Only use if you are a billion percent sure and have cleared it on Trello (or over drinks).',
			'id'      => $prefix . 'worthit_show_we_love',
			'type'    => 'checkbox',
			'default' => false,
		) );
		// Field: Worth It - Affiliate Links
		$field_affiliateurl = $cmb_mustsee->add_field( array(
			'name'       => 'Affiliate Link',
			'desc'       => 'Paste in a direct link. Amazon, Apple, and CBS links will be auto-converted to affiliate links. <br />Examples: <code>https://www.amazon.com/One-Mississippi-Season-1/dp/B017APUVI8</code>, <code>https://itunes.apple.com/us/tv-season/take-my-wife-season-2/id1347392483</code>, <code>https://www.cbs.com/shows/star-trek-discovery/</code>',
			'id'         => $prefix . 'affiliate',
			'type'       => 'text_url',
			'repeatable' => true,
		) );

		// Must See Grid
		if ( ! is_admin() ) {
			return;
		} else {
			$grid_mustsee = new \Cmb2Grid\Grid\Cmb2Grid( $cmb_mustsee );
			$row          = $grid_mustsee->addRow();
			$row->addColumns( array( $field_worththumb, $field_worthdetails ) );
		}

		// Metabox: Basic Show Details
		$cmb_showdetails = new_cmb2_box( array(
			'id'           => 'shows_metabox',
			'title'        => 'Plots and Relationship Details',
			'object_types' => array( 'post_type_shows' ),
			'context'      => 'normal',
			'priority'     => 'high',
			'show_in_rest' => true,
			'show_names'   => true, // Show field names on the left
		) );
		$field_ships     = $cmb_showdetails->add_field( array(
			'name'       => '#Ships',
			'id'         => $prefix . 'ships',
			'type'       => 'text',
			'attributes' => array(
				'placeholder' => 'Separate multiple ship names with commas',
			),
		) );
		// Field: Queer Timeline
		$field_timeline = $cmb_showdetails->add_field( array(
			'name'       => 'Queer Timeline',
			'id'         => $prefix . 'plots',
			'type'       => 'wysiwyg',
			'options'    => array(
				'textarea_rows' => 10,
				'teeny'         => true,
				'media_buttons' => false,
			),
			'attributes' => array(
				'placeholder' => 'A broad overview of the queerest seasons.',
			),
		) );
		// Field: Notable Episodes
		$field_episodes = $cmb_showdetails->add_field( array(
			'name'       => 'Notable Episodes',
			'id'         => $prefix . 'episodes',
			'type'       => 'wysiwyg',
			'options'    => array(
				'textarea_rows' => 10,
				'media_buttons' => false,
				'teeny'         => true,
			),
			'attributes' => array(
				'placeholder' => 'List the best episodes.',
			),
		) );
		// Basic Show Details Grid
		if ( ! is_admin() ) {
			return;
		} else {
			$grid_showdetails = new \Cmb2Grid\Grid\Cmb2Grid( $cmb_showdetails );
			$row              = $grid_showdetails->addRow();
			$row->addColumns( array( $field_timeline, $field_episodes ) );
		}

		// Metabox: Ratings
		$cmb_ratings = new_cmb2_box( array(
			'id'           => 'ratings_metabox',
			'title'        => 'Relativistic Ratings',
			'desc'         => 'Ratings are subjective 1 to 5, with 1 being low and 5 being The L Word.',
			'object_types' => array( 'post_type_shows' ),
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true, // Show field names on the left
			'show_in_rest' => true,
		) );
		// Field: Realness Rating
		$field_rating_real = $cmb_ratings->add_field( array(
			'name'    => 'Realness Rating',
			'id'      => $prefix . 'realness_rating',
			'desc'    => 'How realistic are the queers?',
			'type'    => 'radio_inline',
			'options' => $this->ratings_array,
		) );
		// Field: Realness Details
		$field_detail_real = $cmb_ratings->add_field( array(
			'name'       => 'Realness Details',
			'id'         => $prefix . 'realness_details',
			'type'       => 'wysiwyg',
			'options'    => array(
				'textarea_rows' => 5,
				'media_buttons' => false,
				'teeny'         => true,
			),
			'attributes' => array(
				'placeholder' => 'Explain the rating (optional).',
			),
		) );
		// Field: Show Quality Rating
		$field_rating_quality = $cmb_ratings->add_field( array(
			'name'    => 'Quality Rating',
			'id'      => $prefix . 'quality_rating',
			'desc'    => 'How good is the show for queers?',
			'type'    => 'radio_inline',
			'options' => $this->ratings_array,
		) );
		// Field: Show Quality Details
		$field_detail_quality = $cmb_ratings->add_field( array(
			'name'       => 'Quality Details',
			'id'         => $prefix . 'quality_details',
			'type'       => 'wysiwyg',
			'options'    => array(
				'textarea_rows' => 5,
				'media_buttons' => false,
				'teeny'         => true,
			),
			'attributes' => array(
				'placeholder' => 'Explain the rating (optional).',
			),
		) );
		// Field: Screentime Rating
		$field_rating_screen = $cmb_ratings->add_field( array(
			'name'    => 'Screentime Rating',
			'id'      => $prefix . 'screentime_rating',
			'desc'    => 'How much air-time do they get?',
			'type'    => 'radio_inline',
			'options' => $this->ratings_array,
		) );
		// Field: Screentime Details
		$field_detail_screen = $cmb_ratings->add_field( array(
			'name'       => 'Screentime Details',
			'id'         => $prefix . 'screentime_details',
			'type'       => 'wysiwyg',
			'options'    => array(
				'textarea_rows' => 5,
				'media_buttons' => false,
				'teeny'         => true,
			),
			'attributes' => array(
				'placeholder' => 'Explain the rating (optional).',
			),
		) );
		// Ratings Grid
		if ( ! is_admin() ) {
			return;
		} else {
			$grid_ratings = new \Cmb2Grid\Grid\Cmb2Grid( $cmb_ratings );
			$row1         = $grid_ratings->addRow();
			$row2         = $grid_ratings->addRow();
			$row3         = $grid_ratings->addRow();
			$row2->addColumns( array( $field_rating_quality, $field_detail_quality ) );
			$row1->addColumns( array( $field_rating_real, $field_detail_real ) );
			$row3->addColumns( array( $field_rating_screen, $field_detail_screen ) );
		}

		// Metabox: Additional Data
		$cmb_notes = new_cmb2_box( array(
			'id'           => 'notes_metabox',
			'title'        => 'Additional Data',
			'object_types' => array( 'post_type_shows' ), // Post type
			'context'      => 'side',
			'priority'     => 'default',
			'show_names'   => true, // Show field names on the left
			'show_in_rest' => true,
			'cmb_styles'   => false,
		) );
		// Field: Air Dates
		$field_airdates = $cmb_notes->add_field( array(
			'name'     => 'Air Dates',
			'desc'     => 'Years the show originally aired',
			'id'       => $prefix . 'airdates',
			'type'     => 'date_year_range',
			'earliest' => '1930',
			'text'     => array(
				'start_label'  => '',
				'finish_label' => '',
			),
			'options'  => array(
				'start_reverse_sort'  => true,
				'finish_reverse_sort' => true,
				'start_show_current'  => false,
				'finish_show_current' => true,
			),
		) );
		// Field: Number of Seasons
		$field_seasons = $cmb_notes->add_field( array(
			'name'            => 'Seasons',
			'desc'            => 'Number of seasons aired',
			'id'              => $prefix . 'seasons',
			'type'            => 'text',
			'attributes'      => array(
				'type'    => 'number',
				'pattern' => '\d*',
			),
			'sanitization_cb' => 'absint',
			'escape_cb'       => 'absint',
		) );
		// Field: Show Format
		$field_format = $cmb_notes->add_field( array(
			'name'             => 'Format',
			'desc'             => 'Media format.',
			'id'               => $prefix . 'tvtype',
			'taxonomy'         => 'lez_formats',
			'type'             => 'taxonomy_select',
			'remove_default'   => 'true',
			'default'          => 'tv-show',
			'show_option_none' => false,
		) );
		// Field: IMDb ID
		$field_imdb = $cmb_notes->add_field( array(
			'name'       => 'IMDb ID',
			'id'         => $prefix . 'imdb',
			'type'       => 'text',
			'attributes' => array(
				'placeholder' => 'Example: tt6087250',
			),
		) );
		// Field: Show Genre
		$field_genre = $cmb_notes->add_field( array(
			'name'              => 'Genre',
			'desc'              => 'Subject matter.',
			'id'                => $prefix . 'tvgenre',
			'taxonomy'          => 'lez_genres',
			'type'              => 'pw_multiselect',
			'select_all_button' => false,
			'remove_default'    => 'true',
			'options'           => LWTV_CMB2_Addons::select2_get_options_array_tax( 'lez_genres' ),
			'attributes'        => array(
				'placeholder' => 'What is this show about ...',
			),
		) );
		// Field: Show Intersectionality
		$field_intersectional = $cmb_notes->add_field( array(
			'name'              => 'Intersectionality',
			'desc'              => 'Positive represenation.',
			'id'                => $prefix . 'intersectional',
			'taxonomy'          => 'lez_intersections',
			'type'              => 'pw_multiselect',
			'select_all_button' => false,
			'remove_default'    => 'true',
			'options'           => LWTV_CMB2_Addons::select2_get_options_array_tax( 'lez_intersections' ),
			'attributes'        => array(
				'placeholder' => 'What does this show get RIGHT?',
			),
		) );
		// Field: Show Stars
		$field_stars = $cmb_notes->add_field( array(
			'name'             => 'Show Stars',
			'desc'             => 'Gold is by/for queers, No Stars is normal TV',
			'id'               => $prefix . 'stars',
			'taxonomy'         => 'lez_stars',
			'type'             => 'taxonomy_select',
			'remove_default'   => 'true',
			'show_option_none' => 'No Stars',
		) );
		// Field: Trigger Warning
		$field_trigger = $cmb_notes->add_field( array(
			'name'             => 'Warning?',
			'desc'             => 'Trigger Warnings',
			'id'               => $prefix . 'triggerwarning',
			'taxonomy'         => 'lez_triggers',
			'type'             => 'taxonomy_select',
			'remove_default'   => 'true',
			'show_option_none' => 'None',
		) );
		// Additional Data Grid
		if ( ! is_admin() ) {
			return;
		} else {
			$grid_additional = new \Cmb2Grid\Grid\Cmb2Grid( $cmb_notes );
			$row1            = $grid_additional->addRow();
			$row2            = $grid_additional->addRow();
			$row1->addColumns( array( $field_seasons, $field_format ) );
			$row2->addColumns( array( $field_stars, $field_trigger ) );
		}

	}
}

new LWTV_Shows_CMB2();
