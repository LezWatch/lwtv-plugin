<?php
/**
 * Add custom metaboxes for user profiles
 */

class LWTV_CMB2_Metabox_Profile {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'cmb2_admin_init', array( $this, 'favorite_shows' ) );
	}

	/**
	 * Add metabox for favorite shows in order to customize profiles and
	 * show what we love.
	 */
	public function favorite_shows() {
		$prefix = 'lez_user_';

		/**
		 * Metabox for the user profile screen
		 */
		$cmb_user = new_cmb2_box(
			array(
				'id'               => $prefix . 'edit',
				'title'            => 'Super Queer Love',
				'object_types'     => array( 'user' ),
				'show_names'       => true,
				'new_user_section' => 'add-new-user',
			)
		);

		$cmb_user->add_field(
			array(
				'name'     => 'Favorite Shows',
				'desc'     => 'Drag a show from the left column to the right column to attach them to this page.<br />Rearrange the order in the right column by dragging and dropping.',
				'id'       => $prefix . 'favourite_shows',
				'type'     => 'custom_attached_posts',
				'options'  => array(
					'query_args' => array(
						'posts_per_page' => 5,
						'post_type'      => 'post_type_shows',
					), // override the get_posts args
				),
				'on_front' => true,
			)
		);
	}
}

new LWTV_CMB2_Metabox_Profile();
