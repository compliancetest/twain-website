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