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
                       $groups = getUserCommunities($current_user->ID);
                       if(count($groups) < 1)
                       {
                   ?>
                       <div class="tr">
                           <div class="td td-full">You are currently not a member of any communities.</div>
                           <div class="clear"></div>
                       </div> 
                   <?php
                       }else{
                           foreach($groups as $group)
                           {
                   ?>
                        <div class="tr">
                            <div class="td td-name">
                                <a href="/communities/<?php echo $group->slug?>"><?php echo $group->title ?></a>
                            </div>
                            <div class="td td-since"><?php echo formatDate($group->membership_date); ?></div>
                            <div class="td td-role">
                                <?php
                                    if($group->is_admin)
                                        echo '<span class="group-admin">Admin</span>';
                                    else
                                        echo '<span class="group-member">Member</span>';
                                ?>
                            </div>
                            <div class="td td-action">
                                <a href="<?php echo get_site_url(); ?>/?cp-action=<?php echo wp_create_nonce('leave-group') ?>&group_id=<?php echo $gID ?>" class="action-btn delete-btn icon-btn has-tooltip leave-community-link delete-community-btn">
                                    <span class="p"></span>
                                    <span class="simple_tooltip radius6 no-wrap">Remove Membership<span></span></span>
                                </a>
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
            <a href="<?php echo home_url(); ?>/communities" class="action-btn add-new-btn has-tooltip">
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

<div class="popup-box" id="delete-community-box" style="display: none; width: 500px">
    <div class="popup-box-header radius6 noradiusbottom">Confirm Community Membership Cancellation</div>
    <div class="popup-box-content">
        This will cancel your membership of the <span class="comm_popup_name"></span> community. Are you sure?
    </div>
    <div class="popup-box-footer radius6 noradiustop">                   
        <div class="loading loading-with-text radius6"><div><b>DELETING COMMUNITY</b><span>Please wait...</span></div></div> 
        <a href="#" class="action-btn process-btn"><span class="p"></span><span class="t">Confirm</span></a>            
        <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>            
        <div class="clear"></div>
    </div>
    <a class="close_btn"></a>                
</div>

<script type="text/javascript">
jQuery(document).ready(function($){
    fixTdHeight(jQuery('#my_community_memberships'));
    //Fix Simple ToolTips
    jQuery('.td-status .simple_tooltip').each(function(){
        jQuery(this).css({'top': -1 * jQuery(this).outerHeight() - 6, 'margin-left': -1 * jQuery(this).outerWidth() / 2 + jQuery(this).parent().outerWidth() / 2});
    });

    jQuery('.delete-community-btn').on('click', function(){
        jQuery('.comm_popup_name').text( $( this).closest('div.tr').find('div.td:first').text() );
    });

    jQuery('.delete-community-btn').each(function(){
        var link = jQuery(this).attr('href');
        jQuery(this).cplightbox({
            type: 'inline',
            href: '#delete-community-box',
            onStart: function(){
                jQuery('#delete-community-box .process-btn').attr('href', link);
            }
        })
    });
})
</script>
<?php
get_footer();
?>
