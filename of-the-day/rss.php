<?php
/**
 * Name: Of the Day RSS
 *
 * This will create ONE new feeds:
 * /otd/feed
 *
 * Those will be used to power shit.
 */

class LWTV_Of_The_Day_RSS {

	/**
	 * Initiate all the things!!!
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Run on init.
	 */
	public function init() {
		add_feed( 'otd', array( $this, 'add_feed' ) );
		add_filter( 'feed_content_type', array( $this, 'feed_content_type' ), 10, 2 );
	}

	/**
	 * Add new feed.
	 */
	public function add_feed() {
		get_template_part( 'rss', 'otd' );
	}

	/**
	 * Generate last-build
	 *
	 * This needs to be based on the last entry we added to the table.
	 */
	public function last_build() {
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
			add_action( 'rss2_item', array( $this, 'customize_rss_item' ) );
			add_filter( 'wp_title_rss', array( $this, 'rss_title' ), 20, 1 );
		}
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
	public function rss_feed() {

		global $wpdb;

		$table = $wpdb->prefix . 'lwtv_otd';
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$table_data = $wpdb->get_results( "SELECT * FROM {$table} order by id desc limit 10" );

		foreach ( $table_data as $use_data ) {
			?>
			<item>
				<title><?php echo esc_html( ucfirst( $use_data->post_type ) ); ?> of the Day: <?php echo esc_html( get_the_title( $use_data->post_id ) ); ?></title>
				<link><?php echo esc_url( get_permalink( $use_data->post_id ) ); ?></link>
				<pubDate><?php echo esc_html( mysql2date( 'D, d M Y H:i:s +0000', $use_data->post_datetime ) ); ?></pubDate>
				<dc:creator>LezWatch.TV</dc:creator>
				<guid isPermaLink="false"><?php the_guid( $use_data->post_id ); ?></guid>
				<description><![CDATA[<?php echo wp_kses_post( $use_data->content ); ?>]]></description>
				<content:encoded><![CDATA[<?php echo wp_kses_post( $use_data->content ); ?>]]></content:encoded>
				<?php do_action( 'rss2_item' ); ?>
			</item>
			<?php
		}
	}

}

new LWTV_Of_The_Day_RSS();
