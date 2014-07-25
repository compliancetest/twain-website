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
            
            $result = $controller->subscribe($family_mark, $payment_method, $nickname, $user_id);
            
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
                    <div class="popup-box-content"><p class="message error">There is a problem to process your reqeust. Please refresh your page and try again.</p></div>                    
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
                        To subscribe, an account for your Organisation will need to be created by our support team, and you will be assigned as the administrator. Would you like to proceed?
                        <?php wp_nonce_field('signup-organisation-account', '_organisation_nonce') ?>                        
                    </div>                    
                    <div class="popup-box-footer radius6 noradiustop">
                        <a href="#" class="action-btn process-btn submit-btn"><span class="p"></span><span class="t">Confirm</span></a>
                        <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>            
                        <div class="clear"></div>
                    </div>
                    <a class="close_btn"></a>
                  </form>
                </div>
                <?php
                exit;
            } else {
                if (!groups_is_user_member($user_id, $community_id)) { //First, users should be a member of the community
                    ?>
                    <div class="popup-box" style="display: none; width: 500px">
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
                    </div>
                    <?php
                    exit;        
                }
                if(ct_get_organisation_unallocated_subscriptions($organisation->id, $familyMark) > 0) { //Has unallocated subscriptions
                    
                } else { //All subscriptions have been used, should contact to the admin
                    $organisation_admin = ct_get_organisation_admin($organisation->id);
                    ?>
                    <div class="popup-box" style="display: none; width: 500px">
                      <form name="" action="<?php echo site_url() ?>/index.php" method="post">
                        <div class="popup-box-header radius6 noradiusbottom">Request A Subscription</div>
                        <div class="popup-box-content">                        
                            Do you wish to request a subscription from your organisation administrator (<?php echo $organisation_admin->user_email?>)?
                            <?php wp_nonce_field('request-subscription', '_organisation_nonce') ?>                        
                            <input type="hidden" name="suite_id" value="<?php echo $suite_id?>" />
                        </div>                    
                        <div class="popup-box-footer radius6 noradiustop">
                            <a href="#" class="action-btn process-btn submit-btn"><span class="p"></span><span class="t">Confirm</span></a>
                            <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>            
                            <div class="clear"></div>
                        </div>
                        <a class="close_btn"></a>
                        
                      </form>
                    </div>
                    <?php
                    exit;
                }    
            } 
            
            exit;
        } else if(wp_verify_nonce($action, 'request-subscription')) { //Send Request a subscription to the organisation admin
            $user_id = get_current_user_id();
            
            if ($controller->send_subscription_request($user_id, $_POST['suite_id'])) {
                addMessage("Your request has been sent.");
            } else {
                addMessage($controller->last_message, "error");
            }
            wp_redirect(get_permalink($_POST['suite_id']));
            exit;
        }
    } 
}