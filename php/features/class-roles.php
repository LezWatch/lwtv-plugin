<?php
/**
 * Name: Custom Roles
 * Description: Custom User Roles
 */

namespace LWTV\Features;

class Roles {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'create_roles' ) );
		add_action( 'admin_init', array( $this, 'add_role_caps' ) );
		add_action( 'admin_init', array( $this, 'disallowed_admin_pages' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu_customization' ) );
	}

	/**
	 * Check if role exists
	 *
	 * Credit: https://gist.github.com/hlashbrooke/8f901da7c6f0d107add7
	 *
	 * @param  string $role role name
	 * @return boolean      true or false
	 */
	public function role_exists( $role ) {
		if ( ! empty( $role ) ) {
			return $GLOBALS['wp_roles']->is_role( $role );
		}
		return false;
	}

	public function create_roles() {
		if ( ! self::role_exists( 'data_editor' ) ) {
			$de_capabilities = array(
				'read'              => true,
				'edit_posts'        => true, // required for featured images
				'edit_others_posts' => true, // required for featured images
				'delete_posts'      => false,
				'publish_posts'     => false,
				'upload_files'      => true,
				'manage_categories' => true, // so they can add sexualities etc
				'unfiltered_html'   => true,
			);
			add_role( 'data_editor', 'Data Editor', $de_capabilities );
		}
	}

	/**
	 * Add capabilities for new roles
	 */
	public function add_role_caps() {
		// The roles who get to do this:
		$roles = array( 'data_editor', 'editor', 'administrator' );

		// The CPTs they work for
		$cpts = array( 'actor', 'character', 'show', 'tvmaze_name' );

		// Loop through each role and assign capabilities
		foreach ( $roles as $the_role ) {
			// If the role exists (so it doesn't die later)
			if ( self::role_exists( $the_role ) ) {

				$role = get_role( $the_role );
				$role->add_cap( 'read' );
				foreach ( $cpts as $the_cpt ) {
					$role->add_cap( 'read_' . $the_cpt );
					$role->add_cap( 'read_private_' . $the_cpt . 's' );
					$role->add_cap( 'edit_' . $the_cpt );
					$role->add_cap( 'edit_' . $the_cpt . 's' );
					$role->add_cap( 'edit_others_' . $the_cpt . 's' );
					$role->add_cap( 'edit_published_' . $the_cpt . 's' );
					$role->add_cap( 'edit_private_' . $the_cpt . 's' );
					$role->add_cap( 'publish_' . $the_cpt . 's' );
					$role->add_cap( 'delete_' . $the_cpt );
					$role->add_cap( 'delete_' . $the_cpt . 's' );
					$role->add_cap( 'delete_others_' . $the_cpt . 's' );
					$role->add_cap( 'delete_private_' . $the_cpt . 's' );
					$role->add_cap( 'delete_published_' . $the_cpt . 's' );
				}
			}
		}
	}

	/**
	 * Customize Admin Menu Sidebar
	 *
	 * There's a bug in Gutenberg that prevents custom roles who ONLY have access
	 * to CPTs from seeing Featured Images. [insert young RavenSymone facepalm here]
	 * A bug ticket is opened - https://github.com/WordPress/gutenberg/issues/22847
	 * But until someone GAF, we're going to grant permissions and then hide the hell
	 * out of it....
	 */
	public function admin_menu_customization() {
		global $current_user;
		wp_get_current_user();

		if ( in_array( 'data_editor', $current_user->roles, true ) ) {
			remove_menu_page( 'index.php' );                  //Dashboard
			remove_menu_page( 'edit.php' );                   //Posts
			remove_menu_page( 'edit.php?post_type=page' );    //Pages
			remove_menu_page( 'edit-comments.php' );          //Comments
			remove_menu_page( 'themes.php' );                 //Appearance
			remove_menu_page( 'tools.php' );                  //Tools
			remove_menu_page( 'plugins.php' );                //Plugins
			remove_menu_page( 'options-general.php' );        //Settings
		}
	}

	/**
	 * Disallow access to pages
	 *
	 * There's a bug in Gutenberg that prevents custom roles who ONLY have access
	 * to CPTs from seeing Featured Images. [insert young Raven-Symone facepalm here]
	 * A bug ticket is opened - https://github.com/WordPress/gutenberg/issues/22847
	 * But until someone GAF, we're going to grant permissions and then hide the hell
	 * out of it....
	 */
	public function disallowed_admin_pages() {
		global $pagenow, $current_user;
		wp_get_current_user();

		if ( in_array( 'data_editor', $current_user->roles, true ) ) {
			$disallowed_pages = array( 'index.php', 'edit-comments.php', 'themes.php', 'tools.php', 'plugins.php', 'options-general.php' );
			$allowed_cpts     = array( 'post_type_shows', 'post_type_actors', 'post_type_characters' );

			# Check current admin page.
			if ( in_array( $pagenow, $disallowed_pages, true ) || ( 'edit.php' === $pagenow && ( null === $_GET['post_type'] || ( isset( $_GET['post_type'] ) && ! in_array( $_GET['post_type'], $allowed_cpts, true ) ) ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				wp_safe_redirect( admin_url( '/admin.php?page=lwtv' ) );
				exit;
			}
		}
	}
}
