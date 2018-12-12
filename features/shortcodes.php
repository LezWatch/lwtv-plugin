<?php
/*
Description: Various shortcodes used on LezWatch.TV
Version: 2.0.0
Author: Mika Epstein
*/

class LWTV_Shortcodes {

	protected static $version;

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		add_filter( 'widget_text', 'do_shortcode' );
		self::$version = '2.0.0';
	}

	/**
	 * Init
	 */
	public function init() {
		add_shortcode( 'thismonth', array( $this, 'this_month' ) );
		add_shortcode( 'firstyear', array( $this, 'first_year' ) );
		add_shortcode( 'screener', array( $this, 'screener' ) );
		add_shortcode( 'glossary', array( $this, 'glossary' ) );
		add_shortcode( 'author-box', array( $this, 'author-box' ) );
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
		$attributes  = shortcode_atts(
			array(
				'date' => $default,
			),
			$atts
		);

		// A little sanity checking
		// If it's not a valid date, we defaiult to the time of the post
		if ( ! preg_match( '/^[0-9]{4}-[0-9]{2}$/', $attributes['date'] ) ) {
			$attributes['date'] = $default;
		}
		$datetime = DateTime::createFromFormat( 'Y-m', $attributes['date'] );
		$errors   = DateTime::getLastErrors();
		if ( ! empty( $errors['warning_count'] ) ) {
			$datetime = DateTime::createFromFormat( 'Y-m', $default );
		}

		// Regular post queerys
		$valid_post_types = array(
			'shows'      => 'post_type_shows',
			'characters' => 'post_type_characters',
		);

		foreach ( $valid_post_types as $name => $type ) {
			$post_args = array(
				'post_type'      => $type,
				'posts_per_page' => '-1',
				'orderby'        => 'date',
				'order'          => 'DESC',
				'date_query'     => array(
					array(
						'year'  => $datetime->format( 'Y' ),
						'month' => $datetime->format( 'm' ),
					),
				),
			);
			$queery    = new WP_Query( $post_args );

			$count_array[ $name ] = $queery->post_count;
			wp_reset_postdata();
		}

		// Death count
		$death_query       = LWTV_Loops::post_meta_and_tax_query( 'post_type_characters', 'lezchars_death_year', $datetime->format( 'Y' ), 'lez_cliches', 'slug', 'dead', 'REGEXP' );
		$death_list_array  = LWTV_BYQ_JSON::list_of_dead_characters( $death_query );
		$death_query_count = 0;
		foreach ( $death_list_array as $the_dead ) {
			if ( $datetime->format( 'm' ) === date( 'm', $the_dead['died'] ) ) {
				$death_query_count++;
			}
		}
		$count_array['dead'] = $death_query_count;

		$output = '<ul>';
		foreach ( $count_array as $topic => $count ) {
			$subject = ( 'dead' === $topic ) ? 'characters' : $topic;
			// translators: %s is the subject
			$type    = ( 0 === $count ) ? 'no ' . $subject : sprintf( _n( '%s ' . rtrim( $subject, 's' ), '%s ' . $subject, $count ), $count );
			$added   = ( 'dead' === $topic ) ? 'died' : 'added';
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

		$attributes = shortcode_atts(
			array(
				'title'   => 'Coming Soon',
				'summary' => 'Coming soon ...',
				'queer'   => '3',
				'worth'   => 'meh',
				'trigger' => 'none',
				'star'    => 'none',
			),
			$atts
		);

		$queer = (float) $attributes['queer'];
		$queer = ( $queer < 0 ) ? 0 : $queer;
		$queer = ( $queer > 5 ) ? 5 : $queer;

		$worth = ( in_array( $attributes['worth'], array( 'yes', 'no', 'meh', 'tbd' ), true ) ) ? $attributes['worth'] : 'meh';
		switch ( $worth ) {
			case 'yes':
				$worth_icon  = 'thumbs-up';
				$worth_color = 'success';
				break;
			case 'no':
				$worth_icon  = 'thumbs-down';
				$worth_color = 'danger';
				break;
			case 'tbd':
				$worth_icon  = 'clock-retro';
				$worth_color = 'info';
				break;
			case 'meh':
				$worth_icon  = 'meh';
				$worth_color = 'warning';
				break;
		}
		$worth_image = lwtv_yikes_symbolicons( $worth_icon . '.svg', 'fa-' . $worth_icon );

		// Get proper triger warning data
		$warning = '';
		$trigger = ( in_array( $attributes['trigger'], array( 'high', 'medium', 'low' ), true ) ) ? $attributes['trigger'] : 'none';

		if ( 'none' !== $trigger ) {
			$warn_image = lwtv_yikes_symbolicons( 'warning.svg', 'fa-exclamation-triangle' );
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

		// Get proper Star
		$stars = '';
		$star  = ( in_array( $attributes['star'], array( 'gold', 'silver', 'bronze', 'anti' ), true ) ) ? $attributes['star'] : 'none';

		if ( 'none' !== $star ) {
			$stars = '<span data-toggle="tooltip" aria-label="' . ucfirst( $star ) . ' Star Show" title="' . ucfirst( $star ) . ' Star Show"><button type="button" class="btn btn-info"><span role="img" class="screener screener-star ' . $star . '">' . lwtv_yikes_symbolicons( 'star.svg', 'fa-star' ) . '</span></button></span>';
		}

		$output = '<div class="bd-callout screener-shortcode"><h5 id="' . esc_attr( $attributes['title'] ) . '">Screener Review on <em>' . esc_html( $attributes['title'] ) . '</em></h5>
		<p>' . esc_html( $attributes['summary'] ) . '</p>
		<p><span data-toggle="tooltip" aria-label="How good is this show for queers?" title="How good is this show for queers?"><button type="button" class="btn btn-dark">Queer Score: ' . $queer . '</button></span> <span data-toggle="tooltip" aria-label="Is this show worth watching? ' . ucfirst( $worth ) . '" title="Is this show worth watching? ' . ucfirst( $worth ) . '"><button type="button" class="btn btn-' . $worth_color . '">Worth It? <span role="img" class="screener screener-worthit ' . lcfirst( $worth ) . '">' . $worth_image . '</span></button></span> ' . $warning . $stars . '</p>
		</div>';

		return $output;
	}

	/*
	 * Outputs Glossary Terms
	 *
	 * Usage: [glossary taxonomy="taxonomy slug"]
	 *
	 * Attributes:
	 *		taxonomy = taxonomy slug
	 *
	 * @since 1.0
	 */
	public static function glossary( $atts ) {
		$attr = shortcode_atts(
			array(
				'taxonomy' => '',
			),
			$atts
		);

		$the_taxonomy = sanitize_text_field( $attr['taxonomy'] );
		$the_terms    = get_terms( $the_taxonomy );

		if ( '' === $the_taxonomy || is_array( $atts['taxonomy'] ) || ! $the_terms || is_wp_error( $the_terms ) ) {
			$return = '<p><em>Invalid Taxonomy (' . esc_attr( $atts['taxonomy'] ) . ') selected.';
		} else {
			$return = '<ul class="trope-list list-group">';
			// loop over each returned trope
			foreach ( $the_terms as $term ) {
				$icon    = lwtv_yikes_symbolicons( get_term_meta( $term->term_id, 'lez_termsmeta_icon', true ) . '.svg', 'fa-square' );
				$return .= '<li class="list-group-item glossary term term-' . $term->slug . '"><a href="' . get_term_link( $term->slug, $the_taxonomy ) . '" rel="glossary term">' . $icon . '</a> <a href="' . get_term_link( $term->slug, $the_taxonomy ) . '" rel="glossary term" class="trope-link">' . $term->name . ' (' . get_term_meta( $term->term_id, 'lez_termsmeta_icon', true ) . ')</a></li>';
			}
			$return .= '</ul>';
		}

		return $return;
	}

	/*
	 * Display Author Box
	 *
	 * Usage: [author-box users=username]
	 *
	 * @since 1.2
	*/
	public static function author_box( $attributes ) {

		wp_enqueue_style( 'author-box-shortcode', content_url( 'library/assets/css/author-box.css' ), array(), self::$version );

		$mystery    = '<div class="col-sm-3"><img src="http://0.gravatar.com/avatar/9c7ddb864b01d8e47ce3414c9bbf3008?s=64&d=mm&f=y&r=g"></div><div class="col-sm"><h4 class="author_name">Mystery Girl</h4><div class="author-bio">Yet another lesbian who slept with Shane. Or Sara Lance.</div></div>';
		$author_box = '<div class="author-box-shortcode">';

		if ( '' === $attributes['users'] || ! isset( $attributes['users'] ) ) {
			$valid_user = 0;
		} else {
			$users      = explode( ',', sanitize_user( $attributes['users'] ) );
			$valid_user = 0;
			foreach ( $users as $user ) {
				if ( is_numeric( $user ) ) {
					$get_user = get_user_by( 'id', $user );
					$user     = $get_user->user_login;
				}

				$user = username_exists( sanitize_user( $user ) );
				if ( $user ) {
					$valid_user++;
					$gravatar = get_avatar( get_the_author_meta( 'email', $user ) );

					// Get author's display name
					// If display name is not available then use nickname as display name
					$display_name = ( get_the_author_meta( 'display_name', $user ) ) ? get_the_author_meta( 'display_name', $user ) : get_the_author_meta( 'nickname', $user );
					if ( get_the_author_meta( 'jobrole', $user ) ) {
						$display_name .= ' (' . get_the_author_meta( 'jobrole', $user ) . ')';
					}

					// Get author's biographical information or description
					$user_description = ( get_the_author_meta( 'user_description', $user ) ) ? get_the_author_meta( 'user_description', $user ) : '';

					// Get author's website URL
					$user_twitter = get_the_author_meta( 'twitter', $user );

					// Get link to the author archive page
					$numposts   = count_many_users_posts( array( $user ), 'post', true );
					$user_posts = $numposts[ $user ];

					// Get author Fav Shows
					$all_fav_shows = get_the_author_meta( 'lez_user_favourite_shows', $user );
					if ( '' !== $all_fav_shows ) {
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

					// Build the author box
					$author_details  = '<div class="col-sm-3">' . $gravatar . '</div>';
					$author_details .= '<div class="col-sm">';
					$author_details .= '<h4 class="author_name">' . $display_name . '</h4>';
					$author_details .= '<div class="author-bio">' . nl2br( $user_description ) . '</div>';

					$author_details .= '<div class="author-details">';

					// If the author has posts, show a link
					$author_details .= ( $user_posts > 0 ) ? '<div class="author-archives">' . lwtv_yikes_symbolicons( 'newspaper.svg', 'fa-newspaper-o' ) . '&nbsp;<a href="' . get_author_posts_url( get_the_author_meta( 'ID', $user ) ) . '">View all articles by ' . $display_name . '</a></div>' : '';

					// Add Twitter if it's there
					$author_details .= ( ! empty( $user_twitter ) ) ? '<div class="author-twitter">' . lwtv_yikes_symbolicons( 'twitter.svg', 'fa-twitter' ) . '&nbsp;<a href="https://twitter.com/' . $user_twitter . '" target="_blank" rel="nofollow">@' . $user_twitter . '</a> </div>' : '';

					// Add favourite shows if they're there
					$author_details .= ( isset( $favourites ) && ! empty( $favourites ) ) ? '<div class="author-favourites">' . lwtv_yikes_symbolicons( 'tv-hd.svg', 'fa-tv' ) . '&nbsp;Favorite ' . $fav_title . ': ' . $favourites . '</div>' : '';

					$author_details .= '</div>';
					$author_details .= '</div>';

					$author_box .= '<section class="author-box">' . $author_details . '</section>';
				}
			}
		}

		// If there was no valid user
		if ( 0 === $valid_user ) {
			$author_box .= '<section class="author-box">' . $mystery . '</section>';
		}

		$author_box .= '</div>';

		return $author_box;
	}

}
new LWTV_Shortcodes();
