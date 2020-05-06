<?php
/**
 * Name: Custom Roles
 * Description: Custom User Roles
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * class LWTV_Loops
 *
 * Customize Loops
 *
 * @since 1.0
 */

class LWTV_Roles {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'create_roles' ) );
		add_action( 'admin_init', array( $this, 'add_role_caps' ) );
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
			$capabilities = array(
				'read'          => true,
				'edit_posts'    => false,
				'delete_posts'  => false,
				'publish_posts' => false,
				'upload_files'  => true,
			);
			$result = add_role( 'data_editor', 'Data Editor', $capabilities );
		}
	}

	/**
	 * Add capabilities for new roles
	 */
	public function add_role_caps() {
		// The roles who get to do this:
		$roles = array( 'data_editor', 'editor', 'administrator' );

		// The CPTs they work for
		$cpts  = array( 'actor', 'character', 'show' );

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
					$role->add_cap( 'publish_' . $the_cpt . 's' );
					$role->add_cap( 'delete_others_' . $the_cpt . 's' );
					$role->add_cap( 'delete_private_' . $the_cpt . 's' );
					$role->add_cap( 'delete_published_' . $the_cpt . 's' );
				}
			}
		}
	}

}

new LWTV_Roles();
