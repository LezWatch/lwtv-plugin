<?php
/*
 * Custom Post Type for characters on LWTV
 *
 * @since 1.0
 * Author: Evan Herman, Tracy Levesque, Mika Epstein
 */

/**
 * Constants
 */

$lez_character_roles = array(
	'regular'   => 'Regular/Main Character',
	'recurring'	=> 'Recurring Character',
	'guest'	 	=> 'Guest Character',
);

/**
 * CSS tweaks
 */
add_action( 'admin_enqueue_scripts', 'lez_characters_scripts', 10 );
function lez_characters_scripts( $hook ) {
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

add_action( 'init', 'lez_characters_post_type', 0 );
function lez_characters_post_type() {
	$labels = array(
		'name'					=> _x( 'Characters', 'lezwatchtv' ),
		'singular_name'			=> _x( 'Character', 'lezwatchtv' ),
		'menu_name'				=> __( 'Characters', 'lezwatchtv' ),
		'parent_item_colon'		=> __( 'Parent Character:', 'lezwatchtv' ),
		'all_items'				=> __( 'All Characters', 'lezwatchtv' ),
		'view_item'				=> __( 'View Character', 'lezwatchtv' ),
		'add_new_item'			=> __( 'Add New Character', 'lezwatchtv' ),
		'add_new'				=> __( 'Add New', 'lezwatchtv' ),
		'edit_item'				=> __( 'Edit Character', 'lezwatchtv' ),
		'update_item'			=> __( 'Update Character', 'lezwatchtv' ),
		'search_items'			=> __( 'Search Characters', 'lezwatchtv' ),
		'not_found'				=> __( 'No characters found', 'lezwatchtv' ),
		'not_found_in_trash'	=> __( 'No characters in the Trash', 'lezwatchtv' ),
	);
	$args = array(
		'label'					=> __( 'post_type_characters', 'lezwatchtv' ),
		'description'			=> __( 'Characters', 'lezwatchtv' ),
		'labels'				=> $labels,
		'supports'				=> array( 'title', 'editor', 'thumbnail', 'genesis-cpt-archives-settings', 'genesis-seo', 'revisions' ),
		'hierarchical'			=> false,
		'public'				=> true,
		'show_ui'				=> true,
		'show_in_menu'			=> true,
		'show_in_nav_menus' 	=> true,
		'show_in_admin_bar' 	=> true,
	 	'rewrite' 				=> array( 'slug' => 'characters' ),
		'menu_icon'				=> 'dashicons-nametag',
		'menu_position'			=> 7,
		'can_export'			=> true,
		'has_archive'			=> true,
		'exclude_from_search'	=> false,
		'publicly_queryable'	=> true,
		'capability_type'		=> 'page',
	);
	register_post_type( 'post_type_characters', $args );
}

/*
 * Custom Taxonomies
 *
 */
add_action( 'init', 'lez_create_post_type_characters_taxonomies', 0 );
function lez_create_post_type_characters_taxonomies() {

	// CLICHES
	$names_cliches = array(
		'name'							=> _x( 'Clichés', 'lezwatchtv' ),
		'singular_name'					=> _x( 'Cliché', 'lezwatchtv' ),
		'search_items'					=> __( 'Search Clichés' ),
		'popular_items'					=> __( 'Popular Clichés' ),
		'all_items'						=> __( 'All Clichés' ),
		'parent_item'					=> null,
		'parent_item_colon'				=> null,
		'edit_item'						=> __( 'Edit Cliché' ),
		'update_item'					=> __( 'Update Cliché' ),
		'add_new_item'					=> __( 'Add New Cliché' ),
		'new_item_name'					=> __( 'New Cliché Name' ),
		'separate_items_with_commas'	=> __( 'Separate Clichés with commas' ),
		'add_or_remove_items'			=> __( 'Add or remove Clichés' ),
		'choose_from_most_used'			=> __( 'Choose from the most used Clichés' ),
		'not_found'						=> __( 'No Clichés found.' ),
		'menu_name'						=> __( 'Clichés' ),
	);
	//paramters for the new taxonomy
	$args_cliches = array(
		'hierarchical'			=> true,
		'labels'				=> $names_cliches,
		'show_ui'				=> true,
		'show_admin_column'	 	=> true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'			 	=> true,
		'rewrite'				=> array( 'slug' => 'cliches' ),
	);
	register_taxonomy( 'lez_cliches', 'post_type_characters', $args_cliches );

	// GENDER IDENTITY
	$names_gender = array(
		'name'							=> _x( 'Gender', 'lezwatchtv' ),
		'singular_name'					=> _x( 'Gender Identity', 'taxonomy singular name' ),
		'search_items'					=> __( 'Search Genders' ),
		'popular_items'					=> __( 'Popular Genders' ),
		'all_items'						=> __( 'All Genders' ),
		'parent_item'					=> null,
		'parent_item_colon'				=> null,
		'edit_item'						=> __( 'Edit Gender' ),
		'update_item'					=> __( 'Update Gender' ),
		'add_new_item'					=> __( 'Add New Gender' ),
		'new_item_name'					=> __( 'New Gender Name' ),
		'separate_items_with_commas'	=> __( 'Separate Genders with commas' ),
		'add_or_remove_items'			=> __( 'Add or remove Genders' ),
		'choose_from_most_used'			=> __( 'Choose from the most used Genders' ),
		'not_found'						=> __( 'No Genders found.' ),
		'menu_name'						=> __( 'Gender Identity' ),
	);
	$args_gender = array(
		'hierarchical'			=> false,
		'labels'				=> $names_gender,
		'public'				=> true,
		'show_ui'				=> true,
		'show_admin_column'		=> true,
		'show_in_nav_menus'		=> true,
		'show_in_quick_edit'	=> false,
		'show_tagcloud'			=> false,
		'rewrite'				=> array( 'slug' => 'gender' ),
	);
	register_taxonomy( 'lez_gender', 'post_type_characters', $args_gender );

	// SEXUALITY
	$names_sexuality = array(
		'name'							=> _x( 'Sexuality', 'lezwatchtv' ),
		'singular_name'					=> _x( 'Sexual Orientation', 'taxonomy singular name' ),
		'search_items'					=> __( 'Search Sexual Orientations' ),
		'popular_items'					=> __( 'Popular Sexual Orientations' ),
		'all_items'						=> __( 'All Sexual Orientations' ),
		'parent_item'					=> null,
		'parent_item_colon'				=> null,
		'edit_item'						=> __( 'Edit Sexual Orientation' ),
		'update_item'					=> __( 'Update Sexual Orientation' ),
		'add_new_item'					=> __( 'Add New Sexual Orientation' ),
		'new_item_name'					=> __( 'New Sexual Orientation Name' ),
		'separate_items_with_commas'	=> __( 'Separate Sexual Orientations with commas' ),
		'add_or_remove_items'			=> __( 'Add or remove Sexual Orientations' ),
		'choose_from_most_used'			=> __( 'Choose from the most used Sexual Orientations' ),
		'not_found'						=> __( 'No Sexual Orientations found.' ),
		'menu_name'						=> __( 'Sexual Orientation' ),
	);
	$args_sexuality = array(
		'hierarchical'			=> false,
		'labels'				=> $names_sexuality,
		'public'				=> true,
		'show_ui'				=> true,
		'show_admin_column'		=> true,
		'show_in_nav_menus'		=> true,
		'show_in_quick_edit'	=> false,
		'show_tagcloud'		 	=> false,
		'rewrite'				=> array( 'slug' => 'sexuality' ),
	);
	register_taxonomy( 'lez_sexuality', 'post_type_characters', $args_sexuality );

}

/*
 * Custom Meta Box section
 *
 * This relies fully on CMB2.
 *
 */

// This gets a list of all the shows.
function cmb2_get_post_type_shows_options() {
	return cmb2_get_post_options( array(
			'post_type' 	=> 'post_type_shows',
			'numberposts'	=> -1,
			'post_status'	=> array('publish', 'pending', 'draft', 'future'),
		) );
}

add_filter( 'cmb2_admin_init', 'lez_post_type_characters_metaboxes' );
function lez_post_type_characters_metaboxes() {

	global $lez_character_roles;

	// prefix for all custom fields
	$prefix = 'lezchars_';

	// This is just an array of all years from 1930 on (1930 being the year TV dramas started)
	$year_array = array();
	foreach ( range(date('Y'), '1930' ) as $x) {
		$year_array[$x] = $x;
	}

	// MetaBox Group: Character Details
	$cmb_characters = new_cmb2_box( array(
		'id'				=> 'chars_metabox',
		'title'				=> 'Character Details',
		'object_types'  	=> array( 'post_type_characters', ), // Post type
		'context'			=> 'normal',
		'priority'			=> 'high',
		'show_names'		=> true, // Show field names on the left
	) );
	// Field: Character Clichés
	$cmb_characters->add_field( array(
		'name'				=> 'Character Clichés',
		'id'				=> $prefix . 'cliches',
		'taxonomy'			=> 'lez_cliches', //Enter Taxonomy Slug
		'type'	 			=> 'taxonomy_multicheck',
		'select_all_button'	=> false,
	) );
	// Field: Actor Name
	$cmb_characters->add_field( array(
		'name'				=> 'Actor Name',
		'desc'				=> 'Include years (in parens) for multiple actors',
		'id'				=> $prefix . 'actor',
		'type'				=> 'text',
		'repeatable'		=> 'true',
	    'attributes'		=> array(
	        'autocomplete'		=> 'off',
	        'autocorrect'		=> 'off',
	        'autocapitalize'	=> 'off',
	        'spellcheck'		=> 'false',
	    ),
	) );
	// Field: Character Type
	$cmb_characters->add_field( array(
		'name'				=> 'Character Type',
		'desc'				=> 'Mains are in credits. Recurring have their own plots. Guests show up once or twice.',
		'id'				=> $prefix .'type',
		'type'				=> 'select',
		'show_option_none'	=> true,
		'default'			=> 'custom',
		'options'			=> $lez_character_roles,
	) );
	// Field: Show Name
	$cmb_characters->add_field( array(
		'name'				=> 'Show',
		'desc'				=> 'Select the show this character belongs to',
		'id'				=> $prefix . 'show',
		'type'				=> 'select',
		'repeatable'		=> 'true',
		'show_option_none'	=> true,
		'default'			=> 'custom',
		'options_cb'		=> 'cmb2_get_post_type_shows_options',
	) );

	// Metabox Group: Quick Dropdowns
	$cmb_charside = new_cmb2_box( array(
		'id'            	=> 'charnotes_metabox',
		'title'         	=> 'Additional Data',
		'object_types'  	=> array( 'post_type_characters', ), // Post type
		'context'       	=> 'side',
		'priority'      	=> 'default',
		'show_names'		=> true, // Show field names on the left
		'cmb_styles'		=> false,
	) );
	// Field: Character Gender Idenity
	$cmb_charside->add_field( array(
		'name'				=> 'Gender Identity',
		'desc'				=> 'Gender with which the character identifies',
		'id'				=> $prefix . 'gender',
		'taxonomy'			=> 'lez_gender', //Enter Taxonomy Slug
		'type'				=> 'taxonomy_select',
		'default' 			=> 'cisgender',
		'show_option_none'	=> false,
	) );
	// Field: Character Sexual Orientation
	$cmb_charside->add_field( array(
		'name'				=> 'Sexuality',
		'desc'				=> 'Character\'s sexual orientation',
		'id'				=> $prefix . 'sexuality',
		'taxonomy'			=> 'lez_sexuality', //Enter Taxonomy Slug
		'type'				=> 'taxonomy_select',
		'default' 			=> 'homosexual',
		'show_option_none'	=> false,
	) );
	// Field: Year of Death (if applicable)
	$cmb_charside->add_field( array(
		'name'				=> 'Year of Death',
		'desc'				=> 'If the character is dead, select what year they died.',
		'id'				=> $prefix .'death_year',
		'type'				=> 'select',
		'show_option_none'	=> true,
		'default'			=> 'custom',
		'options'			=> $year_array,
	) );
}

/*
 * Meta Box Adjustments
 *
 */

// Remove Metaboxes we use elsewhere
add_action( 'admin_menu', 'lez_remove_characters_metaboxes');
function lez_remove_characters_metaboxes() {
	remove_meta_box( 'tagsdiv-lez_gender', 'post_type_characters', 'side' );
	remove_meta_box( 'tagsdiv-lez_sexuality', 'post_type_characters', 'side' );
	remove_meta_box( 'tagsdiv-lez_cliches', 'post_type_characters', 'side' );
	remove_meta_box( 'lez_clichesdiv', 'post_type_characters', 'side' );
	remove_meta_box( 'authordiv', 'post_type_characters', 'normal' );
	remove_meta_box( 'postexcerpt' , 'post_type_characters' , 'normal' );

}

/*
 * Post List Pages
 * (custom columns, quick edit, etc)
 */

// Add Custom Column Headers
add_filter( 'manage_post_type_characters_posts_columns', 'lez_characters_set_custom_edit_columns' );
function lez_characters_set_custom_edit_columns($columns) {
	$columns['cpt-shows']			= 'TV Show(s)';
	$columns['postmeta-roletype']	= 'Role Type';
	return $columns;
}

// Add Custom Column Content
add_action( 'manage_post_type_characters_posts_custom_column' , 'lez_characters_custom_column', 10, 2 );
function lez_characters_custom_column( $column, $post_id ) {
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

// Make columns sortable
add_filter( 'manage_edit-post_type_characters_sortable_columns', 'lez_characters_sortable_columns' );
function lez_characters_sortable_columns( $columns ) {
	unset( $columns['cpt-shows'] ); 			 	// Don't allow sort by shows
	$columns['postmeta-roletype']		= 'role';	// Allow sort by role
	$columns['taxonomy-lez_gender']		= 'gender';	// Allow sort by gender identity
	$columns['taxonomy-lez_sexuality']	= 'sex';	// Allow sort by gender identity
    return $columns;
}

// Create Role Sortability
add_action( 'pre_get_posts', 'lez_characters_role_orderby' );
function lez_characters_role_orderby( $query ) {
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

// Create Gender Sortability
function lez_characters_gender_clauses( $clauses, $wp_query ) {
	global $wpdb;

	if ( isset( $wp_query->query['orderby'] ) && 'gender' == $wp_query->query['orderby'] ) {

		$clauses['join'] .= <<<SQL
LEFT OUTER JOIN {$wpdb->term_relationships} ON {$wpdb->posts}.ID={$wpdb->term_relationships}.object_id
LEFT OUTER JOIN {$wpdb->term_taxonomy} USING (term_taxonomy_id)
LEFT OUTER JOIN {$wpdb->terms} USING (term_id)
SQL;

		$clauses['where'] .= " AND (taxonomy = 'lez_gender' OR taxonomy IS NULL)";
		$clauses['groupby'] = "object_id";
		$clauses['orderby']  = "GROUP_CONCAT({$wpdb->terms}.name ORDER BY name ASC) ";
		$clauses['orderby'] .= ( 'ASC' == strtoupper( $wp_query->get('order') ) ) ? 'ASC' : 'DESC';
	}

	return $clauses;
}
add_filter( 'posts_clauses', 'lez_characters_gender_clauses', 10, 2 );

// Create Sexuality Sortability
function lez_characters_sexuality_clauses( $clauses, $wp_query ) {
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
add_filter( 'posts_clauses', 'lez_characters_sexuality_clauses', 10, 2 );

// Add quick Edit boxes
add_action('quick_edit_custom_box',  'lez_characters_quick_edit_add', 10, 2);
function lez_characters_quick_edit_add($column_name, $post_type) {
	global $lez_character_roles;

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
					foreach ($lez_character_roles as $roleslug => $rolename) {
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

// Allow quick edit boxes to save on editing
add_action('save_post', 'lez_characters_quick_edit_save');
function lez_characters_quick_edit_save($post_id) {
	global $lez_character_roles;

	// Criteria for not saving: Auto-saves, not post_type_characters, can't edit
	if ( ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) || ( isset( $_POST['post_type'] ) &&  'post_type_characters' != $_POST['post_type'] ) || !current_user_can( 'edit_page', $post_id ) ) {
		return $post_id;
	}
	$post = get_post($post_id);

	// RoleType
	if ( isset($_POST['postmeta_lez_role']) && ($post->post_type != 'revision') ) {
		$lez_roletype = esc_attr($_POST['postmeta_lez_role']);
		if ( array_key_exists( $lez_roletype, $lez_character_roles ) ) {
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

// Javascript to change 'defaults'
add_action('admin_footer', 'lez_characters_quick_edit_js');
function lez_characters_quick_edit_js() {
	global $current_screen;
	if ( is_null($current_screen) || ($current_screen->id !== 'edit-post_type_characters') || ($current_screen->post_type !== 'post_type_characters') ) return;
	?>
	<script type="text/javascript">
	<!--

	// Sexuality
	function set_inline_lez_quick_edit_defaults( sexualitySet, genderSet, roleSet, nonce ) {
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

// Calls the JS in the previous function
add_filter('post_row_actions', 'lez_characters_quick_edit_link', 10, 2);

function lez_characters_quick_edit_link($actions, $post) {
	global $current_screen;
	if (($current_screen->id != 'edit-post_type_characters') || ($current_screen->post_type != 'post_type_characters')) return $actions;

	$nonce = wp_create_nonce( 'lez_sexuality_'.$post->ID);
	$sex_terms = wp_get_post_terms( $post->ID, 'lez_sexuality', array( 'fields' => 'all' ) );
	$gender_terms = wp_get_post_terms( $post->ID, 'lez_gender', array( 'fields' => 'all' ) );
	$role_term = get_post_meta( $post->ID, 'lezchars_type', true );

	$actions['inline hide-if-no-js'] = '<a href="#" class="editinline" title="';
	$actions['inline hide-if-no-js'] .= esc_attr( __( 'Edit this item inline' ) ) . '" ';
	$actions['inline hide-if-no-js'] .= " onclick=\"set_inline_lez_quick_edit_defaults('{$sex_terms[0]->name}', '{$gender_terms[0]->name}', '{$role_term}', '{$nonce}')\">";
	$actions['inline hide-if-no-js'] .= __( 'Quick&nbsp;Edit' );
	$actions['inline hide-if-no-js'] .= '</a>';
	return $actions;
}


/*
 * Extra Meta Variables for Yoast and Characters
 *
 */

// List of actors who played a character, for use on character pages
function lez_retrieve_actors_replacement( ) {
	if ( !is_array (get_post_meta( get_the_ID(), 'lezchars_actor', true)) ) {
		$actors = array( get_post_meta( get_the_ID(), 'lezchars_actor', true) );
	} else {
		$actors = get_post_meta( get_the_ID(), 'lezchars_actor', true);
	}
	return implode(", ", $actors);
}

// List of shows featuring a character, for use on character pages
function lez_retrieve_shows_replacement( ) {
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

// The actual replacement function
function lez_chars_register_yoast_extra_replacements() {
	wpseo_register_var_replacement( '%%actors%%', 'lez_retrieve_actors_replacement', 'basic', 'A list of actors who played the character, separated by commas.' );
	wpseo_register_var_replacement( '%%shows%%', 'lez_retrieve_shows_replacement', 'basic', 'A list of shows the character was on, separated by commas.' );
}
add_action( 'wpseo_register_extra_replacements', 'lez_chars_register_yoast_extra_replacements' );

/*
 * AMP
 */

add_action( 'amp_init', 'lez_amp_add_chars_cpt' );
function lez_amp_add_chars_cpt() {
    add_post_type_support( 'post_type_characters', AMP_QUERY_VAR );
}
