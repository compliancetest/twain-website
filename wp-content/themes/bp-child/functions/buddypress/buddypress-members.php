<?php
/**
* Manage Buddypress Members
*/


add_action('bp_actions', 'cp_change_avatar');

function cp_change_avatar()
{    
    if( (wp_verify_nonce($_POST['_wpnonce'], 'bp_avatar_upload') || wp_verify_nonce($_POST['_wpnonce'], 'bp_avatar_cropstore')) && is_page('my-profile'))
    {
        
        if(!is_user_logged_in())
        {
            wp_redirect('/');
            exit;
        }
        
        $bp = buddypress();

        if ( ! isset( $bp->avatar_admin ) )
            $bp->avatar_admin = new stdClass();

        $bp->avatar_admin->step = 'upload-image';

        if ( !empty( $_FILES ) ) {

            // Check the nonce
            check_admin_referer( 'bp_avatar_upload' );

            // Pass the file to the avatar upload handler
            if ( bp_core_avatar_handle_upload( $_FILES, 'xprofile_avatar_upload_dir' ) ) {
                $bp->avatar_admin->step = 'crop-image';

                // Make sure we include the jQuery jCrop file for image cropping
                add_action( 'wp_print_scripts', 'bp_core_add_jquery_cropper' );
            }
        }

        // If the image cropping is done, crop the image and save a full/thumb version
        if ( isset( $_POST['avatar-crop-submit'] ) ) {

            // Check the nonce
            check_admin_referer( 'bp_avatar_cropstore' );

            $args = array(
                'item_id'       => bp_loggedin_user_id(),
                'original_file' => $_POST['image_src'],
                'crop_x'        => $_POST['x'],
                'crop_y'        => $_POST['y'],
                'crop_w'        => $_POST['width'],
                'crop_h'        => $_POST['height']
            );

            if ( ! bp_core_avatar_handle_crop( $args ) ) {
                addMessage( __( 'There was a problem cropping your avatar.', 'buddypress' ), 'error' );
            } else {
                addMessage( __( 'Your new avatar was uploaded successfully.', 'buddypress' ) );
//                do_action( 'xprofile_avatar_uploaded' );
            }
            wp_redirect("/my-profile");
            exit;
        }
        
    }
    
}


add_filter('bp_core_avatar_original_max_width', 'cp_core_avatar_original_max_width');
function cp_core_avatar_original_max_width()
{
    return 430;
}

add_action( 'bp_actions', 'cp_xprofile_action_delete_avatar' );
function cp_xprofile_action_delete_avatar() {
    
    if(is_user_logged_in() &&  wp_verify_nonce($_REQUEST['cp-action'], 'delete-avatar'))
    {
        if ( bp_core_delete_existing_avatar( array( 'item_id' => bp_loggedin_user_id() ) ) )
            addMessage( __( 'Your avatar was deleted successfully!', 'buddypress' ) );
        else
            addMessage( __( 'There was a problem deleting that avatar, please try again.', 'buddypress' ), 'error' );

        wp_redirect( wp_get_referer() );    
        exit;
    }
    
}

function cp_bp_get_send_message_button() {
    return apply_filters( 'bp_get_send_message_button',
        bp_get_button( array(
            'id'                => 'private_message',
            'component'         => 'messages',
            'must_be_logged_in' => true,
            'block_self'        => true,
            'wrapper_id'        => 'send-private-message',
            'link_href'         => bp_get_send_private_message_link(),
            'link_title'        => __( 'Send a private message to this user.', 'buddypress' ),
            'link_text'         => __( '<span class="p"></span><span class="t">Contact</span>', 'buddypress' ),
            'link_class'        => 'action-btn mail-btn send-message',
        ) )
    );
}

//Search Users for the messages page
add_filter('bp_friends_autocomplete_list', 'cp_friends_autocomplete_list', 50, 3);
function cp_friends_autocomplete_list($users, $query, $limit)
{
    global $wpdb;
    
    $bp = buddypress();
    
    $user_id = get_current_user_id();
    if(!$user_id)
        return array('friends' => null);
    //Getting Current User Groups
    $groups = groups_get_user_groups($user_id);
    if(!$groups || !$groups['groups'])
        return array('friends' => null);
    
    $results = array('friends' => array(), 'total' => 0);
    
    $users = BP_Core_User::search_users($query);
    
    if( !empty($users['users']) )
    {
        
        foreach($users['users'] as $row)    
        {
            $r = $wpdb->get_var( "SELECT id FROM {$bp->groups->table_name_members} WHERE user_id = " . $row->id . " AND group_id IN ( " . implode(', ', $groups['groups']) . " ) AND is_confirmed = 1 AND is_banned = 0" );
            
            if(!$r)
                continue;
            $results['friends'][] = $row->id;
            $results['total']++;
        }        
    }   
    
    return $results;     
}

//Customize Filter
add_filter('bp_current_action', 'cp_current_action', 50, 1);
function cp_current_action($current_action)
{
    if(!$current_action && (is_page('my-messages') || is_page('inbox')) )
    {
        return  'inbox';
    }
    
    if(!$current_action && is_page('sentbox') )
    {
        return  'sentbox';
    }
    
    if(!$current_action && is_page('compose') )
    {
        return  'compose';
    }
/*    
    if(!$current_action && is_page('view') )
    {
        return  'view';
    }
    */
    return $current_action;
}

