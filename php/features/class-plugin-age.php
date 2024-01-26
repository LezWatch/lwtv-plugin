<?php
/*
 * Plugin_Age
 *
 * @package library
 */

namespace LWTV\Features;

class Plugin_Age {

	// Default environment.
	public $default_env_type;

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Init
	 */
	public function init() {
		wp_register_style( 'ui-labs-pluginage', plugins_url( 'assets/css/plugin-age.css', dirname( __DIR__, 1 ) ), false, LWTV_PLUGIN_VERSION );
		wp_enqueue_style( 'ui-labs-pluginage' );
		add_action( 'after_plugin_row', array( $this, 'pluginage_row' ), 10, 2 );
	}

	/**
	 * Show the age of a plugin underneath if it's over 2 years old
	 *
	 * @param string $file
	 * @param array  $plugin_data
	 * @return false|void
	 */
	public function pluginage_row( $file, $plugin_data ) {

		// Forcing a hard set - If there's no slug, then this plugin is screwy and should be skipped.
		if ( ! isset( $plugin_data['slug'] ) ) {
			return false;
		}

		// Look at https://core.trac.wordpress.org/changeset/50921 and we want to check that!
		$lastupdated = strtotime( $this->pluginage_get_last_updated( $plugin_data['slug'] ) );
		$twoyears    = strtotime( '-2 years' );

		// If it's false (not hosted on .org) or it's less than 2 years old, then we don't want to show it.
		if ( ! $lastupdated || $lastupdated >= $twoyears ) {
			return false;
		}

		$plugins_allowedtags = array(
			'a'       => array(
				'href'  => array(),
				'title' => array(),
			),
			'abbr'    => array( 'title' => array() ),
			'acronym' => array( 'title' => array() ),
			'code'    => array(),
			'em'      => array(),
			'strong'  => array(),
		);

		$plugin_name   = wp_kses( $plugin_data['Name'], $plugins_allowedtags );
		$wp_list_table = _get_list_table( 'WP_Plugins_List_Table' );
		$active_class  = is_plugin_active( $file ) ? ' active' : '';

		echo '<tr class="plugin-age-tr' . esc_attr( $active_class ) . '" id="' . esc_attr( $plugin_data['slug'] . '-age' ) . '" data-slug="' . esc_attr( $plugin_data['slug'] ) . '" data-plugin="' . esc_attr( $file ) . '"><td colspan="' . esc_attr( $wp_list_table->get_column_count() ) . '" class="plugin-age colspanchange"><div class="age-message">';
		// translators: %1 - Plugin Name ; %2 - time since last update
		$results = sprintf( __( '%1$s was last updated %2$s ago and may no longer be supported.', 'ui-labs' ), $plugin_name, human_time_diff( $lastupdated, current_time( 'timestamp' ) ) ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
		echo esc_html( $results );
		echo '</div></td></tr>';
	}

	/**
	 * Detect the age of the plugin
	 * From https://wordpress.org/plugins/plugin-last-updated/
	 *
	 * @param string $slug
	 * @return mixed
	 */
	public function pluginage_get_last_updated( $slug ) {

		// Bail early - If there's no slug, then this plugin is screwy and should be skipped.
		// Return an empty but CACHABLE response.
		if ( ! isset( $slug ) ) {
			return false;
		}

		$request = wp_remote_post(
			'http://api.wordpress.org/plugins/info/1.0/',
			array(
				'body' => array(
					'action'  => 'plugin_information',
					'request' => serialize( // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
						(object) array(
							'slug'   => $slug,
							'fields' => array( 'last_updated' => true ),
						)
					),
				),
			)
		);

		if ( 200 !== wp_remote_retrieve_response_code( $request ) || empty( $request ) ) {
			// If there's no response, return with a cacheable response
			return false;
		} else {
			// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize
			$response = unserialize( wp_remote_retrieve_body( $request ) );
		}

		if ( isset( $response->last_updated ) ) {
			return sanitize_text_field( $response->last_updated );
		} else {
			return false;
		}
	}
}
