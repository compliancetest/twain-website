<?php
/**
* Organisations
*/
require_once (dirname(__FILE__) . '/functions.php');
require_once (dirname(__FILE__) . '/class.organisation.php');
require_once (dirname(__FILE__) . '/controller.php');

add_action("init", "ct_process_organisation_action");
function ct_process_organisation_action()
{
    $action = isset($_REQUEST['_organisation_nonce']) ? $_REQUEST['_organisation_nonce'] : null;
    
    if ($action) {
        $controller = new CT_Organisation_Controller();
        //Purchase Subscription
        if (wp_verify_nonce($action, 'purchase_subscribe')) {
            if (!is_user_logged_in()) {
                echo "Please login to purchase a subcription.";
                exit;
            }
            
            $user_id = get_current_user_id();
            
            $payment_method = $_POST['payment_method'];
            $nickname = $_POST['nickname'];
            $family_mark =  $_POST['suite_family_mark'];
            $pricing_plan_id = intval( $_POST['pricing_plan_id'] );
            $result = $controller->subscribe($family_mark, $payment_method, $nickname, $user_id, $pricing_plan_id );
            
            if ($result) {
                addMessage("You successfully purchased subscription.");
            } else {
                addMessage($controller->last_message, 'error');
            }
            wp_redirect('/my-organisation/test-suites');
            exit;            
        } else if (wp_verify_nonce($action, "subscribe")) {
            $suite_id = $_GET['suite_id'];
            
            if (!is_user_logged_in()) {
                ?>
                <div class="popup-box" style="display: none; width: 500px">
                    <div class="popup-box-header radius6 noradiusbottom">Error!</div>
                    <div class="popup-box-content"><p class="message error">Please login to purchase a subcription.</p></div>
                    <div class="popup-box-footer radius6 noradiustop">
                        <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>            
                        <div class="clear"></div>
                    </div>
                    <a class="close_btn"></a>
                </div>
                <?php
                exit;
            }
            
            $user_id = get_current_user_id();
            
            $suiteClass = new TestSuite($suite_id);
            $familyMark = $suiteClass->loadfamilyMark();
            
            if (!$familyMark) {
                ?>
                <div class="popup-box" style="display: none; width: 500px">
                    <div class="popup-box-header radius6 noradiusbottom">Invalid Request!</div>
                    <div class="popup-box-content"><p class="message error">There is a problem to process your request. Please refresh your page and try again.</p></div>
                    <div class="popup-box-footer radius6 noradiustop">
                        <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>            
                        <div class="clear"></div>
                    </div>
                    <a class="close_btn"></a>
                </div>
                <?php
                exit;
            }
            
            $community_id = $suiteClass->loadSingleValue('community_id');
            
            $organisation = ct_get_user_organisation($user_id);
            
            if (!$organisation) { //Organisation doesn't exist
                ?>
                <div class="popup-box" style="display: none; width: 500px">
                  <form name="" action="<?php echo site_url() ?>/index.php" method="post">
                    <div class="popup-box-header radius6 noradiusbottom">Set Up An Account</div>
                    <div class="popup-box-content">                        
                        To subscribe, an account for your Organisation will need to be created by our support team, and you will be assigned as the administrator. 
                        Please ensure your organisation details are complete in your profile before proceeding. Would you like to proceed?
                        <?php wp_nonce_field('signup-organisation-account', '_organisation_nonce') ?>                        
                    </div>                    
                    <div class="popup-box-footer radius6 noradiustop">
                        <a href="#" class="action-btn process-btn submit-btn" onclick="jQuery(this).parents('form').find('.loading').show()"><span class="p"></span><span class="t">Confirm</span></a>
                        <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>            
                        <div class="clear"></div>
                    </div>
                    <a class="close_btn"></a>
                    <input type="hidden" name="suite_id" value="<?php echo $suiteClass->id ?>" />
                    <input type="hidden" class="pricing_plan_id" name="pricing_plan_id" value="<?php echo $_REQUEST['plan_id'];?>" />
                    <div class="loading loading-with-text"><div><b>SUBMITTING REQUEST</b><span>Please wait...</span></div></div>
                  </form>
                </div>
                <?php
                exit;
            } else {
                if (!groups_is_user_member($user_id, $community_id)) { //First, users should be a member of the community                
                    ?>
                    <div class="popup-box" style="display: none; width: 500px">                        
                        <?php if(groups_check_for_membership_request($user_id, $community_id)) : ?>
                        <div class="popup-box-header radius6 noradiusbottom">Joining The Community</div>
                        <div class="popup-box-content">
                            You already sent the membership request for the community. Please wait until the community admin approve your request.                            
                        </div>                    
                        <div class="popup-box-footer radius6 noradiustop">
                            <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>            
                            <div class="clear"></div>
                        </div>
                        <a class="close_btn"></a>
                        <?php else : ?>
                        <div class="popup-box-header radius6 noradiusbottom">Joining The Community</div>
                        <div class="popup-box-content">                        
                            You need to join the community before subscribing. Do you wish to do that now?
                        </div>                    
                        <div class="popup-box-footer radius6 noradiustop">
                            <a href="<?php echo cp_get_group_permalink_by_id($community_id) ?>" class="action-btn process-btn"><span class="p"></span><span class="t">Confirm</span></a>
                            <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>            
                            <div class="clear"></div>
                        </div>
                        <a class="close_btn"></a>
                        <?php endif; ?>                            
                    </div>
                    <?php
                    exit;        
                }
                if(ct_get_organisation_unallocated_subscriptions($organisation->id, $familyMark) > 0) { //Has unallocated subscriptions
                ?>
                <div class="popup-box" style="display: none; width: 500px">
                  <form name="" id="confirmSubscriptionForm" action="<?php echo site_url() ?>/index.php" method="post">
                    <div class="popup-box-header radius6 noradiusbottom">Confirm Subscription</div>
                    <div class="popup-box-content grid-box-body">                                                
                        <div class="field-row">
                            <div class="grid-cell">
                                 <input type="checkbox" name="agree_terms" value="agree" id="agree_customer_terms"> I agree with the <a href="https://www.compliancetest.net/customer-tc/" target="_blank">Terms & Conditions</a>
                            </div>                
                            <div class="clear"></div>
                        </div>                      
                        <?php wp_nonce_field('confirm-subscribe', '_organisation_nonce') ?>                        
                        <input type="hidden" name="suite_id" value="<?php echo $suite_id?>" />
                    </div>                    
                    <div class="popup-box-footer radius6 noradiustop">
                        <a href="#" class="action-btn process-btn submit-btn"><span class="p"></span><span class="t">Confirm</span></a>
                        <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>            
                        <div class="clear"></div>
                    </div>
                    <a class="close_btn"></a>                        
                    <div class="loading loading-with-text"><div><b>SUBMITTING REQUEST</b><span>Please wait...</span></div></div>                    
                  </form>
                  <script type="text/javascript">
                      jQuery('#confirmSubscriptionForm').submit(function(){
                          jQuery('#confirmSubscriptionForm .message').remove();  
                          if(!jQuery('#confirmSubscriptionForm #agree_customer_terms').prop('checked'))
                          {
                              jQuery('#confirmSubscriptionForm .popup-box-footer').prepend('<div class="message error">You must agree to our Terms & Conditions.</div>');
                              return false;
                          }
                          jQuery('#confirmSubscriptionForm .loading').show();
                          return true;
                      })
                  </script>
                </div>
                <?php
                } else { //All subscriptions have been used, should contact to the admin
                    $organisation_admin = ct_get_organisation_admin($organisation->id);
                    ?>
                    <div class="popup-box" style="display: none; width: 500px">
                      <form name="" action="<?php echo site_url() ?>/index.php" method="post">
                        <div class="popup-box-header radius6 noradiusbottom">Request A Subscription</div>
                        <div class="popup-box-content">                        
                            There are currently no organisation subscriptions available to allocate to you.
                            Do you wish to request a subscription from your organisation administrator (<?php echo $organisation_admin->user_email?>)?
                            <?php wp_nonce_field('request-subscription', '_organisation_nonce') ?>                        
                            <input type="hidden" name="suite_id" value="<?php echo $suite_id?>" />
                        </div>                    
                        <div class="popup-box-footer radius6 noradiustop">
                            <a href="#" class="action-btn process-btn submit-btn" onclick="jQuery(this).parents('form').find('.loading').show()"><span class="p"></span><span class="t">Confirm</span></a>
                            <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>            
                            <div class="clear"></div>
                        </div>
                        <a class="close_btn"></a>                        
                        <div class="loading loading-with-text"><div><b>SUBMITTING REQUEST</b><span>Please wait...</span></div></div>
                      </form>
                    </div>
                    <?php
                    exit;
                }    
            } 
            
            exit;
        } else if(wp_verify_nonce($action, 'signup-organisation-account')) { //Send signup organisation request to the site admin
            if (!is_user_logged_in()) {
                wp_redirect(get_site_url());
                exit;
            }
            
            $user_id = get_current_user_id();
            
            if ($organisation = ct_get_user_organisation($user_id))
            {
                addMessage("There is already the organisation that matches with your email address", "warning");                
            } else {
                $controller->send_signup_organisation_request($user_id, intval( $_REQUEST['pricing_plan_id'] ) );
                addMessage("Your request has been sent.");
            }
            wp_redirect(get_permalink($_POST['suite_id']));
            exit;
        } else if(wp_verify_nonce($action, 'request-subscription')) { //Send Request a subscription to the organisation admin
            if (!is_user_logged_in()) {
                wp_redirect(get_site_url());
                exit;
            }
            $user_id = get_current_user_id();
            
            if ($controller->send_subscription_request($user_id, $_POST['suite_id'])) {
                addMessage("Your request has been sent.");
            } else {
                addMessage($controller->last_message, "error");
            }
            wp_redirect(get_permalink($_POST['suite_id']));
            exit;
        } else if(wp_verify_nonce($action, 'edit-subscription')) { //Edit subscription nickname and assignee
            if (!is_user_logged_in()) {
                wp_redirect(get_site_url());
                exit;
            }
            $id = $_REQUEST['id'];
            
            $subscription = ct_get_organisation_subscription_by_id($id);
            if($subscription)
                $organisationClass = new CT_Organisation($subscription->organisation_id);
                
            if (!$subscription || !can_manage_organisation_subscription($organisationClass->id) ) {
            ?>
                <div class="popup-box" style="display: none; width: 500px">
                    <div class="popup-box-header radius6 noradiusbottom">Invalid Request!</div>
                    <div class="popup-box-content"><p class="message error">The subscription does not exist or you are not allowed to edit it.</p></div>                    
                    <div class="popup-box-footer radius6 noradiustop">
                        <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>            
                        <div class="clear"></div>
                    </div>
                    <a class="close_btn"></a>
                </div>
            <?php
            } else {
                $members= $organisationClass->get_organisation_members();
            ?>
                <div class="popup-box" style="display: none; width: 500px" id="update-subscription-box">
                  <form name="" id="updateSubscriptionForm" action="<?php echo site_url() ?>/index.php" method="post">
                    <div class="popup-box-header radius6 noradiusbottom">Edit Subscription</div>
                    <div class="popup-box-content grid-box-body">                                                
                        <div class="field-row">
                            <div class="grid-cell">
                                <label>Nickname</label>
                                <input type="text" name="nickname" id="nickname" value="<?php echo $subscription->nickname?>" class="input" maxlength="50" />                    
                            </div>                
                            <div class="clear"></div>
                            <div class="space20"></div>
                            <div class="grid-cell">
                                <label>Pricing Plan</label>
                                <select id="pricing_plan_id" class="pricing_plan_id">
                                    <?php
                                        $suite = new TestSuite( $subscription->suite_family_mark );
                                        $suite->load();
                                    ?>
                                    <?php foreach( $suite->test_suite_plans AS $pp ):?>
                                        <?php $pricing_plan = new PricingPlan( $pp );?>
                                            <option value="<?php echo $pricing_plan->id;?>"<?php if( $pricing_plan->id == $subscription->pricing_plan_id ):?> selected="selected"<?php endif;?> ><?php echo $pricing_plan->title;?></option>
                                        <?php endforeach;?>
                                </select>
                                <input type="hidden" id="pricing_plan_id_hidden" name="pricing_plan_id" value="">
                                <a href="<?php echo the_permalink() ?>?_organisation_nonce=<?php echo wp_create_nonce('get_price_plan') ?>&suite_id=<?php echo $subscription->suite_family_mark;?>&is_edit=1&sid=<?php echo $subscription->id;?>&pricing_plan_id=<?php echo $subscription->pricing_plan_id;?>" class="edit_subsc" rel="custom-popup" cp-type="ajax" cp-closeWhenClickOveraly=0 cp-removeBoxAfterClose=1><span class="p"></span><span class="t">Select Pricing Plan</span></a>
                                <script>
                                    jQuery( document).ready( function( $ ){
                                        jQuery(".edit_subsc").off("click").cplightbox( {'href': '<?php echo the_permalink() ?>?_organisation_nonce=<?php echo wp_create_nonce('get_price_plan') ?>&suite_id=<?php echo $subscription->suite_family_mark;?>&is_edit=1&sid=<?php echo $subscription->id;?>&pricing_plan_id=<?php echo $subscription->pricing_plan_id;?>' });
                                        $('.pricing_plan_id').on('change', function(){
                                            $('#pricing_plan_id_hidden').val( $(this).val() )
                                            if( jQuery('.edit_subsc').attr('href').indexOf( 'pricing_plan_id' ) == -1 ){
                                                var new_url = jQuery('.edit_subsc').attr('href') ;
                                            } else {
                                                var new_url = jQuery('.edit_subsc').attr('href').split('&pricing_plan_id');
                                                new_url = new_url[0] ;
                                            }
                                            new_url = new_url+'&pricing_plan_id='+$(this).val();
                                            jQuery('.edit_subsc').attr( 'href', new_url );
                                            jQuery(".edit_subsc").off("click").cplightbox( {'href': new_url});
                                        })
                                    })
                                </script>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <!--<div class="field-row">
                            <div class="grid-cell">
                                <label>Assignee</label>
                                <select name="user_id" id="user_id" class="select">
                                    <option value="0">Not Assigned</option>
                                  <?php foreach($members as $member): ?>
                                    <option value="<?php echo $member->ID?>" <?php echo $member->ID == $subscription->user_id ? 'selected="selected"' : ''?>><?php echo $member->display_name?> (<?php echo $member->user_email?>)</option>
                                  <?php endforeach; ?>
                                </select>
                            </div>                
                            <div class="clear"></div>
                        </div>                        
                        --><?php wp_nonce_field('save-subscription', '_organisation_nonce') ?>                        
                        <input type="hidden" name="id" value="<?php echo $subscription->id?>" />                        
                    </div>                    
                    <div class="popup-box-footer radius6 noradiustop">
                        <a href="#" class="action-btn process-btn submit-btn"><span class="p"></span><span class="t">Save</span></a>
                        <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>            
                        <div class="clear"></div>
                    </div>
                    <div class="loading loading-with-text"><div><b>SAVING DATA</b><span>Please wait...</span></div></div>
                    <a class="close_btn"></a>                    
                  </form>
                </div>
            <?php
            }
            
            exit;
        } else if(wp_verify_nonce($action, 'save-subscription')) {
            //Update Subscription Nickname and Assignee
            $id = $_POST['id'];
            
            $subscription = ct_get_organisation_subscription_by_id($id);
            
            if (!$subscription || !can_manage_organisation_subscription($subscription->organisation_id) ) {
                addMessage('The subscription does not exist or you are not allowed to edit it.', 'error');
            } else if(!$_POST['nickname']) {
                addMessage('The subscription nickname should not be empty', 'error');
            } else {
                $controller->save_subscription($subscription->id, $_POST['nickname'], $_POST['pricing_plan_id']);
                addMessage('The subscription has been updated.');
            }
            
            wp_redirect('/my-organisation/test-suites');
            exit;
        } else if(wp_verify_nonce($action, 'confirm-subscribe')) {
            if (!is_user_logged_in()) {
                wp_redirect(get_site_url());
                exit;
            }
            
            $suite_id = $_POST['suite_id'];
            
            $suite_class = new TestSuite($suite_id);
            $family_mark = $suite_class->loadfamilyMark();
            
            if (!$family_mark) {
                addMessage("Your request is not correct.", "error");
                wp_redirect('/my-test-suites');
                exit;
            }
            
            $user_id = get_current_user_id();
            
            $community_id = $suite_class->loadSingleValue('community_id');
            
            $organisation = ct_get_user_organisation($user_id);
            
            if (!$organisation || !groups_is_user_member($user_id, $community_id)) {
                addMessage("Invalid Request!", "error");
            } else {
                if ($sid = $controller->allocate_subscription_to_user($user_id, $organisation->id, $family_mark)) {
                    $controller->create_user_harness_detail($user_id, $suite_id, $organisation->id, $sid);
                    addMessage('You subscribed successfully');
                } else {
                    addMessage($controller->last_message, "error");                    
                }
            }            
            wp_redirect(get_permalink($suite_id));
            exit;
        } else if(wp_verify_nonce($action, 'unsubscribe')) {
            $user_id = get_current_user_id();
            $id = $_REQUEST['id'];
            
            $subscription = ct_get_organisation_subscription_by_id($id);
            
            if (!$subscription || !$user_id || $subscription->user_id != $user_id)  {
                ?>
                <div class="popup-box" style="display: none; width: 450px">
                    <div class="popup-box-header radius6 noradiusbottom">Invalid Request!</div>
                    <div class="popup-box-content"><p class="message error">Your request is not valid.</p></div>                    
                    <div class="popup-box-footer radius6 noradiustop">
                        <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>            
                        <div class="clear"></div>
                    </div>
                    <a class="close_btn"></a>
                </div>
                <?php
            } else {
                ?>
                <div class="popup-box" id="unsubscription-confirm-box" style="display: none; width: 450px;">
                    <form name="unsubscribe-form" action="/index.php" method="post">
                        <div class="popup-box-header radius6 noradiusbottom">Confirm Subscription Release</div>
                        <div class="popup-box-content grid-box-body">    
                            <p>Releasing this subscription will make it available to other testers in your organisation. </p>
                            <p>Are you sure that you want to release this subscription?</p>
                        </div>
                        <div class="popup-box-footer radius6 noradiustop">      
                            <div class="right">
                                <a href="#" class="action-btn process-btn submit-btn"><span class="p"></span><span class="t">OK</span></a>            
                                <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>            
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="loading loading-with-text radius6"><div><b>UNSUBSCRIBING</b><span>Please wait...</span></div></div>
                        <a class="close_btn"></a>
                        <input type="hidden" name="id" value="<?php echo $subscription->id?>" />    
                        <?php wp_nonce_field('confirm-unsubscribe', '_organisation_nonce'); ?>
                        <?php if($_REQUEST['return']){ ?>
                        <input type="hidden" name="return" value="<?php echo $_REQUEST['return']?>" />
                        <?php } ?>
                    </form>
                    <script type="text/javascript">
                        jQuery('#unsubscription-confirm-box a.submit-btn').click(function(){
                            jQuery(this).parents('form').find('.loading').show(); 
                            jQuery(this).parents('form').submit();
                        })
                    </script>
                </div>
                <?php
            }
            exit;
        } else if(wp_verify_nonce($action, 'organisation-unsubscribe')) {
            $user_id = get_current_user_id();
            $id = $_REQUEST['id'];
            
            $subscription = ct_get_organisation_subscription_by_id($id);
            
            if (!$subscription || !$user_id || !ct_is_organisation_admin($user_id, $subscription->organisation_id)) {
                ?>
                <div class="popup-box" style="display: none; width: 450px">
                    <div class="popup-box-header radius6 noradiusbottom">Invalid Request!</div>
                    <div class="popup-box-content"><p class="message error">Your request is not valid.</p></div>                    
                    <div class="popup-box-footer radius6 noradiustop">
                        <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>            
                        <div class="clear"></div>
                    </div>
                    <a class="close_btn"></a>
                </div>
                <?php
            } else {
                ?>
                <div class="popup-box" id="unsubscription-confirm-box" style="display: none; width: 450px;">
                    <form name="unsubscribe-form" action="/index.php" method="post">
                        <div class="popup-box-header radius6 noradiusbottom">Confirm Subscription Cancellation</div>
                        <div class="popup-box-content grid-box-body">    
                            <p>Are you sure that you want to permanently cancel this subscription?</p>
                            <p>If your subscription is active, it will not be removed until the end of the month, and testing can continue as normal until then.</p>
                            <p>If you want the subscription cancelled immediately, please select the checkbox below.</p>
                        </div>
                        <div class="popup-box-footer radius6 noradiustop">              
                            <label class="left">
                                <input type="checkbox" id="delete-now" name="delete-now" <?php if($subscription->status == 'Unsubscribing'){ ?> disabled="disabled" checked="checked" <?php } ?> /> Unsubscribe immediately
                            </label>
                            <div class="right">
                                <a href="#" class="action-btn process-btn submit-btn"><span class="p"></span><span class="t">OK</span></a>            
                                <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>            
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="loading loading-with-text radius6"><div><b>UNSUBSCRIBING</b><span>Please wait...</span></div></div>
                        <a class="close_btn"></a>
                        <input type="hidden" name="id" value="<?php echo $subscription->id?>" />    
                        <?php wp_nonce_field('confirm-organisation-unsubscribe', '_organisation_nonce'); ?>
                        <?php if($_REQUEST['return']){ ?>
                        <input type="hidden" name="return" value="<?php echo $_REQUEST['return']?>" />
                        <?php } ?>
                    </form>
                    <script type="text/javascript">
                        jQuery('#unsubscription-confirm-box a.submit-btn').click(function(){
                            jQuery(this).parents('form').find('.loading').show(); 
                            jQuery(this).parents('form').submit();
                        })
                    </script>
                </div>
                <?php
            }
            exit;
        } else if(wp_verify_nonce($action, 'confirm-unsubscribe')) {
            $user_id = get_current_user_id();
            $id = $_POST['id'];
            $subscription = ct_get_organisation_subscription_by_id($id);
            
            $return = isset($_POST['return']) ? base64_decode($_POST['return']) : get_site_url();
            
            if (!$subscription || !$user_id || $subscription->user_id != $user_id) {
                addMessage('Invalid Request!', 'error');
            } else {
                //Delete User Subscription
                $controller->delete_user_subscription($user_id, $subscription->id);
                addMessage('Your subscription has been cancelled');
            }
            
            wp_redirect($return);
            exit;
            
        } else if(wp_verify_nonce($action, 'confirm-organisation-unsubscribe')) {
            $user_id = get_current_user_id();
            $id = $_POST['id'];
            $subscription = ct_get_organisation_subscription_by_id($id);
            
            $return = isset($_POST['return']) ? base64_decode($_POST['return']) : get_site_url();
            
            if (!$subscription || !$user_id || !ct_is_organisation_admin($user_id, $subscription->organisation_id)) {
                addMessage('Invalid Request!', 'error');
            } else {
                if (isset($_POST['delete-now']) || $subscription->status == 'Unsubscribing')
                    $controller->delete_organisation_subscription($subscription->id);
                else
                    $controller->unsubscribe_organisation_subscription($subscription->id);
                
                addMessage('Your subscription has been cancelled');
            }
            
            wp_redirect($return);
            exit;
            
        }  else if(wp_verify_nonce($action, 'get_price_plan')) {
            include(dirname(__FILE__) . '/../../content/org-pricing-page.php');
            exit;
        } else if(wp_verify_nonce($action, 'remove-membership')) {
            global $wpdb;
            
            $id = $_POST['id'];
            if (!is_user_logged_in() || !$id) {                
                addMessage('Invalid Request!', 'error');
            } else {
                $user_id = get_current_user_id();
            
                $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}organisations_members WHERE id=%d", $id);
                $row = $wpdb->get_row($query);
                
                if (!$row) {
                    addMessage('Invalid Request!', 'error');
                } else if (!ct_is_organisation_admin($user_id, $row->organisation_id)) {
                    addMessage('Permission Denied!', 'error');
                } else if($row->is_admin == 1) {
                    addMessage("You can't remove the organisation admin from the organisation.", 'error');
                } else {                    
                    $controller->delete_membership($row->user_id, $row->organisation_id);    
                    addMessage("The user was removed successfully.");
                }
            }
            
            wp_redirect(get_site_url() . "/my-organisation/users");
            exit;
        } else if(wp_verify_nonce($action, 'edit-privilege')) {
            $user_id = get_current_user_id();
            $member_id = $_REQUEST['user_id'];
            
            $membership = ct_get_user_organisation_membership($member_id);
            
            if (!$member_id || !$membership || !ct_is_organisation_admin($user_id, $membership->organisation_id)) {
                ?>
                <div class="popup-box" style="display: none; width: 450px">
                    <div class="popup-box-header radius6 noradiusbottom">Invalid Request!</div>
                    <div class="popup-box-content"><p class="message error">Your request is not valid.</p></div>                    
                    <div class="popup-box-footer radius6 noradiustop">
                        <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>            
                        <div class="clear"></div>
                    </div>
                    <a class="close_btn"></a>
                </div>
                <?php
            } else {
                $privileges = ct_get_privileges();
                $current_privileges = ct_get_user_privileges($member_id, $membership->organisation_id);
                $checked_privileges = array();
                foreach($current_privileges as $cp)
                    $checked_privileges[] = $cp->id;
                ?>
                <div class="popup-box" id="edit-privilege-box" style="display: none; width: 450px;">
                    <form name="privilege-form" action="/index.php" method="post">
                        <div class="popup-box-header radius6 noradiusbottom">Edit User Privileges</div>
                        <div class="popup-box-content grid-box-body">    
                        <?php foreach($privileges as $p){ ?>
                            <div class="field-row">
                                <div class="grid-cell width5P">
                                    <input type="checkbox" name="privilege[]" value="<?php echo $p->id ?>" <?php echo in_array($p->id, $checked_privileges) ? 'checked="checked"' : ''?>  /> 
                                </div>
                                <div class="grid-cell width90P">
                                    <?php echo $p->title?>
                                    <br />
                                    <?php echo $p->description?>
                                </div>
                                <div class="clear"></div>
                            </div>
                        <?php } ?>
                        </div>
                        <div class="popup-box-footer radius6 noradiustop">      
                            <div class="right">
                                <a href="#" class="action-btn process-btn submit-btn"><span class="p"></span><span class="t">Confirm</span></a>            
                                <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>            
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="loading loading-with-text radius6"><div><b>SAVING DATA</b><span>Please wait...</span></div></div>
                        <a class="close_btn"></a>
                        <input type="hidden" name="user_id" value="<?php echo $member_id?>" />    
                        <input type="hidden" name="organisation_id" value="<?php echo $membership->organisation_id?>" />    
                        <?php wp_nonce_field('save-privilege', '_organisation_nonce'); ?>
                        <?php if($_REQUEST['return']){ ?>
                        <input type="hidden" name="return" value="<?php echo $_REQUEST['return']?>" />
                        <?php } ?>
                    </form>
                    <script type="text/javascript">
                        jQuery('#edit-privilege-box a.submit-btn').click(function(){
                            jQuery(this).parents('form').find('.loading').show(); 
                            jQuery(this).parents('form').submit();
                        })
                    </script>
                </div>
                <?php
            }
            exit;
        } else if(wp_verify_nonce($action, 'save-privilege')) {
            $user_id = get_current_user_id();
            
            $member_id = $_REQUEST['user_id'];
            
            $membership = ct_get_user_organisation_membership($member_id);
            
            if (!$member_id || !$membership || !ct_is_organisation_admin($user_id, $membership->organisation_id)) {
                addMessage("Invalid Reqeust!", "error");
            } else {
                $controller->remove_privilege($member_id);
                foreach($_POST['privilege'] as $privilege)
                {
                    $controller->add_privilege($member_id, $membership->organisation_id, $privilege);
                }
                addMessage("Successfully Saved!");
            }
            
            wp_redirect(get_site_url() . "/my-organisation/users/");
            exit;
        }
    } 
}