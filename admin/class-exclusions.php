<?php
/*
 * Exclusion Checks For LezWatch.TV
 *
 */

// if this file is called directly abort
if ( ! defined( 'WPINC' ) ) {
	die;
}

class LWTV_Admin_Exclusions {

	/**
	 * Local Variables
	 */
	protected static $nav_tabs;  // all tabs

	/*
	 * Construct
	 *
	 * Actions to happen immediately
	 */
	public function __construct() {

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
			?>
			<div class="lwtv-tools-container lwtv-tools-container__alert">
				<h3><span class="dashicons dashicons-flag"></span> Overridden (<?php echo count( $queery ); ?>)</h3>
				<div id="lwtv-tools-alerts">
					<p>The following character(s) have had their queerness overridden.</p>
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
			?>
			<div class="lwtv-tools-container lwtv-tools-container__alert">
				<h3><span class="dashicons dashicons-flag"></span> Overridden (<?php echo count( $queery ); ?>)</h3>
				<div id="lwtv-tools-alerts">
					<p>The following show(s) have had their death-score deductions overridden.</p>
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
	 */
	public static function queery_loop( $post_type, $meta ) {
		$queery_loop = ( new LWTV_Queery_Post_Meta() )->make( $post_type, $meta, '', 'EXISTS' );
		$queery      = array();

		if ( $queery_loop->have_posts() ) {
			while ( $queery_loop->have_posts() ) {
				$queery_loop->the_post();
				$override = get_post_meta( get_the_ID(), $meta, true );
				if ( 'undefined' !== $override ) {
					$queery[] = get_the_ID();
				}
			}
		}
		wp_reset_postdata();
		wp_reset_query();

		return $queery;
	}
}

new LWTV_Admin_Exclusions();
