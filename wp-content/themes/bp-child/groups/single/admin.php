<?php
    /**
    * Groups Admin Page
    */
?>
<div id="group_admin_page" class="tab-content white_bcg column">
    <div class="half left">
        <!-- Group Details Tab -->
        <div class="grid-box" id="group_details_box">
            <form name="group-details-form" id="group-details-form" action="<?php bp_group_admin_form_action('edit-details')?>" method="post" enctype="multipart/form-data" role="main">
                <div class="grid-box-header">
                    <h5>Details</h5>
                </div>
                <div class="grid-box-body">
                    <div class="column">                      
                        <div class="field-row">
                            <label>Group Name</label>
                            <span class="input-holder"><input type="text" name="group-name" id="group-name" value="<?php bp_group_name(); ?>" aria-required="true" class="input" /></span>
                            <div class="clear"></div>
                        </div>
                        <div class="field-row">
                            <label>Group Description</label>
                            <span class="input-holder"><textarea name="group-desc" id="group-desc" aria-required="true" class="textarea"><?php bp_group_description_editable(); ?></textarea></span>
                            <div class="clear"></div>
                        </div>
                        <div class="field-row">
                            <label><?php _e( 'Notify group members of changes via email', 'buddypress' ); ?></label>
                            <span class="radio-holder">
                                <label><input type="radio" name="group-notify-members" value="1" /> <?php _e( 'YES', 'buddypress' ); ?></label>
                                <label><input type="radio" name="group-notify-members" value="0" checked="checked" /> <?php _e( 'NO', 'buddypress' ); ?></label>
                            </span>
                            <div class="clear"></div>
                        </div>          
                    </div>
                </div>
                <div class="grid-box-footer">
                    <div class="btn-row">                    
                        <a href="#" class="action-btn process-btn"><span class="p"></span><span class="t">SAVE</span></a>                    
                        <div class="clear"></div>
                    </div>
                </div>
                <?php wp_nonce_field( 'groups_edit_group_details_by_ajax' ); ?>
                <input type="hidden" name="group-id" id="group-id" value="<?php bp_group_id(); ?>" />
            </form>
        </div>
        <div class="space20"></div>
        <!-- Group Avatar -->
        <div class="grid-box" id="group_avatar_box">            
            <form name="group-avatar-form" id="group-avatar-form" action="<?php bp_group_admin_form_action('group-avatar')?>" method="post" enctype="multipart/form-data" role="main">
                <div class="grid-box-header">
                    <h5>Display Image</h5>
                </div>
                
              <?php if('crop-image' != bp_get_avatar_admin_step()){?>
                <div class="grid-box-body">
                    <div class="column">     
                        <div class="field-row">
                            <i><?php _e("Upload an image to use as an avatar for this group. The image will be shown on the main group page, and in search results.", 'buddypress'); ?></i>
                        </div>
                        <div class="field-row">
                            <div class="grid_cell current_avatar">
                                <?php if(bp_get_group_has_avatar()){ ?>
                                <?php echo bp_get_group_avatar(array('width' => 98, 'height' => 98))?>
                                <div class="space10"></div>
                                <a href="<?php echo bp_get_group_avatar_delete_link()?>" class="action-btn delete-grey-btn"><span class="p"></span><span class="t">DELETE</span></a>
                                <?php }else{ ?>
                                <img src="<?php echo CHILD_TEMPLATE_DIRECTORY?>/images/default-group-avatar.png" title="Default Avatar" />                                
                                <?php } ?>
                                <?php wp_nonce_field( 'bp_avatar_upload' ); ?>
                            </div>
                            <div class="grid_cell width250 left15">
                                <input type="file" name="file" id="file" />
                                <div class="space10"></div>
                                <a href="#" class="action-btn process-btn"><span class="p"></span><span class="t">Upload</span></a>                                                    
                                <input type="hidden" name="action" id="action" value="bp_avatar_upload" />
                                <input type="hidden" name="upload" id="upload" value="Upload Image" />
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>
                <?php } ?>

                <?php if(bp_is_group_admin_screen('group-avatar') && 'crop-image' == bp_get_avatar_admin_step() ){?>
                <div class="grid-box-body grid-crop-body">
                    <div class="column">                      
                        <div class="field-row">
                            <img src="<?php bp_avatar_to_crop(); ?>" id="avatar-to-crop" class="avatar" alt="<?php _e( 'Avatar to crop', 'buddypress' ); ?>" />
                            <div class="clear"></div>
                            <div id="avatar-crop-pane">
                                <img src="<?php bp_avatar_to_crop(); ?>" id="avatar-crop-preview" class="avatar" alt="<?php _e( 'Avatar preview', 'buddypress' ); ?>" />
                            </div>
                            <a href="#" class="action-btn process-btn"><span class="p"></span><span class="t">Crop Image</span></a>
                            <div class="clear"></div>
                            <input type="hidden" name="avatar-crop-submit" id="avatar-crop-submit" value="<?php _e( 'Crop Image', 'buddypress' ); ?>" />
                            <input type="hidden" name="image_src" id="image_src" value="<?php bp_avatar_to_crop_src(); ?>" />
                            <input type="hidden" id="x" name="x" />
                            <input type="hidden" id="y" name="y" />
                            <input type="hidden" id="w" name="w" />
                            <input type="hidden" id="h" name="h" />
                            <?php wp_nonce_field( 'bp_avatar_cropstore' ); ?>
                        </div>
                    </div>
                </div>
                <?php } ?>                        
                <input type="hidden" name="group-id" id="group-id" value="<?php bp_group_id(); ?>" />                
            </form>
        </div>
        <div class="space20"></div>
        <!-- Remove Group -->
        <div class="grid-box" id="group_remove_box">
            <form name="group-remove-form" id="group-remove-form" action="<?php bp_group_admin_form_action('delete-group')?>" method="post" enctype="multipart/form-data" role="main">
                <div class="grid-box-header">
                    <h5>Details</h5>
                </div>
                <div class="grid-box-body">
                    <div class="column">                      
                        <?php do_action( 'bp_before_group_delete_admin' ); ?>
                        <div class="field-row">
                            <font color='#ce1515'>WARNING</font>: Deleting this group will completely remove ALL content associated with it. There is no way back, please be careful with this option.
                        </div>
                        <div class="field-row">
                            <label><input type="checkbox" name="delete-group-understand" id="delete-group-understand" value="1" /> <?php _e( 'I understand the consequences of deleting this group.', 'buddypress' ); ?></label>
                        </div>    
                        <?php do_action( 'bp_after_group_delete_admin' ); ?>
                        <div class="btn-row">
                            <input type="hidden" value="<?php _e( 'Delete Group', 'buddypress' ); ?>" id="delete-group-button" name="delete-group-button" />
                            <a href="#" class="action-btn delete-grey-btn"><span class="p"></span><span class="t">DELETE GROUP</span></a>                    
                            <div class="clear"></div>
                        </div>                        
                    </div>
                </div>        
                <?php wp_nonce_field( 'groups_delete_group' ); ?>                                
                <input type="hidden" name="group-id" id="group-id" value="<?php bp_group_id(); ?>" />
            </form>
        </div>
        
    </div>
    <div class="right">
        <!-- Memebers -->
        <div class="grid-box" id="group_members_box">
            <div class="grid-box-header">
                <h5>Members</h5>
            </div>
            
            <div class="grid-box-body">
                <div class="column nopaddingbottom">
                    <form name="group-requests-form" id="group-requests-form" action="<?php bp_group_admin_form_action('membership-requests')?>" method="post" enctype="multipart/form-data" role="main">
                    <?php if(bp_group_has_membership_requests()){ ?>
                    <p class="nomarginbottom">The following persons wants to join the Group:</p>
                    <div class="field-row">
                        <ul id="request-list" class="member-list">
                            <?php while ( bp_group_membership_requests() ) : bp_group_the_membership_request(); ?>
                                <?php
                                    global $requests_template;    
                                    $rEmail = bp_core_get_user_email($requests_template->request->user_id);
                                    $rName = cp_get_user_fullname($requests_template->request->user_id);
//                                    bp_group_request_user_link();
                                ?>
                                <li>
                                    <?php bp_group_request_user_avatar_thumb(); ?>
                                    <span class="member-info">
                                        <span class="m-name"><?php echo $rName?></span><br />
                                        <span class="m-email"><?php echo $rEmail?></span>
                                        <span class="activity"><?php bp_group_request_time_since_requested(); ?></span>
                                    </span>
                                    <?php do_action( 'bp_group_membership_requests_admin_item' ); ?>
                                    <span class="action">
                                        <a href="<?php echo bp_get_group_request_accept_link()?>" class="action-btn process-btn no-submit"><span class="p"></span><span class="t">ACCEPT</span></a>
                                        <a href="<?php echo bp_get_group_request_reject_link()?>" class="action-btn cancel-btn"><span class="p"></span><span class="t">REJECT</span></a>
                                        <?php do_action( 'bp_group_membership_requests_admin_item_action' ); ?>
                                    </span>
                                    <div class="clear"></div>
                                </li>

                            <?php endwhile; ?>
                        </ul>
                    </div>
                    <?php }else{ ?>
                        <p><?php _e( 'There are no pending membership requests.', 'buddypress' ); ?></p>
                    <?php } ?>
                    </form>                    
                </div>
            </div>
            <div class="grid-box-body" id="group_members_body">
            <form name="group-members-form" id="group-members-form" action="<?php bp_group_admin_form_action('manage-members')?>" method="post" enctype="multipart/form-data" role="main">
                <div class="space20"></div>
                <div class="nav">
                    <ul>
                        <li><a href="#" data-action="ban">Kick &amp; Ban</a></li>
                        <li><a href="#" data-action="promote_to_mod">Promote to Mod</a></li>
                        <li><a href="#" data-action="promote_to_admin">Promote to Admin</a></li>
                        <li class="last-li"><a href="#" data-action="remove_from_group">Remove from Group</a></li>
                    </ul>
                    <div class="clear"></div>
                </div>
                <div class="clear"></div>
                <!-- Administrators -->
                <?php if(bp_has_members( '&include='. bp_group_admin_ids())){?>        
                <div class="field-row">
                    <p><b>Administrator</b></p>
                    <ul id="admins-list" class="member-list">                    
                        <?php while ( bp_members() ) : bp_the_member(); ?>
                        <?php
                            global $members_template;
                            $tName = cp_get_user_fullname($members_template->member->ID);
                            $tEmail = bp_get_member_user_email();
                        ?>
                        <li>
                            <input type="checkbox" name="id[]" value="<?php echo $members_template->member->ID?>" class="chk" />
                            <?php echo bp_core_fetch_avatar( array( 'item_id' => bp_get_member_user_id(), 'type' => 'thumb', 'width' => 28, 'height' => 28, 'alt' => sprintf( __( 'Profile picture of %s', 'buddypress' ), bp_get_member_name() ) ) ); ?>
                            <span class="member-info">
                                <span class="m-name"><?php echo $tName ?></span>
                                <span class="m-email"><?php echo $tEmail?></span>
                                <span class="clear"></span>
                                <?php if(count( bp_group_admin_ids( false, 'array' ) ) > 1){?>
                                <a class="action-btn process-btn small-action-btn no-submit" href="<?php bp_group_member_demote_link( bp_get_member_user_id() ); ?>"><?php _e( 'Demote to Member', 'buddypress' ); ?></a>
                                <?php } ?>
                            </span>
                            <div class="clear"></div>
                        </li>
                        <?php endwhile; ?>                
                    </ul>   
                    <div class="clear"></div>             
                </div>
                <?php } ?>
                
                <!-- Moderators -->
            <?php if(bp_group_has_moderators()){ ?>
                <?php if(bp_has_members( '&include='. bp_group_mod_ids())){?>        
                <div class="field-row">
                    <p><b>Moderators</b></p>
                    <ul id="mods-list" class="member-list">                    
                        <?php while ( bp_members() ) : bp_the_member(); ?>
                        <?php
                            global $members_template;
                            $tName = cp_get_user_fullname($members_template->member->ID);                            
                            $tEmail = bp_get_member_user_email();
                        ?>
                        <li>
                            <input type="checkbox" name="id[]" value="<?php echo $members_template->member->ID?>" class="chk" />
                            <?php echo bp_core_fetch_avatar( array( 'item_id' => bp_get_member_user_id(), 'type' => 'thumb', 'width' => 28, 'height' => 28, 'alt' => sprintf( __( 'Profile picture of %s', 'buddypress' ), bp_get_member_name() ) ) ); ?>
                            <span class="member-info">
                                <span class="m-name"><?php echo $tName ?></span>
                                <span class="m-email"><?php echo $tEmail?></span>
                                <span class="clear"></span>
                                <a class="action-btn small-action-btn process-btn no-submit" href="<?php bp_group_member_demote_link( bp_get_member_user_id() ); ?>"><?php _e( 'Demote to Member', 'buddypress' ); ?></a>
                            </span>
                            <div class="clear"></div>
                        </li>
                        <?php endwhile; ?>                
                    </ul>
                    <div class="clear"></div>                
                </div>
                <?php } ?>
            <?php } ?>
                <!-- Members -->
              <?php if(bp_group_has_members('per_page=15&exclude_banned=false')){ ?>                
                <div class="field-row">
                    <p><b>Members</b></p>
                    <?php if(bp_group_member_needs_pagination()){ ?>
                    <div class="pagination no-ajax">
                        <div id="member-count" class="pag-count">
                            <?php bp_group_member_pagination_count(); ?>
                        </div>
                        <div id="member-admin-pagination" class="pagination-links">
                            <?php bp_group_member_admin_pagination(); ?>
                        </div>
                    </div>
                    <?php } ?>       
                    <ul id="members-list" class="member-list">
                    <?php while ( bp_group_members() ) : bp_group_the_member(); ?>
                        <?php
                            global $members_template;                            
                            $tName = cp_get_user_fullname($members_template->member->user_id);
                            $tEmail = $members_template->member->user_email;                        
                        ?>
                        <li>
                            <input type="checkbox" name="id[]" value="<?php echo $members_template->member->user_id?>" class="chk" />
                            <?php bp_group_member_avatar_mini(28, 28); ?>                        
                            <span class="member-info">
                                <span class="m-name"><?php echo $tName ?></span> 
                                <?php if ( bp_get_group_member_is_banned() ) _e( ' <font color="#ce1515"><i>(banned)</i></font>', 'buddypress'); ?><br />
                                <span class="m-email"><?php echo $tEmail?></span>
                                <span class="clear"></span>
                                <?php if( bp_get_group_member_is_banned()){ ?>
                                    <a href="<?php bp_group_member_unban_link(); ?>" class="action-btn small-action-btn process-btn no-submit" title="<?php _e( 'Unban this member', 'buddypress' ); ?>">Unban</a>
                                <?php }?>                                
                            </span>                        
                        </li>
                    <?php endwhile; ?>
                    </ul>         
                    <div class="clear"></div>
                </div>
              <?php }else{ ?>
                <div class="field-row">
                    <p><b>Members</b></p>
                    <?php _e( 'This group has no members.', 'buddypress' ); ?>
                </div>
              <?php } ?>
              <div class="space15"></div>
              <?php wp_nonce_field( 'groups_manage_group_members' ); ?> 
              <input type="hidden" id="action" name="action" value="" />
              </form>
            </div>
        </div>
        <div class="space20"></div>
        <!-- Group Privacy -->
        <div class="grid-box" id="group_privacy_box">
            <form name="group-privacy-form" id="group-privacy-form" action="<?php bp_group_admin_form_action('group-settings')?>" method="post" enctype="multipart/form-data" role="main">
                <div class="grid-box-header">
                    <h5>Privacy Options</h5>
                </div>
                <div class="grid-box-body">
                    <div class="column">                      
                        <div class="field-row">
                            <label>
                                <input type="radio" name="group-status" value="public"<?php bp_group_show_status_setting( 'public' ); ?> />
                                <b><?php _e( 'This is a public group', 'buddypress' ); ?></b>
                            </label>                        
                            <ul>
                                <li><?php _e( 'Any site member can join this group.', 'buddypress' ); ?></li>
                                <li><?php _e( 'This group will be listed in the groups directory and in search results.', 'buddypress' ); ?></li>
                                <li><?php _e( 'Group content and activity will be visible to any site member.', 'buddypress' ); ?></li>
                            </ul>
                        </div>
                        <div class="field-row">
                            <label>
                                <input type="radio" name="group-status" value="private"<?php bp_group_show_status_setting( 'private' ); ?> />
                                <b><?php _e( 'This is a private group', 'buddypress' ); ?></b>
                            </label>
                            <ul>
                                <li><?php _e( 'Only users who request membership and are accepted can join the group.', 'buddypress' ); ?></li>
                                <li><?php _e( 'This group will be listed in the groups directory and in search results.', 'buddypress' ); ?></li>
                                <li><?php _e( 'Group content and activity will only be visible to members of the group.', 'buddypress' ); ?></li>
                            </ul>
                        </div>
                        <div class="field-row">
                            <label>
                                <input type="radio" name="group-status" value="hidden"<?php bp_group_show_status_setting( 'hidden' ); ?> />
                                <b><?php _e( 'This is a hidden group', 'buddypress' ); ?></b>
                            </label>
                            <ul>
                                <li><?php _e( 'Only users who are invited can join the group.', 'buddypress' ); ?></li>
                                <li><?php _e( 'This group will not be listed in the groups directory or search results.', 'buddypress' ); ?></li>
                                <li><?php _e( 'Group content and activity will only be visible to members of the group.', 'buddypress' ); ?></li>
                            </ul>
                        </div>
                    </div>
                </div>        
                <div class="grid-box-footer">
                    <div class="btn-row">
                        <a href="#" class="action-btn process-btn"><span class="p"></span><span class="t">SAVE</span></a>
                        <div class="clear"></div>
                    </div>
                </div>
                <?php wp_nonce_field( 'groups_edit_group_settings_by_ajax' ); ?>
                <input type="hidden" name="group-id" id="group-id" value="<?php bp_group_id(); ?>" />
                <input type="hidden" name="group-invite-status" id="group-invite-status" value="<?php echo bp_group_get_invite_status(); ?>" />
            </form>
        </div>
        <div class="space20"></div>
        <!-- Group Invitations -->
        <div class="grid-box" id="group_invitations_box">
            <form name="group-invitation-form" id="group-invitation-form" action="<?php bp_group_admin_form_action('group-settings')?>" method="post" enctype="multipart/form-data" role="main">
                <div class="grid-box-header">
                    <h5>Group Invitations</h5>
                </div>
                <div class="grid-box-body">
                    <div class="column">                   
                        <div class="field-row">
                            Which members of this group are allowed to invite others?
                        </div>
                        <div class="field-row">
                            <label> 
                                <input type="radio" name="group-invite-status" value="members"<?php bp_group_show_invite_status_setting( 'members' ); ?> /> 
                                <b><?php _e( 'All group members', 'buddypress' ); ?></b> 
                            </label>                             
                        </div>
                        <div class="field-row">
                            <label> 
                                <input type="radio" name="group-invite-status" value="mods"<?php bp_group_show_invite_status_setting( 'mods' ); ?> /> 
                                <b><?php _e( 'Group admins and mods only', 'buddypress' ); ?></b>
                            </label>
                        </div>
                        <div class="field-row">
                            <label> 
                                <input type="radio" name="group-invite-status" value="admins"<?php bp_group_show_invite_status_setting( 'admins' ); ?> /> 
                                <b><?php _e( 'Group admins only', 'buddypress' ); ?></b> 
                            </label>
                        </div>                        
                    </div>
                </div>        
                <div class="grid-box-footer">
                    <div class="btn-row">
                        <a href="#" class="action-btn process-btn"><span class="p"></span><span class="t">SAVE</span></a>
                        <div class="clear"></div>
                    </div>
                </div>
                <?php wp_nonce_field( 'groups_edit_group_settings_by_ajax' ); ?>
                <input type="hidden" name="group-id" id="group-id" value="<?php bp_group_id(); ?>" />
                <input type="hidden" name="group-status" id="group-status" value="<?php echo bp_get_group_status(); ?>" />
            </form>
        </div>
    </div>
    <div class="clear"></div>
</div>



