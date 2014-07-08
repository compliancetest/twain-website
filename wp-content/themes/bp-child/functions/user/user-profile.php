<?php
/**
* Manage User Profile Section
*/

add_filter('user_contactmethods', 'cp_user_details');
function cp_user_details( $user_contactmethods, $user = null )
{
    $user_contactmethods['phone_number'] = 'Phone Number';
    $user_contactmethods['user_organisation'] = 'Organisation Name';
    $user_contactmethods['user_organisation_web'] = 'Organisation Website';
    $user_contactmethods['user_organisation_desc'] = 'Organisation Description';
    $user_contactmethods['user_organisation_abn'] = 'Organisation ABN';
    
    return $user_contactmethods;
}


function cp_user_detail_edit()
{
    global $wpdb, $current_user;
        
    if(!is_user_logged_in())
    {
        //Goto Homepage
        wp_redirect('/');
    }
    
    $user_id = $current_user->ID;
    
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    if(!$first_name && !$last_name && !$email)
    {
        echo 'First Name, Last Name and Email should not be empty';
        exit;
    }
    if(!$first_name){
        echo 'Please enter your first name';
        exit;
    }
    if(!$last_name){
        echo 'Please enter your last name';
        exit;
    }
    if(!$email){
        echo 'Please enter your email address.';
        exit;
    }

    //Update Phonenumber
    update_user_meta($user_id, 'phone_number',htmlentities($_POST['phone_number']));
    update_user_meta($user_id, 'description', htmlentities($_POST['biography']));
    
    //Timezone
    update_user_meta($user_id, 'timezone', htmlentities($_POST['timezone']));
    
    //Default Dashboard Tab Url
    update_user_meta($user_id, 'dashboard_page_url', htmlentities($_POST['dashboard_page_url']));
    update_user_meta($user_id, 'dashboard_page_title', htmlentities($_POST['dashboard_page_title']));
    
    //Update User Name
    //$uname = explode(' ', $uname);
    wp_update_user( array ('ID' => $user_id, 'first_name' => $first_name, 'last_name' => $last_name, 'display_name' => $first_name /*. " " . $last_name*/)) ;
    
    $email_regex = '/^[_a-zA-Z0-9-+]+(\.[_a-zA-Z0-9-+]+)*@[a-z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-z]{2,3})$/'; 
    if(!preg_match($email_regex, $email))
    {
        echo 'Please enter a valid email address';
        exit;
    }
    
    //Check Email Duplication
    $query = $wpdb->prepare("SELECT ID FROM " . $wpdb->users . " WHERE user_email=%s AND ID != %d", $email, $user_id);
    $uID = $wpdb->get_var($query);
    if($uID)
    {
        echo 'This email address already exists!';
        exit;
    }
    $query = $wpdb->prepare("SELECT user_id FROM " . $wpdb->prefix . "users_changes WHERE email_changed=%s AND user_id != %d", $email, $user_id);
    $uID = $wpdb->get_var($query);
    if($uID)
    {
        echo 'This email address already exists!';
        exit;
    }
    
    //Not Update Email and Save the email address temporary
    $query = $wpdb->prepare("SELECT user_email FROM " . $wpdb->users . " WHERE ID = %d", $user_id);
    $currentEmail = $wpdb->get_var($query);
    if ($currentEmail != $email) 
    {
        $wpdb->query("DELETE FROM " . $wpdb->prefix . "users_changes WHERE user_id =" . $user_id);
        
        $verification_code = md5($email);
        $wpdb->insert($wpdb->prefix . 'users_changes', array('user_id'=> $user_id, 'email_changed' => $email, 'verification_code' => $verification_code, 'updated_date' => date('Y-m-d H:i:s')));
        
        $data = array(
            '[name]' => get_user_meta($user_id, 'first_name', true) . " " . get_user_meta($user_id, 'last_name', true),
            '[username]' => $current_user->user_login,
            '[email]' => $email,
            '[link]' => get_site_url() . '?cp-action=email_activation&token=' . $verification_code
        );

        cp_send_email(array('name' => $data['[name]'], 'email' => $data['[email]']), 'email_changed', $data);
        cp_send_email_to_admin('email_changed_admin', $data);
    }
    
    //Update Password
    $newPass = $_POST['new_pass'];
    $confPass = $_POST['conf_pass'];
    if($newPass || $confPass)
    {
        if($newPass != $confPass)
        {
            echo 'The passwords do no match!';
            exit;
        }else{
            //update password
            wp_update_user( array ('ID' => $user_id, 'user_pass' => $confPass) ) ;
            $data = array(
                '[name]' => get_user_meta($current_user->ID, 'first_name', true) . " " . get_user_meta($current_user->ID, 'last_name', true),
                '[username]' => $current_user->user_login,
                '[email]' => $current_user->user_email,
            );
            
            cp_send_email(array('name' => $data['[name]'], 'email' => $data['[email]']), 'password_changed', $data);
            cp_send_email_to_admin('password_changed_admin', $data);
        }
    }
    
    echo 'success';
    
    exit();
}

//Delete Payment Information
function cp_delete_payment_method()
{
    global $wpdb, $current_user;   
    
    //Goto Homepage
    if(!is_user_logged_in())
        die("Permission Denied!");
    
    $user_id = get_current_user_id();
    $id = $_REQUEST['id'];
    
    $query = $wpdb->prepare("SELECT id FROM " . $wpdb->prefix ."users_cards WHERE user_id=%d and id=%d", $user_id, $id);
    $id = $wpdb->get_var($query);
    if(!$id)
    {
        echo ("Invalid Request");
        exit;
    }
    
    $query = $wpdb->prepare("SELECT count(id) FROM " . $wpdb->prefix ."users_purchases WHERE card_id=%d", $id);
    $count = $wpdb->get_var($query);
    if($count > 0)
    {
        echo "All subscriptions associated with this payment method must be unsubscribed before the payment method can be deleted.";
        exit;
    }
    
    $wpdb->query("DELETE FROM " . $wpdb->prefix . "users_cards WHERE id=" . $id);
    
    cp_update_user_cards_count($user_id);
    
    echo "success";
    exit;
}

