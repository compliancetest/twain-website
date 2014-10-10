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
<div class="content" id="my-agreements">
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
                                   <?php if( true ):?>
                                       <div class="tr">
                                           <div class="td td-full">No agreement yet</div>
                                           <div class="clear"></div>
                                       </div>
                                   <?php else: ?>
                                       <div class="tr clearfix">
                                           <div class="td td-agreement-id"><a href="#agreement-details-popup" rel="custom-popup">1234567:887653:01</a></div>
                                           <div class="td td-entity-name">BHP Steel Pty</div>
                                           <div class="td td-identifier">887653</div>
                                           <div class="td td-type">ABN</div>
                                           <div class="td td-protocol">ebMS3-GW</div>
                                           <div class="td td-end-point"><a href="#">Westpac</a></div>
                                           <div class="td td-contact">
                                               <?php $email = 'bob@bhp.com'; ?>
                                               <?php if (strlen($email) > 22): ?>
                                                   <div class="has-tooltip" title="<?php echo $email; ?>">
                                                       <a href="mailto:bob@bhp.com"><?php echo $email = substr($email,0,22)."..."; ?></a>
                                                   </div>
                                               <?php else: ?>
                                                   <a href="mailto:bob@bhp.com"><?php echo $email; ?></a>
                                               <?php endif; ?>
                                           </div>
                                           <div class="td td-status"><span class="status-pending">Pending</span></div>
                                           <div class="td td-actions">
                                               <a href="#">Accept</a>&nbsp;|&nbsp;<a href="#deny-agreement-popup" rel="custom-popup">Reject</a>
                                           </div>
                                       </div>
                                       <div class="tr clearfix">
                                           <div class="td td-agreement-id"><a href="#agreement-details-popup" rel="custom-popup">1234567:773645:02</a></div>
                                           <div class="td td-entity-name">Woolworths Pty</div>
                                           <div class="td td-identifier">773645</div>
                                           <div class="td td-type">ABN</div>
                                           <div class="td td-protocol">ebMS3-GW</div>
                                           <div class="td td-end-point"><a href="#">OzEDI</a></div>
                                           <div class="td td-contact">
                                               <?php $email = 'david.schwarzenegger@ozedi.com'; ?>
                                               <?php if (strlen($email) > 22): ?>
                                                   <div class="has-tooltip" title="<?php echo $email; ?>">
                                                       <a href="mailto:bob@bhp.com"><?php echo $email = substr($email,0,22)."..."; ?></a>
                                                   </div>
                                               <?php else: ?>
                                                   <a href="mailto:bob@bhp.com"><?php echo $email; ?></a>
                                               <?php endif; ?>
                                           </div>
                                           <div class="td td-status"><span class="status-testing">Testing</span></div>
                                           <div class="td td-actions">
                                               <a href="#claim-popup" rel="custom-popup">Claim</a>&nbsp;|&nbsp;<a href="#deny-agreement-popup" rel="custom-popup">Cancel</a>
                                           </div>
                                       </div>
                                       <div class="tr clearfix">
                                           <div class="td td-agreement-id"><a href="#agreement-details-popup" rel="custom-popup">1234567:665385:01</a></div>
                                           <div class="td td-entity-name">Coles Pty</div>
                                           <div class="td td-identifier">665385</div>
                                           <div class="td td-type">ABN</div>
                                           <div class="td td-protocol">ebMS3-GW</div>
                                           <div class="td td-end-point"><a href="#">Westpac</a></div>
                                           <div class="td td-contact">
                                               <?php $email = 'janes@coles.com'; ?>
                                               <?php if (strlen($email) > 22): ?>
                                                   <div class="has-tooltip" title="<?php echo $email; ?>">
                                                       <a href="mailto:bob@bhp.com"><?php echo $email = substr($email,0,22)."..."; ?></a>
                                                   </div>
                                               <?php else: ?>
                                                   <a href="mailto:bob@bhp.com"><?php echo $email; ?></a>
                                               <?php endif; ?>
                                           </div>
                                           <div class="td td-status"><span class="status-claimed">Claimed</span></div>
                                           <div class="td td-actions">
                                               <a href="#agreement-confirm-popup" rel="custom-popup">Confirm</a>&nbsp;|&nbsp;<a href="#deny-agreement-popup" rel="custom-popup">Fail</a>
                                           </div>
                                       </div>
                                       <div class="tr clearfix">
                                           <div class="td td-agreement-id"><a href="#agreement-details-popup" rel="custom-popup">1234567:553465:01</a></div>
                                           <div class="td td-entity-name">Telstra Pty</div>
                                           <div class="td td-identifier">Emp AB</div>
                                           <div class="td td-type">ABN</div>
                                           <div class="td td-protocol">ebMS3-GW</div>
                                           <div class="td td-end-point"><a href="#">Westpac</a></div>
                                           <div class="td td-contact">
                                               <?php $email = 'bill@telstra.com'; ?>
                                               <?php if (strlen($email) > 22): ?>
                                                   <div class="has-tooltip" title="<?php echo $email; ?>">
                                                       <a href="mailto:bob@bhp.com"><?php echo $email = substr($email,0,22)."..."; ?></a>
                                                   </div>
                                               <?php else: ?>
                                                   <a href="mailto:bob@bhp.com"><?php echo $email; ?></a>
                                               <?php endif; ?>
                                           </div>
                                           <div class="td td-status"><span class="status-verified">Verified</span></div>
                                           <div class="td td-actions">
                                               <a href="#">View</a>
                                           </div>
                                       </div>
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
    <div class="popup-box" id="delete-service-box" style="display: none; width: 500px">
        <div class="popup-box-header radius6 noradiusbottom">Confirm Deletion</div>
        <div class="popup-box-content"> 
            Are you sure that you want to delete this service?
        </div>
        <div class="popup-box-footer radius6 noradiustop">                   
            <div class="loading loading-with-text radius6"><div><b>DELETING AGREEMENT</b><span>Please wait...</span></div></div>
            <a href="#" class="action-btn process-btn"><span class="p"></span><span class="t">Confirm</span></a>            
            <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>            
            <div class="clear"></div>
        </div>
        <a class="close_btn"></a>                
    </div>

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

    <div class="popup-box claim-popup-box" id="claim-popup" style="display: none; width: 500px">
        <div class="popup-box-header radius6 noradiusbottom">Pop-up Title</div>
        <div class="popup-box-content">
            <p>Please upload screenshots from your business system as an audit record of the successful test.</p>
            <p class="note">Please zip the files if more than one.</p>
            <div class="claim-upload-box clearfix">
                <input type="file" name="file" id="file" class="left input-file" file-type="image" file-extensions="(.jpg, .png, .gif or .jpeg file)" />
                <ul class="uploaded-files">
                    <li><a href="#">Screenshots.zip</a><span class="remove-icon"></span></li>
                </ul>
            </div>
            <div class="claim-test-scope">
                <h4>Test scope:</h4>
                <ul class="claim-test-scopes-list">
                    <li>
                        <label><input type="checkbox"/>Registration</label>
                    </li>
                    <li>
                        <label><input type="checkbox"/>Contribution</label>
                    </li>
                    <li>
                        <label><input type="checkbox"/>Error Responses</label>
                    </li>
                    <li>
                        <label><input type="checkbox"/>Sample Scope</label>
                    </li>
                    <li>
                        <label><input type="checkbox"/>Lorem Ipsum Scope</label>
                    </li>
                </ul>
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

    <div class="popup-box claim-popup-box" id="agreement-confirm-popup" style="display: none; width: 500px">
        <div class="popup-box-header radius6 noradiusbottom">Pop-up Title</div>
        <div class="popup-box-content">
            <p>Please upload screenshots from your business system as an audit record of the successful test.</p>
            <p class="note">Please zip the files if more than one.</p>
            <div class="claim-upload-box clearfix">
                <input type="file" name="file" id="file" class="left input-file" file-type="image" file-extensions="(.jpg, .png, .gif or .jpeg file)" />
                <ul class="uploaded-files">
                    <li><a href="#">Screenshots.zip</a><span class="remove-icon"></span></li>
                </ul>
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


    <div class="popup-box" id="delete-service-box" style="display: none; width: 500px">
        <div class="popup-box-header radius6 noradiusbottom">Confirm Deletion</div>
        <div class="popup-box-content">
            Are you sure that you want to delete this service?
        </div>
        <div class="popup-box-footer radius6 noradiustop">
            <div class="loading loading-with-text radius6"><div><b>DELETING AGREEMENT</b><span>Please wait...</span></div></div>
            <a href="#" class="action-btn process-btn"><span class="p"></span><span class="t">Confirm</span></a>
            <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>
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


    })
})(jQuery)
    
</script>
<?php
get_footer();
?>
