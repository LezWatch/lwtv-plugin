<?php
/**
 * Generator Wrapper for 'this year' data.
 *
 * @package LezWatch.TV
 */

namespace LWTV\This_Year;

use LWTV\This_Year\The_Array;
use LWTV\This_Year\The_Output;

class Generator {
	/**
	 * Generate data
	 *
	 * @param string $this_year  Year
	 * @param string $view       The context we're viewing (characters on air, shows on air, etc)
	 * @param mixed  $custom     Custom data we may (or may not) need.
	 *
	 * @return N/A
	 */
	public static function make( $this_year, $view, $custom = false ) {
		// Build Array.
		$build_data = ( new The_Array() )->make( $this_year, $view, $custom );

		// If the array is not empty, build
		if ( ! empty( $build_data ) ) {
			( new The_Output() )->make( $this_year, $view, $build_data, $custom );
		} else {
			// No data, so we need to output a message.
			$title = str_replace( '-', ' ', $view );
			?>
			<div class="alert alert-info" role="alert">
				<h4 class="alert-heading">No <?php echo esc_html( ucwords( $title ) ); ?></h4>
				<p>There are no <?php echo esc_html( $title ); ?> recorded for <?php echo esc_html( $this_year ); ?>.</p>
			</div>
			<?php
		}
	}
}
