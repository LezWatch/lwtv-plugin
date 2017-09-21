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

		add_action( 'init', array( $this, 'init') );
		add_action( 'init', array( $this, 'create_post_type'), 0 );
		add_action( 'init', array( $this, 'create_taxonomies'), 0 );

		add_action( 'amp_init', array( $this, 'amp_init' ) );

		add_action( 'post_submitbox_misc_actions', array( $this, 'post_page_metabox' ) );
	}

	/**
	 *  Init
	 */
	public function init() {

		// Things that only run for this post type
		$post_id   = ( isset( $_GET['post'] ) )? $_GET['post'] : 0 ;
		if ( $post_id !== 0 && is_admin() ) {
			$post_type = ( isset( $_GET['post_type'] ) )? $_GET['post_type'] : 0 ;
			switch ( $post_type ) {
				case 'post_type_shows':
					// Filter buttons not needed on the teeny MCE
					add_filter( 'teeny_mce_buttons', array($this, 'teeny_mce_buttons' ) );
					// Filter text editor quicktags (commented out until it runs on CMB2 only)
					//add_filter( 'quicktags_settings', array( $this, 'quicktags_settings' ) );

					// Force saving data to convert select2 saved data to a taxonomy
					LWTV_CMB2_Addons::select2_taxonomy_save( $post_id, 'lezshows_tropes', 'lez_tropes' );
					LWTV_CMB2_Addons::select2_taxonomy_save( $post_id, 'lezshows_tvgenre', 'lez_genres' );
					break;
			}
		}
	}

	/**
	 * Admin Init
	 */
	public function admin_init() {
		
		if ( class_exists( 'VarnishPurger' ) ) $this->varnish_purge = new VarnishPurger();
		
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

		add_action( 'do_update_show_meta', array( $this, 'update_show_meta' ), 10, 2 );
		add_action( 'save_post_post_type_shows', array( $this, 'update_show_meta' ), 10, 3 );
		add_action( 'save_post_post_type_characters', array( $this, 'update_show_meta_from_chars' ), 10, 3 );

		add_action( 'dashboard_glance_items', array( $this, 'dashboard_glance_items' ) );
	}

	/**
	 * Remove some Text Editor buttons
	 */
	public function quicktags_settings( $buttons ) {
		$remove = array( 'del', 'ins', 'img', 'code', 'block' );
		$buttons['buttons'] = implode( ',', array_diff ( explode( ',', $buttons['buttons'] ) , $remove ) );
		return $buttons;
	}

	/**
	 * Remove some TEENY MCE buttons (not TinyMCE, TeenyMCE)
	 */
	public function teeny_mce_buttons( $buttons ) {
		$remove = array( 'alignleft', 'aligncenter', 'alignright', 'undo', 'redo', 'fullscreen' );
		return array_diff( $buttons, $remove );
	}

	/**
	 * Create Custom Post Type
	 */
	public function create_post_type() {

		$labels = array(
			'name'                  => 'TV Shows',
			'singular_name'         => 'TV Show',
			'menu_name'             => 'TV Shows',
			'name_admin_bar'        => 'TV Show',
			'add_new_item'          => 'Add New TV Show',
			'edit_item'             => 'Edit TV Show',
			'new_item'              => 'New TV Show',
			'view_item'             => 'View TV Show',
			'all_items'             => 'All TV Shows',
			'search_items'          => 'Search TV Shows',
			'not_found'             => 'No TV Shows found',
			'not_found_in_trash'    => 'No TV Shows found in Trash',
			'update_item'           => 'Update TV Show',
			'featured_image'        => 'TV Show Image',
			'set_featured_image'    => 'Set TV Show image',
			'remove_featured_image' => 'Remove TV Show image',
			'use_featured_image'    => 'Use as TV Show image',
			'archives'              => 'TV Show archives',
			'insert_into_item'      => 'Insert into TV Show',
			'uploaded_to_this_item' => 'Uploaded to this TV Show',
			'filter_items_list'     => 'Filter TV Show list',
			'items_list_navigation' => 'TV Show list navigation',
			'items_list'            => 'TV Show list',
		);
		$args = array(
			'label'               => 'post_type_shows',
			'labels'              => $labels,
			'description'         => 'TV Shows',
			'public'              => true,
			'exclude_from_search' => false,
			'show_in_rest'        => true,
			'rest_base'           => 'show',
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-video-alt',
			'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'genesis-cpt-archives-settings', 'genesis-seo', 'revisions' ),
			'has_archive'         => 'shows',
			'rewrite'             => array( 'slug' => 'show' ),
			'delete_with_user'    => false,

		);
		register_post_type( 'post_type_shows', $args );
	}

	/*
	 * Create Custom Taxonomies
	 */
	public function create_taxonomies() {

		$taxonomies = array (
			'TV station' => 'stations',
			'trope'      => 'tropes',
			'format'     => 'formats',
			'genre'      => 'genres',
			'nation'    => 'country',
		);

		foreach ( $taxonomies as $pretty => $slug ) {
			// Labels for taxonomy
			$labels = array(
				'name'                       => ucwords( $pretty ) . 's',
				'singular_name'              => ucwords( $pretty ),
				'search_items'               => 'Search ' . ucwords( $pretty ) . 's',
				'popular_items'              => 'Popular ' . ucwords( $pretty ) . 's',
				'all_items'                  => 'All' . ucwords( $pretty ) . 's',
				'parent_item'                => null,
				'parent_item_colon'          => null,
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
				'rest_base'             => rtrim( $slug, 's' ),
				'rest_controller_class' => 'WP_REST_Terms_Controller',
				'rewrite'               => array( 'slug' => rtrim( $slug, 's' ) ),
			);
			// Taxonomy name
			$taxonomyname = 'lez_' . $slug;

			// Register taxonomy
			register_taxonomy( $taxonomyname, 'post_type_shows', $arguments );
		}
	}

	/*
	 * Create Custom Columns
	 * Used by quick edit, etc
	 */
	public function manage_posts_columns( $columns ) {
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
		$columns['shows-queercount']	 = 'queers';  // Allow sort by queers
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
	public function quick_edit_custom_box( $column_name, $post_type ) {
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
						foreach ( $terms as $term ) {
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
	public function quick_edit_save_post( $post_id) {
		// Criteria for not saving: Auto-saves, not post_type_characters, can't edit
		if ( ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) || ( isset( $_POST['post_type'] ) &&  'post_type_shows' != $_POST['post_type'] ) || !current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		}
		$post = get_post( $post_id );

		// Formats
		if ( isset( $_POST['terms_lez_formats'] ) && ( $post->post_type != 'revision' ) ) {
			$lez_formats_term = esc_attr( $_POST['terms_lez_formats'] );
			$term = term_exists( $lez_formats_term, 'lez_formats' );
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
		if ( is_null( $current_screen ) || ( $current_screen->id !== 'edit-post_type_shows' ) || ( $current_screen->post_type !== 'post_type_shows' ) ) return;
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
	public function quick_edit_link( $actions, $post ) {
		global $current_screen;
		if ( ( $current_screen->id != 'edit-post_type_shows' ) || ( $current_screen->post_type != 'post_type_shows' ) ) return $actions;

		$nonce = wp_create_nonce( 'lez_formats_'.$post->ID );
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
	 * This will update the following metakeys on save:
	 *  - lezshows_char_count     Number of characters
	 *  - lezshows_dead_count     Number of dead characters
	 *  - lezshows_score_chars    Percentage score of character survival
	 *  - lezshows_score_ratings  Percentage score of show data
	 *
	 * @param int $post_id The post ID.
	 * @param post $post The post object.
	 * @param bool $update Whether this is an existing post being updated or not.
	 */
	public function update_show_meta( $post_id ) {

		// unhook this function so it doesn't loop infinitely
		remove_action( 'save_post_post_type_shows', array( $this, 'update_show_meta' ) );

		// Count characters
			$number_chars = $this->count_queers( $post_id, 'count' );
			update_post_meta( $post_id, 'lezshows_char_count', $number_chars );

		// Count dead characters
			$number_dead = $this->count_queers( $post_id, 'dead' );
			update_post_meta( $post_id, 'lezshows_dead_count', $number_dead );

		// Count 'no cliche' characters
			$number_none = $this->count_queers( $post_id, 'none' );
			update_post_meta( $post_id, 'lezshows_none_count', $number_none );

		// Calculate percentage alive
			if ( $number_chars == 0 || $number_dead == 0 ) {
				$percent_alive = 1;
			} else {
				$percent_alive = ( ( $number_chars - $number_dead ) / $number_chars );
			}
			update_post_meta( $post_id, 'lezshows_score_chars', $percent_alive );

		// Calculate percentage of cliche free characters
			if ( $number_chars == 0 || $number_none == 0 ) {
				$percent_none = 0;
			} else {
				$percent_none = ( $number_none / $number_chars );
			}
			update_post_meta( $post_id, 'lezshows_score_chars', $percent_alive );

		// Calculate percentage of value for show
			$percent_rating = $this->score_show_ratings( $post_id );
			update_post_meta( $post_id, 'lezshows_score_ratings', $percent_rating );

		// Calculate the full score
			$percent_the_score = ( $percent_rating + $percent_alive + $percent_none ) / 3;
			update_post_meta( $post_id, 'lezshows_the_score', $percent_the_score );

		// Flush Varnish
		if ( class_exists( 'VarnishPurger' ) ) {
			// Generate list of URLs based on the show ID:
			$purgeurls = $this->varnish_purge->generate_urls( $post_id );
			
			// Add on the JSON API
			array_push( $purgeurls, 
				get_site_url() . '/wp-json/lwtv/?vhp-regex'
			);
			
			// Purge 'em all
			foreach ( $purgeurls as $url ) {
				$this->varnish_purge->purgeUrl( $url ) ;
			}
		}

		// re-hook this function
		add_action( 'save_post_post_type_shows', array( $this, 'update_show_meta' ) );
	}

	/*
	 * Save post meta for shows on CHARACTER update
	 *
	 * This will update the metakey 'lezshows_char_count' on save
	 *
	 * @param int $post_id The post ID.
	 */
	public function update_show_meta_from_chars( $post_id ) {

		$character_show_IDs = get_post_meta( $post_id, 'lezchars_show_group', true );

		if ( $character_show_IDs !== '' ) {
			foreach ( $character_show_IDs as $each_show ) {
				do_action( 'do_update_show_meta' , $each_show['show'] );
			}
		}
	}

	/**
	 * Calculate show rating.
	 *
	 * @access public
	 * @param mixed $post_id
	 * @return void
	 */
	public function score_show_ratings ( $post_id ) {
		return LWTV_Shows_Calculate::show_score( $post_id );
	}

	/*
	 * Count Queers
	 *
	 * This will update the metakey 'lezshows_char_count' on save
	 *
	 * @param int $post_id The post ID.
	 */
	public function count_queers( $post_id , $type = 'count' ) {
		return LWTV_Shows_Calculate::count_queers( $post_id, $type );
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

	/*
	 * Post Page Meta Box
	 * For listing critical information
	 */
	function post_page_metabox() {
		global $post;

		switch ( $post->post_type ) {
			case 'post_type_shows':
				$countqueers = get_post_meta( $post->ID, 'lezshows_char_count', true );
				$deadqueers  = get_post_meta( $post->ID, 'lezshows_dead_count', true );
				$score       = get_post_meta( $post->ID, 'lezshows_the_score', true );
				$loved       = ( get_post_meta( $post->ID, 'lezshows_worthit_show_we_love', true ) == 'on' )? 'Yes' : 'No';

				?>
				<div class="misc-pub-section lwtv misc-pub-lwtv">
					<span id="loved">Loved: <b><?php echo $loved ?></b></span>
				</div>
				<div class="misc-pub-section lwtv misc-pub-lwtv">
					<span id="characters">Characters: <b><?php echo $countqueers; ?></b> total
						<?php if ( $deadqueers ) { ?> / <b><?php echo $deadqueers; ?></b> dead<?php } ?>
					</span>
				</div>
				<div class="misc-pub-section lwtv misc-pub-lwtv">
					<span id="score">Score: <b><?php echo round( ( $score * 100 ), 2 ); ?>%</b></span>
				</div>
				<?php

				break;
		}
	}
	
	/**
	 * related_posts function.
	 *
	 * @access public
	 * @param mixed $slug
	 * @return void
	 */	
	public static function related_posts( $slug ) {
		return LWTV_Related_Posts::related_posts( $slug );
	}

	/**
	 * Echo content warning if needed.
	 *
	 * @access public
	 * @return void
	 */
	public static function echo_content_warning( $type = 'full' ) {
		LWTV_Shows_Display::echo_content_warning( $type );
	}

	/**
	 * display_worthit function.
	 *
	 * @access public
	 * @param string $show_id
	 * @param string $thumb_rating (default: 'meh')
	 * @return void
	 */
	public static function display_worthit( $show_id, $thumb_rating = 'Meh' ) {
		LWTV_Shows_Display::display_worthit( $show_id, $thumb_rating );
	}

	/**
	 * display_tropes function.
	 *
	 * @access public
	 * @param mixed $show_id
	 * @return void
	 */
	public static function display_tropes( $show_id ) {
		LWTV_Shows_Display::display_tropes( $show_id );
	}

	/**
	 * display_hearts function.
	 *
	 * @access public
	 * @param mixed $show_id
	 * @param string $realness (default: '0')
	 * @param string $quality (default: '0')
	 * @param string $screentime (default: '0')
	 * @return void
	 */
	public static function display_hearts( $show_id, $realness = '0', $quality = '0', $screentime = '0' ) {
		LWTV_Shows_Display::display_hearts( $show_id, $realness, $quality, $screentime );
	}


	/**
	 * display_amazon function.
	 * 
	 * @access public
	 * @static
	 * @param mixed $show_id
	 * @return void
	 */
	public static function display_amazon( $show_id ) {
		echo LWTV_Amazon::show_amazon( $show_id );
	}

}

// Include Sub Files
include_once( 'shows/calculations.php' );
include_once( 'shows/cmb2-metaboxes.php' );
include_once( 'shows/display.php' );
include_once( 'shows/related-posts.php' );

new LWTV_CPT_Shows();