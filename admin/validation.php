<?php
/*
 * Data Sync Checks For LezWatch.TV
 *
 * @since 2.4
 */

// if this file is called directly abort
if ( ! defined( 'WPINC' ) ) {
	die;
}

class LWTV_Data_Validation_Checks {

	/**
	 * Local Variables
	 */
	protected $page_id = null;        // page ID
	protected static $tool_tabs;      // all tabs

	/*
	 * Construct
	 *
	 * Actions to happen immediately
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_notices' ) );
		add_action( 'admin_post_lwtv_data_check_wikidata_actors', array( $this, 'check_actors_wikidata' ) );

		self::$tool_tabs = array(
			'queer_checker'     => array(
				'name' => 'Queer Checker',
				'desc' => 'Checks that all characters with queer actors have the queer cliché, and all actors with queer characters are, in fact, queer.',
			),
			'actor_checker'     => array(
				'name' => 'Actor Checker',
				'desc' => 'Checks that all information for actors appears correct. This includes social media and links.',
			),
			'actor_empty'       => array(
				'name' => 'Incomplete Actors',
				'desc' => 'Actors that have not yet been updated since the Great Migration.',
			),
			'character_checker' => array(
				'name' => 'Character Checker',
				'desc' => 'Checks that all information for characters appears correct, like if they have a show and years-on-air added.',
			),
			'show_checker'      => array(
				'name' => 'Show Checker',
				'desc' => 'Checks that all information for shows appears correct. Like do they have characters and ratings etc, does intersectionality seem to match.',
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

	public static function last_run( $tool ) {
		$options = get_option( 'lwtv_debugger_status' );

		// Get the timestamp from the individual last run OR the global.
		if ( 'intro' !== $tool && isset( $options[ $tool ]['last'] ) ) {
			$timestamp = $options[ $tool ]['last'];
		} else {
			$timestamp = $options['timestamp'];
		}

		$last_run = '<strong>' . get_date_from_gmt( gmdate( 'Y-m-d H:i:s', $timestamp ), 'F j, Y H:i:s' ) . '</strong> (' . human_time_diff( $timestamp ) . ' ago).';

		return $last_run;
	}

	/*
	 * Admin Notices
	 */
	public function admin_notices() {
		if ( ! isset( $_GET['message'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}

		$notice_value = sanitize_text_field( $_GET['message'] ); // phpcs:ignore WordPress.Security.NonceVerification

		switch ( $notice_value ) {
			case 'success':
				$content = 'Automatic fix complete.';
				break;
			case 'warning':
				$content = 'Automatic fix was unable to complete properly.';
				break;
			case 'error':
				$content = 'Something has gone gay-ly wrong.';
				break;
			case 'rerun':
				$content      = 'Check has been re-run.';
				$notice_value = 'success';
				break;
		}

		if ( isset( $content ) ) {
			$message = '<div class="notice notice-' . esc_attr( $notice_value ) . ' is-dismissable"><p>' . esc_html( $content ) . '</p></div>';
			add_action( 'admin_notices', $message );
		}
	}

	/*
	 * Settings Page Content
	 */
	public static function settings_page() {
		// Get the active tab for later
		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'intro'; // phpcs:ignore WordPress.Security.NonceVerification
		?>
		<div class="wrap">

			<h1>Validation Checks</h1>

			<h2 class="nav-tab-wrapper">
				<a href="?page=lwtv_data_check" class="nav-tab <?php echo ( 'intro' === $active_tab ) ? 'nav-tab-active' : ''; ?>">Introduction</a>
				<?php
				foreach ( self::$tool_tabs as $tab => $value ) {
					$active = ( $tab === $active_tab ) ? 'nav-tab-active' : '';
					echo '<a href="?page=lwtv_data_check&tab=' . esc_attr( $tab ) . '" class="nav-tab ' . esc_attr( $active ) . '">' . esc_html( $value['name'] ) . '</a>';
				}
				?>
			</h2>

			<div id="dashboard" class="lwtvtab">
				<?php
				switch ( $active_tab ) {
					case 'queer_checker':
						self::tab_queer_checker();
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
					case 'show_checker':
						self::tab_show_checker();
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
			$class     = ( 0 === $number % 2 ) ? '' : 'alternate';
			$timestamp = get_post_timestamp( (int) $item['id'], 'modified' );
			// phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
			$time_diff = human_time_diff( $timestamp, current_time( 'timestamp' ) );
			if ( ! $timestamp ) {
				$time = 'Unknown';
			} else {
				$time = date_i18n( get_option( 'date_format' ), $timestamp );
			}

			echo '
			<tr class="' . esc_attr( $class ) . '">
				<td><strong><a href="' . esc_url( get_edit_post_link( (int) $item['id'] ) ) . '" target="_new">' . wp_kses_post( get_the_title( (int) $item['id'] ) ) . '</a></strong>

				<div class="row-actions"><span class="edit"><a href="' . esc_url( get_edit_post_link( (int) $item['id'] ) ) . '" aria-label="Edit ' . wp_kses_post( get_the_title( (int) $item['id'] ) ) . '">Edit</a>
				| </span><span class="view"><a href="' . esc_url( get_permalink( (int) $item['id'] ) ) . '" rel="bookmark" aria-label="View ' . wp_kses_post( get_the_title( (int) $item['id'] ) ) . '">View</a></span></div>
				</td>
				<td>' . wp_kses_post( $item['problem'] ) . '</td>
				<td>' . esc_html( $time ) . '<br/>(' . esc_html( $time_diff ) . ' ago)</td>
			</tr>
			';
			$number++;
		}
	}

	/**
	 * Static Introduction to what the hell is going on...
	 */
	public static function tab_introduction() {

		// Get the options
		$options = get_option( 'lwtv_debugger_status' );

		// Get the last run.
		$last_run = self::last_run( 'intro' );
		?>

		<div class="tab-block"><div class="lwtv-tools-container">
			<h3>LezWatch.TV Data Validation Checks</h3>
			<p>If data gets out of sync or we update things incorrectly, these checkers can help identify those errors before people notice. They run on an automated cycle, each check once a week, to try and catch things early.</p>

			<p>When visiting the individual checker, it will show you the status of the last run. To re-run the tool, press the 'Check Again' button at the bottom of the page.</p>

			<p>The checks were last run on <?php echo wp_kses_post( $last_run ); ?></p>

			<ul>
				<?php
				$output = '';
				foreach ( $options as $an_option ) {
					$number = ( isset( $an_option['count'] ) ) ? $an_option['count'] : 0;
					if ( (int) $number > 0 ) {
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
					echo '<li>&bull; <a href="?page=lwtv_data_check&tab=' . esc_attr( $tab ) . '">' . esc_html( $value['name'] ) . '</a> - ' . esc_html( $value['desc'] ) . '</li>';
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

		$items = LWTV_Transients::get_transient( 'lwtv_debug_queercheck' );

		// Check whether the button has been pressed AND also check the nonce
		if ( ( isset( $_POST['rerun'] ) && check_admin_referer( 'run_queer_checker_clicked' ) ) || false === $items ) {
			$items = ( new LWTV_Debug() )->find_queerchars();
		}

		// Get the last run time.
		$last_run_time = self::last_run( 'queercheck' );
		$last_run_echo = '<p>The queer checker was last run on ' . $last_run_time . '</p>';

		if ( empty( $items ) || ! is_array( $items ) ) {
			?>
			<div class="lwtv-tools-container lwtv-tools-container__alert">
				<h3><span class="dashicons dashicons-yes"></span> Excellent!</h3>
				<div id="lwtv-tools-alerts">
					<p>Every character's queerness matches their actors.</p>
					<?php echo wp_kses_post( $last_run_echo ); ?>
				</div>
			</div>
			<?php
		} else {
			?>
			<div class="lwtv-tools-container lwtv-tools-container__alert">
				<h3><span class="dashicons dashicons-warning"></span> Problems (<?php echo count( $items ); ?>)</h3>
				<div id="lwtv-tools-alerts">
					<p>The following character(s) need your attention. Please edit the actor or character queerness as indicated.</p>
					<?php echo wp_kses_post( $last_run_echo ); ?>
				</div>
			</div>

			<div class="lwtv-tools-table">
				<table class="widefat fixed" cellspacing="0">
					<thead><tr>
						<th id="character" class="manage-column column-character" scope="col">Character</th>
						<th id="problem" class="manage-column column-problem" scope="col">Problem</th>
						<th id="date" class="manage-column column-date" scope="col">Last Updated</th>
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

		?>
		<form action="admin.php?page=lwtv_data_check&tab=queer_checker" method="post">
			<?php wp_nonce_field( 'run_queer_checker_clicked' ); ?>
			<input type="hidden" value="true" name="rerun" />
			<?php submit_button( 'Check Again' ); ?>
		</form>
		<?php
	}

	/**
	 * Output the results of actor checking...
	 */
	public static function tab_actor_checker() {

		$items = LWTV_Transients::get_transient( 'lwtv_debug_actor_problems' );

		// Check whether the button has been pressed AND also check the nonce
		if ( ( isset( $_POST['rerun'] ) && check_admin_referer( 'run_actor_checker_clicked' ) ) || false === $items ) {
			$items = ( new LWTV_Debug() )->find_actors_problems();
		}

		// Get the last run time.
		$last_run_time = self::last_run( 'actor_problems' );
		$last_run_echo = '<p>The actor checker was last run on ' . $last_run_time . '</p>';

		if ( empty( $items ) || ! is_array( $items ) ) {
			?>
			<div class="lwtv-tools-container lwtv-tools-container__alert">
				<h3><span class="dashicons dashicons-yes"></span> Excellent!</h3>
				<div id="lwtv-tools-alerts">
					<p>Every actor has at least one character and their data looks sane.</p>
					<?php echo wp_kses_post( $last_run_echo ); ?>
				</div>
			</div>
			<?php
		} else {
			?>
			<div class="lwtv-tools-container lwtv-tools-container__alert">
				<h3><span class="dashicons dashicons-warning"></span> Problems (<?php echo count( $items ); ?>)</h3>
				<div id="lwtv-tools-alerts">
					<p>The following actor(s) need your attention.</p>
					<?php echo wp_kses_post( $last_run_echo ); ?>
				</div>
			</div>

			<div class="lwtv-tools-table">
				<table class="widefat fixed" cellspacing="0">
					<thead><tr>
						<th id="character" class="manage-column column-character" scope="col">Actor</th>
						<th id="problem" class="manage-column column-problem" scope="col">Problem</th>
						<th id="date" class="manage-column column-date" scope="col">Last Updated</th>
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

		?>
		<form action="admin.php?page=lwtv_data_check&tab=actor_checker" method="post">
			<?php wp_nonce_field( 'run_actor_checker_clicked' ); ?>
			<input type="hidden" value="true" name="rerun" />
			<?php submit_button( 'Check Again' ); ?>
		</form>
		<?php
	}

	/**
	 * Do some juggling to see how actors compare to Wikidata
	 * CURRENTLY NOT USED
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
				<input type="hidden" name="action" value="lwtv_data_check_wikidata_actors">
				<select id="actor_id" name="actor">
					<?php
					foreach ( $items as $id => $name ) {
						echo '<option value="' . esc_attr( $id ) . '">' . esc_html( $name ) . '</option>';
					}
					?>
				</select>
				<?php wp_nonce_field( 'lwtv_data_check_wikidata_actors', 'lwtv_data_check_wikidata_actors_nonce', false ); ?>
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

		$items = LWTV_Transients::get_transient( 'lwtv_debug_actor_empty' );

		// Check whether the button has been pressed AND also check the nonce
		if ( ( isset( $_POST['rerun'] ) && check_admin_referer( 'run_actor_empty_clicked' ) ) || false === $items ) {
			$items = ( new LWTV_Debug() )->find_actors_empty();
		}

		// Get the last run time.
		$last_run_time = self::last_run( 'actor_empty' );
		$last_run_echo = '<p>The incomplete actor checker was last run on ' . $last_run_time . '</p>';

		// Convert to JSON
		$json_it = wp_json_encode( $items );

		if ( empty( $items ) || ! is_array( $items ) ) {
			?>
			<div class="lwtv-tools-container lwtv-tools-container__alert">
				<h3><span class="dashicons dashicons-yes"></span> Excellent!</h3>
				<div id="lwtv-tools-alerts">
					<p>All actors have at least some information.</p>
					<?php echo wp_kses_post( $last_run_echo ); ?>
				</div>
			</div>
			<?php
		} else {
			?>
			<div class="lwtv-tools-container lwtv-tools-container__alert">
				<h3><span class="dashicons dashicons-warning"></span> Problems (<?php echo count( $items ); ?>)</h3>
				<div id="lwtv-tools-alerts">
					<p>The following actor(s) have not have their data entered yet. Please review and update them.</p>
					<?php echo wp_kses_post( $last_run_echo ); ?>
				</div>
			</div>

			<div class="lwtv-tools-table">
				<table class="widefat fixed" cellspacing="0">
					<thead><tr>
						<th id="character" class="manage-column column-character" scope="col">Actor</th>
						<th id="problem" class="manage-column column-problem" scope="col">Problem</th>
						<th id="date" class="manage-column column-date" scope="col">Last Updated</th>
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

		?>
		<form action="admin.php?page=lwtv_data_check&tab=actor_empty" method="post">
			<?php wp_nonce_field( 'run_actor_empty_clicked' ); ?>
			<input type="hidden" value="true" name="rerun" />
			<?php submit_button( 'Check Again' ); ?>
		</form>
		<?php
	}

	/**
	 * Output the results of Show checking...
	 */
	public static function tab_show_checker() {

		$items = LWTV_Transients::get_transient( 'lwtv_debug_show_problems' );

		// Check whether the button has been pressed AND also check the nonce
		if ( ( isset( $_POST['rerun'] ) && check_admin_referer( 'run_show_checker_clicked' ) ) || false === $items ) {
			$items = ( new LWTV_Debug() )->find_shows_problems();
		}

		// Get the last run time.
		$last_run_time = self::last_run( 'show_problems' );
		$last_run_echo = '<p>The show checker was last run on ' . $last_run_time . '</p>';

		if ( empty( $items ) || ! is_array( $items ) ) {
			?>
			<div class="lwtv-tools-container lwtv-tools-container__alert">
				<h3><span class="dashicons dashicons-yes"></span> Excellent!</h3>
				<div id="lwtv-tools-alerts">
					<p>All shows look good and the data looks sane.</p>
					<?php echo wp_kses_post( $last_run_echo ); ?>
				</div>
			</div>
			<?php
		} else {
			?>
			<div class="lwtv-tools-container lwtv-tools-container__alert">
				<h3><span class="dashicons dashicons-warning"></span> Problems (<?php echo count( $items ); ?>)</h3>
				<div id="lwtv-tools-alerts">
					<p>The following show(s) need your attention.</p>
					<p>Note: Remember that intersectionality is meant to be a <em>positive</em> representation. If it's bad disability rep (like Grey's Anatomy with Arizona), do not list them.</p>
					<?php echo wp_kses_post( $last_run_echo ); ?>
				</div>
			</div>

			<div class="lwtv-tools-table">
				<table class="widefat fixed" cellspacing="0">
					<thead><tr>
						<th id="character" class="manage-column column-character" scope="col">Show</th>
						<th id="problem" class="manage-column column-problem" scope="col">Problem</th>
						<th id="date" class="manage-column column-date" scope="col">Last Updated</th>
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

		?>
		<form action="admin.php?page=lwtv_data_check&tab=show_checker" method="post">
			<?php wp_nonce_field( 'run_show_checker_clicked' ); ?>
			<input type="hidden" value="true" name="rerun" />
			<?php submit_button( 'Check Again' ); ?>
		</form>
		<?php
	}

	/**
	 * Output the results of character checking...
	 */
	public static function tab_character_checker() {

		$items = LWTV_Transients::get_transient( 'lwtv_debug_character_problems' );

		// Check whether the button has been pressed AND also check the nonce
		if ( ( isset( $_POST['rerun'] ) && check_admin_referer( 'run_character_checker_clicked' ) ) || false === $items ) {
			$items = ( new LWTV_Debug() )->find_characters_problems();
		}

		// Get the last run time.
		$last_run_time = self::last_run( 'character_problems' );
		$last_run_echo = '<p>The character checker was last run on ' . $last_run_time . '</p>';

		if ( empty( $items ) || ! is_array( $items ) ) {
			?>
			<div class="lwtv-tools-container lwtv-tools-container__alert">
				<h3><span class="dashicons dashicons-yes"></span> Excellent!</h3>
				<div id="lwtv-tools-alerts">
					<p>All characters look good and their data looks sane. Even Sara Lance.</p>
					<?php echo wp_kses_post( $last_run_echo ); ?>
				</div>
			</div>
			<?php
		} else {
			?>
			<div class="lwtv-tools-container lwtv-tools-container__alert">
				<h3><span class="dashicons dashicons-warning"></span> Problems (<?php echo count( $items ); ?>)</h3>
				<div id="lwtv-tools-alerts">
					<p>The following character(s) need your attention.</p>
					<?php echo wp_kses_post( $last_run_echo ); ?>
				</div>
			</div>

			<div class="lwtv-tools-table">
				<table class="widefat fixed" cellspacing="0">
					<thead><tr>
						<th id="character" class="manage-column column-character" scope="col">Character</th>
						<th id="problem" class="manage-column column-problem" scope="col">Problem</th>
						<th id="date" class="manage-column column-date" scope="col">Last Updated</th>
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

		?>
		<form action="admin.php?page=lwtv_data_check&tab=character_checker" method="post">
			<?php wp_nonce_field( 'run_character_checker_clicked' ); ?>
			<input type="hidden" value="true" name="rerun" />
			<?php submit_button( 'Check Again' ); ?>
		</form>
		<?php
	}
}

new LWTV_Data_Validation_Checks();
