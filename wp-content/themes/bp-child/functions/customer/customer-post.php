<?php

// Register Customer Post Type
function register_customer_post_type() {

    $labels = array(
        'name'                => _x( 'Our Customers', 'Post Type General Name', 'luxtravel' ),
        'singular_name'       => _x( 'Customer', 'Post Type Singular Name', 'luxtravel' ),
        'menu_name'           => __( 'Customers', 'luxtravel' ),
        'all_items'           => __( 'All Customers', 'luxtravel' ),
        'view_item'           => __( 'View Customer', 'luxtravel' ),
        'add_new_item'        => __( 'Add New Customer', 'luxtravel' ),
        'edit_item'           => __( 'Edit Customer', 'luxtravel' ),
        'search_items'        => __( 'Search for Customers', 'luxtravel' ),
    );
    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'has_archive'         => true,
        'show_in_nav_menus'   => true,
        'rewrite'             => array('slug'=>'customers', 'with_front' => true),
        'exclude_from_search' => true,
        'supports'            => array('title', 'thumbnail'),
    );
    register_post_type( 'customer', $args );

}

// Hook into the 'init' action
add_action( 'init', 'register_customer_post_type', 0 );

add_filter('manage_customer_posts_columns', 'customers_columns', 5);
add_action('manage_customer_posts_custom_column', 'customers_custom_columns', 5, 2);
// ADD NEW COLUMN
function customers_columns($defaults) {
    $defaults['featured_image'] = 'Customer Logo';
    return $defaults;
}

// SHOW THE FEATURED IMAGE
function customers_custom_columns($column_name, $post_ID) {
    if ($column_name == 'featured_image') {
        $post_featured_image = get_customer_logo($post_ID);
        if ($post_featured_image) {
            echo '<img src="' . $post_featured_image . '" />';
        }
    }
}

function get_customer_logo($post_ID) {
    $post_thumbnail_id = get_post_thumbnail_id($post_ID);
    if ($post_thumbnail_id) {
        $post_thumbnail_img = wp_get_attachment_image_src($post_thumbnail_id, 'featured_preview');
        return $post_thumbnail_img[0];
    }
}