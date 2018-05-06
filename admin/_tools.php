<?php
/*
 * Tools for LezWatchTV
 *
 * @since 2.4
 */

// if this file is called directly abort
if ( ! defined('WPINC' ) ) {
	die;
}

class LWTV_Tools {

	/*
	 * Construct
	 *
	 * Actions to happen immediately
	 */
	public function __construct() {
		add_action( 'init', array( &$this, 'init' ) );
	}

	/*
	 * Init
	 *
	 * Actions to happen on WP init
	 * - add settings page to tools
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
	}

	/*
	 * Settings
	 *
	 * Create our settings page
	 */
	public function add_settings_page() {
		
		add_menu_page( 'lwtv-plugin', 'LezWatchTV', 'edit_posts', 'lwtv_tools', array( $this, 'settings_page' ), plugins_url( 'assets/images/rainbow.svg', dirname( __FILE__ ) ) , 2 );

		add_submenu_page( 'lwtv_tools', 'Tools', 'Tools', 'edit_posts', 'lwtv_tools', array( $this, 'settings_page' ) );
	}

	/*
	 * Settings Page Content
	 */
	function settings_page() {

		$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'intro';

		?>
		<div class="wrap">
			
			<h1>Tools</h1>

			<h2 class="nav-tab-wrapper">
				<a href="?page=lwtv_tools" class="nav-tab <?php echo $active_tab == 'intro' ? 'nav-tab-active' : ''; ?>">Introduction</a>
				<a  href="?page=lwtv_tools&tab=queer_checker" class="nav-tab <?php echo $active_tab == 'queer_checker' ? 'nav-tab-active' : ''; ?>">Queer Checker</a>
			</h2>

			<?php
			switch( $active_tab ) {
				case 'queer_checker':
					self::tab_queer_checker();
					break;
				default:
					self::tab_introduction();
			}
			?>

		</div>
		<?php
	}

	/**
	 * Static Introduction to what the hell is going on...
	 */
	static function tab_introduction() {
		?>

		<p>Sometimes we need extra tools to do things here...</p>

		<p>In addition to the tools in the tabs, remember we have the following resources:</p>

		<ul>
			<li><a href="https://lezwatch.slack.com">Slack</a></li>
			<li><a href="https://trello.com/b/hpDs7bvy/lezwatchtv">Trello Board</a></li>
			<li><a href="https://github.com/lezWatch/lwtv-underscores/wiki">Theme Wiki</a></li>
		</ul>

		<h4>Queer Checker</h4>
		<p>Make sure all characters who are flagged as Queer IRL have at least one queer actor, and that all actors who are queer have their characters flagged as Queer IRL. <em>WARNING</em>! This page loads slowly. It has a lot of things to check.</p>
		<?php
	}

	/**
	 * Output the results of queer checking...
	 */
	static function tab_queer_checker() {

		$items = LWTV_Debug::find_queerchars();

		if ( empty( $items ) || !is_array( $items ) ) {
			echo '<p><strong>Congratulations!!!</strong> Every character\'s queerness matches their actors!</p>';
		} else {

			echo '<p>' . count( $items ) . ' character(s) need your attention.</p>';
			?>
			<table class="widefat fixed" cellspacing="0">
				<thead><tr>
					<th id="character" class="manage-column column-character" scope="col">Character</th>
					<th id="problem" class="manage-column column-problem" scope="col">Problem</th>
				</tr></thead>

				<tbody>
					<?php
						$number    = 1;
						foreach( $items as $item ) {
							$class = ( $number % 2 == 0 )? '' : 'class="alternate"';
							echo '
							<tr ' . $class . '>
								<td><a href="' . $item['url'] . '" target="_new">' . get_the_title( $item['id'] ) . '</a></td>
								<td>' . $item['problem'] . '</td>
							</tr>
							';
							$number++;
						}
					?>
				</tbody>
			</table>
			<?php
		}

	}

}

new LWTV_Tools();

include_once( 'screeners.php' );