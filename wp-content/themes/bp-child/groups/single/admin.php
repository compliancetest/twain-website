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
                                <a href="<?php echo bp_get_group_avatar_delete_link()?>" class="action-btn delete-btn"><span class="p"></span><span class="t">DELETE</span></a>
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
                            <a href="#" class="action-btn delete-btn"><span class="p"></span><span class="t">DELETE GROUP</span></a>                    
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
            <form name="group-privacy-form" id="group-privacy-form" action="<?php bp_group_admin_form_action('group-settings')?>" method="post" enctype="multipart/form-data" role="main">
            </form>
        </div>
        
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
<div class="item-list-tabs no-ajax" id="subnav" role="navigation">
	<ul>
		<?php bp_group_admin_tabs(); ?>
	</ul>
</div><!-- .item-list-tabs -->

<form action="<?php bp_group_admin_form_action(); ?>" name="group-settings-form" id="group-settings-form" class="standard-form" method="post" enctype="multipart/form-data" role="main">

<?php do_action( 'bp_before_group_admin_content' ); ?>

<?php /* Manage Group Settings */ ?>
<?php if ( bp_is_group_admin_screen( 'group-settings' ) ) : ?>

	<?php do_action( 'bp_before_group_settings_admin' ); ?>

	<?php if ( bp_is_active( 'forums' ) ) : ?>

		<?php if ( bp_forums_is_installed_correctly() ) : ?>

			<div class="checkbox">
				<label><input type="checkbox" name="group-show-forum" id="group-show-forum" value="1"<?php bp_group_show_forum_setting(); ?> /> <?php _e( 'Enable discussion forum', 'buddypress' ); ?></label>
			</div>

			<hr />

		<?php endif; ?>

	<?php endif; ?>

	<h4><?php _e( 'Privacy Options', 'buddypress' ); ?></h4>

	<div class="radio">
		<label>
			<input type="radio" name="group-status" value="public"<?php bp_group_show_status_setting( 'public' ); ?> />
			<strong><?php _e( 'This is a public group', 'buddypress' ); ?></strong>
			<ul>
				<li><?php _e( 'Any site member can join this group.', 'buddypress' ); ?></li>
				<li><?php _e( 'This group will be listed in the groups directory and in search results.', 'buddypress' ); ?></li>
				<li><?php _e( 'Group content and activity will be visible to any site member.', 'buddypress' ); ?></li>
			</ul>
		</label>

		<label>
			<input type="radio" name="group-status" value="private"<?php bp_group_show_status_setting( 'private' ); ?> />
			<strong><?php _e( 'This is a private group', 'buddypress' ); ?></strong>
			<ul>
				<li><?php _e( 'Only users who request membership and are accepted can join the group.', 'buddypress' ); ?></li>
				<li><?php _e( 'This group will be listed in the groups directory and in search results.', 'buddypress' ); ?></li>
				<li><?php _e( 'Group content and activity will only be visible to members of the group.', 'buddypress' ); ?></li>
			</ul>
		</label>

		<label>
			<input type="radio" name="group-status" value="hidden"<?php bp_group_show_status_setting( 'hidden' ); ?> />
			<strong><?php _e( 'This is a hidden group', 'buddypress' ); ?></strong>
			<ul>
				<li><?php _e( 'Only users who are invited can join the group.', 'buddypress' ); ?></li>
				<li><?php _e( 'This group will not be listed in the groups directory or search results.', 'buddypress' ); ?></li>
				<li><?php _e( 'Group content and activity will only be visible to members of the group.', 'buddypress' ); ?></li>
			</ul>
		</label>
	</div>

	<hr /> 
	 
	<h4><?php _e( 'Group Invitations', 'buddypress' ); ?></h4> 

	<p><?php _e( 'Which members of this group are allowed to invite others?', 'buddypress' ); ?></p> 

	<div class="radio"> 
		<label> 
			<input type="radio" name="group-invite-status" value="members"<?php bp_group_show_invite_status_setting( 'members' ); ?> /> 
			<strong><?php _e( 'All group members', 'buddypress' ); ?></strong> 
		</label> 

		<label> 
			<input type="radio" name="group-invite-status" value="mods"<?php bp_group_show_invite_status_setting( 'mods' ); ?> /> 
			<strong><?php _e( 'Group admins and mods only', 'buddypress' ); ?></strong> 
		</label>
		
		<label> 
			<input type="radio" name="group-invite-status" value="admins"<?php bp_group_show_invite_status_setting( 'admins' ); ?> /> 
			<strong><?php _e( 'Group admins only', 'buddypress' ); ?></strong> 
		</label> 
 	</div> 

	<hr /> 

	<?php do_action( 'bp_after_group_settings_admin' ); ?>

	<p><input type="submit" value="<?php _e( 'Save Changes', 'buddypress' ); ?>" id="save" name="save" /></p>
	<?php wp_nonce_field( 'groups_edit_group_settings' ); ?>

