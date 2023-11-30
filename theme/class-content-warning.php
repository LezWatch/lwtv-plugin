<?php

class LWTV_Theme_Content_Warning {
	/**
	 * Show content warning
	 *
	 * If a show has a content warning, let's show it.
	 *
	 * @access public
	 * @return array
	 */
	public function make( $show_id ) {

		$warning_array = array(
			'card'    => 'none',
			'content' => 'none',
		);

		// If there's no post ID passed or it's not a show, we show nothing.
		if ( is_null( $show_id ) || get_post_type( $show_id ) !== 'post_type_shows' ) {
			return $warning_array;
		}

		$trigger_terms            = get_the_terms( $show_id, 'lez_triggers' );
		$trigger                  = ( ! empty( $trigger_terms ) && ! is_wp_error( $trigger_terms ) ) ? $trigger_terms[0]->slug : get_post_meta( $show_id, 'lezshows_triggerwarning', true );
		$warning_array['content'] = ( ! empty( $trigger_terms ) && ! is_wp_error( $trigger_terms ) ) ? term_description( $trigger_terms[0]->term_id ) : '<strong>WARNING</strong> This show may be upsetting to watch.';

		switch ( $trigger ) {
			case 'on':
			case 'high':
				$warning_array['card'] = 'danger';
				break;
			case 'med':
			case 'medium':
				$warning_array['card'] = 'warning';
				break;
			case 'low':
				$warning_array['card'] = 'info';
				break;
			default:
				$warning_array['card']    = 'none';
				$warning_array['content'] = 'none';
		}

		return $warning_array;
	}
}
