<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
		<?php if ( current_theme_supports( 'bp-default-responsive' ) ) : ?><meta name="viewport" content="width=device-width, initial-scale=1.0" /><?php endif; ?>
		<title><?php wp_title( '|', true, 'right' ); bloginfo( 'name' ); ?></title>
		<?php do_action( 'bp_head' ) ?>
		<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=Oswald' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ) ?>" />
		<script type="text/javascript" src="<?php echo bloginfo('stylesheet_directory'); ?>/js/jquery-1.7.2.min.js"></script>
		<script type="text/javascript" src="<?php echo bloginfo('stylesheet_directory'); ?>/js/custom.js"></script>

		<?php wp_head(); ?>

	</head>

	<body <?php body_class(); ?> id="bp-default">
		<?php do_action( 'bp_before_header' ); ?>
		<div id="mask">
		<div id="popup-wrap">
			<div id="registration" class="radius6">
				<p class="headline bottom30">User Registration</p>
				<div class="user_border radius6 left" id="log">
					<div class="existing_user">
						<div class="ex_user">Existing User</div>
						<?php
						$args = array(
								'echo' => true,
								'redirect' => get_bloginfo('url'), 
								'form_id' => 'logform',
								'label_username' => __( '' ),
								'label_password' => __( '' ),
								'label_remember' => __( 'Remember Me' ),
								'label_log_in' => __( 'LOGIN' ),
								'id_username' => 'user_login',
								'id_password' => 'user_pass',
								'id_remember' => 'rememberme',
								'id_submit' => 'wp-submit',
								'remember' => false,
								'value_remember' => false );  
						wp_login_form($args); ?>
						<a href="wp-login.php?action=lostpassword" id="recover_pass">Password recovery</a>
					
					</div>
				</div>
						
					<div class="user_border user_border2 radius6 right" id="reg">
					<div class="existing_user">
						<div class="reg_user">Register New User</div>
							<form id="formreg" action="" method="post">
								<div class="field">
									<label for="first_name_id">First Name</label>
									<input type="text" class="" title="" name="first_name" id="first_name_id">
								</div>
								<div class="field">
									<label for="last_name_id">Last Name</label>
									<input type="text" class="" title="" name="last_name" id="last_name_id">
								</div>
								<div class="clear"></div>
								
								<div class="field">
									<label for="email_id">Email</label>
									<input type="email" class="" title="" name="user_email" id="email_id">
								</div>
								<div class="field">
									<label for="user_login_id">Username</label>
									<input type="text" class="" title="" name="user_login" id="user_login_id">
								</div>
								<div class="clear"></div>
								
								<div class="field">
									<label for="organisation_id">Organisation</label>
									<input type="text" class="" title="" name="organisation" id="organisation_id">
								</div>
								<div class="field">
									<label for="contact_phone_id">Contact Phone Number</label>
									<input type="text" class="" title="" name="contact_phone" id="contact_phone_id">
								</div>
								<div class="clear"></div>     
								
								<div class="field">
									<label for="user_pass">Password</label>
									<input type="password" class="" title="" name="user_pass" id="user_pass_id">
								</div>
								<div class="field">
									<label for="user_pass_confirm_id">Confirm Password</label>
									<input type="password" class="" title="" name="user_pass_confirm" id="user_pass_confirm_id">
								</div>
								<div class="clear"></div>   
								
								<div class="field">
									<label for="captcha_reg">Stop Spam!</label> <br />
									<img src="<?php echo bloginfo('stylesheet_directory'); ?>/images/captcha.php" class="left"/>
									<input type="text" class="width60P left" title="" name="captcha" id="captcha_reg">
								</div>
								<div class="field top23">		
									<input type="checkbox" name="acc_tc" id="acc_tc_id"><label for="acc_tc">I accept the compliancetest.net <a href="http://nego-solutions.com/dev-clients/compliance/?page_id=779">Terms & Conditions.</a></label>
								</div>
								<div class="clear"></div>
								                 
								<input type="hidden" name="redirect_to" value="<?php echo get_settings('home'); ?>/registration-succeeded"/>
								<div class="err"> </div>
								<input type="hidden" name="form_set" value="testing"/>
								<input type="submit" name="wp_register" class="button" value="Register Me!" tabindex="100" id="reg_user"/>
							</form>
					</div>
				</div>
				<div class="clear"></div>
			</div><!--END registration-->
		<div id="close-popup" class="close_btn"></div>
		</div>
	</div>
		
	<div id="wrapper">

