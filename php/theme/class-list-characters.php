<?php

namespace LWTV\Theme;

class List_Characters {
	/**
	 * Generate character lists
	 *
	 * @access public
	 *
	 * @param  string $show_id
	 * @param  string $format
	 *
	 * @return mixed
	 */
	public function make( $post_id, $format ) {
		switch ( $format ) {
			case 'dead':
				$return = get_post_meta( $post_id, 'lezshows_dead_count', true );
				break;
			default:
				$return = get_post_meta( $post_id, 'lezshows_char_count', true );
				break;
		}

		// If the meta is empty, regenerate
		if ( ! isset( $return ) || empty( $return ) ) {
			$return = lwtv_plugin()->get_characters_list( $post_id, $format );
		}

		return $return;
	}
}
