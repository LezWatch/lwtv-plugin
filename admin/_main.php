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

		if ( class_exists( 'LWTV_Tools' ) ) {
			add_submenu_page( 'lwtv', 'Tools', 'Tools', 'upload_files', 'lwtv_tools', '( new LWTV_Tools() )->settings_page' );
		}

		// Builds page would show the last few builds from Github with data from Codeship.
		//add_submenu_page( 'lwtv', 'Builds', 'Builds', 'manage_options', 'lwtv_builds', array( $this, 'builds_page' ) );

		$submenu['lwtv'][] = array( 'Documentation', 'read', esc_url( 'https://docs.lezwatchtv.com/' ) );
		$submenu['lwtv'][] = array( 'Slack', 'read', esc_url( 'https://lezwatchtv.slack.com/' ) );
		$submenu['lwtv'][] = array( 'Trello', 'manage_options', esc_url( 'https://trello.com/b/hpDs7bvy/lezwatchtv' ) );
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
		$my_hooks = array( 'toplevel_page_lwtv', 'lezwatch-tv_page_lwtv_tools' );
		if ( in_array( $hook, $my_hooks ) ) {
				wp_enqueue_style( 'lwtv_tools_admin', plugins_url( 'assets/css/lwtv-tools.css', dirname( __FILE__ ) ), array(), '1.0.0' );
		}
	}
}

new LWTV_Admin_Menu();

require_once 'admin_tools.php';
require_once 'dashboard.php';