//Edit User Payment Info
function cp_user_payment_edit()
{
    global $wpdb, $current_user;   
    
    //Goto Homepage
    if(!is_user_logged_in()){
        echo "Permission Denied!";
        exit;
    }
    
    get_currentuserinfo();
    
    $user_id = $current_user->ID;
    
    $id = $_REQUEST['id'];
    $card = getUserCardById($id, $user_id);
    
    if(!$card)
    {
        echo "Invalid Request!";
        exit;
    }
    
    $result = getCustomerCardDetailById($card->customer_id);
    
    if(!$result || isset($result['faultstring'])) 
    {
        echo "There was an error while getting the information from eWay.";
        exit;
    }
    
    $result['nickname'] = $card->nickname;
    $result['email'] = $card->email;
    
    echo json_encode($result);
    exit;
}

//Save User Payment Information
function cp_user_payment_save()
{
    global $wpdb, $current_user;   
    
    //Goto Homepage
    if(!is_user_logged_in()){
        return "Permission Denied!";
    }
    
    get_currentuserinfo();
    
    $user_id = $current_user->ID;
    
    $card_number = str_replace(' ', '', $_POST['card_number']);
    $nickname = trim($_POST['nickname']);
    $email = trim($_POST['email']);
    $name_on_card = trim($_POST['name_on_card']);
    $card_expiry = trim($_POST['card_expiry']);
    $card_cvc = trim($_POST['card_cvc']);
    
    $id = trim($_POST['id']);
    
    if($id)
    {
        $query = $wpdb->prepare("SELECT id FROM " . $wpdb->prefix . "users_cards WHERE user_id=%d and id=%d", $user_id, $id);
        $id = $wpdb->get_var($query);
        if(!$id){
            return "Invalid Request!";
        }
    }
    if(!$nickname)
    {
        return 'Please enter a nickname of this card!';
    }
    
    $email_regex = '/^[_a-zA-Z0-9-+]+(\.[_a-zA-Z0-9-+]+)*@[a-z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-z]{2,3})$/'; 
    
    if(!$email)
    {
        return 'Please enter the email to receive invoices!';
    }else if(!preg_match($email_regex, $email) || strlen($email) > 50){
        echo 'Please enter a valid email address. Due to payment system limitations, it cannot be more than 50 characters in length.';
        exit;
    }
    
    //Card Number
    if($card_number == '')
    {
        return 'Credit card number is empty!';
    }else if(strpos($card_number, 'XXXXXX') === false && !check_cc($card_number)){
        return 'Credit card number is not valid!';
    }
    
    if(!$card_expiry){
        return 'Please specify your card expiry date!';        
    }else{
        $card_expiry_arr = explode('/', $card_expiry);
        if($card_expiry_arr[0] > 12){
            return 'Your expiry date is incorrect!';
        }else if(!check_exp_date($card_expiry_arr[0], $card_expiry_arr[1])){
            return 'Your card has expired or your expiry date is incorrect!';
        }
    }
    
    
    if( !($card_cvc!='' && (strlen($card_cvc)==3 || strlen($card_cvc)==4)) ){        
        return 'Your CVC code is incorrect';
    }
    
    $customerID = get_eway_customer_id();
    $userName = get_eway_user_name();
    $userPWD = get_eway_user_pwd();
    
    //Validate the card info using PreAuth Service    
    $preAuthServiceURL = get_eway_pre_auth_url();
    $xmlData = '<ewaygateway> 
                <ewayCustomerID>' . $customerID . '</ewayCustomerID> 
                <ewayTotalAmount>10</ewayTotalAmount> 
                <ewayCustomerFirstName>' . $current_user->first_name . '</ewayCustomerFirstName> 
                <ewayCustomerLastName>' . $current_user->last_name . '</ewayCustomerLastName> 
                <ewayCustomerEmail>' . $email . '</ewayCustomerEmail> 
                <ewayCustomerAddress></ewayCustomerAddress> 
                <ewayCustomerPostcode></ewayCustomerPostcode>
                <ewayCustomerInvoiceDescription></ewayCustomerInvoiceDescription> 
                <ewayCustomerInvoiceRef></ewayCustomerInvoiceRef>
                <ewayCardHoldersName>' . $name_on_card . '</ewayCardHoldersName> 
                <ewayCardNumber>' . $card_number . '</ewayCardNumber> 
                <ewayCardExpiryMonth>' . $card_expiry_arr[0] . '</ewayCardExpiryMonth> 
                <ewayCardExpiryYear>' . $card_expiry_arr[1] . '</ewayCardExpiryYear> 
                <ewayTrxnNumber></ewayTrxnNumber> 
                <ewayOption1></ewayOption1> 
                <ewayOption2></ewayOption2> 
                <ewayOption3></ewayOption3> 
                <ewayCVN>' . $card_cvc . '</ewayCVN> 
                </ewaygateway>';
    
    $ch = curl_init($preAuthServiceURL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch, CURLOPT_POST, 1); 
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData); 
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/xml")); 
    
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);  
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);  
    
    curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');        
    $response = curl_exec($ch);
    
    if(!curl_errno($ch)){        
        $xmlObj = simplexml_load_string($response);
        if($xmlObj && $xmlObj->ewayTrxnStatus == 'False')
        {
            return 'Card Validation Error: ' . $xmlObj->ewayTrxnError;
        }
        
        //Authorisation Void Request

        $xmlData = '<ewaygateway> 
        <ewayCustomerID>' . $customerID . '</ewayCustomerID> 
        <ewayAuthTrxnNumber>' . $xmlObj->ewayTrxnNumber . '</ewayAuthTrxnNumber> 
        <ewayTotalAmount>10</ewayTotalAmount> 
        <ewayOption1></ewayOption1> 
        <ewayOption2></ewayOption2> 
        <ewayOption3></ewayOption3> 
        </ewaygateway>';
        curl_close($ch);
        
        $preAuthVoidServiceURL = get_eway_pre_auth_void_url();        
        
        $ch = curl_init($preAuthVoidServiceURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_POST, 1); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/xml")); 
        
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);  
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);  
        
        curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');        
        
        $response = curl_exec($ch);    
        
        
    } else { 
        return 'Curl Error:' . curl_error($ch);
    }
    
    $tokenWebserviceURL = get_eway_token_webservice_url();    
    
    //Create or Update Customer Information
    require_once(THE_FUNCTION . '/soap/nusoap.php');    
    
    $client = new nusoap_client($tokenWebserviceURL, false);
    $err = $client->getError();
    if ($err) {
        return 'Soap Construction Error: ' . $err;
    }
    
    $client->namespaces['man'] = 'https://www.eway.com.au/gateway/managedpayment';
    $headers = "<man:eWAYHeader><man:eWAYCustomerID>" . $customerID . "</man:eWAYCustomerID><man:Username>" . $userName . "</man:Username><man:Password>" . $userPWD . "</man:Password></man:eWAYHeader>";
    $client->setHeaders($headers);    
    
    //Create Rebill Customer
    if(!$id)
    {
        $requestbody = array(
            'man:Title' => 'Dr.',
            'man:FirstName' => get_user_meta($user->ID, 'first_name', true),
            'man:LastName' => get_user_meta($user->ID, 'last_name', true),
            'man:Address' => '',
            'man:Suburb' => '',
            'man:State' => '',
            'man:Company' => '',
            'man:PostCode' => '',
            'man:Country' => 'au',
            'man:Email' => $email,
            'man:Fax' => '',
            'man:Phone' => '',
            'man:Mobile' => '',
            'man:CustomerRef' => '',
            'man:JobDesc' => '',
            'man:Comments' => '',
            'man:URL' => '',
            'man:CCNumber' => $card_number,
            'man:CCNameOnCard' => $name_on_card,
            'man:CCExpiryMonth' => $card_expiry_arr[0],
            'man:CCExpiryYear' => $card_expiry_arr[1]
        );
        $soapaction = 'https://www.eway.com.au/gateway/managedpayment/CreateCustomer';
        $result = $client->call('man:CreateCustomer', $requestbody, '', $soapaction);
        if(is_array($result))
        {
            return $result['faultstring'];
        }else if(!$result){ 
            return 'There was an error while saving your payment information.';
        }else{
            //Success            
            $query_result = $wpdb->insert($wpdb->prefix . "users_cards", array(
                'user_id' => $user_id,
                'nickname' => $nickname,
                'email' => $email,
                'card_number' => encrypt_card_number($card_number),
                'customer_id' => $result,                
                'status' => 'Active',                
                'created_date' => date('Y-m-d H:i:s')
            ));        
            if(!$query_result)
                $id = $wpdb->last_error;
            else{
                $id = $wpdb->insert_id;            
                cp_update_user_cards_count($user_id);
            }
        }
    }else{
        //Getting Card
        $card = getUserCardById($id);
        
        $requestbody = array(
            'man:managedCustomerID' => $card->customer_id,
            'man:Title' => "Dr.",
            'man:FirstName' => get_user_meta($user->ID, 'first_name', true),
            'man:LastName' => get_user_meta($user->ID, 'last_name', true),
            'man:Address' => '',
            'man:Suburb' => '',
            'man:State' => '',
            'man:Company' => '',
            'man:PostCode' => '',
            'man:Country' => 'au',
            'man:Email' => $email,
            'man:Fax' => '',
            'man:Phone' => '',
            'man:Mobile' => '',
            'man:CustomerRef' => '',
            'man:JobDesc' => '',
            'man:Comments' => '',
            'man:URL' => '',
            'man:CCNumber' => $card_number,
            'man:CCNameOnCard' => $name_on_card,
            'man:CCExpiryMonth' => $card_expiry_arr[0],
            'man:CCExpiryYear' => $card_expiry_arr[1]
        );
        $soapaction = 'https://www.eway.com.au/gateway/managedpayment/UpdateCustomer';
        $result = $client->call('man:UpdateCustomer', $requestbody, '', $soapaction);    
        if($result == 'true')
        {
            $wpdb->update($wpdb->prefix . "users_cards", array('nickname' => $nickname, 'email' => $email, 'status' => 'Active'), array('id' => $card->id));
            
            $query = "SELECT p.*, c.customer_id FROM {$wpdb->prefix}users_purchases AS p LEFT JOIN {$wpdb->prefix}users_cards AS c ON c.id=p.card_id WHERE (p.`status`='InArrears' OR p.`status`='Frozen') AND c.`status`='Active' AND p.user_id=" . $current_user->ID;
            
            $subscriptions = $wpdb->get_results($query, ARRAY_A);
            foreach($subscriptions as $row)
            {
                //Monthly Billing
                $currentPrice = get_post_meta($row['suite_id'], 'monthly_subscription_price', true);
                if($row['monthly_fee'] < $currentPrice)
                    $row['monthly_fee'] = $currentPrice;
                
                //Check User's Level Monthly Fee
                /*$userMonthlyFees = get_user_meta($user->ID, 'monthly_fee', true);
                if(isset($userMonthlyFees[$row['suite_id']]) && $userMonthlyFees[$row['suite_id']] < $currentPrice)
                    $currentPrice = $userMonthlyFees[$row['suite_id']];*/
                
                $result = processEwayPayment($row['customer_id'], $row['monthly_fee']);
                
                $subscription = new CT_Subscription();
                $subscription->bind($row);
                
                if($result['ewayTrxnStatus'] == 'True')
                {            
                    //Save Transaction
                    $wpdb->insert($wpdb->prefix . 'users_transactions', array(
                        "user_id" => $row['user_id'],
                        "suite_id" => $row['suite_id'],
                        "trxn_number" => $result['ewayTrxnNumber'],
                        "amount" => $row['monthly_fee'],
                        "auth_code" => $result['ewayAuthCode'],
                        "created_date" => date("Y-m-d H:i:s")
                    ));
                    
                    $subscription->active();
                    
                }else{             
                    //Set Card Status to Suspended
                    $wpdb->update($wpdb->prefix . 'users_cards', array('status' => 'Suspended'), array('id' => $row['card_id']));
                }
                
            }
            
            echo 'success';
        }else{
            echo $result['faultstring'];
        }
        exit;
    }
        
    return $id;    
}

