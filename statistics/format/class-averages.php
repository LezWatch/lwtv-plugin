<?php

class LWTV_Statistics_Averages_Format {
	/*
	 * Statistics Display Average
	 *
	 * Output the list of data usually from functions like self::meta_array
	 * It loops through the arrays and outputs data as needed
	 *
	 * @param string $subject    The content subject (ex: dead)
	 * @param string $data       The data 'subject' - used to generate the URLs
	 * @param array  $data_array The array of data
	 * @param string $count      The count of posts (usually all characters)
	 *
	 * @return Content
	 */
	public function make( $subject, $data, $data_array, $count, $type = 'average' ) {

		$valid_types = array( 'high', 'low', 'average' );
		if ( ! in_array( $type, $valid_types, true ) ) {
			$type = 'average';
		}

		switch ( $type ) {
			case 'average':
				$n   = ( 'dead-years' === $data ) ? ( gmdate( 'Y' ) - FIRST_LWTV_YEAR ) : $count;
				$sum = 0;
				foreach ( $data_array as $item ) {
					$sum = $sum + (float) $item['count'];
				}
				$average = round( $sum / $n );
				$return  = $average;
				break;
			case 'high':
				$high = 0;
				foreach ( $data_array as $key => $value ) {
					if ( (float) $value['count'] > (float) $high ) {
						$high = (float) $value['count'];
						if ( 'shows' === $subject ) {
							$high .= ' (<a href="' . $value['url'] . '">' . get_the_title( $value['id'] ) . '</a>)';
						}
					}
				}
				$return = $high;
				break;
			case 'low':
				$low = 20;
				foreach ( $data_array as $key => $value ) {
					if ( (float) $low > (float) $value['count'] ) {
						$low = (float) $value['count'];
						if ( 'shows' === $subject ) {
							$low .= ' (<a href="' . $value['url'] . '">' . get_the_title( $value['id'] ) . '</a>)';
						}
					}
				}
				$return = $low;
				break;
		}
		echo (float) $return;
	}
}
