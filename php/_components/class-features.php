<?php
/*
 * LezWatch.TV Features
 *
 */

namespace LWTV\_Components;

use LWTV\Features\Cron;
use LWTV\Features\Dashboard_Posts_In_Progress;
use LWTV\Features\Dashboard;
use LWTV\Features\Embeds;
use LWTV\Features\Environment;
use LWTV\Features\Languages;
use LWTV\Features\Plugin_Age;
use LWTV\Features\Private_Posts;
use LWTV\Features\Roles;
use LWTV\Features\Shortcodes;
use LWTV\Features\Spammers;
use LWTV\Features\Upgrades;
use LWTV\Features\User_Profiles;

class Features implements Component, Templater {

	/*
	 * Init
	 */
	public function init() {
		add_filter( 'wp_headers', array( $this, 'modify_front_end_http_headers' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'pre_ping', array( $this, 'no_self_ping' ) );
		add_filter( 'all_plugins', array( $this, 'hide_lwtv_plugin' ) );

		// Instantiate actions and filters:
		add_action( 'init', array( $this, 'instantiate_actions_and_filters' ) );

		// Load them:
		new Cron();
		new Dashboard_Posts_In_Progress();
		new Dashboard();
		new Embeds();
		new Environment();
		new Plugin_Age();
		new Private_Posts();
		new Roles();
		new Shortcodes();
		new Upgrades();
		new User_Profiles();
	}

	/**
	 * Gets tags to expose as methods accessible through `lwtv_plugin()`.
	 *
	 * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
	 *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
	 *               adding support for further arguments in the future.
	 */
	public function get_template_tags(): array {
		return array(
			'get_all_languages' => array( $this, 'get_all_languages' ),
			'get_spammers_list' => array( $this, 'get_spammers_list' ),
			'is_spammer'        => array( $this, 'is_spammer' ),
		);
	}

	/**
	 * Prevent self pings by interlinks
	 *
	 * @since 1.2.0
	 */
	public function no_self_ping( &$links ) {
		$home = get_option( 'home' );
		foreach ( $links as $l => $link ) {
			if ( 0 === strpos( $link, $home ) ) {
				unset( $links[ $l ] );
			}
		}
	}

	/**
	 * Get all languages we know.
	 */
	public function get_all_languages(): array {
		return ( new Languages() )->all_languages();
	}

	/**
	 * Get all Spammers
	 */
	public function get_spammers_list(): array {
		return ( new Spammers() )->list();
	}

	/**
	 * Is this a Spammer?
	 *
	 * @param  string $to_check - content to check (i.e. "John Doe", "foo@example.com")
	 * @param  string $type     - Type of to check (email, name, URL)
	 * @param  string $keys     - Key to check against (nearly always disallowed)
	 * @return bool
	 */
	public function is_spammer( $to_check, $type = 'email', $keys = 'disallowed_keys' ): bool {
		return ( new Spammers() )->is_spammer( $to_check, $type, $keys );
	}

	/**
	 * Initialize Actions and Filters
	 *
	 * @return void
	 */
	public function instantiate_actions_and_filters(): void {
		add_filter( 'http_request_args', array( $this, 'disable_wp_update' ), 10, 2 );
		add_filter( 'attachment_fields_to_edit', array( $this, 'add_attachment_attribution' ), 10000, 2 );
		add_action( 'edit_attachment', array( $this, 'save_attachment_attribution' ) );
		add_filter( 'avatar_defaults', array( $this, 'default_avatar' ) );

		// Disable check for 'is your admin stuff legit'.
		// https://make.wordpress.org/core/2019/10/17/wordpress-5-3-admin-email-verification-screen/ .
		add_filter( 'admin_email_check_interval', '__return_false' );

		// Disable email update alerts for themes and plugins.
		add_filter( 'auto_plugin_update_send_email', '__return_false' );
		add_filter( 'auto_theme_update_send_email', '__return_false' );

		// Extend the cookies.
		add_filter( 'auth_cookie_expiration', array( $this, 'extend_login_session' ) );

		// Force close comments on media.
		add_filter( 'comments_open', array( $this, 'filter_media_comment_status' ), 10, 2 );

		// Enqueue scripts.
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		// Login Page Changes.
		add_action( 'login_enqueue_scripts', array( $this, 'login_logos' ) );
		add_filter( 'login_headerurl', array( $this, 'login_headerurl' ) );
		add_filter( 'login_headertext', array( $this, 'login_headertitle' ) );
		add_filter( 'login_errors', array( $this, 'login_errors' ) );

		// When in Dev Mode...
		if ( defined( 'LWTV_DEV_SITE' ) && LWTV_DEV_SITE ) {
			add_action( 'wp_head', array( $this, 'add_meta_tags' ), 2 );
			defined( 'JETPACK_DEV_DEBUG' ) || define( 'JETPACK_DEV_DEBUG', true );
		}

		// Post Statues
		wp_register_style( 'ui-labs-post-statuses', plugins_url( 'assets/css/post-statuses.css', dirname( __DIR__ ) ), false, LWTV_PLUGIN_VERSION );
		wp_enqueue_style( 'ui-labs-post-statuses' );

		// After Theme Setup...
		add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ), 11 );

