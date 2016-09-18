<?php

/*
Plugin Name: Remove Stopwords
Plugin URI:  http://halfelf.org
Description: Removes Stopwords
Version: 2.0

Stolen from Yoast's WordPress SEO.

*/

class Stopwords_Admin {

	/**
	 * Class constructor
	 */
	function __construct() {
		add_filter( 'name_save_pre', array( $this, 'remove_stopwords_from_slug' ), 0 );
	}

	/**
	 * Cleans stopwords out of the slug, if the slug hasn't been set yet.
	 *
	 * @since 1.1.7
	 *
	 * @param string $slug if this isn't empty, the function will return an unaltered slug.
	 *
	 * @return string $clean_slug cleaned slug
	 */
	function remove_stopwords_from_slug( $slug ) {
		return $this->filter_stopwords_from_slug( $slug, filter_input( INPUT_POST, 'post_title' ) );
	}

	/**
	 * Filter the stopwords from the slug
	 *
	 * @param string $slug       The current slug, if not empty there will be done nothing.
	 * @param string $post_title The title which will be used in case of an empty slug.
	 *
	 * @return string
	 */
	public function filter_stopwords_from_slug( $slug, $post_title ) {
		// Don't change an existing slug.
		if ( isset( $slug ) && $slug !== '' ) {
			return $slug;
		}

		// When the post title is empty, just return the slug.
		if ( empty( $post_title ) ) {
			return $slug;
		}

		// Don't change the slug if this is a multisite installation and the site has been switched.
		if ( is_multisite() && ms_is_switched() ) {
			return $slug;
		}

		// Don't change slug if the post is a draft, this conflicts with polylang.
		// Doesn't work with filter_input() since need current value, not originally submitted one.
		if ( 'draft' === $_POST['post_status'] ) {
			return $slug;
		}

		// Lowercase the slug and strip slashes.
		$new_slug = sanitize_title( stripslashes( $post_title ) );

		return $this->remove_in( $new_slug );
	}

	/**
	 * Removes stop words in a slug
	 *
	 * @param string $original_slug The slug to remove stop words in.
	 *
	 * @return string
	 */
	public function remove_in( $original_slug ) {
		// Turn it to an array and strip stop words by comparing against an array of stopwords.
		$new_slug_parts = array_diff( explode( '-', $original_slug ), $this->list_stop_words() );

		// Don't change the slug if there are less than 3 words left.
		if ( count( $new_slug_parts ) < 3 ) {
			return $original_slug;
		}

		// Turn the sanitized array into a string.
		$new_slug = join( '-', $new_slug_parts );

		return $new_slug;
	}

	/**
	 * Returns a translated, filtered list of stop words
	 *
	 * @return array An array of stop words.
	 */
	public function list_stop_words() {
		/* translators: this should be an array of stop words for your language, separated by comma's. */
		$stopwords = explode( ',', __( "a,about,above,after,again,against,all,am,an,and,any,are,as,at,be,because,been,before,being,below,between,both,but,by,could,did,do,does,doing,down,during,each,few,for,from,further,had,has,have,having,he,he'd,he'll,he's,her,here,here's,hers,herself,him,himself,his,how,how's,i,i'd,i'll,i'm,i've,if,in,into,is,it,it's,its,itself,let's,me,more,most,my,myself,nor,of,on,once,only,or,other,ought,our,ours,ourselves,out,over,own,same,she,she'd,she'll,she's,should,so,some,such,than,that,that's,the,their,theirs,them,themselves,then,there,there's,these,they,they'd,they'll,they're,they've,this,those,through,to,too,under,until,up,very,was,we,we'd,we'll,we're,we've,were,what,what's,when,when's,where,where's,which,while,who,who's,whom,why,why's,with,would,you,you'd,you'll,you're,you've,your,yours,yourself,yourselves", 'wordpress-seo' ) );

		/**
		 * Allows filtering of the stop words list
		 * Especially useful for users on a language in which WPSEO is not available yet
		 * and/or users who want to turn off stop word filtering
		 *
		 * @api  array  $stopwords  Array of all lowercase stop words to check and/or remove from slug
		 */
		$stopwords = apply_filters( 'wpseo_stopwords', $stopwords );

		return $stopwords;
	}
}

global $typenow;

if ( 'all' !== $_POST['post_status'] ) {

	// when editing pages, $typenow isn't set until later!
	if (empty($typenow)) {
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
		    $typenow = 'fuck';
	    }
	}

	if ( $typenow !== ( 'post_type_shows' || 'post_type_characters' ) ) {
		new Stopwords_Admin;
	}
}