<?php endif; ?>

<?php /* Manage Group Members */ ?>
<?php if ( bp_is_group_admin_screen( 'manage-members' ) ) : ?>

	<?php do_action( 'bp_before_group_manage_members_admin' ); ?>
	
	<div class="bp-widget">
		<h4><?php _e( 'Administrators', 'buddypress' ); ?></h4>

		<?php if ( bp_has_members( '&include='. bp_group_admin_ids() ) ) : ?>
		
		<ul id="admins-list" class="item-list single-line">
			
			<?php while ( bp_members() ) : bp_the_member(); ?>
			<li>
				<?php echo bp_core_fetch_avatar( array( 'item_id' => bp_get_member_user_id(), 'type' => 'thumb', 'width' => 30, 'height' => 30, 'alt' => sprintf( __( 'Profile picture of %s', 'buddypress' ), bp_get_member_name() ) ) ); ?>
				<h5>
					<a href="<?php bp_member_permalink(); ?>"> <?php bp_member_name(); ?></a>
					<?php if ( count( bp_group_admin_ids( false, 'array' ) ) > 1 ) : ?>
					<span class="small">
						<a class="button confirm admin-demote-to-member" href="<?php bp_group_member_demote_link( bp_get_member_user_id() ); ?>"><?php _e( 'Demote to Member', 'buddypress' ); ?></a>
					</span>			
					<?php endif; ?>
				</h5>		
			</li>
			<?php endwhile; ?>
		
		</ul>
		
		<?php endif; ?>

	</div>
	
	<?php if ( bp_group_has_moderators() ) : ?>
		<div class="bp-widget">
			<h4><?php _e( 'Moderators', 'buddypress' ); ?></h4>		
			
			<?php if ( bp_has_members( '&include=' . bp_group_mod_ids() ) ) : ?>
				<ul id="mods-list" class="item-list single-line">
				
					<?php while ( bp_members() ) : bp_the_member(); ?>					
					<li>
						<?php echo bp_core_fetch_avatar( array( 'item_id' => bp_get_member_user_id(), 'type' => 'thumb', 'width' => 30, 'height' => 30, 'alt' => sprintf( __( 'Profile picture of %s', 'buddypress' ), bp_get_member_name() ) ) ); ?>
						<h5>
							<a href="<?php bp_member_permalink(); ?>"> <?php bp_member_name(); ?></a>
							<span class="small">
								<a href="<?php bp_group_member_promote_admin_link( array( 'user_id' => bp_get_member_user_id() ) ); ?>" class="button confirm mod-promote-to-admin" title="<?php _e( 'Promote to Admin', 'buddypress' ); ?>"><?php _e( 'Promote to Admin', 'buddypress' ); ?></a>
								<a class="button confirm mod-demote-to-member" href="<?php bp_group_member_demote_link( bp_get_member_user_id() ); ?>"><?php _e( 'Demote to Member', 'buddypress' ); ?></a>
							</span>		
						</h5>		
					</li>	
					<?php endwhile; ?>			
				
				</ul>
			
			<?php endif; ?>
		</div>
	<?php endif ?>


	<div class="bp-widget">
		<h4><?php _e("Members", "buddypress"); ?></h4>

		<?php if ( bp_group_has_members( 'per_page=15&exclude_banned=false' ) ) : ?>

			<?php if ( bp_group_member_needs_pagination() ) : ?>

				<div class="pagination no-ajax">

					<div id="member-count" class="pag-count">
						<?php bp_group_member_pagination_count(); ?>
					</div>

					<div id="member-admin-pagination" class="pagination-links">
						<?php bp_group_member_admin_pagination(); ?>
					</div>

				</div>

			<?php endif; ?>

			<ul id="members-list" class="item-list single-line">
				<?php while ( bp_group_members() ) : bp_group_the_member(); ?>

					<li class="<?php bp_group_member_css_class(); ?>">
						<?php bp_group_member_avatar_mini(); ?>

						<h5>
							<?php bp_group_member_link(); ?>

							<?php if ( bp_get_group_member_is_banned() ) _e( '(banned)', 'buddypress'); ?>

							<span class="small">

							<?php if ( bp_get_group_member_is_banned() ) : ?>

								<a href="<?php bp_group_member_unban_link(); ?>" class="button confirm member-unban" title="<?php _e( 'Unban this member', 'buddypress' ); ?>"><?php _e( 'Remove Ban', 'buddypress' ); ?></a>

							<?php else : ?>

								<a href="<?php bp_group_member_ban_link(); ?>" class="button confirm member-ban" title="<?php _e( 'Kick and ban this member', 'buddypress' ); ?>"><?php _e( 'Kick &amp; Ban', 'buddypress' ); ?></a>
								<a href="<?php bp_group_member_promote_mod_link(); ?>" class="button confirm member-promote-to-mod" title="<?php _e( 'Promote to Mod', 'buddypress' ); ?>"><?php _e( 'Promote to Mod', 'buddypress' ); ?></a>
								<a href="<?php bp_group_member_promote_admin_link(); ?>" class="button confirm member-promote-to-admin" title="<?php _e( 'Promote to Admin', 'buddypress' ); ?>"><?php _e( 'Promote to Admin', 'buddypress' ); ?></a>

							<?php endif; ?>

								<a href="<?php bp_group_member_remove_link(); ?>" class="button confirm" title="<?php _e( 'Remove this member', 'buddypress' ); ?>"><?php _e( 'Remove from group', 'buddypress' ); ?></a>

								<?php do_action( 'bp_group_manage_members_admin_item' ); ?>

							</span>
						</h5>
					</li>

				<?php endwhile; ?>
			</ul>

		<?php else: ?>

			<div id="message" class="info">
				<p><?php _e( 'This group has no members.', 'buddypress' ); ?></p>
			</div>

		<?php endif; ?>

	</div>

	<?php do_action( 'bp_after_group_manage_members_admin' ); ?>

