<?php

add_action('init', 'process_product_service_actions');
function process_product_service_actions()
{
    $action = isset($_POST['_wpnonce']) ? $_POST['_wpnonce'] : null;
    if(wp_verify_nonce($action, 'save-product-service')){
        saveProductService();        
    }
}


function saveProductService()
{
    $id = $_POST['id'];
    if(!$id)
        $isNew = true;
    else
        $isNew = false;
    
    if(($isNew && !can_create_product_and_service()) || (!$isNew && !can_edit_product_and_service($id)))
    {
        addMessage('Permission Denied!', 'error');
        wp_redirect(get_site_url());
        exit;
    }
    
    if($isNew)
    {
        $id = wp_insert_post(array('post_title' => $_POST['product_name'], 'post_type'=>'product-service', 'post_status' => 'publish'), true);
        if(is_wp_error($id))
        {
            addMessage($id->get_error_message(), 'error');            
            return;
        }    
    }else{
        if(!wp_update_post(array('ID' => $id, 'post_title' =>$_POST['product_name'], 'post_name' => sanitize_title($_POST['product_name']))))
        {
            addMessage('There was an error while updating the test suite.', true);
            return;
        }
    }
    
    update_post_meta($id, 'product_name', $_POST['product_name']);
    update_post_meta($id, 'product_date', date('Y-m-d', strtotime($_POST['product_date'])));
    update_post_meta($id, 'product_type', $_POST['product_type']);
    update_post_meta($id, 'product_owner', $_POST['product_owner']);
    update_post_meta($id, 'product_url', $_POST['product_url']);
    update_post_meta($id, 'product_status', $_POST['product_status']);
    update_post_meta($id, 'product_description', $_POST['product_description']);
    
    //Getting Community IDs
    $groupID = array();
    
    //Save Test Suites
    $test_suite = isset($_POST['test_suites']) ? $_POST['test_suites'] : array();
    delete_post_meta($id, 'test_suites');
    foreach($test_suite as $ts){
        add_post_meta($id, 'test_suites', $ts);
        $groupID[] = get_post_meta($ts, 'community_id', true);
    }
    $groupID = array_unique($groupID);
    //Update Product&Service Community IDs
    delete_post_meta($id, 'community_id');
    foreach($groupID as $gid)
    {
        if(!$gid)
            continue;
        add_post_meta($id, 'community_id', $gid);
    }
    //Save Related Products
    $related_products = isset($_POST['related_products']) ? $_POST['related_products'] : array();
    
    delete_post_meta($id, 'related_products');    
    foreach($related_products as $rp)
        add_post_meta($id, 'related_products', $rp);
        
    addMessage('Product / Service was saved successfully');
    wp_redirect(get_permalink($id));
    exit;
}