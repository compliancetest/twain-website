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
        showDeletePopup();
    }else if(wp_verify_nonce($action, 'delete-product-confirm')){
        deleteProduct();
    }else if(wp_verify_nonce($action, 'delete-search-entry') && is_super_admin() ){
        if( is_super_admin() ) {
            if( $_REQUEST['type'] == 'site'){
                $id = $_REQUEST['id'];
                $cloud_search = new FulltextSearch();
                $cloud_search->delete_item( $id );
                exit(json_encode(array('success' => true)));
            } else {
                $data = explode('_', $_REQUEST['id']);
                $cloud_search = new CloudSearch();
                $cloud_search->cloud_search_delete_item($data[1], $data[0]);
                exit(json_encode(array('success' => true)));
            }
        }
        exit(json_encode(array('error' => true)));

    }else if(wp_verify_nonce($action, 'get-delete-search-entry') && is_super_admin() ){ ?>
            <div id="delete_search_entry" class="popup-box deleting-case-confirm-box" style="display: none; width: 450px">
                <div class="popup-box-header radius6 noradiusbottom">Delete CloudSearch entry</div>
                    <div class="popup-box-content">
                        <div class="field-row">
                            <div class="grid-cell">
                                <p>This action will remove entry from CloudSearch domain. Are you sure?</p>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="space10"></div>
                    </div>
                    <div class="popup-box-footer radius6 noradiustop">
                        <a class="action-btn process-btn submit-btn delete_search_entry_confirm" href="#" data-id="<?php echo $_REQUEST['id'];?>" data-type="<?php echo isset( $_REQUEST['t'] ) && $_REQUEST['t'] == '2' ? 'site' : 'registry';?>"><span class="p"></span><span class="t">Confirm</span></a>
                        <a class="action-btn cancel-btn close-popup-btn" href="#"><span class="p"></span><span class="t">Cancel</span></a>
                        <div class="clear"></div>
                    </div>
            <div class="loading loading-with-text radius6"><div><b>DELETING</b><p>Please wait...</p></div></div>
            <a id="close-popup-delete" class="close_btn"></a>
            </div>
            <script>
                jQuery( document).ready( function($){
                    $('.delete_search_entry_confirm').on('click', function(){
                        var item_id   = $( this).attr('data-id');
                        var item_type = $( this).attr('data-type');
                        var td_row = $("a").find("[data-entryid='" + item_id + "']").parents('tr');
                        $('.loading').show();
                        $.ajax({
                            type: 'post',
                            url: '/',
                            dataType: 'json',
                            data: { '_psnonce' : '<?php echo wp_create_nonce( 'delete-search-entry' );?>', 'id' : item_id, 'type' : item_type  },
                            success: function( data ){
                                if( data.success ){
                                    location.reload();
                                } else{
                                    alert('error' );
                                }
                                $('#close-popup-delete').click();
                                $('.loading').show();
                            }
                        })
                    })
                });
            </script>
    <?php
        exit;
    }
}


function saveProductService()
{
    global $wpdb;
    
    $user_id = get_current_user_id();
    
    $_SESSION['product_data'] = null; unset($_SESSION['product_data']);
    
    $id = htmlspecialchars($_POST['id']);
    if(!$id)
        $isNew = true;
    else
        $isNew = false;
    
    if (($isNew || !is_super_admin()) && !can_maintain_product_and_service($user_id, $id)) 
    {
        addMessage('You do not have the "' . ct_get_privilege_by_code('MAINTAIN_PRODUCTS', 'title') . '" privilege necessary for this action. Please contact your organisation administrator for the '.get_option('tw_site_title').' site.', 'error');
        wp_redirect(get_site_url());
        exit;
    }
    
    $user_organisation = ct_get_user_organisation($user_id);
    
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
        addMessage("Product IDs must be unique across all products configured on ".get_option('tw_site_title').". The Product ID entered is already in use by another product, potentially for another organisation. Please enter a different product ID. We recommend a combination of owner, product name and version, e.g. {owner}_{product name}_{product version}, with spaces replaced with dashes.", "error");
        
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

    $product_visibility = htmlspecialchars( $_POST['product_visibility'] );
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
    if (is_super_admin()) {
        if( isset( $_POST['allow_override'] ) ){
            update_post_meta($id, 'product_owner_override', $_POST['product_owner_override'] );
            update_post_meta($id, 'product_override', 'yes' );
        } else{
            update_post_meta($id, 'product_override', 'no' );
        }
        update_post_meta($id, 'product_organisation_id', $_POST['product_owner'] );
        update_post_meta($id, 'product_owner', '');    
    } else {
        if( isset( $_POST['allow_override'] ) ){
            update_post_meta($id, 'product_owner_override', $_POST['product_owner'] );
            update_post_meta($id, 'product_override', 'yes' );
        } else{
            update_post_meta($id, 'product_override', 'no' );
        }
        update_post_meta($id, 'product_organisation_id', $user_organisation->id);
        update_post_meta($id, 'product_owner', $user_organisation->organisation_admin);
    }
    
    
    update_post_meta($id, 'product_name', htmlspecialchars($_POST['product_name']));
    update_post_meta($id, 'product_release_date', !$_POST['product_release_date'] ? date("Y-m-d H:i:s") : date('Y-m-d H:i:s', getUTCTimeStamp(htmlspecialchars($_POST['product_release_date']))));    
    update_post_meta($id, 'product_type', htmlspecialchars($_POST['product_type']));
    update_post_meta($id, 'product_version', htmlspecialchars($_POST['product_version']));
    update_post_meta($id, 'product_url', htmlspecialchars($_POST['product_url']));
    update_post_meta($id, 'product_description', stripslashes_deep($_POST['product_description']));
    
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
                if( ! Service::has_agreements( $service) ){
                    $cs = new CloudSearch();
                    $cs->cloud_search_delete_item( $service, 'service' );
                    $wpdb->query( $wpdb->prepare("DELETE FROM wp_services WHERE wp_post_id = %d ", $service ) );
                    wp_delete_post( $service );
                } else{
                    addMessage( "Can't delete '".get_the_title( $service )."' service, because it has agreements", 'error' );
                }
            }
        }
    }
    $full_search  = new FulltextSearch();
    $cloud_search = new CloudSearch();
    /**
     * We need to reload data for existing product because
     * it could contain test plans / claims / services which also should be updated
     */
    if( $isNew ){
        $full_search->fullUpload( $id );
    } else{
        $cloud_search->_initial_upload();
        $full_search->fullUpload();
    }

    addMessage('Product was saved successfully');
    wp_redirect(get_permalink($id));
    exit;
}

