<?php
/**
 * Management Test Patterns
 */
add_action('init', 'create_post_type_test_pattern'); // Add Test Pattern Post Type

/*------------------------------------*\
	Post Type Test Pattern
\*------------------------------------*/
function create_post_type_test_pattern()
{
    register_post_type('test-pattern',
        array(
            'labels' => array(
                'name' => __('Test Patterns', 'complincetest'),
                'singular_name' => __('Test Pattern', 'complincetest'),
                'add_new' => __('Add New Test Pattern', 'complincetest'),
                'add_new_item' => __('Add New Test Pattern', 'complincetest'),
                'edit' => __('Edit Test Pattern', 'complincetest'),
                'edit_item' => __('Edit Test Pattern', 'complincetest'),
                'new_item' => __('New Test Pattern', 'complincetest'),
                'view' => __('View Test Pattern', 'complincetest'),
                'view_item' => __('View Test Pattern', 'complincetest'),
                'search_items' => __('Search Test Pattern', 'complincetest'),
                'not_found' => __('No Test Patterns found', 'complincetest'),
                'not_found_in_trash' => __('No Test Patterns found in Trash', 'complincetest')
            ),
            'menu_icon'=>'dashicons-admin-generic',
            'menu_position' => 80,
            'public' => false,
            'show_ui' => true,
            'hierarchical' => false, // Allows your posts to behave like Hierarchy Pages
            'has_archive' => true,
            'supports' => array(
                'title',
            ),
            'can_export' => true, // Allows export in Tools > Export
        ));
}

add_action('admin_init', 'add_metaboxes_for_test_pattern');
function add_metaboxes_for_test_pattern()
{
    // Test Pattern Number
    add_meta_box("test_pattern_number_metabox", "Test Pattern Number", 'test_pattern_number_metabox_html', "test-pattern", "side", "core");
    // Test Pattern Description
    add_meta_box("test_pattern_description_metabox", "Description", 'test_pattern_description_metabox_html', "test-pattern", "normal", "high");

}

//Initiating Message
function test_pattern_description_metabox_html(){
    global $post;

    $current_test_pattern_description = cp_get_post_meta($post->ID, 'test_pattern_description', true);
    ?>
    <textarea name="test_pattern_description" id="test_pattern_description_id" rows="4" cols="100"><?php echo $current_test_pattern_description;?></textarea>
<?php

}

function test_pattern_number_metabox_html()
{
    global $post;

    $test_pattern_number = cp_get_post_meta($post->ID, 'test_pattern_number', true);

    ?>
    <div>
        <input type="text" name="test_pattern_number" value="<?php echo $test_pattern_number?>" />
    </div>
<?php
}


//Save Test Cases
add_action('save_post', 'save_test_pattern_on_admin');
function save_test_pattern_on_admin($post_id)
{
    cp_update_post_meta($post_id, 'test_pattern_number', $_POST['test_pattern_number']);
    cp_update_post_meta($post_id, 'test_pattern_description', $_POST['test_pattern_description']);
}



add_action( 'admin_enqueue_scripts', 'disable_autosave_for_test_patterns' );
function disable_autosave_for_test_patterns() {
    if ( 'test-pattern' == get_post_type() )
        wp_dequeue_script( 'autosave' );
}


// Add to admin_init function
add_filter('manage_edit-test-pattern_columns', 'add_new_test_pattern_columns');

function add_new_test_pattern_columns($test_pattern_columns) {
    $new_columns['cb'] = '<input type="checkbox" />';

    $new_columns['pattern_number'] = __('№');
    $new_columns['title'] = __('Test Pattern Title');

    return $new_columns;
}

// Add to admin_init function
function manage_test_pattern_columns($column_name, $post_id) {
    switch ($column_name) {
        case 'pattern_number':
            echo $test_pattern_number = cp_get_post_meta($post_id, 'test_pattern_number', true);
            break;

        default:
            break;
    } // end switch
}
add_action('manage_test-pattern_posts_custom_column', 'manage_test_pattern_columns', 10, 2);

function custom_test_pattern_columns_width() {
    echo '<style type="text/css">
           .column-pattern_number{width: 5%; white-space: nowrap;}
         </style>';
}

add_action('admin_head', 'custom_test_pattern_columns_width');

// Register the column as sortable
function test_pattern_number_column_register_sortable( $columns ) {
    $columns['pattern_number'] = 'pattern_number';

    return $columns;
}
add_filter( 'manage_edit-test-pattern_sortable_columns', 'test_pattern_number_column_register_sortable' );

// Make column sortable
function test_pattern_number_column_orderby( $vars ) {
    if ( isset( $vars['orderby'] ) && 'pattern_number' == $vars['orderby'] ) {
        $vars = array_merge( $vars, array(
            'meta_key' => 'test_pattern_number',
            'orderby' => 'meta_value_num'
        ) );
    }

    return $vars;
}
add_filter( 'request', 'test_pattern_number_column_orderby' );

//Get all Test Patterns
function get_test_patterns_number(){
   $args = array(
       'post_type' => 'test-pattern',
       'post_status' => 'publish',
       'posts_per_page'   => -1,
       'meta_key' => 'test_pattern_number',
       'orderby' => 'meta_value',
       'order'            => 'ASC',
   );
   $test_patterns = get_posts( $args );

   return $test_patterns;
}