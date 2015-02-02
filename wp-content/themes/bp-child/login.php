<?php
/*
 * Template Name: Login page
 */
get_header();
if( is_user_logged_in() ){
    wp_redirect( '/' );exit();
}
?>
<div class="content container" id="login_page_form">
    <form method="post" action="/wp-login.php" id="top_access" name="top_access">

        <p class="login-username">
            <label for="user_login"></label>
            <input type="text" size="20" value="" class="input" id="user_login" name="log" placeholder="E-mail or User">
        </p>
        <p class="login-password">
            <label for="user_pass"></label>
            <input type="password" size="20" value="" autocomplete="off" class="input" id="user_pass" name="pwd" placeholder="********">
        </p>

        <span style="display:none;" class="header-login-error" id="header_login_error_msg">Wrong username or password, please try again!</span>
        <div class="submit-row">
            <a id="pass_recovery" href="http://compliancetest.my/reset-password/">Forgot Password?</a>
            <input type="submit" value="Login" class="blue-btn action-btn login-submit" id="wp-submit2" name="wp-submit">
            <a href="/" class="action-btn cancel-btn" id="wp-submit2-cancel">Cancel</a>
        </div>
        <input type="hidden" value="/my-profile" name="redirect_to">

    </form>
</div>
<?php get_footer(); ?>
