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

require_once 'screeners.php';

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
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	/*
	 * Init
	 *
	 * Actions to happen on WP init
	 * - add settings page to tools
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_post_lwtv_tools_fix_actors', array( $this, 'fix_actors_problems' ) );
	}

	public function admin_enqueue_scripts( $hook ) {
		// Load only on ?page=mypluginname
		if ( 'toplevel_page_lwtv_tools' !== $hook ) {
				return;
		}
		wp_enqueue_style( 'lwtv_tools_admin', plugins_url( 'assets/css/lwtv-tools.css', dirname( __FILE__ ) ), array(), '1.0.0' );
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
		if ( class_exists( 'LWTV_Screeners' ) ) {
			add_submenu_page( 'lwtv_tools', 'Screeners', 'Screeners', 'edit_posts', 'screeners', array( 'LWTV_Screeners', 'settings_page' ) );
		}

		global $submenu;
		$submenu['lwtv_tools'][] = array( 'Documentation', 'edit_posts', esc_url( 'https://docs.lezwatchtv.com/' ) );
		$submenu['lwtv_tools'][] = array( 'Slack', 'edit_posts', esc_url( 'https://lezwatchtv.slack.com/' ) );
		$submenu['lwtv_tools'][] = array( 'Trello', 'edit_posts', esc_url( 'https://trello.com/b/hpDs7bvy/lezwatchtv' ) );

		// Admin notices
		add_action( 'load-$page_id', array( $this, 'admin_notices' ) );
	}

	/*
	 * Admin Notices
	 */
	public function admin_notices() {
		if ( ! isset( $_GET['message'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}

		switch ( $_GET['message'] ) { // phpcs:ignore WordPress.Security.NonceVerification
			case 'success':
				$content = 'Automatic fix complete.';
				break;
			case 'warning':
				$content = 'Automatic fix was unable to complete properly.';
				break;
			case 'error':
				$content = 'Something has gone gay-ly wrong.';
				break;
		}

		if ( isset( $content ) ) {
			$message = '<div class="notice notice-' . esc_attr( $_GET['message'] ) . ' is-dismissable"><p>' . $content . '</p></div>'; // phpcs:ignore WordPress.Security.NonceVerification
			add_action( 'admin_notices', $message );
		}
	}

	/*
	 * Settings Page Content
	 */
	public function settings_page() {

		$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'intro'; // phpcs:ignore WordPress.Security.NonceVerification

		?>
		<div class="wrap">

			<h1>Tools</h1>

			<h2 class="nav-tab-wrapper">
				<a href="?page=lwtv_tools" class="nav-tab <?php echo ( 'intro' === $active_tab ) ? 'nav-tab-active' : ''; ?>">Introduction</a>
				<a  href="?page=lwtv_tools&tab=queer_checker" class="nav-tab <?php echo ( 'queer_checker' === $active_tab ) ? 'nav-tab-active' : ''; ?>">Queer Checker</a>
				<a  href="?page=lwtv_tools&tab=actor_checker" class="nav-tab <?php echo ( 'actor_checker' === $active_tab ) ? 'nav-tab-active' : ''; ?>">Actor Checker</a>
				<a  href="?page=lwtv_tools&tab=character_checker" class="nav-tab <?php echo ( 'character_checker' === $active_tab ) ? 'nav-tab-active' : ''; ?>">Character Checker</a>
				<a  href="?page=lwtv_tools&tab=show_checker" class="nav-tab <?php echo ( 'show_checker' === $active_tab ) ? 'nav-tab-active' : ''; ?>">Show Checker</a>
			</h2>

			<div id="dashboard" class="lwtvtab">
				<?php
				switch ( $active_tab ) {
					case 'queer_checker':
						self::tab_queer_checker();
						break;
					case 'show_checker':
						self::tab_show_checker();
						break;
					case 'actor_checker':
						self::tab_actor_checker();
						break;
					case 'character_checker':
						self::tab_character_checker();
						break;
					case 'build_status':
						self::tab_build_status();
						break;
					default:
						self::tab_introduction();
				}
				?>
			</div>

		</div>
		<?php
	}

	public static function table_content( $items ) {
		$number = 1;
		foreach ( $items as $item ) {
			$class = ( 0 === $number % 2 ) ? '' : 'alternate';
			echo '
			<tr class="' . esc_attr( $class ) . '">
				<td><strong><a href="' . esc_url( get_edit_post_link( (int) $item['id'] ) ) . '" target="_new">' . get_the_title( (int) $item['id'] ) . '</a></strong>

				<div class="row-actions"><span class="edit"><a href="' . esc_url( get_edit_post_link( (int) $item['id'] ) ) . '" aria-label="Edit ' . get_the_title( (int) $item['id'] ) . '">Edit</a>
				| </span><span class="view"><a href="' . esc_url( get_permalink( (int) $item['id'] ) ) . '" rel="bookmark" aria-label="View ' . get_the_title( (int) $item['id'] ) . '">View</a></span></div>
				</td>
				<td>' . wp_kses_post( $item['problem'] ) . '</td>
			</tr>
			';
			$number++;
		}
	}

	/**
	 * Static Introduction to what the hell is going on...
	 */
	public static function tab_introduction() {
		?>

		<div class="tab-block"><div class="lwtv-tools-container">
			<h3>LezWatch.TV Tools</h3>
			<p>Sometimes we need extra tools to do things here. If data gets out of sync or we update things incorrectly, the checkers can help identify those errors before people get snippy.</p>
			<p>Keep in mind, the checkers have to check a lot of data, so they can be slow.</p>
		</div></div>

		<div class="tab-block"><div class="lwtv-tools-container">
			<h3>External Resources</h3>

			<ul>
				<li><a href="https://docs.lezwatchtv.com">Documentation</a></li>
				<li><a href="https://lezwatch.slack.com">Slack</a></li>
				<li><a href="https://trello.com/b/hpDs7bvy/lezwatchtv">Trello Board</a></li>
			</ul>
		</div></div>
		<?php
	}

	/**
	 * Output the results of queer checking...
	 */
	public static function tab_queer_checker() {

		$items = LWTV_Debug::find_queerchars();

		if ( empty( $items ) || ! is_array( $items ) ) {
			?>
			<div class="lwtv-tools-container lwtv-tools-container__alert">
				<h3><span class="dashicons dashicons-yes"></span> Excellent!</h3>
				<div id="lwtv-tools-alerts">
					<p>Every character's queerness matches their actors.</p>
				</div>
			</div>
			<?php
		} else {
			?>
			<div class="lwtv-tools-container lwtv-tools-container__alert">
				<h3><span class="dashicons dashicons-warning"></span> Problems (<?php echo count( $items ); ?>)</h3>
				<div id="lwtv-tools-alerts">
					<p>The following character(s) need your attention. Please edit the actor or character queerness as indicated.</p>
				</div>
			</div>

			<div class="lwtv-tools-table">
				<table class="widefat fixed" cellspacing="0">
					<thead><tr>
						<th id="character" class="manage-column column-character" scope="col">Character</th>
						<th id="problem" class="manage-column column-problem" scope="col">Problem</th>
					</tr></thead>

					<tbody>
						<?php
						self::table_content( $items );
						?>
					</tbody>
				</table>
			</div>
			<?php
		}
	}

	/**
	 * Output the results of actor checking...
	 */
	public static function tab_actor_checker() {

		$redirect = rawurlencode( remove_query_arg( 'msg', $_SERVER['REQUEST_URI'] ) );
		$redirect = rawurlencode( $_SERVER['REQUEST_URI'] );
		$items    = LWTV_Debug::find_actors_problems();
		$json_it  = wp_json_encode( $items );

		if ( empty( $items ) || ! is_array( $items ) ) {
			?>
			<div class="lwtv-tools-container lwtv-tools-container__alert">
				<h3><span class="dashicons dashicons-yes"></span> Excellent!</h3>
				<div id="lwtv-tools-alerts">
					<p>Every actor has at least one character.</p>
				</div>
			</div>
			<?php
		} else {
			?>
			<div class="lwtv-tools-container lwtv-tools-container__alert">
				<h3><span class="dashicons dashicons-warning"></span> Problems (<?php echo count( $items ); ?>)</h3>
				<div id="lwtv-tools-alerts">
					<p>The following actor(s) need your attention. Some can be automatically fixed, other will need to be manually corrected.</p>
				</div>
			</div>

			<div class="lwtv-tools-table">
				<table class="widefat fixed" cellspacing="0">
					<thead><tr>
						<th id="character" class="manage-column column-character" scope="col">Actor</th>
						<th id="problem" class="manage-column column-problem" scope="col">Problem</th>
					</tr></thead>

					<tbody>
						<?php
						self::table_content( $items );
						?>
					</tbody>
				</table>

				<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="POST">
					<input type="hidden" name="action" value="lwtv_tools_fix_actors">
					<input type="hidden" name="broken_actors" value='<?php echo $json_it; ?>'>
					<?php wp_nonce_field( 'lwtv_tools_fix_actors', 'lwtv_tools_fix_actors_nonce', false ); ?>
					<input type="hidden" name="_wp_http_referer" value="<?php echo $redirect; ?>">
					<?php submit_button( 'Fix Actors' ); ?>
				</form>
			</div>
			<?php
		}
	}

	/*
	 * Attempt to fix the actors...
	 */
	public function fix_actors_problems() {

		if ( ! wp_verify_nonce( $_POST['lwtv_tools_fix_actors_nonce'], 'lwtv_tools_fix_actors' ) ) {
			die( 'Invalid nonce.' );
		}

		if ( ! isset( $_POST['_wp_http_referer'] ) ) {
			die( 'Missing target.' );
		}

		$broken_actors = json_decode( $_POST['broken_actors'], true );

		if ( ! is_array( $broken_actors ) ) {
			$broken_actors = LWTV_Debug::find_actors_problems();
		}

		$items = LWTV_Debug::fix_actors_problems( $broken_actors );

		if ( ! isset( $items ) || 0 === $items || is_null( $items ) ) {
			$message = 'warning';
		} elseif ( is_numeric( $items ) ) {
			$message = 'success';
		} else {
			$message = 'error';
		}

		$url = add_query_arg( 'message', $message, urldecode( $_POST['_wp_http_referer'] ) );

		wp_safe_redirect( $url );
		exit;
	}

	/**
	 * Output the results of Show checking...
	 */
	public static function tab_show_checker() {

		$redirect = rawurlencode( remove_query_arg( 'msg', $_SERVER['REQUEST_URI'] ) );
		$redirect = rawurlencode( $_SERVER['REQUEST_URI'] );
		$items    = LWTV_Debug::find_shows_problems();

		if ( empty( $items ) || ! is_array( $items ) ) {
			?>
			<div class="lwtv-tools-container lwtv-tools-container__alert">
				<h3><span class="dashicons dashicons-yes"></span> Excellent!</h3>
				<div id="lwtv-tools-alerts">
					<p>All shows look good.</p>
				</div>
			</div>
			<?php
		} else {
			?>
			<div class="lwtv-tools-container lwtv-tools-container__alert">
				<h3><span class="dashicons dashicons-warning"></span> Problems (<?php echo count( $items ); ?>)</h3>
				<div id="lwtv-tools-alerts">
					<p>The following show(s) need your attention. Keep in mind, some shows are listed as not having characters on purpose.</p>
				</div>
			</div>

			<div class="lwtv-tools-table">
				<table class="widefat fixed" cellspacing="0">
					<thead><tr>
						<th id="character" class="manage-column column-character" scope="col">Show</th>
						<th id="problem" class="manage-column column-problem" scope="col">Problem</th>
					</tr></thead>

					<tbody>
						<?php
						self::table_content( $items );
						?>
					</tbody>
				</table>
			</div>
			<?php
		}
	}

	/**
	 * Output the results of character checking...
	 */
	public static function tab_character_checker() {

		$redirect = rawurlencode( remove_query_arg( 'msg', $_SERVER['REQUEST_URI'] ) );
		$redirect = rawurlencode( $_SERVER['REQUEST_URI'] );
		$items    = LWTV_Debug::find_characters_problems();

		if ( empty( $items ) || ! is_array( $items ) ) {
			?>
			<div class="lwtv-tools-container lwtv-tools-container__alert">
				<h3><span class="dashicons dashicons-yes"></span> Excellent!</h3>
				<div id="lwtv-tools-alerts">
					<p>All characters look good.</p>
				</div>
			</div>
			<?php
		} else {
			?>
			<div class="lwtv-tools-container lwtv-tools-container__alert">
				<h3><span class="dashicons dashicons-warning"></span> Problems (<?php echo count( $items ); ?>)</h3>
				<div id="lwtv-tools-alerts">
					<p>The following character(s) need your attention.</p>
				</div>
			</div>

			<div class="lwtv-tools-table">
				<table class="widefat fixed" cellspacing="0">
					<thead><tr>
						<th id="character" class="manage-column column-character" scope="col">Character</th>
						<th id="problem" class="manage-column column-problem" scope="col">Problem</th>
					</tr></thead>

					<tbody>
						<?php
						self::table_content( $items );
						?>
					</tbody>
				</table>
			</div>
			<?php
		}
	}
}

new LWTV_Tools();
