<?php
/*
 * Template Name: Password Recovery
 */

if(is_user_logged_in())
{
    //Goto My profile page
    wp_redirect("/my-profile");
    exit;
}

get_header();
?>

	<div class="content container">
		
		<div class="column">
			<?php if (have_posts()) while (have_posts()) : the_post(); ?>
			<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			
			<?php endwhile; ?>
			<div class="space20"></div>
			<?php if (isset($_GET['action']) && $_GET['action'] == 'rp' && isset($_GET['key']) && isset($_GET['login'])) {
			    $error = array();
			    $user = my_check_password_reset_key($_GET['key'], $_GET['login']);
			    if ( is_wp_error($user) ) {
				echo '<p class="error_recovery">'.__('Invalid key!').'</p>';
			    } else {
			    if ( isset($_POST['pass1']) && $_POST['pass1'] != $_POST['pass2'] )
				$error[] = '<p class="error_recovery">'.__('The passwords do not match.').'</p>';
			    if ( empty($error)  && isset( $_POST['pass1'] ) && !empty( $_POST['pass1'] ) ) {
			        do_action('password_reset', $user, $_POST['pass1']);
			        wp_set_password($_POST['pass1'], $user->ID);
			        wp_password_change_notification($user);
				//reset_password($user, $_POST['pass1']);
				echo '<p class="blue_txt">'.__('Your password has been reset.').'</p>';
			    } else {
			?>
			    <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" class="wp-user-form">
				<div class="username">
					<b><?php _e('Enter your new password below.'); ?> </b><br />
					<div class="space5"></div>
					<input type="hidden" id="user_login" value="<?php echo esc_attr( $_GET['login'] ); ?>" autocomplete="off" />
					<div class="space10"></div>
					<b><?php _e('New password') ?></b><br />
					<input type="password" name="pass1" id="pass1"  size="20" value="" autocomplete="off" />
					<div class="space10"></div>
					<b><?php _e('Confirm new password') ?></b><br />
					<input type="password" name="pass2" id="pass2"  size="20" value="" autocomplete="off" />

					<p class="description indicator-hint">
					<?php _e('Hint: The password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers and symbols like ! " ? $ % ^ &amp; ).'); ?>
					</p>
				</div>
			        <div class="login_fields">
				<?php 
				if (!empty($error)){
					foreach($error as $e => $value){
						echo $value . "<br/>";
					}
				}
				?>
				    <input type="submit" name="user-submit" class="user-submit" value="<?php esc_attr_e('Reset Password'); ?>" />
				    <div class="clear"></div>
				    <div class="space10"></div>
				</div>
			    </form>
			<?php
			    }
			}
			} else {
			?>

			<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" class="wp-user-form">
				<div class="username">
					<b><?php _e('Username or Email'); ?>: </b><br />
					<div class="space5"></div>
					<input type="text" name="user_login" value="" size="20" id="user_login" tabindex="1001" />
				</div>
				<div class="space10"></div>
				<div class="login_fields">
					<?php do_action('login_form', 'resetpass'); ?>
					<input type="hidden" name="user-submit" value="<?php _e('Reset my password'); ?>" class="user-submit"  />
                    <a href="#" class="submit-btn action-btn process-btn"><span class="p"></span><span class="t">Reset my password</span></a>
					<div class="clear"></div>
					<div class="space10"></div>
                    <?php wp_nonce_field('request_reset_password', 'cp-action'); ?>
                    <input type="hidden" name="user-cookie" value="1" />			
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
