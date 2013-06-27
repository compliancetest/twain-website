<?php
/**
* Products & Service Section
*/
function add_products_and_services_metaboxes(){
    add_meta_box("test_suites_metabox", "Select Certifications (Test Suites) ", 'products_and_services_test_suites_metabox_html', "product-service", "normal", "high");
    add_meta_box("related_products_metabox", "Select Related Products / Services ", 'products_and_services_related_products_metabox_html', "product-service", "normal", "high");
}

add_action('admin_init', 'add_products_and_services_metaboxes');


function products_and_services_test_suites_metabox_html(){
    global $post;
    
    //Get Current Test Suites
    $current_test_suite = _get_certified_test_suites($post->ID);
    
    $testsuites = get_posts( array( 'post_type' => 'test-suite', 'posts_per_page' => -1) );
    
    foreach($testsuites as $row){
    ?>
         
         <input type="checkbox" name="test_suites[]" <?php if (in_array($row->ID , $current_test_suite)) { echo 'checked="checked"'; } ?> value="<?php echo $row->ID; ?>" style="margin-right: 5px; margin-bottom: 5px;"><?php echo $row->post_title; ?> <br />
        
    <?php
    }    
    
}


function products_and_services_related_products_metabox_html(){
    global $post;
    
    $current_products = _get_current_related_products($post->ID);
    
    $products = get_posts( array( 'post_type' => 'product-service', 'posts_per_page' => -1, 'post__not_in' =>array($post->ID)) );
    
    foreach($products as $row){
         ?>
         
         <input type="checkbox" name="related_products[]" <?php if (in_array($row->ID, $current_products)) { echo 'checked="checked"'; } ?> value="<?php echo $row->ID; ?>" style="margin-right: 5px; margin-bottom: 5px;"><?php echo $row->post_title; ?> <br />
        
        <?php
    }
    
}


//Save Product and service on admin
add_action('save_post', 'save_product_and_service_on_admin');

function save_product_and_service_on_admin($post_id) {
    global $post;
    
    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }
    // check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }
    
    //Check Post Type
    if($post->post_type != 'product-service')
    {
        return $post_id;
    }
    
    if( wp_is_post_revision( $post_id ) )
    {
        return $post_id;
    }
    
    //Getting Community IDs
    $groupID = array();
    
    //Save Test Suites
    $test_suite = isset($_POST['test_suites']) ? $_POST['test_suites'] : array();
    delete_post_meta($post_id, 'test_suites');
    foreach($test_suite as $ts){
        add_post_meta($post_id, 'test_suites', $ts);
        $groupID[] = get_post_meta($ts, 'community_id', true);
    }
    $groupID = array_unique($groupID);
    //Update Product&Service Community IDs
    delete_post_meta($post_id, 'community_id');
    foreach($groupID as $gid)
    {
        if(!$gid)
            continue;
        
        add_post_meta($post_id, 'community_id', $gid);
    }
    
    //Save Related Products
    $related_products = isset($_POST['related_products']) ? $_POST['related_products'] : null;
    delete_post_meta($post_id, 'related_products');    
    foreach($related_products as $rp)
        add_post_meta($post_id, 'related_products', $rp);
    
} 

function _get_current_related_products($pid)
{
    $rows = get_post_meta($pid, 'related_products');

    return $rows;
}

function _get_certified_test_suites($pid)
{
    $rows = get_post_meta($pid, 'test_suites');
        
    return $rows;
}

