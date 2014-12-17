<?php
/**
* Template Name: My Agreements
*/

global $post;
$slug = get_post( $post )->post_name;

if(is_user_logged_in()){
    global $current_user;
    
    $userInfo = get_user_meta( $current_user->ID );
    
    $user = get_userdata( $current_user->ID );
    $user_status = $user->user_status;
    
    if($user_status == 3)
    {
        //Goto My Profile Page
        addMessage('Please verify your email address.', 'warning');
        wp_redirect('/my-profile');
        exit;
    }
    
    
}else{
    wp_redirect(home_url());
    exit;
}
if( ! check_user_has_make_agreement_priv() ){
    addMessage('You do not have the "' . ct_get_privilege_by_code('MAKE_AGREEMENTS', 'title') . '" privilege necessary for this action. Please contact your organisation administrator for the ComplianceTest site.', 'error');
    wp_redirect("/");
    exit;
}
$user_services = Service::get_services();

wp_enqueue_style( 'jquery-custom-scroll', get_stylesheet_directory_uri() . '/css/jquery.jscrollpane.css', '', '2.0.19');
wp_enqueue_script( 'jquery-mousewheel', get_stylesheet_directory_uri() . '/js/jquery.mousewheel.js', array('jquery'), '3.1.9');
wp_enqueue_script( 'jquery-custom-scrollpane', get_stylesheet_directory_uri() . '/js/jquery.jscrollpane.min.js', array('jquery'), '2.0.19');

get_header();
?>
<div class="content" id="my-agreements" xmlns="http://www.w3.org/1999/html">
    <div class="dashboard-tabs">
        <?php get_sidebar('dashboard'); ?>
    </div>

    <div class="container">
        <div class="column">
            <a id="bbp_search_submit" href="/products-and-services/?&type=Web%20Service" class="action-btn file-btn inline-btn has-tooltip" title="Find Services">
                <span class="p"></span><span class="t">Search</span>
            </a>
