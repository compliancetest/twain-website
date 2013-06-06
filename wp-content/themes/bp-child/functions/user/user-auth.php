<?php
/**
* Manage User Login, Register, Verification Email
*/

//Compliancetest login function
function compliancetest_login()
{
    global $wpdb;    
    
    $pUsername = $_POST['log'];
    $pUserPass = $_POST['pwd'];
    
    $query = $wpdb->prepare("SELECT user_login FROM " . $wpdb->users . " WHERE user_email=%s OR user_login=%s", $pUsername, $pUsername);
    $username = $wpdb->get_var($query);
    
    $user = wp_signon(array('user_login'=>$username, 'user_password' => $pUserPass));
    
    if(is_wp_error($user))
    {
       echo $user->get_error_message(); 
    }else{
        echo 'success';
    }        
    exit();
    
}

//Create User Function
function compliancetest_create_new_user(){
    global $wpdb;
    
    //Check Captcha
    if($_POST['captcha'] != $_SESSION['captcha'])
    {
        echo 'captcha_error';
        exit;
    }
    
    $user_id = wp_create_user( $_POST['user_login'], $_POST['user_pass'], $_POST['user_email'] );  
    
    if(is_wp_error($user_id))
    {
        echo $user_id->get_error_message();
    }else{
        wp_update_user( array ('ID' => $user_id, 'first_name' => $_POST['first_name'], 'last_name' => $_POST['last_name'])) ;
    
        $activation_key =  md5($_POST['user_email']);
        $wpdb->query("UPDATE $wpdb->users SET user_activation_key = '$activation_key', user_status=3 WHERE ID ='$user_id' ");

        update_user_meta ($user_id, 'user_organisation', $_POST['organisation']);
        update_user_meta ($user_id, 'contact_phone', $_POST['contact_phone']);
        
        send_email_verification($_POST['user_email'], $_POST['user_login'], $_POST['user_pass']);
        
        //auto login user
        wp_set_auth_cookie($user_id);    
        echo 'success';
    }
    exit;
}

//Function Resend Email Verification

function resend_email_verification(){
    
    global $current_user;
    
    send_email_verification($_POST['uemail'], '', '');
    
    echo 'success';
    exit();
}

//Activate User Account
function cp_activate_user()
{
    global $wpdb;
    
    
    $current_date = date("Y-m-d h:i:s");
    $activation = $_GET['token'];
    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->users . " WHERE user_activation_key =%s", $activation);
    $user = $wpdb->get_row($query);
    
    if($user)
    {
        $wpdb->query("UPDATE " . $wpdb->users .  " SET user_status = 0 WHERE ID =" . $user->ID);
        $wpdb->query("INSERT INTO {$wpdb->prefix}bp_activity (user_id, component, type, action, primary_link, date_recorded,secondary_item_id)     
                      VALUES({$user->ID},'xprofile','new_member',' <a href=\"".get_bloginfo('url')."/members/{$user->user_login}/\">{$user->display_name}</a> became a registered member','".get_bloginfo('url')."/members/{$user->user_login}/','{$current_date}','0')");
        
        //redirect
        wp_redirect(home_url().'/my-profile');              
    }
}

//Function Send Email Verification
function send_email_verification($email, $username, $password){
    //$headers[] = 'From: Nego Office <office@nego-solutions.com>';
    //$headers[] = 'Cc: John Q Codex <jqc@wordpress.org>';
    //$headers[] = 'Cc: iluvwp@wordpress.org'; // note you can just use a simple email address

    $to = $email;
    $subject = 'ComplianceTest Confirmation Email';
    
    if($username !='' && $password!= ''){
        $message = 'Username: ';
        $message .= $username;
        $message .= '<br />';
        $message .= 'Password: ';
        $message .= $password;
    }
    $message .= '<br />To activate you account click the link below <br />';
    $message .= $_SERVER['SERVER_NAME'];
    $message .= $_SERVER['REQUEST_URI'];
    $message .='?cp-action=user_activation&token=';
    $message .= md5($email);

    add_filter( 'wp_mail_content_type', 'set_html_content_type' );
    return wp_mail( $to, $subject, $message, $headers );
    remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
}

function set_html_content_type()
{
    return 'text/html';
}


