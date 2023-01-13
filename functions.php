<?php
/**
 * Plugin Name: Core LezWatch.TV Plugin
 * Plugin URI:  https://lezwatchtv.com
 * Description: All the base code for LezWatch.TV - If this isn't active, the site dies. An ugly death.
 * Version: 3.3
 * Author: LezWatch.TV
 * Update URI: http://lezwatchtv.com
 *
 * @package LWTV_PLUGIN
*/

/*
 * Define the first year
 */
if ( ! defined( 'FIRST_LWTV_YEAR' ) ) {
	define( 'FIRST_LWTV_YEAR', '1961' );
}

/*
 * Load Symbolicons
 */
if ( ! defined( 'LWTV_SYMBOLICONS_PATH' ) ) {
	require_once 'assets/symbolicons.php';
}

/**
 * class LWTV_Functions
 *
 * The background functions for the site, independent of the theme.
 */
class LWTV_Functions {

	protected static $version;

	/**
	 * Constructor.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		self::$version = '3.3';
		add_filter( 'http_request_args', array( $this, 'disable_wp_update' ), 10, 2 );
		add_filter( 'attachment_fields_to_edit', array( $this, 'add_attachment_attribution' ), 10000, 2 );
		add_action( 'edit_attachment', array( $this, 'save_attachment_attribution' ) );
		add_action( 'pre_current_active_plugins', array( $this, 'hide_lwtv_plugin' ) );
		add_filter( 'avatar_defaults', array( $this, 'default_avatar' ) );

		// Disable check for 'is your admin password legit'.
		// https://make.wordpress.org/core/2019/10/17/wordpress-5-3-admin-email-verification-screen/
		add_filter( 'admin_email_check_interval', '__return_false' );

		// Disable email update alerts for themes and plugins
		add_filter( 'auto_plugin_update_send_email', '__return_false' );
		add_filter( 'auto_theme_update_send_email', '__return_false' );

		// Extend the cookies
		add_filter( 'auth_cookie_expiration', array( $this, 'extend_login_session' ) );

		// Force close comments on media
		add_filter( 'comments_open', array( $this, 'filter_media_comment_status' ), 10, 2 );

		// Enqueue scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		// Login Page Changes
		add_action( 'login_enqueue_scripts', array( $this, 'login_logos' ) );
		add_filter( 'login_headerurl', array( $this, 'login_headerurl' ) );
		add_filter( 'login_headertext', array( $this, 'login_headertitle' ) );
		add_filter( 'login_errors', array( $this, 'login_errors' ) );

		// When in Dev Mode...
		if ( defined( 'LWTV_DEV_SITE' ) && LWTV_DEV_SITE ) {
			add_action( 'wp_head', array( $this, 'add_meta_tags' ), 2 );
			defined( 'JETPACK_DEV_DEBUG' ) || define( 'JETPACK_DEV_DEBUG', true );
		}

		// After Theme Setup...
		add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ), 11 );

		// Block pingbacks
		add_filter( 'xmlrpc_methods', array( $this, 'remove_xmlrpc_methods' ) );
	}

	/**
	 * Remove pingbacks
	 * https://blog.sonarsource.com/wordpress-core-unauthenticated-blind-ssrf/
	 */
	public function remove_xmlrpc_methods( $methods ) {
		unset( $methods['pingback.ping'] );
		return $methods;
	}

	/**
	 * Hide the LWTV Plugin.
	 *
	 * @access public
	 * @return void
	 */
	public function hide_lwtv_plugin() {
		global $wp_list_table;

		$hide_plugins = array(
			plugin_basename( __FILE__ ),
		);
		$curr_plugins = $wp_list_table->items;
		foreach ( $curr_plugins as $plugin => $data ) {
			if ( in_array( $plugin, $hide_plugins, true ) ) {
				unset( $wp_list_table->items[ $plugin ] );
			}
		}
	}

	/**
	 * Disable WP from updating this plugin.
	 *
	 * @access public
	 * @param mixed $return - array to return.
	 * @param mixed $url    - URL from which checks come and need to be blocked (i.e. wp.org)
	 * @return array        - $return
	 */
	public function disable_wp_update( $return, $url ) {
		if ( 0 === strpos( $url, 'https://api.wordpress.org/plugins/update-check/' ) ) {
			$my_plugin = plugin_basename( __FILE__ );
			$plugins   = json_decode( $return['body']['plugins'], true );
			unset( $plugins['plugins'][ $my_plugin ] );
			unset( $plugins['active'][ array_search( $my_plugin, $plugins['active'], true ) ] );
			$return['body']['plugins'] = wp_json_encode( $plugins );
		}
		return $return;
	}

