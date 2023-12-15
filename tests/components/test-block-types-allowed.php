<?php
/**
 * Block_Types_Allowed tests.
 *
 * @package LWTV
 */

namespace LWTV\_Components;

/**
 * Test Block_Types_Allowed.
 */
class Test_Block_Types_Allowed extends \WP_UnitTestCase {

	/**
	 * Test class instance.
	 *
	 * @var Block_Types_Allowed
	 */
	private $instance;

	/**
	 * Setup.
	 *
	 * @inheritdoc
	 */
	public function set_up() {
		parent::set_up();

		$this->instance = new Block_Types_Allowed();
	}

	/**
	 * Tests filter init logic.
	 */
	public function test_init() {
		$this->assertFalse( has_filter( 'allowed_block_types_all', [ $this->instance, 'filter_maybe_disable_block_types' ] ) );
		$this->instance->init();
		$this->assertEquals( 10, has_filter( 'allowed_block_types_all', [ $this->instance, 'filter_maybe_disable_block_types' ] ) );
	}

	/**
	 * Allowed core blocks contain expected values.
	 *
	 * core/post-featured-image is NOT allowed
	 */
	public function test_get_allowed_core_block_types() {
		$disallowed_core_blocks = $this->instance->get_disallowed_core_block_types();

		$this->assertIsArray( $disallowed_core_blocks );
		$this->assertNotContains( 'core/columns', $disallowed_core_blocks );
		$this->assertContains( 'core/post-featured-image', $disallowed_core_blocks );
	}

	/**
	 * Allowed core blocks contain expected values.
	 *
	 * This time we're adding core/columns.
	 */
	public function test_get_allowed_core_block_types_filter() {
		$disallowed_core_blocks = $this->instance->get_disallowed_core_block_types();

		add_filter(
			'lwtv_plugin_allowed_core_block_types',
			function ( $block_types ) {
				$block_types[] = 'core/columns';
				return $block_types;
			}
		);

		// This means the filter ADDED core/columns as disallowed
		$this->assertContains(
			'core/columns',
			$this->instance->get_disallowed_core_block_types(),
			'Filter can enable other core blocks'
		);
	}
}
