<?php
/**
* Customize BBPress
*/

add_action('init', 'compliancetest_forum_actions');

function compliancetest_forum_actions() 
{
    $cpAction = isset($_REQUEST['cp-action']) ? $_REQUEST['cp-action'] : null;
    $forum_id = isset($_REQUEST['forum-id']) ? $_REQUEST['forum-id'] : 0;
    
    if ($cpAction == 'cp_forum_notify_all_active') {
        ct_forum_notify_all_active($forum_id);
        exit;
    } else if ($cpAction == 'cp_forum_notify_all_deactive') {
        ct_forum_notify_all_deactive($forum_id);
        exit;
    }
}

function ct_forum_notify_all_active($forum_id = 0) 
{
    $user_id = bbp_get_current_user_id();
        
    if ( empty( $user_id ) || empty( $forum_id ) ) {
        return false;
    }

    $subscriptions = (array) ct_get_user_subscribed_forum_ids( $user_id );
    if ( !in_array( $forum_id, $subscriptions ) ) {
        $subscriptions[] = $forum_id;
        $subscriptions   = implode( ',', wp_parse_id_list( array_filter( $subscriptions ) ) );
        update_user_option( $user_id, '_ct_forum_subscriptions', $subscriptions );
    }

    return true;
}

function ct_forum_notify_all_deactive($forum_id = 0) 
{
    $user_id = bbp_get_current_user_id();
    
    if ( empty( $user_id ) || empty( $forum_id ) ) {
        return false;
    }

    $subscriptions = (array) ct_get_user_subscribed_forum_ids( $user_id );
    if ( empty( $subscriptions ) ) {
        return false;
    }

    $pos = array_search( $forum_id, $subscriptions );
    if ( false === $pos ) {
        return false;
    }

    array_splice( $subscriptions, $pos, 1 );
    $subscriptions = array_filter( $subscriptions );

    if ( !empty( $subscriptions ) ) {
        $subscriptions = implode( ',', wp_parse_id_list( $subscriptions ) );
        update_user_option( $user_id, '_ct_forum_subscriptions', $subscriptions );
    } else {
        delete_user_option( $user_id, '_ct_forum_subscriptions' );
    }
    
    return true;
}

function ct_get_user_subscribed_forum_ids($user_id) {
    
    if ( empty( $user_id ) )
        return false;

    $subscriptions = get_user_option( '_ct_forum_subscriptions', $user_id );
    $subscriptions = array_filter( wp_parse_id_list( $subscriptions ) );

    return (array) $subscriptions;
}

function ct_get_forum_subscribers( $forum_id = 0 ) {
    $forum_id = bbp_get_forum_id( $forum_id );
    if ( empty( $forum_id ) )
        return;

    global $wpdb;

    $key   = $wpdb->prefix . '_ct_forum_subscriptions';
    $users = wp_cache_get( 'ct_get_forum_subscribers_' . $forum_id, 'bbpress_users' );
    if ( false === $users ) {
        $users = $wpdb->get_col( "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = '{$key}' and FIND_IN_SET('{$forum_id}', meta_value) > 0" );
        wp_cache_set( 'ct_get_forum_subscribers_' . $forum_id, $users, 'bbpress_users' );
    }

    return apply_filters( 'ct_get_forum_subscribers', $users );
}

function ct_is_forum_subscriber( $forum_id = 0 ) {
    $user_id = bbp_get_current_user_id();
    $forum_user_ids = ct_get_forum_subscribers($forum_id);
    
    if (!in_array($user_id, $forum_user_ids)) {
        return false;
    }
    
    return true;
}

//remove_action( 'bbp_new_reply',    'bbp_notify_subscribers', 11);
//remove_action( 'bbp_new_topic',    'bbp_notify_forum_subscribers', 11);

add_action('bbp_new_reply', 'ct_notify_subscribers', 11, 5);
add_action('bbp_new_topic', 'ct_notify_forum_subscribers', 11 ,4);

