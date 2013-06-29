<?php
/**
* Customize Buddy Press
*/
//Add Terms and License Metabox on the Group Admin page
add_action('bp_groups_admin_meta_boxes', 'add_terms_and_license_metabox_to_group');
function add_terms_and_license_metabox_to_group()
{
    add_meta_box( 'bp_group_terms_and_license', _x( 'Terms and Conditions, License Agreement', 'group admin edit screen', 'buddypress' ), 'bp_groups_admin_edit_metabox_terms_and_license', get_current_screen()->id, 'normal', 'core' );    
    
}
function bp_groups_admin_edit_metabox_terms_and_license($item)
{
    $terms = groups_get_groupmeta($item->id, 'terms_and_conditions');
    $license = groups_get_groupmeta($item->id, 'license_agreements');
    $obligation = groups_get_groupmeta($item->id, 'obligation_for_claim');
    ?>
    <h4>Terms And Conditions</h4>
    <p><textarea cols="30" rows="5" name="terms_and_conditions" style="width: 100%;" id="terms_and_conditions"><?php echo $terms?></textarea></p>
    <br />
    <h4>License Agreements</h4>
    <p><textarea cols="30" rows="5" name="license_agreements" style="width: 100%;" id="license_agreements"><?php echo $license?></textarea></p>
    <h4>Member Obligations For claim</h4>
    <p><textarea cols="30" rows="5" name="obligation_for_claim" style="width: 100%;" id="obligation_for_claim"><?php echo $obligation?></textarea></p>
    
    <?php
}

//Save Terms and License Agreements
add_action('bp_group_admin_edit_after', 'save_group_terms_and_license');
function save_group_terms_and_license($group_id)
{
    if(isset($_POST['terms_and_conditions']))
    {
        groups_update_groupmeta($group_id, 'terms_and_conditions', $_POST['terms_and_conditions']);
    }
    if(isset($_POST['license_agreements']))
    {
        groups_update_groupmeta($group_id, 'license_agreements', $_POST['license_agreements']);
    }
    if(isset($_POST['obligation_for_claim']))
    {
        groups_update_groupmeta($group_id, 'obligation_for_claim', $_POST['obligation_for_claim']);
    }
    
    
}

function flushMessages($class = '')
{
    if(isset($_SESSION[MESSAGE_KEY]))
    {
        echo '<div id="messages-wrapper"  class="' . $class . '">';
        foreach($_SESSION[MESSAGE_KEY] as $row)
        {
            echo '<div class="message ' . $row['type'] . '">' . $row['message'] . "</div>";
        }
        echo '</div>';
        unset($_SESSION[MESSAGE_KEY]);
    }
}

//Show Result Messages
add_action('bp_before_container', 'flushMessages');

//Customize Join Group Button
add_filter('bp_get_group_join_button', 'cp_bp_get_group_join_button_filter');

function cp_bp_get_group_join_button_filter($button)
{    
    if(is_user_logged_in())
    {
        if($button['id'] == 'request_membership'){        
            $button['link_class'] .= " popup button button_medium button_red white_txt radius6";    
            $button['link_text'] = "Join Community";
            $button['link_title'] = "Join Community";            
        }else if($button['id'] == 'leave_group'){
            $button['link_class'] .= " popup button button_medium button_red white_txt radius6";    
            $button['link_text'] = "Leave Community";
            $button['link_title'] = "Leave Community";
        }else if($button['id'] == 'membership_requested'){
            $button['link_class'] .= " button button_medium status_btn_on_hold white_txt radius6";    
            $button['link_text'] = "Request Sent";
            $button['link_title'] = "Request Sent";
            $button['link_href'] = "#";
        }
    }else{
        
        $button['link_class'] .= " popup button button_medium button_red white_txt register radius6";    
        $button['link_text'] = "Join Community";
        $button['link_title'] = "Join Community";
        $button['link_rel'] = "custom-popup";
        
    }
    
    return $button;
}