<!--           <a  class="action-btn add-new-btn has-tooltip" style="margin-left: 10px;" ><span class="p"></span><span class="t">Search</span></a>-->
           <div class="clear"></div>
           <div class="space20"></div>
               <?php if( is_super_admin() ):?>
                   <?php $user_agreements = $wpdb->get_results("SELECT * FROM wp_e2e_agreement ORDER BY str_id ASC");?>
                   <?php if( $user_agreements ):?>
                       <div class="grid-box table-box grid-box-opened">
                           <div class="grid-box-body tocenter">
                               <div class="thead tr clearfix">
                                   <div class="td td-admin-id">Agreement ID</div>
                                   <div class="td td-requester-data td-two-lines">Requester Service<br>Requester Owner</div>
                                   <div class="td td-responder-data td-two-lines">Responder Service<br>Responder Owner</div>
                                   <div class="td td-status">Status</div>
                                   <div class="td td-actions">Actions</div>
                               </div>
                           <?php foreach( $user_agreements AS $agreement ):?>
                               <?php
                                   $req_serv = new Service( $agreement->requester_service_id );
                                   $req_serv->load();
                                   $res_serv = new Service( $agreement->responder_service_id );
                                   $res_serv->load();
                               ?>
                               <div class="tr clearfix">
                                   <div class="td td-agreement-id"><a href="<?php echo get_site_url()?>?_psnonce=<?php echo wp_create_nonce('get-agreement-info-popup')?>&id=<?php echo $agreement->id?>" cp-type="ajax" rel="agree-popup">
                                           <?php if (strlen( $agreement->str_id ) > 30 ): ?>
                                               <div class="has-tooltip" title="<?php echo $agreement->str_id; ?>">
                                                   <?php echo substr($agreement->str_id,0,30)."..."; ?>
                                               </div>
                                           <?php else: ?>
                                           <?php echo $agreement->str_id;?></a>
                                       <?php endif; ?>
                                   </div>
                                   <div class="td td-requester-data">
                                       <a href="<?php echo get_permalink( $req_serv->id );?>"><?php echo get_the_title( $req_serv->id );?></a>
                                       </br>
                                       <?php echo $req_serv->service_owner;?>
                                   </div>
                                   <div class="td td-responder-data">
                                       <a href="<?php echo get_permalink( $res_serv->id );?>"><?php echo get_the_title( $res_serv->id );?></a>
                                       </br>
                                       <?php echo $res_serv->service_owner;?>
                                   </div>
                                   <div class="td td-status"><span class="status-<?php echo strtolower( $agreement->status );?>"><?php echo $agreement->status;?></span></div>
                                   <div class="td td-actions">
                                       <a href="<?php echo get_site_url()?>?_psnonce=<?php echo wp_create_nonce('delete-agreement')?>&id=<?php echo $agreement->id?>" class="action-btn delete-btn icon-btn delete_service has-tooltip" href="#" title="Delete Agreement"><span class="p"></span></a>
                                   </div>
                               </div>
                           <?php endforeach;?>
                       </div>
                   <?php endif;?>
               <?php else:?>
                <?php if( $user_services ):?>
                    <?php foreach( $user_services AS $user_service ):?>
                        <?php $serv = new Service( $user_service->ID ); $serv->load();?>
                           <div class="grid-box table-box grid-box-opened">
                               <div class="grid-box-header">
                                   <div class="grid-box-header-title left" style="font-size: 12px;">
                                       <ul>
                                           <li>Service: <a href="<?php echo get_permalink( $serv->id );?>"><strong><?php echo get_the_title( $serv->id );?></strong></a></li>
                                           <li>Owner: <strong><?php echo $serv->service_owner;?></strong></li>
                                           <?php
                                                $suite = new TestSuite( $serv->service_suite_id );
                                                $suite->load();
                                           ?>
                                           <li>Suite: <strong><a href="<?php echo get_permalink( $service->service_suite_id  );?>"><?php echo get_the_title( $serv->service_suite_id  ) ;?></strong></a></li>
                                           <li>Role: <strong><?php echo implode( ', ', $serv->service_roles );?></strong></li>
    <!--                                       <li>ServiceID: <strong>--><?php //echo $serv->service_type.':'.$serv->service_id;?><!--</strong></li>-->
                                           <li><?php echo $serv->service_type ?>: <strong><?php echo $serv->service_id ?></strong></li>
                                           <li>Product: <a href="<?php echo get_permalink( $serv->service_product_id );?>"><strong><?php echo get_the_title( $serv->service_product_id ) . ' v'.get_post_meta( $serv->service_product_id, 'product_version', true );?></strong></a></li>
                                       </ul>
                                   </div>
                                   <?php /*if(can_delete_product_and_service( $serv->id )){ ?>
                                   <a class="gbh-btn gbh-btn-delete right delete-product-link has-tooltip" href="#" title="Delete">Delete</a>
                                   <?php } ?>
                                   <?php if(can_edit_product_and_service( $serv->id ) ){ ?>
                                        <a class="gbh-btn gbh-btn-edit right has-tooltip" href="/edit-service?id=<?php echo $serv->id?>" title="Edit">Edit</a>
                                   <?php }*/ ?>

                                   <div class="clear"></div>
                               </div>
                               <div class="grid-box-body tocenter">
                                   <div class="thead tr clearfix">
                                       <div class="td td-agreement-id">Agreement ID</div>
                                       <div class="td td-entity-name td-two-lines">Service<br>Owner</div>
    <!--                                   <div class="td td-identifier"></div>-->
                                       <div class="td td-type">Role</div>