add_filter('bp_displayed_user_id', 'cp_displayed_user_id', 50, 1);
function cp_displayed_user_id($id)
{
    if(!$id && is_user_logged_in() && (is_page('my-profile') || is_page('my-messages') || is_page('inbox') || is_page('sentbox') || is_page('compose') || is_page('view')) )
    {
        return  get_current_user_id();
    }
    
    return $id;
}

add_action('bp_actions', 'cp_messages_screen_compose');
//Send Message
function cp_messages_screen_compose() {
    global $bp;

    // Check if the message form has been submitted
    if ( isset( $_POST['compose-message'] ) ) {
        if ( bp_action_variables() ) {
            bp_do_404();
            return;
        }
        // Remove any saved message data from a previous session.
        messages_remove_callback_values();
        
        // Check the nonce
        check_admin_referer( 'messages_send_message' );

        // Check we have what we need
        if ( empty( $_POST['subject'] ) || empty( $_POST['content'] ) ) {
            bp_core_add_message( __( 'There was an error sending that message, please try again', 'buddypress' ), 'error' );
        } else {
            // If this is a notice, send it
            if ( isset( $_POST['send-notice'] ) ) {
                if ( messages_send_notice( $_POST['subject'], $_POST['content'] ) ) {
                    bp_core_add_message( __( 'Notice sent successfully!', 'buddypress' ) );
                    bp_core_redirect( '/my-messages/' );
                } else {
                    bp_core_add_message( __( 'There was an error sending that notice, please try again', 'buddypress' ), 'error' );
                }
            } else {
                // Filter recipients into the format we need - array( 'username/userid', 'username/userid' )
                $autocomplete_recipients = explode( ',', $_POST['send-to-input'] );
                $typed_recipients        = explode( ' ', $_POST['send_to_usernames'] );
                $recipients              = array_merge( (array) $autocomplete_recipients, (array) $typed_recipients );
                $recipients              = apply_filters( 'bp_messages_recipients', $recipients );

                // Send the message
                if ( $thread_id = messages_new_message( array( 'recipients' => $recipients, 'subject' => $_POST['subject'], 'content' => $_POST['content'] ) ) ) {
                    bp_core_add_message( __( 'Message sent successfully!', 'buddypress' ) );
                    bp_core_redirect( '/my-messages/view/' . $thread_id . '/' );
                } else {
                    bp_core_add_message( __( 'There was an error sending that message, please try again', 'buddypress' ), 'error' );
                }
            }
        }
    }
}

add_filter('wp_title', 'cp_members_title', 100, 1);
function cp_members_title($title)
{
    if(is_page('my-messages') || is_page('inbox'))
    {
        return 'Received Messages | ';
    }else if(is_page('sentbox')){
        return 'Sent Messages | ';
    }else if(is_page('compose')){
        return 'New Message | ';
    }else if(is_page('View')){
        return 'Message Details | ';
    }else if(is_page('my-profile')){
        return 'My Profile | ';
    }
    
    return $title;
      
}

add_filter('bp_action_variable', 'cp_action_variable', 100, 2);
function cp_action_variable($action_variable, $position)
{
    if(is_page('view') && !$action_variable)
    {
        $mid = cp_get_message_id_from_uri();                
        return $mid;
        
    }
    
    return $action_variable;
}

function cp_get_message_id_from_uri()
{
    $uri = $_SERVER['REQUEST_URI'];
    if($uri[0] == "/")
        $uri = substr($uri, 1);
    $arr = explode("/", $uri);
    if(isset($arr[0]) && $arr[0] == 'my-messages' && isset($arr[1]) && $arr[1] == 'view' && isset($arr[2]))
    {
        return intval($arr[2]);
    }
    
    return null;
}

add_action('messages_before_delete_thread', 'cp_messages_delete_thread', 100, 1);
function cp_messages_delete_thread($thread_ids)
{
    if ( is_array( $thread_ids ) ) {
        $error = 0;
        for ( $i = 0, $count = count( $thread_ids ); $i < $count; ++$i ) {
            if ( !$status = BP_Messages_Thread::delete( $thread_ids[$i]) ) {
                $error = 1;
            }
        }

        if ( !empty( $error ) ){
            addMessage( __('There was an error deleting messages.', 'buddypress'), 'error' );
        }else{
            addMessage( __('Messages deleted.', 'buddypress') );
            do_action( 'messages_delete_thread', $thread_ids );
        }
        
        bp_core_redirect(bp_current_action() == 'sentbox' ? '/my-messages/sentbox' : '/my-messages');            
    } else {
        if ( !BP_Messages_Thread::delete( $thread_ids ) )
        {
            addMessage( __('There was an error deleting that message.', 'buddypress'), 'error' );
        }else{
            addMessage( __('Message deleted.', 'buddypress') );
            do_action( 'messages_delete_thread', $thread_ids );    
        }
        bp_core_redirect(bp_current_action() == 'sentbox' ? '/my-messages/sentbox' : '/my-messages');            
    }
    
    exit;
}