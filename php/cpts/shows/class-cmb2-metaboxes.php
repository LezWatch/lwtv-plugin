<?php
/**
 * Name: CMB2 Metaboxes
 */

namespace LWTV\CPTs\Shows;

class CMB2_Metaboxes {

	// prefix for all custom fields
	const PREFIX = 'lezshows_';

	const RATINGS = array(
		'0' => '0',
		'1' => '1',
		'2' => '2',
		'3' => '3',
		'4' => '4',
		'5' => '5',
	);

	// Array of thumbscores.
	const THUMBS = array(
		'Yes' => 'Yes',
		'Meh' => 'Meh',
		'No'  => 'No',
		'TBD' => 'TBD',
	);

	// Languages.
	public $language_array;

	public function __construct() {
		add_action( 'cmb2_init', array( $this, 'cmb2_metaboxes' ) );
		add_filter( 'cmb2_enqueue_js', array( $this, 'cmb2_scripts' ) );
		add_action( 'wp_ajax_get_genres', array( $this, 'return_genres_options' ) );

		// Allow for multiple language names to be saved.
		$this->language_array = lwtv_plugin()->get_all_languages();
	}

	/**
	 * Use CMB2 filter to load our JavaScript
	 * when CMB loads his/hers.
	 *
	 * @param string $scripts CMB scripts.
	 *
	 * @return mixed
	 */
	public function cmb2_scripts( $scripts ) {
		wp_enqueue_script( 'ajaxified_dropdown', LWTV_PLUGIN_URL . 'assets/js/cmb2_ajax.js', array( 'jquery' ), '1.0.0', true );
		$return = $scripts;

		return $return;
	}

	/**
	 * Create a list of all genres that the show has.
	 *
	 * Note: This CANNOT use lwtv_plugin()->get_cmb2_terms_list() as it
	 * lists ALL of the term, and not just the ones they have.
	 */
	public function cmb2_get_genres_options() {
		$the_id = ( false !== get_the_ID() ) ? get_the_ID() : 0;
		$return = array();
		if ( 0 !== $the_id ) {
			$terms = get_the_terms( $the_id, 'lez_genres' );

			if ( $terms && ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					$return[ $term->term_id ] = $term->name;
				}
			}
		}

