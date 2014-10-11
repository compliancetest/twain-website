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

$user_services = Service::get_user_services();

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
           <?php if(can_create_product_and_service()){ ?>
                <a href="/add-new-service" class="action-btn add-new-btn"><span class="p"></span><span class="t">New Service</span></a>
           <div class="clear"></div>
           <div class="space20"></div>
           <?php } ?>
            <?php if( $user_services ):?>
                <?php foreach( $user_services AS $user_service ):?>
                    <?php $serv = new Service( $user_service->ID ); $serv->load();?>
                       <div class="grid-box table-box grid-box-opened">
                           <div class="grid-box-header">
                               <div class="grid-box-header-title left">
                                   <ul>
                                       <li>Service: <a href="<?php echo get_permalink( $serv->id );?>"><strong><?php echo get_the_title( $serv->id );?></strong></a></li>
                                       <li>Owner: <strong><?php echo $serv->service_owner;?></strong></li>
                                       <li>Service ID: <strong><?php echo $serv->service_id;?></strong></li>
                                       <li>Role: <strong><?php echo implode( ', ', $serv->service_roles );?></strong></li>
                                       <li>User Product: <a href="<?php echo get_permalink( $serv->service_product_id );?>"><strong><?php echo get_the_title( $serv->service_product_id );?></strong></a></li>
                                   </ul>
                               </div>
                               <?php if(can_delete_product_and_service( $serv->id )){ ?>
                               <a class="gbh-btn gbh-btn-delete right delete-product-link has-tooltip" href="#" title="Delete">Delete</a>
                               <?php } ?>
                               <?php if(can_edit_product_and_service( $serv->id ) ){ ?>
                                    <a class="gbh-btn gbh-btn-edit right has-tooltip" href="/edit-service?id=<?php echo $serv->id?>" title="Edit">Edit</a>
                               <?php } ?>

                               <div class="clear"></div>
                           </div>
                           <div class="grid-box-body tocenter">
                               <div class="thead tr clearfix">
                                   <div class="td td-agreement-id">Agreement ID</div>
                                   <div class="td td-entity-name">Entity Name</div>
                                   <div class="td td-identifier">Identifier</div>
                                   <div class="td td-type">Type</div>
                                   <div class="td td-protocol">Protocol</div>
                                   <div class="td td-end-point">End Point</div>
                                   <div class="td td-contact">Contact</div>
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
                                               <div class="td td-agreement-id"><a href="#agreement-details-popup" rel="custom-popup"><?php echo $agreement->str_id;?></a></div>
                                               <div class="td td-entity-name">
                                                   <?php if( $agreement->entry_status == 'Responder' ):?>
                                                        <a href="<?php echo get_permalink( $agreement->requester_service->id );?>"><?php echo get_the_title( $agreement->requester_service->id );?></a>
                                                   <?php else:?>
                                                       <a href="<?php echo get_permalink( $agreement->responder_service->id );?>"><?php echo get_the_title( $agreement->responder_service->id );?></a>
                                                   <?php endif;?>
                                               </div>
                                               <div class="td td-identifier"><?php echo $agreement->entry_status == 'Responder' ? $agreement->requester_service->service_id : $agreement->responder_service->service_id;?></div>
                                               <div class="td td-type"><?php echo $agreement->entry_status == 'Responder' ? ( strlen( $agreement->requester_service->service_id ) == 11 ? 'ABN' : 'USI' ) : ( strlen( $agreement->responder_service->service_id ) == 11 ? 'ABN' : 'USI' );?></div>
                                               <div class="td td-protocol"><?php echo $agreement->entry_status == 'Responder' ? $agreement->requester_service->service_protocol : $agreement->responder_service->service_protocol;?></div>
                                               <div class="td td-end-point"><a href="#"><div class="td td-protocol"><?php echo $agreement->entry_status == 'Responder' ? $agreement->requester_service->service_endpoint : $agreement->responder_service->service_endpoint;?></div></a></div>
                                               <div class="td td-contact">
                                                   <?php $user_id = ( $agreement->entry_status == 'Responder' ? $agreement->requester_service->service_user_id : $agreement->responder_service->service_user_id ); ?>
                                                   <?php $email = get_user_by( 'id', $user_id )->data->user_email; ?>
                                                   <?php if (strlen( $email ) > 22): ?>
                                                       <div class="has-tooltip" title="<?php echo $email; ?>">
                                                           <a href="mailto:<?php echo $email;?>"><?php echo $email = substr($email,0,22)."..."; ?></a>
                                                       </div>
                                                   <?php else: ?>
                                                       <a href="mailto:<?php echo $email;?>"><?php echo $email; ?></a>
                                                   <?php endif; ?>
                                               </div>
                                               <div class="td td-status"><span class="status-<?php echo strtolower( $agreement->status );?>"><?php echo $agreement->status;?></span></div>
                                               <div class="td td-actions">
                                                   <?php if( $agreement->status == 'Pending' ):?>
                                                        <?php if( $agreement->entry_status == 'Responder' ):?>
                                                            <a href="?_psnonce=<?php echo wp_create_nonce('accept-agreement');?>&id=<?php echo $agreement->id;?>">Accept</a>&nbsp;|&nbsp;<a href="#deny-agreement-popup-<?php echo $agreement->id;?>" rel="custom-popup">Reject</a>

                                                           <div class="popup-box" id="deny-agreement-popup-<?php echo $agreement->id;?>" style="display: none; width: 500px">
                                                               <div class="popup-box-header radius6 noradiusbottom">Confirm Reject</div>
                                                               <form id="deny-agreement-popup-<?php echo $agreement->id;?>-form" method="post" action="/">
                                                                   <div class="popup-box-content">
                                                                       <p>Are you sure you want to <strong>Reject</strong> this agreement?</p>
                                                                       <div class="agreement-deny-reason">
                                                                           <label>Let us know why:</label>
                                                                           <textarea class="deny-reason-field" name="deny-reason-field" rows="5" cols="20"></textarea>
                                                                       </div>
                                                                       <input type="hidden" name="agreement_id" value="<?php echo $agreement->id;?>">
                                                                       <?php echo wp_nonce_field( 'reject-pending-agreement', '_psnonce' );?>
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
                                                       <a href="#claim-popup-box-<?php echo $agreement->id;?>" rel="custom-popup">Claim</a>&nbsp;|&nbsp;<a href="#deny-agreement-popup-<?php echo $agreement->id;?>" rel="custom-popup">Cancel</a>

                                                       <div class="popup-box" id="deny-agreement-popup-<?php echo $agreement->id;?>" style="display: none; width: 500px">
                                                           <div class="popup-box-header radius6 noradiusbottom">Confirm Cancel</div>
                                                           <form id="deny-agreement-popup-<?php echo $agreement->id;?>-form" method="post" action="/">
                                                               <div class="popup-box-content">
                                                                   <p>Are you sure you want to <strong>Cancel</strong> this agreement?</p>
                                                                   <div class="agreement-deny-reason">
                                                                       <label>Let us know why:</label>
                                                                       <textarea class="deny-reason-field" name="deny-reason-field" rows="5" cols="20"></textarea>
                                                                   </div>
                                                                   <input type="hidden" name="agreement_id" value="<?php echo $agreement->id;?>">
                                                                   <?php echo wp_nonce_field( 'reject-claimed-agreement', '_psnonce' );?>
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
                                                           <form id="claim-popup-box-<?php echo $agreement->id;?>-form" action="/" method="post" enctype="multipart/form-data">
                                                               <div class="popup-box-content">
                                                                   <p>Please upload screenshots from your business system as an audit record of the successful test.</p>
                                                                   <p class="note">Please zip the files if more than one.</p>
                                                                   <div class="claim-upload-box clearfix">
                                                                       <input type="file" name="file" class="left input-file claim_file"  />
                                                                       <ul class="uploaded-files">
                                                                           <li><a href="#"></a><span class="remove-icon" style="display: none;"></span></li>
                                                                       </ul>
                                                                   </div>
                                                                   <div class="claim-test-scope">
                                                                       <h4>Test scope:</h4>
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
                                                           <a href="#agreement-confirm-popup-<?php echo $agreement->id;?>" rel="custom-popup">Confirm</a>&nbsp;|&nbsp;<a href="#deny-agreement-popup-<?php echo $agreement->id;?>" rel="custom-popup">Fail</a>

                                                           <div class="popup-box claim-popup-box" id="agreement-confirm-popup-<?php echo $agreement->id;?>" style="display: none; width: 500px">
                                                               <div class="popup-box-header radius6 noradiusbottom">Confirm</div>
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
                                                               <div class="popup-box-header radius6 noradiusbottom">Confirm Reject</div>
                                                               <form id="deny-agreement-popup-<?php echo $agreement->id;?>-form" method="post" action="/">
                                                                   <div class="popup-box-content">
                                                                       <p>Are you sure you want to <strong>Reject</strong> this agreement?</p>
                                                                       <div class="agreement-deny-reason">
                                                                           <label>Let us know why:</label>
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
        </div>
    </div>
    <div class="clear"></div>


    <div class="popup-box" id="deny-agreement-popup" style="display: none; width: 500px">
        <div class="popup-box-header radius6 noradiusbottom">Confirm [ACTION]</div>
        <div class="popup-box-content">
            <p>Are you sure you want to <strong>[ACTION]</strong> this agreement?</p>
            <div class="agreement-deny-reason">
                <label for="deny-reason-field">Let us know why:</label>
                <textarea id="deny-reason-field" rows="5" cols="20"></textarea>
            </div>
        </div>
        <div class="popup-box-footer radius6 noradiustop">
            <div class="loading loading-with-text radius6"><div><b>DELETING AGREEMENT</b><span>Please wait...</span></div></div>
            <a href="#" class="action-btn process-btn"><span class="p"></span><span class="t">Confirm</span></a>
            <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>
            <div class="clear"></div>
        </div>
        <a class="close_btn"></a>
    </div>

    <div class="popup-box" id="agreement-details-popup" style="display: none; width: 500px">
        <div class="popup-box-header radius6 noradiusbottom">Agreement Title</div>
        <div class="popup-box-content">
            <div class="tabs-contr">
                <ul class="tab-nav">
                    <li class="active">
                        <a href="javascript: void(0)" rel="tab_general_information">General Information</a>
                    </li>
                    <li>
                        <a href="javascript: void(0)" rel="tab_message_log">Message Log</a>
                    </li>
                </ul>
                <div class="tab-content agreement-general-info" id="tab_general_information" style="display: block;">
                    <dl class="common-info">
                        <dt>Status:</dt>
                        <dd><strong class="status-verified">Verified</strong></dd>
                        <dt>Date:</dt>
                        <dd>14-Nov-2014</dd>
                        <dt>Scope:</dt>
                        <dd>Registration, Contribution</dd>
                    </dl>
                    <div class="info-per-item clearfix">
                        <dl>
                            <dt class="item-title">Employer</dt>
                            <dt>Organisation</dt>
                            <dd>MLC Funds</dd>
                            <dt>User</dt>
                            <dd>Bob Brown</dd>
                            <dt>Service</dt>
                            <dd>MLC Masterkey Super Service</dd>
                            <dt>Profile</dt>
                            <dd><a href="#">MLC Member Test Data</a></dd>
                            <dt>Audit Log</dt>
                            <dd><a href="#">ContributionLog.zip</a></dd>
                        </dl>
                        <dl>
                            <dt class="item-title">Fund</dt>
                            <dt>Organisation</dt>
                            <dd>SAP</dd>
                            <dt>User</dt>
                            <dd>Mary Black</dd>
                            <dt>Service</dt>
                            <dd>BHP Payroll Service</dd>
                            <dt>Profile</dt>
                            <dd><a href="#">BHP Employess Test Data</a></dd>
                            <dt>Audit Log</dt>
                            <dd><a href="#">PayRollLog.zip</a></dd>
                        </dl>
                    </div>
                </div>
                <div class="tab-content agreements-message-log" id="tab_message_log" style="display: none;">
                    <div class="agreements-message-log-list" style="height: 450px;">
                        <ul>
                            <li class="employer">
                                <div class="author-name">Employer</div>
                                <div class="message-content">
                                    <div class="message-body"><span class="message-box-arrow"></span>This is Photoshop's version  of Lorem Ipsum. Proin gravida nibh vel velit auctor aliquet.</div>
                                    <div class="message-date">29 Sep 214, 11:50 AM</div>
                                </div>
                            </li>
                            <li class="fund">
                                <div class="author-name">Fund</div>
                                <div class="message-content">
                                    <div class="message-body"><span class="message-box-arrow"></span>Aenean sollicitudin, lorem quis bibendum auctor, nisi elit consequat ipsum, nec sagittis sem nibh id elit. Duis sed odio sit amet.</div>
                                    <div class="message-date">29 Sep 2014: 09:55 AM</div>
                                </div>
                            </li>
                            <li class="employer">
                                <div class="author-name">Employer</div>
                                <div class="message-content">
                                    <div class="message-body"><span class="message-box-arrow"></span>This is Photoshop's version  of Lorem Ipsum. Proin gravida nibh vel velit auctor aliquet.</div>
                                    <div class="message-date">28 Sep 2014, 04:32 PM</div>
                                </div>
                                <div class="message-content">
                                    <div class="message-body"><span class="message-box-arrow"></span>This is Photoshop's version  of Lorem Ipsum. Proin gravida nibh vel velit auctor aliquet.</div>
                                    <div class="message-date">28 Sep 2014, 03:11 PM</div>
                                </div>
                            </li>
                            <li class="fund">
                                <div class="author-name">Fund</div>
                                <div class="message-content">
                                    <div class="message-body"><span class="message-box-arrow"></span>Aenean sollicitudin, lorem quis bibendum auctor, nisi elit consequat ipsum, nec sagittis sem nibh id elit. Duis sed odio sit amet.</div>
                                    <div class="message-date">29 Sep 2014: 09:55 AM</div>
                                </div>
                            </li>
                            <li class="employer">
                                <div class="author-name">Employer</div>
                                <div class="message-content">
                                    <div class="message-body"><span class="message-box-arrow"></span>This is Photoshop's version  of Lorem Ipsum. Proin gravida nibh vel velit auctor aliquet.</div>
                                    <div class="message-date">29 Sep 214, 11:50 AM</div>
                                </div>
                            </li>
                            <li class="fund">
                                <div class="author-name">Fund</div>
                                <div class="message-content">
                                    <div class="message-body"><span class="message-box-arrow"></span>Aenean sollicitudin, lorem quis bibendum auctor, nisi elit consequat ipsum, nec sagittis sem nibh id elit. Duis sed odio sit amet.</div>
                                    <div class="message-date">29 Sep 2014: 09:55 AM</div>
                                </div>
                            </li>
                            <li class="employer">
                                <div class="author-name">Employer</div>
                                <div class="message-content">
                                    <div class="message-body"><span class="message-box-arrow"></span>This is Photoshop's version  of Lorem Ipsum. Proin gravida nibh vel velit auctor aliquet.</div>
                                    <div class="message-date">28 Sep 2014, 04:32 PM</div>
                                </div>
                                <div class="message-content">
                                    <div class="message-body"><span class="message-box-arrow"></span>This is Photoshop's version  of Lorem Ipsum. Proin gravida nibh vel velit auctor aliquet.</div>
                                    <div class="message-date">28 Sep 2014, 03:11 PM</div>
                                </div>
                            </li>
                            <li class="fund">
                                <div class="author-name">Fund</div>
                                <div class="message-content">
                                    <div class="message-body"><span class="message-box-arrow"></span>Aenean sollicitudin, lorem quis bibendum auctor, nisi elit consequat ipsum, nec sagittis sem nibh id elit. Duis sed odio sit amet.</div>
                                    <div class="message-date">29 Sep 2014: 09:55 AM</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="popup-box-footer radius6 noradiustop">
            <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Close</span></a>
            <div class="clear"></div>
        </div>
        <a class="close_btn"></a>
    </div>

</div> <!--end content-->
<script type="text/javascript">
(function($){
    $(document).ready(function(){
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

        $('rel[custom-popup]').cplightbox({});

        $('.tab-nav a').click(function(){
            $('.agreements-message-log-list').jScrollPane({
                autoReinitialise: true
            });
        });


        $('#delete-service-box .process-btn').click(function(){
            $('#delete-agreement-box .loading').show();
        });
        jQuery('#my-agreements .container .simple_tooltip').each(function(){
            jQuery(this).css("margin-left", '-' + jQuery(this).width()/2-5 + "px" );
        });

        $('.claim_agreement_confirm, .agreement_claim_confirm').on( 'click', function(){
            var is_valid = true;
            var form_id = $(this).data('id');
            if( ! $( '#' + form_id + ' .claim_file').val() ){
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
            $( this).parents('.claim-upload-box').find('.uploaded-files li a').html( $('.file-value').text() );
            $( this).parents('.claim-upload-box').find('.uploaded-files li .remove-icon').show();
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
