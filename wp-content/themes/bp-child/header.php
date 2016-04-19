<!DOCTYPE HTML>
<html>
    <head profile="http://gmpg.org/xfn/11">
        <meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />        
        <link rel="icon" href="<?php echo CHILD_TEMPLATE_DIRECTORY ?>/images/drummond_group_logo.png" type="image/x-icon">
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
            <div class="site-header">
                <?php
                if ( is_user_logged_in() ) {
                    ?>
                    <div class="header-actions">
                        <?php
                        global $current_user;
                        get_currentuserinfo();
                        ?>
                        <div id="top_loged_actions">
                            <ul>
                                <li class="dropdown leftmenu">
                                    <a href="javascript:void(0)" class="blue-btn action-btn icon-btn dashboard-btn">
                                        <span class="p"></span>
                                        <span class="t">Dashboard</span>
                                    </a>
                                    <?php echo getDashboardMenuHTML(getDashboardPages('menu'), 'dashboard-dropdown-menu'); ?>
                                </li>
                                <li>
                                    <a href="<?php echo wp_logout_url( get_bloginfo('siteurl') ); ?>" class="red-btn action-btn icon-btn logout-btn">
                                        <span class="p"></span>
                                        <span class="t">Logout</span>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="header-user-info">
                            <?php $userAvatar = get_avatar($current_user->user_email, 32);?>
                            <?php if(strpos($userAvatar, 'mystery-man') !== false):?>
                                <img src="<?php echo DEFAULT_AVATAR;?>" class="avatar user-1-avatar avatar-32 photo" alt="Avatar" width="32" height="32">
                            <?php else:?>
                                <?php echo get_avatar($current_user->user_email, 32);  ?>
                            <?php endif;?>
                            <div class="header-welcome">
                                Welcome
                                <strong class="header-username"><?php echo cp_get_user_display_name($current_user) ;?></strong>
                            </div>
                        </div>
                        <?php get_template_part( 'header-search-form' ); ?>
                    </div>

                <?php
                } else {
                    do_action('cp_header_login_form');
                }
                ?>
                <a href="<?php bloginfo('url'); ?>" class="header-logo"><img src="<?php echo of_get_option('logo'); ?>" style="height: 52px;"/></a>
                <?php if (of_get_option('msg_of_day_title') || of_get_option('msg_of_day_content')): ?>
                <div class="message-of-day-head">
                    <div class="message-of-day-head-inner">
                        <div class="message-of-day-head-title"><?php echo of_get_option('msg_of_day_title') ?></div>
                        <?php echo of_get_option('msg_of_day_content') ?>
                    </div>
                </div>
                <?php endif; ?>
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
                <?php do_action( 'template_notices' ) ?>
                <div id="container"><!-- Start Container -->
                <?php if(!is_home()): ?>
                    <div class="space25"></div>
                <?php endif; ?>