<!--                                       <div class="td td-protocol td-two-lines">Identifier<br>End Point</div>-->
    <!--                                   <div class="td td-end-point"></div>-->
                                       <div class="td td-contact">Contact(s)</div>
                                       <div class="td td-status">Status</div>
                                       <div class="td td-actions">Actions</div>
                                   </div>
                                   <div class="tbody">
                                       <?php
                                            $service_agreements = Agreement::get_service_agreements( $serv->id );
                                       ?>
                                       <?php if( ! $service_agreements ):?>
                                           <div class="tr">
                                               <div class="td td-full">No agreement yet</div>
                                               <div class="clear"></div>
                                           </div>
                                       <?php else: ?>
                                           <?php foreach( $service_agreements AS $agreement ):?>
                                               <div class="tr clearfix">
                                                   <div class="td td-agreement-id"><a href="<?php echo get_site_url()?>?_psnonce=<?php echo wp_create_nonce('get-agreement-info-popup')?>&id=<?php echo $agreement->id?>" cp-type="ajax" rel="agree-popup">
                                                           <?php if (strlen( $agreement->str_id ) > 30 ): ?>
                                                               <div class="has-tooltip" title="<?php echo $agreement->str_id; ?>">
                                                                   <?php echo substr($agreement->str_id,0,30)."..."; ?>
                                                               </div>
                                                           <?php else: ?>
                                                                <?php echo $agreement->str_id;?></a>
                                                           <?php endif; ?>
                                                   </div>
                                                   <div class="td td-entity-name">
                                                       <?php if( $agreement->entry_status == 'Responder' ):?>
                                                            <a href="<?php echo get_permalink( $agreement->requester_service->id );?>"><?php echo get_the_title( $agreement->requester_service->id );?></a>
                                                       <?php else:?>
                                                           <a href="<?php echo get_permalink( $agreement->responder_service->id );?>"><?php echo get_the_title( $agreement->responder_service->id );?></a>
                                                       <?php endif;?>
                                                       </br>
                                                       <?php echo $agreement->entry_status == 'Responder' ? $agreement->requester_service->service_owner : $agreement->responder_service->service_owner;?>
                                                   </div>
    <!--                                               <div class="td td-identifier"></div>-->
                                                   <div class="td td-type"><?php echo $agreement->entry_status == 'Responder' ? ( implode( ', ', $agreement->requester_service->service_roles )  ) : (  implode( ', ', $agreement->responder_service->service_roles ) );?></div>