	/**
	 * Get the icon as SVG.
	 *
	 * Forked from Yoast SEO
	 *
	 * @access public
	 * @param bool $base64 (default: true) - Use SVG, true/false?
	 * @param string $icon_color - What color to use.
	 * @return string
	 */
	public function get_icon_svg( $base64 = true, $icon_color = false ) {
		global $_wp_admin_css_colors;

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
	public function add_attachment_attribution( $form_fields, $post ) {
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
	public function save_attachment_attribution( $attachment_id ) {
		if ( isset( $_REQUEST['attachments'][ $attachment_id ]['lwtv_attribution'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$lwtv_attribution = $_REQUEST['attachments'][ $attachment_id ]['lwtv_attribution']; // phpcs:ignore WordPress.Security.NonceVerification
			update_post_meta( $attachment_id, 'lwtv_attribution', $lwtv_attribution );
		}
	}

	/**
	 * Adding new options for default avatar
	 * @param  array $defaults
	 * @return array $defaults
	 */
	public function default_avatar( $defaults ) {
		$toaster              = plugins_url( 'assets/images/toaster.png', __FILE__ );
		$defaults[ $toaster ] = 'Toaster';
		$unicorn              = plugins_url( 'assets/images/unicorn.png', __FILE__ );
		$defaults[ $unicorn ] = 'Unicorn';
		return $defaults;
	}

	/**
	 * Symbolicons Output
	 *
	 * Echos the default outputtable symbolicon, based on the SVG and FA icon passed to it.
	 *
	 * @access public
	 * @param string $svg (default: 'square.svg')
	 * @param string $fontawesome (default: 'fa-square')
	 * @return icon
	 */
	public function symbolicons( $svg = 'square.svg', $fontawesome = 'fa-square' ) {

		$return = '<i class="fas ' . $fontawesome . ' fa-fw" aria-hidden="true"></i>';
		$square = get_template_directory_uri( '/images/square.svg' );

		if ( ! empty( $svg ) && defined( 'LWTV_SYMBOLICONS_PATH' ) && file_exists( LWTV_SYMBOLICONS_PATH . $svg ) ) {
			$icon = LWTV_SYMBOLICONS_PATH . $svg;
		} elseif ( ! wp_style_is( 'fontawesome', 'enqueued' ) ) {
			$icon = $square;
		}

		if ( isset( $icon ) ) {
			// @codingStandardsIgnoreStart
			$return = '<span class="symbolicon" role="img">' . file_get_contents( $icon ) . '</span>';
			// @codingStandardsIgnoreEnd
		}

		return $return;
	}

	/**
	 * Validate date format/
	 * @param  [type] $date   [description]
	 * @param  string $format [description]
	 * @return [type]         [description]
	 */
	public function validate_date( $date, $format = 'Y-m-d' ) {
		$d = DateTime::createFromFormat( $format, $date );
		// The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
		return $d && $d->format( $format ) === $date;
	}

	/**
	 * After Theme Setup
	 */
	public function after_setup_theme() {
		//https://make.wordpress.org/core/2021/06/14/introducing-the-template-editor-in-wordpress-5-8/
		remove_theme_support( 'block-templates' );
	}

	/*
	 * Admin CSS
	 */
	public function admin_enqueue_scripts() {
		wp_enqueue_style( 'admin-styles', plugins_url( 'assets/css/wp-admin.css', __FILE__ ), array(), self::$version );
	}

	/*
	 * Login Logos
	 */
	public function login_logos() {
		?>
		<style type="text/css">
			#login h1 a, .login h1 a { background-image: url(<?php echo esc_url( plugins_url( 'assets/images/lezwatchtv.png', __FILE__ ) ); ?>);
				height:80px;
				width:80px;
				background-size: 80px 80px;
			}
		</style>
		<?php
	}

	/*
	 * Login URL
	 */
	public function login_headerurl() {
		return home_url();
	}

	/*
	 * Login Title
	 */
	public function login_headertitle() {
		return get_bloginfo( 'name' );
	}

	/*
	 * Login Errors
	 */
	public function login_errors( $error ) {
		$diane = '<br /><img src="' . plugins_url( 'assets/images/diane-fuck-off.gif', __FILE__ ) . '" />';
		$error = $error . $diane;
		return $error;
	}

	/**
	 * Damn it Google, GO AWAY
	 * Since: 2.1.4
	 */
	public function add_meta_tags() {
		echo '<meta name="robots" content="noindex">' . "\n";
	}

	/**
	 * Disable comments on media files.
	 * Since: 1.0.0
	 */
	public function filter_media_comment_status( $open, $post_id ) {
		$post = get_post( $post_id );
		if ( 'attachment' === $post->post_type ) {
			return false;
		}
		return $open;
	}

	/**
	 * Extend login sessions
	 * @param  int    $expire Current expire length (3 days)
	 * @return int            New time
	 */
	public function extend_login_session( $expire ) {
		// Set login session limit in seconds
		return YEAR_IN_SECONDS;
	}

}
new LWTV_Functions();

/*
 * Add-Ons.
 */
require_once 'features/_main.php';     // General Features: This has to be at the top.

require_once 'admin/_main.php';        // Admin Settings
require_once 'affiliates/_main.php';   // Affiliates and Where to Watch
require_once 'assets/symbolicons.php'; // Symbolicons/Font Icons
require_once 'blocks/_main.php';       // Custom Blocks
require_once 'plugins/_main.php';      // Tweaks for Plugins
require_once 'rest-api/_main.php';     // Our Rest API
require_once 'statistics/_main.php';   // Stats
require_once 'this-year/_main.php';    // This Year

require_once 'cpts/_main.php';         // Custom Post Types: This has to be at the end.

/*
 * Composer
 */
require_once 'vendor/autoload.php';
