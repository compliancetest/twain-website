<?php
/**
* Profile - My Pictures
*/
if(!defined('ABSPATH'))
    die('Invalid Request!');
    
?>
<div class="column left three_fifths nopadding">
    <div class="grid-box" id="my_avatar">
        <div class="grid-box-header">
            <h5>My Picture</h5>
            <div class="clear"></div>
        </div>
        <div class="grid-box-body">
            <div class="grid-row">
              <form action="/" method="post" id="avatar-upload-form" class="standard-form" enctype="multipart/form-data">
                <?php if ( 'crop-image' == bp_get_avatar_admin_step() ){ ?> <!-- Crop Image -->
                    <p><?php _e( 'Crop Your New Avatar', 'buddypress' ); ?></p>

                    <img src="<?php bp_avatar_to_crop(); ?>" id="avatar-to-crop" class="avatar" alt="<?php _e( 'Avatar to crop', 'buddypress' ); ?>" />

                    <div id="avatar-crop-pane" class="left">
                        <img src="<?php bp_avatar_to_crop(); ?>" id="avatar-crop-preview" class="avatar" alt="<?php _e( 'Avatar preview', 'buddypress' ); ?>" />
                    </div>

                    <a href="#" class="action-btn submit-btn process-btn right" style="margin-top: 120px; margin-right: 10px;"><span class="p"></span><span class="t">Crop Image</span></a>
                    <div class="clear"></div>
                    <div class="space10"></div>
                    <input type="hidden" name="avatar-crop-submit" id="avatar-crop-submit" value="<?php _e( 'Crop Image', 'buddypress' ); ?>" />

                    <input type="hidden" name="image_src" id="image_src" value="<?php bp_avatar_to_crop_src(); ?>" />
                    <input type="hidden" id="x" name="x" value="" />
                    <input type="hidden" id="y" name="y" value="" />
                    <input type="hidden" id="w" name="width" value="" />
                    <input type="hidden" id="h" name="height" value="" />

                    <?php wp_nonce_field( 'bp_avatar_cropstore' ); ?>
                <?php }else{?> <!-- Upload Avatar -->
                    <div class="grid-cell width30P">
                        <a href="<?php bp_loggedin_user_link(); ?>">
                            <?php $userAvatar = get_avatar($current_user->user_email, 150);?>
                            <?php if(strpos($userAvatar, 'mystery-man') !== false):?>
                                <img src="<?php echo DEFAULT_AVATAR;?>" class="avatar user-1-avatar avatar-150 photo" alt="Avatar" width="150" height="150">
                            <?php else:?>
                                <?php echo get_avatar($current_user->user_email, 150);  ?>
                            <?php endif;?>
                        </a>
                    </div>
                    <div class="grid-cell width70P">
                        <p>Your avatar will be used on your profile and throughout the site.</p>
                        <?php if($user_status != 3){?>            
                        <p>Click below to select a JPG, GIF or PNG format photo from your computer and then click 'Upload Image' to proceed.</p>
                        <p>
                            
                                <input type="file" name="file" id="file" class="left input-file" file-type="image" file-extensions="(.jpg, .png, .gif or .jpeg file)" />
                                <div class="clear"></div>
                                <br />                                                                
                                <a href="#" class="action-btn submit-btn upload-btn"><span class="p"></span><span class="t">Upload Image</span></a>
                                <?php if ( bp_get_user_has_avatar($current_user->ID) ){ ?>
                                <a href="<?php echo get_permalink()?>?cp-action=<?php echo wp_create_nonce('delete-avatar')?>" class="action-btn delete-btn icon-btn left15"><span class="p"></span><span class="t">Delete My Avatar</span></a>
                                <?php } ?>
                                <input type="hidden" name="action" id="action" value="bp_avatar_upload" />
                                <input type="hidden" name="upload" id="action" value="<?php _e( 'Upload Image', 'buddypress' ); ?>" />
                                <?php wp_nonce_field( 'bp_avatar_upload' ); ?>                                    
                        </p>
                        <?php } ?>
                    </div>
                <?php } ?>
                <div class="clear"></div>
              </form>  
            </div>
        </div>
    </div>
</div>
<?php $my_pictures_desc = get_post_meta($post->ID, 'my_pictures_desc', true);?>
<?php if($my_pictures_desc): ?>
<div class="right two_fifths">
    <div class="gray_message_box radius9 light_gray_txt">
        <div class="indicator"></div>
        <?php echo $my_pictures_desc; ?>
    </div>
</div>
<?php endif; ?>