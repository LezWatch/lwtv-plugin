<?php

namespace Cmb2Grid;

if ( ! defined( 'CMB2GRID_DIR' ) ) {
	define( 'CMB2GRID_DIR', trailingslashit( dirname( __FILE__ ) ) );
}


if ( ! class_exists( '\Cmb2Grid\Cmb2GridPlugin' ) ) {

	require_once dirname( __FILE__ ) . '/DesignPatterns/Singleton.php';

	class Cmb2GridPlugin extends DesignPatterns\Singleton {

		const VERSION = '1.0';

		protected function __construct() {
			spl_autoload_register( array( $this, 'auto_load' ) );

			add_action( 'admin_head', array( $this, 'wpHead' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		}

		/**
		 * Auto load our class files.
		 *
		 * @param string $class Class name.
		 *
		 * @return void
		 */
		public function auto_load( $class ) {
			static $prefix;
			static $base_dir;
			static $sep;
			static $length;

			if ( ! isset( $prefix, $base_dir, $sep ) ) {
				// Project-specific namespace prefix.
				$prefix = __NAMESPACE__ . '\\';

				// Base directory for the namespace prefix.
				$base_dir = plugin_dir_path( __FILE__ ); // Has trailing slash.

				// Set directory separator.
				$sep = '/';
				if ( defined( 'DIRECTORY_SEPARATOR' ) ) {
					$sep = DIRECTORY_SEPARATOR;
				}
				$length = strlen( $prefix );
			}

			// Does the class use the namespace prefix?
			if ( strncmp( $prefix, $class, $length ) !== 0 ) {
				// No, move to the next registered autoloader.
				return;
			}

			// Get the relative class name.
			$relative_class = substr( $class, $length );

			/*
			 * Add the base directory, replace namespace separators with directory
			 * separators in the relative class name and append with .php.
			 */
			$file = $base_dir . str_replace( '\\', $sep, $relative_class ) . '.php';

			// If the file exists, require it.
			if ( file_exists( $file ) ) {
				require_once $file;
			}
		}

		public function admin_enqueue_scripts() {
			$suffix = ( ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min' );
			wp_enqueue_style( 'cmb2_grid_bootstrap_light', plugins_url( 'assets/css/bootstrap' . $suffix . '.css', __FILE__ ), null, self::VERSION );
		}

		public function wpHead() {
			?>
			<style>
				.cmb2GridRow .cmb-row{border:none !important;padding:0 !important}
				.cmb2GridRow .cmb-th label:after{border:none !important}
				.cmb2GridRow .cmb-th{width:100% !important}
				.cmb2GridRow .cmb-td{width:100% !important}
				.cmb2GridRow input[type="text"], .cmb2GridRow textarea, .cmb2GridRow select{width:100%}

				.cmb2GridRow .cmb-repeat-group-wrap{max-width:100% !important;}
				.cmb2GridRow .cmb-group-title{margin:0 !important;}
				.cmb2GridRow .cmb-repeat-group-wrap .cmb-row .cmbhandle, .cmb2GridRow .postbox-container .cmb-row .cmbhandle{right:0 !important}

                .cmb2GridRow .cmb-type-group .cmb-remove-field-row{padding-bottom: 1.8em !important;padding-top: 1.8em !important;}
                .cmb2GridRow .cmb-td.cmb-nested{padding-left: 15px;padding-right: 15px;}
			</style>
			<?php

		}
	}
}


/* Instantiate the class on plugins_loaded. */
// wp_installing() function was introduced in WP 4.4.
if ( ( function_exists( 'wp_installing' ) && wp_installing() === false ) || ( ! function_exists( 'wp_installing' ) && ( ! defined( 'WP_INSTALLING' ) || WP_INSTALLING === false ) ) ) {
	add_action( 'plugins_loaded', '\\' . __NAMESPACE__ . '\init' );
}

if ( ! function_exists( '\Cmb2Grid\init' ) ) {
	/**
	 * Initialize the class only if CMB2 is detected.
	 *
	 * @return void
	 */
	function init() {
		if ( defined( 'CMB2_LOADED' ) ) {
			if ( ! defined( 'CMB2GRID_DIR' ) ) {
				define( 'CMB2GRID_DIR', trailingslashit( dirname( __FILE__ ) ) );
			}
			Cmb2GridPlugin::getInstance();
		}
	}
}
add_action( 'cmb2_init',  '\Cmb2Grid\init' );

