<?php
/*
Plugin Name: Yoast SEO Customizations
Description: Some tweaks I have for Yoast SEO
Version: 2.1
*/

/*
 * Kill Yoast Stopwords
 *
 * Admin init function to prevent stopwords from being removed from character and show pages
 * @since 1.0
 */

add_action('admin_init','lez_kill_yoast_stopwords');
function lez_kill_yoast_stopwords() {
	global $pagenow, $typenow;

	$pagenow_array = array( 'post.php', 'edit.php', 'post-new.php' );
	if ( !in_array( $pagenow , $pagenow_array ) ) {
		return;
	}

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
}

/*
 * Extra Meta Variables
 *
 * @since 2.1
 */

// List of actors who played a character, for use on character pages
function lez_retrieve_actors_replacement( ) {
	if ( !is_array (get_post_meta( get_the_ID(), 'lezchars_actor', true)) ) {
		$actors = array( get_post_meta( get_the_ID(), 'lezchars_actor', true) );
	} else {
		$actors = get_post_meta( get_the_ID(), 'lezchars_actor', true);
	}
	return implode(", ", $actors);
}

// List of shows featuring a character, for use on character pages
function lez_retrieve_shows_replacement( ) {
	if ( !is_array (get_post_meta( get_the_ID(), 'lezchars_show', true)) ) {
		$shows_ids = array( get_post_meta( get_the_ID(), 'lezchars_show', true) );
	} else {
		$shows_ids = get_post_meta( get_the_ID(), 'lezchars_show', true);
	}

	$shows_titles = array();
	foreach ( $shows_ids as $show ) {
		$post_object = get_post( $show );
		array_push( $shows_titles, '"'. $post_object->post_title .'"' );
	}
	return implode(", ", $shows_titles);
}

// The actual replacement function
function lez_register_yoast_extra_replacements() {
	wpseo_register_var_replacement( '%%actors%%', 'lez_retrieve_actors_replacement', 'basic', 'A list of actors who played the character, separated by commas.' );
	wpseo_register_var_replacement( '%%shows%%', 'lez_retrieve_shows_replacement', 'basic', 'A list of shows the character was on, separated by commas.' );
}
add_action( 'wpseo_register_extra_replacements', 'lez_register_yoast_extra_replacements' );