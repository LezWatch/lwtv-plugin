<?php
/**
 * Class LWTV_CMB2_Attached_Posts_Field
 */
class LWTV_CMB2_Attached_Posts_Field {

	/**
	 * Current version number
	 */
	const VERSION = CMB2_ATTACHED_POSTS_FIELD_VERSION;

	/**
	 * @var LWTV_CMB2_Attached_Posts_Field
	 */
	protected static $single_instance = null;

	/**
	 * CMB2_Field object
	 *
	 * @var CMB2_Field
	 */
	protected $field;

	/**
	 * Whether to output the type label.
	 * Determined when multiple post types exist in the query_args field arg.
	 *
	 * @var bool
	 */
	protected $do_type_label = false;

	/**
	 * Creates or returns an instance of this class.
	 * @since  0.1.0
	 * @return LWTV_CMB2_Attached_Posts_Field A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	/**
	 * Initialize the plugin by hooking into CMB2
	 */
	protected function __construct() {
		add_action( 'cmb2_render_custom_attached_posts', array( $this, 'render' ), 10, 5 );
		add_action( 'cmb2_pre_field_display_custom_attached_posts', array( $this, 'display_render' ), 20, 5 );
		add_action( 'cmb2_sanitize_custom_attached_posts', array( $this, 'sanitize' ), 10, 2 );
		add_action( 'cmb2_attached_posts_field_add_find_posts_div', array( $this, 'add_find_posts_div' ) );
		add_action( 'cmb2_after_init', array( $this, 'ajax_find_posts' ) );
	}

