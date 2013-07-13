<!DOCTYPE HTML>
<html>
    <head profile="http://gmpg.org/xfn/11">
        <meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />        
        <link rel="icon" href="<?php echo CHILD_TEMPLATE_DIRECTORY ?>/favicon.ico" type="image/x-icon">
        <?php if ( current_theme_supports( 'bp-default-responsive' ) ) : ?><meta name="viewport" content="width=device-width, initial-scale=1.0" /><?php endif; ?>
        <title><?php wp_title( '|', true, 'right' ); bloginfo( 'name' ); ?></title>
        <?php do_action( 'bp_head' ) ?>
        <link href='//fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800|Oswald:400,300,700' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
        
        <link rel="pingback" href="<?php bloginfo( 'pingback_url' ) ?>" />

        <?php wp_head(); ?>
        <script type="text/javascript">
        <?php
            echo of_get_option('google-analytics-code');
        ?>
        </script>
    </head>

    <body <?php body_class(); ?> id="bp-default">
        <?php do_action( 'bp_before_header' ); ?>
        
        
    <div id="wrapper">

        <!-- ****************** HEADER ***************** -->
        <div id="header-wrapper"><!-- Start Header-Wrapper -->
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
                                    if(is_home() || is_page('my-profile') ) {
                                        $logout_redirect = get_bloginfo('siteurl');
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
                        do_action('cp_header_login_form');
                    }
                    ?>
                <div class="clear"></div>
            </div>        
        </div><!-- End Header-Wrapper -->        
        <div class="clear"></div>
        <div id="menu-wrapper"><!-- Start Menu-Wrapper -->
            <div id="cssmenu">
            <?php
                wp_nav_menu( array(
                    'theme_location' => 'header-menu',
                    'container' =>false,
                    'echo' => true,
                    'depth' => 0,
                    'menu_id' => ''
                ));
                ?>
            </div>
        </div><!-- End Menu-Wrapper -->    

        <?php do_action( 'bp_header' ); ?>
        <?php do_action( 'bp_after_header'); ?>
        
        <!-- **************** END HEADER *************** -->
        
        <div id="content-pattern"><!-- Start Content-Pattern -->
            <div id="content-wrapper"><!-- Start Content-Wrapper -->
                <div class="submenu"><!-- Start Sub Menu -->
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
                </div><!-- End Sub Menu -->
                <?php do_action( 'bp_before_container' ); ?>
                <div id="container"><!-- Start Container -->
                <div class="space25"></div>
