<?php
add_action('before_delete_post', 'ct_delete_service_information', 10, 1);
function ct_delete_service_information( $postid )
{
    global $wpdb, $CPRest;
    
    $post = get_post($postid);
    
    if($post->post_type == 'service')
    {    
        //Remove Row from Product Configuration
        $esb = new ManageESB();        
        $esb->deleteProductInfo($postid);
    }
}
add_action('init', 'process_service_actions');
function process_service_actions()
{
    $action = isset($_REQUEST['_psnonce']) ? $_REQUEST['_psnonce'] : null;
    if(wp_verify_nonce($action, 'save-service')){
        saveService();
    }else if(wp_verify_nonce($action, 'delete-service')){
        deleteService();
    }
}


function saveService()
{
    global $wpdb;

    $_SESSION['service_data'] = null; unset($_SESSION['service_data']);

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
    //Check Service ID duplication
    $product_id = htmlspecialchars($_POST['service_id']);

    //Check Product ID duplication
    $query = $wpdb->prepare("SELECT count(distinct(post_id)) FROM $wpdb->postmeta WHERE post_id != %d AND meta_key='service_uniq_name' AND meta_value=%s", $id, stripslashes_deep($_POST['service_name'].'_'.$_POST['service_id']));
    $count = $wpdb->get_var($query);
    
    if($count > 0)
    {
        addMessage("Service already exists.", "error");
        
        $_SESSION['service_data'] = $_POST;
        
        if($isNew)            
            wp_redirect('/add-new-service');
        else
            wp_redirect('/edit-service/?id=' . $id);
            
        exit;
    }
    if( $_POST['service_name'] == '' ){
        $_POST['service_name'] = str_replace( ' ', '_', $_POST['service_process'] ).':'.$_POST['roles'][0];
    }
    if($isNew)
    {
        $id = wp_insert_post(array('post_title' => $_POST['service_name'], 'post_type'=>'service', 'post_status' => 'publish'), true);
        if(is_wp_error($id))
        {
            addMessage($id->get_error_message(), 'error');            
            return;
        }
        update_post_meta($id, 'service_user_id', get_current_user_id() );
    }else{
        if(!wp_update_post(array('ID' => $id, 'post_title' =>$_POST['service_name'], 'post_name' => sanitize_title($_POST['service_name']))))
        {
            addMessage('There was an error while updating the test suite.', true);
            return;
        }
    }

    if(trim($_POST['service_url']) != '')
    {
        $product_url = esc_url_raw(trim($_POST['service_url']));

        if(!preg_match('^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?^', $product_url)){
            addMessage('URL not valid', 'error');
            $_SESSION['service_data'] = $_POST;
        
            if($isNew)            
                wp_redirect('/add-new-service');
            else
                wp_redirect('/edit-service/?id=' . $id);
            exit;
        }
    }
    $service_visibility = "Community";
    if($_POST['visibility'])
    {
        $service_visibility =  $_POST['visibility'];

    }

    update_post_meta($id, 'service_id', $product_id);
    if( $_POST['type'] == 'ABN' ){
        update_post_meta($id, 'service_endpoint', $_POST['endpoint_type_alias']);
    } else{
        update_post_meta($id, 'service_endpoint', $_POST['endpoint_type']);
    }

    //Update Product Name ID Map Table on ESB
//    $esb = new ManageESB();
//    $esb->saveProductInfo($id, $product_id, $_POST['product_name']);
    update_post_meta($id, 'service_uniq_name', $_POST['service_name'].'_'.$_POST['service_id']);
    update_post_meta($id, 'service_name', $_POST['service_name']);
    update_post_meta($id, 'service_type', $_POST['type']);
    update_post_meta($id, 'service_description', stripslashes_deep($_POST['product_description']));
    update_post_meta($id, 'service_owner', $_POST['service_owner']);
    update_post_meta($id, 'service_visibility', $service_visibility);
    update_post_meta($id, 'service_product_id', intval( $_POST['product_id'] ) );
    update_post_meta($id, 'service_suite_id', intval( $_POST['suite_id'] ) );
    update_post_meta($id, 'service_roles', implode( ';;', $_POST['roles'] ) );
    update_post_meta($id, 'service_levels', implode( ';;', $_POST['levels'] ) );
    update_post_meta($id, 'service_protocol', $_POST['protocol'] );

    $cloud_search = new CloudSearch();
    $cloud_search->cloud_search_update_service( $id );

    addMessage('Product / Service was saved successfully');
    wp_redirect(get_permalink($id));
    exit;
}

function deleteService()
{
    global $wpdb;
            
    $id = $_REQUEST['id'];
    
    $service = get_post($id );
    
    if( ! $service ){
        addMessage('Invalid Request!', 'error');
        return;
    }
    
    $return = isset($_REQUEST['return']) ? base64_decode($_REQUEST['return']) : "/";

    $redirectUrl = get_site_url() . '/' . $return;

    if(!can_delete_product_and_service( $service->ID ) ){
        addMessage('Permission Denied!', 'error');
        wp_redirect($redirectUrl);
        exit;
    }
    //dont allow to delete service until we implement agreemnets feature
    if( true ){
        addMessage("You can't delete the service.", "error");
        wp_redirect($redirectUrl);
        exit;
    }
    //todo check that service dont have aggrements
    $count = $wpdb->get_var( $wpdb->prepare("SELECT count(1) FROM " . $wpdb->prefix . "compliance_claims WHERE product_id = %d ", $id ) );
    if( $count > 0 ){
        addMessage("You can't delete the product/service, because it includes claims.", "error");
        wp_redirect($redirectUrl);
        exit;
    }
    
    //Delete Service
    wp_delete_post( $id );
    addMessage("The service was deleted!");
    wp_redirect($redirectUrl);
    exit;
}

function getServiceContactList($service_id)
{
    global $wpdb;
    
    $product_id = get_post_meta($service_id, 'service_product_id', true);
    $org_id = get_post_meta($product_id, 'product_organisation_id', true);
    
    $query = $wpdb->prepare("SELECT DISTINCT(u.ID), u.user_email FROM {$wpdb->prefix}organisations_members AS om 
                INNER JOIN {$wpdb->prefix}users_privileges AS up ON up.user_id=om.user_id
                INNER JOIN {$wpdb->prefix}privileges AS p ON p.id=up.privilege_id AND p.code='MAINTAIN_PRODUCTS'
                INNER JOIN {$wpdb->users} AS u ON u.ID=om.user_id WHERE om.organisation_id=%d", $org_id);
    
    $results = $wpdb->get_results($query);
    
    return $results;
}