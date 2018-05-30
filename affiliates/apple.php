<?php
/**
 * Name: Affiliate Code for Apple iTunes
 * Description: Automagical affiliate things
 */


class LWTV_Affiliate_Apple {
	function show_ads( $post_id, $type ) {

		// Return the proper output
		switch ( $type ) {
			case "text":
				return self::output_text( $post_id );
			case "widget":
			default:
				return self::output_widget( $post_id );
				break;
		}
	}

	/**
	 * Figure out which ad we really want to show....
	 */
	function output_widget( $post_id ) {

		// Generic TV search
		$tv_search   = '<iframe src="https://widgets.itunes.apple.com/widget.html?c=us&brc=FFFFFF&blc=FFFFFF&trc=FFFFFF&tlc=FFFFFF&d=&t=&m=tvSeason&e=tvSeason&w=250&h=300&ids=&wt=search&partnerId=&affiliate_id=&at=1010lMaT&ct=" frameborder=0 style="overflow-x:hidden;overflow-y:hidden;width:250px;height: 300px;border:0px"></iframe>';

		// Apple doesn't actually have any shows yet. I know.
		$slug        = get_post_field( 'post_name', $post_id );
		$named_array = array( 
			'steven-universe' => '714962395',
			'jane-the-virgin' => '912482494',
			'orphan-black'    => '611208235',
			'wynonna-earp'    => '1079965614',
			'the-bold-type'   => '1244274935',
			'take-my-wife'    => '1341038061',
			'killing-eve'     => '1357736773',
			'queen-sugar'     => '1150166775',
			'station-19'      => '1348873499',
		);

		if ( array_key_exists( $slug, $named_array ) ) {
			$link   = $named_array[$slug];
			$the_ad = '<iframe src="https://widgets.itunes.apple.com/widget.html?c=us&brc=FFFFFF&blc=FFFFFF&trc=FFFFFF&tlc=ffffff&d=&t=&m=tvSeason&e=tvSeason&w=250&h=300&ids=' . $link . '&wt=discovery&partnerId=&affiliate_id=&at=1010lMaT&ct=" frameborder=0 style="overflow-x:hidden;overflow-y:hidden;width:250px;height: 300px;border:0px"></iframe>';
		} else {
			$number = rand();
			if ( $number % 2 == 0 ) {
				$the_ad = self::podcasts();
			} else {
				$the_ad = $tv_search;
			}
		}

		$return = '<center>' . $the_ad . '</center>';

		return $return;

	}


	/**
	 * Pick a random podcast....
	 * 
	 * @access public
	 * @return void
	 */
	function podcasts() {
		$podcasts = array(
			'LezRepresent'    => '1308112009',
			'Queery'          => '1268343859',
			'HTLPodcast'      => '1254294886',
			'Buffering'       => '1150241800',
			'LezHangOut'      => '1296938673',
			'LesbianTalkShow' => '1078936307',
			'StrangeFruit'    => '579402864',
			'ButchTalk'       => '1203354302',
		);

		$podcast = $podcasts[array_rand( $podcasts )];

		$return = '<iframe src="//banners.itunes.apple.com/banner.html?partnerId=&aId=1010lMaT&bt=catalog&t=smart_color&id=' . $podcast . '&c=us&l=en-US&w=300&h=250&store=podcast" frameborder=0 style="overflow-x:hidden;overflow-y:hidden;width:300px;height:250px;border:0px"></iframe>';
		
		return $return;

	}

}

new LWTV_Affiliate_Apple();
