<?php
/**
 * Template Name: Custom RSS Template - Of The Day
 *
 * Customizes RSS feeds for OTD - /feeds/otd/
 */

header( 'Content-Type: ' . feed_content_type( 'rss-http' ) . '; charset=' . get_option( 'blog_charset' ), true );

echo '<?xml version="1.0" encoding="' . esc_attr( get_option( 'blog_charset' ) ) . '"?>';
?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
	<?php do_action( 'rss2_ns' ); ?>>
<channel>
	<title>LezWatch.TV Of The Day - Feed</title>
	<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
	<link><?php echo esc_url( bloginfo_rss( 'url' ) ); ?></link>
	<description>Keep up to date with the latest featured characters and shows! Updated twice a day.</description>
	<lastBuildDate>
		<?php echo esc_html( mysql2date( 'D, d M Y H:i:s +0000', lwtv_plugin()->get_rss_otd_last_build(), false ) ); ?>
	</lastBuildDate>
	<language>en-US</language>
	<sy:updatePeriod><?php echo esc_html( apply_filters( 'rss_update_period', 'hourly' ) ); ?></sy:updatePeriod>
	<sy:updateFrequency><?php echo esc_html( apply_filters( 'rss_update_frequency', '1' ) ); ?></sy:updateFrequency>
	<generator>https://wordpress.org/?v=<?php echo floatval( lwtv_plugin()->get_wp_version() ); ?></generator>
	<image>
		<url><?php echo esc_url( get_option( 'jetpack_site_icon_url' ) ); ?></url>
		<title>LezWatch.TV Of The Day - Feed</title>
		<link><?php echo esc_url( get_site_url() ); ?>/feed/otd/</link>
		<width>32</width>
		<height>32</height>
	</image>
	<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	lwtv_plugin()->get_rss_otd_feed();
	?>
</channel>
</rss>
