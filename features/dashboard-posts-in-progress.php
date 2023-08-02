<?php
/*
 * Dashboard: Posts In Progress (FORKED)
 *
 * Displays unpublished posts on your dashboard.
 *
 * @version 2.0.1
 *
 * Author: Viper007Bond, Ipstenu
 * License: GPLv2 (or Later)
 *
 * Copyright 2008-19 Alex Mills (Viper007Bond) - http://www.viper007bond.com/wordpress-plugins/dashboard-pending-review/ https://wordpress.org/plugins/dashboard-pending-review/
 * Copyright 2019-23 Mika Epstein (Ipstenu)
 *
 * This file was part of Dashboard: Posts In Progress, a plugin for WordPress.
 */

// Deactivate existing plugin if it's active.
if ( class_exists( 'DashboardPostsInProgress' ) || is_plugin_active( 'dashboard-in-progress/easy-dashboard-status.php' ) ) {
	deactivate_plugins( plugin_basename( 'dashboard-in-progress/easy-dashboard-status.php' ) );
}

class LWTV_DashboardPostsInProgress {

	/**
	 * Version Number
	 * @var string
	 */
	public static $version = '2.0.1';

	/**
	 * __construct function.
	 *
	 * @return n/a
	 */
	public function __construct() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		add_action( 'admin_enqueue_scripts', array( &$this, 'custom_css' ) );
		add_action( 'wp_dashboard_setup', array( &$this, 'register_widget' ) );
		add_filter( 'wp_dashboard_widgets', array( &$this, 'add_widget' ) );
	}

	/**
	 * Register this widget
	 * we use a hook/function to make the widget a dashboard-only widget
	 */
	public function register_widget() {
		wp_add_dashboard_widget( 'dashboard_in_progress', __( 'Posts in Progress', 'lwtv-plugin' ), array( &$this, 'widget' ) );
	}

	/**
	 * Output custom CSS
	 */
	public function custom_css() {
		if ( is_user_logged_in() && is_admin() ) {
			wp_register_style( 'dashboard_in_progress', plugins_url( 'style.css', __FILE__ ), false, self::$version );
			wp_enqueue_style( 'dashboard_in_progress' );
		}
	}

	/**
	 * Add widget
	 *
	 * Modifies the array of dashboard widgets and adds this plugin's output
	 *
	 * @return array  array of Widget data
	 */
	public function add_widget( $widgets ) {
		global $wp_registered_widgets;

		if ( isset( $wp_registered_widgets['dashboard_in_progress'] ) ) {
			$widgets[] = 'dashboard_in_progress';
		}

		return $widgets;
	}

	/**
	 * Output the widget contents
	 */
	public function widget( $args ) {
		$filtered_post_type  = apply_filters( 'dashboard_posts_in_progress_type', 'post' );
		$filtered_post_type  = ( post_type_exists( $filtered_post_type ) || 'any' === $filtered_post_type ) ? $filtered_post_type : 'post';
		$post_type_object    = get_post_type_object( $filtered_post_type );
		$post_type_name      = $post_type_object->labels->name;
		$number_posts_shown  = absint( apply_filters( 'dashboard_posts_in_progress_shown', 5 ) );
		$number_posts_shown  = ( 0 === $number_posts_shown ) ? 5 : $number_posts_shown; // default 5
		$drafts_query        = new WP_Query(
			array(
				'post_type'      => $filtered_post_type,
				'what_to_show'   => 'posts',
				'post_status'    => 'draft',
				'posts_per_page' => $number_posts_shown,
				'orderby'        => 'ID', // sort by order created, regardless of date
				'order'          => 'DESC',
			)
		);
		$draft_posts_array   =& $drafts_query->posts;
		$pendings_query      = new WP_Query(
			array(
				'post_type'      => $filtered_post_type,
				'what_to_show'   => 'posts',
				'post_status'    => 'pending',
				'posts_per_page' => absint( apply_filters( 'dashboard_in_progress_posts_shown', 5 ) ), // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page
				'orderby'        => 'ID', // sort by order created, regardless of date
				'order'          => 'DESC',
			)
		);
		$pending_posts_array =& $pendings_query->posts;

		echo '<div id="draft-posts" class="activity-block">';
		if ( $draft_posts_array && is_array( $draft_posts_array ) ) {
			$list = array();

			echo '<h3>';
				esc_html_e( 'Drafts', 'lwtv-plugin' );
			echo '</h3>';

			foreach ( $draft_posts_array as $draft ) {
				// Default Data
				$url    = get_edit_post_link( $draft->ID );
				$title  = _draft_or_post_title( $draft->ID );
				$author = get_the_author_meta( 'display_name', $draft->post_author );

				// Date:
				$item = '<span>' . get_the_time( __( 'M dS Y' ), $draft ) . '</span>';

				// Translators: %s = title.
				$item .= '<a href="' . esc_url( $url ) . '" aria-label="' . esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $title ) ) . '">' . esc_html( $title ) . '</a>';

				// Translators: %s = author display name
				$item .= ' <span class="author">(' . sprintf( __( 'By %s', 'lwtv-plugin' ), $author ) . ')</span>';

				// Content if applicable
				$the_content = wp_trim_words( $draft->post_content, 10 );
				if ( $the_content ) {
					$item .= '<p>' . $the_content . '</p>';
				}
				$list[] = $item;
			}
			?>

			<p class="view-all"><a href="<?php echo esc_url( admin_url( 'edit.php?post_status=draft' ) ); ?>">
				<?php
				// Translators: %s = post type (i.e. posts, pages, etc)
				echo esc_html( sprintf( __( 'View all draft %s', 'lwtv-plugin' ), lcfirst( $post_type_name ) ) );
				?>
			</a></p>

			<ul>
				<li>
					<?php
					foreach ( $list as $item ) {
						echo '<li>' . wp_kses_post( $item ) . '</li>';
					}
					?>
				</li>
			</ul>

			<?php
		}
		echo '</div>';

		echo '<div id="pending-posts" class="activity-block">';
		if ( $pending_posts_array && is_array( $pending_posts_array ) ) {

			echo '<h3>';
			esc_html_e( 'Pending Review', 'lwtv-plugin' );
			echo '</h3>';

			$list = array();

			foreach ( $pending_posts_array as $pending ) {
				// Default Data
				$url    = get_edit_post_link( $pending->ID );
				$title  = _draft_or_post_title( $pending->ID );
				$author = get_the_author_meta( 'display_name', $pending->post_author );

				// Date:
				$item = '<span>' . get_the_time( __( 'M dS Y' ), $pending ) . '</span>';

				// Translators: %s = title.
				$item .= '<a href="' . esc_url( $url ) . '" aria-label="' . esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $title ) ) . '">' . esc_html( $title ) . '</a>';

				// Translators: %s = author display name
				$item .= ' <span class="author">(' . sprintf( __( 'By %s', 'lwtv-plugin' ), $author ) . ')</span>';

				// Content if applicable
				$the_content = wp_trim_words( $pending->post_content, 10 );
				if ( $the_content ) {
					$item .= '<p>' . $the_content . '</p>';
				}
				$list[] = $item;
			}
			?>

			<p class="view-all"><a href="<?php echo esc_url( admin_url( 'edit.php?post_status=pending' ) ); ?>">
				<?php
				// Translators: %s = post type (i.e. posts, pages, etc)
				echo esc_html( sprintf( __( 'View all pending %s', 'lwtv-plugin' ), lcfirst( $post_type_name ) ) );
				?>
			</a></p>

			<ul>
				<li>
					<?php
					foreach ( $list as $item ) {
						echo '<li>' . wp_kses_post( $item ) . '</li>';
					}
					?>
				</li>
			</ul>

			<?php
		} else {
			// Translators: %s = post type (i.e. posts, pages, etc)
			echo esc_html( sprintf( __( 'There are no pending %s at this time.', 'lwtv-plugin' ), lcfirst( $post_type_name ) ) );
		}
		echo '</div>';
	}
}

// Start this plugin once all other plugins are fully loaded
add_action( 'plugins_loaded', 'lwtv_dashboard_posts_in_progress_loaded' );
function lwtv_dashboard_posts_in_progress_loaded() {
	new LWTV_DashboardPostsInProgress();
}
