<?php
/**
 * Class Rest_API_Test
 *
 * Functionality tests for Rest_API
 *
 * @package Lwtv_Plugin
 */

namespace LWTV\Tests;

use LWTV\_Components\Rest_API;
use LWTV\Rest_API\What_Happened_JSON;
use LWTV\Rest_API\Stats_JSON;
use LWTV\_Components\Plugins;

/**
 * Ways to Watch Tests.
 */
class Rest_API_Test extends \WP_UnitTestCase {

	/**
	 * Test class instance.
	 */
	private $instance;
	private $char_death_1;
	private $char_death_2;
	private $today;
	private $show_deadchars_post_id;
	private $show_alivechars_post_id;
	private $show_startednow_post_id;
	private $show_endednow_post_id;
	private $actor_dead_post_id;
	private $actor_alive_post_id;
	private $character_1_post_id;
	private $character_2_post_id;
	private $character_3_post_id;
	private $get_what_happened;
	private $get_statistics;

	/**
	 * Set up.
	 *
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();
		$this->instance  = new Rest_API();

		$this->char_death_1 = '2016-03-03';
		$this->char_death_2 = date( 'Y-m-d' );
		$this->today        = date( 'Y-m-d' );

		// Make a Show with dead characters
		$show_deadchars = array (
			'post_title'   => 'Fake Show with Dead',
			'post_content' => 'This is a post about a show and we will pretend things',
			'post_status'  => 'publish',
			'post_author'  => 1,
			'post_type'    => 'post_type_shows',
			'meta_input'   => array(
				'lezshows_airdates' => array (
					'start'  => 2014,
					'finish' => 2020,
				),
			),
		);
		$show_deadchars_post_id = $this->factory->post->create( $show_deadchars );

		// Make a Show without dead
		$show_alivechars = array(
			'post_title'   => 'Fake Show without Dead',
			'post_content' => 'This is a post about a show and we will pretend things',
			'post_status'  => 'publish',
			'post_author'  => 1,
			'post_type'    => 'post_type_shows',
			'meta_input'   => array(
				'lezshows_airdates' => array (
					'start'  => 2010,
					'finish' => 'current',
				),
			),
		);
		$this->show_alivechars_post_id = $this->factory->post->create( $show_alivechars );

		// Make a Show that started now
		$show_startednow = array(
			'post_title'   => 'Fake Show that Started NOW',
			'post_content' => 'This is a post about a show and we will pretend things',
			'post_status'  => 'publish',
			'post_author'  => 1,
			'post_type'    => 'post_type_shows',
			'meta_input'   => array(
				'lezshows_airdates' => array (
					'start'  => date( 'Y' ),
					'finish' => 'current',
				),
			),
		);
		$this->show_startednow_post_id = $this->factory->post->create( $show_startednow );

		// Make a Show that ended now
		$show_endednow = array(
			'post_title'   => 'Fake Show that Ended NOW',
			'post_content' => 'This is a post about a show and we will pretend things',
			'post_status'  => 'publish',
			'post_author'  => 1,
			'post_type'    => 'post_type_shows',
			'meta_input'   => array(
				'lezshows_airdates' => array (
					'start'  => date( 'Y', strtotime( '-3 years' ) ),
					'finish' => date( 'Y' ),
				),
			),
		);
		$this->show_endednow_post_id = $this->factory->post->create( $show_endednow );

		// Make an actor who is dead
		$actor_dead = array(
			'post_title'   => 'Fake Actor Dead',
			'post_content' => 'This is a post about a character and we will pretend things',
			'post_status'  => 'publish',
			'post_author'  => 1,
			'post_type'    => 'post_type_actors',
			'meta_input'   => array(
				'lezactors_birth'      => date( 'Y-m-d', strtotime( '-91 years' ) ),
				'lezactors_death'      => $this->today,
				'lezactors_char_count' => 2,
			),
		);
		$this->actor_dead_post_id = $this->factory->post->create( $actor_dead );

		// Make an actor who is alive
		$actor_alive = array(
			'post_title'   => 'Fake Actor Alive',
			'post_content' => 'This is a post about a character and we will pretend things',
			'post_status'  => 'publish',
			'post_author'  => 1,
			'post_type'    => 'post_type_actors',
			'meta_input'   => array(
				'lezactors_birth'      => date( 'Y-m-d', strtotime( '-31 years' ) ),
				'lezactors_char_count' => 1,
			),
		);
		$this->actor_alive_post_id = $this->factory->post->create( $actor_alive );

		// Make a Character to use.
		$character_1 = array(
			'post_title'   => 'Fake Character 1',
			'post_content' => 'This is a post about a character and we will pretend things',
			'post_status'  => 'publish',
			'post_author'  => 1,
			'post_type'    => 'post_type_characters',
			'meta_input'   => array(
				'lezchars_last_death' => $this->char_death_1,
				'lezchars_death_year' => array( $this->char_death_1 ),
				'lezchars_actor'      => array( $this->actor_dead_post_id ),
				'lezchars_show_group' => array(
					array(
						'show'    => $this->show_deadchars_post_id,
						'type'    => 'recurring',
						'appears' => array( '2016' ),
					),
				),
			),
		);
		$this->character_1_post_id = $this->factory->post->create( $character_1 );
		$shadow_character_1        = $this->factory->term->create(
			array(
				'taxonomy' => 'shadow_tax_characters',
				'name'     => 'Fake Character 1',
				'slug'     => 'fake-character-1',
			)
		);
		update_term_meta( $shadow_character_1, 'shadow_shadow_tax_characters_post_id', $this->character_1_post_id );

		// Make a second Character to use.
		$character_2 = array(
			'post_title'   => 'Fake Character 2',
			'post_content' => 'This is a post about a character and we will pretend things',
			'post_status'  => 'publish',
			'post_author'  => 1,
			'post_type'    => 'post_type_characters',
			'meta_input'   => array(
				'lezchars_last_death' => $this->char_death_2,
				'lezchars_death_year' => array( $this->char_death_2 ),
				'lezchars_actor'      => array( $this->actor_alive_post_id ),
				'lezchars_show_group' => array(
					array(
						'show'    => $this->show_deadchars_post_id,
						'type'    => 'recurring',
						'appears' => array( '2018' ),
					),
				),
			),
		);
		$this->character_2_post_id = $this->factory->post->create( $character_2 );
		$shadow_character_2        = $this->factory->term->create(
			array(
				'taxonomy' => 'shadow_tax_characters',
				'name'     => 'Fake Character 2',
				'slug'     => 'fake-character-2',
			)
		);
		update_term_meta( $shadow_character_2, 'shadow_shadow_tax_characters_post_id', $this->character_2_post_id );

		// Make a third Character to use.
		$character_3 = array(
			'post_title'   => 'Fake Character 3',
			'post_content' => 'This is a post about a character and we will pretend things',
			'post_status'  => 'publish',
			'post_author'  => 1,
			'post_type'    => 'post_type_characters',
			'meta_input'   => array(
				'lezchars_actor'      => array( $this->actor_dead_post_id ),
				'lezchars_show_group' => array(
					array(
						'show'    => $this->show_alivechars_post_id,
						'type'    => 'recurring',
						'appears' => array( '2018' ),
					),
				),
			),
		);
		$this->character_3_post_id = $this->factory->post->create( $character_3 );
		$shadow_character_3        = $this->factory->term->create(
			array(
				'taxonomy' => 'shadow_tax_characters',
				'name'     => 'Fake Character 3',
				'slug'     => 'fake-character-3',
			)
		);
		update_term_meta( $shadow_character_3, 'shadow_shadow_tax_characters_post_id', $this->character_3_post_id );

		// Make the BYQ taxonomy and add it to one show
		$tropes  = get_taxonomy( 'lez_tropes' );
		$add_byq = wp_insert_term(
			'Bury Your Queers',   // the term
			'lez_tropes', // the taxonomy
			array(
				'description' => 'BYQ',
				'slug'        => 'dead-queers',
			)
		);
		wp_set_object_terms( $this->show_deadchars_post_id, $add_byq['term_id'], 'lez_tropes' );

		// Make the dead taxonomy and apply it to two characters
		$cliches  = get_taxonomy( 'lez_cliches' );
		$add_dead = wp_insert_term(
			'Dead Queers',   // the term
			'lez_cliches', // the taxonomy
			array(
				'description' => 'Dead Chars',
				'slug'        => 'dead',
			)
		);
		wp_set_object_terms( $this->character_1_post_id, $add_dead['term_id'], 'lez_cliches' );
		wp_set_object_terms( $this->character_2_post_id, $add_dead['term_id'], 'lez_cliches' );

		// And finally what do we expect this to be:
		$this->get_what_happened = array(
			'dead_year'  => 1,
			'dead'       => 1,
			'posts'      => 0,
			'shows'      => 4,
			'characters' => 3,
			'actors'     => 2,
			'on_air'     => array(
				'current' => 3,
				'started' => 1,
				'ended'   => 1,
			),
		);

		$this->get_statistics = array(
			'simple_chars' => array(
				'total'                => 3,
				'dead'                 => 2,
				'genders'              => 0,
				'sexualities'          => 0,
				'romantic_orientation' => 0,
				'cliches'              => 1,
			),
			'complex_chars' => array(
				'Fake Character 1' => array(
					'id' => $this->character_1_post_id,
					'died' => array( $this->char_death_1 ),
					'actors' => 1,
					'shows' => 1,
					'gender' => '',
					'sexuality' => '',
					'url' => get_the_permalink( $this->character_1_post_id ),
				),
				'Fake Character 2' => array(
					'id' => $this->character_2_post_id,
					'died' => array( $this->char_death_2 ),
					'actors' => 1,
					'shows' => 1,
					'gender' => '',
					'sexuality' => '',
					'url' => get_the_permalink( $this->character_2_post_id ),
				),
				'Fake Character 3' => array(
					'id' => $this->character_3_post_id,
					'died' => array( '' ),
					'actors' => 1,
					'shows' => 1,
					'gender' => '',
					'sexuality' => '',
					'url' => get_the_permalink( $this->character_3_post_id ),
				),
			),
		);

	}

	/**
	 * Confirm last death is calculated correctly.
	 */
	public function test_json_last_death() {
		$last_death_array = lwtv_plugin()->get_json_last_death();
		$last_death       = gmdate( 'Y-m-d', $last_death_array['died'] );

		$this->assertNotSame( $this->char_death_1, $last_death );
		$this->assertSame( $this->char_death_2, $last_death );
	}

	/**
	 * Confirm the posts we expect to have, we do have!
	 */
	public function test_what_happened() {
		$get_what_happened = ( new What_Happened_JSON() )->what_happened( $this->today );

		$this->assertSame( $get_what_happened, $this->get_what_happened );
	}

	public function test_statistics() {
		$simple_chars  = ( new Stats_JSON() )->statistics();
		$complex_chars = ( new Stats_JSON() )->statistics( 'characters', 'complex' );

		$this->assertSame( $simple_chars, $this->get_statistics['simple_chars'] );
		$this->assertSame( $complex_chars, $this->get_statistics['complex_chars'] );
	}

}
