<?php
/*
Description: REST-API - Alexa Skills - This Year

Gives you an idea how this year is going...

Version: 1.0
*/

if ( ! defined('WPINC' ) ) die;

/**
 * class LWTV_Alexa_This_Year
 */
class LWTV_Alexa_This_Year {

	public function year( $year = date('Y') ) {

		$count_array = array();

		// Calculate dead:
		include_once( 'alexa/byq.php' );
		$count_array['dead'] = LWTV_Alexa_BYQ::how_many( 'year', $year );
		$dead = 'Miraculously, no characters died! I\'m surprised too.';
			if ( $count_array['dead'] > '0' ) {
				$dead = sprintf( _n( '%s character died', '%s characters died', $count_array['dead'] ), $count_array['dead'] );
			}
		// Now to personalize it...
		if ( $count_array['dead'] > 20 ) {
			$dead = 'Disturbingly, ' . $dead;
		} elseif ( $count_array['dead'] > 30 ) {
			$dead = 'Distressingly, ' . $dead;
		} elseif ( $count_array['dead'] > 40 ) {
			$dead = 'Horrifyingly, ' . $dead;
		}

		// Calculate characters and shows
		$valid_post_types = array( 
			'shows'      => 'post_type_shows',
			'characters' => 'post_type_characters',
			'actors'     => 'post_type_actors',
		);

		foreach( $valid_post_types as $name => $type ) {
			$post_args = array(
				'post_type'      => $type,
				'posts_per_page' => '-1', 
				'orderby'        => 'date', 
				'order'          => 'DESC',
				'date_query' => array( 
					array( 
						'year' => $year, 
					),
				),
			);
			$queery = new WP_Query( $post_args );
			
			$count_array[$name] = count ( $queery );
			wp_reset_postdata();
		}

		// Conclusion
		$output = 'In ' . $year . ' we have added ' . $chars . ' characters and ' . $shows . ' shows. ' . $dead;

		return $output;
	}

}

new LWTV_Alexa_This_Year();