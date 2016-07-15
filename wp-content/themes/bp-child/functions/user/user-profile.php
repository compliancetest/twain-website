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

    if (empty(trim($_POST['organisation_name']))) {
        exit('Name field is required.');
    }

    $query = $wpdb->prepare("SELECT organisation_id FROM " . $wpdb->prefix . "organisations_members WHERE is_admin=1 AND user_id=%d AND organisation_id=%d", get_current_user_id(), $_REQUEST['organisation_id']);
    $user_organisation_id = $wpdb->get_var($query);

    $is_name_used_in_xero = (boolean)$wpdb->get_results($wpdb->prepare("SELECT * FROM wp_organisations WHERE organisation_name = %s AND id != %d", $_POST['organisation_name'], $user_organisation_id));
    if ($is_name_used_in_xero) {
        exit('A record for an organisation with the same Name has already been created.');
    }



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
        $user_organisation_web = htmlspecialchars($_POST['user_organisation_web']);
        $user_organisation_desc = htmlspecialchars($_POST['user_organisation_desc']);

        update_user_meta($user_id, 'user_organisation', $user_organisation);
        update_user_meta($user_id, 'user_organisation_web', $user_organisation_web);
        update_user_meta($user_id, 'user_organisation_desc', $user_organisation_desc);
    }

    $user_id = get_current_user_id();

    $user_org = trim(get_user_meta($user_id, 'user_organisation', true));
    $user_org_web = get_user_meta($user_id, 'user_organisation_web', true);
    $user_org_desc = get_user_meta($user_id, 'user_organisation_desc', true);
    $message_error = $message_success = false;
    //check that name and ABN for user organisation not empty
    if (empty($user_org) || $user_org == '-') {
        $message_error = 'Organisation Name must be populated before an Organisation Record can be created';
    }
    if (false === $message_error) {
        $is_name_used_in_xero = false;
        //check that organisation name not used in Xero
        $is_name_used_in_xero = (boolean)$wpdb->get_results($wpdb->prepare("SELECT * FROM wp_organisations WHERE organisation_name = %s ", $user_org));
        if ($is_name_used_in_xero) {
            $message_error = 'A record for an organisation with the same Name has already been created. Your organisation may already be set up on ' . get_site_title() . '.';
        }
    }
    if (false === $message_error) {
        $organisationClass = new CT_Organisation($_POST['id']);
        $data = array(
            'organisation_name' => $user_org,
            'organisation_description' => $user_org_desc,
            'organisation_website' => $user_org_web,
            'invoice_me' => 0,
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

function james_compare_alias($a, $b)
{
    return strnatcmp($a['alias'], $b['alias']);
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

function ct_get_user_profile_link($user_id)
{
    $user = get_userdata($user_id);

    return apply_filters('bp_get_member_permalink', bp_core_get_user_domain($user->ID, $user->user_nicename, $user->user_login));
}

