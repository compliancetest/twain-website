<?php
get_header();
?>
<?php // echo get_theme_root(); ?>
		<div class="content">
			<div class="space50"></div>
			<div id="search-wrapper">
				
				<div class="space7"></div>
				
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
				<div class="space7"></div>
			</div> <!-- END search-wrapper-->
			<div class="space50"></div>
			
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
			<div class="space80"></div>
		
		<div class="boxes">
			<div class="box1">
				<img src="<?php echo of_get_option('box_image1')?>" class="aligncenter">
				<a href="<?php echo of_get_option('box_1_linkto') ;?>"><h3><?php echo of_get_option('box_title1')?></h3></a>
				<a href="<?php echo of_get_option('1box_item1_linkto');?>"><h4><?php echo of_get_option('1box_item1');?></h4></a>
				<a href="<?php echo of_get_option('1box_item2_linkto');?>"><h4><?php echo of_get_option('1box_item2');?></h4></a>
				<a href="<?php echo of_get_option('1box_item3_linkto');?>"><h4><?php echo of_get_option('1box_item3');?></h4></a>
			</div><!--end box1-->
			
			<div class="box2">
				<img src="<?php echo of_get_option('box_image2')?>" class="aligncenter">
				<a href="<?php echo of_get_option('box_2_linkto') ;?>"><h3><?php echo of_get_option('box_title2')?></h3></a>
				<a href="<?php echo of_get_option('2box_item1_linkto');?>"><h4><?php echo of_get_option('2box_item1');?></h4></a>
				<a href="<?php echo of_get_option('2box_item2_linkto');?>"><h4><?php echo of_get_option('2box_item2');?></h4></a>
				<a href="<?php echo of_get_option('2box_item3_linkto');?>"><h4><?php echo of_get_option('2box_item3');?></h4></a>
			</div><!--end box2-->
			
			<div class="box3">
				<img src="<?php echo of_get_option('box_image3')?>" class="aligncenter">
				<a href="<?php echo of_get_option('box_3_linkto') ;?>"><h3><?php echo of_get_option('box_title3')?></h3></a>
				<a href="<?php echo of_get_option('3box_item1_linkto');?>"><h4><?php echo of_get_option('3box_item1');?></h4></a>
				<a href="<?php echo of_get_option('3box_item2_linkto');?>"><h4><?php echo of_get_option('3box_item2');?></h4></a>
				<a href="<?php echo of_get_option('3box_item3_linkto');?>"><h4><?php echo of_get_option('3box_item3');?></h4></a>
			</div><!--end box3-->
			<div class="clear"></div>
		</div><!--end boxes-->
			
		<div class="space40"></div>	
		<?php get_sidebar('homepage'); ?>
		<div class="space40"></div>	
		</div>
	</div>
</div>
<div class="clear"></div>

<?php
get_footer();
?>
