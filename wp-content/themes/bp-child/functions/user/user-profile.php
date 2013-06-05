<?php
/**
* Manage User Profile Section
*/

function cp_user_detail_edit()
{
    global $wpdb;
    
    
    if(!is_user_logged_in())
    {
        //Goto Homepage
        wp_redirect('/');
    }
    
    $user_id = $_POST['user_id'];
    $uname = explode(' ', $_POST['uname']);
    $user_email = email_exists( $_POST['email']);
    $email_regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/'; 
    $newPass = $_POST['new_pass'];
    $confPass = $_POST['conf_pass'];
    $errors = 'no_errors';
    
    //update user name
    if($_POST['uname'] && $_POST['uname']!=''){
        update_user_meta($user_id, 'first_name', $uname[1]);
        update_user_meta($user_id, 'last_name', $uname[0]);
    }
    
    //update user email
    if(isset($_POST['email']) && preg_match($email_regex, $_POST['email'])){
        if($user_email==false){
            wp_update_user(array('ID' => $user_id, 'user_email' => esc_attr( $_POST['email'])));

        }else if($user_email!=$user_id){
           $errors = 'This email address already exists!';
        }
    }else{
        $errors = 'This email address is not valid!';
    }
    
    //update user passwords
    if(isset($confPass) && $confPass!=''){
    
        if($newPass == $confPass){
            $user_pass = get_userdata($_POST['user_id']);
            
            wp_update_user( array ('ID' => $user_id, 'user_pass' => $confPass) ) ;

        }else{
            $errors = 'The passwords do no match!';
        }
    }
 
    echo $errors;
    
    //die('success');
    exit();
}