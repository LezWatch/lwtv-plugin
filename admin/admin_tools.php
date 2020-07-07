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

class LWTV_Admin_Tools {

	/**
	 * Local Variables
	 */
	protected $page_id = null;        // page ID
	protected static $tool_tabs;       // all tabs

	/*
	 * Construct
	 *
	 * Actions to happen immediately
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_notices' ) );
		add_action( 'admin_post_lwtv_tools_fix_actors', array( $this, 'fix_actors_problems' ) );
		add_action( 'admin_post_lwtv_tools_wikidata_actors', array( $this, 'check_actors_wikidata' ) );

		self::$tool_tabs = array(
			'queer_checker' => array(
				'name' => 'Queer Checker',
				'desc' => 'Checks that all characters with queer actors have the queer cliché, and all actors with queer characters are, in fact, queer.',
			),
			'actor_checker' => array(
				'name' => 'Actor Checker',
				'desc' => 'Checks that all information for actors appears correct. This includes social media and links.',
			),
			'actor_empty' => array(
				'name' => 'Incomplete Actors',
				'desc' => 'Actors that have not yet been updated since the Great Migration.',
			),
			'character_checker' => array(
				'name' => 'Character Checker',
				'desc' => 'Checks that all information for characters appears correct, like if they have a show and years-on-air added.',
			),
			'show_checker' => array(
				'name' => 'Show Checker',
				'desc' => 'Checks that all information for shows appears correct. Like do they have characters and ratings etc.',
			),
		);

	}

	/*
	 * Settings
	 *
	 * Create our settings page
	 */
	public function add_admin_notices() {
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
			$message = '<div class="notice notice-' . esc_attr( $_GET['message'] ) . ' is-dismissable"><p>' . esc_html( $content ) . '</p></div>';
			add_action( 'admin_notices', $message );
		}
	}

	/*
	 * Settings Page Content
	 */
	public static function settings_page() {
		// Get the active tab for later
		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'intro';

		?>
		<div class="wrap">

			<h1>Tools</h1>

			<h2 class="nav-tab-wrapper">
				<a href="?page=lwtv_tools" class="nav-tab <?php echo ( 'intro' === $active_tab ) ? 'nav-tab-active' : ''; ?>">Introduction</a>
				<?php
				foreach ( self::$tool_tabs as $tab => $value ) {
					$active = ( $tab === $active_tab ) ? 'nav-tab-active' : '';
					echo '<a href="?page=lwtv_tools&tab=' . esc_attr( $tab ) . '" class="nav-tab ' . esc_attr( $active ) . '">' . esc_html( $value['name'] ) . '</a>';
				}
				?>
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
					case 'actor_wiki':
						self::tab_actor_wiki();
						break;
					case 'actor_empty':
						self::tab_actor_empty();
						break;
					case 'character_checker':
						self::tab_character_checker();
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
				<td><strong><a href="' . esc_url( get_edit_post_link( (int) $item['id'] ) ) . '" target="_new">' . wp_kses_post( get_the_title( (int) $item['id'] ) ) . '</a></strong>

				<div class="row-actions"><span class="edit"><a href="' . esc_url( get_edit_post_link( (int) $item['id'] ) ) . '" aria-label="Edit ' . wp_kses_post( get_the_title( (int) $item['id'] ) ) . '">Edit</a>
				| </span><span class="view"><a href="' . esc_url( get_permalink( (int) $item['id'] ) ) . '" rel="bookmark" aria-label="View ' . wp_kses_post( get_the_title( (int) $item['id'] ) ) . '">View</a></span></div>
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

		$options   = get_option( 'lwtv_debugger_status' );
		$timestamp = $options['timestamp'];
		unset( $options['timestamp'] );

		?>

		<div class="tab-block"><div class="lwtv-tools-container">
			<h3>LezWatch.TV Tools</h3>
			<p>Sometimes we need extra tools to do things here. If data gets out of sync or we update things incorrectly, the checkers can help identify those errors before people notice.</p>
			<p>Keep in mind, the checkers have to check a lot of data, so they can be slow.</p>

			<p>The tools were last run on <strong><?php echo esc_html( get_date_from_gmt( gmdate( 'Y-m-d H:i:s', $timestamp ), 'F j, Y H:i:s' ) ); ?></strong>.</p>

			<ul>
				<?php
				$output = '';
				foreach ( $options as $an_option ) {
					if ( $an_option['count'] > 0 ) {
						$output .= '<li>&bull; ' . $an_option['name'] . ' - ' . $an_option['count'] . '</li>';
					}
				}

				if ( ! empty( $output ) ) {
					$output = '<p><strong>Current Status</strong></p>' . $output;
				}

				echo wp_kses_post( $output );

				?>
			</ul>

			<hr>

			<ul>
				<?php
				foreach ( self::$tool_tabs as $tab => $value ) {
					echo '<li>&bull; <a href="?page=lwtv_tools&tab=' . esc_attr( $tab ) . '">' . esc_html( $value['name'] ) . '</a> - ' . esc_html( $value['desc'] ) . '</li>';
				}
				?>
			</ul>

		</div></div>

		<?php
	}

	/**
	 * Output the results of queer checking...
	 */
	public static function tab_queer_checker() {

		$items = ( new LWTV_Debug() )->find_queerchars();

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
		$items    = ( new LWTV_Debug() )->find_actors_problems();
		//$json_it  = wp_json_encode( $items );

		if ( empty( $items ) || ! is_array( $items ) ) {
			?>
			<div class="lwtv-tools-container lwtv-tools-container__alert">
				<h3><span class="dashicons dashicons-yes"></span> Excellent!</h3>
				<div id="lwtv-tools-alerts">
					<p>Every actor has at least one character and their data looks sane.</p>
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
					<input type="hidden" name="broken_actors" value='<?php echo wp_json_encode( $items ); ?>'>
					<?php wp_nonce_field( 'lwtv_tools_fix_actors', 'lwtv_tools_fix_actors_nonce', false ); ?>
					<input type="hidden" name="_wp_http_referer" value="<?php esc_url_raw( $redirect ); ?>">
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
			$broken_actors = ( new LWTV_Debug() )->find_actors_problems();
		}

		$items = ( new LWTV_Debug() )->fix_actors_problems( $broken_actors );

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
	 * Do some juggling to see how actors compare to Wikidata
	 * @var [type]
	 */
	public static function tab_actor_wiki() {
		$redirect = rawurlencode( remove_query_arg( 'msg', $_SERVER['REQUEST_URI'] ) );
		$redirect = rawurlencode( $_SERVER['REQUEST_URI'] );

		$items    = ( new LWTV_Debug() )->list_actors_wikidata();

		/*
			Instead of looping through all, let's do something else.

			1. Select actor from drop down
			2. Check THAT actor.
			3. Output results

			$json_it = will be the actor ID

		 */

		?>
		<div class="lwtv-tools-container">

			<p>Pick an actor you want to check:</p>

			<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="POST">
				<input type="hidden" name="action" value="lwtv_tools_wikidata_actors">
				<select id="actor_id" name="actor">
					<?php
					foreach ( $items as $id => $name ) {
						echo '<option value="' . esc_attr( $id ) . '">' . esc_html( $name ) . '</option>';
					}
					?>
				</select>
				<?php wp_nonce_field( 'lwtv_tools_wikidata_actors', 'lwtv_tools_wikidata_actors_nonce', false ); ?>
				<input type="hidden" name="_wp_http_referer" value="<?php echo esc_url_raw( $redirect ); ?>">
				<?php submit_button( 'Check' ); ?>
			</form>

			<!-- Form needs to output HERE -->
		</div>

		<?php

	}

	/**
	 * Output the results of actors without data ...
	 */
	public static function tab_actor_empty() {

		$redirect = rawurlencode( remove_query_arg( 'msg', $_SERVER['REQUEST_URI'] ) );
		$redirect = rawurlencode( $_SERVER['REQUEST_URI'] );
		$items    = ( new LWTV_Debug() )->find_actors_empty();
		$json_it  = wp_json_encode( $items );

		if ( empty( $items ) || ! is_array( $items ) ) {
			?>
			<div class="lwtv-tools-container lwtv-tools-container__alert">
				<h3><span class="dashicons dashicons-yes"></span> Excellent!</h3>
				<div id="lwtv-tools-alerts">
					<p>All actors have at least some information.</p>
				</div>
			</div>
			<?php
		} else {
			?>
			<div class="lwtv-tools-container lwtv-tools-container__alert">
				<h3><span class="dashicons dashicons-warning"></span> Problems (<?php echo count( $items ); ?>)</h3>
				<div id="lwtv-tools-alerts">
					<p>The following actor(s) have not have their data entered yet. Please review and update them.</p>
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
			</div>
			<?php
		}
	}

	/**
	 * Output the results of Show checking...
	 */
	public static function tab_show_checker() {

		$redirect = rawurlencode( remove_query_arg( 'msg', $_SERVER['REQUEST_URI'] ) );
		$redirect = rawurlencode( $_SERVER['REQUEST_URI'] );
		$items    = ( new LWTV_Debug() )->find_shows_problems();

		if ( empty( $items ) || ! is_array( $items ) ) {
			?>
			<div class="lwtv-tools-container lwtv-tools-container__alert">
				<h3><span class="dashicons dashicons-yes"></span> Excellent!</h3>
				<div id="lwtv-tools-alerts">
					<p>All shows look good and the data looks sane.</p>
				</div>
			</div>
			<?php
		} else {
			?>
			<div class="lwtv-tools-container lwtv-tools-container__alert">
				<h3><span class="dashicons dashicons-warning"></span> Problems (<?php echo count( $items ); ?>)</h3>
				<div id="lwtv-tools-alerts">
					<p>The following show(s) need your attention.</p>
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
		$items    = ( new LWTV_Debug() )->find_characters_problems();

		if ( empty( $items ) || ! is_array( $items ) ) {
			?>
			<div class="lwtv-tools-container lwtv-tools-container__alert">
				<h3><span class="dashicons dashicons-yes"></span> Excellent!</h3>
				<div id="lwtv-tools-alerts">
					<p>All characters look good and their data looks sane. Even Sara Lance.</p>
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

new LWTV_Admin_Tools();
