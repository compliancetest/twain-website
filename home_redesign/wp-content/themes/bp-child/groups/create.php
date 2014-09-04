<?php

/**
 * BuddyPress - Create Group
 *
 * @package BuddyPress
 * @subpackage bp-default
 */

get_header( 'buddypress' ); ?>
<div class="content container">
	<div id="content">
		<div class="padder">
		
		<?php do_action( 'bp_before_create_group_content_template' ); ?>

		<form action="<?php bp_group_creation_form_action(); ?>" method="post" id="create-group-form" class="standard-form" enctype="multipart/form-data">
            <div class="page-title-block column">
			    <h2 class="nomarginbottom left">
                    <?php _e( 'Create a Community', 'buddypress' ); ?> 
                </h2>
                <a class="right top10" href="<?php echo trailingslashit( bp_get_root_domain() . '/' . bp_get_groups_root_slug() ); ?>"><b><?php _e( 'Communities', 'buddypress' ); ?></b></a>
                <div class="clear"></div>
            </div>
			<?php do_action( 'bp_before_create_group' ); ?>
            <div class="column">
                <div class="tabs_wrap radius6 light_gray_bcg system-section">
			        <div class="item-list-tabs no-ajax" id="group-create-tabs" role="navigation">
				        <ul>
					        <?php bp_group_creation_tabs(); ?>
				        </ul>
                        <div class="clear"></div>
			        </div>
			        <?php // do_action( 'template_notices' ); ?>

			        <div class="item-body tab-content white_bcg padding10" id="group-create-body">

				        <?php /* Group creation step 1: Basic group details */ ?>
				        <?php if ( bp_is_group_creation_step( 'group-details' ) ) : ?>

					        <?php do_action( 'bp_before_group_details_creation_step' ); ?>

					        <label for="group-name"><?php _e( 'Group Name (required)', 'buddypress' ); ?>: </label><br />
					        <input type="text" name="group-name" size="80" id="group-name" aria-required="true" value="<?php bp_new_group_name(); ?>" />
                            <br />

					        <label for="group-desc"><?php _e( 'Group Description (required)', 'buddypress' ); ?>: </label>
                            <br />
					        <textarea name="group-desc" id="group-desc" cols="80" aria-required="true"><?php bp_new_group_description(); ?></textarea>
                            <br />

					        <?php
					        do_action( 'bp_after_group_details_creation_step' );
					        do_action( 'groups_custom_group_fields_editable' ); // @Deprecated

					        wp_nonce_field( 'groups_create_save_group-details' ); ?>

				        <?php endif; ?>

				        <?php /* Group creation step 2: Group settings */ ?>
				        <?php if ( bp_is_group_creation_step( 'group-settings' ) ) : ?>

					        <?php do_action( 'bp_before_group_settings_creation_step' ); ?>

					        <h4><?php _e( 'Privacy Options', 'buddypress' ); ?></h4>

					        <div class="radio">
						        <label><input type="radio" name="group-status" value="public"<?php if ( 'public' == bp_get_new_group_status() || !bp_get_new_group_status() ) { ?> checked="checked"<?php } ?> />
							        <strong><?php _e( 'This is a public group', 'buddypress' ); ?></strong>
							        <ul>
								        <li><?php _e( 'Any site member can join this group.', 'buddypress' ); ?></li>
								        <li><?php _e( 'This group will be listed in the groups directory and in search results.', 'buddypress' ); ?></li>
								        <li><?php _e( 'Group content and activity will be visible to any site member.', 'buddypress' ); ?></li>
							        </ul>
						        </label>

						        <label><input type="radio" name="group-status" value="private"<?php if ( 'private' == bp_get_new_group_status() ) { ?> checked="checked"<?php } ?> />
							        <strong><?php _e( 'This is a private group', 'buddypress' ); ?></strong>
							        <ul>
								        <li><?php _e( 'Only users who request membership and are accepted can join the group.', 'buddypress' ); ?></li>
								        <li><?php _e( 'This group will be listed in the groups directory and in search results.', 'buddypress' ); ?></li>
								        <li><?php _e( 'Group content and activity will only be visible to members of the group.', 'buddypress' ); ?></li>
							        </ul>
						        </label>

						        <label><input type="radio" name="group-status" value="hidden"<?php if ( 'hidden' == bp_get_new_group_status() ) { ?> checked="checked"<?php } ?> />
							        <strong><?php _e('This is a hidden group', 'buddypress'); ?></strong>
							        <ul>
								        <li><?php _e( 'Only users who are invited can join the group.', 'buddypress' ); ?></li>
								        <li><?php _e( 'This group will not be listed in the groups directory or search results.', 'buddypress' ); ?></li>
								        <li><?php _e( 'Group content and activity will only be visible to members of the group.', 'buddypress' ); ?></li>
							        </ul>
						        </label>
					        </div>

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

					        <?php if ( bp_is_active( 'forums' ) ) : ?>

						        <h4><?php _e( 'Group Forums', 'buddypress' ); ?></h4>

						        <?php if ( bp_forums_is_installed_correctly() ) : ?>

							        <p><?php _e( 'Should this group have a forum?', 'buddypress' ); ?></p>

							        <div class="checkbox">
								        <label><input type="checkbox" name="group-show-forum" id="group-show-forum" value="1"<?php checked( bp_get_new_group_enable_forum(), true, true ); ?> /> <?php _e( 'Enable discussion forum', 'buddypress' ); ?></label>
							        </div>
						        <?php elseif ( is_super_admin() ) : ?>

							        <p><?php printf( __( '<strong>Attention Site Admin:</strong> Group forums require the <a href="%s">correct setup and configuration</a> of a bbPress installation.', 'buddypress' ), bp_core_do_network_admin() ? network_admin_url( 'settings.php?page=bb-forums-setup' ) :  admin_url( 'admin.php?page=bb-forums-setup' ) ); ?></p>

						        <?php endif; ?>

					        <?php endif; ?>

					        <?php do_action( 'bp_after_group_settings_creation_step' ); ?>

					        <?php wp_nonce_field( 'groups_create_save_group-settings' ); ?>

				        <?php endif; ?>

				        <?php /* Group creation step 3: Avatar Uploads */ ?>
				        <?php if ( bp_is_group_creation_step( 'group-avatar' ) ) : ?>

					        <?php do_action( 'bp_before_group_avatar_creation_step' ); ?>

					        <?php if ( 'upload-image' == bp_get_avatar_admin_step() ) : ?>

						        <div class="left-menu">

							        <?php bp_new_group_avatar(); ?>

						        </div><!-- .left-menu -->

						        <div class="main-column">
							        <p><?php _e( "Upload an image to use as an avatar for this group. The image will be shown on the main group page, and in search results.", 'buddypress' ); ?></p>

							        <p>
								        <input type="file" name="file" id="file" />
								        <input type="submit" name="upload" id="upload" class="action-btn process-btn" style="float: none" value="<?php _e( 'Upload Image', 'buddypress' ); ?>" />
								        <input type="hidden" name="action" id="action" value="bp_avatar_upload" />
							        </p>

							        <p><?php _e( 'To skip the avatar upload process, hit the "Next Step" button.', 'buddypress' ); ?></p>
						        </div><!-- .main-column -->

					        <?php endif; ?>

					        <?php if ( 'crop-image' == bp_get_avatar_admin_step() ) : ?>

						        <h3><?php _e( 'Crop Group Avatar', 'buddypress' ); ?></h3>

						        <img src="<?php bp_avatar_to_crop(); ?>" id="avatar-to-crop" class="avatar" alt="<?php _e( 'Avatar to crop', 'buddypress' ); ?>" />

						        <div id="avatar-crop-pane">
							        <img src="<?php bp_avatar_to_crop(); ?>" id="avatar-crop-preview" class="avatar" alt="<?php _e( 'Avatar preview', 'buddypress' ); ?>" />
						        </div>

						        <input type="submit" name="avatar-crop-submit" id="avatar-crop-submit" value="<?php _e( 'Crop Image', 'buddypress' ); ?>" />

						        <input type="hidden" name="image_src" id="image_src" value="<?php bp_avatar_to_crop_src(); ?>" />
						        <input type="hidden" name="upload" id="upload" />
						        <input type="hidden" id="x" name="x" />
						        <input type="hidden" id="y" name="y" />
						        <input type="hidden" id="w" name="w" />
						        <input type="hidden" id="h" name="h" />

					        <?php endif; ?>

					        <?php do_action( 'bp_after_group_avatar_creation_step' ); ?>

					        <?php wp_nonce_field( 'groups_create_save_group-avatar' ); ?>

				        <?php endif; ?>

				        <?php /* Group creation step 4: Invite friends to group */ ?>
				        <?php if ( bp_is_group_creation_step( 'group-invites' ) ) : ?>

					        <?php do_action( 'bp_before_group_invites_creation_step' ); ?>

					        <?php if ( bp_is_active( 'friends' ) && bp_get_total_friend_count( bp_loggedin_user_id() ) ) : ?>

						        <div class="left-menu">

							        <div id="invite-list">
								        <ul>
									        <?php bp_new_group_invite_friend_list(); ?>
								        </ul>

								        <?php wp_nonce_field( 'groups_invite_uninvite_user', '_wpnonce_invite_uninvite_user' ); ?>
							        </div>

						        </div><!-- .left-menu -->

						        <div class="main-column">

							        <div id="message" class="info">
								        <p><?php _e('Select people to invite from your friends list.', 'buddypress'); ?></p>
							        </div>

							        <?php /* The ID 'friend-list' is important for AJAX support. */ ?>
							        <ul id="friend-list" class="item-list" role="main">

							        <?php if ( bp_group_has_invites() ) : ?>

								        <?php while ( bp_group_invites() ) : bp_group_the_invite(); ?>

									        <li id="<?php bp_group_invite_item_id(); ?>">

										        <?php bp_group_invite_user_avatar(); ?>

										        <h4><?php bp_group_invite_user_link(); ?></h4>
										        <span class="activity"><?php bp_group_invite_user_last_active(); ?></span>

										        <div class="action">
											        <a class="remove" href="<?php bp_group_invite_user_remove_invite_url(); ?>" id="<?php bp_group_invite_item_id(); ?>"><?php _e( 'Remove Invite', 'buddypress' ); ?></a>
										        </div>
									        </li>

								        <?php endwhile; ?>

								        <?php wp_nonce_field( 'groups_send_invites', '_wpnonce_send_invites' ); ?>

							        <?php endif; ?>

							        </ul>

						        </div><!-- .main-column -->

					        <?php else : ?>

						        <div id="message" class="info">
							        <p><?php _e( 'Once you have built up friend connections you will be able to invite others to your group.', 'buddypress' ); ?></p>
						        </div>

					        <?php endif; ?>

					        <?php wp_nonce_field( 'groups_create_save_group-invites' ); ?>

					        <?php do_action( 'bp_after_group_invites_creation_step' ); ?>

				        <?php endif; ?>

				        <?php do_action( 'groups_custom_create_steps' ); // Allow plugins to add custom group creation steps ?>
                        
                        <script type="text/javascript">
                        jQuery("select[name='bp-docs[can-create]'] option:eq(2)").remove();
                        jQuery("select[name='bp-docs[can-create]'] option:eq(1)").remove();
                        </script>

				        <?php do_action( 'bp_before_group_creation_step_buttons' ); ?>

				        <?php if ( 'crop-image' != bp_get_avatar_admin_step() ) : ?>

					        <div class="submit" id="previous-next">
                                
						        <?php /* Previous Button */ ?>
						        <?php if ( !bp_is_first_group_creation_step() ) : ?>

                                    <a href="<?php bp_group_creation_previous_link(); ?>" type="button" class="action-btn back-btn has-tooltip" id="group-creation-previous" name="previous" onclick="location.href='<?php bp_group_creation_previous_link(); ?>'">
                                        <span class="p"></span>
                                        <span class="t"><?php _e( 'Back', 'compliancetest' ); ?></span>
                                        <span class="simple_tooltip"><?php _e( 'Back to Previous Step', 'buddypress' ); ?><span></span></span>
                                    </a>

						        <?php endif; ?>

						        <?php /* Next Button */ ?>
						        <?php if ( !bp_is_last_group_creation_step() && !bp_is_first_group_creation_step() ) : ?>
                                    <button type="submit" class="action-btn next-btn" id="group-creation-next" name="save">
                                        <span class="p"></span>
                                        <span class="t"><?php _e( 'Next', 'compliancetest' ); ?></span>
                                    </button>

						        <?php endif;?>

						        <?php /* Create Button */ ?>
						        <?php if ( bp_is_first_group_creation_step() ) : ?>

							        <input type="submit" class="action-btn process-btn" value="<?php _e( 'Create Group and Continue', 'buddypress' ); ?>" id="group-creation-create" name="save" />

						        <?php endif; ?>

						        <?php /* Finish Button */ ?>
						        <?php if ( bp_is_last_group_creation_step() ) : ?>
                                    <button type="submit" class="action-btn process-btn" id="group-creation-finish" name="save">
                                        <span class="p"></span>
                                        <span class="t"><?php _e( 'Confirm', 'compliancetest' ); ?></span>
                                    </button>

						        <?php endif; ?>
                                <div class="clear"></div>
					        </div>

				        <?php endif;?>

				        <?php do_action( 'bp_after_group_creation_step_buttons' ); ?>

				        <?php /* Don't leave out this hidden field */ ?>
				        <input type="hidden" name="group_id" id="group_id" value="<?php bp_new_group_id(); ?>" />

				        <?php do_action( 'bp_directory_groups_content' ); ?>

			        </div><!-- .item-body -->

			        <?php do_action( 'bp_after_create_group' ); ?>
                </div>
            </div>
		</form>
		
		<?php do_action( 'bp_after_create_group_content_template' ); ?>

		</div><!-- .padder -->
	</div><!-- #content -->
</div>

<?php get_footer( 'buddypress' ); ?>
