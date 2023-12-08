<?php

namespace LWTV\Theme;

class Data_Author {
	/**
	 * Parse Author Data
	 *
	 * @access public
	 *
	 * @param  string $author Author ID
	 * @param  string $format Format of data to return
	 *
	 * @return array  Array of data to use.
	 */
	public function make( float $author, $format ) {

		$valid_data = array( 'social', 'favorite_shows' );
		if ( ! in_array( $format, $valid_data, true ) ) {
			return;
		}

		$output = self::$format( $author );

		return $output;
	}

	/**
	 * Generate author social media details
	 *
	 * @param  string $author Author ID
	 *
	 * @return string Output with social.
	 */
	public function social( $author ) {
		// Get author's Socials
		$user_socials = array(
			'bluesky'   => get_the_author_meta( 'bluesky', $author ),
			'mastodon'  => get_the_author_meta( 'mastodon', $author ),
			'instagram' => get_the_author_meta( 'instagram', $author ),
			'tiktok'    => get_the_author_meta( 'tiktok', $author ),
			'tumblr'    => get_the_author_meta( 'tumblr', $author ),
			'twitter'   => get_the_author_meta( 'twitter', $author ),
			'website'   => get_the_author_meta( 'url', $author ),
		);

		// Get all the stupid social...
		$bluesky   = ( ! empty( $user_socials['bluesky'] ) ) ? '<a href="' . $user_socials['bluesky'] . '" target="_blank" rel="nofollow">' . lwtv_plugin()->get_symbolicon( 'bluesky.svg', 'fa-square' ) . '</a>' : false;
		$instagram = ( ! empty( $user_socials['instagram'] ) ) ? '<a href="https://instagram.com/' . $user_socials['instagram'] . '" target="_blank" rel="nofollow">' . lwtv_plugin()->get_symbolicon( 'instagram.svg', 'fa-instagram' ) . '</a>' : false;
		$twitter   = ( ! empty( $user_socials['twitter'] ) ) ? '<a href="https://twitter.com/' . $user_socials['twitter'] . '" target="_blank" rel="nofollow">' . lwtv_plugin()->get_symbolicon( 'x-twitter.svg', 'fa-twitter' ) . '</a>' : false;
		$tumblr    = ( ! empty( $user_socials['tumblr'] ) ) ? '<a href="' . $user_socials['tumblr'] . '" target="_blank" rel="nofollow">' . lwtv_plugin()->get_symbolicon( 'tumblr.svg', 'fa-tumblr' ) . '</a>' : false;
		$website   = ( ! empty( $user_socials['website'] ) ) ? '<a href="' . $user_socials['website'] . '" target="_blank" rel="nofollow">' . lwtv_plugin()->get_symbolicon( 'home.svg', 'fa-home' ) . '</a>' : false;
		$mastodon  = ( ! empty( $user_socials['mastodon'] ) ) ? '<a href="' . $user_socials['mastodon'] . '" target="_blank" rel="nofollow">' . lwtv_plugin()->get_symbolicon( 'mastodon.svg', 'fa-mastodon' ) . '</a>' : false;

		// Set the array in order and remove any empty ones.
		$social_array = array( $website, $twitter, $instagram, $tumblr, $bluesky, $mastodon );
		$social_array = array_filter( $social_array );

		// Add Socials.
		$details = '<div class="author-socials">' . implode( ' ', $social_array ) . '</div>';

		return $details;
	}

	/**
	 * Generate author favorite shows.
	 *
	 * @param  string $author Author ID
	 *
	 * @return string Output fave shows.
	 */
	public function favorite_shows( $author ) {
		// Get author Fav Shows.
		$all_fav_shows = get_the_author_meta( 'lez_user_favourite_shows', $author );
		if ( '' !== $all_fav_shows && is_array( $all_fav_shows ) ) {
			$show_title = array();
			foreach ( $all_fav_shows as $each_show ) {
				if ( 'publish' !== get_post_status( $each_show ) ) {
					array_push( $show_title, '<em><span class="disabled-show-link">' . get_the_title( $each_show ) . '</span></em>' );
				} else {
					array_push( $show_title, '<em><a href="' . get_permalink( $each_show ) . '">' . get_the_title( $each_show ) . '</a></em>' );
				}
			}
			$favourites = ( empty( $show_title ) ) ? '' : implode( ', ', $show_title );
			$fav_title  = _n( 'Show', 'Shows', count( $show_title ) );
		}

		$details = ( isset( $favourites ) && ! empty( $favourites ) ) ? '<div class="author-favourites">' . lwtv_plugin()->get_symbolicon( 'tv-hd.svg', 'fa-tv' ) . '&nbsp;Favorite ' . $fav_title . ': ' . $favourites . '</div>' : '';

		return $details;
	}
}
