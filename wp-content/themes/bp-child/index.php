<?php
get_header();
?>
<?php // echo get_theme_root(); ?>
		<div class="content">
            <h3 class="sub-title">Search for Test Suites or Certified Products</h3>
			<div id="search-wrapper">				
<!--				<div class="space7"></div>-->
				
				<div id="search_bar">                    
				  <form  role="search" method="get" id="searchform" action="<?php get_bloginfo('url'); ?>"  class="nomargintop">
					<input id="s" name="s" value=" " type="text" autocomplete="off" class="inactive_s">
					<div class="search_select_div">
						<div class="search_select">
							<ul>
								<li><a id="choose_one" class="current_chosen">Tests / Products</a>
									<ul>
										<li><a id="test-suite">Test Suites</a></li>
										<li><a id="product-service">Products</a></li>
									</ul>
								</li>	
							</ul>
						</div>
						<input type="hidden" name="post_type" value="" id="hidden_value"/>
					</div>

				   
					<input type="submit" id="searchsubmit" value="SEARCH">
					</form>
					<?php // echo get_search_form( $echo ); ?>

					<div class="clear"></div>
				</div><!-- end search_bar  -->
<!--				<div class="space7"></div>-->
			</div> <!-- END search-wrapper-->
			<div class="space30"></div>
			<h3 class="sub-title">
                Create a Test Suite or List Your Product
                <br />
                <small>Registered users only</small>
            </h3>            
			<div id="register">
				<div id="left_side" >	
					<div class="blue_box">
						<h2><?php echo of_get_option('lregister_box_content'); ?></h2>
						<div class="blue_box_button">	
							<a href=""><?php echo of_get_option('lregister_box_link_content'); ?></a>
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
							<a href=""><?php echo of_get_option('rregister_box_link_content'); ?></a>
							<img src="<?php echo bloginfo('stylesheet_directory'); ?>/images/shadow_blue_box_button_left.png" id="shadow_blue_box_button_left" />
							<img src="<?php echo bloginfo('stylesheet_directory'); ?>/images/shadow_blue_box_button_right.png" id="shadow_blue_box_button_right" />
						</div><!-- end blue_box_button -->
					</div><!-- end blue_box -->
					<div class="clear"></div>	
				</div><!-- end right_side-->
			</div><!-- end register -->
			<div class="clear"></div>
			<div class="space60"></div>
		<h3 class="sub-title">Browse and Join Any Compliance Community</h3>
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
	
<?php
get_footer();
?>
