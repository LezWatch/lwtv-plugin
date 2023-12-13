<?php
/**
 * Name: Of the Day
 *
 */

namespace LWTV\_Components;

class Of_The_Day implements Component, Templater {

	/**
	 * Initialize
	 */
	public function init() {
		add_filter( 'feed_content_type', array( $this, 'feed_content_type' ), 10, 2 );
		add_action( 'init', array( $this, 'call_add_feed' ) );
	}

	/**
	 * Required steps.
	 * Build out table if it doesn't exist.
	 */
	public function __construct() {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		global $wpdb;

		$this_table_name = $wpdb->prefix . 'lwtv_otd';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $this_table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			post_datetime datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			created date DEFAULT '0000-00-00' NOT NULL,
			posts_id bigint(20) NOT NULL,
			posts_type text NOT NULL,
			content text NOT NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";

		// Make sure our table exists.
		maybe_create_table( $this_table_name, $sql );
	}

	/**
	 * Gets tags to expose as methods accessible through `lwtv_plugin()`.
	 *
	 * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
	 *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
	 *               adding support for further arguments in the future.
	 */
	public function get_template_tags(): array {
		return array(
			'get_wp_version'         => array( $this, 'get_wp_version' ),
			'get_rss_otd_last_build' => array( $this, 'get_rss_otd_last_build' ),
			'get_rss_otd_feed'       => array( $this, 'get_rss_otd_feed' ),
			'set_of_the_day'         => array( $this, 'set_of_the_day' ),
		);
	}

	/**
	 * Call the add to feed.
	 */
	public function call_add_feed() {
		add_feed( 'otd', array( $this, 'add_otd_feed' ) );
	}

	/**
	 * Add new feed.
	 */
	public function add_otd_feed() {
		get_template_part( 'rss', 'otd' );
	}

	/**
	 * Return the current version of WP.
	 */
	public function get_wp_version() {
		global $wp_version;

		return $wp_version;
	}

	/**
	 * Set the OTD values and add to the DB
	 *
	 * Called by CRON
	 */
	public function set_of_the_day( $type ) {
		global $wpdb;

		$valid_types = array( 'character', 'show' );
		$date        = current_time( 'Y-m-d' );
		$table       = $wpdb->prefix . 'lwtv_otd';

		$types = $valid_types;
		if ( in_array( $type, $valid_types, true ) ) {
			$types = array( $type );
		}

		foreach ( $types as $a_type ) {
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$queery = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `{$table}` WHERE posts_type = %s AND created = %s", $a_type, $date ) );

			// If there's NO entry, we can make one.
			if ( 0 === $queery || empty( $queery ) ) {
				$of_the_day = $this->of_the_day( $a_type, 'default' );
				$this->add_to_table( $a_type, $of_the_day );
			}
		}
	}

	/**
	 * Add the OTD to the table
	 *
	 * @param string $type type of content
	 * @param array  $data OTD array
	 */
	public function add_to_table( $type, $data ) {
		global $wpdb;

		$table = $wpdb->prefix . 'lwtv_otd';

		// table: UID | DATE | POST ID | TYPE | CONTENT
		$date    = current_time( 'Y-m-d' );
		$content = 'The LezWatch.TV ' . $type . ' of the day is';

		// Build the content by type
		switch ( $type ) {
			case 'character':
				// NAME from SHOWS - #LWTVcotd HASHTAG URL
				$content .= ' ' . $data['name'] . ' from ' . $data['shows'] . ' - #LWTVcotD';
				break;
			case 'show':
				// NAME, with CHARS characters and an overall score of SCORE - #LWTVsotd HASHTAG URL
				$content .= ' "' . $data['name'] . '," with ' . $data['characters'] . ' characters and an overall score of ' . $data['score'] . '. - #LWTVsotd';
				break;
		}

		$content .= ' ' . $data['hashtag'] . ' - ' . $data['url'];

		$array = array(
			'created'       => $date,
			'post_datetime' => current_time( 'mysql' ),
			'posts_id'      => (int) $data['pid'],
			'posts_type'    => $type,
			'content'       => $content,
		);

		// Add to the DB
		$wpdb->insert(
			$table,
			$array
		);
	}

