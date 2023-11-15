<?php

class LWTV_Stats_Actor_Chars {

	/**
	 * Statistics: Actors and Characters
	 *
	 * @access public
	 * @static
	 * @param string $type (default: 'characters')
	 * @return void
	 */
	public function build( $type = 'characters' ) {
		$transient = 'actor_chars_' . $type;
		$array     = LWTV_Transients::get_transient( $transient );

		if ( false === $array ) {

			// list of people
			$all_query = ( new LWTV_Loops() )->post_type_query( 'post_type_' . $type );

			if ( $all_query->have_posts() ) {
				$all_array = wp_list_pluck( $all_query->posts, 'ID' );
				wp_reset_query();
			}

			$array = array();
			if ( is_array( $all_array ) ) {
				foreach ( $all_array as $all_one ) {
					// The data we parse depends on the data type
					switch ( $type ) {
						case 'characters':
							$data = get_post_meta( $all_one, 'lezchars_actor', true );
							$name = 'actors';
							break;
						case 'actors':
							$data = get_post_meta( $all_one, 'lezactors_char_count', true );
							$name = 'characters';
							break;
					}
					// Now that we have the data, let's count and store
					if ( is_numeric( $data ) ) {
						$key = $data;
					} elseif ( is_array( $data ) ) {
						$key = count( $data );
					} else {
						$key = 0;
					}

					// Check key
					if ( ! array_key_exists( $key, $array ) && is_numeric( $key ) ) {
						$array[ $key ] = array(
							'name'  => $key . ' ' . $name,
							'count' => '1',
							'url'   => '',
						);
					} else {
						++$array[ $key ]['count'];
					}
				}
			}

			ksort( $array );

			// save array as transient for a reason.
			set_transient( $transient, $array, DAY_IN_SECONDS );
		}

		return $array;
	}
}

new LWTV_Stats_Actor_Chars();
