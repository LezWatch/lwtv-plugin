<?php
/*
 * Custom Post Type for characters on LWTV
 *
 * @since 1.0
 * Author: Evan Herman, Tracy Levesque, Mika Epstein
 */

/**
 * class LWTV_CPT_Characters
 */
class LWTV_CPT_Characters {

	public $character_roles;

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->character_roles = array(
			'regular'   => 'Regular/Main Character',
			'recurring'	=> 'Recurring Character',
			'guest'	 	=> 'Guest Character',
		);

		add_action( 'admin_init', array( $this, 'admin_init') );

		add_action( 'init', array( $this, 'create_post_type'), 0 );
		add_action( 'init', array( $this, 'create_taxonomies'), 0 );

		add_action( 'amp_init', array( $this, 'amp_init' ) );
		add_action( 'cmb2_admin_init', array( $this, 'cmb2_metaboxes') );
		add_action( 'admin_menu', array( $this,'remove_metaboxes' ) );

		add_action( 'wpseo_register_extra_replacements', array( $this, 'yoast_seo_register_extra_replacements' ) );
	}

	/**
	 * Admin Init
	 */
	public function admin_init() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts') );
		add_action( 'admin_head', array($this, 'admin_css') );

		add_filter( 'manage_post_type_characters_posts_columns', array( $this, 'manage_posts_columns' ) );
		add_action( 'manage_post_type_characters_posts_custom_column', array( $this, 'manage_posts_custom_column' ), 10, 2 );
		add_filter( 'manage_edit-post_type_characters_sortable_columns', array( $this, 'manage_edit_sortable_columns' ) );

		add_action( 'pre_get_posts', array( $this, 'columns_sortability_simple' ) );
		add_filter( 'posts_clauses', array( $this, 'columns_sortability_sexuality' ), 10, 2 );
		add_filter( 'posts_clauses', array( $this, 'columns_sortability_gender' ), 10, 2 );

		add_action( 'quick_edit_custom_box',  array( $this, 'quick_edit_custom_box' ), 10, 2 );
		add_action( 'save_post', array( $this, 'quick_edit_save_post' ) );
		add_action( 'admin_footer', array( $this, 'quick_edit_js') );
		add_filter( 'post_row_actions', array( $this, 'quick_edit_link' ), 10, 2 );

		add_action( 'dashboard_glance_items', array( $this, 'dashboard_glance_items' ) );
	}

	/**
	 * CSS tweaks
	 */
	public function admin_enqueue_scripts( $hook ) {
		global $current_screen;
		wp_register_style( 'character-styles', plugins_url('characters.css', __FILE__ ) );
		if( 'post_type_characters' == $current_screen->post_type || 'lez_cliches' == $current_screen->taxonomy ) {
			wp_enqueue_style( 'character-styles' );
		}
	}

	/*
	 * CPT Settings
	 *
	 */
	function create_post_type() {
		$labels = array(
			'name'               => 'Characters',
			'singular_name'      => 'Character',
			'menu_name'          => 'Characters',
			'parent_item_colon'  => 'Parent Character:',
			'all_items'          => 'All Characters',
			'view_item'          => 'View Character',
			'add_new_item'       => 'Add New Character',
			'add_new'            => 'Add New',
			'edit_item'          => 'Edit Character',
			'update_item'        => 'Update Character',
			'search_items'       => 'Search Characters',
			'not_found'          => 'No characters found',
			'not_found_in_trash' => 'No characters in the Trash',
		);
		$args = array(
			'label'               => 'post_type_characters',
			'description'         => 'Characters',
			'labels'              => $labels,
			'public'              => true,
			'show_in_rest'        => true,
			'rest_base'           => 'character',
			'menu_position'       => 7,
			'menu_icon'           => 'dashicons-nametag',
			'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'genesis-cpt-archives-settings', 'genesis-seo', 'revisions' ),
			'has_archive'         => true,
			'rewrite'             => array( 'slug' => 'character' ),
			'delete_with_user'    => false,
		);
		register_post_type( 'post_type_characters', $args );
	}

	/*
	 * Custom Taxonomies
	 *
	 */
	function create_taxonomies() {
		// CLICHES
		$names_cliches = array(
			'name'                       => 'Clichés',
			'singular_name'              => 'Cliché',
			'search_items'               => 'Search Clichés',
			'popular_items'              => 'Popular Clichés',
			'all_items'                  => 'All Clichés',
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => 'Edit Cliché',
			'update_item'                => 'Update Cliché',
			'add_new_item'               => 'Add New Cliché',
			'new_item_name'              => 'New Cliché Name',
			'separate_items_with_commas' => 'Separate Clichés with commas',
			'add_or_remove_items'        => 'Add or remove Clichés',
			'choose_from_most_used'      => 'Choose from the most used Clichés',
			'not_found'                  => 'No Clichés found.',
			'menu_name'                  => 'Clichés',
		);
		//paramters for the new taxonomy
		$args_cliches = array(
			'hierarchical'          => true,
			'labels'                => $names_cliches,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'rewrite'               => array( 'slug' => 'cliche' ),
		);
		register_taxonomy( 'lez_cliches', 'post_type_characters', $args_cliches );

		// GENDER IDENTITY
		$names_gender = array(
			'name'                       => 'Gender',
			'singular_name'              => 'Gender Identity',
			'search_items'               => 'Search Genders',
			'popular_items'              => 'Popular Genders',
			'all_items'                  => 'All Genders',
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => 'Edit Gender',
			'update_item'                => 'Update Gender',
			'add_new_item'               => 'Add New Gender',
			'new_item_name'              => 'New Gender Name',
			'separate_items_with_commas' => 'Separate Genders with commas',
			'add_or_remove_items'        => 'Add or remove Genders',
			'choose_from_most_used'      => 'Choose from the most used Genders',
			'not_found'                  => 'No Genders found.',
			'menu_name'                  => 'Gender Identity',
		);
		$args_gender = array(
			'hierarchical'       => false,
			'labels'             => $names_gender,
			'public'             => true,
			'show_ui'            => true,
			'show_admin_column'  => true,
			'show_in_nav_menus'  => true,
			'show_in_quick_edit' => false,
			'show_tagcloud'      => false,
			'rewrite'            => array( 'slug' => 'gender' ),
		);
		register_taxonomy( 'lez_gender', 'post_type_characters', $args_gender );

		// SEXUALITY
		$names_sexuality = array(
			'name'                       => 'Sexuality',
			'singular_name'              => 'Sexual Orientation',
			'search_items'               => 'Search Sexual Orientations',
			'popular_items'              => 'Popular Sexual Orientations',
			'all_items'                  => 'All Sexual Orientations',
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => 'Edit Sexual Orientation',
			'update_item'                => 'Update Sexual Orientation',
			'add_new_item'               => 'Add New Sexual Orientation',
			'new_item_name'              => 'New Sexual Orientation Name',
			'separate_items_with_commas' => 'Separate Sexual Orientations with commas',
			'add_or_remove_items'        => 'Add or remove Sexual Orientations',
			'choose_from_most_used'      => 'Choose from the most used Sexual Orientations',
			'not_found'                  => 'No Sexual Orientations found.',
			'menu_name'                  => 'Sexual Orientation',
		);
		$args_sexuality = array(
			'hierarchical'       => false,
			'labels'             => $names_sexuality,
			'public'             => true,
			'show_ui'            => true,
			'show_admin_column'  => true,
			'show_in_nav_menus'  => true,
			'show_in_quick_edit' => false,
			'show_tagcloud'      => false,
			'rewrite'            => array( 'slug' => 'sexuality' ),
		);
		register_taxonomy( 'lez_sexuality', 'post_type_characters', $args_sexuality );
	}

	/*
	 * Create a list of all shows
	 */
	public function cmb2_get_shows_options() {
		return LWTV_CMB2::get_post_options( array(
				'post_type'   => 'post_type_shows',
				'numberposts' => wp_count_posts( 'post_type_shows' )->publish,
				'post_status' => array('publish', 'pending', 'draft', 'future'),
			) );
	}

	/*
	 * CMB2 Metaboxes
	 */
	public function cmb2_metaboxes() {
		// prefix for all custom fields
		$prefix = 'lezchars_';

		// MetaBox Group: Character Details
		$cmb_characters = new_cmb2_box( array(
			'id'					=> 'chars_metabox',
			'title'				=> 'Character Details',
			'object_types'  		=> array( 'post_type_characters', ), // Post type
			'context'			=> 'normal',
			'priority'			=> 'high',
			'show_names'			=> true, // Show field names on the left
		) );
		// Field: Character Clichés
		$cmb_characters->add_field( array(
			'name'				=> 'Character Clichés',
			'id'					=> $prefix . 'cliches',
			'taxonomy'			=> 'lez_cliches', //Enter Taxonomy Slug
			'type'	 			=> 'taxonomy_multicheck',
			'select_all_button'	=> false,
			'remove_default'		=> 'true'
		) );
		// Field: Actor Name
		$cmb_characters->add_field( array(
			'name'       => 'Actor Name',
			'desc'       => 'Include years (in parens) for multiple actors',
			'id'         => $prefix . 'actor',
			'type'       => 'text',
			'repeatable' => 'true',
			'attributes' => array(
				'autocomplete'   => 'off',
				'autocorrect'    => 'off',
				'autocapitalize' => 'off',
				'spellcheck'     => 'false',
		    ),
		) );
		// Field: Character Type
		$cmb_characters->add_field( array(
			'name'             => 'Character Type',
			'desc'             => 'Mains are in credits. Recurring have their own plots. Guests show up once or twice.',
			'id'               => $prefix .'type',
			'type'             => 'select',
			'show_option_none' => true,
			'default'          => 'custom',
			'options'          => $this->character_roles,
		) );
		// Field: Show Name
		$cmb_characters->add_field( array(
			'name'             => 'Show',
			'desc'             => 'Select the show this character belongs to',
			'id'               => $prefix . 'show',
			'type'             => 'select',
			'repeatable'       => 'true',
			'show_option_none' => true,
			'default'          => 'custom',
			'options_cb'       => array( $this, 'cmb2_get_shows_options'),
		) );

		// Metabox Group: Quick Dropdowns
		$cmb_charside = new_cmb2_box( array(
			'id'           => 'charnotes_metabox',
			'title'        => 'Additional Data',
			'object_types' => array( 'post_type_characters', ), // Post type
			'context'      => 'side',
			'priority'     => 'default',
			'show_names'   => true, // Show field names on the left
			'cmb_styles'   => false,
		) );
		// Field: Character Gender Idenity
		$cmb_charside->add_field( array(
			'name'             => 'Gender Identity',
			'desc'             => 'Gender with which the character identifies',
			'id'               => $prefix . 'gender',
			'taxonomy'         => 'lez_gender', //Enter Taxonomy Slug
			'type'             => 'taxonomy_select',
			'default'          => 'cisgender',
			'show_option_none' => false,
			'remove_default'   => 'true'
		) );
		// Field: Character Sexual Orientation
		$cmb_charside->add_field( array(
			'name'             => 'Sexuality',
			'desc'             => 'Character\'s sexual orientation',
			'id'               => $prefix . 'sexuality',
			'taxonomy'         => 'lez_sexuality', //Enter Taxonomy Slug
			'type'             => 'taxonomy_select',
			'default'          => 'homosexual',
			'show_option_none' => false,
			'remove_default'   => 'true'
		) );
		// Field: Year of Death (if applicable)
		$cmb_charside->add_field( array(
			'name'        => 'Date of Death',
			'desc'        => 'If the character is dead, select when they died.',
			'id'          => $prefix .'death_year',
			'type'        => 'text_date',
			'date_format' => 'm/d/Y',
			'repeatable'  => true, // Sara Lance may die again, and we'll have to figure this out
		) );
	}

	/*
	 * Remove Metaboxes we use elsewhere
	 */
	function remove_metaboxes() {
		remove_meta_box( 'authordiv', 'post_type_characters', 'normal' );
		remove_meta_box( 'postexcerpt' , 'post_type_characters' , 'normal' );
	}

	/*
	 * Create Custom Columns
	 * Used by quick edit, etc
	 */
	public function manage_posts_columns($columns) {
		$columns['cpt-shows']         = 'TV Show(s)';
		$columns['postmeta-roletype'] = 'Role Type';
		return $columns;
	}

	/*
	 * Add Custom Column Content
	 */
	public function manage_posts_custom_column( $column, $post_id ) {
		// Since SOME characters have multiple shows, we force this to be an array
		if ( !is_array( get_post_meta( $post_id, 'lezchars_show', true ) ) ) {
			$character_show_IDs = array( get_post_meta( $post_id, 'lezchars_show', true ) );
		} else {
			$character_show_IDs = get_post_meta( $post_id, 'lezchars_show', true );
		}

		// Show Title is an array to handle fucking commas
		$show_title = array();

		foreach ( $character_show_IDs as $character_show_ID ) {
			array_push( $show_title, get_post( $character_show_ID )->post_title );
		}

		switch ( $column ) {
			case 'cpt-shows':
				echo implode(", ", $show_title );
				break;
			case 'postmeta-roletype':
				echo ucfirst(get_post_meta( $post_id, 'lezchars_type', true ));
				break;
		}
	}

	/*
	 * Make Custom Columns Sortable
	 */
	public function manage_edit_sortable_columns( $columns ) {
		unset( $columns['cpt-shows'] );                  // Don't allow sort by shows
		$columns['postmeta-roletype']      = 'role';     // Allow sort by role
		$columns['taxonomy-lez_gender']    = 'gender';   // Allow sort by gender identity
		$columns['taxonomy-lez_sexuality'] = 'sex';      // Allow sort by gender identity
		return $columns;
	}

	/*
	 * Create Simple Columns Sortability
	 *
	 * Role
	 */
	public function columns_sortability_simple( $query ) {
		if( ! is_admin() ) return;
		if( ! is_admin() ) return;

		if ( $query->is_main_query() && ( $orderby = $query->get( 'orderby' ) ) ) {
	    	switch( $orderby ) {
				case 'role':
					$query->set( 'meta_key', 'lezchars_type' );
					$query->set( 'orderby', 'meta_value' );
					break;
			}
		}
	}

	/*
	 * Create columns sortability for sexuality
	 */
	public function columns_sortability_gender( $clauses, $wp_query ) {
		global $wpdb;

		if ( isset( $wp_query->query['orderby'] ) && 'gender' == $wp_query->query['orderby'] ) {

			$clauses['join'] .= <<<SQL
LEFT OUTER JOIN {$wpdb->term_relationships} ON {$wpdb->posts}.ID={$wpdb->term_relationships}.object_id
LEFT OUTER JOIN {$wpdb->term_taxonomy} USING (term_taxonomy_id)
LEFT OUTER JOIN {$wpdb->terms} USING (term_id)
SQL;

			$clauses['where']   .= " AND (taxonomy = 'lez_gender' OR taxonomy IS NULL)";
			$clauses['groupby']  = "object_id";
			$clauses['orderby']  = "GROUP_CONCAT({$wpdb->terms}.name ORDER BY name ASC) ";
			$clauses['orderby'] .= ( 'ASC' == strtoupper( $wp_query->get('order') ) ) ? 'ASC' : 'DESC';
		}
		return $clauses;
	}

	/*
	 * Create columns sortability for sexuality
	 */
	public function columns_sortability_sexuality( $clauses, $wp_query ) {

		global $wpdb;

		if ( isset( $wp_query->query['orderby'] ) && 'sex' == $wp_query->query['orderby'] ) {

			$clauses['join'] .= <<<SQL
LEFT OUTER JOIN {$wpdb->term_relationships} ON {$wpdb->posts}.ID={$wpdb->term_relationships}.object_id
LEFT OUTER JOIN {$wpdb->term_taxonomy} USING (term_taxonomy_id)
LEFT OUTER JOIN {$wpdb->terms} USING (term_id)
SQL;

			$clauses['where'] .= " AND (taxonomy = 'lez_sexuality' OR taxonomy IS NULL)";
			$clauses['groupby'] = "object_id";
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
			case 'cpt-shows':
				// Multiselect
				break;
			case 'postmeta-roletype':
				?>
				<fieldset class="inline-edit-col-left">
				<div class="inline-edit-col">
				<span class="title">Character Role</span>
					<input type="hidden" name="lez_roletype_noncename" id="lez_roletype_noncename" value="" />
					<select name='postmeta_lez_role' id='postmeta_lez_role'>
						<option class='lez_role-option' value='0'>(Undefined)</option>
						<?php
						foreach ( $this->character_roles as $roleslug => $rolename) {
							echo "<option class='lez_role-option' value='{$roleslug}'>{$rolename}</option>\n";
						}
							?>
					</select>
				</div>
				</fieldset>
				<?php
				break;
			case 'taxonomy-lez_gender':
				?>
				<fieldset class="inline-edit-col-left">
				<div class="inline-edit-col">
				<span class="title">Gender Identity</span>
					<input type="hidden" name="lez_gender_noncename" id="lez_gender_noncename" value="" />
					<?php
						$terms = get_terms( array( 'taxonomy' => 'lez_gender','hide_empty' => false ) );
					?>
					<select name='terms_lez_gender' id='terms_lez_gender'>
						<option class='lez_gender-option' value='0'>(Undefined)</option>
						<?php
						foreach ($terms as $term) {
							echo "<option class='lez_gender-option' value='{$term->name}'>{$term->name}</option>\n";
						}
							?>
					</select>
				</div>
				</fieldset>
				<?php
				break;
			case 'taxonomy-lez_sexuality':
				?>
				<fieldset class="inline-edit-col-left">
				<div class="inline-edit-col">
				<span class="title">Sexual Orientation</span>
					<input type="hidden" name="lez_sexuality_noncename" id="lez_sexuality_noncename" value="" />
					<?php
						$terms = get_terms( array( 'taxonomy' => 'lez_sexuality','hide_empty' => false ) );
					?>
					<select name='terms_lez_sexuality' id='terms_lez_sexuality'>
						<option class='lez_sexuality-option' value='0'>(Undefined)</option>
						<?php
						foreach ($terms as $term) {
							echo "<option class='lez_sexuality-option' value='{$term->name}'>{$term->name}</option>\n";
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
		if ( ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) || ( isset( $_POST['post_type'] ) &&  'post_type_characters' != $_POST['post_type'] ) || !current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		}
		$post = get_post($post_id);

		// RoleType
		if ( isset($_POST['postmeta_lez_role']) && ($post->post_type != 'revision') ) {
			$lez_roletype = esc_attr($_POST['postmeta_lez_role']);
			if ( array_key_exists( $lez_roletype, $this->character_roles ) ) {
				update_post_meta( $post_id, 'lezchars_type', $lez_roletype );
			}
		}

		// Sexuality
		if ( isset($_POST['terms_lez_sexuality']) && ($post->post_type != 'revision') ) {
			$lez_sexuality_term = esc_attr($_POST['terms_lez_sexuality']);
			$term = term_exists( $lez_sexuality_term, 'lez_sexuality');
			if ( $term !== 0 && $term !== null) {
				wp_set_object_terms( $post_id, $lez_sexuality_term, 'lez_sexuality' );
			}
		}

		// Gender
		if ( isset($_POST['terms_lez_gender']) && ($post->post_type != 'revision') ) {
			$lez_gender_term = esc_attr($_POST['terms_lez_gender']);
			$term = term_exists( $lez_gender_term, 'lez_gender');
			if ( $term !== 0 && $term !== null) {
				wp_set_object_terms( $post_id, $lez_gender_term, 'lez_gender' );
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
		if ( is_null($current_screen) || ($current_screen->id !== 'edit-post_type_characters') || ($current_screen->post_type !== 'post_type_characters') ) return;
		?>
		<script type="text/javascript">
		<!--

		function set_inline_lwtv_quick_edit_defaults( sexualitySet, genderSet, roleSet, nonce ) {
			// revert Quick Edit menu so that it refreshes properly
			inlineEditPost.revert();
			var sexualityInput = document.getElementById('terms_lez_sexuality');
			var genderInput	= document.getElementById('terms_lez_gender');
			var roleInput	= document.getElementById('postmeta_lez_role');
			var nonceInput	 = document.getElementById('lez_sexuality_noncename');
			nonceInput.value   = nonce;

			// Set Sexuality Option
			for (i = 0; i < sexualityInput.options.length; i++) {
				if (sexualityInput.options[i].value == sexualitySet) {
					sexualityInput.options[i].setAttribute("selected", "selected");
				} else { sexualityInput.options[i].removeAttribute("selected"); }
			}

			// Set Gender Option
			for (i = 0; i < genderInput.options.length; i++) {
				if (genderInput.options[i].value == genderSet) {
					genderInput.options[i].setAttribute("selected", "selected");
				} else { genderInput.options[i].removeAttribute("selected"); }
			}

			// Set Role Option
			for (i = 0; i < roleInput.options.length; i++) {
				if (roleInput.options[i].value == roleSet) {
					roleInput.options[i].setAttribute("selected", "selected");
				} else { roleInput.options[i].removeAttribute("selected"); }
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
	public function quick_edit_link ($actions, $post) {
		global $current_screen;
		if (($current_screen->id != 'edit-post_type_characters') || ($current_screen->post_type != 'post_type_characters')) return $actions;

		$nonce = wp_create_nonce( 'lez_sexuality_'.$post->ID);
		$sex_terms = wp_get_post_terms( $post->ID, 'lez_sexuality', array( 'fields' => 'all' ) );
		$gender_terms = wp_get_post_terms( $post->ID, 'lez_gender', array( 'fields' => 'all' ) );
		$role_term = get_post_meta( $post->ID, 'lezchars_type', true );

		$actions['inline hide-if-no-js'] = '<a href="#" class="editinline" title="';
		$actions['inline hide-if-no-js'] .= esc_attr( __( 'Edit this item inline' ) ) . '" ';
		$actions['inline hide-if-no-js'] .= " onclick=\"set_inline_lwtv_quick_edit_defaults('{$sex_terms[0]->name}', '{$gender_terms[0]->name}', '{$role_term}', '{$nonce}')\">";
		$actions['inline hide-if-no-js'] .= __( 'Quick&nbsp;Edit' );
		$actions['inline hide-if-no-js'] .= '</a>';
		return $actions;
	}

	/*
	 * Extra Meta Variables for Yoast and Characters
	 *
	 * List of actors who played a character, for use on character pages
	 */
	public function lwtv_retrieve_actors_replacement( ) {
		if ( !is_array (get_post_meta( get_the_ID(), 'lezchars_actor', true)) ) {
			$actors = array( get_post_meta( get_the_ID(), 'lezchars_actor', true) );
		} else {
			$actors = get_post_meta( get_the_ID(), 'lezchars_actor', true);
		}
		return implode(", ", $actors);
	}

	/*
	 * Extra Meta Variables for Yoast and Characters
	 *
	 * List of shows featuring a character, for use on character pages
	 */
	function lwtv_retrieve_shows_replacement( ) {
		if ( !is_array (get_post_meta( get_the_ID(), 'lezchars_show', true)) ) {
			$shows_ids = array( get_post_meta( get_the_ID(), 'lezchars_show', true) );
		} else {
			$shows_ids = get_post_meta( get_the_ID(), 'lezchars_show', true);
		}

		$shows_titles = array();
		foreach ( $shows_ids as $show ) {
			$post_object = get_post( $show );
			array_push( $shows_titles, '"'. $post_object->post_title .'"' );
		}
		return implode(", ", $shows_titles);
	}

	/*
	 * Extra Replacement Functions for Yoast SEO
	 */
	public function yoast_seo_register_extra_replacements() {
		wpseo_register_var_replacement( '%%actors%%', array( $this, 'lwtv_retrieve_actors_replacement' ), 'basic', 'A list of actors who played the character, separated by commas.' );
		wpseo_register_var_replacement( '%%shows%%', array( $this, 'lwtv_retrieve_shows_replacement' ), 'basic', 'A list of shows the character was on, separated by commas.' );
	}

	/*
	 * AMP
	 */
	public function amp_init() {
	    add_post_type_support( 'post_type_characters', AMP_QUERY_VAR );
	}

	/*
	 * Add to 'Right Now'
	 */
	public function dashboard_glance_items() {
		foreach ( array( 'post_type_characters' ) as $post_type ) {
			$num_posts = wp_count_posts( $post_type );
			if ( $num_posts && $num_posts->publish ) {
				if ( 'post_type_characters' == $post_type ) {
					$text = _n( '%s Character', '%s Characters', $num_posts->publish );
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
			#adminmenu #menu-posts-post_type_characters div.wp-menu-image:before, #dashboard_right_now li.post_type_characters-count a:before {
				content: '\\f484';
				margin-left: -1px;
			}
		</style>";
	}
}

new LWTV_CPT_Characters();