<?php endif; ?>

<?php /* Manage Membership Requests */ ?>
<?php if ( bp_is_group_admin_screen( 'membership-requests' ) ) : ?>

	<?php do_action( 'bp_before_group_membership_requests_admin' ); ?>

	<?php if ( bp_group_has_membership_requests() ) : ?>

		<ul id="request-list" class="item-list">
			<?php while ( bp_group_membership_requests() ) : bp_group_the_membership_request(); ?>

				<li>
					<?php bp_group_request_user_avatar_thumb(); ?>
					<h4><?php bp_group_request_user_link(); ?> <span class="comments"><?php bp_group_request_comment(); ?></span></h4>
					<span class="activity"><?php bp_group_request_time_since_requested(); ?></span>

					<?php do_action( 'bp_group_membership_requests_admin_item' ); ?>

					<div class="action">

						<?php bp_button( array( 'id' => 'group_membership_accept', 'component' => 'groups', 'wrapper_class' => 'accept', 'link_href' => bp_get_group_request_accept_link(), 'link_title' => __( 'Accept', 'buddypress' ), 'link_text' => __( 'Accept', 'buddypress' ) ) ); ?>

						<?php bp_button( array( 'id' => 'group_membership_reject', 'component' => 'groups', 'wrapper_class' => 'reject', 'link_href' => bp_get_group_request_reject_link(), 'link_title' => __( 'Reject', 'buddypress' ), 'link_text' => __( 'Reject', 'buddypress' ) ) ); ?>

						<?php do_action( 'bp_group_membership_requests_admin_item_action' ); ?>

					</div>
				</li>

			<?php endwhile; ?>
		</ul>

	<?php else: ?>

		<div id="message" class="info">
			<p><?php _e( 'There are no pending membership requests.', 'buddypress' ); ?></p>
		</div>

	<?php endif; ?>

	<?php do_action( 'bp_after_group_membership_requests_admin' ); ?>

<?php endif; ?>

<?php do_action( 'groups_custom_edit_steps' ) // Allow plugins to add custom group edit screens ?>


<?php /* This is important, don't forget it */ ?>
	<input type="hidden" name="group-id" id="group-id" value="<?php bp_group_id(); ?>" />

<?php do_action( 'bp_after_group_admin_content' ); ?>

</form><!-- #group-settings-form -->

