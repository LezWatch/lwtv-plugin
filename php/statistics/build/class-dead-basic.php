<?php

namespace LWTV\Statistics\Build;

use LWTV\Statistics\Build\Taxonomy as Build_Taxonomy;

class Dead_Basic {

	/*
	 * Statistics Basic death
	 *
	 * Death is insane. This is just looping a lot of things to sort
	 * out who died in what year, so we can use it by other functions
	 *
	 * @param string $subject - whatever we're working with
	 * @param string $output  - Array or Count
	 *
	 * @return array or count
	 */
	public function make( $subject, $output ) {

		switch ( $subject ) {
			case 'characters':
				$taxonomy = 'lez_cliches';
				$terms    = 'dead';
				break;
			case 'shows':
				$taxonomy = 'lez_tropes';
				$terms    = 'dead-queers';
				break;
		}

		$array = ( new Build_Taxonomy() )->make( 'post_type_' . $subject, $taxonomy, $terms );

		switch ( $subject ) {
			case 'characters':
				$array['dead'] = array(
					'count' => ( $array['dead']['count'] ),
					'name'  => 'Dead Characters',
					'url'   => home_url( '/cliche/dead/' ),
				);
				$count         = $array['dead']['count'];
				break;
			case 'shows':
				$array['dead-queers'] = array(
					'count' => ( $array['dead-queers']['count'] ),
					'name'  => 'Shows with Dead',
					'url'   => home_url( '/trope/dead-queers/' ),
				);
				$count                = $array['dead-queers']['count'];
				break;
		}

		switch ( $output ) {
			case 'array':
				$return = $array;
				break;
			case 'count':
				$return = $count;
				break;
		}

		return $return;
	}
}
