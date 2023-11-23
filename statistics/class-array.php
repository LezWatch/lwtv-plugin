<?php
/**
 * Build the array of data.
 *
 * @package LezWatch.TV
 */

class LWTV_Statistics_Array {
	/**
	 * Build Array
	 *
	 * @param string $subject       'actors', 'characters', or 'shows'.
	 * @param string $data          The stats being run.
	 * @param string $format        The format of the output.
	 * @param int    $post_id       Post ID
	 * @param array  $custom_array  Extra array of data
	 * @param string $count         Number of items
	 * @param array  $maybe_deep    Customized Deep Data.
	 * @param array  $data_original Original $data (only exists if $maybe_deep is valid -- OPTIONAL)
	 *
	 * @return array Customized array.
	 */
	public function make( $subject, $data, $format, $post_id, $custom_array, $count, $maybe_deep, $data_original = null ) {
		// Build Variables.
		$post_type = 'post_type_' . $subject;
		$taxonomy  = 'lez_' . $data;
		$prearray  = ( false !== $maybe_deep ) ? $maybe_deep['prearray'] : false;
		$minor     = ( false !== $maybe_deep ) ? $maybe_deep['minor'] : false;

		// Custom Meta
		$meta_params = ( isset( LWTV_Statistics::META_PARAMS[ $data ] ) ) ? LWTV_Statistics::META_PARAMS[ $data ] : null;

		// If there's no data match, return empty:
		if ( ! isset( LWTV_Statistics::DATA_CLASS_MATCHER[ $data ] ) ) {
			return;
		}

		// Build Dead taxonomy.
		$dead_tax = self::dead_taxonomy( $data );

		// Define Data Class.
		$data_class = LWTV_Statistics::DATA_CLASS_MATCHER[ $data ];

		// Reset Data:
		$data = ( ! is_null( $data_original ) ) ? $data_original : $data;

		// Build Params, based on Data type.
		$build_params = array(
			'Actor_Char_Role'       => array( $post_type, $post_id ),
			'Actor_Char_Dead'       => array( $post_type, $post_id ),
			'Actor_Chars'           => $data,
			'Char_Role'             => '',
			'Complex_Taxonomy'      => array( $count, $data, $subject ),
			'Dead_Basic'            => array( $subject, 'array' ),
			'Dead_Complex_Taxonomy' => array( substr( $data, 0, -5 ) ),
			'Dead_List'             => array( $format ),
			'Dead_Role'             => '',
			'Dead_Shows'            => array( 'simple' ),
			'Dead_Taxonomy'         => array( $post_type, $dead_tax ),
			'Dead_Year'             => '',
			'Meta'                  => ! is_null( $meta_params ) ? array( $post_type, $meta_params['array'], $meta_params['key'], $data, $meta_params['compare'] ) : '',
			'On_Air'                => array( $post_type, $prearray, $minor ),
			'Scores'                => array( $post_type ),
			'Taxonomy'              => array( $post_type, $taxonomy ),
			'Taxonomy_Breakdowns'   => array( $count, $format, $data, $subject ),
			'This_Year'             => array( $data, $custom_array ),
			'Yes_No'                => array( $post_type, $data, $count ),
		);

		$build_class  = 'LWTV_Statistics_' . $data_class . '_Build';
		$build_params = $build_params[ $data_class ];

		if ( is_array( $build_params ) ) {
			$build_class_var = new $build_class();
			$array           = call_user_func_array( array( $build_class_var, 'make' ), $build_params );
		} else {
			$array = ( new $build_class() )->make( $build_params );
		}

		// If it's an array, return.
		if ( isset( $array ) && is_array( $array ) ) {
			return $array;
		}
	}

	/**
	 * Rename the taxonomy for death.
	 *
	 * @param string $data Current data value.
	 *
	 * @return [string|null] Correct value.
	 */
	public function dead_taxonomy( $data ) {
		if ( 'dead-sex' === $data ) {
			return 'lez_sexuality';
		}

		if ( 'dead-gender' === $data ) {
			return 'lez_gender';
		}

		return null;
	}
}
