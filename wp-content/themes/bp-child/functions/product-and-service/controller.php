<?php

add_action('init', 'process_product_service_actions');
function process_product_service_actions()
{
    $action = isset($_REQUEST['_psnonce']) ? $_REQUEST['_psnonce'] : null;
    if(wp_verify_nonce($action, 'save-product-service')){
        saveProductService();        
    }else if(wp_verify_nonce($action, 'delete-product')){
        deleteProductService();    
    }
}


function saveProductService()
{
    global $wpdb;
    
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
        
    //Update Permalink    
//    wp_update_post(array('ID' => $id, 'guid' => get_site_url() . "?post_type=product-service&p=" . $id ));
    
    update_post_meta($id, 'product_name', $_POST['product_name']);
    update_post_meta($id, 'product_release_date', date('Y-m-d', strtotime($_POST['product_release_date'])));
    update_post_meta($id, 'product_type', $_POST['product_type']);
    update_post_meta($id, 'product_version', $_POST['product_version']);
    update_post_meta($id, 'product_url', $_POST['product_url']);
    update_post_meta($id, 'product_description', $_POST['product_description']);
    
    //Save Related Products
    $related_products = isset($_POST['related-product']) ? $_POST['related-product'] : array();
    $related_products_relations = isset($_POST['related-product-relation']) ? $_POST['related-product-relation'] : array();
    
    //remove old entries
    $query = $wpdb->prepare("DELETE FROM " . $wpdb->prefix . "products_relationships WHERE product_id=%d", $id);
    $wpdb->query($query);
    
    foreach($related_products as $i => $p)
    {
        if(!$p)
            continue;
        $wpdb->insert($wpdb->prefix . "products_relationships", array('product_id' => $id, 'related_product_id' => $p, 'relationship' => $related_products_relations[$i]));
    }
        
    addMessage('Product / Service was saved successfully');
    wp_redirect(get_permalink($id));
    exit;
}

function deleteProductService()
{
    global $wpdb;
            
    $id = $_REQUEST['id'];
    
    $product = get_post($id );
    
    if(!$product)
    {
        addMessage('Invalid Request!', 'error');
        return;
    }
    
    $return = isset($_REQUEST['return']) ? base64_decode($_REQUEST['return']) : "/";
    
    if(!can_delete_product_and_service($product->ID))
    {
        addMessage('Permission Denied!', 'error');
        wp_redirect($return);
        exit;
    }
    
    //Check if the product has cliams or not
    $query = $wpdb->prepare("SELECT count(1) FROM " . $wpdb->prefix . "compliance_claims WHERE product_id=%d", $id);
    $count = $wpdb->get_var($query);
    if($count > 0)
    {
        addMessage("You can't delete the product/service, because it includes claims.", "error");
        wp_redirect($return);
        exit;
    }
    
    //Delete Product/Service
    wp_trash_post($id);
    addMessage("The product/service was deleted!");
    wp_redirect($return);
    exit;
}