<?php
    /**
    * Groups Admin Page
    */
?>
<div id="group_admin_page" class="tab-content white_bcg column">
    <p><?php echo MESSAGE_WARNING_COMMUNITY_ADMIN; ?></p>
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
                            <label>Community Name</label>
                            <span class="input-holder"><input type="text" name="group-name" id="group-name" value="<?php bp_group_name(); ?>" aria-required="true" class="input" /></span>
                            <div class="clear"></div>
                        </div>
                        <div class="field-row">
                            <label>Community Description</label>
                            <span class="input-holder"><textarea name="group-desc" id="group-desc" aria-required="true" class="textarea"><?php bp_group_description_editable(); ?></textarea></span>
                            <div class="clear"></div>
                        </div>
                        <div class="field-row">
                            <label>Terms and Conditions</label>
                            <span class="input-holder"><textarea name="terms_and_conditions" id="terms_and_conditions" aria-required="true" class="textarea"><?php echo groups_get_groupmeta(bp_get_group_id(), 'terms_and_conditions'); ?></textarea></span>
                            <div class="clear"></div>
                        </div>
                        <div class="field-row">
                            <label>License Agreements</label>
                            <span class="input-holder"><textarea name="license_agreements" id="license_agreements" aria-required="true" class="textarea"><?php echo groups_get_groupmeta(bp_get_group_id(), 'license_agreements'); ?></textarea></span>
                            <div class="clear"></div>
                        </div>
                        <div class="field-row">
                            <label>Obligation to Claim</label>
                            <span class="input-holder"><textarea name="obligation_for_claim" id="obligation_for_claim" aria-required="true" class="textarea"><?php echo groups_get_groupmeta(bp_get_group_id(), 'obligation_for_claim'); ?></textarea></span>
                            <div class="clear"></div>
                        </div>
                        
                        <div class="field-row">
                            <label>Notification Email Content</label>
                            <span class="input-holder"><textarea name="notification_email_of_changes" id="notification_email_of_changes" aria-required="true" class="textarea"><?php echo groups_get_groupmeta(bp_get_group_id(), 'notification_email_of_changes'); ?></textarea></span>
                            <div class="clear"></div>
                        </div>
                        
                        <div class="field-row">
                            <label><?php _e( 'Notify community members of changes via email', 'buddypress' ); ?></label>
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
                    <div class="column grid-row">
                        <div class="field-row">
                            <div class="grid_cell current_avatar">
                                <?php if(bp_get_group_has_avatar()){ ?>
                                <?php echo bp_get_group_avatar(array('width' => 98, 'height' => 98))?>
                                <?php }else{ ?>
                                <img src="<?php echo CHILD_TEMPLATE_DIRECTORY?>/images/default-group-avatar.png" title="Default Avatar" />                                
                                <?php } ?>
                                <?php wp_nonce_field( 'bp_avatar_upload' ); ?>
                            </div>
                            <div class="grid_cell width300 left15">
                                <p class="field-row"><?php _e("Upload an image to use as an avatar for this community. The image will be shown on the main community page, and in search results.", 'buddypress'); ?></p>
                                <p class="field-row"><?php _e("Click below to select a JPG, GIF or PNG format photo from your computer and then click 'Upload Image' to proceed.") ?></p>
                                <input type="file" name="file" id="file" class="input-file"  file-type="image" file-extensions="(.jpg, .png, .gif or .jpeg file)" />
                                <div class="clear space10"></div>
                                <a href="#" class="action-btn process-btn no-submit" id="upload-image-btn"><span class="p"></span><span class="t">Upload Image</span></a>
                                <?php if(bp_get_group_has_avatar()){ ?>
                                    <a href="<?php echo bp_get_group_avatar_delete_link()?>" class="action-btn delete-btn left10"><span class="p"></span><span class="t">Delete Image</span></a>
                                <?php } ?>
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
                            <font color='#ce1515'>WARNING</font>: Deleting this community will completely remove ALL content associated with it. There is no way back, please be careful with this option.
                        </div>
                        <div class="field-row">
                            <label><input type="checkbox" name="delete-group-understand" id="delete-group-understand" value="1" /> <?php _e( 'I understand the consequences of deleting this community.', 'buddypress' ); ?></label>
                        </div>    
                        <?php do_action( 'bp_after_group_delete_admin' ); ?>
                        <div class="btn-row">
                            <input type="hidden" value="<?php _e( 'Delete Community', 'buddypress' ); ?>" id="delete-group-button" name="delete-group-button" />
                            <a href="#" class="action-btn delete-btn"><span class="p"></span><span class="t">DELETE COMMUNITY</span></a>
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
        <!-- Profile -->
        <div class="grid-box" id="group_profile_types_box">
            <div class="grid-box-header">
                <h5>Profile Types</h5>
            </div>            
            <?php
                if(isset($_POST['td-action']) && wp_verify_nonce($_POST['td-action'], 'save-profile-type'))
                    $isEditType = true;
                else
                    $isEditType = false;
            ?>
            <div class="grid-box-body" id="profile-type-list" <?php if($isEditType){ ?> style="display:none" <?php } ?>>
            <?php
                $profileTypes = getCommunityProfileTypes(bp_get_group_id());            
            ?>  
                <div class="grid-box table-box">
                    <div class="grid-box-body">
                        <div class="thead tr">
                           <div class="td td-profile-title">Name</div>
                           <div class="td td-profile-instances">Instances</div>
                           <div class="td td-profile-action">Action</div>
                           <div class="clear"></div>
                       </div>
                       <div class="tbody">
                            <?php if(!$profileTypes){ ?>
                            <div class="tr"><div class="td td-full">No data found</div><div class="clear"></div></div>
                            <?php } ?>
                            <?php foreach($profileTypes as $row) {?>
                            <div class="tr">
                               <div class="td td-profile-title">
                                <?php echo $row->title?>
                                <?php
                                    $pJSON = json_decode(base64_decode($row->schema));
                                    if($pJSON->Version)
                                    {
                                        $version = array();
                                        foreach(get_object_vars($pJSON->Version) as $k=>$v)      
                                        {
                                            $version[] = $v;
                                        }
                                        echo " v" . implode(".", $version);
                                    }
                                ?>
                               </div>
                               <div class="td td-profile-instances"><?php echo $row->instances?></div>
                               <div class="td td-profile-action">
                                   <a href="<?php echo bp_get_group_admin_permalink()?>?td-action=<?php echo wp_create_nonce('download-profile-type')?>&type_id=<?php echo $row->id?>&community_id=<?php echo bp_get_group_id()?>" class="action-btn icon-btn download-btn"><span class="p"></span><span class="simple_tooltip radius6 no-wrap">Download Profile Type<span></span></span></a>
                                   <a href="<?php echo bp_get_group_admin_permalink()?>?td-action=<?php echo wp_create_nonce('edit-profile-type')?>&type_id=<?php echo $row->id?>&community_id=<?php echo bp_get_group_id()?>" class="action-btn blue-edit-btn icon-btn left5 profile-type-edit-btn"><span class="p"></span><span class="simple_tooltip radius6">Edit Profile Type<span></span></span></a>
                                   <a href="<?php echo bp_get_group_admin_permalink()?>?td-action=<?php echo wp_create_nonce('delete-profile-type')?>&type_id=<?php echo $row->id?>&community_id=<?php echo bp_get_group_id()?>" class="action-btn blue-delete-btn icon-btn left5 profile-type-delete-btn"><span class="p"></span><span class="simple_tooltip radius6 no-wrap">Remove Profile Type<span></span></span></a>
                               </div>     
                               <div class="clear"></div>                               
                            </div>
                            <?php } ?>
                            
                       </div>                          
                    </div>                    
                </div>
                <div class="column">
                    <a href='<?php echo bp_group_admin_permalink()  ?>?td-action=<?php echo wp_create_nonce('edit-profile-type')?>&community_id=<?php bp_group_id() ?>' class="action-btn process-btn" id="add-profile-type-btn"><span class="p"></span><span class="t">Add New Profile Type</span></a>
                    <div class="clear"></div>
                </div>
            </div>
            
            <div id="edit-profile-type" <?php if($isEditType){ ?> style="display: block" <?php } ?>>
                <form name="profileTypeForm" id="profileTypeForm" action="" enctype="multipart/form-data" method="post">
                    <div class="grid-box-body column">                    
                        <h5><?php echo $isEditType && $_POST['type_id'] ? 'Edit' : 'Add New'?> Profile Type</h5>
                        <div class="field-row">
                            <label>Enter Schema:</label>
                            <textarea name="profile_type_text" id="profile_type_text" class="textarea"><?php echo isset($_POST['profile_type_text']) ? stripslashes($_POST['profile_type_text']) : '' ?></textarea>
                        </div>      
                        <div class="field-row">
                            <label>Or Select File:</label>
                            <div class="clear"></div>
                            <input type="file" name="profile_type_file" id="profile_type_file" class="input_file" value="" file-type="doc" file-extensions="(.txt or .json file)" />
                            <div class="clear"></div>
                        </div>                        
                        <div class="clear"></div>
                        
                        <input type="hidden" name="community_id" value="<?php bp_group_id() ?>" />
                        <input type="hidden" name="type_id" id="type_id" value="<?php if($isEditType){ echo $_POST['type_id']; } ?>" />
                        <input type="hidden" name="td-action" value="<?php echo wp_create_nonce('save-profile-type')?>" />                        
                    </div>
                    <div class="grid-box-footer">
                        <div class="btn-row">
                            <a href="#" class="action-btn process-btn"><span class="p"></span><span class="t">SAVE</span></a>
                            <a href="#" class="action-btn cancel-btn left10"><span class="p"></span><span class="t">Cancel</span></a>
                            <div class="clear"></div>
                        </div>
                    </div>                    
                </form>
            </div>
        </div>
        <script type="text/javascript">
            jQuery(document).ready(function($){                
                jQuery('#add-profile-type-btn').click(function(){
                    jQuery('#profileTypeForm h5').html('Add New Profile Type');
                    jQuery('#profileTypeForm .message').remove();
                    jQuery('#profileTypeForm #profile_type_text').val('');
                    jQuery('#profileTypeForm #profile_type_file').val('');
                    jQuery('#edit-profile-type').fadeIn();
                    jQuery('#profile-type-list').hide();
                    return false;
                });                
                
                jQuery('#edit-profile-type .cancel-btn').click(function(){                    
                    jQuery('#profile-type-list').fadeIn();
                    jQuery('#edit-profile-type').hide();
                    return false;
                });                
                
                jQuery('#profileTypeForm').submit(function(){
                    jQuery('#profileTypeForm .message').remove();
                    if(jQuery('#profile_type_file').val() == '' && jQuery('#profile_type_text').val() == '')
                    {
                        jQuery('#profileTypeForm .grid-box-footer .btn-row').prepend('<p class="message error">Please enter schema or select a schema file.</p>');
                        return false;
                    }
                    jQuery('#save-profile-type-box .loading b').html('SAVING PROFILE TYPE');
                    jQuery('#save-profile-type-box .loading').show();
                    return true;                    
                });
                
                jQuery('.profile-type-edit-btn').click(function(){
                    jQuery('#profileTypeForm h5').html('Edit Profile Type');
                    jQuery('#profileTypeForm #profile_type_text').val('');
                    jQuery('#profileTypeForm #profile_type_file').val('');
                    jQuery('#edit-profile-type').fadeIn();
                    jQuery('#profile-type-list').hide();                    
                    jQuery('#edit-profile-type .loading b').html('READING PROFILE TYPE');
                    jQuery('#edit-profile-type .loading').show();
                    jQuery('#profileTypeForm .message').remove();
                    var link = jQuery(this).attr('href');
                    jQuery.ajax({
                        url: link,
                        dataType: 'xml',
                        success: function(rsp)
                        {
                            if(jQuery(rsp).find('status').text() == 'success')
                            {
                                jQuery('#profileTypeForm #profile_type_text').val(jQuery(rsp).find('schema').text());
                                jQuery('#profileTypeForm #type_id').val(jQuery(rsp).find('id').text());
                            }else{
                                jQuery('#profileTypeForm .grid-box-footer .btn-row').prepend('<p class="message error">' + jQuery(rsp).find('msg').text() + '</p>');
                            }
                            jQuery('#edit-profile-type .loading').hide();
                        },
                        error: function(err)
                        {
                            jQuery('#profileTypeForm .grid-box-footer .btn-row').prepend('<p class="message error">' + err.responseText + '</p>');
                            jQuery('#edit-profile-type .loading').hide();
                        }
                    })
                    return false;
                })
                
                $('#group_admin_page textarea:visible').redactor({
                    air:true,
                    minHeight: 120
                })
            })
        </script>
        <div class="space20"></div>
        
        
        <!-- Memebers -->
        <div class="grid-box" id="group_members_box">
            <div class="grid-box-header">
                <h5>Members</h5>
            </div>
            
            <div class="grid-box-body">
                <div class="column nopaddingbottom">
                    <form name="group-requests-form" id="group-requests-form" action="<?php bp_group_admin_form_action('membership-requests')?>" method="post" enctype="multipart/form-data" role="main">
                    <?php
                        global $requests_template;
                    ?>
                    <?php if(bp_group_has_membership_requests()){ ?>
                    <p class="nomarginbottom">The following persons wants to join the Community:</p>
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
                    <?php
                        if($requests_template->pag_links):
                    ?>
                        <div class="pagination-wrapper">
                            <div class="pagination">
                            <?php
                                echo $requests_template->pag_links;                                
                            ?>
                            </div>
                        </div>
                        <br />
                    <?php
                        endif;
                    ?>
                    <?php }else{ ?>
                        <p><?php _e( 'There are no pending membership requests.', 'buddypress' ); ?></p>
                    <?php } ?>
                    </form>                    
                </div>
            </div>
            <div class="grid-box-body" id="group_members_body">
            <form name="group-members-form" id="group-members-form" action="<?php bp_group_admin_form_action('manage-members')?>" method="post" enctype="multipart/form-data" role="main">
                <div class="space20"></div>
                <div class="nav left15">
                    <ul>
                        <li><a href="#" data-action="ban">Kick &amp; Ban</a></li>
                        <li><a href="#" data-action="promote_to_mod">Promote to Support Staff</a></li>
                        <li><a href="#" data-action="promote_to_admin">Promote to Admin</a></li>
                        <li class="last-li"><a href="#" data-action="remove_from_group">Remove</a></li>
                    </ul>
                    <div class="clear"></div>
                </div>
                <div class="clear"></div>
                <!-- Administrators -->
                <?php global $members_template; ?>
                <?php if(bp_has_members( 'per_page=10&include='. bp_group_admin_ids())){?>        
                <div class="field-row">
                    <p><b>Administrator</b></p>
                    <ul id="admins-list" class="member-list">                    
                        <?php while ( bp_members() ) : bp_the_member(); ?>
                        <?php                            
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
                
                <?php if($members_template->pag_links): ?>
                <div class="pagination-wrapper">
                    <div class="pagination">
                        <?php
                            echo $members_template->pag_links;
                        ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php } ?>
                
                <!-- Moderators -->
            <?php if(bp_group_has_moderators()){ ?>
                <?php if(bp_has_members( '&include='. bp_group_mod_ids())){?>        
                <div class="field-row">
                    <p><b>Support Staff</b></p>
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
                <?php if($members_template->pag_links): ?>
                <div class="pagination-wrapper">
                    <div class="pagination">
                        <?php
                            echo $members_template->pag_links;
                        ?>
                    </div>
                </div>
                <?php endif; ?>
                <?php } ?>
            <?php } ?>
                <!-- Members -->
              <?php if(bp_group_has_members('per_page=15&exclude_banned=false')){ ?>                
                <div class="field-row">
                    <p><b>Members</b></p>
                    
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
                <?php if(bp_group_member_needs_pagination()){ ?>
                <div class="pagination-wrapper">
                    <!--<div id="member-count" class="pag-count">
                        <?php bp_group_member_pagination_count(); ?>
                    </div>-->
                    <div class="pagination no-ajax">                        
                        <div id="member-admin-pagination" class="pagination-links">
                            <?php bp_group_member_admin_pagination(); ?>
                        </div>
                    </div>
                </div>
                <?php } ?>       
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
                                <b><?php _e( 'This is a public community', 'buddypress' ); ?></b>
                            </label>                        
                            <ul>
                                <li><?php _e( 'Any site member can join this community.', 'buddypress' ); ?></li>
                                <li><?php _e( 'This community will be listed in the communities directory and in search results.', 'buddypress' ); ?></li>
                                <li><?php _e( 'Community content and activity will be visible to any site member.', 'buddypress' ); ?></li>
                            </ul>
                        </div>
                        <div class="field-row">
                            <label>
                                <input type="radio" name="group-status" value="private"<?php bp_group_show_status_setting( 'private' ); ?> />
                                <b><?php _e( 'This is a private community', 'buddypress' ); ?></b>
                            </label>
                            <ul>
                                <li><?php _e( 'Only users who request membership and are accepted can join the community.', 'buddypress' ); ?></li>
                                <li><?php _e( 'This community will be listed in the communities directory and in search results.', 'buddypress' ); ?></li>
                                <li><?php _e( 'Community content and activity will only be visible to members of the community.', 'buddypress' ); ?></li>
                            </ul>
                        </div>
                        <div class="field-row">
                            <label>
                                <input type="radio" name="group-status" value="hidden"<?php bp_group_show_status_setting( 'hidden' ); ?> />
                                <b><?php _e( 'This is a hidden community', 'buddypress' ); ?></b>
                            </label>
                            <ul>
                                <li><?php _e( 'Only users who are invited can join the community.', 'buddypress' ); ?></li>
                                <li><?php _e( 'This community will not be listed in the communities directory or search results.', 'buddypress' ); ?></li>
                                <li><?php _e( 'Community content and activity will only be visible to members of the community.', 'buddypress' ); ?></li>
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
                    <h5>Community Invitations</h5>
                </div>
                <div class="grid-box-body">
                    <div class="column">                   
                        <div class="field-row">
                            Which members of this community are allowed to invite others?
                        </div>
                        <div class="field-row">
                            <label> 
                                <input type="radio" name="group-invite-status" value="members"<?php bp_group_show_invite_status_setting( 'members' ); ?> /> 
                                <b><?php _e( 'All community members', 'buddypress' ); ?></b> 
                            </label>                             
                        </div>
                        <div class="field-row">
                            <label> 
                                <input type="radio" name="group-invite-status" value="mods"<?php bp_group_show_invite_status_setting( 'mods' ); ?> /> 
                                <b><?php _e( 'Community admins and supports only', 'buddypress' ); ?></b>
                            </label>
                        </div>
                        <div class="field-row">
                            <label> 
                                <input type="radio" name="group-invite-status" value="admins"<?php bp_group_show_invite_status_setting( 'admins' ); ?> /> 
                                <b><?php _e( 'Community admins only', 'buddypress' ); ?></b> 
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
        <div class="space20"></div>
        <!-- Article Settings -->
        <?php
            
            $wiki_settings = groups_get_groupmeta( bp_get_group_id(), 'bp-docs' );
            
            $group_wiki_enable = empty( $wiki_settings['group-enable'] ) ? false : true;

            $can_create_wiki = empty( $wiki_settings['can-create'] ) ? false : $wiki_settings['can-create'];
        ?>
        <div class="grid-box" id="group_article_settings_box">
            <form name="group-article-settings-form" id="group-article-settings-form" action="<?php bp_group_admin_form_action('wiki')?>" method="post" enctype="multipart/form-data" role="main">
                <div class="grid-box-header">
                    <h5>Community Articles</h5>
                </div>
                <div class="grid-box-body">
                    <div class="column">                   
                        <div class="field-row">
                            <label for="bp-docs[group-enable]"> <input type="checkbox" name="bp-docs[group-enable]" id="bp-docs-group-enable" value="1" <?php checked( $group_wiki_enable, true ) ?> /> <?php _e( 'Enable BuddyPress Docs for this group', 'bp-docs' ) ?></label>
                        </div>
                        <div id="community-doc-options">
                            <div class="field-row">
                                <label for="bp-docs[can-create-admins]"><?php _e( 'Minimum role to associate Article with this community:', 'bp-docs' ) ?></label>
                            </div>
                            <div class="field-row">
                                <select name="bp-docs[can-create]" class="select">
                                    <option value="admin" <?php selected( $can_create_wiki, 'admin' ) ?>><?php _e( 'Community Admin', 'bp-docs' ) ?></option>
                                    <option value="mod" <?php selected( $can_create_wiki, 'mod' ) ?>><?php _e( 'Community Support', 'bp-docs' ) ?></option>
                                    <option value="member" <?php selected( $can_create_wiki, 'member' ) ?>><?php _e( 'Community Member', 'bp-docs' ) ?></option>
                                </select>                           
                            </div>              
                        </div>
                        
                    </div>
                </div>        
                <div class="grid-box-footer">
                    <div class="btn-row">
                        <a href="#" class="action-btn process-btn"><span class="p"></span><span class="t">SAVE</span></a>
                        <div class="clear"></div>
                    </div>
                </div>
                <?php 
                    wp_nonce_field( 'groups_edit_save_wiki' );
                    wp_nonce_field( 'bp_group_extension_wiki_edit', '_bp_group_edit_nonce_wiki' );
                ?>                
                <input type="hidden" name="group-id" id="group-id" value="<?php bp_group_id(); ?>" />
                <input type="hidden" name="save" id="save" value="Save Changes" />
            </form>
        </div>
        <div class="space20"></div>
        <!-- Generate JSON -->
        <div class="grid-box" id="group_generate_json_box">
            <form name="group-generate-json-form" id="group-generate-json-form" action="<?php bp_group_admin_form_action('group-generate-json')?>" method="post" enctype="multipart/form-data" role="main">
                <div class="grid-box-header">
                    <h5>Generate JSON</h5>
                </div>
                <div class="grid-box-body">
                    <div class="column">
                        <input type="file" name="profile_excel_file" id="profile_excel_file" class="input-file"  file-type="image" file-extensions="(.xls, .xlsx file)" />
                        <a href="#" class="action-btn process-btn no-submit left10 top3" id="upload-profile-excel-btn"><span class="p"></span><span class="t">Generate JSON</span></a>
                        <div class="clear"></div>
                        <input type="hidden" name="action" id="generate-json-action" value="bp_generate_json" />
                        <input type="hidden" name="upload" id="generate-json-upload" value="Upload Excel" />
                    </div>
                </div>
                <?php if (isset($_SESSION['admin_json_zip_link']) && $_SESSION['admin_json_zip_link'] != ''): ?>
                <div class="grid-box-body" id="group-generated-json">
                    <a href="<?php echo $_SESSION['admin_json_zip_link']; ?>">json_profiles.zip</a>
                </div>
                <?php endif; ?>
            </form>
        </div>
        <div class="space20"></div>
        <!-- Generate FVS -->
        <div class="grid-box" id="group_generate_fvs_box">
            <form name="group-generate-fvs-form" id="group-generate-fvs-form" action="<?php bp_group_admin_form_action('group-generate-fvs')?>" method="post" enctype="multipart/form-data" role="main">
                <div class="grid-box-header">
                    <h5>Generate FVS</h5>
                </div>
                <div class="grid-box-body">
                    <div class="column">
                        <input type="file" name="profile_excel_file" id="profile_excel_file" class="input-file"  file-type="image" file-extensions="(.xls, .xlsx file)" />
                        <a href="#" class="action-btn process-btn no-submit left10 top3" id="upload-profile-excel-btn"><span class="p"></span><span class="t">Generate FSV</span></a>
                        <div class="clear"></div>
                        <input type="hidden" name="action" id="generate-fvs-action" value="bp_generate_fvs" />
                        <input type="hidden" name="upload" id="generate-fvs-upload" value="Upload Excel" />
                    </div>
                </div>
                <?php if (isset($_SESSION['admin_json_zip_link']) && $_SESSION['admin_json_zip_link'] != ''): ?>
                <div class="grid-box-body" id="group-generated-fvs">
                    <a href="<?php echo $_SESSION['admin_json_zip_link']; ?>">json_profiles.zip</a>
                </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
    <div class="clear"></div>
</div>
