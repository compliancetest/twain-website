<?php
get_header();
?>
<?php // echo get_theme_root(); ?>
		<div class="content">
            <div class="space20"></div>
<!--            <h3 class="sub-title">Search for Test Suites or Certified Products</h3>-->
			<div id="search-wrapper">				
<!--				<div class="space7"></div>-->
				
				<div id="search_bar">                    
				  <form  role="search" method="get" id="searchform" action="<?php get_bloginfo('url'); ?>/search"  class="nomargintop">
					<input id="q" name="q" value="" type="text" autocomplete="off" class="inactive_s">
					<div class="search_select_div">
						<div class="search_select">
							<ul>
								<li><a id="choose_one" class="current_chosen">Test Suites</a>
									<ul>
										<li><a id="test-suite">Test Suites</a></li>
										<li><a id="product-service">Certified Products</a></li>
									</ul>
								</li>	
							</ul>
						</div>
						<input type="hidden" value="test-suite" id="hidden_value"/>
					</div>

				   
					<input type="submit" id="searchsubmit" value="SEARCH">
					</form>
					<?php // echo get_search_form( $echo ); ?>

					<div class="clear"></div>
				</div><!-- end search_bar  -->
<!--				<div class="space7"></div>-->
			</div> <!-- END search-wrapper-->
			<div class="space40"></div>
			<!--<h3 class="sub-title">
                Create a Test Suite or List Your Product
                <br />
                <small>Registered users only</small>
            </h3>            -->
			<div id="register">
				<div id="left_side" >	
					<div class="blue_box">
						<h2><?php echo of_get_option('lregister_box_content'); ?></h2>
						<div class="blue_box_button">	
							<a href="#under-construction" rel="custom-popup" data-type="inline"><?php echo of_get_option('lregister_box_link_content'); ?></a>
							<img src="<?php echo bloginfo('stylesheet_directory'); ?>/images/shadow_blue_box_button_left.png" id="shadow_blue_box_button_left" />
							<img src="<?php echo bloginfo('stylesheet_directory'); ?>/images/shadow_blue_box_button_right.png" id="shadow_blue_box_button_right" />
						</div><!-- end blue_box_button -->
					</div><!-- end blue_box -->
					<div class="clear"></div>	
				</div><!-- end left_side-->
				
				<div id="right_side">
					<div class="blue_box">
						<h2><?php echo of_get_option('rregister_box_content'); ?></h2>
						<div class="blue_box_button">	
							<a href="#under-construction" rel="custom-popup" data-type="inline"><?php echo of_get_option('rregister_box_link_content'); ?></a>
							<img src="<?php echo bloginfo('stylesheet_directory'); ?>/images/shadow_blue_box_button_left.png" id="shadow_blue_box_button_left" />
							<img src="<?php echo bloginfo('stylesheet_directory'); ?>/images/shadow_blue_box_button_right.png" id="shadow_blue_box_button_right" />
						</div><!-- end blue_box_button -->
					</div><!-- end blue_box -->
					<div class="clear"></div>	
				</div><!-- end right_side-->
			</div><!-- end register -->
			<div class="clear"></div>
			<div class="space60"></div>
<!--		<h3 class="sub-title">Browse and Join Any Compliance Community</h3>-->
		<div class="boxes">
			<div class="box" id="box1">
				<img src="<?php echo of_get_option('box_image1')?>" class="aligncenter">
                <h3><?php echo of_get_option('box_title1') ;?></h3>
                <p>
                    <b><?php echo of_get_option('box_content_title1') ;?></b><br />
                    <?php echo of_get_option('box_content1') ;?>
                </p>
				<a href="<?php echo of_get_option('box_linkto1') ;?>">Find out more</a>
			</div><!--end box1-->
			
			<div class="box" id="box2">
                <img src="<?php echo of_get_option('box_image2')?>" class="aligncenter">
                <h3><?php echo of_get_option('box_title2') ;?></h3>
                <p>
                    <b><?php echo of_get_option('box_content_title2') ;?></b><br />
                    <?php echo of_get_option('box_content2') ;?>
                </p>
                <a href="<?php echo of_get_option('box_linkto2') ;?>">Find out more</a>
            </div><!--end box2-->
			
			<div class="clear"></div>
		</div><!--end boxes-->
			
		<div class="space40"></div>	
		<?php // get_sidebar('homepage'); ?>
		<div class="space40"></div>	
		</div>
	<div id="under-construction" style="display: none; width: 350px" class="popup-box">
        <?php if(is_user_logged_in()){ ?>
        <div class="popup-box-header radius6 noradiusbottom">Notice!</div>
        <div class="popup-box-content">
            <p>
                This feature is under development.
            </p>
        </div>
        <?php }else{ ?>
        <div class="popup-box-header radius6 noradiusbottom">Notice</div>
        <div class="popup-box-content">
            <p>
                You must be a registered user to perform this action.
            </p>
        </div>
        <?php } ?>        
        <div class="popup-box-footer radius6 noradiustop">                        
            <a href="#registration-popup" data-type="inline" class="action-btn cancel-btn" onclick="jQuery('#under-construction .close_btn').click()"><span class="p"></span><span class="t">Close</span></a>
            <div class="clear"></div>
        </div>
        <a class="close_btn"></a>                
    </div> 
<?php
get_footer();
?>