<!-- ****************** HEADER ***************** -->
		<div id="header-wrapper">
			<div class="header column">
				<a href="<?php bloginfo('url'); ?>" class="logo left"><img src="<?php echo of_get_option('logo'); ?>"/></a>
				 <?php 	
				 
				 if ( is_user_logged_in() ) { 
						?>	
						<div class="column fifth right no-marginbottom" id="top_logged_wrap">
							<?php 
								global $current_user;
								get_currentuserinfo();
							?>
							<div id="top_loged_wellcome">
								<?php echo get_avatar($current_user->user_email, 28);  ?>
								<div class="right toright">
									Welcome
									<h5 class="dark_gray_txt">
										<?php 
											print $current_user->user_firstname .' '.$current_user->user_lastname; 
										?>
										</h5>
								</div>
								<div class="clear"></div>
								<div id="top_loged_actions">
									<?php
									if(is_home() ) {
										$logout_redirect = get_bloginfo('url');
										}
										else{
										$logout_redirect = get_permalink();
											}
									?>
									<ul>
										<li><a href="<?php echo home_url();?>/my-suites/">Dashboard</a></li>
										<li><a href="#">Settings</a></li>
										
										<li><a href="<?php echo wp_logout_url( $logout_redirect ); ?>">Logout</a></li>
									</ul>
									<div class="clear"></div>
								</div>
								<div class="clear"></div>
							</div>							
						<div class="clear"></div>
						</div>	
						
					<?php 
					} else {
						$args = array(
								'echo' => true,
								'redirect' => get_bloginfo('url'), 
								'form_id' => 'top_access',
								'label_username' => __( '' ),
								'label_password' => __( '' ),
								'label_remember' => __( 'Remember Me' ),
								'label_log_in' => __( 'LOGIN' ),
								'id_username' => 'user_login',
								'id_password' => 'user_pass',
								'id_remember' => 'rememberme',
								'id_submit' => 'wp-submit',
								'remember' => false,
								'value_remember' => false ); 
						?>
						<div class="column right nopadding nomarginbottom" id="top_acces_wrap">					
						<?php 
						wp_login_form($args); 
						?>
						<span class="simple_tooltip_pop radius6"><span></span>Wrong username or password!</span>
						<?php
						if(isset($_GET['login'])){
							 ?>
							 
							<script type="text/javascript">
							jQuery('.simple_tooltip_pop').show('slow');
							</script>
							
							<?php
							}
						?>
						<div id="or" class="left">
							<img src="<?php echo bloginfo('stylesheet_directory'); ?>/images/or.png" />
						</div>
						<div id="registration_button"><a class="popup">SIGNUP</a></div>
						<?php
						}
				 ?>
				 </div>
				
				<div class="clear"></div>
			</div>		
		</div>
		
		<div class="clear"></div>


			<div id="menu-wrapper">
			<div id="cssmenu">
			<?php
					wp_nav_menu( array(
							'theme_location' => 'header-menu',
							'container' =>false,
							'echo' => true,
							'depth' => 0,
							'fallback_cb'=>'headermenu',
							'menu_id' => ''
						)
					);
				?>
			</div>
		</div>	

			<?php do_action( 'bp_header' ); ?>

		</div><!-- #header -->

		<?php do_action( 'bp_after_header'); ?>
		
