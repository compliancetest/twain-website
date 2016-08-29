<?php
/**
 * Profile - My Pictures
 */
if (!defined('ABSPATH'))
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
                <form action="" method="post" id="avatar-upload-form" class="standard-form"
                      enctype="multipart/form-data">
                        <div class="grid-cell width30P">
                            <a href="<?php bp_loggedin_user_link(); ?>">
                                <?php $userAvatar = getUserAvatar(get_current_user_id()); ?>
                                <?php if (!$userAvatar): ?>
                                    <img src="<?php echo DEFAULT_AVATAR; ?>"
                                         class="avatar user-1-avatar avatar-150 photo" alt="Avatar" width="150"
                                         height="150">
                                <?php else: ?>
                                    <img src="<?php echo $userAvatar; ?>"
                                         class="avatar user-1-avatar avatar-150 photo" alt="Avatar" width="150"
                                         height="150">
                                <?php endif; ?>
                            </a>
                        </div>
                        <div class="grid-cell width70P">
                            <p>Your avatar will be used on your profile and throughout the site.</p>
                            <?php if($user_status != 3){?>
                        <p>Click below to select a JPG, GIF or PNG format photo from your computer and then click 'Upload Image' to proceed.</p>
                        <p>
                            
                                <input type="file" name="file" id="file" class="left input-file" file-type="image" file-extensions="(.jpg, .png, .gif or .jpeg file)" style="cursor: pointer;"/>
                                <div class="clear"></div>
                                <br />                                                                
                                <a href="#" class="action-btn submit-btn upload-btn greyed-out-btn subm_send"><span class="p"></span><span class="t">Upload Image</span></a>
                                <?php if ( getUserAvatar($current_user->ID) ){ ?>
                                    <a href="<?php echo get_permalink()?>?cp-action=<?php echo wp_create_nonce('delete-avatar')?>" class="action-btn delete-btn icon-btn left15"><span class="p"></span><span class="t">Delete My Avatar</span></a>
                                <?php } ?>
                                <input type="hidden" name="action" id="action" value="bp_avatar_upload" />
                                <input type="hidden" name="upload" id="action" value="<?php _e( 'Upload Image', 'buddypress' ); ?>" />
                                <?php wp_nonce_field( 'bp_avatar_upload' ); ?>                                    
                        </p>
                        <?php } ?>
                        </div>
                    <div class="clear"></div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $my_pictures_desc = get_post_meta($post->ID, 'my_pictures_desc', true); ?>
<?php if ($my_pictures_desc): ?>
    <div class="right two_fifths">
        <div class="gray_message_box radius9 light_gray_txt">
            <div class="indicator"></div>
            <?php echo $my_pictures_desc; ?>
        </div>
    </div>
<?php endif; ?>
<script>
    jQuery(document).ready(function () {

        jQuery('.greyed-out-btn').on('click', function (e) {
            e.preventDefault();
            return false;
        });

        jQuery('#file').on('change', function () {
            if (jQuery('#file').val()) {
                jQuery('.greyed-out-btn').off('click');
                jQuery('.subm_send').removeClass('greyed-out-btn');
                jQuery('.subm_send').on('click', function () {
                    jQuery('#avatar-upload-for').submit();
                });
            } else {
                jQuery('.subm_send').addClass('greyed-out-btn');
            }
        });
    });
</script>
