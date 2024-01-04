<?php
/**
 * Blocks tests.
 *
 * @package LWTV
 */

namespace LWTV\_Components;

use WP_Block_Type_Registry;

/**
 * Test Blocks.
 */
class Test_Blocks extends \WP_UnitTestCase {
	/**
	 * Test class instance.
	 */
	private $instance;
	private $blocks;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function set_up() {
		parent::set_up();

		$this->instance = new Blocks();
		$this->blocks = WP_Block_Type_Registry::get_instance()->get_all_registered();
	}

	/**
	 * Tests filter init logic.
	 */
	public function test_init() {
		$this->assertSame( 10, has_action( 'init', [ $this->instance, 'action_register_blocks' ] ) );
	}

	/**
	 * Tests that our serverside blocks exist
	 */
	public function test_serverside() {
		$this->assertArrayHasKey( 'lwtv/serverside', $this->blocks );
	}

	/**
	 * Tests that our common blocks exist
	 */
	public function test_common_gutenberg() {
		$this->assertArrayHasKey( 'lwtv/common-gutenberg', $this->blocks );
	}

	/**
	 * Tests that our featured image block exists
	 */
	public function test_feature_image() {
		$this->assertArrayHasKey( 'lez-library/featured-image', $this->blocks );
	}

	/**
	 * Tests that our grade block exists
	 */
	public function test_grade() {
		$this->assertArrayHasKey( 'lwtv/grade', $this->blocks );
	}

	/**
	 * Tests that our pre-publish checks exists
	 */
	public function test_pre_publish() {
		$this->assertArrayHasKey( 'lwtv/pre-publish-checks', $this->blocks );
	}

	/**
	 * Tests that our screener block exists
	 */
	public function test_screen() {
		$this->assertArrayHasKey( 'lwtv/screener', $this->blocks );
	}

	/**
	 * Tests that our spoiler block exists
	 */
	public function test_spoilers() {
		$this->assertArrayHasKey( 'lez-library/spoilers', $this->blocks );
	}

}