<!-- **************** END HEADER *************** -->
<div id="content-pattern">
	<div id="content-wrapper">
		<div class="submenu">
			<div class="submenu_content">
				<div class="what_is normal_dd">
					<ul>
					  <li class="left width250">
						 <img src="<?php echo of_get_option('what_icon'); ?>" class="left">
						 <h3><?php echo of_get_option('what_t'); ?></h3>
						 <p><?php echo of_get_option('what_d'); ?></p>
					  </li>
					  
					  <li class="left width200">
						 <img src="<?php echo of_get_option('issuers_icon'); ?>" class="left">
						 <h3><?php echo of_get_option('issuers_t'); ?></h3>
						 <p><?php echo of_get_option('issuers_d'); ?></p>
					  </li>
					  
					  <li class="last left width350">
								<img src="<?php echo of_get_option('implementers_icon'); ?>" class="left">
								<h3><?php echo of_get_option('implementers_t'); ?></h3>
								 <p><?php echo of_get_option('implementers_d'); ?></p>
							
								<a href="<?php echo of_get_option('what_link'); ?>" class="right linkto">Find out How it works</a>
					  </li>
					  <div class="clear"></div>
					</ul>
				</div> <!--END what_id DIV-->
				
				<div class="why_compliance normal_dd">
					<ul>
							<li class="left width280">
								<img src="<?php echo of_get_option('community_icon'); ?>" class="left">
								<h3><?php echo of_get_option('community_t'); ?></h3>
								<p><?php echo of_get_option('community_d'); ?></p>
							</li>
							<li class="left width280">
								<img src="<?php echo of_get_option('support_icon'); ?>" class="left">
								<h3><?php echo of_get_option('support_t'); ?></h3>
								<p><?php echo of_get_option('support_d'); ?></p>
							</li>
							<li class="last left width280">
								<img src="<?php echo of_get_option('confidence_icon'); ?>" class="left">
								<h3><?php echo of_get_option('confidence_t'); ?></h3>
								<p><?php echo of_get_option('confidence_d'); ?></p>
							
							<a href="<?php echo of_get_option('why_link'); ?>" class="right linkto" style="margin-bottom: -34px;">Find out more reasons</a>
							</li>
							<div class="clear"></div>
							<li class="last left width280">
								<img src="<?php echo of_get_option('visibility_icon'); ?>" class="left">
								<h3><?php echo of_get_option('visibility_t'); ?></h3>
								<p><?php echo of_get_option('visibility_d'); ?></p>
							</li>	
							<li class="last left width280">
								<img src="<?php echo of_get_option('cost_icon'); ?>" class="left">
								<h3><?php echo of_get_option('cost_t'); ?></h3>
								<p><?php echo of_get_option('cost_d'); ?></p>
							</li>	
							<div class="clear"></div>
						</ul>
				</div><!-- end why DIV-->
				
				<div class="compliancetest_serv normal_dd">
					<ul>
							<li class="left width205">
								<h3><?php echo of_get_option('testsuites_t'); ?></h3>
								<p class="nomarginleft"><?php echo of_get_option('testsuites_d'); ?></p>
								<p class="nomarginleft"><a href="<?php echo of_get_option('testsuites_linkto'); ?>" class="read_more">Read More</a></p>
								<div class="clear"></div>
								
								<h3><?php echo of_get_option('collaboration_t'); ?></h3>
								<p class="nomarginleft"><?php echo of_get_option('collaboration_d'); ?></p>
								<p class="nomarginleft"><a href="<?php echo of_get_option('collaboration_linkto'); ?>" class="read_more">Read More</a></p>		
							</li>
							<li class="left width300">
								<h3><?php echo of_get_option('productrep_t'); ?></h3>
								<p class="nomarginleft"><?php echo of_get_option('productrep_d'); ?></p>
								<p class="nomarginleft"><a href="<?php echo of_get_option('productrep_linkto'); ?>" class="read_more">Read More</a></p>		
							</li>
							
							<li class="last left width350">
								<h3><?php echo of_get_option('testharness_t'); ?></h3>
								<p class="nomarginleft"><?php echo of_get_option('testharness_d'); ?></p>
								<p class="nomarginleft"><a href="<?php echo of_get_option('testharness_linkto'); ?>" class="read_more">Read More</a></p>		
							</li>
							<div class="clear"></div>
						</ul>		
				</div><!-- end ComplianceTest SERVICE DIV-->
				
				<div class="help_faq normal_dd small_dd">
						<ul>
							<li class="left width250">
								<a href="<?php echo of_get_option('how_linkto'); ?>"><h3 class="dark_blue_txt"><?php echo of_get_option('how_t'); ?></h3></a>
								<p class="nomarginleft"><?php echo of_get_option('how_desc'); ?></p>
								<br>
								<a href="<?php echo of_get_option('faq_linkto'); ?>"><h3 class="dark_blue_txt"><?php echo of_get_option('faq_t'); ?></h3></a>
								<p class="nomarginleft"><?php echo of_get_option('faq_desc'); ?></p>
							</li>
							<li class="left width250">
								<a href="<?php echo of_get_option('documentation_linkto'); ?>"><h3 class="dark_blue_txt"><?php echo of_get_option('documentation_t'); ?></h3></a>
								<p class="nomarginleft"><?php echo of_get_option('documentation_desc'); ?></p>
								<br>
								<a href="<?php echo of_get_option('forum_linkto'); ?>"><h3 class="dark_blue_txt"><?php echo of_get_option('forum_t'); ?></h3></a>
								<p class="nomarginleft"><?php echo of_get_option('forum_desc'); ?></p>
							</li>
							<div class="clear"></div>
						</ul>
				</div>
				<!--end Help & Faq DIV -->
			</div>
		</div>
<?php 

if (isset ($_GET['user_activation'])){
	$current_date = date("Y-m-d h:i:s");
	$activation = $_GET['user_activation'];
	$user = $wpdb->get_row("SELECT * FROM $wpdb->users WHERE user_activation_key ='$activation' ");
	echo $user->ID;
	$wpdb->query("UPDATE $wpdb->users SET user_status = 0 WHERE user_activation_key ='$activation' ");
	$wpdb->query("INSERT INTO {$wpdb->prefix}bp_activity (user_id, component, type, action, primary_link, date_recorded,secondary_item_id) 	
				  VALUES({$user->ID},'xprofile','new_member',' <a href=\"http://nego-solutions.com/dev-clients/compliance/members/{$user->user_login}/\">{$user->display_name}</a> became a registered member','http://nego-solutions.com/dev-clients/compliance/members/{$user->user_login}/','{$current_date}','0')");
	}
	
	
 ?>
<?php do_action( 'bp_before_container' ); ?>
		<div id="container">

