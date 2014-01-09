<?php
/**
* Customize Site Default Emails  
*/


//Set Correct From for WP Mail
add_filter('wp_mail_from', 'cp_mail_from', 10, 1);
function cp_mail_from($from)
{
    $from = get_option('support_email');
    return $from;
}
add_filter('wp_mail_from_name', 'cp_mail_from_name', 10, 1);
function cp_mail_from_name($from)
{
    $fromName = get_option('support_name');
    return $fromName;
}

//Set Mail Content Type
add_filter('wp_mail_content_type', 'cp_set_mail_content_type_to_html', 100, 1);
function cp_set_mail_content_type_to_html($content_type)
{       
    return 'text/html';    
}

//Membership Request Send Email Customize
add_filter('groups_notification_new_membership_request_to', 'cp_groups_notification_new_membership_request_to', 100, 1);
function cp_groups_notification_new_membership_request_to($to)
{    
    return cp_get_user_fullname($toData->ID) . " <" . $to . ">";
}
add_filter('groups_notification_new_membership_request_subject', 'cp_groups_notification_new_membership_request_subject', 100, 3);
function cp_groups_notification_new_membership_request_subject($subject, $group, $requesting_user_id)
{
    $subject = get_option('membership_request_received_admin_email_title');
    $user = get_userdata($requesting_user_id);
    $emailData = array(
        '[name]' => $user->first_name .  " " . $user->last_name,
        '[env]' => get_option('env'),
        '[website_url]' => get_site_url(),        
        '[community]' => bp_get_group_name($group)
    );
    $subject = str_replace(array_keys($emailData), array_values($emailData), $subject);
    
    return $subject;
}

add_filter('groups_notification_new_membership_request_message', 'cp_groups_notification_new_membership_request_message', 100, 6);
function cp_groups_notification_new_membership_request_message($message, $group, $requesting_user_id, $profile_link, $group_requests, $settings_link)
{    
    
    $user = get_userdata($requesting_user_id);
    $message = get_option('membership_request_received_admin_email_content');
    $emailData = array(
        '[community]' => bp_get_group_name($group),
        '[community_url]' => bp_get_group_permalink($group),
        '[env]' => get_option('env'),
        '[website_url]' => get_site_url(),
        '[name]' => cp_get_user_fullname($requesting_user_id),
        '[email]' => $user->user_email,
        '[username]' => $user->user_login
    );
    
    $message = str_replace(array_keys($emailData), array_values($emailData), $message);
    $message = apply_filters('the_content', $message);
    return $message;
}

//Membership Request Approved/Rejected Email Customize
add_filter('groups_notification_membership_request_completed_to', 'cp_groups_notification_membership_request_completed_to', 1);
function cp_groups_notification_membership_request_completed_to($to)
{
    $toData = get_user_by_email($to);
    $_SESSION['membership_request_approved_user_id'] = $toData->ID;
    return cp_get_user_fullname($toData->ID) . " <" . $to . ">";
}

add_filter('groups_notification_membership_request_completed_subject', 'cp_groups_notification_membership_request_completed_subject', 100, 2);
function cp_groups_notification_membership_request_completed_subject($subject, $group)
{
    if(strpos($subject, 'accepted') !== false)
        $subject = get_option('membership_request_approved_email_title');
    else
        $subject = get_option('membership_request_rejected_email_title');
    
    $user_id = $_SESSION['membership_request_approved_user_id'];
    
    $emailData = array(
        '[name]' => cp_get_user_fullname($user_id),
        '[env]' => get_option('env'),
        '[website_url]' => get_site_url(),
        '[community]' => bp_get_group_name($group)
    );
    
    $subject = str_replace(array_keys($emailData), array_values($emailData), $subject);
    
    return $subject;
}

add_filter('groups_notification_membership_request_completed_message', 'cp_groups_notification_membership_request_completed_message', 100, 4);
function cp_groups_notification_membership_request_completed_message($message, $group, $group_link, $settings_link)
{
    $user_id = $_SESSION['membership_request_approved_user_id'];
    if(strpos($message, 'accepted') !== false){
        $message = get_option('membership_request_approved_email_content');
        $_SESSION['membership_request_completed_type'] = 'approved';
    }else{
        $message = get_option('membership_request_rejected_email_content');
        $_SESSION['membership_request_completed_type'] = 'rejected';
    }
    $emailData = array(
        '[community]' => bp_get_group_name($group),
        '[community_url]' => $group_link,
        '[name]' => cp_get_user_fullname($user_id),
        '[email]' => $user->user_email,
        '[env]' => get_option('env'),
        '[website_url]' => get_site_url(),
        '[username]' => $user->user_login
    );
    $message = str_replace(array_keys($emailData), array_values($emailData), $message);
    $message = apply_filters('the_content', $message);
    return $message;
}
//Send Approve/Reject Email to Admin
add_action('bp_groups_sent_membership_approved_email', 'cp_groups_sent_membership_approved_email_to_admin', 100, 4);
function cp_groups_sent_membership_approved_email_to_admin($requesting_user_id, $subject, $message, $group_id)
{
    $user = get_userdata($requesting_user_id);
    
    $group = groups_get_group(array('group_id' => $group_id));
    
    $emailData = array(
        '[community]' => bp_get_group_name($group),
        '[community_url]' => bp_get_group_permalink($group),
        '[name]' => $user->first_name . " " . $user->last_name,
        '[email]' => $user->user_email,
        '[env]' => get_option('env'),
        '[website_url]' => get_site_url(),
        '[username]' => $user->user_login
    );
    
    cp_send_email_to_community_admin($group_id, $_SESSION['membership_request_completed_type'] == 'approved' ? 'membership_request_approved_admin' : 'membership_request_rejected_admin', $emailData);
    
}


add_action('groups_leave_group', 'cp_send_leave_community_notification', 100, 2);
function cp_send_leave_community_notification($group_id, $user_id)
{
    $group = groups_get_group( array('group_id'=> $group_id) );
    $user = get_userdata($user_id);
    $emailData = array(
        '[community]' => bp_get_group_name($group),
        '[community_url]' => $group_link,
        '[name]' => cp_get_user_fullname($user_id),
        '[email]' => $user->user_email,
        '[username]' => $user->user_login
    );
    
    $admins = groups_get_group_admins($group_id);
    $to = array();
    foreach($admins as $admin)
    {
        $au = get_userdata($admin->user_id);
        $to[] = array('name' => cp_get_user_fullname($au->ID), 'email' => $au->user_email);        
    }
    cp_send_email($to, 'member_leave_community_admin', $emailData);
    
    return true;
}
