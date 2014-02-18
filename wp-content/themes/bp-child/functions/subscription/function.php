<?php
/***
* Define the functions that are related with User Subscriptions
* 
*/

/** 
* Process Eway Payment
* 
* @param Int $customer_id
* @param Int $amount
*/
function processEwayPayment($eway_profile_id, $amount, $invoiceDescription = '')
{
    $webserviceURL = get_eway_token_webservice_url();
    $customerID = get_eway_customer_id();
    $userName = get_eway_user_name();
    $userPWD = get_eway_user_pwd();
    
    require_once(THE_FUNCTION . '/soap/nusoap.php');
    
    $client = new nusoap_client($webserviceURL, false);
    $err = $client->getError();
    
    if ($err) {
        return array('ewayTrxnStatus' => 'False', 'ewayTrxnError' => 'Soap Construction Error: ' . $err);
    }
    
    $client->namespaces['man'] = 'https://www.eway.com.au/gateway/managedpayment';
    $headers = "<man:eWAYHeader><man:eWAYCustomerID>" . $customerID . "</man:eWAYCustomerID><man:Username>" . $userName . "</man:Username><man:Password>" . $userPWD . "</man:Password></man:eWAYHeader>";
    $client->setHeaders($headers);    
    
    $requestbody = array(
        'man:managedCustomerID' => $eway_profile_id,
        'man:amount' => $amount * 100,
//            'man:cvn' => $card->cvn,
//        'man:invoiceReference' => '',
        'man:invoiceDescription' => $invoiceDescription
    );
    $soapaction = 'https://www.eway.com.au/gateway/managedpayment/ProcessPayment';
    $result = $client->call('man:ProcessPayment', $requestbody, '', $soapaction);
    
    return $result;
}

/**
* Get the list of the subscribed suites
* 
* @param Int $user_id
* @return []
*/
function getUserSubscribedSuites($user_id = null)
{
    global $wpdb;
    
    if($user_id == null)
        $user_id = get_current_user_id();
        
    $query = $wpdb->prepare(
        "SELECT sp.*, p.post_title as `name` FROM " . $wpdb->prefix . "users_purchases AS sp " .
        "LEFT JOIN " . $wpdb->posts . " AS p ON p.ID=sp.suite_id " .
        "WHERE sp.user_id=%d AND p.ID IS NOT NULL", $user_id
    );
    
    $rows = $wpdb->get_results($query);
    
    return $rows;
}

/**
* Calculate the first paid amount by Pro Rata
* 
* @param Int $monthly_price
*/
function calculateFirstPaymentAmount($monthly_price)
{
    $remainedDay = (strtotime(date("Y-m-d", mktime(0, 0, 0, date('n') + 1, 1, date("Y")))) - strtotime(date("Y-m-d"))) / 86400;
    $totalDay = date("t");
    
    return ceil($monthly_price * ($remainedDay / $totalDay));
}

/**
* Cancel Subscriptions and Remove All Data of it
* 
* @param CT_Subscription $subscription
*/
function removeSubscription($subscription)
{
    global $wpdb, $CPRest;
    
    $user = get_userdata(get_current_user_id());
    
    if(intval($subscription->esb_user_id) > 0)
    {
        //Remove Backend Account
        $data = '<api:deleteUserRequest xmlns:api="http://compliancetest.net/api">
                    <api:user>
                        <api:userId>' . $subscription->esb_user_id . '</api:userId>                        
                    </api:user>
                </api:deleteUserRequest>';
        
        $result = $CPRest->doUserAPI('user/delete', $data);
        
        $resultDoc = new DOMDocument(); 
    }
    
    if(intval($subscription->esb_user_id) > 0 && (!$result || !$resultDoc->loadXML($result)))
    {
        addMessage("There was a problem deleting your test credentials.", "error");
        
    }else{
        if(intval($subscription->esb_user_id) > 0 && $resultDoc->getElementsByTagName('code')->item(0)->nodeValue == 'ERROR')
        {
            addMessage('There was a problem deleting your test credentials: ' . $resultDoc->getElementsByTagName('error')->item(0)->nodeValue, 'error');
        }else{                        
            //Remove Subscription
            $subscription->delete();            
            
            addMessage('Your subscription has been cancelled.');
        }
    }
    
    return;
}

function render_unsubscription_popup($return = null)
{
    ?>
    <div class="popup-box" id="unsubscription-confirm-box" style="display: none; width: 450px;">
        <form name="unsubscribe-form" action="/" method="post">
            <div class="popup-box-header radius6 noradiusbottom">Confirm unsubscribing</div>        
            <div class="popup-box-content grid-box-body">    
                <p>Are you sure that you want to unsubscribe?</p>
                <p>If your subscription is active, it will not be removed until the end of the month, and you can continue to test as normal until then.</p>        
                <p>If you want the subscription removed immediately, please select the checkbox below.</p>
            </div>
            <div class="popup-box-footer radius6 noradiustop">              
                <label class="left"><input type="checkbox" id="delete-now" name="delete-now" /> Unsubscribe immediately</label>
                <div class="right">
                    <a href="#" class="action-btn process-btn submit-btn"><span class="p"></span><span class="t">OK</span></a>            
                    <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>            
                </div>
                <div class="clear"></div>
            </div>
            <div class="loading loading-with-text radius6"><div><b>UNSUBSCRIBING</b><span>Please wait...</span></div></div>
            <a class="close_btn"></a>
            <input type="hidden" name="id" id="subscription-id" value="" />    
            <?php wp_nonce_field('unsubscribe', '_paymentnonce'); ?>
            <?php if($return){ ?>
            <input type="hidden" name="return" value="<?php echo base64_encode($return)?>" />
            <?php } ?>
        </form>
    </div>
    <?php
}

/**
* Get the count of the subscriptions with the purchase id
* 
* @param mixed $purchase_id
*/
function getNumSubscriptions($purchase_id)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT count(1) FROM {$wpdb->prefix}users_subscriptions WHERE purchase_id=%d", $purchase_id);
    $count = $wpdb->get_var($query);
    
    return $count;
    
}

/**
* Check the user purchased a subscription to one of the brother test suites 
* 
* @param Int $suite_id
* 
* @return Boolean
*/
function isPurchasedForOtherVersions($familyMark, $user_id = null)
{
    global $wpdb;
    
    if(!$user_id)
        $user_id = get_current_user_id();
            
    //Getting Brother Suites    
    /*$query = $wpdb->prepare("SELECT family_mark FROM {$wpdb->prefix}test_suites WHERE suite_id=%d", $suite_id);
    $familyMark = $wpdb->get_var($query);*/
    
    $query = $wpdb->prepare("SELECT count(DISTINCT(s.id)) FROM {$wpdb->prefix}users_subscriptions AS s
                             INNER JOIN {$wpdb->prefix}test_suites AS ts ON s.suite_id=ts.suite_id
                             WHERE ts.family_mark=%d AND s.user_id=%d AND s.status='Active'", $familyMark, $user_id);
    $count = $wpdb->get_var($query);
    
    return !$count ? false : true;
}