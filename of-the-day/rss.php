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

	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	public function init() {
		add_feed( 'otd', array( $this, 'add_feed' ) );
	}

	public function add_feed() {
		get_template_part( 'rss', 'otd' );
	}

	public function rss_feed() {

		global $wpdb;

		$table = $wpdb->prefix . 'lwtv_otd';
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$table_data = $wpdb->get_results( "SELECT * FROM {$table} order by id desc limit 10" );

		foreach ( $table_data as $use_data ) {
			?>
			<item>
				<title><?php echo esc_html( ucfirst( $use_data->post_type ) ); ?> of the Day: <?php get_the_title( $use_data->post_id ); ?></title>
				<link><?php get_permalink( $use_data->post_id ); ?></link>
				<pubDate><?php echo esc_html( mysql2date( 'D, d M Y H:i:s +0000', $use_data->post_datetime ) ); ?></pubDate>
				<dc:creator>LezWatch.TV</dc:creator>
				<guid isPermaLink="false"><?php the_guid( $use_data->post_id ); ?></guid>
				<description><![CDATA[<?php echo wp_kses_post( $use_data->content ); ?>]]></description>
				<content:encoded><![CDATA[<?php echo wp_kses_post( $use_data->content ); ?>]]></content:encoded>
				<?php
				if ( has_post_thumbnail( $use_data->post_id ) ) {
					$thumbnail = get_attachment_link( get_post_thumbnail_id( $use_data->post_id ) );
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo "<image>{$thumbnail}</image>";
				}
				?>
				<?php do_action( 'rss2_item' ); ?>
			</item>
			<?php
		}
	}

}

new LWTV_Of_The_Day_RSS();
