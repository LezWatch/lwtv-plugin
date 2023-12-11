<?php
/**
 * Interface for classes that act as plugin components.
 *
 * @package LWTV
 */

namespace LWTV\_Components;

/**
 * Interface Component
 */
interface Component {
	/**
	 * Init the component. Hooks go in here.
	 *
	 * @return void
	 */
	public function init();
}
