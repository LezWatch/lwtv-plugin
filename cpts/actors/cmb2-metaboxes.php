<?php
/**
 * Name: CMB2 Metaboxes
 */

if ( ! defined('WPINC' ) ) die;

/**
 * class LWTV_Actors_CMB2
 *
 * @since 2.1.0
 */

class LWTV_Actors_CMB2 {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'cmb2_init', array( $this, 'cmb2_metaboxes') );
		add_action( 'admin_menu', array( $this,'remove_metaboxes' ) );
	}

	/*
	 * CMB2 Metaboxes
	 */
	public function cmb2_metaboxes() {
		// prefix for all custom fields
		$prefix = 'lezactors_';

		// Metabox Group: Quick Dropdowns
		$cmb_actorside = new_cmb2_box( array(
			'id'           => 'actors_metabox',
			'title'        => 'Additional Data',
			'object_types' => array( 'post_type_actors' ),
			'context'      => 'side',
			'priority'     => 'default',
			'show_names'   => true, // Show field names on the left
			'show_in_rest' => true,
			'cmb_styles'   => false,
		) );
		// Field: Actor Gender Idenity
		$field_gender = $cmb_actorside->add_field( array(
			'name'             => 'Gender',
			'desc'             => 'Gender Identity',
			'id'               => $prefix . 'gender',
			'taxonomy'         => 'lez_actor_gender',
			'type'             => 'taxonomy_select',
			'default'          => 'cis-woman',
			'show_option_none' => false,
			'remove_default'   => 'true'
		) );
		// Field: Actor Sexual Orientation
		$field_sexuality = $cmb_actorside->add_field( array(
			'name'             => 'Sexuality',
			'desc'             => 'Sexual Orientation',
			'id'               => $prefix . 'sexuality',
			'taxonomy'         => 'lez_actor_sexuality',
			'type'             => 'taxonomy_select',
			'default'          => 'heterosexual',
			'show_option_none' => false,
			'remove_default'   => 'true'
		) );
		// Field: Year of Birth
		$field_birth = $cmb_actorside->add_field( array(
			'name'        => 'Date of Birth',
			'id'          => $prefix . 'birth',
			'type'        => 'text_date',
			'date_format' => 'Y-m-d',
		) );
		// Field: Year of Death (if applicable)
		$field_death = $cmb_actorside->add_field( array(
			'name'        => 'Date of Death',
			'desc'        => 'If applicable.',
			'id'          => $prefix . 'death',
			'type'        => 'text_date',
			'date_format' => 'Y-m-d',
		) );
		// Field: IMDb ID
		$field_imdb = $cmb_actorside->add_field( array(
			'name'       => 'IMDb ID',
			'id'         => $prefix . 'imdb',
			'type'       => 'text',
			'attributes' => array(
				'placeholder' => 'Ex: nm6087250',
			),
		) );
		// Field: WikiPedia
		$field_wiki = $cmb_actorside->add_field( array(
			'name'       => 'WikiPedia URL',
			'id'         => $prefix . 'wikipedia',
			'type'       => 'text_url',
			'attributes' => array(
				'placeholder' => 'https://en.wikipedia.org/wiki/Caity_Lotz',
			),
		) );
		// Field: Home URL
		$field_home = $cmb_actorside->add_field( array(
			'name'       => 'Homepage URL',
			'id'         => $prefix . 'homepage',
			'type'       => 'text_url',
			'attributes' => array(
				'placeholder' => 'https://actorname.com',
			),
		) );
		// Field: Twitter ID
		$field_twitter = $cmb_actorside->add_field( array(
			'name'       => 'Twiter ID',
			'id'         => $prefix . 'Twitter',
			'type'       => 'text',
			'attributes' => array(
				'placeholder' => 'Ex: aliliebert - without the @',
			),
		) );
		// Field: Instagram ID
		$field_instagram = $cmb_actorside->add_field( array(
			'name'       => 'Instagram ID',
			'id'         => $prefix . 'instagram',
			'type'       => 'text',
			'attributes' => array(
				'placeholder' => 'Ex: whododatlikedat',
			),
		) );
		// Actor Sidebar Grid
		if( !is_admin() ){
			return;
		} else {
			$grid_actorside = new \Cmb2Grid\Grid\Cmb2Grid( $cmb_actorside );
			$row1 = $grid_actorside->addRow();
			$row1->addColumns( array( $field_gender, $field_sexuality ) );
		}
	}

	/*
	 * Remove Metaboxes we use elsewhere
	 */
	function remove_metaboxes() {
		remove_meta_box( 'authordiv', 'post_type_actors', 'normal' );
		remove_meta_box( 'postexcerpt' , 'post_type_actors' , 'normal' );
	}

}
new LWTV_Actors_CMB2();