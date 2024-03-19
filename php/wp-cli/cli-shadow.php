<?php
/*
 * WP CLI Commands for LezWatch.TV
 *
 * These commands are 'shadow' sync tools.
 */

// Bail if directly accessed
if ( ! defined( 'ABSPATH' ) && ! defined( 'WP_CLI' ) ) {
	die();
}

/**
 * LezWatch.TV commands to regenerate content.
 */
class WP_CLI_LWTV_Shadow {

	/**
	 * @var string
	 */
	public $format;

	/**
	 * @var string
	 */
	public $dry_run;

	/**
	 * Construct to block facet from munging results.
	 */
	public function __construct() {
		// phpcs:disable
		// Remove <!--fwp-loop--> from output
		add_filter( 'facetwp_is_main_query', function( $is_main_query, $query ) {
			return false;
		}, 10, 2 );
		// phpcs:enable
	}

	/**
	 * Sync the shadow taxonomies to custom post types.
	 *
	 * Since everything is based off characters, we only need to
	 * sync from characters to do it all.
	 *
	 * ## OPTIONS
	 *
	 * <type>
	 * : Custom Post Type to sync.
	 * ---
	 * options:
	 *   - shows
	 *   - actors
	 * ---
	 *
	 * [--post_id=<post_id>]
	 * : Post ID to sync.
	 * ---
	 * default: null
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 * wp lwtv shadow shows
	 * wp lwtv shadow actors
	 *
	 * @param array $args
	 * @param array $assoc_args
	 */
	public function __invoke( $args, $assoc_args = array() ) {

		$this->format  = \WP_CLI\Utils\get_flag_value( $assoc_args, 'format', 'table' );
		$this->dry_run = \WP_CLI\Utils\get_flag_value( $assoc_args, 'dry-run', false );
		$type          = isset( $args[0] ) ? $args[0] : 'shows';
		$post_id       = \WP_CLI\Utils\get_flag_value( $assoc_args, 'post_id', false );

		try {
			$this->run_shadow_sync( $type, $post_id );
		} catch ( Exception $exception ) {
			\WP_CLI::error( $exception->getMessage(), false );
		}
	}

	/**
	 * Run the syncer
	 *
	 * @param string $tax    Taxonomy to sync
	 * @param int    $post_id Post ID to sync
	 */
	public function run_shadow_sync( $tax, $post_id ) {
		// The taxonomy we're syncing FROM
		$shadow_tax = 'shadow_tax_characters';

		if ( ! taxonomy_exists( $shadow_tax ) ) {
			\WP_CLI::error( 'The Taxonomy you provided does not exist.' );
		}

		$this->sync_characters( $tax, $post_id );
	}

	/**
	 * Sync characters with the requested shadow taxonomy.
	 *
	 * @param string tax what we're syncing TO
	 * @param int    post_id Post ID to sync (optional)
	 */
	public function sync_characters( $tax, $post_id ) {
		if ( $post_id ) {
			$posts_array = array( $post_id );
		} else {
			$posts_queery = lwtv_plugin()->queery_post_type( 'post_type_characters' );

			if ( ! is_object( $posts_queery ) || ! $posts_queery->have_posts() ) {
				\WP_CLI::error( 'There are no posts in that post type.' );
			} else {
				$posts_array = wp_list_pluck( $posts_queery->posts, 'ID' );
			}
		}

		if ( ! is_array( $posts_array ) || empty( $posts_array ) ) {
			\WP_CLI::error( 'There are no posts in that post type.' );
		}

		$progress_bar = \WP_CLI\Utils\make_progress_bar( sprintf( 'Starting sync. Found %d characters...', count( $posts_array ) ), count( $posts_array ) );

		foreach ( $posts_array as $one_post ) {
			$progress_bar->tick();
			switch ( $tax ) {
				case 'shows':
					$this->sync_characters_to_shows( $one_post );
					break;
				case 'actors':
					$this->sync_characters_to_actors( $one_post );
					break;
				default:
					\WP_CLI::error( 'The Taxonomy you provided does not have a sync function for characters.' );
			}
		}

		\WP_CLI::line( 'Synced Characters with ' . $tax . '.' );
	}

	/**
	 * Sync characters to shows.
	 *
	 * This looks for all the shows attached to a character and syncs them to the shadow taxonomy for the character.
	 *
	 * @param int $one_post Character post ID
	 */
	public function sync_characters_to_shows( $one_post ) {
		$shadow_cpt       = 'shadow_tax_characters';
		$show_group       = get_post_meta( $one_post, 'lezchars_show_group', true );
		$shadow_character = \Shadow_Taxonomy\Core\get_associated_term( $one_post, $shadow_cpt );

		if ( ! $show_group ) {
			return;
		}

		// Add shows to character
		foreach ( $show_group as $each_show ) {
			// Remove the Array.
			if ( is_array( $each_show['show'] ) ) {
				$each_show['show'] = $each_show['show'][0];
			}

			// Add the tax for the character to the show.
			if ( ! has_term( $shadow_character->term_id, $shadow_cpt, $each_show['show'] ) ) {
				wp_add_object_terms( (int) $each_show['show'], (int) $shadow_character->term_id, $shadow_cpt );
			}
		}
	}

	/**
	 * Sync characters to actors.
	 *
	 * This looks for all the actors attached to a character and syncs them to the shadow taxonomy for the character.
	 *
	 * @param int $one_post Character post ID
	 */
	public function sync_characters_to_actors( $one_post ) {
		$shadow_cpt       = 'shadow_tax_characters';
		$shadow_character = \Shadow_Taxonomy\Core\get_associated_term( $one_post, $shadow_cpt );
		$actors           = get_post_meta( $one_post, 'lezchars_actor', true );

		if ( ! $actors ) {
			return;
		}

		// Force it to be an array.
		$actors = ( ! is_array( $actors ) ) ? array( $actors ) : $actors;

		foreach ( $actors as $actor ) {
			wp_add_object_terms( (int) $actor, (int) $shadow_character->term_id, $shadow_cpt );
		}
	}
}

\WP_CLI::add_command( 'lwtv shadow', 'WP_CLI_LWTV_Shadow' );