	/**
	 * Add a CMB custom field to allow for the selection of multiple posts
	 * attached to a single page
	 */
	public function render( $field, $escaped_value, $object_id, $object_type, $field_type ) {
		self::setup_scripts();
		$this->field         = $field;
		$this->do_type_label = false;

		if ( ! is_admin() ) {
			// Will need custom styling!
			// @todo add styles for front-end
			require_once ABSPATH . 'wp-admin/includes/template.php';
			do_action( 'cmb2_attached_posts_field_add_find_posts_div' );
		} else {
			// markup needed for modal
			add_action( 'admin_footer', 'find_posts_div' );
		}

		$query_args  = (array) $this->field->options( 'query_args' );
		$post_status = array( 'publish' );

		$args = wp_parse_args(
			$query_args,
			array(
				'post_type'      => 'post',
				'posts_per_page' => apply_filters( 'cmb2_attached_posts_per_page_filter', 50 ),
				'post_status'    => apply_filters( 'cmb2_attached_posts_status_filter', $post_status ),
			)
		);

		// Exclude this post from search
		if ( isset( $_POST['post'] ) ) {                               // phpcs:ignore WordPress.Security.NonceVerification
			$args['post__not_in'] = array( absint( $_POST['post'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
		}

		// loop through post types to get labels for all
		$post_type_labels = array();
		foreach ( (array) $args['post_type'] as $post_type ) {
			// Get post type object for attached post type.
			$post_type_obj = get_post_type_object( $post_type );

			// Continue if we don't have a label for the post type.
			if ( ! $post_type_obj || ! isset( $post_type_obj->labels->name ) ) {
				continue;
			}

			$args['orderby'] = isset( $args['orderby'] ) ? $args['orderby'] : 'name';
			$args['order']   = isset( $args['order'] ) ? $args['order'] : 'ASC';

			$post_type_labels[] = $post_type_obj->labels->name;
		}

		$this->do_type_label = count( $post_type_labels ) > 1;

		$post_type_labels = implode( '/', $post_type_labels );

		$filter_boxes = '';
		// Check 'filter' setting
		if ( $this->field->options( 'filter_boxes' ) ) {
			$filter_boxes = '<div class="search-wrap"><input type="text" placeholder="' . sprintf( 'Filter %s', $post_type_labels ) . '" class="regular-text search" name="%s" /></div>';
		}

		// Check to see if we have any meta values saved yet
		$attached = (array) $escaped_value;

		// Get all objects.
		$att_objects = $this->get_all_objects( $args, $attached );

		// If there are no posts found, just stop
		if ( empty( $att_objects ) ) {
			echo '<p>No entries found for the ' . esc_html( $post_type_labels ) . ' post types</p>';
			return;
		}

		// Wrap our lists.
		echo '<div class="attached-posts-wrap widefat" data-fieldname="' . esc_attr( $field_type->_name() ) . '">';

		// Open our retrieved, or found posts, list
		echo '<div class="retrieved-wrap column-wrap">';
		echo '<h4 class="attached-posts-section">' . esc_html( sprintf( 'Newest %s', $post_type_labels ) ) . '</h4>';

		// Set .has_thumbnail
		$has_thumbnail = $this->field->options( 'show_thumbnails' ) ? ' has-thumbnails' : '';
		$hide_selected = $this->field->options( 'hide_selected' ) ? ' hide-selected' : '';

		// Check if we have a max limit AND if we reached it.
		$has_max_limit = ( $this->field->attributes( 'data-max-items' ) ) ? (int) $this->field->attributes( 'data-max-items' ) : false;
		$reached_limit = ( false !== $has_max_limit && count( $attached ) === $has_max_limit ) ? ' has-reached-limit' : '';

		if ( $filter_boxes ) {
			printf( $filter_boxes, 'available-search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '<ul class="retrieved connected' . esc_attr( $reached_limit ) . esc_attr( $has_thumbnail ) . esc_attr( $hide_selected ) . '">';

		// Loop through our posts as list items
		$this->display_retrieved( $att_objects, $attached );

		// Close our retrieved, or found, posts
		echo '</ul><!-- .retrieved -->';

		// Set defaults.
		$search_txt = $field_type->_text( 'find_text', 'Search' );
		$find_txt   = 'Find Posts and Pages';

		// if the post type value isn't an array, let the post type decide what it is.
		if ( ! is_array( $args['post_type'] ) ) {
			$post_type_obj = get_post_type_object( $args['post_type'] );
			$find_txt      = $post_type_obj->labels->search_items;
		}

		// Build JS.
		$js_data = wp_json_encode(
			array(
				'types'    => (array) $args['post_type'],
				'cmbId'    => $this->field->cmb_id,
				'errortxt' => esc_attr( $field_type->_text( 'error_text', 'An error has occurred. Please reload the page and try again.' ) ),
				'findtxt'  => esc_attr( $field_type->_text( 'find_text', $find_txt ) ),
				'groupId'  => $this->field->group ? $this->field->group->id() : false,
				'fieldId'  => $this->field->_id(),
				'exclude'  => isset( $args['post__not_in'] ) ? $args['post__not_in'] : array(),
			)
		);

		echo '<p><button type="button" class="button cmb2-attached-posts-search-button" data-search=\'' . esc_js( $js_data ) . '\'>' . esc_html( $search_txt ) . ' <span title="' . esc_attr( $search_txt ) . '" class="dashicons dashicons-search"></span></button></p>';
		echo '</div><!-- .retrieved-wrap -->';

		// Open our attached posts list
		echo '<div class="attached-wrap column-wrap">';
		// translators: %s is the post content.
		echo '<h4 class="attached-posts-section">' . esc_html( sprintf( 'Attached %s', $post_type_labels ) ) . '<span class="attached-posts-remaining hidden"> (<span class="attached-posts-remaining-number"></span> remaining)</span></span></h4>';

		if ( $filter_boxes ) {
			printf( $filter_boxes, 'attached-search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '<ul class="attached connected' . esc_attr( $has_thumbnail ) . '">';

		// If we have any ids saved already, display them
		$ids = $this->display_attached( $attached );

		// Close up shop
		echo '</ul><!-- #attached -->';
		echo '</div><!-- .attached-wrap -->';

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $field_type->input(
			array(
				'type'  => 'hidden',
				'class' => 'attached-posts-ids',
				'value' => esc_attr( ! empty( $ids ) ? implode( ',', $ids ) : '' ),
				'desc'  => '',
			)
		);

		echo '</div><!-- .attached-posts-wrap -->';

		// Display our description if one exists
		$field_type->_desc( true, true );
	}

	/**
	 * Outputs the <li>s in the retrieved (left) column.
	 *
	 * @since  1.2.5
	 *
	 * @param  mixed  $att_objects  Posts
	 * @param  array  $attached Array of attached posts.
	 *
	 * @return void
	 */
	protected function display_retrieved( $att_objects, $attached ) {
		$count = 0;

		// Loop through our posts as list items
		foreach ( $att_objects as $att_object ) {

			// Set our zebra stripes
			++$count; // increment count.
			$class = ( 0 === $count % 2 ) ? 'even' : 'odd';

			// Set a class if our post is in our attached meta
			$class .= ! empty( $attached ) && in_array( $this->get_id( $att_object ), $attached, true ) ? ' added' : '';

			$this->list_item( $att_object, $class );
		}
	}

	/**
	 * Outputs the <li>s in the attached (right) column.
	 *
	 * @since  1.2.5
	 *
	 * @param  array  $attached Array of attached posts.
	 *
	 * @return void
	 */
	protected function display_attached( $attached ) {
		$ids = array();

		// Remove any empty values
		$attached = array_filter( $attached );

		if ( empty( $attached ) ) {
			return $ids;
		}

		$count = 0;

		// Loop through and build our existing display items
		foreach ( $attached as $id ) {
			$att_object = get_post( absint( $id ) );
			$id         = $this->get_id( $att_object );

			if ( empty( $att_object ) ) {
				continue;
			}

			// Set our zebra stripes
			++$count;
			$class = ( 0 === $count % 2 ) ? 'even' : 'odd';

			$this->list_item( $att_object, $class, 'dashicons-minus' );
			$ids[ $id ] = $id;
		}

		return $ids;
	}

	/**
	 * Outputs a column list item.
	 *
	 * @since  1.2.5
	 *
	 * @param  mixed  $att_object     Post or User.
	 * @param  string  $li_class   The list item (zebra) class.
	 * @param  string  $icon_class The icon class. Either 'dashicons-plus' or 'dashicons-minus'.
	 *
	 * @return void
	 */
	public function list_item( $att_object, $li_class, $icon_class = 'dashicons-plus' ) {
		// Build our list item
		printf(
			'<li data-id="%1$d" class="%2$s" target="_blank">%3$s<a title="Edit" href="%4$s" target="_new">%5$s</a>%6$s<span class="dashicons %7$s add-remove"></span></li>',
			esc_attr( $this->get_id( $att_object ) ),
			esc_attr( $li_class ),
			esc_html( $this->get_thumb( $att_object ) ),
			esc_url( get_edit_post_link( $att_object ) ),
			esc_html( $this->get_title( $att_object ) ),
			esc_html( $this->get_object_label( $att_object ) ),
			esc_attr( $icon_class )
		);
	}

	/**
	 * Get thumbnail for the object.
	 *
	 * @since  1.2.4
	 *
	 * @param  mixed  $att_object Post
	 *
	 * @return string         The thumbnail, if enabled/found.
	 */
	public function get_thumb( $att_object ) {
		$thumbnail = '';

		// Set thumbnail if the options is true
		if ( $this->field->options( 'show_thumbnails' ) ) {
			$thumbnail = get_the_post_thumbnail( $att_object->ID, array( 50, 50 ) );
		}

		return $thumbnail;
	}

	/**
	 * Get ID for the object.
	 *
	 * @since  1.2.4
	 *
	 * @param  mixed  $att_object Post
	 *
	 * @return int    The object ID.
	 */
	public function get_id( $att_object ) {
		return isset( $att_object->ID ) ? $att_object->ID : false;
	}

	/**
	 * Get title for the object.
	 *
	 * @since  1.2.4
	 *
	 * @param  mixed  $att_object Post
	 *
	 * @return string             The object title.
	 */
	public function get_title( $att_object ) {
		$post  = get_post( $att_object );
		$title = get_the_title( $post->ID );
		$title = apply_filters( 'cmb2_attached_posts_title_filter', $title, $post->ID );

		return $title;
	}

	/**
	 * Get object label.
	 *
	 * @since  1.2.6
	 *
	 * @param  mixed  $att_object Post
	 *
	 * @return string             The object label.
	 */
	public function get_object_label( $att_object ) {
		if ( ! $this->do_type_label ) {
			return '';
		}

		$post_type_obj = get_post_type_object( $att_object->post_type );
		$label         = isset( $post_type_obj->labels->singular_name ) ? $post_type_obj->labels->singular_name : $post_type_obj->label;

		return apply_filters( 'cmb2_attached_posts_field_label', ' &mdash; <span class="object-label">' . $label . '</span> ', $label, $att_object );
	}

	/**
	 * Fetches the default query for items, and combines with any objects attached.
	 *
	 * @since  1.2.4
	 *
	 * @param  array  $args     Array of query args.
	 * @param  array  $attached Array of attached object ids.
	 *
	 * @return array            Array of attached object ids.
	 */
	public function get_all_objects( $args, $attached = array() ) {
		$attached_objects = array();

		// Get each object from original search.
		$original_objects = get_posts( $args );
		foreach ( $original_objects as $att_object ) {
			$attached_objects[ $this->get_id( $att_object ) ] = $att_object;
		}

		// If there are already attached posts, exclude them.
		if ( ! empty( $attached ) ) {
			$args['post__in']       = $attached;
			$args['posts_per_page'] = count( $attached );

			$new = get_posts( $args );

			foreach ( $new as $att_object ) {
				if ( ! isset( $attached_objects[ $this->get_id( $att_object ) ] ) ) {
					$attached_objects[ $this->get_id( $att_object ) ] = $att_object;
				}
			}
		}

		return apply_filters( 'cmb2_attached_posts_objects', $attached_objects, $args );
	}

	/**
	 * Enqueue admin scripts for our attached posts field
	 */
	protected static function setup_scripts() {
		static $once = false;

		$dir = CMB2_ATTACHED_POSTS_FIELD_DIR;
		$url = str_replace(
			array( WP_CONTENT_DIR, WP_PLUGIN_DIR ),
			array( WP_CONTENT_URL, WP_PLUGIN_URL ),
			$dir
		);

		$url = set_url_scheme( $url );
		$url = apply_filters( 'cmb2_attached_posts_field_assets_url', $url );

		$requirements = array(
			'jquery-ui-core',
			'jquery-ui-widget',
			'jquery-ui-mouse',
			'jquery-ui-draggable',
			'jquery-ui-droppable',
			'jquery-ui-sortable',
			'wp-backbone',
		);

		wp_enqueue_script( 'cmb2-attached-posts-field', plugins_url( 'js/attached-posts.js', __FILE__ ), $requirements, self::VERSION, true );
		wp_enqueue_style( 'cmb2-attached-posts-field', plugins_url( 'css/attached-posts-admin.css', __FILE__ ), array(), self::VERSION );

		if ( ! $once ) {
			wp_localize_script(
				'cmb2-attached-posts-field',
				'CMBAP',
				array(
					'edit_link_template' => str_replace( get_the_ID(), 'REPLACEME', get_edit_post_link( get_the_ID() ) ),
					'ajaxurl'            => admin_url( 'admin-ajax.php', 'relative' ),
				)
			);

			$once = true;
		}
	}

	/**
	 * Add the find posts div via a hook so we can relocate it manually
	 */
	public function add_find_posts_div() {
		add_action( 'wp_footer', 'find_posts_div' );
	}

	/**
	 * Sanitizes/formats the attached-posts field value.
	 *
	 * @since  1.2.4
	 *
	 * @param  string  $sanitized_val The sanitized value to be saved.
	 * @param  string  $val           The non-sanitized value.
	 *
	 * @return string                 The (maybe-modified) sanitized value to be saved.
	 */
	public function sanitize( $sanitized_val, $val ) {
		if ( ! empty( $val ) ) {
			$sanitized_val = explode( ',', $val );
		}

		return $sanitized_val;
	}

	/**
	 * Check to see if we have a post type set and, if so, add the
	 * pre_get_posts action to set the queried post type
	 *
	 * @since  1.2.4
	 *
	 * @return void
	 */
	public function ajax_find_posts() {
		// @codingStandardsIgnoreStart
		if (
			defined( 'DOING_AJAX' )
			&& DOING_AJAX
			&& isset( $_POST['cmb2_attached_search'], $_POST['retrieved'], $_POST['action'], $_POST['search_types'] )
			&& 'find_posts' === $_POST['action']
			&& ! empty( $_POST['search_types'] )
		) {
			add_action( 'pre_get_posts', array( $this, 'modify_query' ) );
		}
		// @codingStandardsIgnoreEnd
	}

	/**
	 * Modify the search query.
	 *
	 * @since  1.2.4
	 *
	 * @param  WP_Query  $query WP_Query instance during the pre_get_posts hook.
	 *
	 * @return void
	 */
	public function modify_query( $query ) {
		// Set post types
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$types = $_POST['search_types'];
		$types = is_array( $types ) ? array_map( 'sanitize_text_field', $types ) : sanitize_text_field( $types );
		$query->set( 'post_type', $types );

		// Search Term
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$search = sanitize_text_field( isset( $_POST['ps'] ) ? $_POST['ps'] : '' );

		// Posts we've manually chosen to exclude
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$exclude = ( isset( $_POST['exclude'] ) && is_array( $_POST['exclude'] ) ) ? array_map( 'absint', $_POST['exclude'] ) : array();

		// Posts already listed.
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$retrieved = ( isset( $_POST['retrieved'] ) && is_array( $_POST['retrieved'] ) ) ? array_map( 'absint', $_POST['retrieved'] ) : array();

		// If there are posts already listed, we don't need to re-search them.
		if ( ! empty( $retrieved ) ) {
			$ids = $retrieved;

			// If there are other posts we manually chose to exclude, merge them.
			if ( ! empty( $exclude ) ) {
				$ids = array_merge( $ids, $exclude );
			}

			// Set the query.
			$query->set( 'post__not_in', $ids );
		}

		// for search, we want to do relevance.
		if ( $query->is_search ) {
			$search_term = $query->query_vars['s'];
			$query->set( 'orderby', 'relevance' );

			// If it's under 4 characters, we need a more exact search.
			if ( 4 > strlen( $search_term ) ) {
				$query->set( 'title', $search_term );
			}
		}
	}

	/**
	 * Outputs the display of our custom field.
	 *
	 * @since 1.2.7
	 *
	 * @param bool|mixed         $pre_output Default null value.
	 * @param CMB2_Field         $field      This field object.
	 * @param CMB2_Field_Display $display    The `CMB2_Field_Display` object.
	 */
	public function display_render( $pre_output, $field, $display ) {
		if ( ! empty( $pre_output ) ) {
			return $pre_output;
		}

		$return = '';

		// If repeatable
		if ( $display->field->args( 'repeatable' ) ) {
			$rows = array();

			// And has a repeatable value
			if ( is_array( $display->field->value ) ) {

				// Then loop and output.
				foreach ( $display->field->value as $val ) {
					$rows[] = $this->re_display_render( $display->field, $val );
				}
			}

			if ( ! empty( $rows ) ) {

				$return .= '<ul class="cmb2-' . str_replace( '_', '-', $display->field->type() ) . '"><li>';
				foreach ( (array) $rows as $row ) {
					$return .= sprintf( '<li>%s</a>', $row );
				}
				$return .= '</ul>';

			} else {
				$return .= '&mdash;';
			}
		} else {
			$return .= $this->re_display_render( $display->field, $display->field->value );
		}

		return $return ? $return : $pre_output;
	}

	/**
	 * Outputs the display of our custom field per repeatable value, if applicable.
	 *
	 * @since 1.2.7
	 *
	 * @param CMB2_Field $field This field object.
	 * @param mixed      $val   The field value.
	 */
	public function re_display_render( $field, $val ) {
		$return = '';
		$posts  = array();

		if ( ! empty( $val ) ) {
			foreach ( (array) $val as $id ) {
				$title = get_the_title( $id );
				if ( $title ) {
					$edit_link    = get_edit_post_link( $id );
					$posts[ $id ] = compact( 'title', 'edit_link' );
				}
			}
		}

		if ( ! empty( $posts ) ) {

			$return .= '<ol>';
			foreach ( (array) $posts as $id => $post ) {

				$title = apply_filters( 'cmb2_attached_posts_title_filter', $post['title'], $id );

				$return .= sprintf(
					'<li id="attached-%d"><a href="%s" target="_new">%s</a></li>',
					$id,
					$post['edit_link'],
					$title
				);
			}
			$return .= '</ol>';

		} else {
			$return .= '&mdash;';
		}

		return $return;
	}
}

LWTV_CMB2_Attached_Posts_Field::get_instance();
