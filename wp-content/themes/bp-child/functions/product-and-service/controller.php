<?php
add_action('before_delete_post', 'ct_delete_product_information', 10, 1);
function ct_delete_product_information($postid)
{
    global $wpdb, $CPRest;
    
    $post = get_post($postid);
    
    if($post->post_type == 'product-service')
    {    
        //Remove Row from Product Configuration
        $esb = new ManageESB();        
        $esb->deleteProductInfo($postid);
    }
}
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
    
    $_SESSION['product_data'] = null; unset($_SESSION['product_data']);
    
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
    
    //Check Product ID duplication
    $product_id = htmlspecialchars($_POST['product_id']);
    if(!$product_id)
    {        
        //Generate Product ID        
        $product_slug = sanitize_title(htmlspecialchars($_POST['product_name']));
        $product_id = sanitize_title($_POST['product_owner']) . "_" . $product_slug .  "_v" . $_POST['product_version'];
    }else{
        //Only allow letters, numbers, dot, line and underline.
        $product_id_arr = explode(".", $product_id);
        foreach($product_id_arr as $p_i=>$p_s)
        {
            $product_id_arr[$p_i] = sanitize_title($p_s);
        }
        $product_id = implode(".", $product_id_arr);
    }
    
    //Check Product ID duplication
    $query = $wpdb->prepare("SELECT count(distinct(post_id)) FROM $wpdb->postmeta WHERE post_id!=%d AND meta_key='product_id' AND meta_value=%s", $id, $product_id);
    $count = $wpdb->get_var($query);
    
    if($count > 0)
    {
        addMessage("Product IDs must be unique across all products configured on ComplianceTest. The Product ID entered is already in use by another product, potentially for another organisation. Please enter a different product ID. We recommend a combination of owner, product name and version, e.g. {owner}_{product name}_{product version}, with spaces replaced with dashes.", "error");
        
        $_SESSION['product_data'] = $_POST;
        
        if($isNew)            
            wp_redirect('/add-new-product-and-service');
        else
            wp_redirect('/edit-product-and-service/?id=' . $id);
            
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

    if(trim($_POST['product_url']) != '')
    {
        $product_url = sanitize_url(trim($_POST['product_url']));

        if(!preg_match('^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?^', $product_url)){
            addMessage('URL not valid', 'error');
            $_SESSION['product_data'] = $_POST;
        
            if($isNew)            
                wp_redirect('/add-new-product-and-service');
            else
                wp_redirect('/edit-product-and-service/?id=' . $id);
            exit;
        }
    }
    
    if($_POST['product_visibility'])
    {
        $product_status_post = htmlspecialchars($_POST['product_visibility']);
        if ($product_status_post == 'on'){
            $product_visibility = 'Public';
        }

    } else {
        $product_visibility = 'Private';
    }
    if($_POST['services_not_permitted'])
    {
        if( $_POST['services_not_permitted'] == 'on' && is_super_admin() ){
            $services_not_permitted = 1;
            update_post_meta($id, 'services_not_permitted', $services_not_permitted );
        }
    } else {
        if( is_super_admin() ){
            update_post_meta($id, 'services_not_permitted', 0 );
        }
    }

    if($_POST['product_release_date'] != '' && !preg_match('@^[0-9]{4}-[0-9]{2}-[0-9]{2}$@', $_POST['product_release_date'])){
        addMessage('Date not valid', 'error');
        $_SESSION['product_data'] = $_POST;
        
        if($isNew)            
            wp_redirect('/add-new-product-and-service');
        else
            wp_redirect('/edit-product-and-service/?id=' . $id);
        exit;
        
    }

    
    update_post_meta($id, 'product_id', $product_id);
    
    //Update Product Name ID Map Table on ESB
    $esb = new ManageESB();
    $esb->saveProductInfo($id, $product_id, $_POST['product_name']);
    
    update_post_meta($id, 'product_name', htmlspecialchars($_POST['product_name']));
    update_post_meta($id, 'product_release_date', !$_POST['product_release_date'] ? date("Y-m-d H:i:s") : date('Y-m-d H:i:s', getUTCTimeStamp(htmlspecialchars($_POST['product_release_date']))));
    update_post_meta($id, 'product_type', htmlspecialchars($_POST['product_type']));
    update_post_meta($id, 'product_version', htmlspecialchars($_POST['product_version']));
    update_post_meta($id, 'product_url', htmlspecialchars($_POST['product_url']));
    update_post_meta($id, 'product_description', htmlspecialchars($_POST['product_description']));
    update_post_meta($id, 'product_owner', htmlspecialchars($_POST['product_owner']));
    update_post_meta($id, 'product_visibility', $product_visibility);

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

    if( isset( $_POST['services_to_delete'] ) && ! empty(  $_POST['services_to_delete'] ) ){
        $services = explode( ',', trim( $_POST['services_to_delete'], ',' ) );
        if( ! empty( $services ) ){
            foreach( $services AS $service ){
                wp_delete_post( $service );
            }
        }
    }
    addMessage('Product was saved successfully');
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
        addMessage("You can't delete the product, because it includes claims.", "error");
        wp_redirect($redirectUrl);
        exit;
    }
    
    //Delete Product/Service
    wp_delete_post($id);
    addMessage("The product was deleted!");
    wp_redirect($redirectUrl);
    exit;
}