function ct_notify_forum_subscribers( $topic_id = 0, $forum_id = 0, $anonymous_data = false, $topic_author = 0 ) 
{

    /** Validation ************************************************************/

    $topic_id = bbp_get_topic_id( $topic_id );
    $forum_id = bbp_get_forum_id( $forum_id );

    /** Topic *****************************************************************/

    // Bail if topic is not published
    if ( ! bbp_is_topic_published( $topic_id ) )
        return false;

    /** User ******************************************************************/

    // Get forum subscribers and bail if empty
    $user_ids = ct_get_forum_subscribers( $forum_id, true );
    if ( empty( $user_ids ) )
        return false;

    // Poster name
    $topic_author_name = bbp_get_topic_author_display_name( $topic_id );

    /** Mail ******************************************************************/
    //do_action( 'bbp_pre_notify_forum_subscribers', $topic_id, $forum_id, $user_ids );

    // Remove filters from reply content and topic title to prevent content
    // from being encoded with HTML entities, wrapped in paragraph tags, etc...
    remove_all_filters( 'bbp_get_topic_content' );
    remove_all_filters( 'bbp_get_topic_title'   );

    // Strip tags from text
    $topic_title   = strip_tags( bbp_get_topic_title( $topic_id ) );
    $topic_content = strip_tags( bbp_get_topic_content( $topic_id ) );
    $topic_url     = get_permalink( $topic_id );
    $blog_name     = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
    $community_name = bp_group_name();

    // Loop through users
    foreach ( (array) $user_ids as $user_id ) {

        // Don't send notifications to the person who made the post
        if ( !empty( $topic_author ) && (int) $user_id === (int) $topic_author )
            continue;
            
        $user = get_userdata( $user_id );
        
        $data = array(
            '[name]' => $user->first_name . " " . $user->last_name,
            '[username]' => $user->user_login,
            '[email]' => $user->user_email,
            '[topic_author_name]' => $topic_author_name,
            '[topic_title]' => $topic_title,
            '[topic_content]' => $topic_content,
            '[community]' => $community_name,
            '[topic_url]' => $topic_url
        );

        cp_send_email(array('name' => $user->first_name . " " . $user->last_name, 'email' => $user->user_email), 'forum_new_post', $data);
    }
    
    do_action( 'bbp_post_notify_forum_subscribers', $topic_id, $forum_id, $user_ids );

    return true;
}

function ct_notify_subscribers( $reply_id = 0, $topic_id = 0, $forum_id = 0, $anonymous_data = false, $reply_author = 0 ) 
{

    /** Validation ************************************************************/

    $reply_id = bbp_get_reply_id( $reply_id );
    $topic_id = bbp_get_topic_id( $topic_id );
    $forum_id = bbp_get_forum_id( $forum_id );

    /** Reply *****************************************************************/

    // Bail if reply is not published
    if ( !bbp_is_reply_published( $reply_id ) )
        return false;

    /** Topic *****************************************************************/

    // Bail if topic is not published
    if ( !bbp_is_topic_published( $topic_id ) )
        return false;

    /** User ******************************************************************/

    // Get topic subscribers and bail if empty
    $user_ids = bbp_get_topic_subscribers( $topic_id, true );
    // Get forum subscribers and bail if empty
    $forum_user_ids = ct_get_forum_subscribers( $forum_id, true );
    
    $user_ids = array_unique(array_merge($user_ids, $forum_user_ids), SORT_REGULAR);
    
    if ( empty( $user_ids ) )
        return false;

    // Poster name
    $reply_author_name = bbp_get_reply_author_display_name( $reply_id );

    /** Mail ******************************************************************/

    // Remove filters from reply content and topic title to prevent content
    // from being encoded with HTML entities, wrapped in paragraph tags, etc...
    remove_all_filters( 'bbp_get_reply_content' );
    remove_all_filters( 'bbp_get_topic_title'   );

    // Strip tags from text
    $topic_title   = strip_tags( bbp_get_topic_title( $topic_id ) );
    $reply_content = strip_tags( bbp_get_reply_content( $reply_id ) );
    $reply_url     = bbp_get_reply_url( $reply_id );
    $blog_name     = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
    $community_name = bp_group_name();

    // Loop through users
    foreach ( (array) $user_ids as $user_id ) {

        // Don't send notifications to the person who made the post
        if ( !empty( $reply_author ) && (int) $user_id === (int) $reply_author )
            continue;
            
        $user = get_userdata( $user_id );

        $data = array(
            '[name]' => $user->first_name . " " . $user->last_name,
            '[username]' => $user->user_login,
            '[email]' => $user->user_email,
            '[reply_author_name]' => $reply_author_name,
            '[topic_title]' => $topic_title,
            '[reply_content]' => $reply_content,
            '[community]' => $community_name,
            '[reply_url]' => $reply_url
        );

        cp_send_email(array('name' => $user->first_name . " " . $user->last_name, 'email' => $user->user_email), 'forum_reply_post', $data);

    }

    return true;
}