<?php
/*
 * Exclusion Checks For LezWatch.TV
 *
 */

namespace LWTV\Admin_Menu;

class Exclusions {

	/**
	 * Local Variables
	 */
	protected static $nav_tabs;  // all tabs

	/*
	 * Construct
	 *
	 * Actions to happen immediately
	 */
	public function init() {

		self::$nav_tabs = array(
			'queer_checker' => array(
				'name' => 'Queer Checker',
				'desc' => 'Lists all actors with their queerness overridden.',
			),
			'dead_checker'  => array(
				'name' => 'Dead Checker',
				'desc' => 'List all shows with the death score deduction overridden.',
			),
		);

		add_submenu_page( 'lwtv', 'Exclusion Checker', 'Exclusion Checker', 'activate_plugins', 'lwtv_exclusion_check', array( $this, 'settings_page' ) );
	}

	/*
	 * Settings Page Content
	 */
	public static function settings_page() {
		// Get the active tab for later
		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'intro'; // phpcs:ignore WordPress.Security.NonceVerification
		?>
		<div class="wrap">

			<h1>Exclusion Tools</h1>

			<h2 class="nav-tab-wrapper">
				<a href="?page=lwtv_exclusion_check" class="nav-tab <?php echo ( 'intro' === $active_tab ) ? 'nav-tab-active' : ''; ?>">Introduction</a>
				<?php
				foreach ( self::$nav_tabs as $tab => $value ) {
					$active = ( $tab === $active_tab ) ? 'nav-tab-active' : '';
					echo '<a href="?page=lwtv_exclusion_check&tab=' . esc_attr( $tab ) . '" class="nav-tab ' . esc_attr( $active ) . '">' . esc_html( $value['name'] ) . '</a>';
				}
				?>
			</h2>

			<div id="dashboard" class="lwtvtab">
				<?php
				switch ( $active_tab ) {
					case 'queer_checker':
						self::tab_queer_checker();
						break;
					case 'dead_checker':
						self::tab_byq_checker();
						break;
					default:
						self::tab_introduction();
				}
				?>
			</div>

		</div>
		<?php
	}

	/**
	 * Table content
	 *
	 * @param  array   $items Items to check
	 * @return string  Build Table in HTML
	 */
	public static function table_content( $items, $check = 'queerness' ) {
		$number = 1;
		foreach ( $items as $item ) {
			$class = ( 0 === $number % 2 ) ? '' : 'alternate';
			switch ( $check ) {
				case 'queerness':
					$override = get_post_meta( $item, 'lezactors_queer_override', true );
					break;
				case 'death':
					$override = ucfirst( get_post_meta( $item, 'lezshows_byq_override', true ) );
					break;
			}

			// If override is empty, we keep going.
			if ( empty( $override ) || 'undefined' === $override ) {
				continue;
			}

			echo '
			<tr class="' . esc_attr( $class ) . '">
				<td><strong><a href="' . esc_url( get_edit_post_link( (int) $item ) ) . '" target="_new">' . wp_kses_post( get_the_title( (int) $item ) ) . '</a></strong>

				<div class="row-actions"><span class="edit"><a href="' . esc_url( get_edit_post_link( (int) $item ) ) . '" aria-label="Edit ' . wp_kses_post( get_the_title( (int) $item ) ) . '">Edit</a>
				| </span><span class="view"><a href="' . esc_url( get_permalink( (int) $item ) ) . '" rel="bookmark" aria-label="View ' . wp_kses_post( get_the_title( (int) $item ) ) . '">View</a></span></div>
				</td>
				<td>' . esc_html( $override ) . '</td>
			</tr>
			';
			++$number;
		}
	}

	/**
	 * Static Introduction to what the hell is going on...
	 */
	public static function tab_introduction() {
		?>
		<div class="tab-block"><div class="lwtv-tools-container">
			<h3>LezWatch.TV Exclusion Checks</h3>
			<p>There are times when we override certain settings because automation only goes so far. However to keep ourselves honest, we have to track those things.</p>

			<hr>

			<ul>
				<?php
				foreach ( self::$nav_tabs as $tab => $value ) {
					echo '<li>&bull; <a href="?page=lwtv_exclusion_check&tab=' . esc_attr( $tab ) . '">' . esc_html( $value['name'] ) . '</a> - ' . esc_html( $value['desc'] ) . '</li>';
				}
				?>
			</ul>

		</div></div>

		<?php
	}