		return $return;
	}

	/**
	 * Dynamically update genres.
	 *
	 * Has some issues.
	 *
	 * @return void
	 */
	public function return_genres_options() {
		$terms_array = array();
		$output      = '<option value="">None (please choose one)</option>';

		// @codingStandardsIgnoreStart.
		$genres = $_POST[ 'value' ];
		// @codingStandardsIgnoreEnd.

		// Force this to be an array.
		if ( ! is_array( $genres ) ) {
			$genres = array( (int) $genres );
		}

		// Rebuild our array.
		foreach ( $genres as $a_genre ) {
			// get term name by ID.
			$term_data                       = get_term_by( 'id', (int) $a_genre, 'lez_genres' );
			$terms_array[ $term_data->name ] = $a_genre;
		}

		// Alphabetize.
		ksort( $terms_array );

		// Output.
		foreach ( $terms_array as $term_name => $term_id ) {
			$output .= sprintf( "<option value='%s'>%s</option>", $term_id, $term_name );
		}

		if ( ! empty( $output ) ) {
			wp_send_json_success( $output );
		}
		wp_send_json_error();
	}

	/**
	 * CMB2 Metaboxes
	 */
	public function cmb2_metaboxes() {
		// @codingStandardsIgnoreStart.
		// Get the post ID.
		$post_id = null;
		if ( isset( $_GET['post'] ) ) {
			$post_id = (int) $_GET['post'];
		} elseif ( isset( $_POST['post_ID'] ) ) {
			$post_id = (int) $_POST['post_ID'];
		}
		// @codingStandardsIgnoreEnd.

		// Metabox Group: Summary.
		$cmb_a_excerpt = \new_cmb2_box(
			array(
				'id'           => 'show_summary_metabox',
				'title'        => 'Show Summary',
				'object_types' => array( 'post_type_shows' ),
				'context'      => 'normal',
				'priority'     => 'high',
				'show_in_rest' => true,
				'show_names'   => true, // Show field names on the left.
				'cmb_styles'   => false,
			)
		);
		// Field: Excerpt.
		$cmb_a_excerpt->add_field(
			array(
				'name'      => 'Excerpt',
				'id'        => 'excerpt',
				'desc'      => 'Excerpts are short, one to two sentences, summaries of what the show is about. This will be used on the list of all shows, as well as the front page for new shows.',
				'type'      => 'textarea',
				'escape_cb' => false,
				'default'   => get_post_field( 'post_excerpt', $post_id ),
			)
		);

		// METABOX GROUP: Must See.
		$cmb_b_details = \new_cmb2_box(
			array(
				'id'           => 'show_details_metabox',
				'title'        => 'Basic Show Details',
				'object_types' => array( 'post_type_shows' ),
				'context'      => 'normal',
				'priority'     => 'high',
				'show_in_rest' => true,
				'show_names'   => true, // Show field names on the left.
				'cmb_styles'   => false,
			)
		);
		// Field: Air Dates.
		$field_airdates = $cmb_b_details->add_field(
			array(
				'name'    => 'Air Dates',
				'id'      => self::PREFIX . 'airdates',
				'type'    => 'date_year_range',
				'text'    => array(
					'start_label'  => '',
					'finish_label' => '',
				),
				'default' => array(
					'start'  => gmdate( 'Y' ),
					'finish' => 'current',
				),
				'options' => array(
					'earliest'            => ( LWTV_FIRST_YEAR - 10 ),
					'start_reverse_sort'  => true,
					'finish_reverse_sort' => true,
					'start_show_current'  => false,
					'finish_show_current' => true,
				),
			)
		);
		// Field: Number of Seasons.
		$field_seasons = $cmb_b_details->add_field(
			array(
				'name'            => 'Seasons Aired',
				'id'              => self::PREFIX . 'seasons',
				'type'            => 'text',
				'attributes'      => array(
					'type'    => 'number',
					'pattern' => '\d*',
				),
				'sanitization_cb' => 'absint',
				'escape_cb'       => 'absint',
			)
		);
		// Field: Stations.
		$field_stations = $cmb_b_details->add_field(
			array(
				'name'              => 'TV Station(s)',
				'id'                => self::PREFIX . 'tvstations',
				'taxonomy'          => 'lez_stations',
				'type'              => 'pw_multiselect',
				'select_all_button' => false,
				'remove_default'    => 'true',
				'options'           => lwtv_plugin()->get_cmb2_terms_list( 'lez_stations' ),
				'default'           => lwtv_plugin()->get_select2_defaults( 'lezshows_tvstations', 'lez_stations', $post_id, true ),
				'attributes'        => array(
					'placeholder' => 'Ex. NBC',
				),
			)
		);

		// Field: Nations.
		$field_nations = $cmb_b_details->add_field(
			array(
				'name'              => 'Country of Origin',
				'id'                => self::PREFIX . 'tvnations',
				'taxonomy'          => 'lez_country',
				'type'              => 'pw_multiselect',
				'select_all_button' => false,
				'remove_default'    => 'true',
				'options'           => lwtv_plugin()->get_cmb2_terms_list( 'lez_country' ),
				'default'           => lwtv_plugin()->get_select2_defaults( 'lezshows_tvnations', 'lez_country', $post_id, true ),
				'attributes'        => array(
					'placeholder' => 'Ex. Canada',
				),
			)
		);
		// Field: Show Format.
		$field_format = $cmb_b_details->add_field(
			array(
				'name'             => 'Media Format',
				'id'               => self::PREFIX . 'tvtype',
				'taxonomy'         => 'lez_formats',
				'type'             => 'taxonomy_select',
				'remove_default'   => 'true',
				'default'          => 'tv-show',
				'show_option_none' => false,
			)
		);
		// Field: IMDb ID.
		$field_imdb = $cmb_b_details->add_field(
			array(
				'name'       => 'IMDb ID',
				'id'         => self::PREFIX . 'imdb',
				'type'       => 'text',
				'attributes' => array(
					'placeholder' => 'Ex: tt6087250',
				),
			)
		);
		// Field: Show Genre.
		$field_genre = $cmb_b_details->add_field(
			array(
				'name'              => 'Genre',
				'id'                => self::PREFIX . 'tvgenre',
				'taxonomy'          => 'lez_genres',
				'type'              => 'pw_multiselect',
				'select_all_button' => false,
				'remove_default'    => 'true',
				'options'           => lwtv_plugin()->get_cmb2_terms_list( 'lez_genres' ),
				'default'           => lwtv_plugin()->get_select2_defaults( 'lezshows_tvgenre', 'lez_genres', $post_id ),
				'attributes'        => array(
					'placeholder' => 'Ex. Drama',
				),
			)
		);
		// Field: Genre Primary.
		$field_genre_primary = $cmb_b_details->add_field(
			array(
				'name'             => 'Primary Genre',
				'id'               => self::PREFIX . 'tvgenre_primary',
				'type'             => 'select',
				'default'          => 'custom',
				'options_cb'       => array( $this, 'cmb2_get_genres_options' ),
				'remove_default'   => 'true',
				'show_option_none' => 'None (please choose one)',
			)
		);
		// Field: Show Stars.
		$field_stars = $cmb_b_details->add_field(
			array(
				'name'             => 'Show Stars',
				'desc'             => 'Gold is by/for queers, No Stars is normal TV',
				'id'               => self::PREFIX . 'stars',
				'taxonomy'         => 'lez_stars',
				'type'             => 'taxonomy_select',
				'remove_default'   => 'true',
				'show_option_none' => 'No Stars',
			)
		);
		// Field: Trigger Warning.
		$field_trigger = $cmb_b_details->add_field(
			array(
				'name'             => 'Warning?',
				'desc'             => 'Trigger Warnings',
				'id'               => self::PREFIX . 'triggerwarning',
				'taxonomy'         => 'lez_triggers',
				'type'             => 'taxonomy_select',
				'remove_default'   => 'true',
				'show_option_none' => 'None',
			)
		);
		// Field: Show Intersectionality.
		$cmb_b_details->add_field(
			array(
				'name'              => 'Intersectionality',
				'id'                => self::PREFIX . 'intersectional',
				'taxonomy'          => 'lez_intersections',
				'type'              => 'pw_multiselect',
				'select_all_button' => false,
				'remove_default'    => 'true',
				'options'           => lwtv_plugin()->get_cmb2_terms_list( 'lez_intersections' ),
				'default'           => lwtv_plugin()->get_select2_defaults( 'lezshows_intersectional', 'lez_intersections', $post_id ),
				'attributes'        => array(
					'placeholder' => 'Ex. Disabilities',
				),
			)
		);
		// Field: Tropes.
		$cmb_b_details->add_field(
			array(
				'name'              => 'Trope Plots',
				'id'                => self::PREFIX . 'tropes',
				'taxonomy'          => 'lez_tropes',
				'type'              => 'pw_multiselect',
				'select_all_button' => false,
				'remove_default'    => 'true',
				'options'           => lwtv_plugin()->get_cmb2_terms_list( 'lez_tropes' ),
				'default'           => lwtv_plugin()->get_select2_defaults( 'lezshows_tropes', 'lez_tropes', $post_id ),
				'attributes'        => array(
					'placeholder' => 'Ex. Bury Your Queers',
				),
			)
		);
		// Details Grid.
		if ( ! is_admin() ) {
			return;
		} else {
			$grid_b_deet = new \Forked\CMB2\CMB2Grid\Grid\Cmb2Grid( $cmb_b_details );
			$row1_b_deet = $grid_b_deet->addRow();
			$row2_b_deet = $grid_b_deet->addRow();
			$row3_b_deet = $grid_b_deet->addRow();
			$row4_b_deet = $grid_b_deet->addRow();
			$row5_b_deet = $grid_b_deet->addRow();
			$row1_b_deet->addColumns( array( $field_airdates, $field_seasons ) );
			$row2_b_deet->addColumns( array( $field_stations, $field_nations ) );
			$row3_b_deet->addColumns( array( $field_format, $field_imdb ) );
			$row4_b_deet->addColumns( array( $field_genre, $field_genre_primary ) );
			$row5_b_deet->addColumns( array( $field_stars, $field_trigger ) );
		}

		// METABOX GROUP: Worth Id.
		$cmb_c_worth = \new_cmb2_box(
			array(
				'id'           => 'show_worth_metabox',
				'title'        => 'Worth Watching Details',
				'object_types' => array( 'post_type_shows' ),
				'context'      => 'normal',
				'priority'     => 'high',
				'show_in_rest' => true,
				'show_names'   => true, // Show field names on the left.
				'cmb_styles'   => false,
			)
		);

		// Field: Worth It?
		$field_worththumb = $cmb_c_worth->add_field(
			array(
				'name'    => 'Worth Watching?',
				'id'      => self::PREFIX . 'worthit_rating',
				'desc'    => 'Is this show worth watching?',
				'type'    => 'select',
				'default' => 'TBD',
				'options' => self::THUMBS,
			)
		);
		// Field: Worth Wit Details.
		$field_worthdetails = $cmb_c_worth->add_field(
			array(
				'name'       => 'Details on Why',
				'id'         => self::PREFIX . 'worthit_details',
				'type'       => 'textarea_small',
				'attributes' => array(
					'placeholder' => 'Why is this show worth (or not) watching?',
				),
			)
		);

		// GRID: Worth It
		if ( ! is_admin() ) {
			return;
		} else {
			$grid_c_worth = new \Forked\CMB2\CMB2Grid\Grid\Cmb2Grid( $cmb_c_worth );
			$row_c_worth  = $grid_c_worth->addRow();
			$row_c_worth->addColumns( array( $field_worththumb, $field_worthdetails ) );
		}

		// METABOX GROUP: Editorial Section
		$cmb_d_editorial = \new_cmb2_box(
			array(
				'id'           => self::PREFIX . 'editorial',
				'title'        => 'Editorial Section - STAFF ONLY (non admins can\'t see this)',
				'object_types' => array( 'post_type_shows' ),
				'context'      => 'normal',
				'priority'     => 'high',
				'show_in_rest' => true,
				'show_names'   => true, // Show field names on the left.
				'cmb_styles'   => false,
				'show_on'      => array(
					'key'   => 'role',
					'value' => 'administrator',
				),
			)
		);
		// Field: Worth It - We Love This Shit.
		$field_worthshowwelove = $cmb_d_editorial->add_field(
			array(
				'name'    => 'Show We Love',
				'desc'    => 'This is a show we officially love.',
				'id'      => self::PREFIX . 'worthit_show_we_love',
				'type'    => 'checkbox',
				'default' => false,
			)
		);
		// Field: Death Override
		$field_byq_override = $cmb_d_editorial->add_field(
			array(
				'name' => 'BYQ Override',
				'desc' => 'Do NOT treat BYQ as a negative.',
				'id'   => self::PREFIX . 'byq_override',
				'type' => 'checkbox',
			)
		);
		// GRID: Editorial Details
		if ( ! is_admin() ) {
			return;
		} else {
			$grid_d_edit = new \Forked\CMB2\CMB2Grid\Grid\Cmb2Grid( $cmb_d_editorial );
			$row_d_edit  = $grid_d_edit->addRow();
			$row_d_edit->addColumns( array( $field_worthshowwelove, $field_byq_override ) );
		}

		// METABOX GROUP: Watch
		$cmb_e_watch = \new_cmb2_box(
			array(
				'id'           => self::PREFIX . 'watchdeets',
				'title'        => 'Watching Details',
				'object_types' => array( 'post_type_shows' ),
				'context'      => 'normal',
				'priority'     => 'high',
				'show_in_rest' => true,
				'show_names'   => true, // Show field names on the left.
				'cmb_styles'   => false,
			)
		);
		// Field: Watch Online
		$cmb_e_watch->add_field(
			array(
				'name'       => 'Watch Online Link(s)',
				'desc'       => 'Paste in a direct link. Links are auto-converted to affiliate links.',
				'id'         => self::PREFIX . 'affiliate',
				'type'       => 'text_url',
				'repeatable' => true,
			)
		);
		// Field: Similar Shows.
		$cmb_e_watch->add_field(
			array(
				'name'       => 'Similar Shows',
				'desc'       => 'Drag shows from the left column to the right column to add them.<br />Use search to find the shows.',
				'id'         => self::PREFIX . 'similar_shows',
				'type'       => 'custom_attached_posts',
				'options'    => array(
					'query_args' => array(
						'posts_per_page' => 5,
						'post_type'      => 'post_type_shows',
						'post__not_in'   => isset( $_GET['post'] ) ? array( absint( $_GET['post'] ) ) : false, // phpcs:ignore WordPress.Security.NonceVerification
					), // override the get_posts args
				),
				'attributes' => array(
					'data-max-items' => 6,
				),
			)
		);
		// Field Group: Show Name Information
		$group_names = $cmb_e_watch->add_field(
			array(
				'id'         => self::PREFIX . 'show_names',
				'type'       => 'group',
				'repeatable' => true,
				'options'    => array(
					'group_title'   => 'Alternative Name #{#} (OPTIONAL)',
					'add_button'    => 'Add Another Name',
					'remove_button' => 'Remove Name',
					'sortable'      => true,
				),
			)
		);
		// Field: Show Name
		$cmb_e_watch->add_group_field(
			$group_names,
			array(
				'name'             => 'Name',
				'desc'             => 'Use if the show has different names per language',
				'id'               => self::PREFIX . 'alt_show_name',
				'type'             => 'text',
				'show_option_none' => true,
			)
		);
		// Field: Show Language
		$cmb_e_watch->add_group_field(
			$group_names,
			array(
				'name'             => 'Language',
				'desc'             => 'Language (select from dropdown)',
				'id'               => 'type',
				'type'             => 'select',
				'show_option_none' => true,
				'default'          => 'custom',
				'options'          => $this->language_array,
			)
		);
		// NO GRID NEEDED

		// GROUP: 'Ships and Plots
		$cmb_f_shiplots = \new_cmb2_box(
			array(
				'id'           => 'shows_metabox',
				'title'        => 'Plots and Relationship Details',
				'object_types' => array( 'post_type_shows' ),
				'context'      => 'normal',
				'priority'     => 'high',
				'show_in_rest' => true,
				'show_names'   => true, // Show field names on the left.
			)
		);
		$cmb_f_shiplots->add_field(
			array(
				'name'       => '#Ships',
				'id'         => self::PREFIX . 'ships',
				'type'       => 'text',
				'attributes' => array(
					'placeholder' => 'Separate multiple ship names with commas',
				),
			)
		);
		// Field: Queer Timeline.
		$field_timeline = $cmb_f_shiplots->add_field(
			array(
				'name'       => 'Queer Timeline',
				'id'         => self::PREFIX . 'plots',
				'type'       => 'wysiwyg',
				'options'    => array(
					'textarea_rows' => 10,
					'teeny'         => true,
					'media_buttons' => false,
				),
				'attributes' => array(
					'placeholder' => 'A broad overview of the queerest seasons.',
				),
			)
		);
		// Field: Notable Episodes.
		$field_episodes = $cmb_f_shiplots->add_field(
			array(
				'name'       => 'Notable Episodes',
				'id'         => self::PREFIX . 'episodes',
				'type'       => 'wysiwyg',
				'options'    => array(
					'textarea_rows' => 10,
					'media_buttons' => false,
					'teeny'         => true,
				),
				'attributes' => array(
					'placeholder' => 'List the best episodes.',
				),
			)
		);
		// Basic Show Details Grid.
		if ( ! is_admin() ) {
			return;
		} else {
			$grid_f_plot = new \Forked\CMB2\CMB2Grid\Grid\Cmb2Grid( $cmb_f_shiplots );
			$rows_f_plot = $grid_f_plot->addRow();
			$rows_f_plot->addColumns( array( $field_timeline, $field_episodes ) );
		}

		// Metabox: Ratings.
		$cmb_g_ratings = \new_cmb2_box(
			array(
				'id'           => 'ratings_metabox',
				'title'        => 'Relativistic Ratings',
				'desc'         => 'Ratings are subjective 1 to 5, with 1 being low and 5 being The L Word.',
				'object_types' => array( 'post_type_shows' ),
				'context'      => 'normal',
				'priority'     => 'high',
				'show_names'   => true, // Show field names on the left.
				'show_in_rest' => true,
			)
		);
		// Field: Realness Rating.
		$field_rating_real = $cmb_g_ratings->add_field(
			array(
				'name'    => 'Realness Rating',
				'id'      => self::PREFIX . 'realness_rating',
				'desc'    => 'How realistic are the queers?',
				'type'    => 'radio_inline',
				'options' => self::RATINGS,
			)
		);
		// Field: Realness Details.
		$field_detail_real = $cmb_g_ratings->add_field(
			array(
				'name'       => 'Realness Details',
				'id'         => self::PREFIX . 'realness_details',
				'type'       => 'wysiwyg',
				'options'    => array(
					'textarea_rows' => 5,
					'media_buttons' => false,
					'teeny'         => true,
				),
				'attributes' => array(
					'placeholder' => 'Explain the rating (optional).',
				),
			)
		);
		// Field: Show Quality Rating.
		$field_rating_quality = $cmb_g_ratings->add_field(
			array(
				'name'    => 'Quality Rating',
				'id'      => self::PREFIX . 'quality_rating',
				'desc'    => 'How good is the show for queers?',
				'type'    => 'radio_inline',
				'options' => self::RATINGS,
			)
		);
		// Field: Show Quality Details.
		$field_detail_quality = $cmb_g_ratings->add_field(
			array(
				'name'       => 'Quality Details',
				'id'         => self::PREFIX . 'quality_details',
				'type'       => 'wysiwyg',
				'options'    => array(
					'textarea_rows' => 5,
					'media_buttons' => false,
					'teeny'         => true,
				),
				'attributes' => array(
					'placeholder' => 'Explain the rating (optional).',
				),
			)
		);
		// Field: Screentime Rating.
		$field_rating_screen = $cmb_g_ratings->add_field(
			array(
				'name'    => 'Screentime Rating',
				'id'      => self::PREFIX . 'screentime_rating',
				'desc'    => 'How much air-time do they get?',
				'type'    => 'radio_inline',
				'options' => self::RATINGS,
			)
		);
		// Field: Screentime Details.
		$field_detail_screen = $cmb_g_ratings->add_field(
			array(
				'name'       => 'Screentime Details',
				'id'         => self::PREFIX . 'screentime_details',
				'type'       => 'wysiwyg',
				'options'    => array(
					'textarea_rows' => 5,
					'media_buttons' => false,
					'teeny'         => true,
				),
				'attributes' => array(
					'placeholder' => 'Explain the rating (optional).',
				),
			)
		);
		// Ratings Grid.
		if ( ! is_admin() ) {
			return;
		} else {
			$grid_g_rate  = new \Forked\CMB2\CMB2Grid\Grid\Cmb2Grid( $cmb_g_ratings );
			$rows1_g_rate = $grid_g_rate->addRow();
			$rows2_g_rate = $grid_g_rate->addRow();
			$rows3_g_rate = $grid_g_rate->addRow();
			$rows1_g_rate->addColumns( array( $field_rating_quality, $field_detail_quality ) );
			$rows2_g_rate->addColumns( array( $field_rating_real, $field_detail_real ) );
			$rows3_g_rate->addColumns( array( $field_rating_screen, $field_detail_screen ) );
		}
	}
}