	/*
	 * Of the Day function.
	 *
	 * @access public
	 * @param string $type   Type of content.
	 * @param string $format Type of output
	 */
	public function of_the_day( $type = 'character', $format = 'default' ) {

		// Valid types of 'of the day'.
		// If there's no known type, we'll assume character
		$valid_types = array( 'birthday', 'character', 'show', 'death' );
		$type        = ( ! in_array( $type, $valid_types, true ) ) ? 'character' : $type;

		// Valid types of 'format'
		// If there's no known format, we'll assume character
		$valid_format = array( 'default', 'tweet', 'json', 'table' );
		$format       = ( ! in_array( $format, $valid_format, true ) ) ? 'default' : $format;

		// Create the date with regards to timezones
		$tz        = 'America/New_York';
		$timestamp = time();
		$dt        = new \DateTime( 'now', new \DateTimeZone( $tz ) ); //first argument "must" be a string
		$dt->setTimestamp( $timestamp ); //adjust the object to correct timestamp
		$date = $dt->format( 'm-d' );

		// Create the array
		switch ( $type ) {
			case 'death':
				$of_the_day_array = lwtv_plugin()->on_this_day( $date, $format );
				break;
			case 'birthday':
				$of_the_day_array = self::birthday( $date, $format );
				break;
			case 'character':
			case 'show':
				$of_the_day_array = self::character_show( $date, $type, $format );
				break;
			default:
				$of_the_day_array = '';
				break;
		}

		if ( empty( $of_the_day_array ) ) {
			return new \WP_Error( 'no_type', 'Invalid content type given.', array( 'status' => 400 ) );
		}

		// No errors! Return array
		return $of_the_day_array;
	}

