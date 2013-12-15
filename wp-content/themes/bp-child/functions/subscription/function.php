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
function processEwayPayment($eway_profile_id, $amount)
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
        'man:invoiceDescription' => ''
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
    $remainedDay = (strtotime("first day next month") - time()) / 86400;
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
        <form name="unsubscribe-form" action="" method="post">
            <div class="popup-box-header radius6 noradiusbottom">Confirm unsubscribing</div>        
            <div class="popup-box-content grid-box-body">    
                <p>Are you sure that you want to unsubscribe the subscription?<br > will remain active until then, and you can continue to test as normal by the end of this month.</p>        
                <p>If you check the below checkbox, the subscription will be cancelled immediately.</p>
            </div>
            <div class="popup-box-footer radius6 noradiustop">              
                <label class="left"><input type="checkbox" id="delete-now" name="delete-now" /> Delete immediately</label>
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

