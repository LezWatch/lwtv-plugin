<?php
/*
Description: REST-API - Alexa Skills - Newest

Generate the newest shows or characters (or deaths)

Version: 1.0
*/

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * class LWTV_Alexa_Newest
 */
class LWTV_Alexa_Newest {

	/**
	 * What's New Overall
	 *
	 * @access public
	 * @return string
	 */
	public function whats_new() {
		$character = self::latest( 'character' );
		$show      = self::latest( 'show' );
		$death     = self::latest( 'death' );
		$post      = self::latest( 'post' );

		$output = 'The latest character added to LezWatch T. V. was ' . $character['name'] . '. The latest show added was ' . $show['name'] . '. And the latest character who died was ' . $death . '. Our latest post was ' . $post['name'] . ' -- ';

		$output .= 'For more information on the shows or characters, you can ask me "Tell me about the show ' . $show['name'] . '." or "Tell me about the character ' . $character['name'] . '".';

		return $output;
	}

	/**
	 * The latest whatever we're going to use.
	 * @param  string $posttype [actor|character\show\post]
	 * @return string           Name of the latest
	 */
	public function latest( $posttype ) {

		if ( 'death' === $posttype ) {
			$data   = ( new LWTV_BYQ_JSON() )->last_death();
			$name   = $data['name'];
			$output = $name . ' on ' . gmdate( 'F j, Y', $data['died'] );
		} else {
			$data          = array();
			$get_post_type = ( 'post' === $posttype ) ? 'post' : 'post_type_' . $posttype . 's';

			$post_args = array(
				'post_type'      => $get_post_type,
				'posts_per_page' => '1',
				'orderby'        => 'date',
				'order'          => 'DESC',
				'no_found_rows'  => true,
			);

			$queery = new WP_Query( $post_args );

			while ( $queery->have_posts() ) {
				$queery->the_post();
				$id           = get_the_ID();
				$data['name'] = get_the_title( $id );
				$data['date'] = get_the_date( 'l F j, Y', $id );
			}
			wp_reset_postdata();

			$output = array(
				'name' => $data['name'],
				'date' => $data['date'],
			);
		}

		return $output;
	}
}

new LWTV_Alexa_Newest();
