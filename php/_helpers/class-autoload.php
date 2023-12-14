<?php
/**
 * Set up Theme Autoloader.
 *
 * @package LWTV
 */

namespace LWTV\_Helpers;

/**
 * Class Autoload.
 *
 * @package LWTV
 */
class Autoload {

	/**
	 * Namespace to directory mapping.
	 *
	 * @var array<string, string>
	 */
	protected $namespace_dir_map = array();

	/**
	 * Set up the autoloader.
	 */
	public function __construct() {
		spl_autoload_register( array( $this, 'autoload' ) );
	}

	/**
	 * Add mapping for a directory to a namespace.
	 *
	 * @param string $maybe_namespace Namespace.
	 * @param string $dir Absolute path to the directory.
	 * @return void
	 */
	public function add( $maybe_namespace, $dir ) {
		$this->namespace_dir_map[ trim( $maybe_namespace, '/\\' ) ] = rtrim( $dir, '/\\' );

		krsort( $this->namespace_dir_map ); // Ensure the sub-namespaces are matched first.
	}

	/**
	 * Autoload the registered classes.
	 *
	 * @param string $maybe_class Fully qualified class name.
	 *
	 * @return void
	 */
	public function autoload( $maybe_class ) {
		$paths = $this->resolve( $maybe_class );

		foreach ( $paths as $path ) {
			if ( is_readable( $path ) ) {
				require_once $path;

				return; // Return as soon as we've resolved the first one.
			}
		}
	}

	/**
	 * Resolve the requested classname to the possible file path
	 * of a registered namespace by type (abstract, class, interface, trait).
	 *
	 * @param string $maybe_class Fully qualified class name.
	 *
	 * @return array<string> List of mapped file paths.
	 */
	public function resolve( $maybe_class ) {
		$prefixes = array( 'abstract', 'class', 'interface', 'trait' );

		foreach ( $this->namespace_dir_map as $namespace => $path ) {
			// Append the trailing slash to not match SomeClassName where SomeClass is defined.
			if ( 0 === strpos( $maybe_class, $namespace . '\\' ) ) {
				$maybe_class = substr( $maybe_class, strlen( $namespace ) + 1 );

				$file_path_template = $this->file_path_from_parts(
					array(
						$path,
						$this->class_to_file_path_template( $maybe_class, '{prefix}' ),
					)
				);

				return array_map(
					function ( $prefix ) use ( $file_path_template ) {
						return str_replace( '{prefix}', $prefix, $file_path_template );
					},
					$prefixes
				);
			}
		}

		return array();
	}

	/**
	 * Map fully qualified class names to file path
	 * according to WP coding standard rules.
	 *
	 * @param string $maybe_class Fully qualified class name.
	 * @param string $placeholder Placeholder string to use for the file type designation.
	 *
	 * @return string
	 */
	protected function class_to_file_path_template( $maybe_class, $placeholder = '{prefix}' ) {
		$class_parts = explode( '\\', $maybe_class );
		$class_name  = array_pop( $class_parts );

		// Map nested namespaces to sub-directories.
		if ( ! empty( $class_parts ) ) {
			$class_parts = array_map(
				array( $this, 'turn_class_to_file_name' ),
				$class_parts
			);
		}

		// Add filename at the end.
		$class_parts[] = sprintf(
			'%s-%s.php',
			$placeholder,
			$this->turn_class_to_file_name( $class_name )
		);

		return $this->file_path_from_parts( $class_parts );
	}

	/**
	 * Generate file path based on components and the system
	 * directory separator.
	 *
	 * @param array<string> $parts File path parts.
	 *
	 * @return string
	 */
	protected function file_path_from_parts( $parts ) {
		return implode( DIRECTORY_SEPARATOR, $parts );
	}

	/**
	 * Sanitize class name to filename according to WP coding standards.
	 *
	 * @param string $maybe_class Class name.
	 *
	 * @return string
	 */
	protected function turn_class_to_file_name( $maybe_class ) {
		$underscore_classes = array( '_Components', '_Helpers', '_Utils' );
		// If these are Components or Helpers, we keep the underscore
		if ( in_array( $maybe_class, $underscore_classes, true ) ) {
			return strtolower( $maybe_class );
		}

		// Else, whack it.
		return strtolower( str_replace( '_', '-', $maybe_class ) );
	}
}
