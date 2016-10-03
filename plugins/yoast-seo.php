<?php

/*
Plugin Name: Yoast SEO Customizations
Description: Some tweaks I have for Yoast SEO
Version: 2.0

*/

// Unless we're on a post or a post editing related page, shut up
global $pagenow;

$pagenow_array = array( 'post.php', 'edit.php', 'post-new.php' );
if ( !in_array( $pagenow , $pagenow_array ) ) {
	return;
}

// Since we are, we need to know exactly what we're on and this is a hassle.
global $typenow;

// when editing pages, $typenow isn't set until later!
if ( empty($typenow) ) {
    // try to pick it up from the query string
    if (!empty($_GET['post'])) {
        $post = get_post($_GET['post']);
        $typenow = $post->post_type;
    }
    // try to pick it up from the query string
    elseif ( !empty($_GET['post_type']) ) {
	    $typenow = $_GET['post_type'];
    }
    // try to pick it up from the quick edit AJAX post
    elseif (!empty($_POST['post_ID'])) {
        $post = get_post($_POST['post_ID']);
        $typenow = $post->post_type;
    }
    else {
	    $typenow = 'nopostfound';
    }
}

$typenow_array = array( 'post_type_shows', 'post_type_characters' );
if ( !in_array( $typenow , $typenow_array ) ) {
	return;
}

add_filter( 'wpseo_stopwords', '__return_empty_array' );
remove_action( 'get_sample_permalink', 'wpseo_remove_stopwords_sample_permalink', 10 );