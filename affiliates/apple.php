<?php
/**
 * Name: Affiliate Code for Apple iTunes
 * Description: Automagical affiliate things
 */


class LWTV_Affiliate_Apple {

	/**
	 * Determine which ads to show
	 */
	public static function show_ads( $post_id, $type ) {

		// Return the proper output
		switch ( $type ) {
			case 'widget':
			default:
				$return = self::output_widget( $post_id );
				break;
		}
		return $return;
	}

	/**
	 * Search!
	 */
	public static function search( $post_id ) {

		$use_fallback = false;

		// Build the URL
		$slug = get_post_field( 'post_name', $post_id );
		$term = str_replace( '-', '+', $slug );
		$url  = 'http://ax.itunes.apple.com/WebObjects/MZStoreServices.woa/wa/wsSearch?media=tvShow&entity=tvSeason&limit=1&term=' . $term;

		$request = wp_remote_get( $url );

		if ( is_wp_error( $request ) ) {
			$use_fallback = true;
		} else {
			$body = json_decode( wp_remote_retrieve_body( $request ), true );

			// If the result isn't 1, we have an issue....
			if ( isset( $body ) && 1 !== $body['resultCount'] ) {
				$use_fallback = true;
			} else {
				// We found a show! Let's show it, eh?
				$return = $body['results'][0]['collectionId'];
			}
		}

		if ( $use_fallback ) {
			$return = 'fallback';
		}

		return $return;

	}

	/**
	 * Output Widget
	 */
	public static function output_widget( $post_id ) {

		$link = 'fallback';
		if ( 'post_type_shows' === get_post_type( $post_id ) ) {
			$link = self::search( $post_id );
		}

		if ( 'fallback' !== $link || is_null( $link ) ) {
			$the_ad = '<iframe src="https://widgets.itunes.apple.com/widget.html?c=us&brc=FFFFFF&blc=FFFFFF&trc=FFFFFF&tlc=ffffff&d=&t=&m=tvSeason&e=tvSeason&w=250&h=300&ids=' . $link . '&wt=discovery&partnerId=&affiliate_id=&at=1010lMaT&ct=" frameborder=0 style="overflow-x:hidden;overflow-y:hidden;width:250px;height: 300px;border:0px"></iframe>';
		} else {
			$the_ad = self::random( $post_id );
		}

		$return = '<center>' . $the_ad . '</center>';

		return $return;
	}

	/**
	 * Something random...
	 */
	public static function random( $post_id ) {

		$number = wp_rand();
		if ( 0 === $number % 3 ) {
			$the_ad = self::podcasts();
		} elseif ( 0 === $number % 2 ) {
			require_once 'amazon.php';
			$the_ad = LWTV_Affiliate_Amazon::bounty( $post_id );
		} else {
			$the_ad = '<iframe src="https://widgets.itunes.apple.com/widget.html?c=us&brc=FFFFFF&blc=FFFFFF&trc=FFFFFF&tlc=FFFFFF&d=&t=&m=tvSeason&e=tvSeason&w=250&h=300&ids=&wt=search&partnerId=&affiliate_id=&at=1010lMaT&ct=" frameborder=0 style="overflow-x:hidden;overflow-y:hidden;width:250px;height: 300px;border:0px"></iframe>';
		}

		return $the_ad;
	}

	/**
	 * Pick a podcast....
	 */
	public static function podcasts() {
		$podcasts = array(
			'LezRepresent'    => '1308112009',
			'Queery'          => '1268343859',
			'Buffering'       => '1150241800',
			'LezHangOut'      => '1296938673',
			'LesbianTalkShow' => '1078936307',
			'StrangeFruit'    => '579402864',
			'ButchTalk'       => '1203354302',
			'Nancy'           => '1222041050',
			'AngelOnTop'      => '1383009934',
			'TalesBlackBadge' => '1097564705',
			'HistoryIsGay'    => '1327642994',
			'QueerAsFiction'  => '1355999869',
			'GalPalsWatch'    => '1412048173',
		);

		// Randomize the Podcast
		$podcast = $podcasts[ array_rand( $podcasts ) ];

		$return = '<iframe src="//banners.itunes.apple.com/banner.html?partnerId=&aId=1010lMaT&bt=catalog&t=smart_color&id=' . $podcast . '&c=us&l=en-US&w=300&h=250&store=podcast" frameborder=0 style="overflow-x:hidden;overflow-y:hidden;width:300px;height:250px;border:0px"></iframe>';

		return $return;

	}

}

new LWTV_Affiliate_Apple();
