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
			'recurring' => 'Recurring Character',
			'guest'     => 'Guest Character',
		);

		add_action( 'admin_init', array( $this, 'admin_init') );

		add_action( 'init', array( $this, 'init') );
		add_action( 'init', array( $this, 'create_post_type'), 0 );
		add_action( 'init', array( $this, 'create_taxonomies'), 0 );

		add_action( 'amp_init', array( $this, 'amp_init' ) );
		add_action( 'cmb2_init', array( $this, 'cmb2_metaboxes') );
		add_action( 'admin_menu', array( $this,'remove_metaboxes' ) );

		add_action( 'wpseo_register_extra_replacements', array( $this, 'yoast_seo_register_extra_replacements' ) );
	}

	/**
	 * Admin Init
	 */
	public function admin_init() {
		add_action( 'admin_head', array($this, 'admin_css') );

		add_filter( 'manage_post_type_characters_posts_columns', array( $this, 'manage_posts_columns' ) );
		add_action( 'manage_post_type_characters_posts_custom_column', array( $this, 'manage_posts_custom_column' ), 10, 2 );
		add_filter( 'manage_edit-post_type_characters_sortable_columns', array( $this, 'manage_edit_sortable_columns' ) );

		add_filter( 'posts_clauses', array( $this, 'columns_sortability_sexuality' ), 10, 2 );
		add_filter( 'posts_clauses', array( $this, 'columns_sortability_gender' ), 10, 2 );
		add_filter( 'posts_clauses', array( $this, 'columns_sortability_romantic' ), 10, 2 );

		add_action( 'quick_edit_custom_box',  array( $this, 'quick_edit_custom_box' ), 10, 2 );
		add_action( 'save_post', array( $this, 'quick_edit_save_post' ) );
		add_action( 'admin_footer', array( $this, 'quick_edit_js') );
		add_filter( 'post_row_actions', array( $this, 'quick_edit_link' ), 10, 2 );

		add_action( 'dashboard_glance_items', array( $this, 'dashboard_glance_items' ) );
	}

	/**
	 *  Init
	 */
	public function init() {
		// Force saving data to convert select2 saved data to a taxonomy
		$post_id = ( isset( $_GET['post'] ) )? $_GET['post'] : 0 ;

		// Cliches
		LWTV_CMB2_Addons::select2_taxonomy_save( $post_id, 'lezchars_cliches', 'lez_cliches' );

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
			'has_archive'         => 'characters',
			'rewrite'             => array( 'slug' => 'character' ),
			'delete_with_user'    => false,
			'exclude_from_search' => false,
		);
		register_post_type( 'post_type_characters', $args );
	}

	/*
	 * Custom Taxonomies
	 *
	 */
	public function create_taxonomies() {

		$taxonomies = array (
			'cliché'               => 'cliches',
			'gender'               => 'gender',
			'sexual orientation'   => 'sexuality',
			'romantic orientation' => 'romantic',
		);

		foreach ( $taxonomies as $pretty => $slug ) {
			// Labels for taxonomy
			$labels = array(
				'name'                       => ucwords( $pretty ) . 's',
				'singular_name'              => ucwords( $pretty ) ,
				'search_items'               => 'Search ' . ucwords( $pretty ) . 's',
				'popular_items'              => 'Popular ' . ucwords( $pretty ) . 's',
				'all_items'                  => 'All' . ucwords( $pretty ) . 's',
				'edit_item'                  => 'Edit ' . ucwords( $pretty ),
				'update_item'                => 'Update ' . ucwords( $pretty ),
				'add_new_item'               => 'Add New ' . ucwords( $pretty ),
				'new_item_name'              => 'New' . ucwords( $pretty ) . 'Name',
				'separate_items_with_commas' => 'Separate ' . $pretty . 's with commas',
				'add_or_remove_items'        => 'Add or remove' . $pretty . 's',
				'choose_from_most_used'      => 'Choose from the most used ' . $pretty . 's',
				'not_found'                  => 'No ' . ucwords( $pretty ) . 's found.',
				'menu_name'                  => ucwords( $pretty ) . 's',
			);
			//parameters for the new taxonomy
			$arguments = array(
				'hierarchical'          => false,
				'labels'                => $labels,
				'show_ui'               => true,
				'show_in_rest'          => true,
				'show_admin_column'     => true,
				'update_count_callback' => '_update_post_term_count',
				'query_var'             => true,
				'show_in_nav_menus'     => true,
				'rewrite'               => array( 'slug' => rtrim( $slug, 's' ) ),
			);
			// Taxonomy name
			$taxonomyname = 'lez_' . $slug;

			// Register taxonomy
			register_taxonomy( $taxonomyname, 'post_type_characters', $arguments );
		}
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
			'id'           => 'chars_metabox',
			'title'        => 'Character Details',
			'object_types' => array( 'post_type_characters' ),
			'context'      => 'normal',
			'priority'     => 'high',
			'show_in_rest' => true,
			'show_names'   => true, // Show field names on the left
		) );
		// Field: Character Clichés
		$field_cliches = $cmb_characters->add_field( array(
			'name'              => 'Character Clichés',
			'id'                => $prefix . 'cliches',
			'taxonomy'          => 'lez_cliches',
			'type'              => 'pw_multiselect',
			'select_all_button' => false,
			'remove_default'    => 'true',
			'options'           => LWTV_CMB2_Addons::select2_get_options_array_tax( 'lez_cliches' ),
			'attributes'        => array(
				'placeholder' => 'Common clichés ...'
			),
		) );
		// Field: Actor Name(s)
		$field_actors = $cmb_characters->add_field( array(
			'name'       => 'Actor Name',
			'desc'       => 'Include identifying features (in parens) for multiple actors',
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
		// Field Group: Character Show information
		// Made repeatable since each show might have a separate role. Yikes...
		$group_shows = $cmb_characters->add_field( array(
			'id'          => $prefix . 'show_group',
			'type'        => 'group',
			'repeatable'  => true,
			'options'     => array(
				'group_title'   => 'Show #{#}',
				'add_button'    => 'Add Another Show',
				'remove_button' => 'Remove Show',
				'sortable'      => true,
			),
		) );
		// Field: Show Name
		$field_shows = $cmb_characters->add_group_field( $group_shows, array(
			'name'             => 'TV Show',
			'id'               => 'show',
			'type'             => 'select',
			'show_option_none' => true,
			'default'          => 'custom',
			'options_cb'       => array( $this, 'cmb2_get_shows_options' ),
		) );
		// Field: Character Type
		$field_chartype = $cmb_characters->add_group_field( $group_shows, array(
			'name'             => 'Character Type',
			'desc'             => 'Mains are in credits. Recurring have their own plots. Guests show up once or twice.',
			'id'               => 'type',
			'type'             => 'select',
			'show_option_none' => true,
			'default'          => 'custom',
			'options'          => $this->character_roles,
		) );

		// Metabox Group: Quick Dropdowns
		$cmb_charside = new_cmb2_box( array(
			'id'           => 'charnotes_metabox',
			'title'        => 'Additional Data',
			'object_types' => array( 'post_type_characters' ),
			'context'      => 'side',
			'priority'     => 'default',
			'show_names'   => true, // Show field names on the left
			'show_in_rest' => true,
			'cmb_styles'   => false,
		) );
		// Field: Character Gender Idenity
		$field_gender = $cmb_charside->add_field( array(
			'name'             => 'Gender',
			'desc'             => 'Gender identity',
			'id'               => $prefix . 'gender',
			'taxonomy'         => 'lez_gender',
			'type'             => 'taxonomy_select',
			'default'          => 'cisgender',
			'show_option_none' => false,
			'remove_default'   => 'true'
		) );
		// Field: Character Sexual Orientation
		$field_sexuality = $cmb_charside->add_field( array(
			'name'             => 'Sexuality',
			'desc'             => 'Sexual orientation',
			'id'               => $prefix . 'sexuality',
			'taxonomy'         => 'lez_sexuality',
			'type'             => 'taxonomy_select',
			'default'          => 'homosexual',
			'show_option_none' => false,
			'remove_default'   => 'true'
		) );
		// Field: Character Romantic Orientation
		$field_romantic = $cmb_charside->add_field( array(
			'name'             => 'Romantic',
			'desc'             => 'Romantic orientation',
			'id'               => $prefix . 'romantic',
			'taxonomy'         => 'lez_romantic',
			'type'             => 'taxonomy_select',
			'default'          => 'homoromantic',
			'show_option_none' => false,
			'remove_default'   => 'true'
		) );
		// Field: Year of Death (if applicable)
		$field_death = $cmb_charside->add_field( array(
			'name'        => 'Date of Death',
			'desc'        => 'If the character is dead, select when they died.',
			'id'          => $prefix . 'death_year',
			'type'        => 'text_date',
			'date_format' => 'm/d/Y',
			'repeatable'  => true, // Sara Lance may die again, and we'll have to figure this out
		) );
		// Character Sidebar Grid
		if( !is_admin() ){
			return;
		} else {
			$grid_charside = new \Cmb2Grid\Grid\Cmb2Grid( $cmb_charside );
			$row = $grid_charside->addRow();
			$row->addColumns( array( $field_gender, $field_sexuality ) );
		}

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
	public function manage_posts_columns( $columns ) {
		$columns['cpt-shows']         = 'TV Show(s)';
		$columns['postmeta-roletype'] = 'Role Type';
		$columns['postmeta-death']    = 'Died';
		return $columns;
	}

	/*
	 * Add Custom Column Content
	 */
	public function manage_posts_custom_column( $column, $post_id ) {

		$character_show_IDs = get_post_meta( $post_id, 'lezchars_show_group', true );
		$show_title  = array();
		$role_array  = array();

		if ( $character_show_IDs !== '' ) {
			foreach ( $character_show_IDs as $each_show ) {

				$show = get_the_title( $each_show[ 'show' ] );
				$role = ( isset( $each_show[ 'type' ] )? ucfirst( $each_show[ 'type' ] ) : 'ERROR' );

				array_push( $show_title, $show );
				array_push( $role_array, $role );
			}
		}

		$character_death = get_post_meta( $post_id, 'lezchars_death_year', true );

		if ( empty( $character_death) ) $character_death = array( 'Alive' );

		switch ( $column ) {
			case 'cpt-shows':
				echo implode(", ", $show_title );
				break;
			case 'postmeta-roletype':
				echo implode(", ", $role_array );
				break;
			case 'postmeta-death':
				echo implode(", ", $character_death );
				break;
		}
	}

	/*
	 * Make Custom Columns Sortable
	 */
	public function manage_edit_sortable_columns( $columns ) {
		unset( $columns['cpt-shows'] );                  // Don't allow sort by shows
		unset( $columns['postmeta-roletype'] );          // Don't allow sort by role
		$columns['taxonomy-lez_gender']    = 'gender';   // Allow sort by gender identity
		$columns['taxonomy-lez_sexuality'] = 'sex';      // Allow sort by gender identity
		$columns['taxonomy-lez_romantic']  = 'romantic'; // Allow sort by gender identity
		return $columns;
	}

	/*
	 * Create columns sortability for gender
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
	 * Create columns sortability for romantic
	 */
	public function columns_sortability_romantic( $clauses, $wp_query ) {

		global $wpdb;

		if ( isset( $wp_query->query['orderby'] ) && 'sex' == $wp_query->query['orderby'] ) {

			$clauses['join'] .= <<<SQL
LEFT OUTER JOIN {$wpdb->term_relationships} ON {$wpdb->posts}.ID={$wpdb->term_relationships}.object_id
LEFT OUTER JOIN {$wpdb->term_taxonomy} USING (term_taxonomy_id)
LEFT OUTER JOIN {$wpdb->terms} USING (term_id)
SQL;

			$clauses['where'] .= " AND (taxonomy = 'lez_romantic' OR taxonomy IS NULL)";
			$clauses['groupby'] = "object_id";
			$clauses['orderby']  = "GROUP_CONCAT({$wpdb->terms}.name ORDER BY name ASC) ";
			$clauses['orderby'] .= ( 'ASC' == strtoupper( $wp_query->get('order') ) ) ? 'ASC' : 'DESC';
		}

		return $clauses;
	}
	
	/*
	 * Add Quick Edit Boxes
	 */
	public function quick_edit_custom_box( $column_name, $post_type ) {
		switch ( $column_name ) {
			case 'cpt-shows':
				// Multiselect
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
						foreach ( $terms as $term ) {
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
			case 'taxonomy-lez_romantic':
				?>
				<fieldset class="inline-edit-col-left">
				<div class="inline-edit-col">
				<span class="title">Romantic Orientation</span>
					<input type="hidden" name="lez_romantic_noncename" id="lez_romantic_noncename" value="" />
					<?php
						$terms = get_terms( array( 'taxonomy' => 'lez_romantic','hide_empty' => false ) );
					?>
					<select name='terms_lez_romantic' id='terms_lez_romantic'>
						<option class='lez_romantic-option' value='0'>(Undefined)</option>
						<?php
						foreach ($terms as $term) {
							echo "<option class='lez_romantic-option' value='{$term->name}'>{$term->name}</option>\n";
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
	public function quick_edit_save_post( $post_id ) {
		// Criteria for not saving: Auto-saves, not post_type_characters, can't edit
		if ( ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) || ( isset( $_POST['post_type'] ) &&  'post_type_characters' != $_POST['post_type'] ) || !current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		}
		$post = get_post($post_id);

		// Sexuality
		if ( isset( $_POST['terms_lez_sexuality'] ) && ( $post->post_type != 'revision' ) ) {
			$lez_sexuality_term = esc_attr( $_POST['terms_lez_sexuality'] );
			$term = term_exists( $lez_sexuality_term, 'lez_sexuality' );
			if ( $term !== 0 && $term !== null) {
				wp_set_object_terms( $post_id, $lez_sexuality_term, 'lez_sexuality' );
			}
		}

		// Gender
		if ( isset( $_POST['terms_lez_gender'] ) && ( $post->post_type != 'revision' ) ) {
			$lez_gender_term = esc_attr( $_POST['terms_lez_gender'] );
			$term = term_exists( $lez_gender_term, 'lez_gender' );
			if ( $term !== 0 && $term !== null) {
				wp_set_object_terms( $post_id, $lez_gender_term, 'lez_gender' );
			}
		}

		// Romantic
		if ( isset( $_POST['terms_lez_romantic'] ) && ( $post->post_type != 'revision' ) ) {
			$lez_gender_term = esc_attr( $_POST['terms_lez_romantic'] );
			$term = term_exists( $lez_gender_term, 'lez_romantic' );
			if ( $term !== 0 && $term !== null) {
				wp_set_object_terms( $post_id, $lez_gender_term, 'lez_romantic' );
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
		if ( is_null( $current_screen) || ( $current_screen->id !== 'edit-post_type_characters' ) || ( $current_screen->post_type !== 'post_type_characters' ) ) return;
		?>
		<script type="text/javascript">
		<!--

		function set_inline_lwtv_quick_edit_defaults( sexualitySet, genderSet, romanticSet, roleSet, nonce ) {
			// revert Quick Edit menu so that it refreshes properly
			inlineEditPost.revert();
			var sexualityInput = document.getElementById('terms_lez_sexuality');
			var genderInput    = document.getElementById('terms_lez_gender');
			var romanticInput  = document.getElementById('terms_lez_romantic');
			var nonceInput     = document.getElementById('lez_sexuality_noncename');
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

			// Set Romantic Option
			for (i = 0; i < romanticInput.options.length; i++) {
				if (romanticInput.options[i].value == romanticSet) {
					romanticInput.options[i].setAttribute("selected", "selected");
				} else { romanticInput.options[i].removeAttribute("selected"); }
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
	public function quick_edit_link ( $actions, $post ) {
		global $current_screen;
		if ( ( $current_screen->id != 'edit-post_type_characters' ) || ( $current_screen->post_type != 'post_type_characters' ) ) return $actions;

		$nonce          = wp_create_nonce( 'lez_sexuality_' . $post->ID );
		$sex_terms      = wp_get_post_terms( $post->ID, 'lez_sexuality', array( 'fields' => 'all' ) );
		$gender_terms   = wp_get_post_terms( $post->ID, 'lez_gender', array( 'fields' => 'all' ) );
		$romantic_terms = wp_get_post_terms( $post->ID, 'lez_romantic', array( 'fields' => 'all' ) );

		$actions['inline hide-if-no-js'] = '<a href="#" class="editinline" title="';
		$actions['inline hide-if-no-js'] .= esc_attr( __( 'Edit this item inline' ) ) . '" ';
		$actions['inline hide-if-no-js'] .= " onclick=\"set_inline_lwtv_quick_edit_defaults('{$sex_terms[0]->name}', '{$gender_terms[0]->name}', '{$nonce}')\">";
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
		if ( !is_array ( get_post_meta( get_the_ID(), 'lezchars_actor', true ) ) ) {
			$actors = array( get_post_meta( get_the_ID(), 'lezchars_actor', true ) );
		} else {
			$actors = get_post_meta( get_the_ID(), 'lezchars_actor', true );
		}
		return implode(", ", $actors);
	}

	/*
	 * Extra Meta Variables for Yoast and Characters
	 *
	 * List of shows featuring a character, for use on character pages
	 */
	function lwtv_retrieve_shows_replacement() {

		global $post;

		$shows_ids = get_post_meta( $post->ID, 'lezchars_show_group', true );
		$shows_titles = array();

		if ( $shows_ids !== '' && !is_null( $shows_ids ) ) {
			foreach ( $shows_ids as $each_show ) {
				array_push( $shows_titles, get_the_title( $each_show['show'] ) );
			}
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


	/**
	 * list_characters function.
	 *
	 * @access public
	 * @static
	 * @param mixed $show_id
	 * @param string $output (default: 'query')
	 * @return void
	 */
	public static function list_characters( $show_id, $output = 'query' ) {
		$charactersloop = LWTV_Loops::post_meta_query( 'post_type_characters', 'lezchars_show_group', $show_id, 'LIKE' );

		$characters = array();
		$charcount  = 0;
		$deadcount  = 0;

		// Store as array to defeat some stupid with counting and prevent querying the database too many times
		if ( $charactersloop->have_posts() ) {
			while ( $charactersloop->have_posts() ) {
				$charactersloop->the_post();
				$char_id = get_the_ID();
				$shows_array = get_post_meta( $char_id, 'lezchars_show_group', true );

				// If the character is in this show, AND a published character
				// we will pass the following data to the character template
				// to determine what to display

				if ( $shows_array !== '' && !empty( $shows_array ) && get_post_status ( $char_id ) == 'publish' ) {
					foreach( $shows_array as $char_show ) {
						if ( $char_show['show'] == $show_id ) {
							$characters[$char_id] = array(
								'id'        => $char_id,
								'title'     => get_the_title( $char_id ),
								'url'       => get_the_permalink( $char_id ),
								'content'   => get_the_content( $char_id ),
								'shows'     => $shows_array,
								'show_from' => $show_id,
							);
							$charcount++;

							if ( has_term( 'dead', 'lez_cliches', $char_id) ) {
								$deadcount++;
							}
						}
					}
				}
			}
			wp_reset_query();
		}

		switch( $output ) {
			case 'count':
				$return = $charcount;
				break;
			case 'dead':
				$return = $deadcount;
				break;
			case 'query':
				$return = $characters;
				break;
		}

		return $return;

	}

}

new LWTV_CPT_Characters();