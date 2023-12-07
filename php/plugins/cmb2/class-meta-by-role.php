<?php
/**
 * Only allow people with certain roles to edit certain post meta.
 */

namespace LWTV\Plugins\CMB2;

class Meta_By_Role {

	/**
	 * Constructor
	 */
	public function __construct() {
		// Filter to allow show_on to limit by role
		add_filter( 'cmb2_show_on', array( $this, 'cmb_show_meta_to_chosen_roles' ), 10, 2 );
	}

	/**
	 * Display metabox for only certain user roles.
	 * @author @Mte90
	 * @link   https://github.com/CMB2/CMB2/wiki/Adding-your-own-show_on-filters
	 *
	 * @param  bool  $display  Whether metabox should be displayed or not.
	 * @param  array $meta_box Metabox config array
	 * @return bool            (Modified) Whether metabox should be displayed or not.
	 */
	public function cmb_show_meta_to_chosen_roles( $display, $meta_box ) {
		if ( ! isset( $meta_box['show_on']['key'], $meta_box['show_on']['value'] ) ) {
			return $display;
		}

		if ( 'role' !== $meta_box['show_on']['key'] ) {
			return $display;
		}

		$user = wp_get_current_user();

		// No user found, return
		if ( empty( $user ) ) {
			return false;
		}

		$roles = (array) $meta_box['show_on']['value'];

		foreach ( $user->roles as $role ) {
			// Does user have role.. check if array
			if ( is_array( $roles ) && in_array( $role, $roles, true ) ) {
				return true;
			}
		}

		return false;
	}
}