	/**
	 * character_show function.
	 *
	 * @access public
	 * @param string $date   (default: '')
	 * @param string $type   (default: 'character')
	 * @param string $format (default: 'format')
	 * @return array
	 */
	// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
	public function character_show( $date = '', $type = 'character', $format = 'default' ) {

		// Defaults...
		$return = array();

		// Grab the options
		$default = array(
			'character' => array(
				'time' => strtotime( 'tomorrow 01:00' ),
				'post' => 'none',
			),
			'show'      => array(
				'time' => strtotime( 'tomorrow 01:00' ),
				'post' => 'none',
			),
		);
		$options = get_option( 'lwtv_otd', $default );

		// If there's no ID or the timestamp has past, we need a new ID
		// Or if we're in dev mode.
		if ( 'none' === $options[ $type ]['post'] || time() >= $options[ $type ]['time'] || ( defined( 'LWTV_DEV_SITE' ) && LWTV_DEV_SITE ) ) {
			// Get the show ID
			$id = self::find_char_show( $type, $date );

			// Update the options
			$options[ $type ]['post'] = $id;
			$options[ $type ]['time'] = strtotime( 'midnight tomorrow' );
			update_option( 'lwtv_otd', $options );

			// Set post_meta for the next available use (+4 months from now)
			update_post_meta( $id, 'lwtv_of_the_day', strtotime( '+4 months' ) );
		}

		$post_id = $options[ $type ]['post'];
		$image   = ( has_post_thumbnail( $post_id ) ) ? get_the_post_thumbnail_url( $post_id, 'full' ) : get_site_icon_url();

		// Build the Base Array:
		$return = array(
			'id'    => $post_id,
			'pid'   => $post_id,
			'name'  => get_the_title( $post_id ),
			'url'   => get_the_permalink( $post_id ),
			'image' => $image,
		);

		// Add custom array items based on type
		switch ( $type ) {
			case 'character':
				$all_shows   = get_post_meta( $post_id, 'lezchars_show_group', true );
				$shows_value = isset( $all_shows[0] ) ? $all_shows[0] : '';

				// Set Hashtag
				if ( ! empty( $shows_value ) ) {
					$num_shows = count( $all_shows );
					$showsmore = ( $num_shows > 1 ) ? ' (plus ' . ( $num_shows - 1 ) . ' more)' : '';
					$show_post = get_post( $shows_value['show'] );
					$show_name = trim( preg_replace( '~\([^)]+\)~', '', $show_post->post_title ) ); // Remove the (2018) from some shows, using ⌘ as delimiter because shows have all sorts of characters.
					$show_name = str_replace( ' & ', ' and ', $show_name );
					$show_name = sanitize_title( $show_name );
					$hashtag   = '#' . implode( '', array_map( 'ucfirst', explode( '-', $show_name ) ) ) . ' ' . $showsmore;
				}
				// Set all shows but we only use the one because Sara Lance.
				if ( '' !== $all_shows && ! empty( $shows_value ) ) {
					$show_titles = array();
					foreach ( $all_shows as $each_show ) {
						// Remove the nested Array.
						if ( is_array( $each_show['show'] ) ) {
							$each_show['show'] = $each_show['show'][0];
						}
						array_push( $show_titles, get_the_title( $each_show['show'] ) );
					}
				}

				// This shouldn't happen but it did.
				if ( '#HelloWorld' === $hashtag ) {
					$hashtag = '';
				}

				$return['status']  = ( has_term( 'dead', 'lez_cliches', $post_id ) ) ? 'dead' : 'alive';
				$return['shows']   = ( empty( $show_titles ) ) ? 'n/a' : implode( ', ', $show_titles );
				$return['hashtag'] = $hashtag;
				break;
			case 'show':
				$return['loved']      = ( get_post_meta( $post_id, 'lezshows_worthit_show_we_love', true ) ) ? 'yes' : 'no';
				$return['score']      = number_format( (float) get_post_meta( $post_id, 'lezshows_the_score', true ), 2, '.', '' );
				$return['characters'] = get_post_meta( $post_id, 'lezshows_char_count', true );

				// We need to do some crazy generation here
				$post_data = get_post( $post_id );
				// Remove the (2018) from some shows, using ⌘ as delimiter because shows have all sorts of characters but ONLY if they have a space.
				$show_name = trim( preg_replace( '~\([^)]+\)~', '', $post_data->post_title ) );
				// change & to and for "WillAndGrace" or "LawAndOrder"
				$show_name = str_replace( ' & ', ' and ', $show_name );
				// change @ to a for "tagged"
				$show_name = str_replace( '@', 'a', $show_name );
				$show_name = sanitize_title( $show_name );

				// Hashtag
				$return['hashtag'] = '#' . implode( '', array_map( 'ucfirst', explode( '-', $show_name ) ) );
				break;
		}

		return $return;
	}

