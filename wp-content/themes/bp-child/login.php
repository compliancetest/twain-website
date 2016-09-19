<?php
/*
 * Template Name: Login page
 */

if (is_user_logged_in()) {
    wp_redirect('/');
    exit();
}

get_header();

$userIP = \LoginAttempts\LoginAttempts::getUserIP();
$attempts = \LoginAttempts\LoginAttempts::getAttempts($userIP);
?>
<div class="content container" id="login_page_form">
    <form method="post" action="/wp-login.php" id="top_access" name="top_access" style="max-width: 300px;">

        <p class="login-username" style="width: 270px;">
            <label for="user_login"></label>
            <input type="text" size="20" style="width: 275px;" value="" class="input" id="user_login" name="log" placeholder="E-mail or User" autocomplete="off">
        </p>

        <p class="login-password" style="width: 270px;">
            <label for="user_pass"></label>
            <input type="password" style="width: 275px;" size="20" value="" autocomplete="off" class="input" id="user_pass" name="pwd" placeholder="********">
        </p>
         <?php if($attempts > 2):?>
<!--                --><?php //echo recaptcha_get_html(RECAPTCHA_PUBLIC_KEY, null, true); ?>
                <script src="https://www.google.com/recaptcha/api.js?onload=myCallBack&render=explicit"></script>
                <script>
                  var login_recaptcha;
                  var myCallBack = function() {
                    //Render the recaptcha1 on the element with ID "recaptcha1"
                    login_recaptcha = grecaptcha.render('login_recaptcha', {
                      'sitekey' : '<?php echo RECAPTCHA_PUBLIC_KEY;?>'
                    });
                   };

                </script>
            <?php endif;?>
        <div class="login_recaptcha" id="login_recaptcha">

        </div>
        <span style="display:none;" class="header-login-error" id="header_login_error_msg">Wrong username or password, please try again!</span>

        <div class="submit-row">
            <a id="pass_recovery" href="/reset-password/">Forgot Password?</a>
            <a href="/" class="action-btn cancel-btn" id="wp-submit2-cancel">Cancel</a>
            <input type="submit" value="Login" class="blue-btn action-btn login-submit" id="wp-submit2" name="wp-submit">
        </div>
        <input type="hidden" value="/my-profile" name="redirect_to">

    </form>
</div>
<?php get_footer(); ?>