function showDeletePopup()
{
    $id = filter_var( $_REQUEST['id'], FILTER_SANITIZE_NUMBER_INT );

    $user_id = get_current_user_id();

    $can_delete = true;
    $message = 'Do you really want to delete this product?';
    $title = 'Product Deletion';
    $cancel_button_text = 'Cancel';

    $can_delete = can_delete( $id, $user_id );

    if( $can_delete['status'] == 'error' ){
        $message = $can_delete['message'];
        $title = 'Product Deletion Failure';
        $cancel_button_text = 'Close';
        $can_delete = false;
    }

    ?>
    <div id="delete_product_ajax" class="popup-box deleting-case-confirm-box" style="display: none; width: 450px">
        <div class="popup-box-header radius6 noradiusbottom"><?php echo $title;?></div>
        <div class="popup-box-content">
            <div class="field-row">
                <div class="grid-cell">
                    <p><?php echo $message;?></p>
                </div>
                <div class="clear"></div>
            </div>
            <div class="space10"></div>
        </div>
        <div class="popup-box-footer radius6 noradiustop">
            <?php if( $can_delete ):?>
                <a class="action-btn process-btn submit-btn delete_prod_confirm" href="#" data-id="<?php echo $id;?>"><span class="p"></span><span class="t">Confirm</span></a>
            <?php endif;?>
            <a class="action-btn cancel-btn close-popup-btn" href="#"><span class="p"></span><span class="t"><?php echo $cancel_button_text;?></span></a>
            <div class="clear"></div>
        </div>
        <div class="loading loading-with-text radius6"><div><b>DELETING</b><p>Please wait...</p></div></div>
        <a id="close-popup-delete" class="close_btn"></a>
    </div>
    <?php if( $can_delete ):?>
        <script>
            jQuery( document).ready( function($){
                $('.delete_prod_confirm').on('click', function(){
                    var item_id   = $( this).attr('data-id');
                    $('.loading').show();
                    $.ajax({
                        type: 'post',
                        url: '/',
                        data: { '_psnonce' : '<?php echo wp_create_nonce( 'delete-product-confirm' );?>', 'id' : item_id  },
                        success: function( data ){
                            if( data == 'success' ){
                                location.href = '<?php echo base64_decode( $_REQUEST['return'] );?>';
                            }
                        }
                    })
                })
            });
        </script>
    <?php endif;?>
    <?php
    exit;
}

function deleteProduct()
{
    $id = filter_var( $_REQUEST['id'], FILTER_SANITIZE_NUMBER_INT );
    $user_id = get_current_user_id();
    $can_delete = can_delete( $id, $user_id );
    if( $can_delete['status'] == 'success' ){
        $fullTextSearch = new FulltextSearch();
        $fullTextSearch->fullDelete( $id );
        wp_delete_post($id);
        addMessage('Product was deleted successfully');
    }
    exit('success');
}
function can_delete( $product_id, $user_id )
{
    global $wpdb;

    $response = array( 'status' => 'success', 'message' => '' );
    if( ( ! is_super_admin() ) && ! can_maintain_product_and_service( $user_id, $product_id ) ) {
        $response['message'] = 'You do not have the "' . ct_get_privilege_by_code('MAINTAIN_PRODUCTS', 'title') . '" privilege necessary for this action. Please contact your organisation administrator for the '.get_option('tw_site_title').' site.';
        $response['status'] = 'error';
    }

    //Check if the product has claims, test plans, services
    $count_claims    = $wpdb->get_var( $wpdb->prepare( "SELECT count(1) FROM wp_compliance_claims WHERE product_id = %d", $product_id ) );
    $count_plans     = $wpdb->get_var( $wpdb->prepare( "SELECT count(1) FROM wp_test_plans WHERE product_id = %d AND is_deleted = 0 ", $product_id ) );
    $count_services  = $wpdb->get_var( $wpdb->prepare( "SELECT count(1) FROM wp_services WHERE product_id = %d", $product_id ) );

    if( $count_services > 0 || $count_claims > 0 || $count_plans > 0 ){
        $response['message'] = 'We were unable to delete this product because there are test plans, claims or services currently associated with it. Please delete the associated items then try again.';
        $response['status']  = 'error';
    }
    return $response;
}