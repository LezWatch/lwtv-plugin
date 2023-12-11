<?php
/**
 * Block_Types_Allowed class.
 *
 * @package WP_Theme_Template
 */

namespace LWTV\_Components;

use WP_Block_Type_Registry;

/**
 * Class for disabling certain block types.
 */
class Block_Types_Allowed implements Component {

	/**
	 * List of disallowed WP core block types.
	 *
	 * These are blocks we DO NOT want to run.
	 *
	 * @var array<string>
	 */
	const DISALLOWED_CORE_BLOCK_TYPES = array(
		'core/post-featured-image' => 'core/post-featured-image',
	);

	/**
	 * Register any needed hooks/filters.
	 */
	public function init() {
		add_filter( 'allowed_block_types_all', array( $this, 'filter_maybe_disable_block_types' ) );
	}

	/**
	 * Get all allowed core block types.
	 *
	 * @return array<string>
	 */
	public function get_disallowed_core_block_types() {
		/**
		 * Filters the list of disallowed core block types.
		 *
		 * @param array<string> $disallowed_core_block_types List of NOT allowed core block types.
		 */
		return apply_filters( 'lwtv_plugin_allowed_core_block_types', self::DISALLOWED_CORE_BLOCK_TYPES );
	}

	/**
	 * Filter the list of allowed blocks.
	 *
	 * @return array<string> Allowed blocks types.
	 */
	public function filter_maybe_disable_block_types() {
		$registered_block_types = WP_Block_Type_Registry::get_instance()->get_all_registered();
		$disallowed_block_types = self::get_disallowed_core_block_types();
		$allowed_block_types    = array();

		foreach ( $registered_block_types as $block_type => $_ ) {
			// Disable all WP core blocks not in the allow-list.
			if ( false === str_contains( $block_type, 'core/' ) || ! isset( $disallowed_block_types[ $block_type ] ) ) {
				$allowed_block_types[] = $block_type;
			}
		}

		/**
		 * List of allowed block types.
		 */
		return $allowed_block_types;
	}
}
