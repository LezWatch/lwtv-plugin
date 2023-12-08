<?php
/**
 * Interface for classes that return template functions.
 *
 * @package LWTV
 */

namespace LWTV\_Components;

/**
 * Interface Templater
 */
interface Templater {
	/**
	 * Gets tags to expose as methods accessible through `lwtv_plugin()`.
	 *
	 * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
	 *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
	 *               adding support for further arguments in the future.
	 */
	public function get_template_tags(): array;
}
