<?php
/*
Custom Taxonomy for cliches
*/

// Register Custom Taxonomy
function show_cliche_taxonomy() {

        $labels = array(
                'name'                       => _x( 'Show Clichés', 'Taxonomy General Name', 'lezwatchtv' ),
                'singular_name'              => _x( 'Cliché', 'Taxonomy Singular Name', 'lezwatchtv' ),
                'menu_name'                  => __( 'Clichés', 'lezwatchtv' ),
                'all_items'                  => __( 'All Clichés', 'lezwatchtv' ),
                'parent_item'                => __( 'Parent Cliché', 'lezwatchtv' ),
                'parent_item_colon'          => __( 'Parent Cliché:', 'lezwatchtv' ),
                'new_item_name'              => __( 'New Cliché', 'lezwatchtv' ),
                'add_new_item'               => __( 'Add New Cliché', 'lezwatchtv' ),
                'edit_item'                  => __( 'Edit Cliché', 'lezwatchtv' ),
                'update_item'                => __( 'Update Cliché', 'lezwatchtv' ),
                'separate_items_with_commas' => __( 'Separate cliché names with commas', 'lezwatchtv' ),
                'search_items'               => __( 'Search Clichés', 'lezwatchtv' ),
                'add_or_remove_items'        => __( 'Add or remove clichés', 'lezwatchtv' ),
                'choose_from_most_used'      => __( 'Choose from the most used cliché name', 'lezwatchtv' ),
                'not_found'                  => __( 'Not Found', 'lezwatchtv' ),
        );
        $args = array(
                'labels'                     => $labels,
                'hierarchical'               => false,
                'public'                     => true,
                'show_ui'                    => true,
                'show_admin_column'          => true,
                'show_in_nav_menus'          => true,
                'show_tagcloud'              => false,
                'rewrite' 				  	 => array( 'slug' => 'cliches' ),
        );
        register_taxonomy( 'lez_cliches', array( 'post' ), $args );

}

// Hook into the 'init' action
add_action( 'init', 'show_cliche_taxonomy', 0 );
