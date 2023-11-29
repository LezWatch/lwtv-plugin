<?php

// Plugin Version
define( 'LWTV_VERSION', '5.0' );

// Define First Year:
define( 'FIRST_LWTV_YEAR', '1961' );

/**
 * Symbolicons
 */
$upload_dir = wp_upload_dir();
// Define Symbolicons path:
define( 'LWTV_SYMBOLICONS_PATH', $upload_dir['basedir'] . '/lezpress-icons/symbolicons/' );
// Define Symbolicons url:
define( 'LWTV_SYMBOLICONS_URL', $upload_dir['baseurl'] . '/lezpress-icons/symbolicons/' );
