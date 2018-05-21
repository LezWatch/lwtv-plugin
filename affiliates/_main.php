<?php
/**
 * Name: Affiliate Code
 * Description: Automagical affiliate things
 */


class LWTV_Affilliates {

	/**
	 * Determine what to call for actors
	 * Pick a random ad just for fun
	 */
	public function actors( $id, $type ) {
		$number = rand();

		if ($number % 2 == 0) {
			$return = self::amazon( $id, $type );
		} else {
			$return = self::cbs( $id, $type );
		}

		return $return;
	}

	/**
	 * Determine what to call for characters
	 */	
	public function characters( $id, $type ) {
		$number = rand();

		if ($number % 2 == 0) {
			$return = self::amazon( $id, $type );
		} else {
			$return = self::cbs( $id, $type );
		}

		return $return;
	}

	/**
	 * Determine what to call for shows
	 * This is much more complex!
	 */
	public function shows( $id, $type ) {

		// Default
		$return = self::amazon( $id, $type );
		
		if ( $type == 'affiliate' ) {
			$return = self::affiliate_link( $id );
		} else {

			// Get the slug (needed for Star Trek
			$slug = get_post_field( 'post_name', $id );

			// If Vimeo:
			if ( has_term( 'vimeo', 'lez_stations', $id ) ) {
				// Uncomment and fix when Vimeo approves us
				// $return = self::vimeo( $id, $type );
			}

			// If CBS (or Star Trek)
			if ( has_term( 'cbs', 'lez_stations', $id ) || has_term( 'cbs-all-access', 'lez_stations', $id ) || strpos( $slug, 'star-trek' ) !== false ) {
				$return = self::cbs( $id, $type );
			}
		}
		return $return;
	}

	/**
	 * Call Amazon Affilate Data
	 */
	function amazon( $id, $type ) {
		include_once( 'amazon.php' );
		return LWTV_Affiliate_Amazon::show_ads( $id, $type );
	}

	/**
	 * Call CBS Affilate Data
	 */
	function cbs( $id, $type ) {
		include_once( 'cbs.php' );
		return LWTV_Affiliate_CBS::show_ads( $id, $type );
	}

	/**
	 * Call Vimeo Affiliate Data
	 */
	function vimeo( $id, $type ) {
		include_once( 'vimeo.php' );
		return LWTV_Affiliate_Vimeo::show_ads( $id, $type );
	}

	/**
	 * Call Custom Affiliate Links
	 * This is used by shows to figure out where people can watch things
	 */
	function affiliate_link( $id ) {

		$aff_type = get_post_meta( $id, 'lezshows_affiliate', true );
		$aff_url  = get_post_meta( $id, 'lezshows_affiliateurl', true );

		// If the type is a URL but URL is empty, bail
		if ( $aff_type == 'url' && empty( $aff_url ) ) return;

		$data = array( 'link' => '', 'text' => 'Watch online now', 'img'  => '', );

		switch( $aff_type ) {
			case "amazon":
				$data = self::amazon( $id, 'text' );
				break;
			case "apple":
				$data['link'] = 'https://www.apple.com/itunes/charts/tv-shows/';
				$data['text'] = 'Watch TV on iTunes';
				break;
			case "cbs":
				$data = self::cbs( $id, 'text' );
				break;
			case "vimeo":
				$data['link'] = 'https://join.vimeo.com';
				$data['text'] = 'Watch on Vimeo - Buy Directly from Creators';
				break;
			case "youtube":
				$data['link'] = 'https://www.youtube.com/red';
				$data['text'] = 'Watch Ad Free on YouTube Red';
				break;
			case "url":
				$data['link'] = $aff_url;
				break;
		}

		$icon   = lwtv_yikes_symbolicons( 'tv-hd.svg', 'fa-tv' );
		$output = '<a target="_blank" href="' . esc_url( $data['link'] ) . '"><button type="button" class="btn btn-info btn-lg btn-block">' . $icon . $data['text'] . $data['img'] . '</button></a>';

		return $output;

	}

}
new LWTV_Affilliates();