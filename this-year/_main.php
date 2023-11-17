<?php
/**
 * Functions that power the "This Year" pages
 *
 * @package LezWatch.TV
 */

require 'query_vars.php';

class LWTV_This_Year {

	/**
	 * Array of Data types and their associated classes.
	 */
	const DATA_CLASS_MATCHER = array(
		'characters-on-air' => 'Characters_List',
		'dead-characters'   => 'Characters_Dead',
		'shows-on-air'      => 'Shows_List',
		'new-shows'         => 'Shows_List',
		'canceled-shows'    => 'Shows_List',
		'overview'          => 'Overview',
		'chart'             => 'Characters_List',
	);

	/**
	 * Array of Data types and their associated classes.
	 */
	const FORMAT_CLASS_MATCHER = array(
		'characters-on-air' => 'Characters',
		'dead-characters'   => 'Dead',
		'shows-on-air'      => 'Shows',
		'new-shows'         => 'Shows',
		'canceled-shows'    => 'Shows',
		'overview'          => 'Default',
		'chart'             => 'Chart',
	);

	/**
	 * Build the stuff for this year
	 *
	 * @param  int    $this_year the year.
	 *
	 * @return n/a    outputs everything
	 */
	public function display( $this_year ) {
		$this_year   = ( isset( $this_year ) ) ? $this_year : gmdate( 'Y' );
		$valid_views = array( 'characters-on-air', 'dead-characters', 'shows-on-air', 'new-shows', 'canceled-shows' );
		$view        = get_query_var( 'view', 'overview' );
		$baseurl     = ( gmdate( 'Y' ) !== $this_year ) ? '/this-year/' . $this_year . '/' : '/this-year/';

		?>
		<div class="container">
			<ul class="nav nav-tabs">
				<?php
				echo '<li class="nav-item"><a class="nav-link' . esc_attr( ( 'overview' === $view ) ? ' active' : '' ) . '" href="' . esc_url( $baseurl ) . '">OVERVIEW</a></li>';
				foreach ( $valid_views as $the_view ) {
					$active = ( $view === $the_view ) ? ' active' : '';
					echo '<li class="nav-item"><a class="nav-link' . esc_attr( $active ) . '" href="' . esc_url( $baseurl . $the_view ) . '/">' . esc_html( strtoupper( str_replace( '-', ' ', $the_view ) ) ) . '</a></li>';
				}
				?>
			</ul>

			<p>&nbsp;</p>

			<?php
			if ( ! in_array( $view, $valid_views, true ) ) {
				$view = 'overview';
			}

			// Generate data
			self::generator( $this_year, $view );

			// Navigation
			self::navigation( $this_year, $view );

			?>
		</div>
		<?php
	}

	/**
	 * Generate data
	 *
	 * @param string $this_year  Year
	 * @param string $view       View
	 *
	 * @return N/A
	 */
	public function generator( $this_year, $view, $custom = false ) {
		// Build Array.
		$build_data = self::build_array( $this_year, $view, $custom );

		// If the array is not empty, build
		if ( ! empty( $build_data ) ) {
			self::build_format( $this_year, $view, $build_data, $custom );
		}
	}

	public function build_array( $this_year, $view, $custom = false, $count = false ) {
		// If there's no data match, return empty:
		if ( ! isset( self::DATA_CLASS_MATCHER[ $view ] ) ) {
			return;
		}

		// Define Data Class.
		$data_class = self::DATA_CLASS_MATCHER[ $view ];

		// Define the class file: 'Characters_List' becomes 'class-characters-list.php'
		$build_file = __DIR__ . '/build/class-' . strtolower( str_replace( '_', '-', $data_class ) ) . '.php';

		// If the class file does not exist, bail.
		if ( ! file_exists( $build_file ) ) {
			return;
		}

		// Build Params, based on Data type.
		$build_params = array(
			'Characters_List' => array( $this_year, $count ), // get_list
			'Characters_Dead' => array( $this_year, $count ), // get_dead
			'Shows_List'      => array( $this_year, $view, $count ), // get_list
			'Overview'        => array( $this_year ),
		);

		require_once $build_file;
		$build_class  = 'LWTV_This_Year_' . $data_class;
		$build_params = $build_params[ $data_class ];

		if ( is_array( $build_params ) ) {
			$build_class_var = new $build_class();
			$array           = call_user_func_array( array( $build_class_var, 'build' ), $build_params );
		} else {
			$array = ( new $build_class() )->build( $build_params );
		}

		return $array;
	}