	/**
	 * Let's find something valid...
	 * @param  string $type [character|show]
	 * @return number $id   [ID of the show or character]
	 */
	public function find_char_show( $type = 'character', $date = '' ) {

		// phpcs:disable
		add_filter( 'facetwp_is_main_query', function( $is_main_query, $query ) {
			return false;
		}, 10, 2 );
		// phpcs:enable

		$meta_query_array = '';
		$tax_query_array  = '';

		switch ( $type ) {
			case 'character':
				if ( '' === $date ) {
					// Create the date with regards to timezones
					$tz        = 'America/New_York';
					$timestamp = time();
					$dt        = new \DateTime( 'now', new \DateTimeZone( $tz ) ); //first argument "must" be a string
					$dt->setTimestamp( $timestamp ); //adjust the object to correct timestamp
					$date = $dt->format( 'm-d' );
				}

				$mystery_array    = array( 10250, 11066, 79739, 87052 );
				$meta_query_array = array(
					array(
						'key'     => '_thumbnail_id',
						'value'   => $mystery_array, // Don't show if the image is Mystery woman.
						'compare' => 'NOT IN',
					),
					array(
						'key'     => '_thumbnail_id', // Images are required
						'compare' => 'EXISTS',
					),
					array(
						'key'     => 'lezchars_show_group',
						'value'   => 're', // Only show REgulars or REcurring, but not guest.
						'compare' => 'LIKE',
					),
					array(
						'key'     => 'lezchars_cliches',
						'field'   => 'slug',
						'terms'   => array( 'conditional-queerness', 'phase' ), // Don't show conditionally queers, or just-a-phasers.
						'compare' => 'NOT IN',
					),
				);
				$tax_query_array  = self::character_awareness( $date );
				break;
			case 'show':
				$meta_query_array = array(
					array(
						'key'     => 'lezshows_the_score',
						'value'   => '50', // Shows with a score over 50.
						'compare' => '>=',
					),
					array(
						'key'     => 'lezshows_worthit_rating',
						'value'   => 'e', // yEs or mEh, but not NO or TBD
						'compare' => 'LIKE',
					),
				);
				break;
		}

		// Grab a random post
		$valid_post = false;

		while ( ! $valid_post ) {
			$args = array(
				'post_type'      => 'post_type_' . $type . 's',
				'orderby'        => 'rand',
				'posts_per_page' => '1',
				's'              => '-TBD', // Excluding posts with "TBD" as the content
				'tax_query'      => $tax_query_array,
				'meta_query'     => $meta_query_array,
				'no_found_rows'  => true,
			);
			$post = new \WP_Query( $args );

			// Do the needful
			if ( $post && $post->have_posts() ) {
				while ( $post->have_posts() ) {
					$post->the_post();
					$id = get_the_ID();
				}
				wp_reset_postdata();
			}

			switch ( $type ) {
				case 'character':
					// if the character is a cartoon, they MUST be a regular.
					$is_toon = ( has_term( 'cartoon', 'lez_cliches', $id ) ) ? true : false;
					// phpcs:ignore WordPress.PHP.StrictInArray
					$is_regu = ( in_array( 'regular', get_post_meta( $id, 'lezchars_show_group', true ) ) ) ? true : false;
					if ( ! $is_toon || ( $is_toon && $is_regu ) ) {
						$valid_post = true;
					}
					break;
				case 'show':
					// All shows have to have at least one regular character
					$role_data = get_post_meta( $id, 'lezshows_char_roles', true );
					if ( isset( $role_data['regular'] ) && 0 !== $role_data['regular'] ) {
						$valid_post = true;
					}
					break;
				default:
					$valid_post = true;
					break;
			}

			// If the time (now) is less than or equal to the last used AND it's
			// not empty, then it's not a valid post.
			// If it's not set at all, then we've never used it.
			$last_used = get_post_meta( $id, 'lwtv_of_the_day', true );
			if ( isset( $last_used ) && time() <= $last_used ) {
				$valid_post = false;
			}
		}

		return $id;
	}