//Save User Organisation 
function cp_user_organisation_edit()
{
    global $wpdb, $current_user;   
    
    //Goto Homepage
    if(!is_user_logged_in())
        wp_redirect('/');
    
    $user_id = $current_user->ID;
    
    $user_organisation = htmlspecialchars($_POST['user_organisation']);
    $user_organisation_web = htmlspecialchars($_POST['user_organisation_web']);
    $user_organisation_desc = htmlspecialchars($_POST['user_organisation_desc']);
    $user_organisation_abn = htmlspecialchars($_POST['user_organisation_abn']);
    
    update_user_meta($user_id, 'user_organisation', $user_organisation);
    update_user_meta($user_id, 'user_organisation_web', $user_organisation_web);
    update_user_meta($user_id, 'user_organisation_desc', $user_organisation_desc);
    update_user_meta($user_id, 'user_organisation_abn', $user_organisation_abn);
    
    echo 'success';
    
    exit();

}

//Get User Full Name
function cp_get_user_fullname($user_id)
{
    $fname = get_user_meta($user_id, 'first_name', true);
    $lname = get_user_meta($user_id, 'last_name', true);
    
    return $fname . " " . $lname;
    
}

function cp_get_customer_harness_detail()
{
    global $wpdb, $CPRest;
    
    $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}users_subscriptions WHERE id=%d", $_REQUEST['id']);
    $row = $wpdb->get_row($query);
    
    $gateways = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}gateways");
    
    $user_id = get_current_user_id();
    
    $user = get_userdata($user_id);
    
    if(!$row): 
    ?>
        <div class="popup-box" id="harness-detail-box<?php echo $_REQUEST['id']?>" style="display: none; width: 450px;">
            <div class="popup-box-header radius6 noradiusbottom">Invalid Request!</div>     
            <div class="popup-box-content grid-box-body">    
                <p class="message error">Your request is not correct. Please try again.</p>
            </div>
            <div class="popup-box-footer radius6 noradiustop">                                                
                <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>            
                <div class="clear"></div>
            </div>
        </div>
    <?php else: ?>

        <div class="popup-box" id="harness-detail-box<?php echo $_REQUEST['id']?>" style="display: none; width: 450px;">
            <div id="harness-detail-container">
                <div class="popup-box-header radius6 noradiusbottom">Test Harness Access Detail.</div>    
                <form name="harness-form" id="harness-form" action="">
                    <div class="popup-box-content grid-box-body">    
                        <div class="second-tabs">
                            <ul>
                                <li class="active"><a onclick="switch_secondtabs(this)" rel="harness-direct"><span>Direct</span></a></li>
                                <li><a onclick="switch_secondtabs(this)" rel="harness-gateway"><span>Gateway</span></a></li>
                            </ul>
                            <div class="clear"></div>
                        </div>
                        <div class="second-tabs-container">
                            <div id="harness-direct" class="second-tab-content">
                                <div class="field-row">
                                    <div class="grid-cell">
                                        <label>P-Mode Profile:</label>
                                        <select name="p_mode_agreement" id="p_mode_agreement" class="select">
                                            <option value="LIGHT" <?php echo $row->p_mode_agreement != 'HIGH-END' ? 'selected="selected"' : ''?>>LIGHT</option>
                                            <option value="HIGH-END" <?php echo $row->p_mode_agreement == 'HIGH-END' ? 'selected="selected"' : ''?>>HIGH-END</option>
                                        </select>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <div class="harness-endpoint-info">                
                                    <div class="field-row">
                                        <div class="grid-cell">
                                            <label>Harness EndPoint:</label>
                                            <input class="input" type="text" name="harness_endpoint_url" id="harness_endpoint_url" readonly="readonly" disabled="disabled" value="<?php echo $row->harness_endpoint_url?>" />
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                    <div class="field-row">
                                        <div class="grid-cell">
                                            <label>Harness Username:</label>
                                            <input class="input" type="text" name="harness_username" readonly="readonly" disabled="disabled" id="harness_username" value="<?php echo $row->harness_username?>" />
                                        </div>
                                        <div class="clear"></div>
                                    </div>            
                                    <div class="field-row">
                                        <div class="grid-cell">
                                            <label>Harness Password:</label>
                                            <input class="input" type="text" name="harness_password" id="harness_password" value="<?php echo $row->harness_password?>" />
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                                <div class="tester-endpoint-info" <?php echo $row->p_mode_agreement == 'LIGHT' ? 'style="display: none"' : '' ?>>
                                    <div class="field-row">
                                        <div class="grid-cell">
                                            <label>Tester EndPoint:</label>
                                            <input class="input" type="text" name="tester_endpoint_url" id="tester_endpoint_url" value="<?php echo $row->tester_endpoint_url?>" />
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                    <div class="field-row">
                                        <div class="grid-cell">
                                            <label>Tester Username:</label>
                                            <input class="input" type="text" name="tester_username" id="tester_username" value="<?php echo $row->tester_username?>" />
                                        </div>
                                        <div class="clear"></div>
                                    </div>            
                                    <div class="field-row">
                                        <div class="grid-cell">
                                            <label>Tester Password:</label>
                                            <input class="input" type="text" name="tester_password" id="tester_password" value="<?php echo $row->tester_password?>" />
                                        </div>
                                        <div class="clear"></div>
                                    </div>                 
                                </div>
                            </div>
                            <div id="harness-gateway" class="second-tab-content" style="display: none;">
                                <?php $profileInstances = getCustomerProfileInstances(); ?>
                                <div class="field-row">
                                    <div class="grid-cell">
                                        <label>Profile:</label>
                                        <select name="profile_id" class="select" onchange="viewProfileData()">
                                            <option value="">None</option>
                                            <?php 
                                                if (count($profileInstances) > 0):
                                                foreach ($profileInstances as $instance): 
                                                    $instanceObj = json_decode(base64_decode($instance->content));
                                                    $version = array();
                                                    if($instanceObj->Profile->Version) {
                                                        foreach(get_object_vars($instanceObj->Profile->Version) as $k=>$v) {
                                                            $version[] = $v;
                                                        }
                                                        
                                                    }
                                                    $profileName = $instance->profile_name . ' v' . implode('.', $version);
                                            ?>
                                            <option value="<?php echo $instance->id; ?>" <?php echo ($row->profile_id == $instance->id) ? ('selected="selected"') : (''); ?>><?php echo $profileName; ?></option>
                                            <?php endforeach; endif; ?>
                                        </select>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <div class="field-row">
                                    <div class="grid-cell">
                                        <label>Gateway:</label>
                                        <select name="gateway_id" class="select">
                                            <option value="">None</option>
                                            <?php foreach ($gateways as $gateway): ?>
                                            <option value="<?php echo $gateway->gateway_id; ?>" <?php echo ($row->gateway_id == $gateway->gateway_id) ? ('selected="selected"') : (''); ?>><?php echo $gateway->name; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <div id="profile-data-container"></div>
                            </div>
                        </div>
                    </div>
                    <div class="popup-box-footer radius6 noradiustop">                                    
                        <a href="javascript: void(0)" class="action-btn process-btn submit-btn" onclick="saveHarnessDetails('<?php echo $_REQUEST['id']?>')"><span class="p"></span><span class="t">Confirm</span></a>            
                        <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>            
                        <div class="clear"></div>
                    </div>
                    <div class="loading"></div>
                    <a class="close_btn"></a>
                    <input type="hidden" name="id" id="harness-id" value="<?php echo $row->id?>" />
                    <?php wp_nonce_field('save-harness', 'cp-action'); ?>
                </form>
            </div>
            <div id="harness-generate-profile-container" style="display: none;">
                    <div class="popup-box-header radius6 noradiusbottom">Generate Profiles</div>
                    <div class="popup-box-content grid-box-body">    
                        This action will generate a custom set of data profiles in your test data tab specifically tailored to work with the selected gateway profile.
                    </div>
                    <div class="popup-box-footer radius6 noradiustop">                                    
                        <a href="javascript: confirmGenerateProfile('<?php echo $_REQUEST['id']?>')" class="action-btn process-btn" ><span class="p"></span><span class="t">Confirm</span></a>            
                        <a href="javascript: cancelGenerateProfile()" class="action-btn cancel-btn"><span class="p"></span><span class="t">Cancel</span></a>            
                        <div class="clear"></div>
                    </div>
            </div>
            <script type="text/javascript">
                function switch_secondtabs(obj)
                {
                    jQuery(obj).parent().addClass('active');
                    jQuery(obj).parent().siblings().removeClass('active');
                    
                    jQuery('.second-tabs-container > div').hide();
                    var id = jQuery(obj).attr("rel");
                 
                    jQuery('#' + id).show();
                    
                    return false;
                }
                function viewProfileData()
                {
                    var profile_id = jQuery('#harness-gateway select[name=profile_id]').val();
                    var subscription_id = '<?php echo $_REQUEST['id']; ?>';
                    
                    if (profile_id == 0) {
                        jQuery('#profile-data-container').html('');
                        jQuery('select[name=gateway_id]')[0].selectedIndex = 0;
                    } else {
                        jQuery('#harness-detail-box<?php echo $_REQUEST['id']?> .loading').show();
                        jQuery.ajax({
                            url: '/?cp-action=<?php echo wp_create_nonce('get-harness-profile-data')?>&id=' + profile_id + '&subscription_id=' + subscription_id,
                            type: 'post',
                            success: function(res) {
                                jQuery('#profile-data-container').html(res);
                                jQuery('#harness-detail-box<?php echo $_REQUEST['id']?> .loading').hide();
                            }
                        });
                    }
                }
                
                viewProfileData();
                jQuery('#my_testsuites .message').remove();
                
                function generateProfile()
                {
                    jQuery('#harness-detail-container').hide();
                    jQuery('#harness-generate-profile-container').show();
                }
                function cancelGenerateProfile()
                {
                    jQuery('#harness-detail-container').show();
                    jQuery('#harness-generate-profile-container').hide();
                }
                function confirmGenerateProfile(id)
                {
                    jQuery('#harness-detail-container').show();
                    jQuery('#harness-generate-profile-container').hide();
                    
                    jQuery('#harness-detail-box' + id + ' .loading').show();
                    jQuery('#harness-detail-box' + id + ' .message').remove();

                    jQuery.ajax({
                        url: '/',
                        data: jQuery('#harness-form').serialize() + '&action_mode=generate-profile',
                        type: 'post',
                        success: function(rsp){
                            jQuery('#harness-detail-box' + id + ' .loading').hide();
                            if(rsp == 'success')
                            {
                                jQuery('#my_testsuites').prepend('<div style="margin-bottom:20px;" class="message success">New profile has been generated successfully!</div>');                
                                jQuery('#harness-detail-box' + id + ' .close-popup-btn').click();
                            }
                            else
                            {
                                jQuery('#harness-detail-box' + id + ' .popup-box-footer').prepend('<div class="message error">' + rsp + "</div>");
                            }
                        },
                        error: function(err){
                            jQuery('#harness-detail-box' + id + ' .loading').hide();
                            jQuery('#harness-detail-box' + id + ' .popup-box-footer').prepend('<div class="message error">' + err.responseText + "</div>");
                        }
                    })
                    return false;
                }
                
                function selectAlias() 
                {
                }
            </script>
        </div>
    <?php 
    endif; 
}

