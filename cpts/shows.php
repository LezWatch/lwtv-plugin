<?php
/*
 * Custom Post Type for Shows on LWTV
 *
 * @since 1.0
 * Author: Evan Herman, Tracy Levesque, Mika Epstein
 */

/**
 * class LWTV_CPT_Shows
 */
class LWTV_CPT_Shows {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'admin_init') );

		add_action( 'init', array( $this, 'create_post_type'), 0 );
		add_action( 'init', array( $this, 'create_taxonomies'), 0 );

		add_action( 'amp_init', array( $this, 'amp_init' ) );
		add_action( 'cmb2_admin_init', array( $this, 'cmb2_metaboxes') );
	}

	/**
	 * Admin Init
	 */
	public function admin_init() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts') );
		add_action( 'admin_head', array($this, 'admin_css') );

		add_filter( 'manage_post_type_shows_posts_columns', array( $this, 'manage_posts_columns' ) );
		add_action( 'manage_post_type_shows_posts_custom_column', array( $this, 'manage_posts_custom_column' ), 10, 2 );
		add_filter( 'manage_edit-post_type_shows_sortable_columns', array( $this, 'manage_edit_sortable_columns' ) );

		add_action( 'pre_get_posts', array( $this, 'columns_sortability_simple' ) );
		add_filter( 'posts_clauses', array( $this, 'columns_sortability_format' ), 10, 2 );

		add_action( 'quick_edit_custom_box',  array( $this, 'quick_edit_custom_box' ), 10, 2 );
		add_action( 'save_post', array( $this, 'quick_edit_save_post' ) );
		add_action( 'admin_footer', array( $this, 'quick_edit_js') );
		add_filter( 'post_row_actions', array( $this, 'quick_edit_link' ), 10, 2 );

		add_action( 'do_update_char_count', array( $this, 'update_char_count' ), 10, 2 );
		add_action( 'save_post_post_type_shows', array( $this, 'update_char_count' ), 10, 3 );
		add_action( 'save_post_post_type_characters', array( $this, 'update_char_count_from_chars' ), 10, 3 );

		add_action( 'dashboard_glance_items', array( $this, 'dashboard_glance_items' ) );

	}

	/**
	 * CSS Customizations
	 */
	public function admin_enqueue_scripts( $hook ) {
		global $current_screen;
		wp_register_style( 'shows-styles', plugins_url('shows.css', __FILE__ ) );
		if( 'post_type_shows' == $current_screen->post_type || 'lez_stations' == $current_screen->taxonomy || 'lez_tropes' == $current_screen->taxonomy ) {
			wp_enqueue_style( 'shows-styles' );
		}
	}

	/**
	 * Create Custom Post Type
	 */
	public function create_post_type() {
		
		$name = 'TV Show';
		
		$labels = array(
			'name'                  => $name.'s',
			'singular_name'         => $name,
			'menu_name'             => $name.'s',
			'name_admin_bar'        => $name,
			'add_new'               => 'Add New',
			'add_new_item'          => 'Add New '.$name,
			'edit_item'             => 'Edit '.$name,
			'new_item'              => 'New '.$name,
			'view_item'             => 'View '.$name,
			'all_items'             => 'All '.$name.'s',
			'search_items'          => 'Search '.$name.'s',
			'not_found'             => 'No '.$name.'s found',
			'not_found_in_trash'    => 'No '.$name.'s found in Trash',
			'update_item'           => 'Update '.$name,
			'featured_image'        => $name.' Image',
			'set_featured_image'    => 'Set '.$name.' image',
			'remove_featured_image' => 'Remove '.$name.' image',
			'use_featured_image'    => 'Use as '.$name.' image',
			'archives'              => $name.' archives',
			'insert_into_item'      => 'Insert into '.$name,
			'uploaded_to_this_item' => 'Uploaded to this '.$name,
			'filter_items_list'     => 'Filter '.$name.' list',
			'items_list_navigation' => $name.' list navigation',
			'items_list'            => $name.' list',
		);
		$args = array(
			'label'               => 'post_type_shows',
			'description'         => $name.'s',
			'labels'              => $labels,
			'public'              => true,
			'show_in_rest'        => true,
			'rest_base'           => 'show',
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-video-alt',
			'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'genesis-cpt-archives-settings', 'genesis-seo', 'revisions' ),
			'taxonomies'          => array( 'lez_tropes' ),
			'has_archive'         => true,
			'rewrite'             => array( 'slug' => 'show' ),
			'delete_with_user'    => false,
		);
		register_post_type( 'post_type_shows', $args );
	}

	/*
	 * Create Custom Taxonomies
	 */
	public function create_taxonomies() {
		
		// TV STATIONS
		$name_tvstations   = 'TV Station';
		$labels_tvstations = array(
			'name'                       => 'TV Station(s)',
			'singular_name'              => 'TV Station',
			'search_items'               => 'Search Stations',
			'popular_items'              => 'Popular Stations',
			'all_items'                  => 'All Stations',
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => 'Edit Station',
			'update_item'                => 'Update Station',
			'add_new_item'               => 'Add New Station',
			'new_item_name'              => 'New Station Name',
			'separate_items_with_commas' => 'Separate stations with commas',
			'add_or_remove_items'        => 'Add or remove stations',
			'choose_from_most_used'      => 'Choose from the most used stations',
			'not_found'                  => 'No Stations found.',
			'menu_name'                  => 'TV Stations',
		);
		//parameters for the new taxonomy
		$args_tvstations = array(
			'hierarchical'          => false,
			'labels'                => $labels_tvstations,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'show_in_nav_menus'     => true,
			'rewrite'               => array( 'slug' => 'station' ),
		);
		register_taxonomy( 'lez_stations', 'post_type_shows', $args_tvstations );

		// SHOW TROPES
		$names_tropes = array(
			'name'                       => 'Show Tropes',
			'singular_name'              => 'Trope',
			'menu_name'                  => 'Tropes',
			'all_items'                  => 'All Tropes',
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'new_item_name'              => 'New Trope',
			'add_new_item'               => 'Add New Trope',
			'edit_item'                  => 'Edit Trope',
			'update_item'                => 'Update Trope',
			'separate_items_with_commas' => 'Separate tropes with commas',
			'search_items'               => 'Search Tropes',
			'add_or_remove_items'        => 'Add or remove tropes',
			'choose_from_most_used'      => 'Choose from the most used tropes',
			'not_found'                  => 'Not Found',
		);
		$args_tropes = array(
			'hierarchical'      => true,
			'labels'            => $names_tropes,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => false,
			'rewrite'           => array( 'slug' => 'trope' ),
		);
		register_taxonomy( 'lez_tropes', array( 'post_type_shows' ), $args_tropes );

		// SHOW Format
		$names_showformat = array(
			'name'                       => 'Show Formats',
			'singular_name'              => 'Show Format',
			'search_items'               => 'Search Formats',
			'popular_items'              => 'Popular Formats',
			'all_items'                  => 'All Formats',
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => 'Edit Format',
			'update_item'                => 'Update Format',
			'add_new_item'               => 'Add New Format',
			'new_item_name'              => 'New Format Name',
			'separate_items_with_commas' => 'Separate Formats with commas',
			'add_or_remove_items'        => 'Add or remove Formats',
			'choose_from_most_used'      => 'Choose from the most used Formats',
			'not_found'                  => 'No Formats found.',
			'menu_name'                  => 'Show Formats',
		);
		//parameters for the new taxonomy
		$args_showformat = array(
			'hierarchical'          => false,
			'labels'                => $names_showformat,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'query_var'             => true,
			'show_in_nav_menus'		=> true,
			'show_in_quick_edit'	=> false,
			'rewrite'               => array( 'slug' => 'format' ),
		);
		register_taxonomy( 'lez_formats', 'post_type_shows', $args_showformat );
	}

	/*
	 * CMB2 Metaboxes
	 */
	public function cmb2_metaboxes() {

		// prefix for all custom fields
		$prefix = 'lezshows_';

		// This is just an array of all years from 1930 on (1930 being the year TV dramas started)
		$year_array = array();
		foreach ( range(date('Y'), '1930' ) as $year) {
			$startyear_array[$year] = $year;
		}

		// Must See Metabox - this should be required
		$cmb_mustsee = new_cmb2_box( array(
			'id'           => 'mustsee_metabox',
			'title'        => 'Required Details',
			'object_types' => array( 'post_type_shows', ), // Post type
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true, // Show field names on the left
		) );

		$cmb_mustsee->add_field( array(
			'name'              => 'Trope Plots',
			'id'                => $prefix . 'tropes',
			'taxonomy'          => 'lez_tropes', //Enter Taxonomy Slug
			'type'              => 'taxonomy_multicheck',
			'select_all_button' => false,
			'remove_default'    => 'true',
		) );

		$cmb_mustsee->add_field( array(
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
		$cmb_mustsee->add_field( array(
			'name'    => 'Worth It Details',
			'id'      => $prefix . 'worthit_details',
			'type'    => 'textarea_small',
		) );

		// Basic Show Details
		$cmb_showdetails = new_cmb2_box( array(
			'id'           => 'shows_metabox',
			'title'        => 'Shows Details',
			'object_types' => array( 'post_type_shows', ), // Post type
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true, // Show field names on the left
		) );

		$cmb_showdetails->add_field( array(
			'name'    => 'Queer Timeline',
			'desc'    => 'Which seasons/episodes have the queer in it',
			'id'      => $prefix . 'plots',
			'type'    => 'wysiwyg',
			'options' => array( 'textarea_rows' => 10, ),
		) );

		$cmb_showdetails->add_field( array(
			'name'    => 'Notable Episodes',
			'desc'    => 'Lez-centric episodes and plotlines',
			'id'      => $prefix . 'episodes',
			'type'    => 'wysiwyg',
			'options' => array(	'textarea_rows' => 10, ),
		) );

		// Box for Ratings
		$cmb_ratings = new_cmb2_box( array(
			'id'            => 'ratings_metabox',
			'title'         => 'Show Rating',
			'desc'          => 'Ratings are subjective 1 to 5, with 1 being low and 5 being The L Word.',
			'object_types'  => array( 'post_type_shows', ), // Post type
			'context'       => 'normal',
			'priority'      => 'high',
			'show_names   ' => true, // Show field names on the left
		) );

		$cmb_ratings->add_field( array(
			'name'    => 'Realness Rating',
			'id'      => $prefix . 'realness_rating',
			'desc'    => 'How realistic are the queers?',
			'type'    => 'radio_inline',
			'options' => array(
				'1' => '1',
				'2' => '2',
				'3' => '3',
				'4' => '4',
				'5' => '5',
			),
		) );

		$cmb_ratings->add_field( array(
			'name'    => 'Realness Details',
			'id'      => $prefix . 'realness_details',
			'type'    => 'wysiwyg',
			'options' => array(	'textarea_rows' => 5, ),
		) );

		$cmb_ratings->add_field( array(
			'name'    => 'Show Quality Rating',
			'id'      => $prefix . 'quality_rating',
			'desc'    => 'How good is the show for queers?',
			'type'    => 'radio_inline',
			'options' => array(
				'1' => '1',
				'2' => '2',
				'3' => '3',
				'4' => '4',
				'5' => '5',
			),
		) );

		$cmb_ratings->add_field( array(
			'name'    => 'Show Quality Details',
			'id'      => $prefix . 'quality_details',
			'type'    => 'wysiwyg',
			'options' => array(	'textarea_rows' => 5, ),
		) );

		$cmb_ratings->add_field( array(
			'name'    => 'Screentime Rating',
			'id'      => $prefix . 'screentime_rating',
			'desc'    => 'How much air-time do the queers get?',
			'type'    => 'radio_inline',
			'options' => array(
				'1' => '1',
				'2' => '2',
				'3' => '3',
				'4' => '4',
				'5' => '5',
			),
		) );

		$cmb_ratings->add_field( array(
			'name'    => 'Screentime Details',
			'id'      => $prefix . 'screentime_details',
			'type'    => 'wysiwyg',
			'options' => array(	'textarea_rows' => 5, ),
		) );

		// Metabox for the side (under shows)
		$cmb_notes = new_cmb2_box( array(
			'id'            	=> 'notes_metabox',
			'title'         	=> 'Additional Data',
			'object_types'  	=> array( 'post_type_shows', ), // Post type
			'context'       	=> 'side',
			'priority'      	=> 'default',
			'show_names'		=> true, // Show field names on the left
			'cmb_styles'		=> false,
		) );
		$cmb_notes->add_field( array(
			'name'     => 'Air Dates',
			'desc'     => 'Years the show Aired',
			'id'       => $prefix . 'airdates',
			'type'     => 'date_year_range',
			'earliest' => '1930',
			'text'     => array(
				'start_label'  => '',
				'finish_label' => '',
			),
			'options'  => array(
				'start_reverse_sort' => true,
				'finish_reverse_sort' => true,
		    ),
		) );
		$cmb_notes->add_field( array(
			'name'             => 'Show Format',
			'desc'             => 'What kind of television entertainment is this?',
			'id'               => $prefix . 'tvtype',
			'taxonomy'         => 'lez_formats',
			'type'             => 'taxonomy_select',
			'remove_default'   => 'true',
			'default'          => 'tv-show',
			'show_option_none' => false,
		) );
		$cmb_notes->add_field( array(
			'name'             => 'Show Stars',
			'desc'             => 'Gold is by/for queers, No Stars is normal TV',
			'id'               => $prefix . 'stars',
			'type'             => 'select',
			'show_option_none' => 'No Stars',
			'options'          => array(
				'gold'   => 'Gold Star',
				'silver' => 'Silver Star',
			)
		) );
		$cmb_notes->add_field( array(
			'name' => 'Triggers Warning?',
			'desc' => 'i.e. Game of Thrones, Jessica Jones, etc.',
			'id'   => $prefix . 'triggerwarning',
			'type' => 'checkbox'
		) );
	}

	/*
	 * Create Custom Columns
	 * Used by quick edit, etc
	 */
	public function manage_posts_columns($columns) {
		$columns['shows-airdate']    = 'Airdates';
		$columns['shows-worthit']    = 'Worth It?';
		$columns['shows-queercount'] = '#';
		return $columns;
	}

	/*
	 * Add Custom Column Content
	 */
	public function manage_posts_custom_column( $column, $post_id ) {
		if ( get_post_meta( $post_id, 'lezshows_airdates', true ) ) {
			$airdates = get_post_meta( $post_id, 'lezshows_airdates', true );
			$airdates = $airdates['start'] .' - '. $airdates['finish'];
		} else {
			$airdates = "N/A";
		}

		switch ( $column ) {
			case 'shows-airdate':
				echo $airdates;
				break;
			case 'shows-worthit':
				echo ucfirst(get_post_meta( $post_id, 'lezshows_worthit_rating', true ));
				break;
			case 'shows-queercount':
				echo get_post_meta( $post_id, 'lezshows_char_count', true );
				break;
		}
	}

	/*
	 * Make Custom Columns Sortable
	 */
	public function manage_edit_sortable_columns( $columns ) {
		unset( $columns['cpt-airdate'] );             // Don't allow sort by airdates
		$columns['taxonomy-lez_formats'] = 'format';  // Allow sort by show format
		$columns['shows-worthit']        = 'worth';   // Allow sort by worth
		$columns['shows-queercount']	     = 'queers';  // Allow sort by queers
		return $columns;
	}

	/*
	 * Create Simple Columns Sortability
	 *
	 * Worth It, Char Count
	 */
	public function columns_sortability_simple( $query ) {
		if( ! is_admin() ) return;

		if ( $query->is_main_query() && ( $orderby = $query->get( 'orderby' ) ) ) {
		switch( $orderby ) {
				case 'worth':
					$query->set( 'meta_key', 'lezshows_worthit_rating' );
					$query->set( 'orderby', 'meta_value' );
					break;
				case 'queers':
					$query->set( 'meta_key', 'lezshows_char_count' );
					$query->set( 'orderby', 'meta_value_num' );
			}
		}
	}

	/*
	 * Create columns sortability for show Formats
	 */
	public function columns_sortability_format( $clauses, $wp_query ) {
		global $wpdb;

		if ( isset( $wp_query->query['orderby'] ) && 'format' == $wp_query->query['orderby'] ) {

			$clauses['join'] .= <<<SQL
LEFT OUTER JOIN {$wpdb->term_relationships} ON {$wpdb->posts}.ID={$wpdb->term_relationships}.object_id
LEFT OUTER JOIN {$wpdb->term_taxonomy} USING (term_taxonomy_id)
LEFT OUTER JOIN {$wpdb->terms} USING (term_id)
SQL;

			$clauses['where']   .= " AND (taxonomy = 'lez_formats' OR taxonomy IS NULL)";
			$clauses['groupby']  = "object_id";
			$clauses['orderby']  = "GROUP_CONCAT({$wpdb->terms}.name ORDER BY name ASC) ";
			$clauses['orderby'] .= ( 'ASC' == strtoupper( $wp_query->get('order') ) ) ? 'ASC' : 'DESC';
		}

		return $clauses;
	}

	/*
	 * Add Quick Edit Boxes
	 */
	public function quick_edit_custom_box($column_name, $post_type) {
		switch ( $column_name ) {
			case 'taxonomy-lez_formats':
				?>
				<fieldset class="inline-edit-col-left">
				<div class="inline-edit-col">
				<span class="title">Show Format</span>
					<input type="hidden" name="lez_formats_noncename" id="lez_formats_noncename" value="" />
					<?php
						$terms = get_terms( array( 'taxonomy' => 'lez_formats', 'hide_empty' => false ) );
					?>
					<select name='terms_lez_formats' id='terms_lez_formats'>
						<option class='lez_formats-option' value='0'>(Undefined)</option>
						<?php
						foreach ($terms as $term) {
							echo "<option class='lez_formats-option' value='{$term->name}'>{$term->name}</option>\n";
						}
							?>
					</select>
				</div>
				</fieldset>
				<?php
				break;
		}
	}

	/*
	 * Allow Quick Edit boxes to save
	 */
	public function quick_edit_save_post($post_id) {
		// Criteria for not saving: Auto-saves, not post_type_characters, can't edit
		if ( ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) || ( isset( $_POST['post_type'] ) &&  'post_type_shows' != $_POST['post_type'] ) || !current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		}
		$post = get_post($post_id);

		// Formats
		if ( isset($_POST['terms_lez_formats']) && ($post->post_type != 'revision') ) {
			$lez_formats_term = esc_attr($_POST['terms_lez_formats']);
			$term = term_exists( $lez_formats_term, 'lez_formats');
			if ( $term !== 0 && $term !== null) {
				wp_set_object_terms( $post_id, $lez_formats_term, 'lez_formats' );
			}
		}
	}

	/*
	 * Quick Edit Save
	 *
	 * Javascript to force defaults
	 */
	public function quick_edit_js() {
		global $current_screen;
		if ( is_null($current_screen) || ($current_screen->id !== 'edit-post_type_shows') || ($current_screen->post_type !== 'post_type_shows') ) return;
		?>
		<script type="text/javascript">
		<!--

		function set_inline_lwtv_quick_edit_defaults( formatsSet, nonce ) {
			// revert Quick Edit menu so that it refreshes properly
			inlineEditPost.revert();
			var formatsInput = document.getElementById('terms_lez_formats');
			var nonceInput	 = document.getElementById('lez_formats_noncename');
			nonceInput.value   = nonce;

			// Set Formats Option
			for (i = 0; i < formatsInput.options.length; i++) {
				if (formatsInput.options[i].value == formatsSet) {
					formatsInput.options[i].setAttribute("selected", "selected");
				} else { formatsInput.options[i].removeAttribute("selected"); }
			}
		}

		//-->
		</script>
		<?php
	}

	/*
	 * Quick Edit Link
	 *
	 * Call the Javascript in Quick Edit Save
	 */
	public function quick_edit_link($actions, $post) {
		global $current_screen;
		if (($current_screen->id != 'edit-post_type_shows') || ($current_screen->post_type != 'post_type_shows')) return $actions;

		$nonce = wp_create_nonce( 'lez_formats_'.$post->ID);
		$formats_terms = wp_get_post_terms( $post->ID, 'lez_formats', array( 'fields' => 'all' ) );

		$actions['inline hide-if-no-js'] = '<a href="#" class="editinline" title="';
		$actions['inline hide-if-no-js'] .= esc_attr( __( 'Edit this item inline' ) ) . '" ';
		$actions['inline hide-if-no-js'] .= " onclick=\"set_inline_lwtv_quick_edit_defaults('{$formats_terms[0]->name}', '{$nonce}')\">";
		$actions['inline hide-if-no-js'] .= __( 'Quick&nbsp;Edit' );
		$actions['inline hide-if-no-js'] .= '</a>';
		return $actions;
	}


	/*
	 * Save post meta for shows on SHOW update
	 *
	 * This will update the metakey 'lezshows_char_count' on save
	 *
	 * @param int $post_id The post ID.
	 * @param post $post The post object.
	 * @param bool $update Whether this is an existing post being updated or not.
	 */
	public function update_char_count( $post_id ) {

		// unhook this function so it doesn't loop infinitely
		remove_action( 'save_post_post_type_shows', array( $this, 'update_char_count' ) );

		$meta_value = $this->count_queers($post_id);
		update_post_meta( $post_id, 'lezshows_char_count', $meta_value );

		// re-hook this function
		add_action( 'save_post_post_type_shows', array( $this, 'update_char_count' ) );
	}

	/*
	 * Save post meta for shows on CHARACTER update
	 *
	 * This will update the metakey 'lezshows_char_count' on save
	 *
	 * @param int $post_id The post ID.
	 */
	public function update_char_count_from_chars( $post_id ) {
		if ( !is_array (get_post_meta( $post_id, 'lezchars_show', true)) ) {
			$shows_array = array( get_post_meta( $post_id, 'lezchars_show', true) );
		} else {
			$shows_array = get_post_meta( $post_id, 'lezchars_show', true);
		}

		foreach ( $shows_array as $show_id ) {
			do_action( 'do_update_char_count' , $show_id );
		}
	}

	/*
	 * Count Queers
	 *
	 * This will update the metakey 'lezshows_char_count' on save
	 *
	 * @param int $post_id The post ID.
	 */
	public function count_queers( $post_id ) {

		// If this isn't a show post, return nothing.
		if ( get_post_type( $post_id ) !== 'post_type_shows' )
			return;

		// Loop to get the list of characters
		$charactersloop = new WP_Query(
			array(
				'post_type'       => 'post_type_characters',
				'orderby'         => 'title',
				'order'           => 'ASC',
				'posts_per_page'  => '-1',
				'meta_query'      => array(
					array(
						'key'     => 'lezchars_show',
						'value'   => $post_id,
						'compare' => 'LIKE',
					),
				),
			)
		);

		$queercount  = 0;

		// Store as array to defeat some stupid with counting and prevent querying the database too many times
		if ($charactersloop->have_posts() ) {
			while ( $charactersloop->have_posts() ) {

				$charactersloop->the_post();
				$char_id = get_the_ID();

				if ( !is_array (get_post_meta( $char_id, 'lezchars_show', true)) ) {
					$shows_array = array( get_post_meta( $char_id, 'lezchars_show', true) );
				} else {
					$shows_array = get_post_meta( $char_id, 'lezchars_show', true);
				}

				if ( in_array( $post_id, $shows_array  ) ) {
					$queercount++;
				}
			}
			wp_reset_query();
		}

		// Return Queers!
		return $queercount;
	}

	/*
	 * Add CPT to AMP
	 */
	function amp_init() {
	    add_post_type_support( 'post_type_shows', AMP_QUERY_VAR );
	}

	/*
	 * Add to 'Right Now'
	 */
	public function dashboard_glance_items() {
		foreach ( array( 'post_type_shows' ) as $post_type ) {
			$num_posts = wp_count_posts( $post_type );
			if ( $num_posts && $num_posts->publish ) {
				if ( 'post_type_shows' == $post_type ) {
					$text = _n( '%s TV Show', '%s TV Shows', $num_posts->publish );
				}
				$text = sprintf( $text, number_format_i18n( $num_posts->publish ) );
				printf( '<li class="%1$s-count"><a href="edit.php?post_type=%1$s">%2$s</a></li>', $post_type, $text );
			}
		}
	}

	/*
	 * Style for dashboard
	 */
	public function admin_css() {
		echo "<style type='text/css'>
			#adminmenu #menu-posts-post_type_shows div.wp-menu-image:before, #dashboard_right_now li.post_type_shows-count a:before {
				content: '\\f126';
				margin-left: -1px;
			}
		</style>";
	}

}
new LWTV_CPT_Shows();