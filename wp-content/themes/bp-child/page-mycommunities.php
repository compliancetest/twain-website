<?php
/*
 * Template Name: My Communities
 */


if(!is_user_logged_in()){
    wp_redirect(home_url());
    exit;
}
get_header();
?>
<div class="content" id="my_communities">
    <div class="dashboard-tabs">
        <?php get_sidebar('dashboard'); ?>
    </div>
    <div class="container">
        <div class="column">
            
            <div class="grid-box table-box" id="my_community_memberships">
                <div class="grid-box-body">
                    <div class="thead tr">
                       <div class="td td-name">Name</div>
                       <div class="td td-since">Since</div>
                       <div class="td td-role">Role</div>
                       <div class="td td-action">Action</div>
                       <div class="clear"></div>
                   </div>
                   <div class="tbody">
                   <?php
                       $groups =  groups_get_user_groups($current_user->ID);
                       if($groups['total'] < 1)
                       {
                   ?>
                       <div class="tr">
                           <div class="td td-full">You are currently not a member of any communities.</div>
                           <div class="clear"></div>
                       </div> 
                   <?php
                       }else{
                           foreach($groups['groups'] as $gID)
                           {
                               $group = groups_get_group(array('group_id'=>$gID));
                               $member = getGroupMemberDetail($gID, $current_user->ID);
                               
                   ?>
                        <div class="tr">
                            <div class="td td-name">
                                <a href="<?php echo bp_get_group_permalink($group)?>"><?php echo bp_get_group_name($group) ?></a>
                            </div>
                            <div class="td td-since"><?php echo formatDate($member->date_modified); ?></div>
                            <div class="td td-role">
                                <?php
                                    if($member->is_admin)
                                        echo '<span class="group-admin">Admin</span>';
                                    else if($member->is_mod)
                                        echo '<span class="group-support">Support</span>';
                                    else 
                                        echo '<span class="group-member">Member</span>';
                                ?>
                            </div>
                            <div class="td td-action">
                                <a href="?cp-action=<?php echo wp_create_nonce('leave-group') ?>&group_id=<?php echo $gID ?>" class="action-btn delete-btn icon-btn leave-community-link has-tooltip"><span class="p"></span><span class="simple_tooltip radius6">Remove Membership<span></span></span></a>
                            </div>
                            <div class="clear"></div>
                        </div>
                   <?php
                           }
                       }
                   ?>
                   <div class="loading1"></div>
                   </div>
                   
                </div>                    
            </div>
            <div class="space10"></div>
            <a href="<?php echo home_url(); ?>/communities" class="action-btn add-new-btn">
                <span class="p"></span>
                <span class="t">Add</span>
                <span class="simple_tooltip radius6">Add Community Membership<span></span></span>
            </a>
            <div class="space20"></div>
        </div>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
</div> <!--end content-->


<script type="text/javascript">
jQuery(document).ready(function(){
    fixTdHeight(jQuery('#my_community_memberships'));
    //Fix Simple ToolTips
    jQuery('.td-status .simple_tooltip').each(function(){
        jQuery(this).css({'top': -1 * jQuery(this).outerHeight() - 6, 'margin-left': -1 * jQuery(this).outerWidth() / 2 + jQuery(this).parent().outerWidth() / 2});
    })
})
</script>
<?php
get_footer();
?>
