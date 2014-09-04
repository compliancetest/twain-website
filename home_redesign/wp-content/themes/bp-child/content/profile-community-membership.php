<?php
/**
* Profile - My Community Membership Tab
*/
if(!defined('ABSPATH'))
    die('Invalid Request!');
    
?>
<div class="column left three_fifths nopadding">
    <div class="grid-box table-box" id="my_community_memberships">
        <div class="grid-box-header">
            <h5 class="left">My Community Memberships</h5>
            <a class="gbh-btn gbh-btn-add right" href="/communities">Add<span class="simple_tooltip radius6">View Communities<span></span></span></a>
            <div class="clear"></div>
        </div>
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
                   <div class="td td-full">There is no community that you joined.</div>
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
</div>
<?php $my_community_memberships_desc = get_post_meta($post->ID, 'my_community_memberships_desc', true);?>
<?php if($my_community_memberships_desc): ?>
<div class="right two_fifths">
    <div class="gray_message_box radius9 light_gray_txt">
        <div class="indicator"></div>
        <?php echo $my_community_memberships_desc;?>
    </div>
</div>
<?php endif; ?>