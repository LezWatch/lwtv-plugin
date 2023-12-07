<?php
/**
 * Class LWTV_Tests_Roles
 *
 * Functionality tests for Ways to Watch
 *
 * @package Lwtv_Plugin
 */

namespace LWTV\Tests;

use LWTV\Features\Roles;
use LWTV\Plugin;

/**
 * Roles Tests.
 */
class Roles_Test extends \WP_UnitTestCase {

	/**
	 * Test if the role exists
	 */
	public function test_role_exists() {
		$true_role  = ( new Roles() )->role_exists( 'data_editor' );
		$false_role = ( new Roles() )->role_exists( 'fake_role' );

		$this->assertFalse( $false_role );
		$this->assertTrue( $true_role );
	}
}
