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
function cp_groups_notification_new_membership_request_subject($subject, $group, $requesting_user_id = false)
{
    if (!$requesting_user_id || !is_integer($requesting_user_id)) {
        $requesting_user_id = get_current_user_id();
    }
    $subject = get_option('membership_request_received_admin_email_title');
    $user = get_userdata($requesting_user_id);
    $user_organisation = get_user_meta ($requesting_user_id, 'user_organisation', true);
    $emailData = array(
        '[name]' => $user->first_name .  " " . $user->last_name,
        '[env]' => get_option('env'),
        '[website_url]' => get_site_url(),        
        '[community]' => bp_get_group_name($group),
        '[organisation]' => $user_organisation
    );
    $subject = str_replace(array_keys($emailData), array_values($emailData), $subject);
    
    return $subject;
}

add_filter('groups_notification_new_membership_request_message', 'cp_groups_notification_new_membership_request_message', 100, 6);
function cp_groups_notification_new_membership_request_message($message, $group, $requesting_user_name, $profile_link, $group_requests, $settings_link)
{
    $user = get_user_by('user_login', $requesting_user_name);
    $requesting_user_id = $user->ID;
    if (!$requesting_user_id || !is_integer($requesting_user_id)) {
        $requesting_user_id = get_current_user_id();
    }
    $user = get_userdata($requesting_user_id);
    $user_organisation = get_user_meta ($requesting_user_id, 'user_organisation', true);
    $message = get_option('membership_request_received_admin_email_content');
    $emailData = array(
        '[community]' => bp_get_group_name($group),
        '[community_url]' => bp_get_group_permalink($group),
        '[env]' => get_option('env'),
        '[website_url]' => get_site_url(),
        '[name]' => cp_get_user_fullname($requesting_user_id),
        '[email]' => $user->user_email,
        '[username]' => $user->user_login,
        '[organisation]' => $user_organisation
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

//Member Promoted Email Customize
function cp_groups_notification_promoted_member( $user_id, $group_id ) {

    if ( doesUserCommunityAdmin( $user_id, $group_id ) ) {
        $promoted_to = __( 'administrator', 'buddypress' );
    } else {
        $promoted_to = __( 'support', 'buddypress' );
    }

    // Post a screen notification first.
    bp_core_add_notification( $group_id, $user_id, 'groups', $type );

    if ( 'no' == bp_get_user_meta( $user_id, 'notification_groups_admin_promotion', true ) )
        return false;

    $group         = groups_get_group( array( 'group_id' => $group_id ) );
    $ud            = bp_core_get_core_userdata($user_id);
    $group_link    = bp_get_group_permalink( $group );
    $settings_slug = function_exists( 'bp_get_settings_slug' ) ? bp_get_settings_slug() : 'settings';
    $settings_link = bp_core_get_user_domain( $user_id ) . $settings_slug . '/notifications/';

    $emailData = array(
        '[name]' => cp_get_user_fullname($user_id),
        '[email]' => $ud->user_email,
        '[website_url]' => get_site_url(),
        '[env]' => get_option('env'),
        '[username]' => $ud->user_login,
        '[community]' => bp_get_group_name($group),
        '[community_url]' => bp_get_group_permalink($group),
        '[settings_link]' => $settings_link,
        '[member_type]' => $promoted_to,
    );


    // Set up and send the message
    $to       = $ud->user_email;
    $subject = get_option('member_promoted_email_title');
    $message = get_option('member_promoted_email_content');

    /* Send the message */
    $to      = apply_filters( 'groups_notification_promoted_member_to', $to );
    $subject = str_replace(array_keys($emailData), array_values($emailData), $subject);

    $message = str_replace(array_keys($emailData), array_values($emailData), $message);
    $message = apply_filters('the_content', $message);

    wp_mail( $to, $subject, $message );


    // Send notifications to community admins
    $admin_subject = get_option('member_promoted_admin_email_title');
    $admin_message = get_option('member_promoted_admin_email_content');

    /* Send the message */
    $admin_subject = str_replace(array_keys($emailData), array_values($emailData), $admin_subject);

    $admin_message = str_replace(array_keys($emailData), array_values($emailData), $admin_message);
    $admin_message = apply_filters('the_content', $admin_message);

    $admins = groups_get_group_admins($group_id);
    foreach($admins as $admin)
    {
        $au = get_userdata($admin->user_id);
        wp_mail($au->user_email, $admin_subject, $admin_message );
    }

    do_action( 'bp_groups_sent_promoted_email', $user_id, $subject, $message, $group_id );
}
remove_action( 'groups_promoted_member', 'groups_notification_promoted_member' );
add_action( 'groups_promoted_member', 'cp_groups_notification_promoted_member', 10, 2 );