function james_compare_alias($a, $b)
{
    return strnatcmp($a['alias'], $b['alias']);
}

function cp_get_customer_harness_detail_profile_data()
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT pi.*, pt.title AS profile_type_title, pt.schema FROM " . $wpdb->prefix . "community_profile_instances AS pi LEFT JOIN " . $wpdb->prefix . "community_profile_types AS pt ON pt.id=pi.type_id WHERE pi.id=%d", $_REQUEST['id']);
    $row = $wpdb->get_row($query);
    
    $subscription_id = $_REQUEST['subscription_id'];
    $subscription_query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "users_subscriptions WHERE id=%d", $subscription_id);
    $subscription_row = $wpdb->get_row($subscription_query);
    
    $gateways_query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "gateways");
    $gateways = $wpdb->get_row($gateways_query);
    $alias_list = array();
    
    foreach ($gateways as $gateway) {
        if ($gateway->alias_list != '') {
            $alias_group = explode('|', $gateway->alias_list);
        }
        if (count($alias_group) > 0) {
            foreach ($alias_group as $alias) {
                $alias_list[] = array('gateway_id' => $gateway->gateway_id, 'alias' => $alias);
            }
        }
    }
    usort($alias_list, 'james_compare_alias');
    
    if (!empty($row)):
        $profile_instance = json_decode(base64_decode($row->content));
        $profile_schema = json_decode(base64_decode($row->schema)); 
                                   
        $profile_type_title = $row->profile_type_title;
        if($profile_schema->Version) {
            $version = array();
            foreach (get_object_vars($profile_schema->Version) as $k=>$v) {
                $version[] = $v;
            }
            $profile_type_title .= ' v' . implode('.', $version);
        }
