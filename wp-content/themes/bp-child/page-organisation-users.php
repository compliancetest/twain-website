<?php
/*
 * Template Name: Organisation Users
 */

if (!($organisation_id = ct_is_organisation_admin())) {    
    wp_redirect(home_url());
    exit;
}

$organisationClass = new CT_Organisation($organisation_id);

get_header();

?>
<div class="content" id="organisation-container">
    <div class="dashboard-tabs">
        <?php get_sidebar('dashboard'); ?>
    </div>
    <div class="container">
        <div class="column">
            <?php get_sidebar('organisation'); ?>
            <div id="item-body">
                <div id="organisation_test_suites" class="tab-content white_bcg column">
                    <div class="grid-box table-box" id="organisation_users">
                        <div class="grid-box-header">
                            <h5>Users</h5>
                            <div class="clear"></div>
                        </div>
                        <div class="grid-box-body">
                            <div class="thead tr">
                               <div class="td td-name">Name</div>
                               <div class="td td-email">Email</div>
                               <div class="td td-roles tocenter">Role(s)</div>
                               <div class="td td-privilege tocenter">Privilege(s)</div>
                               <div class="td td-action tocenter">Action</div>
                               <div class="clear"></div>
                             </div>
                           <div class="tbody">
                           <?php
                               $members =  $organisationClass->get_organisation_members();
                               if(count($members) < 1)
                               {
                           ?>
                               <div class="tr">
                                   <div class="td td-full">No user found.</div>
                                   <div class="clear"></div>
                               </div> 
                           <?php
                               }else{
                                   foreach($members as $row)
                                   {
                                       $privileges = ct_get_user_privileges($row->ID, $organisation_id);
                           ?>
                                <div class="tr">
                                    <div class="td td-name"><?php echo $row->full_name?></div>
                                    <div class="td td-email"><?php echo $row->user_email?></div>
                                    <div class="td td-roles tocenter"><?php echo $row->is_admin ? 'Admin' : 'Member'?></div>
                                    <div class="td td-privilege tocenter">
                                        <?php
                                            foreach($privileges as $p) {
                                        ?>
                                                <span class="has-tooltip"><?php echo $p->title?><span class="simple_tooltip"><?php echo $p->description?><span></span></span></span><br />
                                        <?php
                                            }
                                        ?>
                                    </div>
                                    <div class="td td-action tocenter">                                        
                                        <a href="<?php echo the_permalink() ?>?_organisation_nonce=<?php echo wp_create_nonce('edit-privilege') ?>&user_id=<?php echo $row->ID;?>&organisation_id=<?php echo $organisation_id;?>" class="action-btn edit-btn has-tooltip icon-btn left15" data-id="26" rel="custom-popup" cp-type="ajax" cp-removeboxafterclose="1" cp-closewhenclickoveraly="0">
                                            <span class="p"></span>
                                            <span class="simple_tooltip">Edit Privilege<span></span></span>
                                        </a>                           
                                        <a href="javascript: void(0)" data-id="<?php echo $row->membership_id?>" class="action-btn icon-btn delete-btn has-tooltip left10">
                                            <span class="p"></span>
                                            <span class="simple_tooltip">Delete Member<span></span></span>
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
                    <div class="clear"></div>
                </div>
            </div>
            <div class="clear"></div>            
        </div>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
</div> <!--end content-->
<div class="popup-box" id="member-deletion-confirm-box" style="display: none; width: 450px;">
    <form name="deleteMemberForm" id="deleteMemberForm" action="" method="post">
        <div class="popup-box-header radius6 noradiusbottom">Confirm Membership Deletion</div>        
        <div class="popup-box-content grid-box-body">    
            <p>
                Are you sure that you want to remove this person as a member of the organisation? <br />
                The subscriptions that have been allocated to this member will be released.
            </p>
        </div>
        <?php
            wp_nonce_field('remove-membership', '_organisation_nonce');
        ?>
        <div class="loading loading-with-text"><div><b>REMOVING MEMBER</b><span>Please wait...</span></div></div>

        <div class="popup-box-footer radius6 noradiustop">
            <a href="#" class="action-btn process-btn"><span class="p"></span><span class="t">Confirm</span></a>
            <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>            
            <div class="clear"></div>
        </div>
        <input type="hidden" name="id" id="id" value="" />
        <a class="close_btn"></a>                                
    </form>
</div>
<script type="text/javascript">
jQuery(document).ready(function(){
    fixTdHeight(jQuery('#organisation_users'));
    jQuery('#organisation_users .delete-btn').each(function(){
        var id = jQuery(this).attr('data-id');
        
        jQuery(this).cplightbox({
            type : 'inline',
            href : '#member-deletion-confirm-box',
            onStart: function(){
                jQuery('#deleteMemberForm #id').val(id);
            }
        })
        
        jQuery('#member-deletion-confirm-box .process-btn').click(function(){
            jQuery('#deleteMemberForm .loading').show();
            jQuery('#deleteMemberForm').submit();
        })
    })
})
</script>
<?php
get_footer();
?>