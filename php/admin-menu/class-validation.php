<?php
/*
 * Data Sync Checks For LezWatch.TV
 *
 * @since 2.4
 */

namespace LWTV\Admin_Menu;

use LWTV\Validator\Actor_Checker;
use LWTV\Validator\Actor_Empty;
use LWTV\Validator\Actor_IMDb;
use LWTV\Validator\Actor_Wiki;
use LWTV\Validator\Character_Checker;
use LWTV\Validator\Queer_Checker;
use LWTV\Validator\Show_Checker;
use LWTV\Validator\Show_IMDb;
use LWTV\Validator\Show_URLs;

class Validation {

	/**
	 * Local Variables
	 */
	protected $page_id = null;        // page ID

	private const TOOL_TABS = array(
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
		'actor_imdb'        => array(
			'name' => 'Actors IMDb',
			'desc' => 'Actors who have no IMDb value. This may actually be okay as not all webseries/international shows are listed.',
		),
		'character_checker' => array(
			'name' => 'Character Checker',
			'desc' => 'Checks that all information for characters appears correct, like if they have a show and years-on-air added.',
		),
		'show_checker'      => array(
			'name' => 'Show Checker',
			'desc' => 'Checks that all information for shows appears correct. Like do they have characters and ratings etc, does intersectionality seem to match.',
		),
		'show_imdb'         => array(
			'name' => 'Shows IMDb',
			'desc' => 'Shows that have no IMDb value. This may actually be okay as not all webseries/international shows are listed.',
		),
		'show_urls'         => array(
			'name' => 'Shows Watch Links',
			'desc' => 'Shows that have invalid Ways to Watch links. Make sure they are all valid and functional.',
		),
	);

	/**
	 * Setup dashboard.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'add_admin_notices' ) );
		add_action( 'admin_post_lwtv_data_check_wikidata_actors', array( $this, 'check_actors_wikidata' ) );
		add_submenu_page( 'lwtv', 'Data Validation', 'Data Validation', 'upload_files', 'lwtv_data_check', array( $this, 'settings_page' ) );
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

	/**
	 * Last Run
	 *
	 * @param  string $tool
	 * @return string When was a tool last run.
	 */
	public static function last_run( $tool ) {
		$options = get_option( 'lwtv_debugger_status' );

		// Get the timestamp from the individual last run OR the global.
		if ( 'intro' !== $tool && isset( $options[ $tool ]['last'] ) ) {
			$timestamp = $options[ $tool ]['last'];
			$tool      = 'checker';
		} else {
			$timestamp = $options['timestamp'];
		}

		$last_run_time = '<strong>' . get_date_from_gmt( gmdate( 'Y-m-d H:i:s', $timestamp ), 'F j, Y H:i:s' ) . '</strong> (' . human_time_diff( $timestamp ) . ' ago).';
		$last_run_echo = '<p>The ' . str_replace( '_', ' ', $tool ) . ' was last run on ' . $last_run_time . '</p>';

		return $last_run_echo;
	}

	/*
	 * Admin Notices
	 *
	 * @reutrn void
	 */
	private function admin_notices() {
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
			$message = '<div class="notice notice-' . esc_attr( $notice_value ) . ' is-dissmissable"><p>' . esc_html( $content ) . '</p></div>';
			add_action( 'admin_notices', $message );
		}
	}

	/*
	 * Settings Page Content
	 *
	 * @return void
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
				foreach ( self::TOOL_TABS as $tab => $value ) {
					$active = ( 'tab_' . $tab === $active_tab ) ? 'nav-tab-active' : '';
					echo '<a href="?page=lwtv_data_check&tab=tab_' . esc_attr( $tab ) . '" class="nav-tab ' . esc_attr( $active ) . '">' . esc_html( $value['name'] ) . '</a>';
				}
				?>
			</h2>

			<div id="dashboard" class="lwtvtab">
				<?php

				switch ( $active_tab ) {
					case 'tab_actor_checker':
						( new Actor_Checker() )->make();
						break;
					case 'tab_actor_empty':
						( new Actor_Empty() )->make();
						break;
					case 'tab_actor_imdb':
						( new Actor_IMDB() )->make();
						break;
					case 'tab_actor_wiki':
						( new Actor_Wiki() )->make();
						break;
					case 'tab_character_checker':
						( new Character_Checker() )->make();
						break;
					case 'tab_queer_checker':
						( new Queer_Checker() )->make();
						break;
					case 'tab_show_checker':
						( new Show_Checker() )->make();
						break;
					case 'tab_show_imdb':
						( new Show_IMDb() )->make();
						break;
					case 'tab_show_urls':
						( new Show_URLs() )->make();
						break;
					default:
						self::tab_introduction();
						break;
				}
				?>
			</div>

		</div>
		<?php
	}

	/**
	 * Build Table content
	 *
	 * @param  array  $items
	 * @return string Table Content
	 */
	public static function table_content( $items ) {
		$number = 1;
		foreach ( $items as $item ) {
			$class     = ( 0 === $number % 2 ) ? '' : 'alternate';
			$modified  = get_post_timestamp( (int) $item['id'], 'modified' );
			$published = get_post_timestamp( (int) $item['id'], 'date' );
			$current   = current_datetime()->format( 'U' );
			$time_diff = 'never modified';
			if ( ! empty( $modified ) ) {
				$time      = date_i18n( get_option( 'date_format' ), $modified );
				$time_diff = human_time_diff( $modified, $current );
			} else {
				$time = date_i18n( get_option( 'date_format' ), $published );
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
			++$number;
		}
	}

	/**
	 * Static Introduction to what the hell is going on...
	 */
	public static function tab_introduction() {
		?>

		<div class="tab-block"><div class="lwtv-tools-container">
			<h3>LezWatch.TV Data Validation Checks</h3>
			<p>If data gets out of sync or we update things incorrectly, these checkers can help identify those errors before people notice. They run on an automated cycle, each check once a week, to try and catch things early.</p>

			<p>When visiting the individual checker, it will show you the status of the last run. To re-run the tool, press the 'Run Scan' button at the bottom of the page.</p>

			<?php self::last_run( 'intro' ); ?>

			<?php self::current_status(); ?>

			<hr>

			<ul>
				<?php
				foreach ( self::TOOL_TABS as $tab => $value ) {
					echo '<li>&bull; <a href="?page=lwtv_data_check&tab=tab_' . esc_attr( $tab ) . '">' . esc_html( $value['name'] ) . '</a> - ' . esc_html( $value['desc'] ) . '</li>';
				}
				?>
			</ul>

		</div></div>

		<?php
	}

	private static function current_status(): void {
		// Get the options
		$options = get_option( 'lwtv_debugger_status' );

		if ( empty( $options ) || ! array( $options ) ) {
			return;
		}

		// Build the list.
		$list = '<ul>';
		foreach ( $options as $an_option ) {
			$number = ( isset( $an_option['count'] ) ) ? $an_option['count'] : 0;
			if ( (int) $number > 0 ) {
				$list .= '<li>&bull; ' . $an_option['name'] . ' - ' . $an_option['count'] . '</li>';
			}
		}
		$list .= '</ul>';

		if ( ! empty( $list ) ) {
			echo wp_kses_post( '<p><strong>Current Status</strong></p>' . $list );
		}
	}
}
