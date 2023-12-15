<?php
/**
 * LWTV\_Components\Debugger class.
 *
 * @package LWTV
 */

namespace LWTV\_Components;

use LWTV\Debugger\Actors;
use LWTV\Debugger\Characters;
use LWTV\Debugger\Queers;
use LWTV\Debugger\Shows;

/**
 * Class for adding primary theme support.
 *
 * Exposes template tags
 *
 */
class Debugger implements Component, Templater {

	/**
	 * Init the component. Hooks go in here.
	 *
	 * @return void
	 */
	public function init(): void {
		// Void
	}

	/**
	 * Retrieve the template tags.
	 *
	 * @return array
	 */
	public function get_template_tags(): array {
		return array(
			'sanitize_social'           => array( $this, 'sanitize_social' ),
			'validate_imdb'             => array( $this, 'validate_imdb' ),
			'format_wikidate'           => array( $this, 'format_wikidate' ),
			'find_actors_problems'      => array( $this, 'find_actors_problems' ),
			'find_actors_incomplete'    => array( $this, 'find_actors_incomplete' ),
			'find_actors_no_imdb'       => array( $this, 'find_actors_no_imdb' ),
			'check_actors_wikidata'     => array( $this, 'check_actors_wikidata' ),
			'find_shows_bad_url'        => array( $this, 'find_shows_bad_url' ),
			'find_shows_no_imdb'        => array( $this, 'find_shows_no_imdb' ),
			'find_shows_problems'       => array( $this, 'find_shows_problems' ),
			'find_characters_problems'  => array( $this, 'find_characters_problems' ),
			'check_disabled_characters' => array( $this, 'check_disabled_characters' ),
			'find_queer_chars'          => array( $this, 'find_queer_chars' ),
		);
	}

	/**
	 * Sanitize social media handles
	 * @param  string $usename Username
	 * @param  string $social  Social Media Type
	 * @return string          sanitized username
	 */
	public function sanitize_social( $usename, $social ): string {

		// Defaults.
		$trim  = 10;
		$regex = '/[^a-zA-Z_.0-9]/';

		switch ( $social ) {
			case 'instagram': // ex: https://instagram.com/lezwatchtv
				$usename = str_replace( 'https://instagram.com/', '', $usename );
				$trim    = 30;
				break;
			case 'twitter': // ex: https://twitter.com/lezwatchtv OR https://x.com/lezwatchtv
				$usename = str_replace( 'https://twitter.com/', '', $usename );
				$usename = str_replace( 'https://x.com/', '', $usename );
				$trim    = 15;
				break;
			case 'mastodon': // ex: https://mstdn.social/@lezwatchtv
				$regex = '/[^a-zA-Z_.0-9:\/@]/';
				$trim  = 2000;
				break;
		}

		// Remove all illegal characters.
		$clean = preg_replace( $regex, '', trim( $usename ) );

		$clean = substr( $clean, 0, $trim );

		return $clean;
	}

	/**
	 * Clean up the WikiDate
	 * @param  string $date Wiki formatted date: +1968-07-07T00:00:00Z
	 * @return string      LezWatch formatted date: 1968-07-07
	 */
	public function format_wikidate( $date ): string {
		$clean = trim( substr( $date, 0, strpos( $date, 'T' ) ), '+' );
		return $clean;
	}

	/**
	 * Validate IMDB
	 * @param  string  $imdb IMDB ID
	 * @return boolean         true/false
	 */
	public function validate_imdb( $imdb, $type = 'show' ): bool {

		// Defaults
		$result = true;
		$type   = ( ! in_array( $type, array( 'show', 'actor' ), true ) ) ? 'show' : $type;

		switch ( $type ) {
			case 'show':
				$substr = 'tt';
				break;
			case 'actor':
				$substr = 'nm';
				break;
			default:
				$substr = 'tt';
				break;
		}

		// IMDB looks like tt123456 or nm12356
		if ( substr( $imdb, 0, 2 ) !== $substr || ! is_numeric( substr( $imdb, 2 ) ) ) {
			$result = false;
		}

		return $result;
	}

	/**
	 * Find Actors problems
	 *
	 * @param array $items - array of Actors
	 */
	public function find_actors_problems( $items = array() ): array {
		return ( new Actors() )->find_actors_problems( $items );
	}

	/**
	 * Find Incomplete Actors
	 *
	 * @param array $items - array of Actors
	 */
	public function find_actors_incomplete( $items = array() ): array {
		return ( new Actors() )->find_actors_incomplete( $items );
	}

	/**
	 * Find Actors without IMDb Entries
	 *
	 * @param array $items - array of Actors
	 */
	public function find_actors_no_imdb( $items = array() ): array {
		return ( new Actors() )->find_actors_no_imdb( $items );
	}

	/**
	 * Check Actors' WikiData
	 *
	 * @param int|array $actors - Post ID of actor OR array of actors
	 * @param array     $items  - array of existing items (used for re-check)
	 */
	public function check_actors_wikidata( $actors = 0, $items = array() ): array {
		return ( new Actors() )->check_actors_wikidata( $actors, $items );
	}

	/**
	 * Find Shows with bad URLs
	 *
	 * @param array $items - array of Shows
	 */
	public function find_shows_bad_url( $items = array() ): array {
		return ( new Shows() )->find_shows_bad_url( $items );
	}

	/**
	 * Find Shows with no IMDb
	 *
	 * @param array $items - array of Shows
	 */
	public function find_shows_no_imdb( $items = array() ): array {
		return ( new Shows() )->find_shows_no_imdb( $items );
	}

	/**
	 * Find Shows with Problems
	 *
	 * @param array $items - array of Shows
	 */
	public function find_shows_problems( $items = array() ): array {
		return ( new Shows() )->find_shows_problems( $items );
	}

	/**
	 * Find Characters with Problems
	 *
	 * @param array $items - array of Characters
	 */
	public function find_characters_problems( $items = array() ): array {
		return ( new Characters() )->find_characters_problems( $items );
	}

	/**
	 * Find Characters with Disabilities
	 *
	 * @param array $the_id - Show ID to check
	 */
	public function check_disabled_characters( $the_id ): array {
		return ( new Characters() )->check_disabled_characters( $the_id );
	}

	/**
	 * Find Queer Characters
	 *
	 * Find all characters who are mismatched with their queer settings
	 *
	 * @param array $the_id - Show ID to check
	 */
	public function find_queer_chars( $items = array() ): array {
		return ( new Queers() )->find_queerchars( $items );
	}
}
