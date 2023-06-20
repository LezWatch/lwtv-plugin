<?php
/*
 * LezWatch.TV Admin Menu
 *
 */

// if this file is called directly abort
if ( ! defined( 'WPINC' ) ) {
	die;
}

class LWTV_Admin_Menu {

	/**
	 * Local Variables
	 */
	protected $page_id = null;        // page ID

	/*
	 * Construct
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	/*
	 * Settings
	 *
	 * Create our settings page
	 */
	public function add_settings_page() {
		global $submenu;

		// Add main menu
		add_menu_page( 'lwtv-plugin', 'LezWatch.TV', 'read', 'lwtv', array( $this, 'settings_page' ), ( new LWTV_Functions() )->get_icon_svg(), 2 );

		add_submenu_page( 'lwtv', 'Welcome', 'Welcome', 'read', 'lwtv', array( $this, 'settings_page' ) );

		if ( class_exists( 'LWTV_Data_Validation_Checks' ) ) {
			add_submenu_page( 'lwtv', 'Data Checker', 'Data Checker', 'upload_files', 'lwtv_data_check', 'LWTV_Data_Validation_Checks::settings_page' );
		}

		if ( class_exists( 'LWTV_Monitor_Checks' ) ) {
			add_submenu_page( 'lwtv', 'Monitors', 'Monitors', 'upload_files', 'lwtv_monitor_check', 'LWTV_Monitor_Checks::settings_page' );
		}

		// Only admins can access this part:
		if ( class_exists( 'LWTV_Exclusion_Checks' ) && current_user_can( 'activate_plugins' ) ) {
			add_submenu_page( 'lwtv', 'Exclusion Checker', 'Exclusion Checker', 'activate_plugins', 'lwtv_exclusion_check', 'LWTV_Exclusion_Checks::settings_page' );
		}

		//phpcs:ignore WordPress.WP.GlobalVariablesOverride
		$submenu['lwtv'][] = array( 'Documentation', 'read', esc_url( 'https://docs.lezwatchtv.com/' ) );
		//phpcs:ignore WordPress.WP.GlobalVariablesOverride
		$submenu['lwtv'][] = array( 'Slack', 'read', esc_url( 'https://lezwatchtv.slack.com/' ) );
	}

	/*
	 * Settings Page Content
	 */
	public function settings_page() {
		// Get the active tab for later
		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'intro';

		?>
		<div class="wrap">

			<h1>Welcome to Editing LezWatch.TV</h1>

			<div class="lwtv-tools-container">

				<h3>Welcome!</h3>

				<p>If you're reading this page, it's because you're here to help make the LWTV world a little better. We love you for it.</p>

				<p>There are some links to the side for handy-dandy tools that may help you along your way to better understanding everything that goes in to making LezWatch.TV so shucky-darn awesome.</p>

				<p>As always, if you have any questions, give us a shout in the <code>#editors</code> channel in Slack! Remember, there are no bad questions, just bad documentation.</p>
			</div>
		</div>
		<?php
	}

	public function admin_enqueue_scripts( $hook ) {
		// Load only on ?page=mypluginname
		$my_hooks = array( 'toplevel_page_lwtv', 'lezwatch-tv_page_lwtv_data_check', 'lezwatch-tv_page_lwtv_monitor_check' );
		if ( in_array( $hook, $my_hooks, true ) ) {
				wp_enqueue_style( 'lwtv_data_check_admin', plugins_url( 'assets/css/lwtv-tools.css', __DIR__ ), array(), '1.0.0' );
		}
	}
}

new LWTV_Admin_Menu();

require_once 'admin_tools.php';
require_once 'dashboard.php';
require_once 'exclusions.php';
require_once 'monitors.php';
