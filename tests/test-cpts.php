<?php
/**
 * Class LWTV_Tests_CPTs
 *
 * Functionality tests for CPTs
 *
 * @package Lwtv_Plugin
 */

namespace LWTV\Tests;

use LWTV\_Components\CPTs;
use LWTV\Plugin;

/**
 * CPTs Tests.
 */
class CPTs_Test extends \WP_UnitTestCase {

	/**
	 * Test if the post types exists
	 */
	public function test_cpts_exists() {
		$actors     = post_type_exists( 'post_type_actors' );
		$characters = post_type_exists( 'post_type_characters' );
		$shows      = post_type_exists( 'post_type_shows' );
		$tvmaze     = post_type_exists( 'post_type_tvmaze' );
		$fake       = post_type_exists( 'post_type_faker' );

		$this->assertFalse( $fake );
		$this->assertTrue( $actors );
		$this->assertTrue( $characters );
		$this->assertTrue( $shows );
		$this->assertTrue( $tvmaze );
	}
}
