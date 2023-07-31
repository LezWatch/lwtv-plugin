<?php
/*
Description: REST-API - Alexa Skills - Flash Briefing

Since the flash brief has trouble with media in post content, we've made our own
special version.

Version: 1.0
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * class LWTV_Alexa_Flash_Brief
 */
class LWTV_Alexa_Flash_Brief {

	/**
	 * Generate the Flash Briefing output
	 *
	 * @access public
	 * @return void
	 */
	public function flash_briefing() {
		// Arguments
		$queery_args = array(
			'numberposts'   => '10',
			'no_found_rows' => true,
		);

		// The Queery
		$queery = new WP_Query( $queery_args );
		if ( $queery->have_posts() ) {
			while ( $queery->have_posts() ) {
				$queery->the_post();
				$response    = array(
					'uid'            => get_the_permalink(),
					'updateDate'     => get_post_modified_time( 'Y-m-d\TH:i:s.\0\Z' ),
					'titleText'      => get_the_title(),
					'mainText'       => get_the_title() . '. ' . get_the_excerpt(),
					'redirectionUrl' => home_url(),
				);
				$responses[] = $response;
			}
			wp_reset_postdata();
		}
		if ( count( $responses ) === 1 ) {
			$responses = $responses[0];
		}
		return $responses;
	}

}

new LWTV_Alexa_Flash_Brief();
