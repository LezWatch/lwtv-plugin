<?php
/*
Description: Various shortcodes used on LezWatchTV
Version: 1.1
Author: Mika Epstein
*/

class LWTV_Shortcodes {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		add_filter( 'widget_text', 'do_shortcode' );
	}

	/**
	 * Init
	 */
	public function init() {
		add_shortcode( 'thismonth', array( $this, 'this_month' ) );
		add_shortcode( 'firstyear', array( $this, 'first_year' ) );
		add_shortcode( 'screener', array( $this, 'screener' ) );
	}

	/*
	 * Display The first year we had queers
	 *
	 * Usage: 
	 *		[firstyear]
	 *
	 * @since 1.1
	 */
	public function first_year( $atts ) {
		return FIRST_LWTV_YEAR;
	}

	/*
	 * Display This Month recap
	 *
	 * Usage: 
	 *		[thismonth]
	 *		[thismonth date="2017-01"]
	 *
	 * @since 1.0
	 */
	public function this_month( $atts ) {
		
		$default     = get_the_date( 'Y-m' );
		$count_array = array();
		$attributes  = shortcode_atts( array(
			'date' => $default,
		), $atts );
		
		// A little sanity checking
		// If it's not a valid date, we defaiult to the time of the post
		if ( !preg_match( '/^[0-9]{4}-[0-9]{2}$/', $attributes['date'] ) ) {
			$attributes['date'] = $default;
		}
		$datetime = DateTime::createFromFormat( 'Y-m', $attributes['date'] );
		$errors   = DateTime::getLastErrors();
		if ( !empty( $errors[ 'warning_count' ] ) ) {
			$datetime = DateTime::createFromFormat( 'Y-m', $default );
		}

		// Regular post queerys
		$valid_post_types = array( 
			//'posts'      => 'post',
			'shows'      => 'post_type_shows',
			'characters' => 'post_type_characters',
			//'actors'     => 'post_type_actors',
		);

		foreach( $valid_post_types as $name => $type ) {
			$post_args = array(
				'post_type'      => $type,
				'posts_per_page' => '-1', 
				'orderby'        => 'date', 
				'order'          => 'DESC',
				'date_query' => array( 
					array( 
						'year' => $datetime->format( 'Y' ), 
						'month' => $datetime->format( 'm' ) 
					),
				),
			);
			$queery = new WP_Query( $post_args );
			
			$count_array[$name] = $queery->post_count;
			wp_reset_postdata();
		}

		// Death count
		$death_query         = LWTV_Loops::post_meta_and_tax_query( 'post_type_characters', 'lezchars_death_year', $datetime->format( 'Y' ), 'lez_cliches', 'slug', 'dead', 'REGEXP' );
		$death_list_array    = LWTV_BYQ_JSON::list_of_dead_characters( $death_query );
		$death_query_count   = 0;
		foreach ( $death_list_array as $the_dead ) {
			if ( $datetime->format( 'm' ) == date( 'm' , $the_dead['died'] ) ) {
				$death_query_count++;
			}
		}
		$count_array['dead'] = $death_query_count;

		$output = '<ul>';
		foreach ( $count_array as $topic => $count ) {
			
			$subject = ( $topic == 'dead' )? 'characters' : $topic;
			$type    = ( $count == 0 )? 'no ' . $subject : sprintf( _n( '%s ' . rtrim( $subject, 's' ), '%s ' . $subject , $count ), $count );
			$added   = ( $topic == 'dead' )? 'died' : 'added';
			
			$output .= '<li>' . $type . ' ' . $added . '</li>';
		}
		$output .= '</ul>';

		return $output;
	}


	/**
	 * Screeners.
	 * 
	 * @access public
	 * @param mixed $atts
	 * @return void
	 */
	public function screener( $atts ) {

		$attributes = shortcode_atts( array(
			'title'   => 'Coming Soon',
			'summary' => 'Coming soon ...',
			'queer'   => '3',
			'worth'   => 'meh',
			'trigger' => 'none',
		), $atts );

		$queer = (float) $attributes['queer'];
		$queer = ( $queer < 0 )? 0 : $queer;
		$queer = ( $queer > 5 )? 5 : $queer;

		$worth = ( in_array( $attributes['worth'], array( 'yes', 'no', 'meh' ) ) )? $attributes['worth'] : 'meh';
		switch ( $worth ) {
			case 'yes':
				$worth_icon = 'thumbs-up';
				$worth_color = 'success';
				break;
			case 'no':
				$worth_icon  = 'thumbs-down';
				$worth_icon  = 'danger';
				break;
			case 'meh':
				$worth_icon  = 'meh';
				$worth_icon  = 'warning';
				break;
		}
		$worth_image = lwtv_yikes_symbolicons( $worth_icon . '.svg', 'fa-' . $worth_icon );

		// Get proper triger warning data
		$warning = '';
		$trigger = ( in_array( $attributes['trigger'], array( 'high', 'medium', 'low' ) ) )? $attributes['trigger'] : 'none';

		if ( $trigger != 'none' ) {
			$warn_image    = lwtv_yikes_symbolicons( 'warning.svg', 'fa-exclamation-triangle' );
			switch ( $trigger ) {
				case 'high':
					$warn_color = 'danger';
					break;
				case 'medium':
					$warn_color = 'warning';
					break;
				case 'low':
					$warn_color = 'info';
					break;
			}

			$warning = '<span data-toggle="tooltip" aria-label="Warning - This show contains triggers" title="Warning - This show contains triggers"><button type="button" class="btn btn-' . $warn_color . '"><span class="screener screener-warn ' . $warn_color . '" role="img">' . $warn_image . '</span></button></span>';
		}

		$output = '<div class="bd-callout"><h5 id="' . esc_attr( $attributes['title'] ) . '">Screener Review on <em>' . esc_html( $attributes['title'] ) . '</em></h5>
		<p>' . esc_html( $attributes['summary'] ) . '</p>
		<p><span data-toggle="tooltip" aria-label="How good is this show for queers?" title="How good is this show for queers?"><button type="button" class="btn btn-dark">Queer Score: ' . $queer . '</button></span> <span data-toggle="tooltip" aria-label="Is this show worth watching? ' . ucfirst( $worth ) . '" title="Is this show worth watching? ' . ucfirst( $worth ) . '"><button type="button" class="btn btn-' . $worth_color . '">Worth It? <span role="img" class="screener screener-worthit ' . lcfirst( $worth ) . '">' . $worth_image . '</span></button></span> ' . $warning . '</p>
		</div>';

		return $output;

	}

}
new LWTV_Shortcodes();