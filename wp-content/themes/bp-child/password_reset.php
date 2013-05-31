<?php
/*
 * Template Name: Password Recovery
 */
get_header();
?>

	<div class="space25"></div>
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
					<input type="submit" name="user-submit" value="<?php _e('Reset my password'); ?>" class="user-submit" tabindex="1002" />
					<div class="clear"></div>
					<div class="space10"></div>
			<?php if (isset($_POST['reset_pass'])){
			global $wpdb;
			$error = array();
			$username = trim($_POST['user_login']);
			$user_exists = false;
			// First check by username
			if ( username_exists( $username ) ){
				$user_exists = true;
				$user = get_user_by('login', $username);
			}
			// Then, by e-mail address
			elseif( email_exists($username) ){
					$user_exists = true;
					$user = get_user_by_email($username);
					
			}else{
				$error[0] = '<p class="error_recovery">'.__('Username or Email was not found, try again!').'</p>';
			}
			if ($user_exists){
				$user_login = $user->user_login;
				$user_email = $user->user_email;

				$key = $wpdb->get_var($wpdb->prepare("SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $user_login));
				if ( empty($key) ) {
					// Generate something random for a key...
					$key = wp_generate_password(20, false);
					do_action('retrieve_password_key', $user_login, $key);
					// Now insert the new md5 key into the db
					$wpdb->update($wpdb->users, array('user_activation_key' => $key), array('user_login' => $user_login));
				}

				//create email message
				add_filter( 'wp_mail_content_type', 'set_html_content_type' ); 
				$message = __('Someone has asked to reset the password for the following site and username.');
				$message .= "<br />";
				$message .= get_option('siteurl') . "\r\n\r\n";
				$message .= "<br />";
				$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
				$message .= "<br />";
				$message .= __('To reset your password visit the following address, otherwise just ignore this email and nothing will happen.') . "\r\n\r\n";
				$message .= network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . "&redirect_to=".urlencode(get_option('siteurl'))."\r\n";
				//send email meassage
				if (FALSE == wp_mail($user_email, sprintf(__('[%s] Password Reset'), get_option('blogname')), $message))
				$error[1] = '<p class="error_recovery">' . __('The e-mail could not be sent.') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function...') . '</p>';
			}
			if (!empty($error)){
				foreach($error as $e => $value){
							echo $value . "<br/>";
						}
					//	die(print_r($error));
			}else{
				echo '<p class="blue_txt">'.__('A message will be sent to your email address.').'</p>'; 
			}
			
			}?> 
					<input type="hidden" name="reset_pass" value="1" />
					<input type="hidden" name="user-cookie" value="1" />
				</div>
				<div class="clear"></div>
				<div class="space30"></div>
			</form> 
			<?php } ?>  <!--end action-->
			
		</div><!--end column-->
		
		<div class="clear"></div>

	</div> <!--end content container-->
	
</div>
<div class="space45"></div>
<div class="clear"></div>
</div>
<div class="clear"></div>
<?php
get_footer();
?>
