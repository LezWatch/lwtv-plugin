<?php

/*
Custom Taxonomy for Realness Scores
*/

// Register Custom Taxonomy
function lez_realnessscore_taxonomy() {

        $labels = array(
                'name'                       => _x( 'Realness Score', 'Taxonomy General Name', 'lezwatchtv' ),
                'singular_name'              => _x( 'Realness Score', 'Taxonomy Singular Name', 'lezwatchtv' ),
                'menu_name'                  => __( 'Realness Score', 'lezwatchtv' ),
                'all_items'                  => __( 'All Realness Score', 'lezwatchtv' ),
                'parent_item'                => __( 'Parent Score', 'lezwatchtv' ),
                'parent_item_colon'          => __( 'Parent Score:', 'lezwatchtv' ),
                'new_item_name'              => __( 'New Score', 'lezwatchtv' ),
                'add_new_item'               => __( 'Add New Score', 'lezwatchtv' ),
                'edit_item'                  => __( 'Edit Score', 'lezwatchtv' ),
                'update_item'                => __( 'Update Score', 'lezwatchtv' ),
                'separate_items_with_commas' => __( 'Separate scores with commas', 'lezwatchtv' ),
                'search_items'               => __( 'Search scores', 'lezwatchtv' ),
                'add_or_remove_items'        => __( 'Add or remove scored', 'lezwatchtv' ),
                'choose_from_most_used'      => __( 'Choose from the most used score', 'lezwatchtv' ),
                'not_found'                  => __( 'Not Found', 'lezwatchtv' ),
        );
        $args = array(
                'labels'                     => $labels,
                'hierarchical'               => false,
                'public'                     => true,
                'show_ui'                    => false,
                'show_admin_column'          => true,
                'show_in_nav_menus'          => true,
                'show_tagcloud'              => false,
                'rewrite' 					 => array( 'slug' => 'realness' ),
        );
        register_taxonomy( 'lez_realnessscore', array( 'post' ), $args );

        wp_insert_term('1', 'lez_realnessscore');
        wp_insert_term('2', 'lez_realnessscore');
        wp_insert_term('3', 'lez_realnessscore');
        wp_insert_term('4', 'lez_realnessscore');
        wp_insert_term('5', 'lez_realnessscore');
}

// Hook into the 'init' action
add_action( 'init', 'lez_realnessscore_taxonomy', 0 );


function add_lez_realnessscore_box() {
    add_meta_box('lez_realnessscore_score_box_ID', __('Realness Score'), 'lez_realnessscore_styling_function', 'post', 'side', 'core');
}   
 
function add_lez_realnessscore_menus() {
 
    if ( ! is_admin() )
        return;
 
    add_action('admin_menu', 'add_lez_realnessscore_box');

    /* Use the save_post action to save new post data */
    add_action('save_post', 'save_taxonomy_data');
}
 
add_lez_realnessscore_menus();

// This function gets called in edit-form-advanced.php
function lez_realnessscore_styling_function($post) {
 
    echo '<input type="hidden" name="taxonomy_noncename" id="taxonomy_noncename" value="' . 
            wp_create_nonce( 'taxonomy_lez_realnessscore' ) . '" />';
 
     
    // Get all realness_scores terms
    $realness_scores = get_terms('lez_realnessscore', 'hide_empty=0'); 
 
?>
<select name='post_lez_realnessscore' id='post_lez_realnessscore'>
    <!-- Display themes as options -->
    <?php 
        $names = wp_get_object_terms($post->ID, 'lez_realnessscore'); 
        ?>
        <option class='lez_realnessscore-option' value=''
        <?php if (!count($names)) echo "selected";?>>None</option>
        <?php
    foreach ($realness_scores as $realness) {
        if (!is_wp_error($names) && !empty($names) && !strcmp($realness->slug, $names[0]->slug)) 
            echo "<option class='realness-option' value='" . $realness->slug . "' selected>" . $realness->name . "</option>\n"; 
        else
            echo "<option class='realness-option' value='" . $realness->slug . "'>" . $realness->name . "</option>\n"; 
    }
   ?>
</select>    
<?php
}

function remove_original_lez_realnessscore_metabox() {
	remove_meta_box('tagsdiv-lez_realnessscore','post','core');
}

add_action( 'do_meta_boxes' , 'remove_original_lez_realnessscore_metabox' );

function save_taxonomy_data($post_id) {
// verify this came from our screen and with proper authorization.
 
    if ( !wp_verify_nonce( $_POST['taxonomy_noncename'], 'taxonomy_lez_realnessscore' )) {
        return $post_id;
    }
 
    // verify if this is an auto save routine. If it is our form has not been submitted, so we dont want to do anything
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
        return $post_id;
 
   
    // Check permissions
    if ( 'page' == $_POST['post_type'] ) {
        if ( !current_user_can( 'edit_page', $post_id ) )
            return $post_id;
    } else {
        if ( !current_user_can( 'edit_post', $post_id ) )
        return $post_id;
    }
 
    // OK, we're authenticated: we need to find and save the data
    $post = get_post($post_id);
    if (($post->post_type == 'post') || ($post->post_type == 'page')) { 
           // OR $post->post_type != 'revision'
           $realness = $_POST['post_lez_realnessscore'];
       wp_set_object_terms( $post_id, $realness, 'lez_realnessscore' );
        }
    return $realness;
 
}
