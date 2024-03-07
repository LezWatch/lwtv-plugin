<?php
/*
 * FacetWP Pagination
 */

namespace LWTV\Plugins\FacetWP;

class Pagination {

	/**
	 * Constructor
	 */
	public function __construct() {
		// Filter paged output
		add_filter( 'facetwp_pager_html', array( $this, 'facetwp_pager_html' ), 10, 2 );

		// Javascript
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
	}

	/**
	 * Enqueue Scripts on archive pages only.
	 * @return void
	 */
	public function wp_enqueue_scripts() {
		if ( is_archive() ) {
			wp_enqueue_script( 'facetwp-pagination', LWTV_PLUGIN_URL . '/assets/js/facetwp-pagination.js', array(), LWTV_PLUGIN_VERSION, true );
		}
	}

	/**
	 * Only show pagination if there's more than one page
	 * Credit: https://gist.github.com/mgibbs189/69176ef41fa4e26d1419
	 */
	public function facetwp_pager_html( $output, $params ) {
		$output      = '';
		$page        = (int) $params['page'];
		$total_pages = (int) $params['total_pages'];

		// Only show pagination when > 1 page
		if ( 1 < $total_pages ) {

			if ( 1 < $page ) {
				$output .= '<a class="facetwp-page" data-page="' . ( $page - 1 ) . '">&laquo; Previous</a>';
			}
			if ( 3 < $page ) {
				$output .= '<a class="facetwp-page first-page" data-page="1">1</a>';
				$output .= ' <span class="dots">…</span> ';
			}
			for ( $i = 2; $i > 0; $i-- ) {
				if ( 0 < ( $page - $i ) ) {
					$output .= '<a class="facetwp-page" data-page="' . ( $page - $i ) . '">' . ( $page - $i ) . '</a>';
				}
			}

			// Current page
			$output .= '<a class="facetwp-page active" data-page="' . $page . '">' . $page . '</a>';

			for ( $i = 1; $i <= 2; $i++ ) {
				if ( $total_pages >= ( $page + $i ) ) {
					$output .= '<a class="facetwp-page" data-page="' . ( $page + $i ) . '">' . ( $page + $i ) . '</a>';
				}
			}
			if ( $total_pages > ( $page + 2 ) ) {
				$output .= ' <span class="dots">…</span> ';
				$output .= '<a class="facetwp-page last-page" data-page="' . $total_pages . '">' . $total_pages . '</a>';
			}
			if ( $page < $total_pages ) {
				$output .= '<a class="facetwp-page" data-page="' . ( $page + 1 ) . '">Next &raquo;</a>';
			}
		}

		return $output;
	}
}