?>
    <?php 
        $hasABN = 0;
        foreach ($profile_instance->Entity as $label=>$value) {
            if (strtolower($label) == 'abn') {
                $hasABN = 1;
                break;
            }
        }
    ?>
    <?php if ($hasABN): ?>
    <div class="field-row">
        <div class="grid-cell">
            <input class="input" type="text" name="gateway_label" id="gateway_label" value="<?php echo $subscription_row->alias; ?>" readonly="readonly" disabled="disabled"/>
        </div>
        <div class="clear"></div>
    </div>
    <div class="field-row">
        <div class="grid-cell">
            <label>Alias:</label>
            <?php if (count($alias_list) == 0): ?>
            <input class="input" type="text" name="alias" value="<?php echo $subscription_row->alias; ?>"/>
            <?php else: ?>
            <select name="alias" class="select" onchange="selectAlias()">
                <?php foreach ($alias_list as $alias): ?>
                <option value="<?php echo $alias['alias']; ?>" rel="<?php echo $alias['gateway_id']; ?>" <?php echo ($alias['alias'] == $subscription_row->alias) ? ('selected') : (''); ?>><?php echo $alias['alias']; ?></option>
                <?php endforeach; ?>
            </select>
            <?php endif; ?>
        </div>
        <div class="clear"></div>
    </div>
    <?php else: ?>
        <input class="input" type="hidden" name="alias" value=""/>
    <?php endif; ?>
    <div class="field-row">
        <div class="grid-cell">
            <label>Profile Type:</label>
            <input class="input" type="text" name="profile_type_title" value="<?php echo $profile_type_title; ?>" readonly="readonly" disabled="disabled"/>
        </div>
        <div class="clear"></div>
    </div>
    <?php foreach ($profile_instance->Entity as $label=>$value): ?>
    <div class="field-row">
        <div class="grid-cell">
            <label><?php echo $label; ?>:</label>
            <input class="input" type="text" name="profile_entity_<?php echo strtolower(str_replace(' ', '_', $label)); ?>" value="<?php echo $value; ?>" readonly="readonly" disabled="disabled"/>
            <input type="hidden" name="entity_<?php echo strtolower(str_replace(' ', '_', $label)); ?>" value="<?php echo $value; ?>"/>
        </div>
        <div class="clear"></div>
    </div>
    <?php endforeach; ?>
    <div id="generate-profile-container">
        <a href="javascript: void(0)" class="action-btn process-btn" onclick="generateProfile()"><span class="p"></span><span class="t">Generate Profiles</span></a>            
        <div class="clear"></div>
    </div>