		// Block pingbacks.
		add_filter( 'xmlrpc_methods', array( $this, 'remove_xmlrpc_methods' ) );
	}

	/**
	 * Remove pingbacks.
	 * https://blog.sonarsource.com/wordpress-core-unauthenticated-blind-ssrf/
	 *
	 * @param array $methods XMLRPC methods.
	 */
	public function remove_xmlrpc_methods( $methods ): array {
		unset( $methods['pingback.ping'] );
		return $methods;
	}

	/**
	 * Hide the LWTV Plugin from the Plugin list.
	 *
	 * @access public
	 * @return array
	 */
	public function hide_lwtv_plugin( $plugins ): array {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$should_hide    = ! array_key_exists( 'show_all', $_GET );
		$hidden_plugins = array(
			'lwtv-plugin/functions.php',
		);

		if ( $should_hide ) {
			foreach ( $hidden_plugins as $hidden_plugin ) {
				unset( $plugins[ $hidden_plugin ] );
			}
		}

		return $plugins;
	}

	/**
	 * Disable WP from updating this plugin.
	 *
	 * @access public
	 * @param mixed $data   - array to return.
	 * @param mixed $url    - URL from which checks come and need to be blocked (i.e. wp.org).
	 * @return array        - $data
	 */
	public function disable_wp_update( $data, $url ): array {
		if ( 0 === strpos( $url, 'https://api.wordpress.org/plugins/update-check/' ) ) {
			$my_plugin = plugin_basename( dirname( __DIR__, 1 ) );
			$plugins   = json_decode( $data['body']['plugins'], true );
			unset( $plugins['plugins'][ $my_plugin ] );
			unset( $plugins['active'][ array_search( $my_plugin, $plugins['active'], true ) ] );
			$data['body']['plugins'] = wp_json_encode( $plugins );
		}
		return $data;
	}

	/**
	 * Get the icon as SVG.
	 *
	 * Forked from Yoast SEO
	 *
	 * @access public
	 * @param bool   $base64 (default: true) - Use SVG, true/false.
	 * @param string $icon_color - What color to use.
	 * @return string
	 */
	public function get_icon_svg( $base64 = true, $icon_color = false ): string {
		$fill = ( false !== $icon_color ) ? sanitize_hex_color( $icon_color ) : '#82878c';

		$svg = '<svg width="100%" height="100%" version="1.1" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="fill:' . $fill . '"><path d="M4,10c0,-4.411 3.589,-8 8,-8c4.411,0 8,3.589 8,8v2.08c0.706,0.102 1.378,0.308 2,0.605v-2.685c0,-5.514 -4.486,-10 -10,-10c-5.514,0 -10,4.486 -10,10v2.685c0.622,-0.297 1.294,-0.503 2,-0.605Zm8,-6c-3.309,0 -6,2.691 -6,6v1.025c0.578,-0.772 1.294,-1.43 2.112,-1.929c0.412,-1.77 1.994,-3.096 3.888,-3.096c1.894,0 3.476,1.326 3.888,3.096c0.819,0.499 1.534,1.157 2.112,1.929v-1.025c0,-3.309 -2.691,-6 -6,-6Zm7,10h-1.712c-0.654,-2.307 -2.771,-4 -5.288,-4c-2.517,0 -4.634,1.693 -5.288,4h-1.712c-2.761,0 -5,2.239 -5,5c0,2.761 2.239,5 5,5h14c2.761,0 5,-2.239 5,-5c0,-2.761 -2.239,-5 -5,-5Z" transform="scale(0.666667)" fill="' . $fill . '"></path></svg>';

		if ( $base64 ) {
			return 'data:image/svg+xml;base64,' . base64_encode( $svg );
		}

		return $svg;
	}

	/**
	 * Add attribution element to images.
	 *
	 * @access public
	 * @param mixed $form_fields  array - the fields.
	 * @param mixed $post         int   - the post ID.
	 * @return                    array - form fields.
	 */
	public function add_attachment_attribution( $form_fields, $post ): array {
		$field_value                     = get_post_meta( $post->ID, 'lwtv_attribution', true );
		$form_fields['lwtv_attribution'] = array(
			'value' => $field_value ? $field_value : '',
			'label' => __( 'Attribution' ),
			'helps' => __( 'Insert image attribution here (i.e. "NBCUniversal" etc)' ),
		);
		return $form_fields;
	}

	/**
	 * Save attribution element to attachment post meta.
	 *
	 * @access public
	 * @param mixed $attachment_id  int - attachment ID.
	 * @return void
	 */
	public function save_attachment_attribution( $attachment_id ): void {
		if ( isset( $_REQUEST['attachments'][ $attachment_id ]['lwtv_attribution'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$lwtv_attribution = sanitize_text_field( wp_unslash( $_REQUEST['attachments'][ $attachment_id ]['lwtv_attribution'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
			update_post_meta( $attachment_id, 'lwtv_attribution', $lwtv_attribution );
		}
	}

	/**
	 * Adding new options for default avatar
	 *
	 * @param  array $defaults  Default Avatar data.
	 *
	 * @return array $defaults  Updated defaults.
	 */
	public function default_avatar( $defaults ): array {
		$toaster              = plugins_url( 'assets/images/toaster.png', dirname( __DIR__, 1 ) );
		$defaults[ $toaster ] = 'Toaster';
		$unicorn              = plugins_url( 'assets/images/unicorn.png', dirname( __DIR__, 1 ) );
		$defaults[ $unicorn ] = 'Unicorn';
		return $defaults;
	}


	/**
	 * Validate date format.
	 *
	 * @param  string $date   Date String.
	 * @param  string $format Format to output.
	 * @return string         Updated date format.
	 */
	public function validate_date( $date, $format = 'Y-m-d' ): string {
		$d = \DateTime::createFromFormat( $format, $date );
		// The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
		return $d && $d->format( $format ) === $date;
	}

	/**
	 * After Theme Setup
	 *
	 * Source: https://make.wordpress.org/core/2021/06/14/introducing-the-template-editor-in-wordpress-5-8/
	 */
	public function after_setup_theme(): void {
		remove_theme_support( 'block-templates' );
	}

	/**
	 * Admin CSS
	 */
	public function admin_enqueue_scripts(): void {
		if ( is_admin() ) {
			wp_enqueue_style( 'lwtv_data_check_admin', plugins_url( 'assets/css/wp-admin.css', dirname( __DIR__, 1 ) ), array(), LWTV_PLUGIN_VERSION );
		}
	}

	/**
	 * Login Logos
	 */
	public function login_logos(): void {
		?>
		<style type="text/css">
			#login h1 a, .login h1 a { background-image: url(<?php echo esc_url( plugins_url( 'assets/images/lezwatchtv.png', dirname( __DIR__, 1 ) ) ); ?>);
				height:80px;
				width:80px;
				background-size: 80px 80px;
			}
		</style>
		<?php
	}

	/**
	 * Login URL
	 */
	public function login_headerurl(): string {
		return home_url();
	}

	/**
	 * Login Title
	 */
	public function login_headertitle(): string {
		return get_bloginfo( 'name' );
	}

	/**
	 * Login Errors
	 *
	 * If you put in the wrong password, Diane flips you off.
	 *
	 * @param  string $error Existing Error.
	 * @return string $error Updated Error.
	 */
	public function login_errors( $error ): string {
		$diane = '<br /><img src="' . plugins_url( '/assets/images/diane-fuck-off.gif', dirname( __DIR__, 1 ) ) . '" />';
		$error = $error . $diane;
		return $error;
	}

	/**
	 * Damn it Google, GO AWAY from our dev sites!
	 * Since: 2.1.4
	 */
	public function add_meta_tags(): void {
		echo '<meta name="robots" content="noindex">' . "\n";
	}

	/**
	 * Disable comments on media files.
	 * Since: 1.0.0
	 *
	 * @param bool $open    TrueFalse if the comments are open.
	 * @param int  $post_id Post ID.
	 *
	 * @return boolean  Open or Closed.
	 */
	public function filter_media_comment_status( $open, $post_id ): bool {
		$post = get_post( $post_id );
		if ( 'attachment' === $post->post_type ) {
			return false;
		}
		return $open;
	}

	/**
	 * Extend login sessions
	 *
	 * @param  int $expire Current expire length (3 days).
	 * @return int            New time (1 year)
	 */
	public function extend_login_session( $expire ): string {
		if ( ! empty( $expire ) ) {
			return $expire;
		}
		// Set login session limit in seconds.
		return YEAR_IN_SECONDS;
	}

	/**
	 * Modify the response HTTP headers for front-end requests
	 *
	 * @param array $headers
	 * @param WP    $wp
	 *
	 * @return array
	 */
	public function modify_front_end_http_headers( $headers, $wp ): array {
		// The oEmbed endpoints should remain embed-able.
		if ( ! isset( $wp->query_vars['embed'] ) || ! $wp->query_vars['embed'] ) {
			$headers['X-Frame-Options'] = 'SAMEORIGIN';
		}

		return $headers;
	}
}
