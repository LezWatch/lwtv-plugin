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

$valid_exports = array( 'wikidata' );
$export_type   = ( isset( $wp_query->query['export'] ) && in_array( $wp_query->query['export'], $valid_exports, true ) ) ? esc_attr( $wp_query->query['export'] ) : 'wikidata';
$a_post_type   = str_replace( 'post_type_', '', $wp_query->query['post_type'] );

switch ( $export_type ) {
	case 'wikidata':
		$return = LWTV_Export_JSON::export( $a_post_type, esc_attr( $wp_query->query['name'] ) );
		break;
}

echo '<pre>';
print_r( wp_json_encode( $return, JSON_UNESCAPED_SLASHES ) );
echo '</pre>';