/********************************* Group Admin Sections ********************************************/
//Group Name And Description
add_action( 'init', 'groups_screen_group_admin_edit_details_by_ajax' );
function groups_screen_group_admin_edit_details_by_ajax()
{    
    
    if ( 'edit-details' != bp_get_group_current_admin_tab() )
        return false;
    
    if ( bp_is_item_admin() ) {

        $bp = buddypress();

        // If the edit form has been submitted, save the edited details
        if ( isset( $_POST['group-name'] ) ) {
            // Check the nonce
            if ( !check_admin_referer( 'groups_edit_group_details_by_ajax' ) )
                return false;
                
            $result = groups_edit_base_group_details( $_POST['group-id'], $_POST['group-name'], $_POST['group-desc'], (int) $_POST['group-notify-members'] );
            if ( !$result ) {
                $message =  __( 'There was an error updating group details, please try again.', 'buddypress' );
            } else {
                $message =  __( 'Group details were successfully updated.', 'buddypress' );
            }
            
            echo json_encode(array('result' => $result ? 'success' : 'error', 'message' => $message));
            
            do_action( 'groups_group_details_edited', $bp->groups->current_group->id );            
            do_action( 'bp_group_admin_edit_after', $bp->groups->current_group->id );            
            exit;
        }
        
    }
    
}

//Group Privacy Settings
//Group Name And Description
add_action( 'init', 'groups_edit_group_settings_by_ajax' );
function groups_edit_group_settings_by_ajax()
{   
    if ( 'group-settings' != bp_get_group_current_admin_tab() )
        return false;
    
    if ( bp_is_item_admin() ) {

        $bp = buddypress();
        // If the edit form has been submitted, save the edited details
        if ( isset( $_POST['group-status'] ) || $_POST['group-invite-status']) {
            // Check the nonce
            if ( !check_admin_referer( 'groups_edit_group_settings_by_ajax' ) )
                return false;
            
            // Checked against a whitelist for security
            $allowed_status = apply_filters( 'groups_allowed_status', array( 'public', 'private', 'hidden' ) );
            $status         = ( in_array( $_POST['group-status'], (array) $allowed_status ) ) ? $_POST['group-status'] : 'public';

            $enable_forum = 1;
            
            // Checked against a whitelist for security
            $allowed_invite_status = apply_filters( 'groups_allowed_invite_status', array( 'members', 'mods', 'admins' ) );
            $invite_status           = in_array( $_POST['group-invite-status'], (array) $allowed_invite_status ) ? $_POST['group-invite-status'] : 'members';
            
            $result = groups_edit_group_settings( $_POST['group-id'], $enable_forum, $status, $invite_status );
            
            if (!$result) {
                $message = __( 'There was an error updating group settings, please try again.', 'buddypress' );
            } else {
                $message = __( 'Group settings were successfully updated.', 'buddypress' );
            }

            do_action( 'groups_group_settings_edited', $bp->groups->current_group->id );
            
            echo json_encode(array('result' => $result ? 'success' : 'error', 'message' => $message));
            
            exit;
        }
        
    }
    
}


//Change Group Avatar Upload Messages
add_filter('bp_core_avatar_original_max_width', 'bp_core_avatar_original_max_width_for_cp');
function bp_core_avatar_original_max_width_for_cp()
{
    return 395;
}

//Hook Buddypress action messages
add_action('groups_screen_group_admin_avatar', "hook_buddypress_action_messages");
add_action('groups_unbanned_member', "hook_buddypress_action_messages");
add_action('groups_demoted_member', "hook_buddypress_action_messages");
add_action('groups_group_request_managed', "hook_buddypress_action_messages");
function hook_buddypress_action_messages()
{
    global $bp;
    
    if(isset($bp->template_message) && $bp->template_message)
    {
        addMessage($bp->template_message, $bp->template_message_type == 'error' ? 'error' : 'success');
        //Remove Cookie
        @setcookie('bp-message',      null, time() + 60 * 60 * 24, COOKIEPATH);
        @setcookie('bp-message-type', null,    time() + 60 * 60 * 24, COOKIEPATH);
    }
}