<!--                                                   <div class="td td-protocol">-->
<!--                                                       --><?php //echo $agreement->entry_status == 'Responder' ? $agreement->requester_service->service_protocol : $agreement->responder_service->service_protocol;?><!--<br>-->
<!--                                                       --><?php //if( ( $agreement->entry_status == 'Responder' ? ( $agreement->requester_service->service_type  ) : (  $agreement->responder_service->service_type ) ) == 'ABN' ):?>
<!--                                                           --><?php //echo $agreement->entry_status == 'Responder' ? $agreement->requester_service->service_endpoint : $agreement->responder_service->service_endpoint;?>
<!--                                                       --><?php //else:?>
<!--                                                           --><?php //$gateway = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM wp_gateways WHERE gateway_id = %d ", $agreement->entry_status == 'Responder' ? $agreement->requester_service->service_endpoint : $agreement->responder_service->service_endpoint ) );?>
<!--                                                           --><?php //echo $gateway->name;?>
<!--                                                       --><?php //endif;?>
<!--                                                   </div>-->

                                                   <div class="td td-contact">

                                                       <?php $contacts = get_service_contacts($agreement->entry_status == 'Responder' ? $agreement->requester_service_id : $agreement->responder_service_id) ;?>
                                                       <?php foreach($contacts as $urow): ?>
                                                           <?php if (strlen( $urow->user_email ) > 30): ?>
                                                               <div class="has-tooltip" title="<?php echo $urow->user_email; ?>">
                                                                   <a href="mailto:<?php echo $urow->user_email;?>"><?php echo $urow->user_email = substr($urow->user_email,0,30)."..."; ?></a>
                                                               </div>
                                                           <?php else: ?>
                                                               <a href="mailto:<?php echo $urow->user_email;?>"><?php echo $urow->user_email; ?></a>
                                                           <?php endif; ?>
                                                       <?php endforeach; ?>
                                                   </div>
                                                   <div class="td td-status"><span class="status-<?php echo strtolower( $agreement->status );?>"><?php echo $agreement->status;?></span></div>
                                                   <div class="td td-actions centered">

                                                        <?php if( is_super_admin() ):?>

                                                            <a href="<?php echo get_site_url()?>?_psnonce=<?php echo wp_create_nonce('delete-agreement')?>&id=<?php echo $agreement->id?>" class="action-btn delete-btn icon-btn delete_service has-tooltip centered" href="#" style="margin-left: 30px; margin-top: 10px;"><span class="p"></span><span class="simple_tooltip radius6" style="top: -40px;">Delete Agreement<span></span></span></a>

                                                        <?php else:?>

                                                           <?php if( $agreement->status == 'Pending' ):?>
                                                                <?php if( $agreement->entry_status == 'Responder' ):?>
                                                                    <a href="#e2e-test-request-<?php echo $agreement->id;?>" rel="agree-popup">Accept</a>&nbsp;|&nbsp;<a href="#deny-agreement-popup-<?php echo $agreement->id;?>" rel="agree-popup">Reject</a>

                                                                   <div id="e2e-test-request-<?php echo $agreement->id;?>" class="popup-box e2e-test-request-popup" style="display: none; width: 450px;">
                                                                       <div class="popup-box-header radius6 noradiusbottom">Confirm Acceptance</div>
                                                                       <form name="generate_agreement_form" id="e2e-test-request-<?php echo $agreement->id;?>-form" action="/" method="get">
                                                                           <div class="popup-box-content">
                                                                               <div class="form-horizontal">
                                                                                       <?php
                                                                                            $responder_profiles = getCustomerProfileInstances();
                                                                                       ?>
                                                                                       <input type="hidden" name="" value="<?php echo $service->id;?>">
                                                                                       <div class="field-row">
                                                                                           <label>Profile:</label>
                                                                                           <div class="field-box">
                                                                                               <select name="responder_profiles" class="responder_profiles select input-field">
                                                                                                   <option></option>
                                                                                                   <?php foreach( $responder_profiles AS $responder_profile ):?>
                                                                                                        <?php
                                                                                                            $instanceObj = json_decode(base64_decode($responder_profile->content));
                                                                                                        ?>
                                                                                                       <option value="<?php echo $responder_profile->id;?>">
                                                                                                            <?php
                                                                                                                echo $responder_profile->profile_name;
                                                                                                                if($instanceObj->Profile->Version)
                                                                                                                {
                                                                                                                    $version = array();
                                                                                                                    foreach(get_object_vars($instanceObj->Profile->Version) as $k=>$v)
                                                                                                                    {
                                                                                                                        $version[] = $v;
                                                                                                                    }
                                                                                                                    echo " v" . implode(".", $version);
                                                                                                                }
                                                                                                            ?>
                                                                                                       </option>
                                                                                                   <?php endforeach;?>
                                                                                               </select>
                                                                                               <span class="info-icon has-tooltip" title="Optional data to be used during testing representing the state of your service"></span>
                                                                                           </div>
                                                                                       </div>
                                                                                       <div class="field-row">
                                                                                           <label>Message:</label>
                                                                                           <div class="field-box">
                                                                                               <textarea name="agreement_message" rows="5" cols="20" class="agreement_message textarea input-field"></textarea>
                                                                                               <span class="info-icon has-tooltip" title="Text to be included in an email accepting the end-to-end test request"></span>
                                                                                           </div>
                                                                                       </div>
                                                                                       <?php echo wp_nonce_field( 'accept-agreement', '_psnonce');?>
                                                                                       <input type="hidden" name="agreement_id" value="<?php echo $agreement->id;?>">
                                                                               </div>
                                                                           </div>
                                                                           <div class="popup-box-footer radius6 noradiustop">
                                                                               <div class="loading loading-with-text radius6 loading_agreement_acept"><div><b>PROCESSING</b><span>Please wait...</span></div></div>
                                                                               <a href="#" class="action-btn process-btn submit-btn save_append_agreement" data-id="e2e-test-request-<?php echo $agreement->id;?>-form"><span class="p"></span><span class="t">Confirm</span></a>
                                                                               <a href="#" class="action-btn cancel-btn" onclick="jQuery('.popup-box .close_btn').click()"><span class="p"></span><span class="t">Cancel</span></a>
                                                                               <div class="clear"></div>
                                                                           </div>
                                                                           <a class="close_btn"></a>
                                                                       </form>
                                                                   </div>

                                                                   <div class="popup-box" id="deny-agreement-popup-<?php echo $agreement->id;?>" style="display: none; width: 500px">
                                                                       <div class="popup-box-header radius6 noradiusbottom">Confirm Agreement Rejection</div>
                                                                       <form id="deny-agreement-popup-<?php echo $agreement->id;?>-form" method="post" action="/">
                                                                           <div class="popup-box-content">
                                                                               <p>Are you sure you want to <strong>Reject</strong> this agreement?</p>
                                                                               <div class="agreement-deny-reason">
                                                                                   <label>Reason for rejection:</label>
                                                                                   <textarea class="deny-reason-field" name="deny-reason-field" rows="5" cols="20"></textarea>
                                                                               </div>
                                                                               <input type="hidden" name="agreement_id" value="<?php echo $agreement->id;?>">
                                                                               <?php echo wp_nonce_field( 'reject-agreement', '_psnonce' );?>
                                                                           </div>
                                                                           <div class="popup-box-footer radius6 noradiustop">
                                                                               <div class="loading loading-with-text radius6"><div><b>DELETING AGREEMENT</b><span>Please wait...</span></div></div>
                                                                               <a href="#" class="action-btn process-btn reject_pending" data-id="deny-agreement-popup-<?php echo $agreement->id;?>"><span class="p"></span><span class="t">Confirm</span></a>
                                                                               <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>
                                                                               <div class="clear"></div>
                                                                           </div>
                                                                           <a class="close_btn"></a>
                                                                       </form>
                                                                   </div>

                                                                <?php endif;?>
                                                           <?php elseif( $agreement->status == 'Testing' ):?>
                                                               <a href="#claim-popup-box-<?php echo $agreement->id;?>" rel="agree-popup">Claim</a>&nbsp;|&nbsp;<a href="#deny-agreement-popup-<?php echo $agreement->id;?>" rel="agree-popup">Cancel</a>

                                                               <div class="popup-box" id="deny-agreement-popup-<?php echo $agreement->id;?>" style="display: none; width: 500px">
                                                                   <div class="popup-box-header radius6 noradiusbottom">Confirm Agreement Cancellation</div>
                                                                   <form id="deny-agreement-popup-<?php echo $agreement->id;?>-form" method="post" action="/">
                                                                       <div class="popup-box-content">
                                                                           <p>Are you sure you want to <strong>Cancel</strong> this agreement?</p>
                                                                           <div class="agreement-deny-reason">
                                                                               <label>Reason for cancellation:</label>
                                                                               <textarea class="deny-reason-field" name="deny-reason-field" rows="5" cols="20"></textarea>
                                                                           </div>
                                                                           <input type="hidden" name="agreement_id" value="<?php echo $agreement->id;?>">
                                                                           <?php echo wp_nonce_field( 'cancel-agreement', '_psnonce' );?>
                                                                       </div>
                                                                       <div class="popup-box-footer radius6 noradiustop">
                                                                           <div class="loading loading-with-text radius6"><div><b>DELETING AGREEMENT</b><span>Please wait...</span></div></div>
                                                                           <a href="#" class="action-btn process-btn reject_pending" data-id="deny-agreement-popup-<?php echo $agreement->id;?>"><span class="p"></span><span class="t">Confirm</span></a>
                                                                           <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>
                                                                           <div class="clear"></div>
                                                                       </div>
                                                                       <a class="close_btn"></a>
                                                                   </form>
                                                               </div>

                                                               <div class="popup-box" id="claim-popup-box-<?php echo $agreement->id;?>" style="display: none; width: 500px">
                                                                   <div class="popup-box-header radius6 noradiusbottom">Upload Audit Logs</div>
                                                                   <form id="claim-popup-box-<?php echo $agreement->id;?>-form" class="make-claim-form" action="/" method="post" enctype="multipart/form-data">
                                                                       <div class="popup-box-content">
                                                                           <p>Please upload screenshots from your business system as an audit record of the successful test.</p>
                                                                           <p class="note">Please zip the files if more than one.</p>
                                                                           <div class="claim-upload-box clearfix">
                                                                               <span class="info-icon has-tooltip right top5 left5" title="Please select at least one zip file"></span>
                                                                               <input type="file" name="file" class="left input-file claim_file"  />
                                                                               <ul class="uploaded-files">
                                                                                   <li><a href="#"></a><span class="remove-icon" style="display: none;"></span></li>
                                                                               </ul>
                                                                           </div>
                                                                           <div class="claim-test-scope">
                                                                               <h4>Test scope: <span class="info-icon has-tooltip right left5" title="You must select at least one"></span></h4>
                                                                               <?php
                                                                                    $suite_id = $agreement->entry_status == 'Responder' ? $agreement->requester_service->service_suite_id : $agreement->responder_service->service_suite_id;
                                                                                    $suite = new TestSuite( $suite_id );
                                                                                    $suite->load();
                                                                                    $scope = explode( ',', $suite->initiatingMessage );
                                                                               ?>
                                                                               <ul class="claim-test-scopes-list">
                                                                                   <?php foreach( $scope AS $s ):?>
                                                                                       <li>
                                                                                           <label><input type="checkbox" name="scope[]" value="<?php echo trim( $s );?>"/><?php echo trim( $s );?></label>
                                                                                       </li>
                                                                                       <div class="clear"></div>
                                                                                  <?php endforeach;?>
                                                                               </ul>
                                                                           </div>
                                                                           <input type="hidden" name="agreement_id" value="<?php echo $agreement->id;?>">
                                                                           <input type="hidden" name="role" value="<?php echo $agreement->entry_status;?>">
                                                                           <?php echo wp_nonce_field( 'claim_agreement', '_psnonce' );?>
                                                                       </div>
                                                                       <div class="popup-box-footer radius6 noradiustop">
                                                                           <div class="loading loading-with-text radius6"><div><b>SUBMITTING AGREEMENT</b><span>Please wait...</span></div></div>
                                                                           <a href="#" class="action-btn process-btn claim_agreement_confirm" data-id="claim-popup-box-<?php echo $agreement->id;?>"><span class="p"></span><span class="t">Confirm</span></a>
                                                                           <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>
                                                                           <div class="clear"></div>
                                                                       </div>
                                                                       <a class="close_btn"></a>
                                                                   </form>
                                                               </div>
                                                           <?php elseif( $agreement->status == 'Claimed' ):?>
                                                               <?php if( ( $agreement->entry_status == 'Requester' && empty( $agreement->requestor_audit_log_name ) ) || (  $agreement->entry_status == 'Responder' && empty( $agreement->responder_audit_log_name ) ) ):?>
                                                                   <a href="#agreement-confirm-popup-<?php echo $agreement->id;?>" rel="agree-popup">Confirm</a>&nbsp;|&nbsp;<a href="#deny-agreement-popup-<?php echo $agreement->id;?>" rel="agree-popup">Fail</a>

                                                                   <div class="popup-box claim-popup-box" id="agreement-confirm-popup-<?php echo $agreement->id;?>" style="display: none; width: 500px">
                                                                       <div class="popup-box-header radius6 noradiusbottom">Confirm Claim</div>
                                                                       <form id="agreement-confirm-popup-<?php echo $agreement->id;?>-form" action="/" method="post" enctype="multipart/form-data">
                                                                           <div class="popup-box-content">
                                                                               <p>Please upload screenshots from your business system as an audit record of the successful test.</p>
                                                                               <p class="note">Please zip the files if more than one.</p>
                                                                               <div class="claim-upload-box clearfix">
                                                                                   <input type="file" name="file" class="left input-file claim_file" file-type="image" file-extensions="(.jpg, .png, .gif or .jpeg file)" />
                                                                                   <ul class="uploaded-files">
                                                                                       <li><a href="#"></a><span class="remove-icon" style="display: none;"></span></li>
                                                                                   </ul>
                                                                               </div>
                                                                           </div>
                                                                           <input type="hidden" value="1" id="respond">
                                                                           <div class="popup-box-footer radius6 noradiustop">
                                                                               <div class="loading loading-with-text radius6"><div><b>CONFIRMING AGREEMENT</b><span>Please wait...</span></div></div>
                                                                               <a href="#" class="action-btn process-btn agreement_claim_confirm" data-id="agreement-confirm-popup-<?php echo $agreement->id;?>"><span class="p"></span><span class="t">Confirm</span></a>
                                                                               <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>
                                                                               <div class="clear"></div>
                                                                           </div>
                                                                           <a class="close_btn"></a>
                                                                           <?php echo wp_nonce_field( 'confirm-agreement', '_psnonce');?>
                                                                           <input type="hidden" name="agreement_id" value="<?php echo $agreement->id;?>">
                                                                           <input type="hidden" name="role" value="<?php echo $agreement->entry_status;?>">
                                                                       </form>
                                                                   </div>

                                                                   <div class="popup-box" id="deny-agreement-popup-<?php echo $agreement->id;?>" style="display: none; width: 500px">
                                                                       <div class="popup-box-header radius6 noradiusbottom">Confirm Failure of Claim</div>
                                                                       <form id="deny-agreement-popup-<?php echo $agreement->id;?>-form" method="post" action="/">
                                                                           <div class="popup-box-content">
                                                                               <p>Are you sure you want to fail this claim? The agreement will not be rejected but will be returned to a state where further testing can be undertaken.</p>
                                                                               <div class="agreement-deny-reason">
                                                                                   <label>Reason for failing the claim:</label>
                                                                                   <textarea class="deny-reason-field" name="deny-reason-field" rows="5" cols="20"></textarea>
                                                                               </div>
                                                                               <input type="hidden" name="agreement_id" value="<?php echo $agreement->id;?>">
                                                                               <?php echo wp_nonce_field( 'reject-failed-agreement', '_psnonce' );?>
                                                                           </div>
                                                                           <div class="popup-box-footer radius6 noradiustop">
                                                                               <div class="loading loading-with-text radius6"><div><b>UPDATING AGREEMENT</b><span>Please wait...</span></div></div>
                                                                               <a href="#" class="action-btn process-btn reject_pending" data-id="deny-agreement-popup-<?php echo $agreement->id;?>"><span class="p"></span><span class="t">Confirm</span></a>
                                                                               <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>
                                                                               <div class="clear"></div>
                                                                           </div>
                                                                           <a class="close_btn"></a>
                                                                       </form>
                                                                   </div>
                                                               <?php endif;?>
                                                           <?php elseif( $agreement->status == 'Verified' ):?>
                                                                <?php if( $agreement->entry_status == 'Requester' ):?>
                                                                    <?php if( $agreement->requester_token != '' ):?>
                                                                        <a href="<?php echo S3Wrapper::getAgreementClaimLink( $agreement->requester_token ); ?>" onclick="window.open('<?php echo S3Wrapper::getAgreementClaimLink( $agreement->requester_token );?>', '', 'height=600');return false;">View</a>&nbsp;|&nbsp;<a href="<?php echo S3Wrapper::getAgreementClaimLink( $agreement->requester_token, true ); ?>">Download</a>
                                                                    <?php endif;?>
                                                                <?php else:?>
                                                                    <?php if( $agreement->responder_token != '' ):?>
                                                                        <a href="<?php echo S3Wrapper::getAgreementClaimLink( $agreement->responder_token ); ?>" onclick="window.open('<?php echo S3Wrapper::getAgreementClaimLink( $agreement->responder_token ); ?>', '', 'height=600');return false;">View</a>&nbsp;|&nbsp;<a href="<?php echo S3Wrapper::getAgreementClaimLink( $agreement->responder_token, true ); ?>">Download</a>
                                                                    <?php endif;?>
                                                                <?php endif;?>
                                                           <?php endif;?>
                                                        <?php endif;?>
                                                   </div>
                                               </div>

                                           <?php endforeach;?>
                                       <?php endif;?>
                                   </div>
                               </div>
                           </div>
                           <div class="clear"></div>
                           <div class="space20"></div>
                    <?php endforeach;?>
                <?php endif;?>
               <?php if( check_user_has_make_agreement_priv() ):?>
                   <a href="/add-new-service/" class="action-btn add-new-btn has-tooltip" title="Add Service"><span class="p"></span><span class="t">Add</span></a>
               <?php else:?>
                   <a href="/?cp-action=<?php echo wp_create_nonce("insufficient-privilege") ?>&privilege=<?php echo base64_encode('MAKE_AGREEMENTS')?>" rel="custom-popup" cp-type="ajax" cp-removeBoxAfterClose=1 class="action-btn add-new-btn has-tooltip" title="Add Service"><span class="p"></span><span class="t">Add</span></a>
               <?php endif;?>
               <div class="clear"></div>
               <div class="space10"></div>
           <?php endif;?>

        </div>
    </div>
    <div class="clear"></div>


