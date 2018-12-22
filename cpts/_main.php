<?php
/**
 * Name: Custom Post Types
 *
 */

// Include the base files
require_once 'actors/_main.php';
require_once 'characters/_main.php';
require_once 'shows/_main.php';
require_once 'related-posts.php';

// phpcs:disable
// Suppress the editorial calendar for all custom post types.
add_filter( 'edcal_show_calendar_post_type_shows', function() { return false; } );
add_filter( 'edcal_show_calendar_post_type_characters', function() { return false; } );
add_filter( 'edcal_show_calendar_post_type_actors', function() { return false; } );
// phpcs:enable