//Manage Group Members
add_action('init', 'cp_groups_screen_group_admin_manage_members');
function cp_groups_screen_group_admin_manage_members()
{
    if ( 'manage-members' != bp_get_group_current_admin_tab() )
        return false;
    
    if ( bp_is_item_admin() ) {
                
        $bp = buddypress();
        // If the edit form has been submitted, save the edited details
        if ( isset( $_POST['action'] ) && in_array($_POST['action'], array('ban', 'promote_to_mod', 'promote_to_admin', 'remove_from_group')) && isset( $_POST['id'] )) {
            // Check the nonce
            if ( !check_admin_referer( 'groups_manage_group_members' ) )
                return false;
                
            $userIDs = $_POST['id'];
            if(!$userIDs)
                return false;
            if(!is_array($userIDs))
                $userIDs = array($userIDs);
            //Ban
            if($_POST['action'] == 'ban')
            {
                $success = array();
                $failure = array();
                foreach($userIDs as $userID)
                {
                    if ( !groups_ban_member( $userID, $bp->groups->current_group->id ) )
                        $failure[] = cp_get_user_fullname($userID);
                        
                    else
                        $success[] = cp_get_user_fullname($userID);                    

                    do_action( 'groups_banned_member', $userID, $bp->groups->current_group->id );
                }
                //Set Error Message
                if(count($success) > 0)
                    addMessage('The user' . (count($success) > 1 ? 's ' :' ') . implode(', ', $success) . ' banned successfully.');
                if(count($failure) > 0)
                    addMessage('There was an error when banning the user' . (count($success) > 1 ? 's ' :' ') . implode(', ', $success), 'error');                
                
                bp_core_redirect( bp_get_group_permalink( groups_get_current_group() ) . 'admin' );
                
            }
            
            //Promote to Mod
            if($_POST['action'] == 'promote_to_mod')
            {
                $success = array();
                $failure = array();
                foreach($userIDs as $userID)
                {
                    if ( !groups_promote_member( $userID, $bp->groups->current_group->id, 'mod' ) )
                        $failure[] = cp_get_user_fullname($userID);
                        
                    else
                        $success[] = cp_get_user_fullname($userID);                    

                    do_action( 'groups_promoted_member', $userID, $bp->groups->current_group->id );
                }
                //Set Error Message
                if(count($success) > 0)
                    addMessage('The user' . (count($success) > 1 ? 's ' :' ') . implode(', ', $success) . ' promoted successfully.');
                if(count($failure) > 0)
                    addMessage('There was an error when promoting the user' . (count($success) > 1 ? 's ' :' ') . implode(', ', $success), 'error');                
                
                bp_core_redirect( bp_get_group_permalink( groups_get_current_group() ) . 'admin' );
            }
            //Promote to Admin
            if($_POST['action'] == 'promote_to_admin')
            {
                $success = array();
                $failure = array();
                foreach($userIDs as $userID)
                {
                    if ( !groups_promote_member( $userID, $bp->groups->current_group->id, 'admin' ) )
                        $failure[] = cp_get_user_fullname($userID);
                        
                    else
                        $success[] = cp_get_user_fullname($userID);                    

                    do_action( 'groups_promoted_member', $userID, $bp->groups->current_group->id );
                }
                //Set Error Message
                if(count($success) > 0)
                    addMessage('The user' . (count($success) > 1 ? 's ' :' ') . implode(', ', $success) . ' promoted successfully.');
                if(count($failure) > 0)
                    addMessage('There was an error when promoting the user' . (count($success) > 1 ? 's ' :' ') . implode(', ', $success), 'error');                
                
                bp_core_redirect( bp_get_group_permalink( groups_get_current_group() ) . 'admin' );
            }
            
            //Remove From Group
            if($_POST['action'] == 'remove_from_group')
            {
                $success = array();
                $failure = array();
                foreach($userIDs as $userID)
                {
                    if ( !groups_remove_member( $userID, $bp->groups->current_group->id ) )
                        $failure[] = cp_get_user_fullname($userID);
                        
                    else
                        $success[] = cp_get_user_fullname($userID);                    

                    do_action( 'groups_banned_member', $userID, $bp->groups->current_group->id );
                }
                //Set Error Message
                if(count($success) > 0)
                    addMessage('The user' . (count($success) > 1 ? 's ' :' ') . implode(', ', $success) . ' removed successfully.');
                if(count($failure) > 0)
                    addMessage('There was an error removing the user' . (count($success) > 1 ? 's ' :' ') . implode(', ', $success) . " from the group", 'error');                
                
                bp_core_redirect( bp_get_group_permalink( groups_get_current_group() ) . 'admin' );
                
            }
        }
        
    }
}

//Automatically Enable Group Forum
add_action('init', 'cp_auto_enable_group_forum');
function cp_auto_enable_group_forum()
{
    global $wpdb;
    
    if(bp_is_group())
    {
        $group = groups_get_current_group();
        $query = 'UPDATE ' . $wpdb->prefix . 'bp_groups SET enable_forum=1 WHERE id=' . $group->id;
        $wpdb->query($query);
        $group->enable_forum = 1;
        
        return true;                
    }
}

//Customize Template
add_filter('template_include', 'cp_template_customize', 10, 1);
function cp_template_customize($template)
{
    global $post;
    
    if((is_page() || is_single()) && get_post_type() == 'bp_doc') //If wiki page
    {
        $template = get_query_template( 'page', 'page-noheader.php' );
    }else if(bp_docs_is_doc_create() || bp_docs_is_existing_doc()){
        $template = get_query_template( 'page', 'page-noheader.php' );
    }
    
    return $template;
}