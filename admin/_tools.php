<?php
/*
 * Tools for LezWatch.TV
 *
 * @since 2.4
 */

// if this file is called directly abort
if ( ! defined( 'WPINC' ) ) {
	die;
}

class LWTV_Tools {

	/**
	 * Page ID
	 */
	protected $page_id = null;

	/*
	 * Construct
	 *
	 * Actions to happen immediately
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	/*
	 * Init
	 *
	 * Actions to happen on WP init
	 * - add settings page to tools
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_post_lwtv_tools_fix_actors', array( $this, 'fix_actors_no_chars' ) );
	}

	/*
	 * Settings
	 *
	 * Create our settings page
	 */
	public function add_settings_page() {

		// Add Tools pages
		add_menu_page( 'lwtv-plugin', 'LezWatch.TV', 'edit_posts', 'lwtv_tools', array( $this, 'settings_page' ), LWTV_Functions::get_icon_svg(), 2 );
		add_submenu_page( 'lwtv_tools', 'Tools', 'Tools', 'edit_posts', 'lwtv_tools', array( $this, 'settings_page' ) );

		// Admin notices
		add_action( 'load-$page_id', array( $this, 'admin_notices' ) );
	}

	/*
	 * Admin Notices
	 */
	public function admin_notices() {
		if ( ! isset( $_GET['message'] ) ) { // WPCS: CSRF ok.
			return;
		}

		switch ( $_GET['message'] ) { // WPCS: CSRF ok.
			case 'success':
				$content = 'Automatic fix complete.';
				break;
			case 'warning':
				$content = 'Automatic fix was unable to complete properly.';
				break;
			case 'error':
				$content = 'Something has gone gaily wrong.';
				break;
		}

		if ( isset( $content ) ) {
			$message = '<div class="notice notice-' . esc_attr( $_GET['message'] ) . ' is-dismissable"><p>' . $content . '</p></div>'; // WPCS: CSRF ok.
			add_action( 'admin_notices', $message );
		}
	}

	/*
	 * Settings Page Content
	 */
	public function settings_page() {

		$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'intro'; // WPCS: CSRF ok.

		?>
		<div class="wrap">

			<h1>Tools</h1>

			<h2 class="nav-tab-wrapper">
				<a href="?page=lwtv_tools" class="nav-tab <?php echo ( 'intro' === $active_tab ) ? 'nav-tab-active' : ''; ?>">Introduction</a>
				<a  href="?page=lwtv_tools&tab=queer_checker" class="nav-tab <?php echo ( 'queer_checker' === $active_tab ) ? 'nav-tab-active' : ''; ?>">Queer Checker</a>
				<a  href="?page=lwtv_tools&tab=actor_checker" class="nav-tab <?php ( 'actor_checker' === $active_tab ) ? 'nav-tab-active' : ''; ?>">Actor Checker</a>
			</h2>

			<?php
			switch ( $active_tab ) {
				case 'queer_checker':
					self::tab_queer_checker();
					break;
				case 'actor_checker':
					self::tab_actor_checker();
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
	public static function tab_introduction() {
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

		<h4>Actor Checker</h4>
		<p>Make sure all actors have characters listed. It happens sometimes when things get out of sync.</p>
		<?php
	}

	/**
	 * Output the results of queer checking...
	 */
	public static function tab_queer_checker() {

		$items = LWTV_Debug::find_queerchars();

		if ( empty( $items ) || ! is_array( $items ) ) {
			echo '<p><strong>Congratulations!!!</strong> Every character\'s queerness matches their actors!</p>';
		} else {

			echo '<p>' . count( $items ) . ' character(s) need your attention. Please edit the actor or character as indicated.</p>';
			?>
			<table class="widefat fixed" cellspacing="0">
				<thead><tr>
					<th id="character" class="manage-column column-character" scope="col">Character</th>
					<th id="problem" class="manage-column column-problem" scope="col">Problem</th>
				</tr></thead>

				<tbody>
					<?php
					$number = 1;
					foreach ( $items as $item ) {
						$class = ( 0 === $number % 2 ) ? '' : 'class="alternate"';
						echo '
						<tr ' . esc_attr( $class ) . '>
							<td><a href="' . esc_url( $item['url'] ) . '" target="_new">' . get_the_title( (int) $item['id'] ) . '</a></td>
							<td>' . esc_html( $item['problem'] ) . '</td>
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

	/**
	 * Output the results of queer checking...
	 */
	public static function tab_actor_checker() {

		$redirect = rawurlencode( remove_query_arg( 'msg', $_SERVER['REQUEST_URI'] ) );
		$redirect = rawurlencode( $_SERVER['REQUEST_URI'] );
		$items    = LWTV_Debug::find_actors_no_chars();

		if ( empty( $items ) || ! is_array( $items ) ) {
			echo '<p><strong>Congratulations!!!</strong> Every actor has at least one character listed!</p>';
		} else {

			echo '<p>' . count( $items ) . ' actor(s) need your attention. You can just visit the actor and save the post to fix this in most cases.</p>';
			// @codingStandardsIgnoreStart
			?>
			<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="POST">
				<input type="hidden" name="action" value="lwtv_tools_fix_actors">
				<?php wp_nonce_field( 'lwtv_tools_fix_actors', 'lwtv_tools_fix_actors_nonce', false ); ?>
				<input type="hidden" name="_wp_http_referer" value="<?php echo $redirect; ?>">
				<?php submit_button( 'Fix Actors' ); ?>
			</form>

			<table class="widefat fixed" cellspacing="0">
				<thead><tr>
					<th id="character" class="manage-column column-character" scope="col">Actor</th>
					<th id="problem" class="manage-column column-problem" scope="col">Problem</th>
				</tr></thead>

				<tbody>
					<?php
					$number = 1;
					foreach ( $items as $item ) {
						$class = ( 0 === $number % 2 ) ? '' : 'class="alternate"';
						echo '
						<tr ' . esc_attr( $class ) . '>
							<td><a href="' . esc_url( $item['url'] ) . '" target="_new">' . get_the_title( (int) $item['id'] ) . '</a></td>
							<td>' . wp_kses_post( $item['problem'] ) . '</td>
						</tr>
						';
						$number++;
					}
					?>
				</tbody>
			</table>
			<?php
			// @codingStandardsIgnoreEnd
		}
	}

	/*
	 * Attempt to fix the actors...
	 */
	public function fix_actors_no_chars() {

		if ( ! wp_verify_nonce( $_POST['lwtv_tools_fix_actors_nonce'], 'lwtv_tools_fix_actors' ) ) {
			die( 'Invalid nonce.' );
		}

		$items = LWTV_Debug::fix_actors_no_chars();

		if ( ! isset( $items ) || 0 === $items || is_null( $items ) ) {
			$message = 'warning';
		} elseif ( is_numeric( $items ) ) {
			$message = 'success';
		} else {
			$message = 'error';
		}

		if ( ! isset( $_POST['_wp_http_referer'] ) ) {
			die( 'Missing target.' );
		}

		$url = add_query_arg( 'message', $message, urldecode( $_POST['_wp_http_referer'] ) );

		wp_safe_redirect( $url );
		exit;
	}

}

new LWTV_Tools();

require_once 'screeners.php';
