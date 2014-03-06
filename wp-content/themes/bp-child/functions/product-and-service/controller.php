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
    
    $id = htmlspecialchars($_POST['id']);
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
        $id = wp_insert_post(array('post_title' => htmlspecialchars($_POST['product_name']), 'post_type'=>'product-service', 'post_status' => 'publish'), true);
        if(is_wp_error($id))
        {
            addMessage($id->get_error_message(), 'error');            
            return;
        }
    }else{
        if(!wp_update_post(array('ID' => $id, 'post_title' =>htmlspecialchars($_POST['product_name']), 'post_name' => sanitize_title(htmlspecialchars($_POST['product_name'])))))
        {
            addMessage('There was an error while updating the test suite.', true);
            return;
        }
    }

    $product_url = sanitize_url($_POST['product_url']);

    if(!preg_match('^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?^', $product_url)){
        addMessage('URL not valid', 'error');
        return;
    }

    if(!preg_match('@^[0-9]{4}-[0-9]{2}-[0-9]{2}$@', $_POST['product_release_date'])){
        addMessage('Date not valid', 'error');
        return;
    }

    //Update Product ID
    $product_id = htmlspecialchars($_POST['product_id']);
    if(!$product_id)
    {        
        //Generate Product ID        
        $product_slug = sanitize_title(htmlspecialchars($_POST['product_name']));
        $product_id = sanitize_title($_POST['product_owner']) . "." . $product_slug .  ".v" . $_POST['product_version'];
    }
    
    //Check Product ID duplication
    $query = $wpdb->prepare("SELECT meta_id FROM $wpdb->postmeta WHERE post_id!=%d AND meta_key='product_id' AND meta_value=%s", $id, $product_id);
    $meta_id = $wpdb->get_var($query);
    
    $idx = 2;
    $t_product_id = $product_id;
    while($meta_id)
    {
        $t_product_id = $product_id . "-" .$idx;
        $query = $wpdb->prepare("SELECT meta_id FROM $wpdb->postmeta WHERE post_id!=%d AND meta_key='product_id' AND meta_value=%s", $id, $t_product_id);
        $meta_id = $wpdb->get_var($query);    
    }
    $product_id = $t_product_id;
    
    update_post_meta($id, 'product_id', $product_id);
    
    //Update Product Name ID Map Table on ESB
    $esb = new ManageESB();
    $esb->saveProductInfo($id, $product_id, $_POST['product_name']);
    
    update_post_meta($id, 'product_name', htmlspecialchars($_POST['product_name']));
    update_post_meta($id, 'product_release_date', date('Y-m-d H:i:s', getUTCTimeStamp(htmlspecialchars($_POST['product_release_date']))));
    update_post_meta($id, 'product_type', htmlspecialchars($_POST['product_type']));
    update_post_meta($id, 'product_version', htmlspecialchars($_POST['product_version']));
    update_post_meta($id, 'product_url', htmlspecialchars($_POST['product_url']));
    update_post_meta($id, 'product_description', htmlspecialchars($_POST['product_description']));
    update_post_meta($id, 'product_owner', htmlspecialchars($_POST['product_owner']));
    
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

    $redirectUrl = get_site_url() . '/' . $return;

    if(!can_delete_product_and_service($product->ID))
    {
        addMessage('Permission Denied!', 'error');
        wp_redirect($redirectUrl);
        exit;
    }
    
    //Check if the product has cliams or not
    $query = $wpdb->prepare("SELECT count(1) FROM " . $wpdb->prefix . "compliance_claims WHERE product_id=%d", $id);
    $count = $wpdb->get_var($query);
    if($count > 0)
    {
        addMessage("You can't delete the product/service, because it includes claims.", "error");
        wp_redirect($redirectUrl);
        exit;
    }
    
    //Delete Product/Service
    wp_trash_post($id);
    addMessage("The product/service was deleted!");
    wp_redirect($redirectUrl);
    exit;
}