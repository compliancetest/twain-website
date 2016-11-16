<?php
/*
 * Template Name: Registration page
 */

if (is_user_logged_in()) {
    wp_redirect('/');
    exit();
}

get_header();
$invitationData = $wpdb->get_row($wpdb->prepare("SELECT * FROM community_invitations WHERE id = %s AND status = 1", @$_GET['GUID']));
?>
<div class="content container" id="login_page_form">
    <div class="user_border user_border2 radius6" id="reg" style="margin: 0 auto">
        <div class="">
            <div class="reg_user">Register New User</div>
            <form id="formreg" action="" method="post">
                <div class="field">
                    <label for="first_name_id">First Name</label>
                    <input type="text" class="required" title="" name="first_name" id="first_name_id"
                           autocomplete="off" <?php if ($invitationData): ?> value="<?php echo $invitationData->first_name; ?>" <?php endif; ?>>
                </div>
                <div class="field">
                    <label for="last_name_id">Last Name</label>
                    <input type="text" class="required" title="" name="last_name" id="last_name_id"
                           autocomplete="off" <?php if ($invitationData): ?> value="<?php echo $invitationData->last_name; ?>" <?php endif; ?>>
                </div>
                <div class="clear"></div>

                <div class="field">
                    <label for="email_id">Email</label>
                    <input type="email" class="required" title="" name="user_email" id="email_id"
                           autocomplete="off" <?php if ($invitationData): ?> value="<?php echo $invitationData->invitation_email; ?>" <?php endif; ?>>
                </div>
                <div class="field">
                    <label for="confirm_email_id">Confirm Email</label>
                    <input type="text" class="required" title="" name="user_email_confirm"
                           id="confirm_email_id" autocomplete="off" <?php if ($invitationData): ?> value="<?php echo $invitationData->invitation_email; ?>" <?php endif; ?>>
                </div>
                <div class="clear"></div>

                <div class="field">
                    <label for="organisation_id">Organization Key (optional)</label>

                    <div class="has-field-tooltip" style="width: 165px;">
                        <input type="text" class="field-tooltip" title="" autocomplete="off"
                               name="organisation_key" id="organisation_key"
                               data-tooltip-content="If your organisation is already registered on <?php echo get_site_title(); ?>, ask your administrator for your organisation key to immediately become a member of your organisation. If not, just leave this field blank for now.">
                    </div>
                </div>
                <div class="field">
                    <label for="contact_phone_id">Contact Phone Number</label>
                    <input type="text" class="required" title="" name="contact_phone"
                           id="contact_phone_id" autocomplete="off">
                </div>
                <div class="clear"></div>

                <div class="field">
                    <label for="user_login_id">Username</label>
                    <input type="text" class="required" title="" name="user_login" id="user_login_id"
                           autocomplete="off">
                </div>
                <div class="clear"></div>

                <div class="field">
                    <label for="user_pass">Password</label>

                    <div class="has-field-tooltip" style="width: 165px; text-align: left;">
                        <input type="password" class="field-tooltip required" title="" name="user_pass"
                               id="user_pass_id" autocomplete="off"
                               data-tooltip-content="<ul type='circle'><li>&#9679 8 characters long at minimum</li><li>&#9679 Includes upper and lowercase characters</li><li>&#9679 Includes at least 1 special character within the string</li><li>&#9679 Includes at least 1 number within the string</li></ul>">
                    </div>
                </div>
                <div class="field">
                    <label for="user_pass_confirm_id">Confirm Password</label>

                    <div class="has-field-tooltip" style="width: 165px; text-align: left;">
                        <input type="password" class="field-tooltip required" title=""
                               name="user_pass_confirm" id="user_pass_confirm_id" autocomplete="off"
                               data-tooltip-content="<ul type='circle'><li>&#9679 8 characters long at minimum</li><li>&#9679 Includes upper and lowercase characters</li><li>&#9679 Includes at least 1 special character within the string</li><li>&#9679 Includes at least 1 number within the string</li></ul>">
                    </div>
                </div>
                <div class="clear"></div>

                <?php if ($invitationData): ?>
                    <input type="hidden" name="invitation_id" value="<?php echo $invitationData->id; ?>">
                    <input type="hidden" id="recaptcha_response_field" name="recaptcha_response_field" value="<?php echo $invitationData->id; ?>">
                <?php else: ?>
                    <div class="field captcha-field">
                        <?php echo recaptcha_get_html(RECAPTCHA_PUBLIC_KEY, null, true); ?>
                    </div>
                <?php endif; ?>
                <div class="field width90P">
                    <input type="checkbox" name="acc_tc" id="acc_tc_id" class="cursor-pointer"><label for="acc_tc">I accept the
                        <a href="#site-terms-box" data-type="inline" id="agree_terms" cp-closeWhenClickOveraly="0">Terms &
                            Conditions.</a></label>
                </div>
                <div class="clear"></div>

                <input type="hidden" name="redirect_to"
                       value="<?php echo get_settings('home'); ?>/registration-succeeded"/>
                <input type="hidden" name="cp-action"
                       value="<?php echo wp_create_nonce('register') ?>"/>
                <!--<input type="submit" name="wp_register" class="button" value="Register Me!" tabindex="100" id="reg_user"/>-->
                <div id="reg_user">Register Me</div>
                <div class="space10"></div>
                <div class="message" style="display: none"></div>
                <div class="loading"></div>
            </form>
        </div>
    </div>
</div>
<div id="site-terms-box" style="display: none" class="popup-box">
    <div class="popup-box-header radius6 noradiusbottom">Terms and Conditions</div>
    <div class="popup-box-content">
        <p>
            <?php
            //Getting Terms & Conditions
            $terms = get_page_by_path('terms-conditions');
            echo apply_filters('the_content', $terms->post_content);
            ?>
        </p>
    </div>
    <div class="popup-box-footer radius6 noradiustop">
        <a href="#" class="action-btn cancel-btn"><span
                class="p"></span><span class="t">CANCEL</span></a>
        <a href="#" class="action-btn process-btn"><span
                class="p"></span><span class="t">AGREE</span></a>

        <div class="clear"></div>
    </div>
</div>
<?php get_footer(); ?>
