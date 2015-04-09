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
    if( ! Service::can_edit( get_current_user_id(), $id )  )
    {
        addMessage('Permission Denied!', 'error');
        wp_redirect( '/agreements/');
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
        $_POST['service_name'] = str_replace( ' ', '_', get_the_title( $_POST['suite_id'] ) ).':'.$_POST['roles'][0];
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
    if( isset( $_POST['roles'] ) && is_array( $_POST['roles'] ) ) {
        update_post_meta($id, 'service_roles', implode(';;', array_unique($_POST['roles'])));
    }
    if( isset( $_POST['levels'] ) && is_array( $_POST['levels'] ) ) {
        update_post_meta($id, 'service_levels', implode(';;', array_unique($_POST['levels'])));
    }
    update_post_meta($id, 'service_protocol', $_POST['protocol'] );

    save_wp_service( $id );
    $cloud_search = new CloudSearch();
    $cloud_search->_initial_upload();
    $full_search  = new FulltextSearch();
    $full_search->fullUpload();
    addMessage('Product / Service was saved successfully');
    wp_redirect(get_permalink($id));
    exit;
}

function save_wp_service( $service_id ){
    global $wpdb;
    $post_meta = get_post_meta( $service_id );
    $product_meta = get_post_meta( $post_meta['service_product_id'][0] );
    if( $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM wp_services WHERE wp_post_id = %d ", $service_id ) ) ){
        $wpdb->update( 'wp_services',
            array(
                'wp_post_id'          => $service_id,
                'owner_name'          => $post_meta['service_owner'][0],
                'owner_identifier'    => $post_meta['service_id'][0],
                'owner_type'          => $post_meta['service_type'][0],
                'service_name'        => $post_meta['service_name'][0],
                'service_description' => $post_meta['service_description'][0],
                'service_visibility'  => $post_meta['service_visibility'][0],
                'product_id'          => $post_meta['service_product_id'][0],
                'test_suite_id'       => $post_meta['service_suite_id'][0],
                'roles'               => $post_meta['service_roles'][0],
                'levels'              => $post_meta['service_levels'][0],
                'organisation_id'     => $product_meta['product_organisation_id'][0]
            ),
            array( 'id' => $row->id ),
            array( '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%d' ),
            array( '%d' )
        );
    } else{
        $wpdb->insert( 'wp_services',
            array(
                'wp_post_id'          => $service_id,
                'owner_name'          => $post_meta['service_owner'][0],
                'owner_identifier'    => $post_meta['service_id'][0],
                'owner_type'          => $post_meta['service_type'][0],
                'service_name'        => $post_meta['service_name'][0],
                'service_description' => $post_meta['service_description'][0],
                'service_visibility'  => $post_meta['service_visibility'][0],
                'product_id'          => $post_meta['service_product_id'][0],
                'test_suite_id'       => $post_meta['service_suite_id'][0],
                'roles'               => $post_meta['service_roles'][0],
                'levels'              => $post_meta['service_levels'][0],
                'organisation_id'     => $product_meta['product_organisation_id'][0]
            ),
            array( '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%d' )
        );
    }
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

function get_service_contacts($service_id)
{
    global $wpdb;
    
    $product_id = get_post_meta($service_id, 'service_product_id', true);
    $org_id = get_post_meta($product_id, 'product_organisation_id', true);
    
    $query = $wpdb->prepare("SELECT u.user_email, u.ID, u.display_name FROM {$wpdb->prefix}organisations_members AS os
             INNER JOIN {$wpdb->prefix}users_privileges AS up ON up.user_id=os.user_id
             INNER JOIN {$wpdb->prefix}privileges AS p ON p.id=up.privilege_id AND p.code='MAKE_AGREEMENTS'
             INNER JOIN {$wpdb->users} AS u ON u.ID=os.user_id WHERE os.organisation_id=%d", $org_id);
    $users = $wpdb->get_results($query);
    
    return $users;
}