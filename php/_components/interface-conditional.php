<?php
/**
 * Interface for classes that can be conditionally loaded.
 *
 * @package LWTV
 */

namespace LWTV\_Components;

/**
 * Interface Component
 */
interface Conditional {
	/**
	 * When true returned, the component will be loaded.
	 *
	 * @return bool
	 */
	public function is_needed(): bool;
}
