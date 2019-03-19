<?php
/*
 * Code to show newsapi page
 *
 * @since 2.3
*/

// if this file is called directly abort
if ( ! defined( 'WPINC' ) ) {
	die;
}

class LWTV_Admin_News {
	/*
	 * Settings Page Content
	 */
	public static function settings_page() {
		$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'intro'; // phpcs:ignore WordPress.Security.NonceVerification
		?>

		<div class="wrap">
			<h1>Recent News</h1>

			<p><a href="https://newsapi.org/">Powered by News API</a></p>

			<p>This service scans every day to see if there's any queer TV related news it should list. It's not perfect, but it's nice to see if we missed anything over the last 6 months.</p>

			<?php
			// Check the transient. If it's expired, we'll fetch news again.
			$newsapi = get_transient( 'lwtv_newsapi' );
			if ( empty( $transient ) ) {
				$today     = date( 'Y-m-d' );
				$yesterday = date( 'Y-m-d', strtotime( '-1 days' ) );
				$searchfor = urlencode( '(television OR tv OR storyline) AND (lesbian OR lgbt OR queer)' );
				$the_url   = 'https://newsapi.org/v2/everything?q=' . $searchfor . '&sortBy=publishedAt&pageSize=50';
				$url_args  = array(
					'headers' => array(
						'x-api-key' => NEWSAPI,
					),
				);
				$newsapi   = wp_remote_get( $the_url, $url_args );

				set_transient( 'lwtv_newsapi', $newsapi, DAY_IN_SECONDS );
			}

			$news_data = json_decode( $newsapi['body'], true );
			?>

			<div class="lwtv-tools-table">
				<table class="widefat fixed" cellspacing="0">
					<thead><tr>
						<th id="source" class="manage-column column-author" scope="col">Souce</th>
						<th id="date" class="manage-column column-date" scope="col">Date</th>
						<th id="title" class="manage-column column-title" scope="col">Title</th>
						<th id="content" class="manage-column column-content" scope="col">Content</th>
					</tr></thead>

					<tbody>
						<?php
						$articles = $news_data['articles'];
						self::table_articles( $articles );
						?>
					</tbody>
				</table>
			</div>
		</div>
		<?php
	}

	public static function table_articles( $articles ) {
		$number = 1;

		if ( empty( $articles ) ) {
			echo '
			<tr>
				<td><strong>ERROR</strong></td>
				<td>&nbsp;</td>
				<td>No content found.</td>
				<td>&nbsp;</td>
			</tr>
			';
		} else {
			foreach ( $articles as $article ) {
				$class = ( 0 === $number % 2 ) ? '' : 'alternate';
				echo '
				<tr class="' . esc_attr( $class ) . '">
					<td>' . $article['source']['name'] . '</td>
					<td>' . $article['publishedAt'] . '</td>
					<td><strong><a href="' . esc_url( $article['url'] ) . '" target="_new">' . $article['title'] . '</a></strong></td>
					<td>' . wp_kses_post( $article['content'] ) . '</td>
				</tr>
				';
				$number++;
			}
		}
	}

}

new LWTV_Admin_News();
