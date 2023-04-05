<?php
/*
 * Name: Show Columns
 * Desc: Custom columns on post-list pages for TV shows
 */

class LWTV_CPT_Show_Columns {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}

	/**
	 * Admin Init
	 */
	public function admin_init() {
		add_filter( 'manage_post_type_shows_posts_columns', array( $this, 'manage_posts_columns' ) );
		add_action( 'manage_post_type_shows_posts_custom_column', array( $this, 'manage_posts_custom_column' ), 10, 2 );
		add_filter( 'manage_edit-post_type_shows_sortable_columns', array( $this, 'manage_edit_sortable_columns' ) );

		add_action( 'pre_get_posts', array( $this, 'columns_sortability_simple' ) );
		add_filter( 'posts_clauses', array( $this, 'columns_sortability_format' ), 10, 2 );
	}

	/*
	 * Create Custom Columns
	 * Used by quick edit, etc
	 */
	public function manage_posts_columns( $columns ) {
		$columns['shows-airdate']    = 'Airdates';
		$columns['shows-worthit']    = 'Worth It?';
		$columns['shows-queercount'] = '#';
		return $columns;
	}

	/*
	 * Add Custom Column Content
	 */
	public function manage_posts_custom_column( $column, $post_id ) {
		if ( get_post_meta( $post_id, 'lezshows_airdates', true ) ) {
			$airdates = get_post_meta( $post_id, 'lezshows_airdates', true );
			$airdate  = $airdates['start'] . ' - ' . $airdates['finish'];
			if ( $airdates['start'] === $airdates['finish'] ) {
				$airdate = $airdates['finish'];
			}
		} else {
			$airdate = 'N/A';
		}

		switch ( $column ) {
			case 'shows-airdate':
				$output = $airdate;
				break;
			case 'shows-worthit':
				$output = ucfirst( get_post_meta( $post_id, 'lezshows_worthit_rating', true ) );
				break;
			case 'shows-queercount':
				$output = count( get_post_meta( $post_id, 'lezshows_char_list', true ) );
				break;
			default:
				$output = '';
		}

		echo wp_kses_post( $output );
	}

	/*
	 * Make Custom Columns Sortable
	 */
	public function manage_edit_sortable_columns( $columns ) {
		unset( $columns['cpt-airdate'] );             // Don't allow sort by airdates
		$columns['taxonomy-lez_formats'] = 'format';  // Allow sort by show format
		$columns['shows-worthit']        = 'worth';   // Allow sort by worth
		$columns['shows-queercount']     = 'queers';  // Allow sort by queers
		return $columns;
	}

	/*
	 * Create Simple Columns Sortability
	 *
	 * Worth It, Char Count
	 */
	public function columns_sortability_simple( $query ) {
		if ( ! is_admin() ) {
			return;
		}

		if ( $query->is_main_query() && isset( $orderby ) && $orderby === $query->get( 'orderby' ) ) {
			switch ( $orderby ) {
				case 'worth':
					$query->set( 'meta_key', 'lezshows_worthit_rating' );
					$query->set( 'orderby', 'meta_value' );
					break;
				case 'queers':
					$query->set( 'meta_key', 'lezshows_char_count' );
					$query->set( 'orderby', 'meta_value_num' );
					break;
			}
		}
	}

	/*
	 * Create columns sortability for show Formats
	 */
	public function columns_sortability_format( $clauses, $wp_query ) {
		global $wpdb;

		if ( isset( $wp_query->query['orderby'] ) && 'format' === $wp_query->query['orderby'] ) {

			$clauses['join'] .= <<<SQL
LEFT OUTER JOIN {$wpdb->term_relationships} ON {$wpdb->posts}.ID={$wpdb->term_relationships}.object_id
LEFT OUTER JOIN {$wpdb->term_taxonomy} USING (term_taxonomy_id)
LEFT OUTER JOIN {$wpdb->terms} USING (term_id)
SQL;

			$clauses['where']   .= " AND (taxonomy = 'lez_formats' OR taxonomy IS NULL)";
			$clauses['groupby']  = 'object_id';
			$clauses['orderby']  = "GROUP_CONCAT({$wpdb->terms}.name ORDER BY name ASC) ";
			$clauses['orderby'] .= ( 'ASC' === strtoupper( $wp_query->get( 'order' ) ) ) ? 'ASC' : 'DESC';
		}

		return $clauses;
	}

}

new LWTV_CPT_Show_Columns();
