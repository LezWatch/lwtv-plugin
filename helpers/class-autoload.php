<?php
/**
 * Set up Autoloader.
 *
 * @package LWTV
 */

namespace LWTV\Helpers;

/**
 * Class Autoload.
 */
class Autoload {

	private const SUBFOLDERS = array( 'build', 'format', 'templates' );

	/**
	 * Set up the autoloader.
	 */
	public function __construct() {
		spl_autoload_register( array( $this, 'autoload' ) );
	}

	/**
	 * Add components.
	 *
	 * @param  string $components
	 * @return void
	 */
	public function add( $component ) {
		new $component();
	}

	/**
	 * Autoload the registered classes.
	 *
	 * @param string $auto_class Fully qualified class name.
	 *
	 * @return void
	 */
	public function autoload( $auto_class ) {

		// Only run for LezWatch
		if ( ! str_starts_with( $auto_class, 'LWTV_' ) ) {
			return;
		}

		// LWTV_Theme_Data_Author => lwtv-theme-data-author
		$filename = strtolower( str_replace( '_', '-', $auto_class ) );

		// lwtv-theme-data-author => theme
		$folder = self::parse_folder( $filename );

		// If there's no folder, bail early.
		if ( is_null( $folder ) ) {
			return;
		}

		// lwtv-theme-data-author => class-data-author
		$filename = self::parse_filename( $folder, $filename );

		// Path = /theme/class-data-author.php
		$path = dirname( __DIR__, 1 ) . '/' . $folder . '/' . $filename . '.php';

		// @TODO: Give a better fallback.
		// If the folder exists but not the path, call the default
		if ( is_dir( dirname( __DIR__, 1 ) . '/' . $folder . '/' ) && ! file_exists( $path ) ) {
			// LWTV_This_Year => /this-year/class-this-year.php
			$path = dirname( __DIR__, 1 ) . '/' . $folder . '/class-' . $folder . '.php';
		}

		if ( is_readable( $path ) ) {
			require_once $path; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable, WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			return; // Return as soon as we've resolved the first one.
		}
	}

	/**
	 * Parse the folder name as some have two names.
	 *
	 * @param string $name Cleaned up name of Class
	 *
	 * @return mixed       Folder name or null.
	 */
	private function parse_folder( $name ) {
		// lwtv-theme-data-author => theme
		// lwtv-this-year         => this-year
		$name_array = explode( '-', $name );

		$i      = 1;
		$count  = count( $name_array );
		$folder = $name_array[ $i ];

		// Loop through the possible names to find the first one that's valid.
		while ( $i < $count ) {
			// Check for a special 'end' like LWTV_This_Year_Characters_Dead_Build => /this-year/build/class-characters-dead.php
			$end       = self::last_item( $name_array );
			$subfolder = ( in_array( $end, self::SUBFOLDERS, true ) ) ? '/' . $end : '';

			// If the folder exists, return it and stop.
			if ( is_dir( dirname( __DIR__, 1 ) . '/' . $folder . $subfolder ) ) {
				return $folder . $subfolder;
			}

			// If not, add another item to the folder name and try again.
			++$i;
			if ( ! isset( $name_array[ $i ] ) ) {
				continue;
			}
			$folder .= '-' . $name_array[ $i ];
		}

		return null;
	}

	/**
	 * Parse the filename
	 *
	 * @param string $folder    Name of folder
	 * @param string $filename  Name of file
	 *
	 * @return string $filename Updated file name
	 */
	private function parse_filename( $folder, $filename ) {
		// folder = this-year/build => array( 'this-year', 'build ) => this-year
		$folder_array = explode( '/', $folder );
		$folder       = $folder_array[0];

		// lwtv-this-year-characters-dead-build => class-characters-dead-build
		$filename = str_replace( 'lwtv-' . $folder, 'class', $filename );

		// Remove SUBFOLDER from end of filename.
		foreach ( self::SUBFOLDERS as $subfolder ) {
			// class-characters-dead-build => class-characters-dead
			$filename = self::str_last_replace( $subfolder, '', $filename );
		}

		$filename = rtrim( $filename, '-' );

		return $filename;
	}

	/**
	 * Get the last item in the array in a non-destructive manner.
	 *
	 * Using end() moves your pointer, but we need to keep our place.
	 *
	 * @param array &$name_array Array to parse
	 *
	 * @return mixed Null or String of last item.
	 */
	private function last_item( array &$name_array ) {

		if ( empty( $name_array ) ) {
			return null;
		}

		end( $name_array );

		$end = &$name_array[ key( $name_array ) ];

		return $end;
	}

	/**
	 * Replace last existence of item in string.
	 *
	 * @param string $search  Term to search for
	 * @param string $replace Term to replace with
	 * @param string $subject String to search in
	 *
	 * @return string $subject Updated Subject (or not)
	 */
	private function str_last_replace( $search, $replace, $subject ) {
		$pos = strrpos( $subject, $search );

		if ( false !== $pos ) {
			$subject = substr_replace( $subject, $replace, $pos, strlen( $search ) );
		}

		return $subject;
	}
}
