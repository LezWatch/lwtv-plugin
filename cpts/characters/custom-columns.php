<?php
/*
 * Name: Character Columns
 * Desc: Custom columns on post-list pages for characters
 */

class LWTV_CPT_Char_Columns {

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
		add_filter( 'manage_post_type_characters_posts_columns', array( $this, 'manage_posts_columns' ) );
		add_action( 'manage_post_type_characters_posts_custom_column', array( $this, 'manage_posts_custom_column' ), 10, 2 );
		add_filter( 'manage_edit-post_type_characters_sortable_columns', array( $this, 'manage_edit_sortable_columns' ) );

		add_filter( 'posts_clauses', array( $this, 'columns_sortability_sexuality' ), 10, 2 );
		add_filter( 'posts_clauses', array( $this, 'columns_sortability_gender' ), 10, 2 );
		add_filter( 'posts_clauses', array( $this, 'columns_sortability_romantic' ), 10, 2 );

	}

	/*
	 * Create Custom Columns
	 * Used by quick edit, etc
	 */
	public function manage_posts_columns( $columns ) {
		$columns['cpt-shows']         = 'TV Show(s)';
		$columns['postmeta-roletype'] = 'Role Type';
		$columns['postmeta-death']    = 'Died';
		return $columns;
	}

	/*
	 * Add Custom Column Content
	 */
	public function manage_posts_custom_column( $column, $post_id ) {

		$character_show_ids = get_post_meta( $post_id, 'lezchars_show_group', true );
		$show_title         = array();
		$role_array         = array();

		if ( '' !== $character_show_ids ) {
			foreach ( $character_show_ids as $each_show ) {

				$show = get_the_title( $each_show['show'] );
				$role = ( isset( $each_show['type'] ) ) ? ucfirst( $each_show['type'] ) : 'ERROR';

				array_push( $show_title, $show );
				array_push( $role_array, $role );
			}
		}

		$character_death = get_post_meta( $post_id, 'lezchars_death_year', true );

		if ( empty( $character_death ) ) {
			$character_death = array( 'Alive' );
		}

		switch ( $column ) {
			case 'cpt-shows':
				$output = implode( ', ', $show_title );
				break;
			case 'postmeta-roletype':
				$output = implode( ', ', $role_array );
				break;
			case 'postmeta-death':
				$output = implode( ', ', $character_death );
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
		unset( $columns['cpt-shows'] );                  // Don't allow sort by shows
		unset( $columns['postmeta-roletype'] );          // Don't allow sort by role
		$columns['taxonomy-lez_gender']    = 'gender';   // Allow sort by gender identity
		$columns['taxonomy-lez_sexuality'] = 'sex';      // Allow sort by gender identity
		$columns['taxonomy-lez_romantic']  = 'romantic'; // Allow sort by gender identity
		return $columns;
	}

	/*
	 * Create columns sortability for gender
	 */
	public function columns_sortability_gender( $clauses, $wp_query ) {
		global $wpdb;

		if ( isset( $wp_query->query['orderby'] ) && 'gender' === $wp_query->query['orderby'] ) {

			$clauses['join'] .= <<<SQL
LEFT OUTER JOIN {$wpdb->term_relationships} ON {$wpdb->posts}.ID={$wpdb->term_relationships}.object_id
LEFT OUTER JOIN {$wpdb->term_taxonomy} USING (term_taxonomy_id)
LEFT OUTER JOIN {$wpdb->terms} USING (term_id)
SQL;

			$clauses['where']   .= " AND (taxonomy = 'lez_gender' OR taxonomy IS NULL)";
			$clauses['groupby']  = 'object_id';
			$clauses['orderby']  = "GROUP_CONCAT({$wpdb->terms}.name ORDER BY name ASC) ";
			$clauses['orderby'] .= ( 'ASC' === strtoupper( $wp_query->get( 'order' ) ) ) ? 'ASC' : 'DESC';
		}
		return $clauses;
	}

	/*
	 * Create columns sortability for sexuality
	 */
	public function columns_sortability_sexuality( $clauses, $wp_query ) {

		global $wpdb;

		if ( isset( $wp_query->query['orderby'] ) && 'sex' === $wp_query->query['orderby'] ) {

			$clauses['join'] .= <<<SQL
LEFT OUTER JOIN {$wpdb->term_relationships} ON {$wpdb->posts}.ID={$wpdb->term_relationships}.object_id
LEFT OUTER JOIN {$wpdb->term_taxonomy} USING (term_taxonomy_id)
LEFT OUTER JOIN {$wpdb->terms} USING (term_id)
SQL;

			$clauses['where']   .= " AND (taxonomy = 'lez_sexuality' OR taxonomy IS NULL)";
			$clauses['groupby']  = 'object_id';
			$clauses['orderby']  = "GROUP_CONCAT({$wpdb->terms}.name ORDER BY name ASC) ";
			$clauses['orderby'] .= ( 'ASC' === strtoupper( $wp_query->get( 'order' ) ) ) ? 'ASC' : 'DESC';
		}

		return $clauses;
	}

	/*
	 * Create columns sortability for romantic
	 */
	public function columns_sortability_romantic( $clauses, $wp_query ) {

		global $wpdb;

		if ( isset( $wp_query->query['orderby'] ) && 'sex' === $wp_query->query['orderby'] ) {

			$clauses['join'] .= <<<SQL
LEFT OUTER JOIN {$wpdb->term_relationships} ON {$wpdb->posts}.ID={$wpdb->term_relationships}.object_id
LEFT OUTER JOIN {$wpdb->term_taxonomy} USING (term_taxonomy_id)
LEFT OUTER JOIN {$wpdb->terms} USING (term_id)
SQL;

			$clauses['where']   .= " AND (taxonomy = 'lez_romantic' OR taxonomy IS NULL)";
			$clauses['groupby']  = 'object_id';
			$clauses['orderby']  = "GROUP_CONCAT({$wpdb->terms}.name ORDER BY name ASC) ";
			$clauses['orderby'] .= ( 'ASC' === strtoupper( $wp_query->get( 'order' ) ) ) ? 'ASC' : 'DESC';
		}

		return $clauses;
	}
}

new LWTV_CPT_Char_Columns();
