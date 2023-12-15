<?php
/**
 * Class LWTV_Tests_CPTs
 *
 * Functionality tests for CPTs
 *
 * @package Lwtv_Plugin
 */

namespace LWTV\Tests;

use LWTV\Plugin;
use LWTV\CPTs\Actors;
use LWTV\CPTs\Characters;
use LWTV\CPTs\Shows;
use LWTV\CPTs\TVMaze;

/**
 * CPTs Tests.
 */
class CPTs_Test extends \WP_UnitTestCase {

	/**
	 * Test if the post types exists
	 */
	public function test_cpts_exists() {
		$actors     = post_type_exists( Actors::SLUG );
		$characters = post_type_exists( Characters::SLUG );
		$shows      = post_type_exists( Shows::SLUG );
		$tvmaze     = post_type_exists( TVMaze::SLUG );
		$fake       = post_type_exists( 'post_type_faker' );

		$this->assertFalse( $fake );
		$this->assertTrue( $actors );
		$this->assertTrue( $characters );
		$this->assertTrue( $shows );
		$this->assertTrue( $tvmaze );
	}

	/**
	 * Test if the ACTOR taxonomies exist
	 */
	public function test_actor_taxonomies_exists() {

		foreach( Actors::ALL_TAXONOMIES as $taxonomy => $items ) {
			$taxonomy_exists = taxonomy_exists( $taxonomy );
			$this->assertTrue( $taxonomy_exists );
		}

		$fake_taxonomy = taxonomy_exists( 'lezactors_fake' );
		$this->assertFalse( $fake_taxonomy );
	}

	/**
	 * Test if the Character taxonomies exist
	 */
	public function test_character_taxonomies_exists() {

		foreach( Characters::ALL_TAXONOMIES as $taxonomy => $items ) {
			$taxonomy_exists = taxonomy_exists( $taxonomy );
			$this->assertTrue( $taxonomy_exists );
		}

		$fake_taxonomy = taxonomy_exists( 'lezactors_fake' );
		$this->assertFalse( $fake_taxonomy );
	}

	/**
	 * Test if the Show taxonomies exist
	 */
	public function test_show_taxonomies_exists() {

		foreach( Shows::ALL_TAXONOMIES as $taxonomy => $items ) {
			$taxonomy_exists = taxonomy_exists( $taxonomy );
			$this->assertTrue( $taxonomy_exists );
		}

		$fake_taxonomy = taxonomy_exists( 'lezactors_fake' );
		$this->assertFalse( $fake_taxonomy );
	}

}
