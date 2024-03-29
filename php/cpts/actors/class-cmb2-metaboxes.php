<?php
/**
 * Name: CMB2 Metaboxes
 */

namespace LWTV\CPTs\Actors;

class CMB2_Metaboxes {

	// prefix for all custom fields
	const PREFIX = 'lezactors_';

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'cmb2_init', array( $this, 'cmb2_metaboxes' ) );
		add_action( 'admin_menu', array( $this, 'remove_metaboxes' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_wikidata_metabox' ) );
	}

	/*
	 * Non CMB2 Metaboxes
	 */
	public function add_wikidata_metabox() {
		add_meta_box(
			'metaboxlezactors_saved_wikidata',
			'WikiData',
			array( $this, 'wikidata_metabox_callback' ),
			'post_type_actors',
			'side',
			'high'
		);
	}

	/**
	 * WikiData MetaBox
	 * @param  object $post Post Object
	 * @return echo         The MetaBox content.
	 */
	public function wikidata_metabox_callback( $post ) {

		wp_nonce_field( 'wikidata-metabox-save', 'wikidata_metabox_nonce' );

		$wikidata     = false;
		$wikidata_url = '';

		// If it's an auto draft, we do nothing. Else, we roll.
		if ( ! isset( $post->ID ) || 'draft' === get_post_status( $post->ID ) || 'auto-draft' === get_post_status( $post->ID ) || '' === get_the_title( $post->ID ) ) {
			$wikidata = 'auto-draft';
		} else {
			lwtv_plugin()->check_actors_wikidata( $post->ID );
			$wikidata     = get_post_meta( $post->ID, 'lezactors_saved_wikidata', true );
			$wikidata_qid = get_post_meta( $post->ID, 'lezactors_wikidata_qid', true );

			if ( empty( $wikidata_qid ) && isset( $wikidata['wikidata'] ) ) {
				$wikidata_qid = $wikidata['wikidata'];
			}
		}

		// If Wikidata isn't empty AND there's a valid Q code, we go
		if ( ! empty( $wikidata ) && ! empty( $wikidata_qid ) && lwtv_plugin()->validate_wikidata_id( $wikidata_qid ) ) {

			// Build URL
			$wikidata_url = 'https://www.wikidata.org/wiki/' . $wikidata_qid;

			// Clean array
			unset( $wikidata['id'] );
			unset( $wikidata['name'] );
			unset( $wikidata['wikidata'] );

			foreach ( $wikidata as $datatype => $result ) {
				if ( 'match' === $result || 'n/a' === $result ) {
					unset( $wikidata[ $datatype ] );
				}
			}

			$wikidata = ( empty( $wikidata ) ) ? true : $wikidata;

		} elseif ( empty( $wikidata ) ) {
			$wikidata = false;
		}

		// Output Wikidata info.
		$this->output_wikidata( $post->ID, $wikidata, $wikidata_url );

		// To Do: A button here to rerun the check.
	}

	/**
	 * Outputs the Wikidata.
	 *
	 * @param  [type] $wikidata
	 * @param  [type] $wikidata_url
	 * @return void
	 */
	public function output_wikidata( $post_id, $wikidata, $wikidata_url ) {

		if ( ! $wikidata ) {
			echo '<p>No information for ' . esc_html( get_the_title( $post_id ) ) . ' found in WikiData.</p>';
		} elseif ( 'auto-draft' === $wikidata ) {
			echo '<p>WikiData checks pending. Once we fill in some information, it will be able to check.</p>';
			// To Do: A button here to trigger the check
		} elseif ( empty( $wikidata_url ) ) {
			echo '<p>There is no WikiData available on this actor.</p>';
		} elseif ( true === $wikidata ) {
			echo '<p>All data for ' . esc_html( get_the_title( $post_id ) ) . ' matches <a href="' . esc_url( $wikidata_url ) . '" target="_blank">WikiData!</a></p>';
		} else {
			echo '<p>The following data does not match <a href="' . esc_url( $wikidata_url ) . '" target="_blank">WikiData</a>:</p>';
			foreach ( $wikidata as $datatype => $result ) {
				echo '<p><strong>' . esc_html( ucfirst( $datatype ) ) . ':</strong></p>';
				echo '<ul>';
				echo '<li>LezWatch: ' . esc_html( $result['ours'] ) . '</li>';
				echo '<li>WikiData: ' . esc_html( $result['wikidata'] ) . '</li>';
				echo '<ul>';
			}

			echo '<hr>';
			echo '<p>Please double check. WikiData is sometimes wrong about Social Media.</p>';
			echo '<p>(Warning: This doesn\'t currently refresh on save.)</p>';
		}
	}

	/**
	 * Rerun the wikidata checker on save.
	 *
	 * Not being used yet, since the stupid thing won't re-run until a reload because
	 * Gutenberg STILL lacks that ability.
	 */
	public function save_wikidata_metabox( $post_id ) {
		//
		// Check if our nonce is set.
		if ( ! isset( $_POST['wikidata_metabox_nonce'] ) || ! wp_verify_nonce( $_POST['wikidata_metabox_nonce'], 'wikidata-metabox-save' ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check the user's permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Rerun the wiki:
		lwtv_plugin()->check_actors_wikidata( $post_id );
	}

	/*
	 * CMB2 Metaboxes
	 */
	public function cmb2_metaboxes() {
		// Metabox Group: Quick Dropdowns
		$cmb_actorside = \new_cmb2_box(
			array(
				'id'           => 'actors_metabox',
				'title'        => 'Actor Details',
				'object_types' => array( 'post_type_actors' ),
				'context'      => 'normal',
				'priority'     => 'default',
				'show_names'   => true, // Show field names on the left
				'show_in_rest' => true,
			)
		);
		// Field: Actor Gender Idenity
		$field_gender = $cmb_actorside->add_field(
			array(
				'name'             => 'Gender',
				'desc'             => 'Gender Identity',
				'id'               => self::PREFIX . 'gender',
				'taxonomy'         => 'lez_actor_gender',
				'type'             => 'taxonomy_select',
				'default'          => 'cis-woman',
				'show_option_none' => false,
				'remove_default'   => 'true',
			)
		);
		// Field: Actor Sexual Orientation
		$field_sexuality = $cmb_actorside->add_field(
			array(
				'name'             => 'Sexuality',
				'desc'             => 'Sexual Orientation',
				'id'               => self::PREFIX . 'sexuality',
				'taxonomy'         => 'lez_actor_sexuality',
				'type'             => 'taxonomy_select',
				'default'          => 'unknown',
				'show_option_none' => false,
				'remove_default'   => 'true',
			)
		);
		// Field: Actor Romantic Orientation
		$field_romantic = $cmb_actorside->add_field(
			array(
				'name'             => 'Romantic Nature',
				'desc'             => 'AKA affectional orientation',
				'id'               => self::PREFIX . 'romantic',
				'taxonomy'         => 'lez_actor_romantic',
				'type'             => 'taxonomy_select',
				'show_option_none' => 'No Selection (default)',
			)
		);
		// Field: Queer override Checkbox
		$field_queer_override = $cmb_actorside->add_field(
			array(
				'name'    => 'Queer Override',
				'desc'    => 'Allow manual intervention to force someone to be marked as queer or not.',
				'id'      => self::PREFIX . 'queer_override',
				'type'    => 'select',
				'default' => 'undefined',
				'options' => array(
					'undefined' => 'No Selection (Default)',
					'is_queer'  => 'Is Queer',
					'not_queer' => 'Is NOT Queer',
				),
			)
		);
		// Field: Actor Pronouns
		$field_pronouns = $cmb_actorside->add_field(
			array(
				'name'              => 'Pronouns',
				'desc'              => 'Pronouns (Optional)',
				'id'                => self::PREFIX . 'pronouns',
				'taxonomy'          => 'lez_actor_pronouns',
				'type'              => 'taxonomy_multicheck_inline',
				'default'           => '',
				'select_all_button' => false,
				'show_option_none'  => false,
				'remove_default'    => 'true',
			)
		);
		// Field: Year of Birth
		$field_birth = $cmb_actorside->add_field(
			array(
				'name'        => 'Date of Birth',
				'desc'        => 'If known',
				'id'          => self::PREFIX . 'birth',
				'type'        => 'text_date',
				'date_format' => 'Y-m-d',
			)
		);
		// Field: Year of Death (if applicable)
		$field_death = $cmb_actorside->add_field(
			array(
				'name'        => 'Date of Death',
				'desc'        => 'If applicable',
				'id'          => self::PREFIX . 'death',
				'type'        => 'text_date',
				'date_format' => 'Y-m-d',
			)
		);
		// Field: IMDb ID
		$field_imdb = $cmb_actorside->add_field(
			array(
				'name'       => 'IMDb',
				'id'         => self::PREFIX . 'imdb',
				'type'       => 'text',
				'attributes' => array(
					'placeholder' => 'Ex: nm6087250',
				),
			)
		);
		// Field: WikiPedia
		$field_wiki = $cmb_actorside->add_field(
			array(
				'name'       => 'WikiPedia URL',
				'id'         => self::PREFIX . 'wikipedia',
				'type'       => 'text_url',
				'attributes' => array(
					'placeholder' => 'https://en.wikipedia.org/wiki/Caity_Lotz',
				),
			)
		);
		// Field: Home URL
		$field_home = $cmb_actorside->add_field(
			array(
				'name'       => 'Homepage URL',
				'id'         => self::PREFIX . 'homepage',
				'type'       => 'text_url',
				'attributes' => array(
					'placeholder' => 'https://actorname.com',
				),
			)
		);
		// Field: Twitter ID
		$field_twitter = $cmb_actorside->add_field(
			array(
				'name'       => 'X (Twitter)',
				'id'         => self::PREFIX . 'twitter',
				'type'       => 'text',
				'attributes' => array(
					'placeholder' => 'Ex: aliliebert - without the @',
				),
			)
		);
		// Field: Instagram ID
		$field_instagram = $cmb_actorside->add_field(
			array(
				'name'       => 'Instagram',
				'id'         => self::PREFIX . 'instagram',
				'type'       => 'text',
				'attributes' => array(
					'placeholder' => 'Ex: whododatlikedat',
				),
			)
		);
		// Field: Tumblr ID
		$field_tumblr = $cmb_actorside->add_field(
			array(
				'name'       => 'Tumblr',
				'id'         => self::PREFIX . 'tumblr',
				'type'       => 'text',
				'attributes' => array(
					'placeholder' => 'Ex: tiredandlonelymuse (remove .tumblr.com)',
				),
			)
		);
		// Field: Mastodon URL
		$field_mastodon = $cmb_actorside->add_field(
			array(
				'name'       => 'Mastodon',
				'id'         => self::PREFIX . 'mastodon',
				'type'       => 'text',
				'attributes' => array(
					'placeholder' => 'Ex: https://mastodon.instance/@username',
				),
			)
		);
		// Field: Facebook URL
		$field_facebook = $cmb_actorside->add_field(
			array(
				'name'       => 'Facebook',
				'id'         => self::PREFIX . 'facebook',
				'type'       => 'text',
				'attributes' => array(
					'placeholder' => 'Ex: https://facebook.com/username',
				),
			)
		);
		// Field: TikTok URL
		$field_tiktok = $cmb_actorside->add_field(
			array(
				'name'       => 'TikTok',
				'id'         => self::PREFIX . 'tiktok',
				'type'       => 'text',
				'attributes' => array(
					'placeholder' => 'Ex: @reesewitherspoon - WITH the @',
				),
			)
		);
		// Field: Bluesky URL
		$field_bluesky = $cmb_actorside->add_field(
			array(
				'name'       => 'BlueSky',
				'id'         => self::PREFIX . 'bluesky',
				'type'       => 'text',
				'attributes' => array(
					'placeholder' => 'Ex: https://bsky.app/profile/jerilryan.bsky.social',
				),
			)
		);
		// Field: Twitch URL
		$field_twitch = $cmb_actorside->add_field(
			array(
				'name'       => 'Twitch',
				'id'         => self::PREFIX . 'twitch',
				'type'       => 'text',
				'attributes' => array(
					'placeholder' => 'Ex: https://www.twitch.tv/criticalrole',
				),
			)
		);
		// Field: WikiData QID
		$field_wikidata = $cmb_actorside->add_field(
			array(
				'name'       => 'WikiData ID',
				'id'         => self::PREFIX . 'wikidata_qid',
				'type'       => 'text',
				'attributes' => array(
					'placeholder' => 'Ex: Q2933359 - use to override the search',
				),
			)
		);
		// Field: Excerpt
		$field_excerpt = $cmb_actorside->add_field(
			array(
				'name'      => 'Additional Notes',
				'id'        => 'excerpt',
				'desc'      => 'Enter any terms here that should be used for search. For example, if someone changed their name, you would list their deadname here so it can be searched.',
				'type'      => 'textarea',
				'escape_cb' => false,
				'default'   => get_post_field( 'post_excerpt' ),
			)
		);
		// Field: Make Actor Private
		$field_make_private = $cmb_actorside->add_field(
			array(
				'name'    => 'Make specific data private',
				'id'      => self::PREFIX . 'make_option_private',
				'type'    => 'multicheck',
				'desc'    => 'Check the boxes for the data you want to make private. If "All" is selected, the page will be private.',
				'options' => array(
					'hide_all'     => 'Hide All',
					'hide_dob'     => 'Date of Birth',
					'hide_socials' => 'Social Media',
				),
			)
		);
		// Privacy Notes
		$field_make_private_note = $cmb_actorside->add_field(
			array(
				'name' => 'Details on why an actor is private',
				'id'   => self::PREFIX . 'make_option_private_notes',
				'type' => 'textarea',
				'desc' => 'If we got an email or any such notes, we can put them here.',
			)
		);

		// Actor Sidebar Grid
		if ( ! is_admin() ) {
			return;
		} else {
			$grid_actorside = new \Forked\CMB2\CMB2Grid\Grid\Cmb2Grid( $cmb_actorside );
			$row1           = $grid_actorside->addRow();
			$row2           = $grid_actorside->addRow();
			$row3           = $grid_actorside->addRow();
			$row4           = $grid_actorside->addRow();
			$row5           = $grid_actorside->addRow();
			$row6           = $grid_actorside->addRow();
			$row7           = $grid_actorside->addRow();
			$row8           = $grid_actorside->addRow();
			$row9           = $grid_actorside->addRow();
			$row10          = $grid_actorside->addRow();
			$row11          = $grid_actorside->addRow();
			$row12          = $grid_actorside->addRow();
			$row13          = $grid_actorside->addRow();
			$row1->addColumns( array( $field_gender, $field_sexuality ) );
			$row2->addColumns( array( $field_romantic, $field_queer_override ) );
			$row3->addColumns( array( $field_pronouns ) );
			$row4->addColumns( array( $field_birth, $field_death ) );
			$row5->addColumns( array( $field_imdb, $field_wiki ) );
			$row6->addColumns( array( $field_home ) );
			$row7->addColumns( array( $field_twitter, $field_instagram ) );
			$row8->addColumns( array( $field_tumblr, $field_mastodon ) );
			$row9->addColumns( array( $field_facebook, $field_tiktok ) );
			$row10->addColumns( array( $field_bluesky, $field_twitch ) );
			$row11->addColumns( array( $field_wikidata ) );
			$row12->addColumns( array( $field_excerpt ) );
			$row13->addColumns( array( $field_make_private, $field_make_private_note ) );
		}
	}

	/*
	 * Remove Metaboxes we use elsewhere
	 */
	public function remove_metaboxes() {
		remove_meta_box( 'authordiv', 'post_type_actors', 'normal' );
		remove_meta_box( 'postexcerpt', 'post_type_actors', 'normal' );
	}
}