	/**
	 * Character Awareness Days
	 *
	 * On visibility/awareness days, only show characters that are those things.
	 *
	 * @param mixed $date
	 * @return array()
	 */
	public function character_awareness( $date = '' ) {

		$return = '';

		if ( '' === $date ) {
			// Create the date with regards to timezones
			$tz        = 'America/New_York';
			$timestamp = time();
			$dt        = new \DateTime( 'now', new \DateTimeZone( $tz ) ); //first argument "must" be a string
			$dt->setTimestamp( $timestamp ); //adjust the object to correct timestamp
			$date = $dt->format( 'm-d' );
		}

		// Missing things:
		// Asexual Awareness Week - it's in October
		// Bisexual Awareness Week - it's the week the DAY happens

		switch ( $date ) {
			case '03-31': // Transgender Day of Visibility
			case '11-20': // Transgender Day of Remembrance
				$return = array(
					array(
						'taxonomy' => 'lez_gender',
						'field'    => 'slug',
						'terms'    => array( 'trans-man', 'trans-woman' ),
					),
				);
				break;
			case '04-26': // Lesbian Visibility Day
			case '10-08': // International Lesbian Day
				$return = array(
					array(
						'taxonomy' => 'lez_sexuality',
						'field'    => 'slug',
						'terms'    => array( 'homosexual' ),
					),
					array(
						'taxonomy' => 'lez_gender',
						'field'    => 'slug',
						'terms'    => array( 'cisgender', 'trans-woman' ),
					),
				);
				break;
			case '05-24': // Pansexual Day of Visibility
			case '12-08': // Pansexual Pride Day
				$return = array(
					array(
						'taxonomy' => 'lez_sexuality',
						'field'    => 'slug',
						'terms'    => array( 'pansexual' ),
					),
					array(
						'taxonomy' => 'lez_gender',
						'field'    => 'slug',
						'terms'    => array( 'cisgender', 'trans-woman' ),
					),
				);
				break;
			case '07-14': // Non-Binary Day
				$return = array(
					array(
						'taxonomy' => 'lez_gender',
						'field'    => 'slug',
						'terms'    => array( 'non-binary' ),
					),
				);
				break;
			case '09-23': // Celebrate Bisexuality Day
				$return = array(
					array(
						'taxonomy' => 'lez_sexuality',
						'field'    => 'slug',
						'terms'    => array( 'bisexual' ),
					),
					array(
						'taxonomy' => 'lez_gender',
						'field'    => 'slug',
						'terms'    => array( 'cisgender', 'trans-woman' ),
					),
				);
				break;
			case '10-26': // Intersex Awareness Day
			case '11-08': // Intersex Day of Remembrance
				$return = array(
					array(
						'taxonomy' => 'lez_gender',
						'field'    => 'slug',
						'terms'    => array( 'intersex' ),
					),
				);
				break;
		}

		return $return;
	}

	/**
	 * You say it's your birthday!
	 *
	 * @param  string $date   [description]
	 * @param  string $format [description]
	 * @return [type]         [description]
	 */
	public function birthday( $date = '', $format = 'default' ) {

		// Get all our birthdays
		$actor_loop = lwtv_plugin()->queery_post_meta( 'post_type_actors', 'lezactors_birth', $date, 'LIKE' );

		if ( is_object( $actor_loop ) && $actor_loop->have_posts() ) {
			foreach ( $actor_loop->posts as $actor ) {

				// Get the post slug
				$post_slug = get_post_field( 'post_name', get_post( $actor ) );

				// Calculate Age
				$age_end = new \DateTime();
				if ( get_post_meta( $actor->ID, 'lezactors_death', true ) ) {
					$age_end = new \DateTime( get_post_meta( $actor->ID, 'lezactors_death', true ) );
				}
				if ( get_post_meta( $actor->ID, 'lezactors_birth', true ) ) {
					$age_start = new \DateTime( get_post_meta( $actor->ID, 'lezactors_birth', true ) );
				}
				if ( isset( $age_start ) ) {
					$alive = $age_start->diff( $age_end );
				}

				// Their age is ...
				$age = $alive->format( '%Y' );

				// Setup the WordPress name (used by LWTV News)
				$wordpress_name = '<a href="' . get_permalink( $actor ) . '">' . get_the_title( $actor ) . ' (' . $age . ')</a>';

				// If they have a Twitter handle, use that ; Else use their name
				$hashtag_name = '#' . implode( '', array_map( 'ucfirst', explode( '-', $actor->post_name ) ) );

				// Add to array:
				$twitter_array[ $post_slug ]   = $hashtag_name . ' (' . $age . ')';
				$wordpress_array[ $post_slug ] = $wordpress_name;

			}

			switch ( $format ) {
				case 'tweet':
					$birthdays = implode( ', ', $twitter_array );
					break;
				default:
					$birthdays = '<p>A very happy birthday to:</p><ul><li>' . implode( '</li><li>', $wordpress_array ) . '</li></ul>';
			}
		} else {
			// If no one has a birthday, whomp whomp
			switch ( $format ) {
				case 'tweet':
					$birthdays = false;
					break;
				default:
					$birthdays = '<p>No one has a birthday today. Who knew?</p>';
			}
		}

		$return = array(
			'date'      => $date,
			'birthdays' => $birthdays,
		);

		return $return;
	}
	/**
	 * Generate last-build
	 *
	 * This needs to be based on the last entry we added to the table.
	 */
	public function get_rss_otd_last_build() {
		global $wpdb;

		$table = $wpdb->prefix . 'lwtv_otd';
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$table_data = $wpdb->get_results( "SELECT * FROM {$table} order by id desc limit 1", ARRAY_A );

		return $table_data[0]['post_datetime'];
	}

