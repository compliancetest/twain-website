<?php
 
add_filter('bbp_has_search_results', 'group_search_filter', 2, 10);

function group_search_filter($have_posts, $query)
{
    if(bp_is_group())
    {   
        $group = groups_get_current_group();
        //Getting Current Group Forum ID
        $forumID = groups_get_groupmeta($group->id, 'forum_id');        
        if($forumID && is_array($forumID))
            $forumID = $forumID[0];
        //Create New Query adding the post parent id        
        $query->query['post_parent'] = $forumID;        
        
        $newQuery = new WP_Query($query->query);
        
        return $newQuery->have_posts();
    }
    return $have_posts;
}

//Customize bbp_has_topics function to add parent object
function cp_bbp_has_topics()
{
    if(is_user_logged_in())
    {
        $user = wp_get_current_user();
        //Getting the groups that this user has been joined
        $query = "SELECT group_id FROM " . $wpdb->prefix ."bp_groups_members WHERE user_id=" . $user->ID . " AND is_confirmed=1 AND is_banned=0";
        $rows = $wpdb->get_results($query);
        $ids = array();
        foreach($rows as $r)
            $ids[] = $r->group_id;
        
        if($ids)
        {
            bbp_has_topics(array('post_parent__in' => $ids));            
            return;
        }
        
    }
    
    bbp_has_topics();
}


function getCurrentUserGroupForumIDs()
{
    global $wpdb;
    
    if(is_user_logged_in())
    {
        $user = wp_get_current_user();
        //Getting the groups that this user has been joined
        $query = "SELECT group_id FROM " . $wpdb->prefix ."bp_groups_members WHERE user_id=" . $user->ID . " AND is_confirmed=1 AND is_banned=0";
        $rows = $wpdb->get_results($query);
        $ids = array();
        foreach($rows as $r)
        {
            $forumID = groups_get_groupmeta($r->group_id, 'forum_id');        
            if($forumID && is_array($forumID))
                $forumID = $forumID[0];
            if($forumID)
                $ids[] = $forumID;
        }
        
        if($ids)
        {
            return $ids;
        }
        
    }
    
    return null;
}
//Show Forum Topic Tag Page
//add_action('bbp_after_group_forum_display', 'group_forum_topic_tag_page');
//function group_forum_topic_tag_page()
//{
//    $offset = 0;
//    
//    $bbp = bbpress();

//    /** Query Resets ******************************************************/

    // Forum data
//    $forum_action = bp_action_variable( $offset );
//    $forum_id     = array_shift( bbp_get_group_forum_ids( bp_get_current_group_id() ) );
//    
//    if($forum_action == 'topic-tag')
//    {            
//        $tagName =  bp_action_variable(1);
//        if(!$tagName)
//        {
            //Goto Forum Homepage
//            wp_redirect(bp_get_group_permalink());
//            exit;
//        }
        //Getting Tag ID by Tag Name
//        $tagID = bbp_get_topic_tag_id($tagName);
//        if(!$tagID)
//        {
            //Goto Forum Homepage
//            wp_redirect(bp_get_group_permalink());
//            exit;
//        }

//        echo $bbp->shortcodes->display_topics_of_tag( array( 'id' => $tagID ) );
//    }
//}