<?php
    endif;
}

function cp_save_customer_harness_detail()
{
    global $wpdb, $CPRest;
    
    $id = $_POST['id'];
    $user_id = get_current_user_id();
    
    $user = get_userdata($user_id);
    
    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "users_subscriptions WHERE id=%d AND user_id=%d", $id, $user_id);
    $data = $wpdb->get_row($query);
    
    if(!$data)
    {
        return 'Invalid Request!';
    }
    
    $isSaved = false;
    
    //Harness Username and Password Should not be changed
    
    $_POST['harness_endpoint_url'] = $data->harness_endpoint_url;    
    $_POST['harness_username'] = $data->harness_username;
    
    if($_POST['p_mode_agreement'] == 'LIGHT'){        
        $_POST['tester_endpoint_url'] = $data->tester_endpoint_url;
        $_POST['tester_username'] = $data->tester_username;
        $_POST['tester_password'] = $data->tester_password;
    }    
    
    $community_id = cp_get_post_meta($data->suite_id, 'community_id', true);    
    $group = groups_get_group( array('group_id' => $community_id));
    
    $updateArr = array(
        'p_mode_agreement' => $_POST['p_mode_agreement'],
        'harness_password' => $_POST['harness_password'],
        'gateway_id' => (($_POST['gateway_id']!='') ? ($_POST['gateway_id']) : ('NULL')),
        'profile_id' => (($_POST['profile_id']!='') ? ($_POST['profile_id']) : ('NULL')),
        'alias' => (($_POST['alias']!='') ? ($_POST['alias']) : (''))
    );
    
    if($_POST['p_mode_agreement'] == 'HIGH-END'){
        $updateArr['tester_endpoint_url'] = $_POST['tester_endpoint_url'];
        $updateArr['tester_username'] = $_POST['tester_username'];
        $updateArr['tester_password'] = $_POST['tester_password'];
    }
    
    if (isset($_POST['entity_usi']) && $_POST['entity_usi'] != '') {
        $updateArr['entity_id'] = $_POST['entity_usi'];
        $updateArr['entity_type'] = 'http://sbr.gov.au/identifier/usi';
    } else if (isset($_POST['entity_abn']) && $_POST['entity_abn'] != '') {
        $updateArr['entity_id'] = $_POST['entity_abn'];
        $updateArr['entity_type'] = 'urn:oasis:tc:ebcore:partyid-type:ABN:0151';
    } else {
        $updateArr['entity_id'] = '';
        $updateArr['entity_type'] = '';
    }
    
    // Check if USI or ABN is duplicated in subscription list
    $duplicateRow = false;
    
    if ($updateArr['entity_id'] != '') {
        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "users_subscriptions WHERE id!=%d AND entity_id=%s AND entity_type=%s", $data->id, $updateArr['entity_id'], $updateArr['entity_type']);
        $duplicateRow = $wpdb->get_row($query); 
    }
    
    if (!$duplicateRow) {
        
        add_filter( 'query', 'wp_db_null_value' );
        $wpdb->update($wpdb->prefix . "users_subscriptions", 
            $updateArr,
            array('id' => $data->id)
        );        
        remove_filter( 'query', 'wp_db_null_value' );
        
        if ((isset($_POST['action_mode']) && $_POST['action_mode'] == 'generate-profile') && $updateArr['profile_id'] != 'NULL') {
            generateProfile($updateArr['profile_id'], $community_id);
        }
        
        return "success";
    } else {
        return "The USI or ABN in the selected profile is already in use. Please update the profile and try again.";
    }
}

