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
        }
    }
    
    echo 'success';
    
    exit();
}

//Save User Payment Information
function cp_user_payment_edit()
{
    global $wpdb, $current_user;   
    
    //Goto Homepage
    if(!is_user_logged_in())
        wp_redirect('/');
    
    $user_id = $current_user->ID;
    
    $card_number = str_replace(' ', '', $_POST['card_number']);
    $name_on_card = trim($_POST['name_on_card']);
    $card_expiry = trim($_POST['card_expiry']);
    $card_cvc = trim($_POST['card_cvc']);
    
    $errors = 'no_errors';
    
    $check = check_cc($card_number);//4533345657653245
    
    //Card Number
    if($card_number != '')
    {
        if(!check_cc($card_number))
        {
            echo 'Credit card number is not valid!';
            exit;
        }else{ //Update Card Number
            update_user_meta( $user_id, 'card_number', $card_number);
        }
    }
    
    //Card Name
    if($name_on_card != '')
    {
        update_user_meta( $user_id, 'name_on_card', $name_on_card);
    }
    
    if(!$card_expiry){
        echo 'Please specify your card expiry date!';
        exit;
    }else{
        $card_expiry_arr = explode('/', $card_expiry);
        if($card_expiry_arr[0] > 12){
            echo 'Your expiry date is incorrect!';
            exit;
        }else if(check_exp_date($card_expiry_arr[0], $card_expiry_arr[1])){
            update_user_meta( $user_id, 'card_expiry', $card_expiry); 
        }else{
            echo 'Your card has expired or your expiry date is incorrect!';
            exit;
        }
    }
    
    
    if($card_cvc!='' && (strlen($card_cvc)==3 || strlen($card_cvc)==4)){
        update_user_meta( $user_id, 'card_cvc', $card_cvc);
    }else{
        echo 'Your CVC code is incorrect';
        exit;
    }
    
    echo 'success';
    
    exit();
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