<?php
/**
 * Manage User Profile Section
 */

add_filter('user_contactmethods', 'cp_user_details');
function cp_user_details($user_contactmethods, $user = null)
{
    $user_contactmethods['phone_number'] = 'Phone Number';
    $user_contactmethods['user_organisation'] = 'Organisation Name';
    $user_contactmethods['user_organisation_web'] = 'Organisation Website';
    $user_contactmethods['user_organisation_desc'] = 'Organisation Description';
    $user_contactmethods['user_organisation_abn'] = 'Organisation ABN';

    return $user_contactmethods;
}

function remove_special_chars($string)
{
    return preg_replace('/[^A-Za-z0-9\-\ \',]/', '', $string);
}

function cp_user_detail_edit()
{
    global $wpdb, $current_user;

    if (!is_user_logged_in()) {
        //Goto Homepage
        wp_redirect('/');
    }

    $user_id = $current_user->ID;

    $first_name = trim(remove_special_chars($_POST['first_name']));
    $last_name = trim(remove_special_chars($_POST['last_name']));
    $email = trim($_POST['email']);
    $phoneNumber = trim($_POST['phone_number']);

    if (!$first_name && !$last_name && !$email) {
        echo 'First Name, Last Name and Email should not be empty';
        exit;
    }
    if (!$first_name) {
        echo 'Please enter your first name';
        exit;
    }
    if (!$last_name) {
        echo 'Please enter your last name';
        exit;
    }
    if (!$email) {
        echo 'Please enter your email address.';
        exit;
    }
    if (!$phoneNumber) {
        echo 'Please enter your phone number.';
        exit;
    }

    if (!preg_match('#[^0-9]#', str_replace(array('+', ' ', '(', ')', '-'), '', $_POST['phone_number'])) != 1)
    {
        echo "Invalid phone number";
        exit;
    }

    //Update Phonenumber
    update_user_meta($user_id, 'phone_number', htmlentities($_POST['phone_number']));
    update_user_meta($user_id, 'description', htmlentities($_POST['biography']));

    //Timezone
    update_user_meta($user_id, 'timezone', htmlentities($_POST['timezone']));

    //Default Dashboard Tab Url
    update_user_meta($user_id, 'dashboard_page_url', htmlentities($_POST['dashboard_page_url']));
    update_user_meta($user_id, 'dashboard_page_title', htmlentities($_POST['dashboard_page_title']));

    delete_user_meta($user_id, 'fill_profile_notification');

    //Update User Name
    //$uname = explode(' ', $uname);
    wp_update_user(array('ID' => $user_id, 'first_name' => $first_name, 'last_name' => $last_name, 'display_name' => $first_name /*. " " . $last_name*/));

    $email_regex = '/^[_a-zA-Z0-9-+]+(\.[_a-zA-Z0-9-+]+)*@[a-z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-z]{2,3})$/';
    if (!preg_match($email_regex, $email)) {
        echo 'Please enter a valid email address';
        exit;
    }

    //Check Email Duplication
    $query = $wpdb->prepare("SELECT ID FROM " . $wpdb->users . " WHERE user_email=%s AND ID != %d", $email, $user_id);
    $uID = $wpdb->get_var($query);
    if ($uID) {
        echo 'This email address already exists!';
        exit;
    }
    $query = $wpdb->prepare("SELECT user_id FROM " . $wpdb->prefix . "users_changes WHERE email_changed=%s AND user_id != %d", $email, $user_id);
    $uID = $wpdb->get_var($query);
    if ($uID) {
        echo 'This email address already exists!';
        exit;
    }

    //Not Update Email and Save the email address temporary
    $query = $wpdb->prepare("SELECT user_email FROM " . $wpdb->users . " WHERE ID = %d", $user_id);
    $currentEmail = $wpdb->get_var($query);
    if ($currentEmail != $email) {
        $wpdb->query("DELETE FROM " . $wpdb->prefix . "users_changes WHERE user_id =" . $user_id);

        $verification_code = md5($email);
        $wpdb->insert($wpdb->prefix . 'users_changes', array('user_id' => $user_id, 'email_changed' => $email, 'verification_code' => $verification_code, 'updated_date' => date('Y-m-d H:i:s')));

        $data = array(
            '[name]' => get_user_meta($user_id, 'first_name', true) . " " . get_user_meta($user_id, 'last_name', true),
            '[username]' => $current_user->user_login,
            '[email]' => $email,
            '[link]' => get_site_url() . '?cp-action=email_activation&token=' . $verification_code
        );

        cp_send_email(array('name' => $data['[name]'], 'email' => $data['[email]']), 'email_changed', $data);
        cp_send_email_to_admin('email_changed_admin', $data);
        addMessage('A confirmation email has been sent to updated email address. Please confirm your new email address using the link it contains.');
    }

    //Update Password
    $newPass = $_POST['new_pass'];
    $confPass = $_POST['conf_pass'];
    if ($newPass || $confPass) {
        if ($newPass != $confPass) {
            echo 'The passwords do no match!';
            exit;
        } else {
            if (!\User\User::isPasswordValid($newPass)) {
                echo 'Invalid password!';
                exit;
            }

            if (!wp_check_password($_POST['curr_pass'], $current_user->data->user_pass, $user_id)) {
                echo 'Your current password is incorrect.';
                exit;
            }

            if ($_POST['curr_pass'] == $newPass) {
                echo 'New password should be different to old one';
                exit;
            }
            //update password
            wp_update_user(array('ID' => $user_id, 'user_pass' => $confPass));
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

function cp_user_organisation_detail_edit()
{
    global $wpdb;

    $query = $wpdb->prepare("SELECT organisation_id FROM " . $wpdb->prefix . "organisations_members WHERE is_admin=1 AND user_id=%d AND organisation_id=%d", get_current_user_id(), $_REQUEST['organisation_id']);
    $user_organisation_id = $wpdb->get_var($query);

    if ($user_organisation_id) {
        $organisation = new CT_Organisation($user_organisation_id);
        $organisation->bind($_POST);
        $response = $organisation->save_force();
        if ($response) {
            echo 'success';
        } else {
            echo 'fail';
        }
    } else {
        echo 'fail';
    }
    exit;
}

//Delete Payment Information
function cp_delete_payment_method()
{
    global $wpdb, $current_user;

    //Goto Homepage
    if (!is_user_logged_in())
        die("Permission Denied!");

    $user_id = get_current_user_id();
    $id = $_REQUEST['id'];

    $query = $wpdb->prepare("SELECT id FROM " . $wpdb->prefix . "organisations_payment_methods WHERE user_id=%d and id=%d", $user_id, $id);
    $id = $wpdb->get_var($query);
    if (!$id) {
        echo("Invalid Request");
        exit;
    }

    $query = $wpdb->prepare("SELECT count(id) FROM wp_organisations_charge WHERE is_paid = 0 AND payment_id  = %d", $id);
    $count = $wpdb->get_var($query);
    if ($count > 0) {
        exit("All charges associated with this payment method must be paid before the payment method can be deleted.");
    }

    $wpdb->query("DELETE FROM " . $wpdb->prefix . "organisations_payment_methods WHERE id=" . $id);

    cp_update_user_cards_count($user_id);

    echo "success";
    exit;
}

//Edit User Payment Info
function cp_user_payment_edit()
{
    global $wpdb, $current_user;

    //Goto Homepage
    if (!is_user_logged_in()) {
        echo "Permission Denied!";
        exit;
    }

    get_currentuserinfo();

    $user_id = $current_user->ID;
    $is_admin = $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_organisations_members WHERE user_id = %d AND is_admin = 1 ", $user_id));
    if (!$is_admin) {
        exit("Permission Denied! Only organisation admin can edit payment methods!");
    }
    $id = $_REQUEST['id'];
    $card = getOrganisationCardById($id, $is_admin->organisation_id);

    $result = array();

    if (!$card) {
        echo "Invalid Request!";
        exit;
    }

    if ($card->invoice_me == 0) {
        $result = getCustomerCardDetailById($card->customer_id);

        if (!$result || isset($result['faultstring'])) {
            echo "There was an error while getting the information from eWay.";
            exit;
        }
    } else {
        $result['card_number'] = '';
        $result['name_on_card'] = '';
        $result['card_expiry'] = '';
        $result['card_cvc'] = '';
    }

    $result['nickname'] = $card->nickname;
    $result['email'] = $card->email;
    $result['invoice_me'] = $card->invoice_me;
    $result['organisation_id'] = $card->organisation_id;
    $result['is_default'] = $card->is_default;
    $result['customer_reference'] = $card->customer_reference;
    $result = stripslashes_deep($result);
    echo json_encode($result);
    exit;
}

/**
 * @param $organisationId
 * @param $organisationsList
 * @return bool
 */
function saveProductsOrganisations($organisationId, $organisationsList){
    global $wpdb;

    if (!$wpdb->get_row($wpdb->prepare("SELECT * FROM wp_organisations_members WHERE user_id = %d AND is_admin = 1 ", get_current_user_id()))) {
        exit("Permission Denied! Only organisation admin can edit organisations list!");
    }

    $wpdb->update("wp_organisations",
        ['products_organisations' => $organisationsList],
        ['id' => $organisationId],
        ['%s'],
        ['%d']
    );
    return true;
}
//Save User Payment Information
function cp_user_payment_save()
{
    global $wpdb, $current_user;

    //Goto Homepage
    if (!is_user_logged_in()) {
        return "Permission Denied!";
    }

    get_currentuserinfo();
    //$organisation_id = getOrganisationID();
    //$organisation = getOrganisationById($organisation_id);

    $user_id = $current_user->ID;

    $card_number = str_replace(' ', '', $_POST['card_number']);
    $nickname = trim($_POST['nickname']);
    $email = trim($_POST['email']);
    //$email = $organisation->contact_email;
    $name_on_card = trim($_POST['name_on_card']);
    $card_expiry = trim($_POST['card_expiry']);
    $card_cvc = trim($_POST['card_cvc']);
    $invoice_me = trim($_POST['invoice_me']);
    $organisation_id = trim($_POST['organisation_id']);
    $is_default = trim($_POST['is_default']);
    $customer_reference = trim($_POST['customer_reference']);

    $id = intval(trim($_POST['id']));

    $is_admin = $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_organisations_members WHERE user_id = %d AND is_admin = 1 ", $user_id));
    if (!$is_admin) {
        exit("Permission Denied! Only organisation admin can edit payment methods!");
    }
    if ($id) {
        $query = $wpdb->prepare("SELECT id FROM wp_organisations_payment_methods WHERE organisation_id = %d AND id = %d", $is_admin->organisation_id, $id);
        $id = intval($wpdb->get_var($query));
        if (!$id) {
            return "Invalid Request!";
        }
    }

    if(empty($name_on_card)){
        return 'Card holder name is empty!';
    }

    if (!$nickname) {
        return 'Please enter a nickname of this card!';
    }

    $checkNicknameExist = $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_organisations_payment_methods WHERE organisation_id = %d AND nickname = %s", $is_admin->organisation_id, $nickname));
    if ($checkNicknameExist) {
        if($id != $checkNicknameExist->id) {
            return "You already using this payment method nickname!";
        }
    }

    if ($invoice_me == 1) {
        if ($is_default == '1') {
            $wpdb->update($wpdb->prefix . "organisations_payment_methods", array('is_default' => 0), array('organisation_id' => $organisation_id));
        }

        if ($id) {
            $wpdb->update($wpdb->prefix . "organisations_payment_methods", array('nickname' => $nickname, 'invoice_me' => $invoice_me, 'customer_reference' => $customer_reference, 'status' => 'Active', 'is_default' => $is_default), array('id' => $id));
            return $id;
        } else {
            $wpdb->insert($wpdb->prefix . "organisations_payment_methods", array(
                'user_id' => $user_id,
                'nickname' => $nickname,
                'customer_reference' => $customer_reference,
                'invoice_me' => $invoice_me,
                'organisation_id' => $organisation_id,
                'is_default' => $is_default,
                'status' => 'Active',
                'created_date' => date('Y-m-d H:i:s')
            ));
            $new_pm_id = $wpdb->insert_id;
            cp_update_user_cards_count($user_id);
            return $new_pm_id;
        }
    }

    $email_regex = '/^[_a-zA-Z0-9-+]+(\.[_a-zA-Z0-9-+]+)*@[a-z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-z]{2,3})$/';

    if (!$email) {
        return 'Please enter the email to receive invoices!';
    } else if (!preg_match($email_regex, $email) || strlen($email) > 50) {
        echo 'Please enter a valid email address. Due to payment system limitations, it cannot be more than 50 characters in length.';
        exit;
    }

    //Card Number
    if ($card_number == '') {
        return 'Credit card number is empty!';
    } else if (strpos($card_number, 'XXXXXX') === false && !check_cc($card_number)) {
        return 'Credit card number is not valid!';
    }

    if (!$card_expiry) {
        return 'Please specify your card expiry date!';
    } else {
        $card_expiry_arr = explode('/', $card_expiry);
        if ($card_expiry_arr[0] > 12 || strlen($card_expiry_arr[1]) > 2) {
            return 'Your expiry date is incorrect!';
        } else if (!check_exp_date($card_expiry_arr[0], $card_expiry_arr[1])) {
            return 'Your card has expired or your expiry date is incorrect!';
        }
    }


    if (!($card_cvc != '' && (strlen($card_cvc) == 3 || strlen($card_cvc) == 4))) {
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

    if (!curl_errno($ch)) {
        $xmlObj = simplexml_load_string($response);
        if ($xmlObj && $xmlObj->ewayTrxnStatus == 'False') {
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
    if (!$id) {
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
        if (is_array($result)) {
            return $result['faultstring'];
        } else if (!$result) {
            return 'There was an error while saving your payment information.';
        } else {
            //Success
            if ($is_default == '1') {
                $wpdb->update($wpdb->prefix . "organisations_payment_methods", array('is_default' => 0), array('organisation_id' => $organisation_id));
            }

            $query_result = $wpdb->insert($wpdb->prefix . "organisations_payment_methods", array(
                'user_id' => $user_id,
                'nickname' => $nickname,
                'customer_reference' => $customer_reference,
                'email' => $email,
                'card_number' => encrypt_card_number($card_number),
                'customer_id' => $result,
                'status' => 'Active',
                'organisation_id' => $organisation_id,
                'is_default' => $is_default,
                'created_date' => date('Y-m-d H:i:s')
            ));
            if (!$query_result)
                $id = $wpdb->last_error;
            else {
                $id = $wpdb->insert_id;
                cp_update_user_cards_count($user_id);
            }
        }
    } else {
        //Getting Card
        $card = getOrganisationCardById($id, $is_admin->organisation_id);

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
        if ($result == 'true') {
            if ($is_default == '1') {
                $wpdb->update($wpdb->prefix . "organisations_payment_methods", array('is_default' => 0), array('organisation_id' => $organisation_id));
            }

            $wpdb->update($wpdb->prefix . "organisations_payment_methods", array('nickname' => $nickname, 'email' => $email, 'customer_reference' => $customer_reference, 'status' => 'Active', 'is_default' => $is_default), array('id' => $card->id));

            echo 'success';
        } else {
            echo $result['faultstring'];
        }
        exit;
    }

    return $id;
}

//Join organisation
function cp_user_organisation_join()
{
    if (!is_user_logged_in()) {
        exit('Please log in to perform this action!');
    }
    $user_id = get_current_user_id();
    $organisation_key = isset($_POST['user_organisation_key']) ? htmlspecialchars($_POST['user_organisation_key']) : null;

    $org_controller = new CT_Organisation_Controller();
    $organisation = ct_get_organisation_by_key($organisation_key);
    if ($organisation) {
        $org_controller->add_membership($user_id, $organisation->id);
        exit('success');
    }
    exit('Organisation not found!');
}

//Edit organisation
function cp_user_organisation_edit()
{
    global $wpdb, $current_user;
    if (!is_user_logged_in()) {
        exit('Please log in to perform this action!');
    }
    $user_id = get_current_user_id();

    $org_membership = ct_get_user_organisation_membership($current_user->ID);
    if (!$org_membership) {
        $user_organisation = htmlspecialchars($_POST['user_organisation']);
        $user_organisation_abn = htmlspecialchars($_POST['user_organisation_abn']);
        $user_organisation_web = htmlspecialchars($_POST['user_organisation_web']);
        $user_organisation_desc = htmlspecialchars($_POST['user_organisation_desc']);

        update_user_meta($user_id, 'user_organisation', $user_organisation);
        update_user_meta($user_id, 'user_organisation_abn', $user_organisation_abn);
        update_user_meta($user_id, 'user_organisation_web', $user_organisation_web);
        update_user_meta($user_id, 'user_organisation_desc', $user_organisation_desc);
    }

    $user_id = get_current_user_id();

    $user_org = trim(get_user_meta($user_id, 'user_organisation', true));
    $user_org_abn = trim(get_user_meta($user_id, 'user_organisation_abn', true));
    $user_org_web = get_user_meta($user_id, 'user_organisation_web', true);
    $user_org_desc = get_user_meta($user_id, 'user_organisation_desc', true);
    $message_error = $message_success = false;
    //check that name and ABN for user organisation not empty
    if (empty($user_org_abn) || empty($user_org) || $user_org_abn == '-' || $user_org == '-') {
        $message_error = 'Organisation Name and ABN must be populated before an Organisation Record can be created';
    }
    if (false === $message_error) {
        $is_abn_used = $is_name_used_in_xero = false;
        //check that ABN number not used for another organisation
        $is_abn_used = (boolean)$wpdb->get_results($wpdb->prepare("SELECT * FROM wp_organisations WHERE abn = %s ", $user_org_abn));
        //check that organisation name not used in Xero
        $is_name_used_in_xero = (boolean)$wpdb->get_results($wpdb->prepare("SELECT * FROM wp_organisations WHERE organisation_name = %s ", $user_org));
        if ($is_name_used_in_xero || $is_abn_used) {
            $message_error = 'A record for an organisation with the same ABN or Name has already been created. Your organisation may already be set up on ' . get_site_title() . '.';
        }
    }
    if (false === $message_error) {
        $organisationClass = new CT_Organisation($_POST['id']);
        $data = array(
            'organisation_name' => $user_org,
            'organisation_description' => $user_org_desc,
            'organisation_website' => $user_org_web,
            'invoice_me' => 0,
            'abn' => $user_org_abn,
            'contact_first_name' => get_user_meta($user_id, 'first_name', true),
            'contact_last_name' => get_user_meta($user_id, 'last_name', true),
            'contact_email' => $current_user->user_email,
        );

        $organisationClass->bind($data);
        if ($organisationClass->save()) {
            //Save Organisation Admin
            $organisationClass->save_organisation_admin($organisationClass->id, $user_id);
            $full_name = get_user_meta($user_id, 'first_name', true) . " " . get_user_meta($user_id, 'last_name', true);
            $email_data = array(
                '[requester_name]' => $full_name,
                '[requester_email]' => $current_user->user_email,
                '[organisation]' => $user_org,
                '[organisation_website]' => $user_org_web,
                '[organisation_description]' => $user_org_desc,
                '[organisation_abn]' => $user_org_abn
            );

            cp_send_email_to_admin('send_organisation_signup_request_to_admin', $email_data);
            cp_send_email(array('email' => $current_user->user_email, 'name' => $full_name), 'send_organisation_signup_request_to_user', $email_data);
            exit('success');
        }
    }
    exit($message_error);
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

    $test_suite = new TestSuite($row->suite_id);
    $test_suite->load();
    $allowedRoles = array();
    foreach ($test_suite->roles AS $role) {
        $allowedRoles = array_merge($allowedRoles, explode(',', $role['profileTypes']));
    }

    if (!$row):
        ?>
        <div class="popup-box" id="harness-detail-box<?php echo $_REQUEST['id'] ?>"
             style="display: none; width: 450px;">
            <div class="popup-box-header radius6 noradiusbottom">Invalid Request!</div>
            <div class="popup-box-content grid-box-body">
                <p class="message error">An organisation record needs to be created for your organisation as test suite
                    subscriptions are owned by organisations. You can create a record via the "+" icon on the
                    Organisation section in your Profile tab.</p>
            </div>
            <div class="popup-box-footer radius6 noradiustop">
                <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span
                        class="t">Cancel</span></a>

                <div class="clear"></div>
            </div>
        </div>
    <?php else: ?>

        <div class="popup-box" id="harness-detail-box<?php echo $_REQUEST['id'] ?>"
             style="display: none; width: 450px;">
            <div id="harness-detail-container">
                <div class="popup-box-header radius6 noradiusbottom">Test Harness Access Detail.</div>
                <form name="harness-form" id="harness-form" action="">
                    <div class="popup-box-content grid-box-body">
                        <div class="second-tabs">
                            <ul>
                                <li class="active"><a onclick="switch_secondtabs(this)" rel="harness-direct"><span>Direct</span></a>
                                </li>
                                <li><a onclick="switch_secondtabs(this)" rel="harness-gateway"><span>Gateway</span></a>
                                </li>
                            </ul>
                            <div class="clear"></div>
                        </div>
                        <div class="second-tabs-container">
                            <div id="harness-direct" class="second-tab-content">
                                <div class="field-row">
                                    <div class="grid-cell">
                                        <label>P-Mode Profile:</label>
                                        <select name="p_mode_agreement" id="p_mode_agreement" class="select">
                                            <option value="LIGHT" selected="selected">LIGHT</option>
                                        </select>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <div class="harness-endpoint-info">
                                    <div class="field-row">
                                        <div class="grid-cell">
                                            <label>Harness EndPoint:</label>
                                            <input class="input" type="text" name="harness_endpoint_url"
                                                   id="harness_endpoint_url" readonly="readonly" disabled="disabled"
                                                   value="<?php echo $row->harness_endpoint_url ?>"/>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                    <div class="field-row">
                                        <div class="grid-cell">
                                            <label>Harness Username:</label>
                                            <input class="input" type="text" name="harness_username" readonly="readonly"
                                                   disabled="disabled" id="harness_username"
                                                   value="<?php echo $row->harness_username ?>"/>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                    <div class="field-row">
                                        <div class="grid-cell">
                                            <label>Harness Password:</label>
                                            <input class="input" type="text" name="harness_password"
                                                   id="harness_password" value="<?php echo $row->harness_password ?>"/>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                                <div
                                    class="tester-endpoint-info" <?php echo $row->p_mode_agreement == 'LIGHT' ? 'style="display: none"' : '' ?>>
                                    <div class="field-row">
                                        <div class="grid-cell">
                                            <label>Tester EndPoint:</label>
                                            <input class="input" type="text" name="tester_endpoint_url"
                                                   id="tester_endpoint_url"
                                                   value="<?php echo $row->tester_endpoint_url ?>"/>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                    <div class="field-row">
                                        <div class="grid-cell">
                                            <label>Tester Username:</label>
                                            <input class="input" type="text" name="tester_username" id="tester_username"
                                                   value="<?php echo $row->tester_username ?>"/>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                    <div class="field-row">
                                        <div class="grid-cell">
                                            <label>Tester Password:</label>
                                            <input class="input" type="text" name="tester_password" id="tester_password"
                                                   value="<?php echo $row->tester_password ?>"/>
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
                                                foreach ($profileInstances AS $instance):
                                                    if ($instance->validation_status != 'valid' || $instance->content_length > get_option('s3_bulk_treshold') || !in_array(str_replace(' ', '', $instance->profile_role), $allowedRoles)) continue;
                                                    ?>
                                                    <option
                                                        value="<?php echo $instance->id; ?>" <?php echo ($row->profile_id == $instance->id) ? ('selected="selected"') : (''); ?>><?php echo $instance->profile_name; ?></option>
                                                <?php endforeach; endif; ?>
                                        </select>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <div class="field-row" id="gateway-box">
                                    <div class="grid-cell">
                                        <label>Gateway:</label>
                                        <select name="gateway_id" class="select">
                                            <option value="">None</option>
                                            <?php foreach ($gateways as $gateway): ?>
                                                <option
                                                    value="<?php echo $gateway->gateway_id; ?>" <?php echo ($row->gateway_id == $gateway->gateway_id) ? ('selected="selected"') : (''); ?>><?php echo $gateway->name; ?></option>
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
                        <a href="javascript:void(0)" class="action-btn process-btn"
                           onclick="saveHarnessDetails('<?php echo $_REQUEST['id'] ?>')"><span class="p"></span><span
                                class="t">Confirm</span></a>
                        <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span
                                class="t">Cancel</span></a>

                        <div class="clear"></div>
                    </div>
                    <div class="loading"></div>
                    <a class="close_btn"></a>
                    <input type="hidden" name="id" id="harness-id" value="<?php echo $row->id ?>"/>
                    <?php wp_nonce_field('save-harness', 'cp-action'); ?>
                </form>
            </div>
            <div id="harness-generate-profile-container" style="display: none;">
                <div class="popup-box-header radius6 noradiusbottom">Generate Profiles</div>
                <div class="popup-box-content grid-box-body">
                    This action will generate a custom set of data profiles in your test data tab specifically tailored
                    to work with the selected gateway profile.
                </div>
                <div class="popup-box-footer radius6 noradiustop">
                    <a href="javascript:confirmGenerateProfile('<?php echo $_REQUEST['id'] ?>')"
                       class="action-btn process-btn"><span class="p"></span><span class="t">Confirm</span></a>
                    <a href="javascript:cancelGenerateProfile()" class="action-btn cancel-btn"><span
                            class="p"></span><span class="t">Cancel</span></a>

                    <div class="clear"></div>
                </div>
            </div>
            <script type="text/javascript">
                function switch_secondtabs(obj) {
                    jQuery(obj).parent().addClass('active');
                    jQuery(obj).parent().siblings().removeClass('active');

                    jQuery('.second-tabs-container > div').hide();
                    var id = jQuery(obj).attr("rel");

                    jQuery('#' + id).show();

                    return false;
                }
                function viewProfileData() {
                    var profile_id = jQuery('#harness-gateway select[name=profile_id]').val();
                    var subscription_id = '<?php echo $_REQUEST['id']; ?>';

                    if (profile_id == 0) {
                        jQuery('#profile-data-container').html('');
                        jQuery('select[name=gateway_id]')[0].selectedIndex = 0;
                        jQuery('#gateway-box').hide();
                    } else {
                        jQuery('#gateway-box').show();
                        jQuery('#harness-detail-box<?php echo $_REQUEST['id']?> .loading').show();
                        jQuery.ajax({
                            url: '/?cp-action=<?php echo wp_create_nonce('get-harness-profile-data')?>&id=' + profile_id + '&subscription_id=' + subscription_id,
                            type: 'post',
                            success: function (res) {
                                jQuery('#profile-data-container').html(res);
                                jQuery('#harness-detail-box<?php echo $_REQUEST['id']?> .loading').hide();
                            }
                        });
                    }
                }

                viewProfileData();
                jQuery('#my_testsuites .message').remove();

                function generateProfile() {
                    var tag = jQuery('input[name="tag"]').val().trim();

                    if (tag.length !== 0 && ( tag.length > 30 || !/^[a-zA-Z0-9_. -]+$/g.test(tag) )) {
                        jQuery('input[name="tag"]').addClass('input-error');
                        jQuery('.validation_error').show();
                        return;
                    }
                    jQuery('#harness-detail-container').hide();
                    jQuery('#harness-generate-profile-container').show();
                }
                function cancelGenerateProfile() {
                    jQuery('#harness-detail-container').show();
                    jQuery('#harness-generate-profile-container').hide();
                }
                function confirmGenerateProfile(id) {
                    jQuery('#harness-detail-container').show();
                    jQuery('#harness-generate-profile-container').hide();

                    jQuery('#harness-detail-box' + id + ' .loading').show();
                    jQuery('#harness-detail-box' + id + ' .message').remove();

                    jQuery.ajax({
                        url: '/',
                        data: jQuery('#harness-form').serialize() + '&action_mode=generate-profile',
                        type: 'post',
                        success: function (rsp) {
                            jQuery('#harness-detail-box' + id + ' .loading').hide();
                            if (rsp == 'success') {
                                jQuery('#my_testsuites').prepend('<div style="margin-bottom:20px;" class="message success">New profile has been generated successfully!</div>');
                                jQuery('#harness-detail-box' + id + ' .close-popup-btn').click();
                            }
                            else {
                                jQuery('#harness-detail-box' + id + ' .popup-box-footer').prepend('<div class="message error">' + rsp + "</div>");
                            }
                        },
                        error: function (err) {
                            jQuery('#harness-detail-box' + id + ' .loading').hide();
                            jQuery('#harness-detail-box' + id + ' .popup-box-footer').prepend('<div class="message error">' + err.responseText + "</div>");
                        }
                    });
                    //return false;
                }

                function selectAlias() {
                    var gateway_id = jQuery('select[name=alias] option:selected').attr('rel');
                    jQuery('select[name=gateway_id] option').attr('selected', false);
                    jQuery('select[name=gateway_id] option[value=' + gateway_id + ']').attr('selected', true);
                    jQuery('input[name=gateway_label]').val(jQuery('select[name=gateway_id] option[value=' + gateway_id + ']').html());
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

    $gateways = $wpdb->get_results("SELECT * FROM wp_gateways");
    $alias_list = array();

    foreach ($gateways as $gateway) {
        if ($gateway->alias_list != '') {
            $alias_group = explode('|', $gateway->alias_list);
            foreach ($alias_group as $alias) {
                $alias_list[] = array('gateway_id' => $gateway->gateway_id, 'alias' => $alias);
            }
        }
    }

    usort($alias_list, 'james_compare_alias');

    if (!empty($row)):
        $profile_instance = S3Wrapper::getProfile($row->token);
        $profile_schema = json_decode(base64_decode($row->schema));

        $profile_type_title = $row->profile_type_title;
        if ($profile_schema->Version) {
            $version = array();
            foreach (get_object_vars($profile_schema->Version) as $k => $v) {
                $version[] = $v;
            }
            $profile_type_title .= ' v' . implode('.', $version);
        }
        ?>
        <?php
        $hasABN = 0;
        if (is_iterable($profile_instance->Entity)) {
            foreach ($profile_instance->Entity AS $label => $value) {
                if (strtolower($label) == 'abn') {
                    $hasABN = 1;
                    break;
                }
            }
        }
        ?>
        <?php if ($hasABN): ?>
        <div class="field-row">
            <div class="grid-cell">
                <label>Gateway:</label>
                <input class="input" type="text" name="gateway_label" id="gateway_label" value="" readonly="readonly"
                       disabled="disabled"/>
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
                            <option value="<?php echo $alias['alias']; ?>"
                                    rel="<?php echo $alias['gateway_id']; ?>" <?php echo ($alias['gateway_id'] == $subscription_row->gateway_id && $alias['alias'] == $subscription_row->alias) ? ('selected') : (''); ?>><?php echo $alias['alias']; ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
            </div>
            <div class="clear"></div>
        </div>
        <script type="text/javascript">
            jQuery('#gateway-box').hide();
            selectAlias();
        </script>
    <?php else: ?>
        <input class="input" type="hidden" name="alias" value=""/>
        <script type="text/javascript">
            jQuery('#gateway-box').show();
        </script>
    <?php endif; ?>
        <div class="field-row">
            <div class="grid-cell">
                <label>Profile Type:</label>
                <input class="input" type="text" name="profile_type_title" value="<?php echo $profile_type_title; ?>"
                       readonly="readonly" disabled="disabled"/>
            </div>
            <div class="clear"></div>
        </div>

        <?php if (is_iterable($profile_instance->Entity)): ?>
        <?php foreach ($profile_instance->Entity as $label => $value): ?>
            <div class="field-row">
                <div class="grid-cell">
                    <label><?php echo $label; ?>:</label>
                    <input class="input" type="text"
                           name="profile_entity_<?php echo strtolower(str_replace(' ', '_', $label)); ?>"
                           value="<?php echo $value; ?>" readonly="readonly" disabled="disabled"/>
                    <input type="hidden" name="entity_<?php echo strtolower(str_replace(' ', '_', $label)); ?>"
                           value="<?php echo $value; ?>"/>
                </div>
                <div class="clear"></div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
        <div class="field-row">
            <div class="grid-cell">
                <label>Expected Entity Type:</label>
                <input class="input" type="text" name="expected_entity_type"
                       value="<?php echo $subscription_row->entity_type; ?>" readonly="readonly" disabled="disabled"/>
            </div>
            <div class="clear"></div>
        </div>
        <div class="field-row">
            <div class="grid-cell">
                <label>Expected Entity Value:</label>
                <input class="input" type="text" name="expected_entity_value"
                       value="<?php echo $subscription_row->entity_id; ?>" readonly="readonly" disabled="disabled"/>
            </div>
            <div class="clear"></div>
        </div>
        <div class="field-row">
            <div class="grid-cell">
                <label>Tag:</label>

                <div class="has-field-tooltip">
                    <input class="input field-tooltip" type="text" name="tag" value=""
                           onfocus="jQuery(this).removeClass('input-error');jQuery('.validation_error').hide();"
                           data-tooltip-content="Value to allow grouping of generated profiles"/>
                </div>
                <div class="validation_error" style="color: red; font-size: smaller; display: none; max-width: 290px;">
                    Tags may only use upper and lower case letters, numbers, underscores, dashes, dots and spaces. They
                    may be a maximum of 30 characters in length.
                </div>
            </div>
            <div class="clear"></div>
        </div>

        <div id="generate-profile-container">
            <a href="javascript: void(0)" class="action-btn process-btn" onclick="generateProfile()"><span
                    class="p"></span><span class="t">Generate Profiles</span></a>

            <div class="clear"></div>
        </div>
        <script>
            jQuery(document).ready(function () {
                jQuery('.field-tooltip').focus(function () {
                    var tooltip_obj,
                        parentContainer = jQuery(this).parent('.has-field-tooltip'),
                        fieldWidth = jQuery(this).outerWidth(),
                        fieldHeight = jQuery(this).outerHeight();

                    if (parentContainer.find('.simple_tooltip').length == 0) {
                        tooltip_obj = '<span class="simple_tooltip" style="width:' + fieldWidth + 'px; margin-left: ' + -(fieldWidth / 2) + 'px; bottom:' + (fieldHeight + 10) + 'px; ">' + jQuery(this).data('tooltip-content') + '<span></span></span>';
                        jQuery(this).after(tooltip_obj);
                    }
                    jQuery(this).next('.simple_tooltip').show();
                });
                jQuery('.field-tooltip').blur(function () {
                    jQuery(this).parent('.has-field-tooltip').find('.simple_tooltip').hide();
                });
            });
        </script>
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

    if (!$data) {
        return 'Invalid Request!';
    }

    $isSaved = false;

    //Harness Username and Password Should not be changed

    $_POST['harness_endpoint_url'] = $data->harness_endpoint_url;
    $_POST['harness_username'] = $data->harness_username;

    if ($_POST['p_mode_agreement'] == 'LIGHT') {
        $_POST['tester_endpoint_url'] = $data->tester_endpoint_url;
        $_POST['tester_username'] = $data->tester_username;
        $_POST['tester_password'] = $data->tester_password;
    }

    $community_id = cp_get_post_meta($data->suite_id, 'community_id', true);
    $group = groups_get_group(array('group_id' => $community_id));

    $updateArr = array(
        'p_mode_agreement' => $_POST['p_mode_agreement'],
        'harness_password' => $_POST['harness_password'],
        'gateway_id' => (($_POST['gateway_id'] != '') ? ($_POST['gateway_id']) : ('NULL')),
        'profile_id' => (($_POST['profile_id'] != '') ? ($_POST['profile_id']) : ('NULL')),
//        'force_e2e_routing' => (isset($_POST['force_e2e_routing']) ? 1 : 0),
        'alias' => (($_POST['alias'] != '') ? ($_POST['alias']) : (''))
    );

    if ($_POST['p_mode_agreement'] == 'HIGH-END') {
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

        add_filter('query', 'wp_db_null_value');
        $wpdb->update($wpdb->prefix . "users_subscriptions",
            $updateArr,
            array('id' => $data->id)
        );
        remove_filter('query', 'wp_db_null_value');

        if ((isset($_POST['action_mode']) && $_POST['action_mode'] == 'generate-profile') && $updateArr['profile_id'] != 'NULL') {
            $tag = false;
            if (isset($_POST['tag']) && !empty($_POST['tag'])) {
                $tag = $_POST['tag'];
            }
            generateProfile($updateArr['profile_id'], $community_id, $tag);
        }

        return "success";
    } else {
        return "The USI or ABN in the selected profile is already in use. Please update the profile and try again.";
    }
}

function generateProfile($profile_id, $community_id, $tag = false)
{
    global $wpdb;

    //BE profiles generation
    if (get_option('generate_via_sqs') == 'yes') {
        ProfileInstance::generateProfiles($profile_id, $tag);
        return;
    }
    $user_id = get_current_user_id();

    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "community_profile_instances WHERE id=%d", $profile_id);
    $profile = $wpdb->get_row($query);
    $profile_content = S3Wrapper::getProfile($profile->token);
    $customDataGeneration = isset($profile_content->CustomProfilesGeneration) ? ($profile_content->CustomProfilesGeneration) : (null);

    //when generating profiles, the selected profile should be tagged as well
    if ($tag) {
        Tag::assignTag($tag, $profile_id);
    }
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

    if (!empty($profile->token_original)) {
        $profile_ref[$profile->token_original] = $profile->token;
    }
    if (is_iterable($customDataGeneration)) {
        foreach ($customDataGeneration as $customData) {
            $identifierPath = str_replace('.', '_', $customData->SourceProfiles->IdentifierPath);
            $identifierValues = $customData->SourceProfiles->Values;
            if (implode(',', $identifierValues) == '?') {
                continue;
            }
            if ($identifierPath != 'Self') {
                $sorting_order = array(
                    'Employer' => 1,
                    'ClearingHouse' => 2
                );
                $rows = $wpdb->get_results(
                    $wpdb->prepare("SELECT  cpi.id, cpi.type, cpi.profile_name , cpi.profile_description, cpi.purpose, cpi.type_id, cpi.type_name, cpi.community_id, cpi.created_date,cpi.creator_id, cpi.token, cpi.token_original, cpi.lookup,cpi.validation_status, cpi.validation_url, cpi.content_length, cpi.profile_role, cpi.is_expanded  FROM wp_community_profile_meta AS cpm
                                    LEFT JOIN wp_community_profile_instances AS cpi ON cpi.id = cpm.profile_id
                                    WHERE cpi.type='harness' AND cpi.community_id = %d
                                    AND cpm.meta_value IN ('" . implode("','", $identifierValues) . "') AND cpm.meta_key = %s ", $community_id, $identifierPath), ARRAY_A);
                foreach ($rows AS $key => $row) {
                    $rows[$key]['order'] = isset($sorting_order[$row['profile_role']]) ? $sorting_order[$row['profile_role']] : 3;
                }
                usort($rows, function ($a, $b) {
                    return $a['order'] - $b['order'];
                });
                if (is_iterable($rows)) {
                    foreach ($rows as $row) {
                        if ($row['content_length'] > get_option('s3_bulk_treshold') || $row['validation_status'] != 'valid') {
                            continue;
                        }
                        $content = S3Wrapper::getProfile($row['token']);

                        $token_original = $row['token'];
                        $row['token_original'] = $token_original;
                        $row['token'] = sha1(time() . $content->Profile->Title . rand(0, 9999) . $row['type_id'] . $community_id);

                        $profile_ref[$token_original] = $row['token'];
                        unset($row['id']);
                        if (isset($customData->Rules) && is_iterable($customData->Rules)) {
                            foreach ($customData->Rules as $rule) {
                                if ($rule->Type == 'Value') {

                                    $replacementPath = str_replace('.', '->', $rule->ReplacementPath);
                                    eval('$replacementValue = $profile_content->' . $replacementPath . ';');
                                    if ($replacementValue) {
                                        $content = json_decode(str_replace($rule->OriginalValue, $replacementValue, json_encode($content)));
                                    }

                                } else if ($rule->Type == 'Reference') {
                                    // Replace $ref values with links of generated profiles
                                    if (is_iterable($content->Employers) || is_iterable($content->Templates)) {
                                        if ($content->Profile->Type == 'Schedule') {
                                            foreach ($content->Templates as $template) {

                                                $refTester = explode('=', $template->Tester->Profile);
                                                $refHarness = explode('=', $template->Harness->Profile);

                                                if (!isset($profile_ref[$refTester[1]])) {
                                                    $query = $wpdb->prepare("SELECT * FROM wp_community_profile_instances WHERE token_original = %s AND creator_id = %d", $refTester[1], $user_id);
                                                    $temp_profile = $wpdb->get_row($query);
                                                    if (!empty($temp_profile)) {
                                                        $profile_ref[$temp_profile->token_original] = $temp_profile->token;
                                                    }
                                                }
                                                if (isset($profile_ref[$refTester[1]])) {
                                                    $template->Tester->Profile = $refTester[0] . '=' . $profile_ref[$refTester[1]];
                                                }

                                                if (!isset($profile_ref[$refHarness[1]])) {
                                                    $query = $wpdb->prepare("SELECT * FROM wp_community_profile_instances WHERE token_original = %s AND creator_id = %d", $refHarness[1], $user_id);
                                                    $temp_profile = $wpdb->get_row($query);
                                                    if (!empty($temp_profile)) {
                                                        $profile_ref[$temp_profile->token_original] = $temp_profile->token;
                                                    }
                                                }
                                                if (isset($profile_ref[$refHarness[1]])) {
                                                    $template->Harness->Profile = $refHarness[0] . '=' . $profile_ref[$refHarness[1]];
                                                }
                                            }
                                        } else {
                                            foreach ($content->Employers as $employer) {

                                                $ref = explode('=', $employer->Profile->{'$ref'});

                                                if (!isset($profile_ref[$ref[1]])) {
                                                    $query = $wpdb->prepare("SELECT * FROM wp_community_profile_instances WHERE token_original = %s AND creator_id = %d", $ref[1], $user_id);
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
                                }
                            }
                        }
                        $content->Profile->Title .= ' (' . $profile->profile_name . ')';
                        $content->Profile->Description = $pre_desc . ' ' . $content->Profile->Description;

                        $profileData = array(
                            'type' => 'tester',
                            'data' => json_encode($content),
                            'type_id' => $row['type_id'],
                            'user_id' => get_current_user_id(),
                            'community_id' => $community_id,
                            'token_original' => $token_original,
                            'token' => $row['token']

                        );
                        ProfileInstance::save($profileData, true, false, $tag);
                    }
                }
            } else // Self
            {
                $query = $wpdb->prepare("SELECT * FROM wp_community_profile_instances WHERE id = %d ", $profile_id);
                $self_instance = $wpdb->get_row($query, ARRAY_A);
                $self_content = S3Wrapper::getProfile($self_instance['token']);

                foreach ($customData->Rules AS $rule) {
                    if ($rule->Type == 'Reference') {
                        if ($content->Profile->Type == 'Schedule') {
                            foreach ($self_content->Templates as $template) {

                                $refTester = explode('=', $template->Tester->Profile);
                                $refHarness = explode('=', $template->Harness->Profile);

                                if (!isset($profile_ref[$refTester[1]])) {
                                    $query = $wpdb->prepare("SELECT * FROM wp_community_profile_instances WHERE token_original = %s AND creator_id = %d", $refTester[1], $user_id);
                                    $temp_profile = $wpdb->get_row($query);
                                    if (!empty($temp_profile)) {
                                        $profile_ref[$temp_profile->token_original] = $temp_profile->token;
                                    }
                                }
                                if (isset($profile_ref[$refTester[1]])) {
                                    $template->Tester->Profile = $refTester[0] . '=' . $profile_ref[$refTester[1]];
                                }

                                if (!isset($profile_ref[$refHarness[1]])) {
                                    $query = $wpdb->prepare("SELECT * FROM wp_community_profile_instances WHERE token_original = %s AND creator_id = %d", $refHarness[1], $user_id);
                                    $temp_profile = $wpdb->get_row($query);
                                    if (!empty($temp_profile)) {
                                        $profile_ref[$temp_profile->token_original] = $temp_profile->token;
                                    }
                                }
                                if (isset($profile_ref[$refHarness[1]])) {
                                    $template->Harness->Profile = $refHarness[0] . '=' . $profile_ref[$refHarness[1]];
                                }
                            }
                        } else {
                            foreach ($self_content->Employers as $employer) {
                                $ref = explode('=', $employer->Profile->{'$ref'});

                                if (!isset($profile_ref[$ref[1]])) {
                                    $query = $wpdb->prepare("SELECT * FROM wp_community_profile_instances WHERE token_original=%s AND creator_id=%d", $ref[1], $user_id);
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
                }

                $profileData = array(
                    'type' => 'tester',
                    'data' => json_encode($self_content),
                    'type_id' => $self_instance['type_id'],
                    'user_id' => get_current_user_id(),
                    'community_id' => $community_id,
                    'instance_id' => $profile_id

                );
                ProfileInstance::save($profileData, true, false, $tag);
            }
        }
    }
}

function wp_db_null_value($query)
{
    return str_ireplace("'NULL'", "NULL", $query);
}

function cp_save_suite_notify_changes()
{
    global $wpdb;

    $user_id = get_current_user_id();
    if (!$user_id)
        return;

    $suiteID = intval($_POST['id']);
    if (!$suiteID)
        return;

    if ($_POST['checked'] == 1)
        update_user_meta($user_id, 'notify_suite_changes' . $suiteID, 1);
    else
        delete_user_meta($user_id, 'notify_suite_changes' . $suiteID);
}

function cp_save_mandatory_response_validation_checking()
{
    global $wpdb;

    $user_id = get_current_user_id();

    $subscriptionId = intval($_POST['id']);
    if (!$subscriptionId)
        return false;

    $wpdb->update('wp_users_subscriptions',
        array('mandatory_response_validation' => intval($_POST['status'])),
        array(
            'id' => $subscriptionId,
            'user_id' => $user_id
        ),
        array('%d'),
        array('%d', '%d')
    );
    return true;
}

function cp_save_limited_error_checking()
{
    global $wpdb;

    $user_id = get_current_user_id();

    $subscriptionId = intval($_POST['id']);
    if (!$subscriptionId)
        return false;

    $wpdb->update('wp_users_subscriptions',
        array('limited_error_checking' => intval($_POST['status'])),
        array(
            'id' => $subscriptionId,
            'user_id' => $user_id
        ),
        array('%d'),
        array('%d', '%d')
    );
    return true;
}

function cp_save_force_checking()
{
    global $wpdb;

    $user_id = get_current_user_id();

    $subscriptionId = intval($_POST['id']);
    if (!$subscriptionId)
        return false;

    $wpdb->update('wp_users_subscriptions',
        array('force_e2e_routing' => intval($_POST['status'])),
        array(
            'id' => $subscriptionId,
            'user_id' => $user_id
        ),
        array('%d'),
        array('%d', '%d')
    );
    return true;
}

function ct_get_user_profile_link($user_id)
{
    $user = get_userdata($user_id);

    return apply_filters('bp_get_member_permalink', bp_core_get_user_domain($user->ID, $user->user_nicename, $user->user_login));
}