	/**
	 * Output the results of queer checking...
	 */
	public static function tab_queer_checker() {
		$queery = self::queery_loop( 'post_type_actors', 'lezactors_queer_override' );

		if ( empty( $queery ) || ! is_array( $queery ) ) {
			?>
			<div class="lwtv-tools-container lwtv-tools-container__alert">
				<h3><span class="dashicons dashicons-info"></span> None!</h3>
				<div id="lwtv-tools-alerts">
					<p>No actors have their queerness overridden at this time.</p>
				</div>
			</div>
			<?php
		} else {
			$count = count( $queery );
			// translators: %s is the number of shows.
			$have = _n( 'character has', 'characters have', $count );
			?>
			<div class="lwtv-tools-container lwtv-tools-container__alert">
				<h3><span class="dashicons dashicons-flag"></span> Overridden (<?php echo (int) $count; ?>)</h3>
				<div id="lwtv-tools-alerts">
					<p>The following <?php echo esc_html( $have ); ?> had their queerness overridden.</p>
				</div>
			</div>

			<div class="lwtv-tools-table">
				<table class="widefat fixed" cellspacing="0">
					<thead><tr>
						<th id="character" class="manage-column column-character" scope="col">Character</th>
						<th id="problem" class="manage-column column-problem" scope="col">Setting</th>
					</tr></thead>

					<tbody>
						<?php
						self::table_content( $queery, 'queerness' );
						?>
					</tbody>
				</table>
			</div>
			<?php
		}
	}

	/**
	 * Output the results of BYQ checking...
	 */
	public static function tab_byq_checker() {
		$queery = self::queery_loop( 'post_type_shows', 'lezshows_byq_override' );

		if ( empty( $queery ) || ! is_array( $queery ) ) {
			?>
			<div class="lwtv-tools-container lwtv-tools-container__alert">
				<h3><span class="dashicons dashicons-info"></span> None!</h3>
				<div id="lwtv-tools-alerts">
					<p>No shows have their scores for death overridden at this time.</p>
				</div>
			</div>
			<?php
		} else {
			$count = count( $queery );
			// translators: %s is the number of shows.
			$have = _n( 'show has', 'shows have', $count );
			?>
			<div class="lwtv-tools-container lwtv-tools-container__alert">
				<h3><span class="dashicons dashicons-flag"></span> Overridden (<?php echo (int) $count; ?>)</h3>
				<div id="lwtv-tools-alerts">
					<p>The following <?php echo esc_html( $have ); ?> had death-score deductions overridden.</p>
				</div>
			</div>

			<div class="lwtv-tools-table">
				<table class="widefat fixed" cellspacing="0">
					<thead><tr>
						<th id="show" class="manage-column column-show" scope="col">Show</th>
						<th id="problem" class="manage-column column-problem" scope="col">Setting</th>
					</tr></thead>

					<tbody>
						<?php
						self::table_content( $queery, 'death' );
						?>
					</tbody>
				</table>
			</div>
			<?php
		}
	}

	/**
	 * Loop through the results and remove posts that aren't undefined.
	 *
	 * @param  string $post_type
	 * @param  string $meta
	 * @return array  Posts to check.
	 */
	public static function queery_loop( $post_type, $post_meta ) {
		$queery_loop = lwtv_plugin()->queery_post_meta( $post_type, $post_meta, '', 'EXISTS' );
		$queery      = array();

		if ( ! is_object( $queery_loop ) || ! $queery_loop->have_posts() ) {
			return $queery;
		}

		while ( $queery_loop->have_posts() ) {
			$queery_loop->the_post();
			$override = get_post_meta( get_the_ID(), $post_meta, true );
			if ( 'undefined' !== $override ) {
				$queery[] = get_the_ID();
			}
		}

		return $queery;
	}
}
