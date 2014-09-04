<?php
/*
 * Template Name: Reset Password
 */

if(is_user_logged_in())
{
    //Goto My profile page
    wp_redirect("/my-profile");
    exit;
}

if(/*isset($_GET['cp-action']) && wp_verify_nonce($_GET['cp-action'], 'pre_reset_password') &&*/ isset($_GET['key']) && isset($_GET['login']))
{    
    $user = my_check_password_reset_key($_GET['key'], $_GET['login']);
    if(!$user)   
    {
        addMessage('Invalid Request!', 'error');
    }
}

get_header();
?>

	<div class="content container" id="reset-password-container">
		
		<div class="column">			            
			<?php if (isset($user) && $user) {			    
			        //do_action('password_reset', $user, $_POST['pass1']);
//			        wp_set_password($_POST['pass1'], $user->ID);
//			        wp_password_change_notification($user);
				//reset_password($user, $_POST['pass1']);
				
			    
			?>                
                <h2><a href=""><?php the_title()?></a></h2>
                <div class="space10"></div>
			    <form method="post" action="">                                        
                    <p>                       
                        <i><?php _e('Hint: To make it stronger, use upper and lower case letters, numbers and symbols like ! " ? $ % ^ &amp; ).'); ?></i>
                    </p>
                    <div class="field-row">
                        <label>New Password</label>
                        <input type="password" name="pass1" id="pass1"  size="20" value="" autocomplete="off" class="input" />    
                        <div class="clear"></div>
                    </div>
                    <div class="field-row">
                        <label>Confirm new password</label>
                        <input type="password" name="pass2" id="pass2"  size="20" value="" autocomplete="off" class="input" />
                        <div class="clear"></div>
                    </div>
			        <div class="btn-row">			
                    <input type="hidden" id="user_login" name="user_login" value="<?php echo esc_attr( $_GET['login'] ); ?>" autocomplete="off" />
                    <input type="hidden" name="key" value="<?php echo esc_attr( $_GET['key'] ); ?>" autocomplete="off" />
				    
                    <input type="hidden" name="user-submit" class="user-submit" value="<?php esc_attr_e('Reset Password'); ?>" />
                    <?php wp_nonce_field('reset_password', 'cp-action'); ?>
                    
                    <a href="#" class="submit-btn action-btn process-btn"><span class="p"></span><span class="t">Save Password</span></a>
				    <div class="clear"></div>
				    <div class="space10"></div>
				</div>
			    </form>
			<?php
			    
			} else {
			?>
                <h2><a href=""><?php the_title()?></a></h2>
                <div class="space20"></div>
			    <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
				    <div class="field-row">
					    <label><?php _e('Username or Email'); ?>: </label>
                        <input type="text" name="user_login" value="" size="20" id="user_login" autocomplete="off" class="input" />
					    <div class="clear"></div>
				    </div>
				    <div class="btn-row">
					    <?php do_action('login_form', 'resetpass'); ?>
					    <input type="hidden" name="user-submit" value="<?php _e('Reset Password'); ?>" class="user-submit"  />
                        <a href="#" class="submit-btn action-btn process-btn"><span class="p"></span><span class="t">Reset Password</span></a>
					    <div class="clear"></div>
                        <?php wp_nonce_field('request_reset_password', 'cp-action'); ?>
				    </div>
				    <div class="clear"></div>
				    <div class="space30"></div>
			    </form> 
			<?php } ?>  <!--end action-->
			
		</div><!--end column-->
		
		<div class="clear"></div>

	</div> <!--end content container-->

<?php
get_footer();
?>
