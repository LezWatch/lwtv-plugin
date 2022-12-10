<?php
/**
 * Name: CMB2 Metaboxes
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

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
		add_action( 'cmb2_init', array( $this, 'cmb2_metaboxes' ) );
		add_action( 'admin_menu', array( $this, 'remove_metaboxes' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_wikidata_meta_box' ) );
	}

	/*
	 * Non CMB2 Metaboxes
	 */
	public function add_wikidata_meta_box() {
		add_meta_box(
			'metabox_lezactors_wikidata',
			'WikiData',
			array( $this, 'wikidata_meta_box_callback' ),
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
	public function wikidata_meta_box_callback( $post ) {

		// If it's an auto draft, we do nothing. Else, we roll.
		if ( ! isset( $post->ID ) || 'draft' === get_post_status( $post->ID ) || 'auto-draft' === get_post_status( $post->ID ) || '' === get_the_title( $post->ID ) ) {
			$wikidata = 'auto-draft';
		} else {
			( new LWTV_Debug() )->check_actors_wikidata( $post->ID );
			$wikidata = get_post_meta( $post->ID, '_lezactors_wikidata' );
		}

		// If Wikidata isn't empty AND there's a valid Q code, we go
		if ( ! empty( $wikidata ) && ! empty( $wikidata['0']['wikidata'] ) ) {

			// Build URL
			$wikidata_url = 'https://www.wikidata.org/wiki/' . $wikidata['0']['wikidata'];

			// Clean array
			unset( $wikidata['0']['id'] );
			unset( $wikidata['0']['name'] );
			unset( $wikidata['0']['wikidata'] );

			foreach ( $wikidata['0'] as $datatype => $result ) {
				if ( 'match' === $result || 'n/a' === $result ) {
					unset( $wikidata['0'][ $datatype ] );
				}
			}
		} elseif ( empty( $wikidata ) ) {
			$wikidata = false;
		}

		if ( ! $wikidata ) {
			echo '<p>No information for ' . esc_html( get_the_title( $post->ID ) ) . ' found in WikiData.</p>';
		} elseif ( 'auto-draft' === $wikidata ) {
			echo '<p>WikiData checks pending. Once we fill in some information, it will be able to check.</p>';
			// To Do: A button here to trigger the check
		} elseif ( ! isset( $wikidata_url ) ) {
			echo '<p>There is no WikiData available on this actor.</p>';
		} elseif ( empty( $wikidata['0'] ) ) {
			echo '<p>All data for ' . esc_html( get_the_title( $post->ID ) ) . ' matches <a href="' . esc_url( $wikidata_url ) . '" target="_blank">WikiData!</a></p>';
		} else {
			echo '<p>The following data does not match <a href="' . esc_url( $wikidata_url ) . '" target="_blank">WikiData!</a>:</p>';
			echo '<ul>';
			foreach ( $wikidata['0'] as $datatype => $result ) {
				echo '<li><strong>' . esc_html( ucfirst( $datatype ) ) . ':</strong><ul><li><em>Our Data:</em> ' . esc_html( $result['ours'] ) . '</li><li><em>WikiData:</em> ' . esc_html( $result['wikidata'] ) . '</li></ul></li>';
			}
			echo '<ul>';

			echo '<p>Please double check. WikiData is sometimes wrong about Social Media.</p>';

			echo '<p>(Warning: This doesn\'t currently refresh on save.)</p>';
			// To Do: A button here to rerun the check.
		}
	}

	/*
	 * CMB2 Metaboxes
	 */
	public function cmb2_metaboxes() {
		// prefix for all custom fields
		$prefix = 'lezactors_';

		// Metabox Group: Quick Dropdowns
		$cmb_actorside = new_cmb2_box(
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
				'id'               => $prefix . 'gender',
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
				'id'               => $prefix . 'sexuality',
				'taxonomy'         => 'lez_actor_sexuality',
				'type'             => 'taxonomy_select',
				'default'          => 'unknown',
				'show_option_none' => false,
				'remove_default'   => 'true',
			)
		);
		// Field: Actor Pronouns
		$field_pronouns = $cmb_actorside->add_field(
			array(
				'name'              => 'Pronouns',
				'desc'              => 'Pronouns (Optional)',
				'id'                => $prefix . 'pronouns',
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
				'id'          => $prefix . 'birth',
				'type'        => 'text_date',
				'date_format' => 'Y-m-d',
			)
		);
		// Field: Year of Death (if applicable)
		$field_death = $cmb_actorside->add_field(
			array(
				'name'        => 'Date of Death',
				'desc'        => 'If applicable',
				'id'          => $prefix . 'death',
				'type'        => 'text_date',
				'date_format' => 'Y-m-d',
			)
		);
		// Field: IMDb ID
		$field_imdb = $cmb_actorside->add_field(
			array(
				'name'       => 'IMDb ID',
				'id'         => $prefix . 'imdb',
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
				'id'         => $prefix . 'wikipedia',
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
				'id'         => $prefix . 'homepage',
				'type'       => 'text_url',
				'attributes' => array(
					'placeholder' => 'https://actorname.com',
				),
			)
		);
		// Field: Twitter ID
		$field_twitter = $cmb_actorside->add_field(
			array(
				'name'       => 'Twitter ID',
				'id'         => $prefix . 'twitter',
				'type'       => 'text',
				'attributes' => array(
					'placeholder' => 'Ex: aliliebert - without the @',
				),
			)
		);
		// Field: Tumblr ID
		$field_tumblr = $cmb_actorside->add_field(
			array(
				'name'       => 'Tumblr ID',
				'id'         => $prefix . 'tumblr',
				'type'       => 'text',
				'attributes' => array(
					'placeholder' => 'Ex: tiredandlonelymuse',
				),
			)
		);
		// Field: Instagram ID
		$field_instagram = $cmb_actorside->add_field(
			array(
				'name'       => 'Instagram ID',
				'id'         => $prefix . 'instagram',
				'type'       => 'text',
				'attributes' => array(
					'placeholder' => 'Ex: whododatlikedat',
				),
			)
		);
		// Field: Excerpt
		$field_excerpt = $cmb_actorside->add_field(
			array(
				'name'      => 'Additional Notes',
				'id'        => 'excerpt',
				'desc'      => 'Enter any terms here that should be used for search. For example, if someone change their name, list their deadname here so it can be searched.',
				'type'      => 'textarea',
				'escape_cb' => false,
				'default'   => get_post_field( 'post_excerpt' ),
			)
		);

		// Actor Sidebar Grid
		if ( ! is_admin() ) {
			return;
		} else {
			$grid_actorside = new \Cmb2Grid\Grid\Cmb2Grid( $cmb_actorside );
			$row1           = $grid_actorside->addRow();
			$row2           = $grid_actorside->addRow();
			$row3           = $grid_actorside->addRow();
			$row4           = $grid_actorside->addRow();
			$row5           = $grid_actorside->addRow();
			$row6           = $grid_actorside->addRow();
			$row7           = $grid_actorside->addRow();
			$row1->addColumns( array( $field_gender, $field_sexuality ) );
			$row2->addColumns( array( $field_pronouns ) );
			$row3->addColumns( array( $field_birth, $field_death ) );
			$row4->addColumns( array( $field_imdb, $field_wiki ) );
			$row5->addColumns( array( $field_home ) );
			$row6->addColumns( array( $field_twitter, $field_tumblr, $field_instagram ) );
			$row7->addColumns( array( $field_excerpt ) );
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

new LWTV_Actors_CMB2();