	/**
	 * Limit the actions to ONLY this feed.
	 */
	public function feed_content_type( $content_type, $type ) {
		if ( 'otd' === $type ) {
			add_filter( 'wp_title_rss', array( $this, 'rss_title' ), 20, 1 );
			$content_type = 'application/rss+xml';
		}
		return $content_type;
	}

	/**
	 * Customize RSS title.
	 */
	public function rss_title() {
		$rss_title = 'LezWatch.TV Of The Day - Feed';
		return $rss_title;
	}

	/**
	 * Customize RSS Item
	 *
	 * Adds Enclosure to RSS if it exists.
	 */
	public function customize_rss_item() {
		if ( ! has_post_thumbnail() ) {
			return;
		}

		$thumbnail_size = apply_filters( 'rss_enclosure_image_size', 'large' );
		$thumbnail_id   = get_post_thumbnail_id( get_the_ID() );
		$thumbnail      = image_get_intermediate_size( $thumbnail_id, $thumbnail_size );

		if ( empty( $thumbnail ) ) {
			return;
		}

		$upload_dir = wp_upload_dir();

		printf(
			'<enclosure url="%s" length="%s" type="%s" />',
			esc_url( $thumbnail['url'] ),
			esc_html( filesize( path_join( $upload_dir['basedir'], $thumbnail['path'] ) ) ),
			esc_html( get_post_mime_type( $thumbnail_id ) )
		);
	}

	/**
	 * Build RSS feed.
	 */
	public function get_rss_otd_feed() {

		global $wpdb;

		$table = $wpdb->prefix . 'lwtv_otd';
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$table_data = $wpdb->get_results( "SELECT * FROM {$table} order by id desc limit 10" );

		foreach ( $table_data as $use_data ) {
			?>
			<item>
				<title><?php echo esc_html( ucfirst( $use_data->posts_type ) ); ?> of the Day: <?php echo esc_html( get_the_title( $use_data->posts_id ) ); ?></title>
				<link><?php echo esc_url( get_permalink( $use_data->posts_id ) ); ?></link>
				<pubDate><?php echo esc_html( mysql2date( 'D, d M Y H:i:s +0000', $use_data->post_datetime ) ); ?></pubDate>
				<dc:creator>LezWatch.TV</dc:creator>
				<guid isPermaLink="false"><?php the_guid( $use_data->posts_id ); ?></guid>
				<description><![CDATA[<?php echo wp_kses_post( $use_data->content ); ?>]]></description>
				<content:encoded><![CDATA[<?php echo wp_kses_post( $use_data->content ); ?>]]></content:encoded>
				<?php
				if ( has_post_thumbnail( $use_data->posts_id ) ) {
					$thumbnail_id = get_post_thumbnail_id( $use_data->posts_id );
					$thumbnail    = image_get_intermediate_size( $thumbnail_id, $use_data->posts_type . '-img' );

					// If there is image that size, try thumbnail.
					if ( empty( $thumbnail ) ) {
						$thumbnail = image_get_intermediate_size( $thumbnail_id );
					}

					// Now we should have one.
					if ( ! empty( $thumbnail ) ) {
						$upload_dir = wp_upload_dir();

						printf(
							'<enclosure url="%s" length="%s" type="%s" />',
							esc_url( $thumbnail['url'] ),
							esc_html( filesize( path_join( $upload_dir['basedir'], $thumbnail['path'] ) ) ),
							esc_html( get_post_mime_type( $thumbnail_id ) )
						);
					}
				}
				?>

				<?php do_action( 'rss2_item' ); ?>
			</item>
			<?php
		}
	}
}