if(!is_user_logged_in())    
{
    //Add Custom Action for Top Login Box
    add_action('cp_header_login_form', 'cp_header_loing_form');
    function cp_header_loing_form()
    {
        ob_start();
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
                'id_submit' => 'wp-submit2',
                'remember' => false,
                'value_remember' => false ); 
        ?>
        <div class="column right nopadding nomarginbottom" id="top_acces_wrap">                    
            
            <?php 
            wp_login_form($args); 
            ?>
            <a href="<?php echo get_bloginfo('url');?>/password-recovery/" id="pass_recovery">Password Recovery</a>
            <span class="simple_tooltip_pop radius6" id="header_login_error_msg"><span></span>Wrong username or password, please try again!</span>
            <div id="or" class="left">
                <img src="<?php echo bloginfo('stylesheet_directory'); ?>/images/or.png" />
            </div>
            <div id="registration_button"><a class="popup register">SIGNUP</a></div>            
        </div>
        <?php
        $html = ob_get_contents();
        ob_end_clean();
        echo $html;
    }
    
    //Add custom actino for the login and register box
    add_action('cp_login_register_box', 'cp_login_register_box');
    function cp_login_register_box()
    {    
        ob_start();
        ?>
        <div id="mask">
            <div id="popup-wrap">
                <div id="dinamic_pop" class="dinamic_pop radius6">
                    <p class="headline bottom30">Add New User</p>
                    <div class="pop_add_user">
                        <div class="wrap_wline">
                            <label for="user_org_email">User E-mail</label> <input type="email" id="user_org_email" name="user_org_email"/></p> 
                        </div>
                        <div>
                            <label for="user_org_role">Role</label>
                            <select>
                                <option value="">Admin</option>
                                <option value="">Tester</option>
                            </select>
                        </div>
                    </div>
                </div>                
                
                <div id="registration" class="radius6">
                    <p class="headline bottom30">User Registration</p>
                    <div id="wrap_forms">
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
                                        'id_username' => 'user_login2',
                                        'id_password' => 'user_pass2',
                                        'id_remember' => 'rememberme',
                                        'id_submit' => 'wp-submit',
                                        'remember' => false,
                                        'value_remember' => false ); 

                                wp_login_form($args); ?>
                                <a href="<?php echo get_bloginfo('url');?>/password-recovery/" id="recover_pass">Password recovery</a>
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
                                        <input type="checkbox" name="acc_tc" id="acc_tc_id"><label for="acc_tc">I accept the compliancetest.net <a id="terms_co">Terms & Conditions.</a></label>
                                    </div>
                                    <div class="clear"></div>

                                    <input type="hidden" name="redirect_to" value="<?php echo get_settings('home'); ?>/registration-succeeded"/>
                                    <div class="err"> </div>
                                    <input type="hidden" name="cp-action" value="register"/>
                                    <!--<input type="submit" name="wp_register" class="button" value="Register Me!" tabindex="100" id="reg_user"/>-->
                                    <div id="reg_user">Register Me</div><div class="loader"></div>
                                </form>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="reg_message"><?php echo of_get_option('reg_msg');?></div>
                    <div class="reg_message log_msg" id="popup-login-msg"><?php //echo of_get_option('log_msg');?></div>
                </div><!--END registration-->
          
				<!--<div class="terms_container" class="radius6">
					<p class="headline bottom30">Terms & Conditions</p>

					<div class="terms_content">

					</div>
					
				</div> -->
				<div id="scrollbar1" class="terms_container" class="radius6">
					<p class="headline bottom30">Terms & Conditions</p>
					<div class="scrollbar">
						<div class="track">
							<div class="thumb">
								<div class="end">
								</div>
							</div>
						</div>
					</div>
					<div class="viewport">
						 <div class="overview">
						 <?php 
							$page_id = 1061;
							$page_data = get_page( $page_id );
							echo apply_filters('the_content', $page_data->post_content); // echo the content and retain WordPress 
							?>
							<div class="clear"></div>
							<div class="space10"></div>
							<a id="accept_terms">Accept</a>
							<a id="reject_terms">Reject</a>
							<div class="clear"></div>
						</div>
					</div>
				</div>	
          
            <div id="close-popup" class="close_btn"></div>
            </div>
        </div>
        <?php
        $content = ob_get_contents();
        ob_end_clean();
        
        echo $content;
    }
}
