<?php

namespace LWTV\Statistics\Build;

use LWTV\Statistics\Build\Meta as Build_Meta;

class Yes_No {

	/*
	 * Yes/No arrays
	 *
	 * Generate array to parse post meta data
	 *
	 * @param string $post_type Post Type to be search
	 * @param array $meta_array Meta terms to loop through
	 * @param string $key Post Meta Key name (i.e. lezchars_gender)
	 * @param string $data The data 'subject' - used to generate the URLs
	 * @param string $count Total post count
	 *
	 * @return array
	 */
	public function make( $post_type, $data, $count ) {

		$array = array(
			'no'  => array(
				'count' => '0',
				'name'  => 'No',
				'url'   => '',
			),
			'yes' => array(
				'count' => '0',
				'name'  => 'Yes',
				'url'   => '',
			),
		);

		// Define the options
		switch ( $data ) {
			case 'weloveit':
				$meta_array = array(
					'on',
				);
				$key        = 'lezshows_worthit_show_we_love';
				$compare    = '=';
				break;
			case 'current':
				$meta_array = array(
					'current',
					'notcurrent',
				);
				$key        = 'lezshows_airdates';
				$compare    = 'REGEXP';
				break;
		}

		// Collect the data
		$meta = ( new Build_Meta() )->make( $post_type, $meta_array, $key, $data, $compare );

		// Parse the data
		switch ( $data ) {
			case 'weloveit':
				$array['no']['count']  = $count - $meta['on']['count'];
				$array['yes']['count'] = $meta['on']['count'];
				$array['yes']['url']   = home_url( '/shows/?fwp_show_loved=on' );
				break;
			case 'current':
				$array['no']['count']  = $count - $meta['current']['count'];
				$array['yes']['count'] = $meta['current']['count'];
				break;
		}

		return $array;
	}
}
