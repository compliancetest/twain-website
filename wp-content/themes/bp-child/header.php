<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
    <head profile="http://gmpg.org/xfn/11">
        <meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />        
        <?php if ( current_theme_supports( 'bp-default-responsive' ) ) : ?><meta name="viewport" content="width=device-width, initial-scale=1.0" /><?php endif; ?>
        <title><?php wp_title( '|', true, 'right' ); bloginfo( 'name' ); ?></title>
        <?php do_action( 'bp_head' ) ?>
        <link href='//fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>                
        <link href='//fonts.googleapis.com/css?family=Oswald' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
        <link rel="pingback" href="<?php bloginfo( 'pingback_url' ) ?>" />
        <script type="text/javascript" src="<?php echo bloginfo('stylesheet_directory'); ?>/js/jquery-1.7.2.min.js"></script>
        <script type="text/javascript" src="<?php echo bloginfo('stylesheet_directory'); ?>/js/jquery.form.js"></script>
        <script type="text/javascript" src="<?php echo bloginfo('stylesheet_directory'); ?>/js/custom.js"></script>
<script type="text/javascript">
var HOMEURL = "<?php echo get_home_url(); ?>";
</script>
        <?php wp_head(); ?>
    </head>

    <body <?php body_class(); ?> id="bp-default">
        <?php do_action( 'bp_before_header' ); ?>
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
                                    <input type="checkbox" name="acc_tc" id="acc_tc_id"><label for="acc_tc">I accept the compliancetest.net <a href="<?php echo get_bloginfo('url');?>/terms-conditions">Terms & Conditions.</a></label>
                                </div>
                                <div class="clear"></div>

                                <input type="hidden" name="redirect_to" value="<?php echo get_settings('home'); ?>/registration-succeeded"/>
                                <div class="err"> </div>
                                <input type="hidden" name="form_set" value="testing"/>
                                <!--<input type="submit" name="wp_register" class="button" value="Register Me!" tabindex="100" id="reg_user"/>-->
                                <div id="reg_user">Register Me</div><div class="loader"></div>
                            </form>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="reg_message"><?php echo of_get_option('reg_msg');?></div>
                <div class="reg_message log_msg"><?php //echo of_get_option('log_msg');?></div>
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
                                    <h5 class="dark_gray_txt"><?php echo $current_user->user_firstname .' '.$current_user->user_lastname;?></h5>
                                </div>
                                <div class="clear"></div>
                                <div id="top_loged_actions">
                                    <?php
                                    if(is_home() ) {
                                        $logout_redirect = get_bloginfo('url');
                                    }else{
                                        $logout_redirect = get_permalink();
                                    }
                                    ?>
                                    <ul>
                                        <li><a href="<?php echo home_url();?>/my-profile/">Dashboard</a></li>
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
                                'id_submit' => 'wp-submit2',
                                'remember' => false,
                                'value_remember' => false ); 
                        ?>
                    <div class="column right nopadding nomarginbottom" id="top_acces_wrap">                    
                        
                        <?php 
                        wp_login_form($args); 
                        ?>
                        <a href="<?php echo get_bloginfo('url');?>/password-recovery/" id="pass_recovery">Password Recovery</a>
                        <span class="simple_tooltip_pop log_err1 radius6"><span></span>Wrong username or password, please try again!</span>
                        <span class="simple_tooltip_pop log_err2 radius6"><span></span><?php echo of_get_option('log_msg');?></span>
                        <div id="or" class="left">
                            <img src="<?php echo bloginfo('stylesheet_directory'); ?>/images/or.png" />
                        </div>
                        <div id="registration_button"><a class="popup register">SIGNUP</a></div>
                        
                    </div>
                    <?php
                    }
                    ?>
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
                ));
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
                    <div class="submenu_inner">
                      <div class="left menuitem1">
                         <span class="left"><img src="<?php echo of_get_option('what_icon'); ?>"></span>
                         <h3><?php echo of_get_option('what_t'); ?></h3>
                         <p><?php echo of_get_option('what_d'); ?></p>
                      </div>
                      
                      <div class="left menuitem2">
                         <span class="left"><img src="<?php echo of_get_option('issuers_icon'); ?>"></span>
                         <h3><?php echo of_get_option('issuers_t'); ?></h3>
                         <p><?php echo of_get_option('issuers_d'); ?></p>
                      </div>
                      
                      <div class="left menuitem3">
                        <span class="left"><img src="<?php echo of_get_option('implementers_icon'); ?>"></span>
                        <h3><?php echo of_get_option('implementers_t'); ?></h3>
                        <p><?php echo of_get_option('implementers_d'); ?></p>
                      </div>
                      <a href="<?php echo of_get_option('what_is_compliancetest_more_link')?>" class="morelink">More Information &nbsp;&nbsp;&nbsp;&raquo;</a>                        
                      <div class="clear"></div>
                    </div>
                </div> <!--END what_id DIV-->
                
                <div class="why_compliance normal_dd">
                    <div class="submenu_inner">
                        <div class="left menuitem1">
                            <span class="left"><img src="<?php echo of_get_option('community_icon'); ?>"></span>
                            <h3><?php echo of_get_option('community_t'); ?></h3>
                            <p><?php echo of_get_option('community_d'); ?></p>
                        </div>
                        <div class="left menuitem2">
                            <span class="left"><img src="<?php echo of_get_option('support_icon'); ?>"></span>
                            <h3><?php echo of_get_option('support_t'); ?></h3>
                            <p><?php echo of_get_option('support_d'); ?></p>
                        </div>
                        <div class="left menuitem3">
                            <span class="left"><img src="<?php echo of_get_option('confidence_icon'); ?>"></span>
                            <h3><?php echo of_get_option('confidence_t'); ?></h3>
                            <p><?php echo of_get_option('confidence_d'); ?></p>
                            <!-- RPV <a href="<?php echo of_get_option('why_link'); ?>" class="right linkto" style="margin-bottom: -34px;">More Information</a> -->                            
                        </div>
                        <div class="clear"></div>
                        <div class="left menuitem1">
                            <span class="left"><img src="<?php echo of_get_option('visibility_icon'); ?>"></span>
                            <h3><?php echo of_get_option('visibility_t'); ?></h3>
                            <p><?php echo of_get_option('visibility_d'); ?></p>
                        </div>    
                        <div class="left menuitem2">
                            <span class="left"><img src="<?php echo of_get_option('cost_icon'); ?>"></span>
                            <h3><?php echo of_get_option('cost_t'); ?></h3>
                            <p><?php echo of_get_option('cost_d'); ?></p>
                        </div>    
                        <a href="<?php echo of_get_option('why_compliancetest_more_link'); ?>" class="morelink">More Information &nbsp;&nbsp;&nbsp;&raquo;</a>
                        <div class="clear"></div>
                    </div>
                </div><!-- end why DIV-->
                
                <div class="compliancetest_serv normal_dd">
                    <div class="submenu_inner">
                        <div class="left menuitem1">
                            <span class="left"><img src="<?php echo of_get_option('testsuites_icon'); ?>"></span>
                            <h3><?php echo of_get_option('testsuites_t'); ?></h3>
                            <p><?php echo of_get_option('testsuites_d'); ?></p>
                            <br />
                            <span class="left"><img src="<?php echo of_get_option('collaboration_icon'); ?>"></span>
                            <h3><?php echo of_get_option('collaboration_t'); ?></h3>
                            <p><?php echo of_get_option('collaboration_d'); ?></p>
                        </div>
                        <div class="left menuitem2">
                            <span class="left"><img src="<?php echo of_get_option('productrep_icon'); ?>"></span>
                            <h3><?php echo of_get_option('productrep_t'); ?></h3>
                            <p><?php echo of_get_option('productrep_d'); ?></p>    
                        </div>                        
                        <div class="left menuitem3">
                            <span class="left"><img src="<?php echo of_get_option('testharness_icon'); ?>"></span>
                            <h3><?php echo of_get_option('testharness_t'); ?></h3>
                            <p><?php echo of_get_option('testharness_d'); ?></p>
                        </div>                        
                        <a href="<?php echo of_get_option('compliancetest_service_more_link'); ?>" class="morelink">More Information &nbsp;&nbsp;&nbsp;&raquo;</a>
                        <div class="clear"></div>
                    </div>        
                </div><!-- end ComplianceTest SERVICE DIV-->
                
                <div class="help_faq normal_dd small_dd">
                    <div class="submenu_inner">
                        <div class="left menuitem1">
                            <span class="left"><img src="<?php echo of_get_option('how_icon'); ?>"></span>
                            <h3><a href="<?php echo of_get_option('how_linkto'); ?>"><?php echo of_get_option('how_t'); ?></a></h3>
                            <p><?php echo of_get_option('how_desc'); ?></p>
                            <br />
                            <span class="left"><img src="<?php echo of_get_option('faq_icon'); ?>"></span>
                            <h3><a href="<?php echo of_get_option('faq_linkto'); ?>"><?php echo of_get_option('faq_t'); ?></a></h3>
                            <p><?php echo of_get_option('faq_desc'); ?></p>
                        </div>
                        <div class="left menuitem2">
                            <span class="left"><img src="<?php echo of_get_option('documentation_icon'); ?>"></span>
                            <h3><a href="<?php echo of_get_option('documentation_linkto'); ?>"><?php echo of_get_option('documentation_t'); ?></a></h3>
                            <p><?php echo of_get_option('documentation_desc'); ?></p>
                            <br />
                            <span class="left"><img src="<?php echo of_get_option('forum_icon'); ?>"></span>
                            <h3><a href="<?php echo of_get_option('forum_linkto'); ?>"><?php echo of_get_option('forum_t'); ?></a></h3>
                            <p><?php echo of_get_option('forum_desc'); ?></p>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
                <!--end Help & Faq DIV -->
            </div>
        </div>
<?php 

if (isset ($_GET['user_activation'])){
    $current_date = date("Y-m-d h:i:s");
    $activation = $_GET['user_activation'];
    $user = $wpdb->get_row("SELECT * FROM $wpdb->users WHERE user_activation_key ='$activation' ");
    //echo $user->ID;
    $wpdb->query("UPDATE $wpdb->users SET user_status = 0 WHERE user_activation_key ='$activation' ");
    $wpdb->query("INSERT INTO {$wpdb->prefix}bp_activity (user_id, component, type, action, primary_link, date_recorded,secondary_item_id)     
                  VALUES({$user->ID},'xprofile','new_member',' <a href=\"".get_bloginfo('url')."/members/{$user->user_login}/\">{$user->display_name}</a> became a registered member','".get_bloginfo('url')."/members/{$user->user_login}/','{$current_date}','0')");
    //redirect
        wp_redirect(home_url().'/my-profile');      
    }
    
    
 ?>
<?php do_action( 'bp_before_container' ); ?>
        <div id="container">