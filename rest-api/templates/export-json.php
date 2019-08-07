<?php
/**
 * Template Name: Export JSON
 * Description: This is a custom exporter meant for easily exporting data.
 *
 * This uses var query data to determine what to show. Currently only used by
 * the WikiData project, but build expandable to handle more.
 *
 * @package LezWatch.TV
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

$valid_exports = array( 'wikidata', 'wikilist' );
$export_type   = ( isset( $wp_query->query['export'] ) && in_array( $wp_query->query['export'], $valid_exports, true ) ) ? esc_attr( $wp_query->query['export'] ) : 'wikidata';
$a_post_type   = ( isset( $wp_query->query['exportname'] ) ) ? esc_attr( $wp_query->query['exportname'] ) : str_replace( 'post_type_', '', esc_attr( $wp_query->query['post_type'] ) );

switch ( $export_type ) {
	case 'wikilist':
		$return = LWTV_Export_JSON::export( 'list', $a_post_type . 's' );
		foreach ( $return as $item ) {
			$echo = wp_json_encode( $item, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
			if ( '[]' !== $echo ) {
				echo '<pre>' . wp_kses_post( $echo ) . '</pre>';
			}
		}
		break;
	case 'wikidata':
		$return = LWTV_Export_JSON::export( $a_post_type, esc_attr( $wp_query->query['name'] ) );
		$echo   = wp_json_encode( $return, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
		echo '<pre>' . wp_kses_post( $echo ) . '</pre>';
		break;
}
