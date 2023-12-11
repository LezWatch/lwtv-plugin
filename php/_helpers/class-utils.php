<?php
/**
 * Holds common utility functions.
 *
 * @package LWTV
 */

namespace LWTV\_Helpers;

/**
 * Class Utils
 */
class Utils {
	/**
	 * Check whether a class implements an interface. If not, throw exception.
	 *
	 * @param string|object $class_being_checked Class being checked.
	 * @param string        $target_interface    The interface to check against.
	 *
	 * @throws \RuntimeException Throws exception if condition passes.
	 * @return void
	 */
	public static function throw_if_not_of_type( $class_being_checked, $target_interface ) {
		if ( is_object( $class_being_checked ) && ! ( $class_being_checked instanceof $target_interface ) ) {
			throw new \RuntimeException( esc_attr( get_class( $class_being_checked ) ) . ' must implement ' . esc_attr( $target_interface ) );
		}

		if ( is_object( $class_being_checked ) && ! is_a( $class_being_checked, $target_interface, true ) ) {
			throw new \RuntimeException( esc_attr( get_class( $class_being_checked ) ) . ' must implement ' . esc_attr( $target_interface ) );
		}
	}

	/**
	 * Reliably detect if the request is an API or Ajax request.
	 *
	 * @return bool
	 */
	public static function is_api_call() {
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return true;
		}

		if ( wp_doing_ajax() ) {
			return true;
		}

		return false;
	}

	/**
	 * Is WP debug mode enabled.
	 *
	 * @return boolean
	 */
	public static function is_debug() {
		return ( defined( 'WP_DEBUG' ) && WP_DEBUG );
	}

	/**
	 * Is WP script debug mode enabled.
	 *
	 * @return boolean
	 */
	public static function is_script_debug() {
		return ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG );
	}
}