</div> <!--end content-->
<script type="text/javascript">
(function($){
    $(document).ready(function(){
        fixTdHeight($('#my-agreements .table-box'));
        jQuery('.save_append_agreement').on( 'click', function(){
            var is_valid = true;
            var form_id = $(this).data('id');
            if( ! jQuery( '#' + form_id + ' .agreement_message').val() ){
                jQuery( '#' + form_id + ' .agreement_message').addClass('textarea-error');
                return false;
            }
            jQuery('.loading_agreement_acept').show();
            jQuery( '#' + form_id ).submit();
        });

        $('.delete-product-link').each(function(){
            var link = $(this).attr('href');
            $(this).cplightbox({
                type: 'inline',
                href: '#delete-service-box',
                onStart: function(){
                    $('#delete-agreement-box .process-btn').attr('href', link);
                }
            })
        });
                               
        $("[rel='agree-popup']").cplightbox({
            onLoad: function(){
                jQuery('.simple_tooltip').each(function(){
                    var tooltipLeft = -1 * jQuery(this).width()/2;
                    var tooltipTop = -1 * jQuery(this).outerHeight() - 6;
                    jQuery(this).css({'top': tooltipTop, 'margin-left': tooltipLeft});
                });                
            }
        });




        $('#delete-service-box .process-btn').click(function(){
            $('#delete-agreement-box .loading').show();
        });
        
        jQuery('#my-agreements .container .simple_tooltip').each(function(){
            jQuery(this).css("margin-left", '-' + jQuery(this).outerWidth()/2-5 + "px" );
        });

        $('.claim_agreement_confirm, .agreement_claim_confirm').on( 'click', function(){
            var is_valid = true;
            var form_id = $(this).data('id');
            //Remove error messages
            $('#'+ form_id).find('.message').remove();
            
            if( ! $( '#' + form_id + ' .claim_file').val() ){
                $('#'+ form_id + ' .popup-box-footer').prepend('<p class="message error">Please select file to upload.</p>');
                return false;
            }
            if( $( '#' + form_id + ' .claim-test-scopes-list input[type="checkbox"]:checked').length == 0 && $('#respond').val() != '1' ){
                $('#'+ form_id + ' .popup-box-footer').prepend('<p class="message error">You need to select at least one on the test scope.</p>');
                return false;
            }
            
            $( '#' + form_id + ' .loading').show();
            $( '#' + form_id + '-form').submit();
        })

        $('.reject_pending').on( 'click', function(){
            var is_valid = true;
            var form_id = $(this).data('id');
            if( ! $( '#' + form_id + ' .deny-reason-field').val() ){
                $( '#' + form_id + ' .deny-reason-field').addClass('textarea-error');
                return false;
            }
            $( '#' + form_id + ' .loading').show();
            $( '#' + form_id + '-form').submit();
        });

        $('.claim_file').on('change', function(){
            jQuery( this).parents('.claim-upload-box').find('.uploaded-files li a').html( jQuery('.file-value').text().replace( /Choose File/gi, '' ) );
            jQuery( this).parents('.claim-upload-box').find('.uploaded-files li .remove-icon').show();
        });
        $('.remove-icon').on('click', function(){
            $('.claim_file').val( '' );
            $( this).prev( '.uploaded-files li a').html( '' );
            $( this).hide();
        });
        
        
    })                           
})(jQuery)
    
</script>
<?php
get_footer();
?>
