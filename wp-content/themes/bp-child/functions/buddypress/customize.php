<?php
/**
* Customize Buddy Press
*/
//Add Terms and License Metabox on the Group Admin page
add_action('bp_groups_admin_meta_boxes', 'add_terms_and_license_metabox_to_group');
function add_terms_and_license_metabox_to_group()
{
    add_meta_box( 'bp_group_terms_and_license', _x( 'Terms and Conditions, License Agreement', 'group admin edit screen', 'buddypress' ), 'bp_groups_admin_edit_metabox_terms_and_license', get_current_screen()->id, 'normal', 'core' );    
    add_meta_box( 'bp_group_mailchimp_list', _x( 'MailChimp List', 'group admin edit screen', 'buddypress' ), 'bp_groups_admin_edit_mailchimp_list', get_current_screen()->id, 'side', 'core' );    
    
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
function bp_groups_admin_edit_mailchimp_list($item)
{
    $mailchimp = new Mailchimp(get_mailchimp_api_key(), array('ssl_verifypeer' => false));
    $mailchimp_list = new mailchimp_lists($mailchimp);
    $lists = $mailchimp_list->getList();
    $list_id = groups_get_groupmeta($item->id, 'community_mailchimp_list_id');
    ?>
    <h4>Select a List</h4>        
    <?php
    foreach($lists['data'] as $list)
    {
        ?><p><input type="radio" name="community_mailchimp_list_id" value="<?php echo $list['id']?>" <?php echo $list['id'] == $list_id ? 'checked="checked"' : ''?> /> <label><?php echo $list['name']?></label></p><?php
    }
    ?>
    <p>
        <a href="<?php echo admin_url() ?>?page=admin-actions&amp;admin-action=<?php echo wp_create_nonce('sync-users-to-mailchimp2')?>&id=<?php echo $item->id?>" target="_blank" class="button">Sync members with the selected list</a>
        <br />(You will need to save your change first.)        
    </p>
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
    if(isset($_POST['notification_email_of_changes']))
    {
        groups_update_groupmeta($group_id, 'notification_email_of_changes', $_POST['notification_email_of_changes']);
    }
    
    //Save Mail Chimp ID
    if(isset($_POST['community_mailchimp_list_id']))
    {
        groups_update_groupmeta($group_id, 'community_mailchimp_list_id', $_POST['community_mailchimp_list_id']);
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
        if($button['id'] == 'request_membership' || $button['id'] == 'join_group'){        
            $button['link_class'] .= " popup button button_medium button_red white_txt radius6 join-community-button";    
            $button['link_text'] = "Join Community";
            $button['link_title'] = "Join Community";                        
        }else if($button['id'] == 'leave_group'){
            $button['link_class'] .= " popup button button_medium button_red white_txt radius6 leave-community-button";    
            $button['link_text'] = "Leave Community";
            $button['link_title'] = "Leave Community";
        }else if($button['id'] == 'membership_requested'){
            $button['link_class'] .= " button button_medium status_deprecated white_txt radius6 leave-community-button";    
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
    remove_filter( 'bp_get_group_description',             'bp_groups_filter_kses', 1 );
    
    if ( !function_exists('bp_get_group_current_admin_tab') || 'edit-details' != bp_get_group_current_admin_tab() )
        return false;
    
    if ( bp_is_item_admin() ) {

        $bp = buddypress();

        // If the edit form has been submitted, save the edited details
        if ( isset( $_POST['group-name'] ) ) {
            // Check the nonce
            if ( !check_admin_referer( 'groups_edit_group_details_by_ajax' ) )
                return false;
                
            //Remove Filter to strip tags of description
            remove_filter( 'groups_group_description_before_save', 'force_balance_tags' );
            remove_filter( 'groups_group_description_before_save', 'wp_filter_kses', 1 );
            
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
    if ( !function_exists('bp_get_group_current_admin_tab') || 'group-settings' != bp_get_group_current_admin_tab() )
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
/*add_action('groups_screen_group_admin_avatar', "hook_buddypress_action_messages");
add_action('groups_unbanned_member', "hook_buddypress_action_messages");
add_action('groups_demoted_member', "hook_buddypress_action_messages");
add_action('groups_group_request_managed', "hook_buddypress_action_messages");

function hook_buddypress_action_messages()
{
    global $bp;
    
    if(isset($bp->template_message) && $bp->template_message)
    {
        addMessage(str_replace(array("Group", "Groups"), array("Community", "Communities"), $bp->template_message), $bp->template_message_type == 'error' ? 'error' : 'success');
        //Remove Cookie
        @setcookie('bp-message',      null, time() + 60 * 60 * 24, COOKIEPATH);
        @setcookie('bp-message-type', null,    time() + 60 * 60 * 24, COOKIEPATH);
    }
}*/


//Hook Buddypress message render action
add_action('bp_actions', 'cp_bp_message_setup', 4);

function cp_bp_message_setup()
{    
    //Remove Action 
    remove_action( 'template_notices', 'bp_core_render_message' );
    //Add My action
    add_action( 'template_notices', 'cp_bp_render_message' );
}

function cp_bp_render_message() {
    global $bp;

    if ( !empty( $bp->template_message ) ) :
        $type    = ( 'success' == $bp->template_message_type ) ? 'update' : 'error';
        $content = apply_filters( 'bp_core_render_message_content', $bp->template_message, $type ); ?>
        
        <div id="messages-wrapper" class="bp-template-notice">        
            <p  class="message <?php echo ( 'success' == $bp->template_message_type ) ? 'success' : 'error'?>">
            <?php echo $bp->template_message; ?>
            </p>
        </div>

    <?php
        $bp->template_message = null;
        $bp->template_message_type = null;
        do_action( 'bp_core_render_message' );

    endif;
    
    //Getting BBPress Errors    
    $bbp = bbpress();
}


//Manage Group Members
add_action('init', 'cp_groups_screen_group_admin_manage_members');
function cp_groups_screen_group_admin_manage_members()
{
    if ( !function_exists('bp_get_group_current_admin_tab') || 'manage-members' != bp_get_group_current_admin_tab() )
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
                        
                    else{
                        $success[] = cp_get_user_fullname($userID);                    
                        //Send Email
                        $user = get_userdata($userID);
                        $emailData = array(
                            '[community]' => bp_get_group_name($bp->groups->current_group),
                            '[community_url]' => bp_get_group_permalink($bp->groups->current_group),
                            '[name]' => $user->first_name . " " . $user->last_name,
                            '[email]' => $user->user_email,
                            '[username]' => $user->user_login
                        );
                        
                        cp_send_email(array('name' => $emailData['[name]'], 'email' => $emailData['[email]']), 'remove_member', $emailData);
                        cp_send_email_to_community_admin($bp->groups->current_group->id, 'remove_member_admin', $emailData);
                    }

                    do_action( 'groups_removed_member', $userID, $bp->groups->current_group->id );
                }
                //Set Error Message
                if(count($success) > 0){
                    addMessage('The user' . (count($success) > 1 ? 's ' :' ') . implode(', ', $success) . ' removed successfully.');                    
                }
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
    
    if(function_exists('bp_is_group') && bp_is_group())
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
        $template = get_query_template( 'page', 'page-wiki.php' );
    }else if(bp_docs_is_doc_create() || bp_docs_is_existing_doc()){
        $template = get_query_template( 'page', 'page-wiki.php' );
    }
    
    return $template;
}

//Customize Forum Title
add_filter('bbp_get_forum_title', 'cp_get_forum_title', 10, 2);
function cp_get_forum_title($title, $forum_id)
{
    $post = get_post( $forum_id );
    
    return apply_filters("the_title", $post->post_title);
}

//Add user to subscribe list when user join the group
add_filter('groups_join_group', 'subscribe_user_to_community_list', 10, 2);
function subscribe_user_to_community_list($group_id, $user_id)
{
    $list_id = groups_get_groupmeta($group_id, 'community_mailchimp_list_id');
    //Integrate User to Mailchimp
    $mailChimp = new Mailchimp(get_mailchimp_api_key(), array('ssl_verifypeer' => false));
    $mailChimpList = new Mailchimp_Lists($mailChimp);
    $user = get_userdata($user_id);
    try{
        $result = $mailChimpList->subscribe($list_id, array('email' => $user->user_email), array('FNAME' => $user->first_name, 'LNAME' => $user->last_name), "html", false);        
    }catch(Exception $e){
        
    }
    
}

//Add user to subscribe list when user join the group
add_filter('groups_membership_accepted', 'subscribe_user_to_community_list1', 10, 2);
function subscribe_user_to_community_list1($user_id, $group_id)
{
    $list_id = groups_get_groupmeta($group_id, 'community_mailchimp_list_id');
    //Integrate User to Mailchimp
    $mailChimp = new Mailchimp(get_mailchimp_api_key(), array('ssl_verifypeer' => false));
    $mailChimpList = new Mailchimp_Lists($mailChimp);
    $user = get_userdata($user_id);
    try{
        $result = $mailChimpList->subscribe($list_id, array('email' => $user->user_email), array('FNAME' => $user->first_name, 'LNAME' => $user->last_name), 'html', false);
    }catch(Exception $e){
        
    }
    
}

//remove user to subscribe list when user join the group
add_filter('groups_leave_group', 'unsubscribe_user_to_community_list', 10, 2);
add_filter('groups_remove_member', 'unsubscribe_user_to_community_list', 10, 2);
function unsubscribe_user_to_community_list($group_id, $user_id)
{
    $list_id = groups_get_groupmeta($group_id, 'community_mailchimp_list_id');
    //Integrate User to Mailchimp
    $mailChimp = new Mailchimp(get_mailchimp_api_key(), array('ssl_verifypeer' => false));
    $mailChimpList = new Mailchimp_Lists($mailChimp);
    $user = get_userdata($user_id);
    try{
        $result = $mailChimpList->unsubscribe($list_id, array('email' => $user->user_email), true);
    }catch(Exception $e){
        
    }
    
}


//Getting Community Test Suite Counts
function getCommunityTestSuites($community_id)
{
    global $wpdb;
    
    $args = array(
        'post_type' => 'test-suite', 
        'posts_per_page' => -1,
        'tax_query' => array('relation' => 'and'),
        'meta_query' => array(
            array(
                'key' => 'community_id',
                'value' => $community_id,
                'compare' => '='
            )
        )
    );
    
    if(!groups_is_user_admin(get_current_user_id(), $community_id)){
        $args['meta_query'][] =  array(
                    'key' => 'hide_suite',
                    'value' => 0,
                    'compare' => '='
                );
    }
    
    $testsuites = get_posts( $args );
    
    return $testsuites;
}


//Getting Community Products&Service Counts
function getCommunityProductsCount($community_id)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT 
                              COUNT(DISTINCT (p.product_id)) 
                            FROM
                              wp_test_plans AS p 
                              LEFT JOIN wp_postmeta AS m 
                                ON p.suite_id = m.post_id 
                            WHERE m.meta_key = 'community_id' 
                              AND m.meta_value = %d 
                            GROUP BY m.meta_value ", $community_id);
    
    $count = $wpdb->get_var($query);
    
    return !$count ? 0 : $count;
}

// Get Dashboard Pages
function getDashboardPages($type = 'page')
{
    global $current_user;
    get_currentuserinfo();
    
    $pages = array();
    
    $menu = wp_get_nav_menu_object('dashboard_menu');
    $menu_items = wp_get_nav_menu_items($menu->term_id);
    
    foreach ((array)$menu_items as $key => $menu_item ) {
        $title = $menu_item->title;
        $url = $menu_item->url;
        $class = implode(' ', $menu_item->classes);
        
        $item = array('title' => $title, 'url' => $url, 'class' => $class);
        
        if ($class == 'menu-communities') {
            
            $item['subpages'] = array();
            
            $groups = groups_get_user_groups($current_user->ID);
            if($groups['total'] > 0)
            {
                foreach($groups['groups'] as $gID)
                {
                    $group = groups_get_group(array('group_id' => $gID));
                    $member = getGroupMemberDetail($gID, $current_user->ID);
                    
                    $community_url = bp_get_group_permalink($group);
                    
                    $item1 = array('title' => bp_get_group_name($group), 'url' => $community_url);
                    $item2 = array();
                    $item2[] = array('title' => 'Test Suites', 'url' => $community_url);
                    $item2[] = array('title' => 'Test Data', 'url' => $community_url.'testdata');
                    $item2[] = array('title' => 'Articles', 'url' => $community_url.'wiki');
                    $item2[] = array('title' => 'Forum', 'url' => $community_url.'forum');
                    $item2[] = array('title' => 'Downloads', 'url' => $community_url.'downloads');
                    $item2[] = array('title' => 'Reports', 'url' => $community_url.'reports');
                    if(groups_is_user_admin(get_current_user_id(), $gID)) {
                        $item2[] = array('title' => 'Admin', 'url' => $community_url.'admin');
                    }
                    
                    $testsuites = getCommunityTestSuites($gID); 
                    if (count($testsuites) > 0) {
                        $item2[0]['subpages'] = array();
                        foreach ($testsuites as $row) {
                            $item2[0]['subpages'][] = array('title' => apply_filters('the_title', $row->post_title), 'url' => get_permalink($row->ID));
                        }
                    }
                    $item1['subpages'] = $item2;
                    
                    $item['subpages'][] = $item1;
                }
            }
            if ($type == 'menu') {
                $item['subpages'][] = array('title' => '+ Add', 'url' => home_url().'/communities');
            }
        } elseif ($class == 'menu-test-suites') {
            
            $item['subpages'] = array();
            $subscriptions =  getUserSubscriptions(null, true);           
            
            if(count($subscriptions) > 0) {
                foreach($subscriptions as $row) {     
                    $item['subpages'][] = array('title' => $row->suite_title, 'url' => get_permalink($row->suite_id));
                }
            }
            
            if ($type == 'menu') {
                $item['subpages'][] = array('title' => '+ Add', 'url' => home_url().'/test-suites');
            }
        } elseif ($class == 'menu-organisation') {
            if (!is_organisation_admin()) {
                continue;
            } else {
                $item['subpages'] = array();
                $item['subpages'][] = array('title' => 'Users', 'url' => home_url().'/my-organisation/users');
                $item['subpages'][] = array('title' => 'Subscriptions', 'url' => home_url().'/my-organisation/test-suites');
                $item['subpages'][] = array('title' => 'Profile', 'url' => $item['url']);
            }
        }
                
        $pages[] = $item;
    }
    
    return $pages;
}

// Get Dashboard Menu HTML
function getDashboardMenuHTML($pages = array(), $menu_class = '', $path = '', $level = 0)
{
    global $wpdb;
    if (!is_array($pages) || count($pages) == 0) {
        return '';
    }
    
    $html = '<ul class="dropdown-menu '.(($level==0)?($menu_class):('')).'">';
    
    foreach ($pages as $page) {
        if( $page['title'] == 'Agreements' && ! check_user_has_make_agreement_priv() ){
            continue;
        }
        $class = isset($page['class']) ? ($page['class']) : ('');
        $url = $page['url'];
        $title = $page['title'];
        $li_class = ($title == '+ Add') ? ('action-link') : ('');
        
        $path1 = ($path == '') ? ($title) : ($path . ' > ' . $title);
        
        $html .= '<li class="'.$li_class.'"><a class="'.$class.'" href="'.$url.'" data-title="'.$path1.'">'.$title.'</a>';
        
        if (isset($page['subpages']) && count($page['subpages']) > 0) {
            $html .= getDashboardMenuHTML($page['subpages'], '', $path1, $level + 1);
        }
        
        $html .= '</li>';
    }
    
    $html .= '</ul>';
    
    return $html;
}

function cp_docs_current_user_can($user_can, $action) {
    
    $group_id = bp_get_group_id();
    
    if ( ! $group_id ) {
        $doc_id = is_single() ? get_the_ID() : 0;
        $group_id = bp_docs_get_associated_group_id( $doc_id );
    }
    
    if ($user_can) {
        if ($action == 'edit') {
            $user_can = groups_is_user_admin(get_current_user_id(), $group_id);
        }
    }
    return $user_can;
}
add_filter('bp_docs_current_user_can', 'cp_docs_current_user_can', 10, 2);

function cp_directory_groups_search_form() {

    $default_search_value = bp_get_search_default_text( 'groups' );
    $search_value         = !empty( $_REQUEST['s'] ) ? stripslashes( $_REQUEST['s'] ) : $default_search_value; ?>

    <form action="" method="get" id="search-groups-form">
        <label><input type="text" name="s" id="groups_search" <?php if (!empty( $_REQUEST['s'] )){ ?>value="<?php echo esc_attr( $search_value ); ?>" <?php } else { ?> placeholder="<?php echo esc_attr( $search_value ) ?>" <?php } ?> /></label>
        <input type="submit" id="groups_search_submit" name="groups_search_submit" value="<?php _e( 'Search', 'buddypress' ) ?>" />
    </form>
    <script>
        // Search autofocus
        jQuery(document).ready(function() {
            var inputSearch = jQuery('#groups_search');
            var searchTerm = inputSearch.val();
            if (searchTerm != ''){
                inputSearch.focus().val('').val(searchTerm);
            } else {
                inputSearch.focus();
            }
        });
    </script>

<?php
}

//Make default Community Invitations only for admins
add_filter('bp_group_invite_status_fallback', 'cp_group_invite_status_fallback');
function cp_group_invite_status_fallback(){
    return 'admins';
}
add_action('bp_init', 'cp_check_group_wiki_permission');
//Wiki Page Permission Check
function cp_check_group_wiki_permission()
{
    global $bp;
    
    if(bp_is_group() && bp_current_action() == 'wiki')
    {
        if($bp->bp_docs->current_view != 'list') 
        {
            $group = groups_get_group("group_id=" . bp_get_current_group_id());
            //Redirect to Wiki Page.
            //All wiki detail pages from Community was disabled
            wp_redirect(bp_get_group_permalink($group) . "wiki");
            exit;    
        }
    }
    
}

add_filter('bp_docs_is_doc_create', 'cp_check_docs_create', 100, 1);
function cp_check_docs_create($is_doc_create)
{
    if($is_doc_create)
    {
        //Check group param
        $group_slug = isset($_REQUEST['group']) ? $_REQUEST['group'] : null;
        $group_id = groups_get_id($group_slug);
        if(!$group_id)
        {
            //Redirect Homepage
            wp_redirect('/');
            exit;
        }
        
        $group = groups_get_group('group_id=' . $group_id);
        
        //Check if the user can create article
        $wiki_settings = groups_get_groupmeta( $group_id, 'bp-docs' );        
        
        $group_wiki_enable = empty( $wiki_settings['group-enable'] ) ? false : true;
        $can_create_wiki = empty( $wiki_settings['can-create'] ) ? false : $wiki_settings['can-create'];
        
        if(!$group_wiki_enable)
        {            
            //Redirect Homepage
            wp_redirect(bp_get_group_permalink($group));
            exit;
        }
        
        $user_id = get_current_user_id();
        
        if(!can_create_community_article($group_id, $user_id))
        {
            addMessage(__( 'You are not allowed to create Docs.', 'bp-docs' ), "error");
            //Redirect Homepage
            wp_redirect(bp_get_group_permalink($group));
            exit;
        }
        
    }
    
    return $is_doc_create;
}

function groups_screen_group_admin_generate_json() {
    
    if ( 'group-generate-json' != bp_get_group_current_admin_tab() )
        return false;
        
    $folders = glob(ABSPATH . 'wp-content/uploads/json_zips/*');
    foreach ($folders as $folder) {
        if (file_exists($folder)) {
            $between = date_diff(date_create(date('Y-m-d H:i:s')), date_create(date('Y-m-d H:i:s', filemtime($folder))))->format('%a');
            if ($between >= 2) {
                array_map('unlink', glob($folder . '/*.*'));
                rmdir($folder);
            }
        }
    }
        
    require_once( ABSPATH . 'wp-content/themes/bp-child/functions/generate-json/JsonGenerator.php' );

    if (!empty($_FILES) && isset($_POST['upload'])) {
        $jg = new JsonGenerator( $_FILES['profile_excel_file']['tmp_name'] );
        $zip_link = $jg->checkSheets();
        if (!empty($zip_link)) {
            $_SESSION['admin_json_zip_link'] = $zip_link;
        }
    }

    bp_core_load_template( apply_filters( 'groups_template_group_admin_generate_json', 'groups/single/home' ) );
}
add_action('bp_screens', 'groups_screen_group_admin_generate_json');

function groups_screen_group_admin_upload_fvs() {
    
    if ( 'group-upload-fvs' != bp_get_group_current_admin_tab() )
        return false;
            
    require_once( ABSPATH . 'wp-content/themes/bp-child/functions/buddypress/generate-fvs.php' );
    
    $fvs_generator = new FvsGenerator();
    
    $fvs_generator->saveToDatabase();
    
    bp_core_load_template( apply_filters( 'groups_template_group_admin_upload_fvs', 'groups/single/home' ) );
}
add_action('bp_screens', 'groups_screen_group_admin_upload_fvs');

function groups_screen_group_admin_generate_fvs() {
    
    if ( 'group-generate-fvs' != bp_get_group_current_admin_tab() )
        return false;
        
    require_once( ABSPATH . 'wp-content/themes/bp-child/functions/buddypress/generate-fvs.php' );
    
    $fvs_generator = new FvsGenerator();
    
    $fvs_generator->init();
    $fvs_generator->process();
    
    $fvs_generator->download();
    
    exit;
    
}
add_action('init', 'groups_screen_group_admin_generate_fvs');