function generateProfile($profile_id, $community_id)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "community_profile_instances WHERE id=%d", $profile_id);
    $profile = $wpdb->get_row($query);
    $profile_content = json_decode(base64_decode($profile->content));
    $customDataGeneration = isset($profile_content->CustomProfilesGeneration) ? ($profile_content->CustomProfilesGeneration) : (null);
    
    //$customDataGeneration = json_decode('{"CustomDataGeneration": [{"Description": "Generate custom versions of Gadget and Foo", "SourceProfiles": {"IdentifierPath": "Entity.ABN", "Values": ["98111133334", "23111144445"] }, "Rules": [{"Type": "Value", "OriginalValue": "79111188889.010", "ReplacementPath": "Entity.USI"}, {"Type": "Value", "OriginalValue": "ACME Investments", "ReplacementPath": "Entity.MainName"}, {"Type": "Value", "OriginalValue": "79111188889", "ReplacementPath": "Entity.ABN"} ] }, {"Description": "Generate custom version of Super Choose for Test Product", "SourceProfiles": {"IdentifierPath": "Entity.ABN", "Values": ["73000570911"] }, "Rules": [{"Type": "Value", "OriginalValue": "79111188889.010", "ReplacementPath": "Entity.USI"}, {"Type": "Value", "OriginalValue": "ACME Investments", "ReplacementPath": "Entity.MainName"}, {"Type": "Value", "OriginalValue": "79111188889", "ReplacementPath": "Entity.ABN"}, {"Type": "Reference"} ] } ]}');
    
    $pre_desc = '';
    if (isset($profile_content->Entity->USI)) {
        $pre_desc = '(For testing with ' . $profile->profile_name . ', USI ' . $profile_content->Entity->USI . ')';
    } else if (isset($profile_content->Entity->ABN)) {
        $pre_desc = '(For testing with ' . $profile->profile_name . ', ABN ' . $profile_content->Entity->ABN . ')';
    }
    
    if (empty($customDataGeneration)) {
        return;
    }
    
    $profile_ref = array();
    
    /*if (!empty($profile->token_original)) {
        $profile_ref[$profile->token_original] = $profile->token;
    }*/
    
    foreach ($customDataGeneration as $customData) 
    {
        $identifierPath = str_replace('.', '_', $customData->SourceProfiles->IdentifierPath);
        $identifierValues = $customData->SourceProfiles->Values;
        if ($identifierPath != 'Self') 
        {
            $rows = $wpdb->get_results("SELECT cpi.* FROM {$wpdb->prefix}community_profile_meta as cpm LEFT JOIN {$wpdb->prefix}community_profile_instances AS cpi ON cpi.id=cpm.profile_id Where cpi.type='harness' AND cpi.community_id=" . $community_id . " AND cpm.meta_value IN (" . implode(',', $identifierValues) . ") AND cpm.meta_key = '" . $identifierPath . "'", ARRAY_A);

            foreach ($rows as $row) {
                $content = json_decode(base64_decode($row['content']));
                
                $row['type'] = 'tester';
                $token_original = $row['token'];
                $row['token_original'] = $token_original;
                $row['token'] = sha1(time() . $content->Profile->Title . rand(0, 9999) . $row['type_id'] . $community_id);
                $row['created_date'] = date('Y-m-d F:i:s');
                $row['purpose'] = $content->Profile->Purpose;
                $row['creator_id'] = get_current_user_id();
                $row['token_original'] = $token_original;
                
                $profile_ref[$token_original] = $row['token'];
                unset($row['id']);
                
                foreach ($customData->Rules as $rule) {
                    if ($rule->Type == 'Value') {
                        
                        $replacementPath = str_replace('.', '->', $rule->ReplacementPath);
                        eval('$replacementValue = $profile_content->' . $replacementPath . ';');
                        if ($replacementValue) {
                            $content = json_decode(str_replace($rule->OriginalValue, $replacementValue, json_encode($content)));
                        }
                            
                    } else if ($rule->Type == 'Reference') {
                        // Replace $ref values with links of generated profiles
                        foreach ($content->Employers as $employer) {
                            $ref = explode('=', $employer->Profile->{'$ref'});
                            
                            if (!isset($profile_ref[$ref[1]])) {
                                $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "community_profile_instances WHERE token_original=%s", $ref[1]);
                                $temp_profile = $wpdb->get_row($query);
                                if (!empty($temp_profile)) {
                                    $profile_ref[$temp_profile->token_original] = $temp_profile->token;
                                }
                            }
                            
                            if (isset($profile_ref[$ref[1]])) {
                                $employer->Profile->{'$ref'} = $ref[0] . '=' . $profile_ref[$ref[1]];
                            }
                        }
                    }
                }
                
                $content->Profile->Description = $pre_desc . ' ' . $content->Profile->Description;
                
                $row['content'] = base64_encode(stripcslashes(json_encode($content)));
                
                // Create new profile
                $query_result = $wpdb->insert($wpdb->prefix . "community_profile_instances", $row);
                $new_profile_id = $wpdb->insert_id;
                
                $wpdb->delete($wpdb->prefix . 'community_profile_meta', array('profile_id'=>$new_profile_id), '%d');
                
                // Generate meta values of new profile
                $profile_meta = getProfileMetaData($content);
                foreach ($profile_meta as $meta_key => $meta_value) {
                    $wpdb->insert($wpdb->prefix . "community_profile_meta", array(
                        'profile_id' => $new_profile_id,
                        'meta_key' => $meta_key,
                        'meta_value' => $meta_value,
                    ));
                }
            }
        }
        else // Self
        {
            $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "community_profile_instances WHERE id=%d", $profile_id);
            $self_instance = $wpdb->get_row($query, ARRAY_A);
            $self_content = json_decode(base64_decode($self_instance['content']));
            
            foreach ($customData->Rules as $rule) {
                if ($rule->Type == 'Reference') {
                    foreach ($self_content->Employers as $employer) {
                        $ref = explode('=', $employer->Profile->{'$ref'});
                        
                        if (!isset($profile_ref[$ref[1]])) {
                            $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "community_profile_instances WHERE token_original=%s", $ref[1]);
                            $temp_profile = $wpdb->get_row($query);
                            if (!empty($temp_profile)) {
                                $profile_ref[$temp_profile->token_original] = $temp_profile->token;
                            }
                        }
                        
                        if (isset($profile_ref[$ref[1]])) {
                            $employer->Profile->{'$ref'} = $ref[0] . '=' . $profile_ref[$ref[1]];
                        }
                    }
                }
            }
            
            $self_instance['content'] = base64_encode(stripcslashes(json_encode($self_content)));
            
            $wpdb->update($wpdb->prefix . "community_profile_instances", array('content' => $self_instance['content']), array('id' => $profile_id));
        }
    }
}

function wp_db_null_value( $query )
{
  return str_ireplace( "'NULL'", "NULL", $query ); 
}

function cp_save_suite_notify_changes()
{
    global $wpdb;
    
    $user_id = get_current_user_id();
    if(!$user_id)
        return;
    
    $suiteID = intval($_POST['id']);
    if(!$suiteID)
        return;
    
    if($_POST['checked'] == 1)
        update_user_meta($user_id, 'notify_suite_changes' . $suiteID, 1);
    else
        delete_user_meta($user_id, 'notify_suite_changes' . $suiteID);
}


function ct_get_user_profile_link($user_id)
{
    $user = get_userdata($user_id);
    
    return apply_filters( 'bp_get_member_permalink', bp_core_get_user_domain( $user->ID, $user->user_nicename, $user->user_login ) );
}