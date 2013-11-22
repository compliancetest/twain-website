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
    update_user_meta($user_id, 'phone_number', $_POST['phone_number']);
    update_user_meta($user_id, 'description', htmlentities($_POST['biography']));
    
    //Timezone
    update_user_meta($user_id, 'timezone', htmlentities($_POST['timezone']));
    
    //Update User Name
    //$uname = explode(' ', $uname);
    wp_update_user( array ('ID' => $user_id, 'first_name' => $first_name, 'last_name' => $last_name, 'display_name' => $first_name /*. " " . $last_name*/)) ;
    
    $email_regex = '/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-z]{2,3})$/'; 
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
    
    //Update Email
    wp_update_user(array('ID' => $user_id, 'user_email' => esc_attr($email)));
    
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
    
    $result['CCCvn'] = $card->cvn;
    
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
                <ewayCustomerFirstName></ewayCustomerFirstName> 
                <ewayCustomerLastName></ewayCustomerLastName> 
                <ewayCustomerEmail></ewayCustomerEmail> 
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
            'man:Email' => $user->user_email,
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
                'card_number' => encrypt_card_number($card_number),
                'customer_id' => $result,
                /*'name' => $name_on_card,
                'cvn' => $card_cvc,*/
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
            'man:Email' => $user->user_email,
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
            $wpdb->update($wpdb->prefix . "users_cards", array('cvn' => $card_cvc, 'name' => $name_on_card), array('id' => $card->id));
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
    
    $user_organisation = $_POST['user_organisation'];
    $user_organisation_web = $_POST['user_organisation_web'];
    $user_organisation_desc = $_POST['user_organisation_desc'];
    $user_organisation_abn = $_POST['user_organisation_abn'];
    
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

function cp_save_customer_harness_detail()
{
    global $wpdb, $CPRest;
    
    $id = $_POST['id'];
    $user_id = get_current_user_id();
    
    $user = get_userdata($user_id);
    
    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "users_purchases WHERE id=%d AND user_id=%d", $id, $user_id);
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
    
    if(!$data->esb_user_id)
    {
        $xmlData = '<api:createUserRequest xmlns:api="http://compliancetest.net/api">
                        <api:user>
                            <api:username>' . $user->user_login . "_" . $data->suite_id . '</api:username>
                            <api:password>' . $_POST['harness_password'] . '</api:password>
                            <api:userGroups>
                                <api:group>
                                    <api:groupId>' . $community_id . '</api:groupId>
                                    <api:groupName>' . bp_get_group_name($group) . '</api:groupName>
                                </api:group>
                            </api:userGroups>                       
                            <api:userPModeAgreement>' . $_POST['p_mode_agreement'] . '</api:userPModeAgreement>                            
                            <api:userEndpoint>' . $_POST['tester_endpoint_url'] . '</api:userEndpoint>
                            <api:userEndpointUsername>' . $_POST['tester_username'] . '</api:userEndpointUsername>
                            <api:userEndpointPassword>' . $_POST['tester_password'] . '</api:userEndpointPassword>
                        </api:user>
                    </api:createUserRequest>';
        
        $result = $CPRest->doUserAPI('user/create', $xmlData);
        
        $resultDoc = new DOMDocument();
        
        if(!$resultDoc || !$resultDoc->loadXML($result))
        {
            return "There was ane error while saving your data.";
        }else if($resultDoc->getElementsByTagName('code')->item(0)->nodeValue == 'ERROR'){
            return $resultDoc->getElementsByTagName('error')->item(0)->nodeValue;
        }else{ //Success
            $wpdb->update($wpdb->prefix . "users_purchases", array('esb_user_id' => $resultDoc->getElementsByTagName('userId')->item(0)->nodeValue), array('id' => $id));            
        }
    }else{
        //Update Data
        $xmlData = '<api:updateUserRequest xmlns:api="http://compliancetest.net/api">
                    <api:user>
                        <api:userId>' . $data->esb_user_id . '</api:userId>
                        <api:username>' . $data->harness_username . '</api:username>            
                        <api:password>' . $_POST['harness_password'] . '</api:password>                        
                        <api:userGroups>
                            <api:group>
                                <api:groupId>' . $community_id . '</api:groupId>
                                <api:groupName>' . bp_get_group_name($group) . '</api:groupName>
                            </api:group>
                        </api:userGroups>   
                        <api:userPModeAgreement>' . $_POST['p_mode_agreement'] . '</api:userPModeAgreement>                            
                        <api:userEndpoint>' . $_POST['tester_endpoint_url'] . '</api:userEndpoint>
                        <api:userEndpointUsername>' . $_POST['tester_username'] . '</api:userEndpointUsername>
                        <api:userEndpointPassword>' . $_POST['tester_password'] . '</api:userEndpointPassword>
                    </api:user>
                </api:updateUserRequest>';
        
        $result = $CPRest->doUserAPI('user/update', $xmlData);
        
        $resultDoc = new DOMDocument();
        
        if(!$result || !$resultDoc->loadXML($result))
        {
            return 'There was an error while updating your data.';
        }else if($resultDoc->getElementsByTagName('code')->item(0)->nodeValue == 'ERROR'){            
            return $resultDoc->getElementsByTagName('error')->item(0)->nodeValue;
        }
        
    }
    
    $updateArr = array(
        'p_mode_agreement' => $_POST['p_mode_agreement'],
        'harness_password' => $_POST['harness_password']
    );
    
    if($_POST['p_mode_agreement'] == 'HIGH-END'){
        $updateArr['tester_endpoint_url'] = $_POST['tester_endpoint_url'];
        $updateArr['tester_username'] = $_POST['tester_username'];
        $updateArr['tester_password'] = $_POST['tester_password'];
    }
        
    $wpdb->update($wpdb->prefix . "users_purchases", 
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

