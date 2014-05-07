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
            else
                $id = $wpdb->insert_id;            
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
            <div class="popup-box-header radius6 noradiusbottom">Test Harness Access Detail.</div>    
            <form name="harness-form" id="harness-form" action="">
                <div class="popup-box-content grid-box-body">    
                    <div class="second-tabs">
                        <ul>
                            <li class="active"><a onclick="switch_secondtabs(this)" rel="harness-direct">Direct</a></li>
                            <li><a onclick="switch_secondtabs(this)" rel="harness-gateway">Gateway</a></li>
                        </ul>
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
                            <div class="field-row">
                                <div class="grid-cell">
                                    <label>Entity Type:</label>
                                    <select name="entity_type" class="select">
                                        <option value="">None</option>
                                        <option value="ABN" <?php echo ($row->entity_type == 'ABN') ? ('selected="selected"') : (''); ?>>ABN</option>
                                        <option value="USI" <?php echo ($row->entity_type == 'USI') ? ('selected="selected"') : (''); ?>>USI</option>
                                    </select>
                                </div>
                                <div class="clear"></div>
                            </div>
                            <div class="field-row">
                                <div class="grid-cell">
                                    <label>Entity Indentifier:</label>
                                    <input class="input" type="text" name="entity_identifier" value="<?php echo $row->entity_identifier?>" />
                                </div>
                                <div class="clear"></div>
                            </div>                 
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
        'gateway_id' => $_POST['gateway_id'],
        'entity_type' => $_POST['entity_type'],
        'entity_identifier' => $_POST['entity_identifier']
    );
    
    if($_POST['p_mode_agreement'] == 'HIGH-END'){
        $updateArr['tester_endpoint_url'] = $_POST['tester_endpoint_url'];
        $updateArr['tester_username'] = $_POST['tester_username'];
        $updateArr['tester_password'] = $_POST['tester_password'];
    }
        
    $wpdb->update($wpdb->prefix . "users_subscriptions", 
        $updateArr,
        array('id' => $data->id)
    );        
    
    return "success";
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