	public function build_format( $this_year, $view, $build_data, $custom = false, $count = false ) {
		// If there's no valid format, bail.
		if ( ! isset( self::FORMAT_CLASS_MATCHER[ $view ] ) ) {
			return;
		}

		// Define match:
		$format_class_match = self::FORMAT_CLASS_MATCHER[ $view ];

		// Define the class file: 'Characters_List' becomes 'class-characters-list.php'
		$format_file = __DIR__ . '/format/class-' . strtolower( str_replace( '_', '-', $format_class_match ) ) . '.php';

		// If the class file does not exist, bail.
		if ( ! file_exists( $format_file ) ) {
			return;
		}

		// Build Params, based on Data type.
		$format_params = array(
			'Characters' => array( $this_year, $count, $build_data ),
			'Dead'       => array( $this_year, $count, $build_data ),
			'Shows'      => array( $this_year, $build_data, $view ),
			'Default'    => array( $this_year, $build_data ),
			'Chart'      => array( $this_year, $custom, $build_data ),
		);

		require_once $format_file;
		$format_class  = 'LWTV_This_Year_' . $format_class_match;
		$format_params = $format_params[ $format_class_match ];

		if ( is_array( $format_params ) ) {
			$format_class_var = new $format_class();
			call_user_func_array( array( $format_class_var, 'generate' ), $format_params );
		} else {
			( new $format_class() )->generate( $format_params );
		}
	}

	/**
	 * Navigation for the year
	 *
	 * @param string  $this_year
	 * @param string  $view
	 *
	 * @return N/A
	 */
	public function navigation( $this_year, $view ) {
		$start_year = FIRST_LWTV_YEAR;
		$baseurl    = '/this-year/';
		$view       = ( 'overview' === $view ) ? '' : $view;
		?>

		<nav aria-label="This Year Navigation" role="navigation" class="yikes-pagination">
			<ul class="pagination justify-content-center">

				<?php
				// If it's not the oldest year there were queers, we can show the first year we have queers.
				if ( $this_year !== $start_year ) {
					?>
					<li class="page-item first me-auto"><a href="<?php echo esc_url( $baseurl . $start_year . '/' . $view ); ?>" class="page-link"><?php echo ( new LWTV_Functions() )->symbolicons( 'caret-left-circle.svg', 'fa-chevron-circle-left' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> First (<?php echo (int) $start_year; ?>)</a></li>
					<li class="page-item"><a href="<?php echo esc_url( $baseurl . ( $this_year - 1 ) . '/' . $view ); ?>" title="previous year" class="page-link"><?php echo ( new LWTV_Functions() )->symbolicons( 'caret-left.svg', 'fa-chevron-left' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> Previous</a></li>
					<li class="page-item"><a href="<?php echo esc_url( $baseurl . ( $this_year - 2 ) . '/' . $view ); ?>" class="page-link"><?php echo (int) ( $this_year - 2 ); ?></a></li>
					<li class="page-item"><a href="<?php echo esc_url( $baseurl . ( $this_year - 1 ) . '/' . $view ); ?>" class="page-link"><?php echo (int) ( $this_year - 1 ); ?></a></li>
					<?php
				}
				?>

				<li class="page-item active"><span class="active page-link"><?php echo (int) $this_year; ?></span></li>

				<?php
				if ( gmdate( 'Y' ) !== $this_year ) {
					?>
					<li class="page-item"><a href="<?php echo esc_url( $baseurl . ( $this_year + 1 ) . '/' . $view ); ?>" class="page-link"><?php echo (int) ( $this_year + 1 ); ?></a></li>
					<li class="page-item"><a href="<?php echo esc_url( $baseurl . ( $this_year + 1 ) . '/' . $view ); ?>" class="page-link" title="next year">Next <?php echo ( new LWTV_Functions() )->symbolicons( 'caret-right.svg', 'fa-chevron-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></li>
					<li class="page-item last ms-auto"><a href="<?php echo esc_url( $baseurl . gmdate( 'Y' ) . '/' . $view ); ?>" class="page-link">Last (<?php echo (int) gmdate( 'Y' ); ?>) <?php echo ( new LWTV_Functions() )->symbolicons( 'caret-right-circle.svg', 'fa-chevron-circle-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></li>
					<?php
				}
				?>
			</ul>
		</nav><!-- .navigation -->
		<?php
	}
}
