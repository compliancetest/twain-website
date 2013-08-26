<?php
/**
* Manage User Profile Section
*/

function cp_user_detail_edit()
{
    global $wpdb, $current_user;
        
    if(!is_user_logged_in())
    {
        //Goto Homepage
        wp_redirect('/');
    }
    
    $user_id = $current_user->ID;
    
    $uname = trim($_POST['uname']);
    $email = trim($_POST['email']);
    if(!$uname && !$email)
    {
        echo 'Name and Email should not be empty';
        exit;
    }
    if(!$uname){
        echo 'Please enter your name';
        exit;
    }
    if(!$email){
        echo 'Please enter your email address.';
        exit;
    }
    
    //Update Phonenumber
    update_user_meta($user_id, 'phone_number', $_POST['phone_number']);
    update_user_meta($user_id, 'description', htmlentities($_POST['biography']));
    
    //Update User Name
    $uname = explode(' ', $uname);
    update_user_meta($user_id, 'first_name', $uname[1]);
    update_user_meta($user_id, 'last_name', $uname[0]);
    
    $email_regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/'; 
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
    
    $tokenWebserviceURL = get_eway_token_webservice_url();
    $customerID = get_eway_customer_id();
    $userName = get_eway_user_name();
    $userPWD = get_eway_user_pwd();
    
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
                'name' => $name_on_card,
                'cvn' => $card_cvc,
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
    global $wpdb;
    
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
        
        $result = sendRestUserAction('/user/create', $xmlData);
        
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
        
        $result = sendRestUserAction('/user/update', $xmlData);
        
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

function cp_edit_transaction_log(){
    global $wpdb;    
    
    if(!is_user_logged_in())
        return '<p class="message error">Permission Denied!</p>';
    
    $ids = $_POST['id'];
    
    if(!$ids)
    {
        $rows = array();    
    }else{
        $esb = new ManageESB();
        $rows =$esb->getTransactionLogByID($ids);
    }
    
    if($rows)
    {
        //Getting User Products
        $products = getUserProductsAndServices();
        $testCases  = getUserSubscribedCases();
        
        $result = "";
        ob_start();
        foreach($rows as $row)
        {
            ?>
            <input type="hidden" name="id[]" value="<?php echo $row->ID?>" />
            <div class="tr">
               <div class="td td-product">                               
                   <select name="product<?php echo $row->ID?>" class="select">
                      <?php foreach($products as $p){?>
                      <option value="<?php echo $p->ID?>" <?php echo $row->PRODUCT_ID == $p->ID ? 'selected="selected"' : ''?>><?php echo get_post_meta($p->ID, 'product_name', true)?></option>
                      <?php } ?>
                   </select>       
               </div>
               <div class="td td-case">
                   <select name="case<?php echo $row->ID?>" class="select">
                       <?php foreach($testCases as $c){ ?>
                       <option value="<?php echo $c->ID?>" <?php echo $row->TEST_CASE_DB_ID == $c->ID ? 'selected="selected"' : ''?>><?php echo cp_wrap($c->caseName, 12)?></option>
                       <?php } ?>
                   </select>
               </div>
               <div class="td td-suite td-fixed">
                   <a href="<?php echo get_permalink($row->TEST_SUITE_ID)?>"><?php echo cp_wrap($row->TEST_SUITE_NAME, 12)?></a>
               </div>
               <div class="td td-outcome tocenter td-fixed">
                   <?php if($row->TEST_OUTCOME == 'SUCCESS'){ ?>
                   <span class="status-certified">Pass</span>
                   <?php }else if($row->TEST_OUTCOME == 'FAILURE'){ ?>
                   <span class="status-testing">Fail</span>
                   <?php }else{ ?>
                   <span class="status-unverified">Not Performed</span>
                   <?php } ?>
               </div>
               <div class="td td-audit tocenter">
                   <select name="audit<?php echo $row->ID?>" class="select">
                       <option value="1" <?php echo $row->AUDIT_RECORD ? 'selected="selected"' : ''?>>Yes</option>
                       <option value="0" <?php echo !$row->AUDIT_RECORD ? 'selected="selected"' : ''?>>No</option>
                   </select>                   
               </div>
               <div class="td td-service td-fixed">
                   <?php echo cp_wrap($row->SERVICE, 17)?>
               </div>
               <div class="td td-action td-fixed">
                   <?php echo cp_wrap($row->ACTION, 11)?>
               </div>               
               <div class="td td-convsn td-fixed">
                   <?php echo  cp_wrap($row->CONVERSATION_ID, 12) ?>
               </div>
               <div class="td td-date tocenter td-fixed">                   
                   <?php echo formatDate($row->EXECUTION_DATE, 'm/d/y')?><br />
                   <?php echo date("H:i:s", strtotime($row->EXECUTION_DATE)) ?>
               </div>
               <div class="td td-from td-fixed">
                    <div style="border-bottom: solid 1px #999; padding-bottom: 3px; margin-bottom: 3px;"><?php echo $row->FROM_PARTY_ID?></div>
                    <?php echo $row->TO_PARTY_ID?>
               </div>
               <!--<div class="td td-to td-fixed"><?php echo $row->TO_PARTY_ID?></div>-->
               <div class="clear"></div> 
           </div>                       
            <?php
        }
        $result = ob_get_contents();
        ob_end_clean();
        
        return $result;
    }else{
        return '<p class="message error">Invalid Request!</p>';
    }
}


function cp_view_validation_log()
{
    global $wpdb;
    
    $id = $_POST['id'];
    
    $esb = new ManageESB();
    
    $data = $esb->getValidationResult($id);
    
    if($data === null)
    {
        return '<p class="message error">Invalid Request!</p>';
    }
    
    ob_start();
    foreach($data as $row){
    ?>
    <div class="tr">
        <div class="td td-phase"><?php echo $row->PHASE ?></div>
        <div class="td td-status tocenter">
            <?php
                switch(strtolower($row->STATUS))
                {
                    case 'ok':
                    case 'passed':
                        echo '<span class="status-active">Ok</span>';
                        break;
                    case 'error':
                    case 'failed':
                        echo '<span class="status-suspended">Error</span>';
                        break;
                    default:
                        echo '<span>' . $row->STATUS . '</span>';
                        break;
                }
            ?>
        </div>
        <div class="td td-result tocenter">
            <?php
                if(!$row->HARNESS_VALIDATION_ERROR)
                    echo '-';
                else{
                    echo '<a href="/view-validation-error?id=' . $row->ID . '" target="_blank">XML</a>' . ' &middot; '
                    . '<a href="/view-validation-error?id=' . $row->ID . '&mode=html" target="_blank">HTML</a>';;
                }
            ?>
        </div>
        <div class="clear"></div>    
    </div>
    <?php
    }
    if(!$data)
    {
        ?>
        <div class="tr">
            <div class="td td-full">No data found!</div>
            <div class="clear"></div>
        </div>
        <?php
    }
    $result = ob_get_contents();
    ob_end_clean();
    
    return $result;
}

function cp_save_transaction_log()
{
    global $wpdb;    
    
    if(!is_user_logged_in())
        return '<p class="message error">Permission Denied!</p>';
    
    $ids = $_POST['id'];
    
    if(!$ids)
    {
        return '<p class="message error">Invalid Request!</p>';
    }else{
        $esb = new ManageESB();
        $rows =$esb->getTransactionLogByID($ids);
        
        foreach($rows as $row)
        {
            $caseDBId = $_POST['case' . $row->ID];
            $caseId = get_post_meta($caseDBId, 'test_case_id', true);
            $suiteId = get_post_meta($caseDBId, 'test_suite', true);
            $productId = $_POST['product' . $row->ID];
            $audit = $_POST['audit' . $row->ID];
            
            
            $esb = new ManageESB();
            
            $query = ManageESB::$esbdb->prepare("UPDATE " . $esb->table_metadata . " SET TEST_CASE_ID=%s, TEST_SUITE_ID=%d, PRODUCT_ID=%d, AUDIT_RECORD=%d WHERE ID=%d", $caseId, $suiteId, $productId, $audit, $row->ID);
            ManageESB::$esbdb->query($query);
        }
        
    }
    
    
    return 'success';
    
}

function cp_delete_transaction_log(){
    global $wpdb;    
    
    if(!is_user_logged_in())
    {
        addMessage('Permission Denied!', 'error');
        return false;
    }
    
    $ids = $_POST['id'];
    
    if(!$ids)
    {
        addMessage('Invalid Request!', 'error');
        return false;
    }else{
        $esb = new ManageESB();
        $rows =$esb->getTransactionLogByID($ids);
    }
    
    if(!$rows)
    {
        addMessage('Invalid Request!', 'error');
        return false;
    }
    
    $lIds = array();
    foreach($rows as $row)
    {
        $lIds[] = $row->ID;
    }
    
    $esb = new ManageESB();
    
    //Delete MSH_METADATA_PAYLOAD            
    $query = "DELETE FROM " . $esb->table_metadata_payload . " WHERE MSH_METADATA_ID in (" . implode(", ", $lIds) . ")";    
    ManageESB::$esbdb->query($query);
    
    //DELETE FROM MSH_METADATA_VALIDATION_RESULT
    $query = "DELETE FROM " . $esb->table_metadata_validation_result . " WHERE MSH_METADATA_ID in (" . implode(", ", $lIds) . ")";    
    ManageESB::$esbdb->query($query);
    
    
    $query = "DELETE FROM " . $esb->table_metadata . " WHERE ID in (" . implode(", ", $lIds) . ")";    
    ManageESB::$esbdb->query($query);
    
    
    
    addMessage("Selected data was removed!");
    
    return true;
}