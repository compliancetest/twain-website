<?php
/*
 * Template Name: My Profile
 */
get_header();
?>

<div class="space25"></div>
<div class="content" id="my_profile">
	<div class="space25"></div>
	<div class="column fifth left nopaddingleft nopaddingright sidebar">
		<?php get_sidebar('dashboard'); ?>
	</div>
		
	<div class="column four_fifths right container">
			<div class="column left three_fifths nopadding">
				<div class="default_grid">
					<div class="grid_head blue_head">
						<div class="grid_row">
							<div class="grid_cell width60P"><h5>My Details</h5></div>
							<div class="grid_cell">Role: Administrator</div>
							<div class="grid_cell grid_button grid_button_right"><a href="#"><img src="<?php echo get_bloginfo('stylesheet_directory');?>/images/grid_button_pencil_icon_w.png" /><span class="simple_tooltip radius6">Edit Section<span></span></span></a></div>
							<div class="clear"></div>
						</div>
					</div>
					<div class="grid_body">
						<div class="grid_row">
							<div class="grid_cell width20P"><b>Name</b></div>
							<div class="grid_cell width40P">Steve A. Capell</div>
							<div class="clear"></div>
						</div>
						<div class="grid_row">
							<div class="grid_cell width20P"><b>Email</b></div>
							<div class="grid_cell width40P">steve.capell@acme.com</div>
							<div class="clear"></div>
						</div>
						<div class="grid_row">
							<div class="grid_cell width20P"><b>Password</b></div>
							<div class="grid_cell width40P">*********</div>
							<div class="clear"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="column right two_fifths nopaddingtop nopaddingright nopaddingbottom">
				<div class="gray_message_box radius9 light_gray_txt">
					<div class="indicator"></div>
					Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
				</div>
			</div>
			<div class="clear"></div>
			
			<div class="space25"></div>
			
			<div class="column left three_fifths nopadding">
				<div class="default_grid">
					<div class="grid_head blue_head">
						<div class="grid_row">
							<div class="grid_cell width60P"><h5>My Payment Method</h5></div>
							<div class="grid_cell grid_button grid_button_right"><a href="#"><img src="<?php echo get_bloginfo('stylesheet_directory');?>/images/grid_button_pencil_icon_w.png" /><span class="simple_tooltip radius6">Edit Section<span></span></span></a></div>
							<div class="grid_cell grid_button grid_button_right"><a href="#"><img src="<?php echo get_bloginfo('stylesheet_directory');?>/images/grid_button_magnifier_icon_w.png" /><span class="simple_tooltip radius6">View Statement<span></span></span></a></div>
							<div class="clear"></div>
						</div>
					</div>
					<div class="grid_body">
						<div class="grid_row">
							<div class="grid_cell width20P"><b>Card Number</b></div>
							<div class="grid_cell width40P">**** **** **** 2345</div>
							<div class="clear"></div>
						</div>
						<div class="grid_row">
							<div class="grid_cell width20P"><b>Name on Card</b></div>
							<div class="grid_cell width40P">Steve A. Capell</div>
							<div class="clear"></div>
						</div>
						<div class="grid_row">
							<div class="grid_cell width20P"><b>Expiry</b></div>
							<div class="grid_cell width40P">10/15</div>
							<div class="clear"></div>
						</div>
						<div class="grid_row">
							<div class="grid_cell width20P"><b>CVC</b></div>
							<div class="grid_cell width40P">***</div>
							<div class="clear"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="column right two_fifths nopaddingtop nopaddingright nopaddingbottom">
				<div class="gray_message_box radius9 light_gray_txt">
					<div class="indicator"></div>
					Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
				</div>
			</div>
			<div class="clear"></div>
			
			<div class="space25"></div>
			
			<div class="column left three_fifths nopadding">
				<div class="default_grid">
					<div class="grid_head blue_head">
						<div class="grid_row">
							<div class="grid_cell width60P"><h5>My Organization</h5></div>
							<div class="grid_cell">Role: Issuer</div>
							<div class="grid_cell grid_button grid_button_right"><a href="#"><img src="<?php echo get_bloginfo('stylesheet_directory');?>/images/grid_button_pencil_icon_w.png" /><span class="simple_tooltip radius6">Edit Section<span></span></span></a></div>
							<div class="clear"></div>
						</div>
					</div>
					<div class="grid_body">
						<div class="grid_row">
							<div class="grid_cell width20P"><b>Name</b></div>
							<div class="grid_cell width70P">ACME PTY LTD.</div>
							<div class="clear"></div>
						</div>
						<div class="grid_row">
							<div class="grid_cell width20P"><b>Website</b></div>
							<div class="grid_cell width70P">www.acme.com</div>
							<div class="clear"></div>
						</div>
						<div class="grid_row">
							<div class="grid_cell width20P"><b>Description</b></div>
							<div class="grid_cell width70P">ACME is a manufacturer of the payrole Software</div>
							<div class="clear"></div>
						</div>
						<div class="grid_row">
							<div class="grid_cell width20P"><b>ABN</b></div>
							<div class="grid_cell width70P">41 234 955 895</div>
							<div class="clear"></div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="column right two_fifths nopaddingtop nopaddingright nopaddingbottom">
				<div class="gray_message_box radius9 light_gray_txt">
					<div class="indicator"></div>
					Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
				</div>
			</div>
			<div class="clear"></div>
			
			<div class="space25"></div>
			
			<div class="column left three_fifths nopadding">
				<div class="default_grid">
					<div class="grid_head blue_head">
						<div class="grid_row">
							<div class="grid_cell width60P"><h5>My Organization Members</h5></div>
							<div class="grid_cell grid_button grid_button_right"><a href="#"><img src="<?php echo get_bloginfo('stylesheet_directory');?>/images/grid_button_plus_icon_w.png" /><span class="simple_tooltip radius6">Add User<span></span></span></a></div>
							<div class="clear"></div>
						</div>
					</div>
					<div class="grid_body">
						<div class="grid_row">
							<div class="grid_cell width60P"><b>Fred Smith</b></div>
							<div class="grid_cell width15P">Tester</div>
							<div class="grid_cell width15P green_txt"><b>Active</b></div>
							<div class="grid_cell grid_button grid_button_right"><a href="#"><img src="<?php echo get_bloginfo('stylesheet_directory');?>/images/grid_button_pencil_icon_g.png" /><span class="simple_tooltip radius6">Edit User<span></span></span></a></div>
							<div class="clear"></div>
						</div>
						<div class="grid_row">
							<div class="grid_cell width60P"><b>Marie Boyle</b></div>
							<div class="grid_cell width15P">Tester</div>
							<div class="grid_cell width15P green_txt"><b>Active</b></div>
							<div class="grid_cell grid_button grid_button_right"><a href="#"><img src="<?php echo get_bloginfo('stylesheet_directory');?>/images/grid_button_pencil_icon_g.png" /><span class="simple_tooltip radius6">Edit User<span></span></span></a></div>
							<div class="clear"></div>
						</div>
						<div class="grid_row">
							<div class="grid_cell width60P"><b>John Doe</b></div>
							<div class="grid_cell width15P">Admin</div>
							<div class="grid_cell width15P red_txt"><b>Suspended</b></div>
							<div class="grid_cell grid_button grid_button_right"><a href="#"><img src="<?php echo get_bloginfo('stylesheet_directory');?>/images/grid_button_pencil_icon_g.png" /><span class="simple_tooltip radius6">Edit User<span></span></span></a></div>
							<div class="clear"></div>
						</div>
						<div class="grid_row">
							<div class="grid_cell width60P"><b>Will Smith</b></div>
							<div class="grid_cell width15P">Tester</div>
							<div class="grid_cell width15P green_txt"><b>Active</b></div>
							<div class="grid_cell grid_button grid_button_right"><a href="#"><img src="<?php echo get_bloginfo('stylesheet_directory');?>/images/grid_button_pencil_icon_g.png" /><span class="simple_tooltip radius6">Edit User<span></span></span></a></div>
							<div class="clear"></div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="column right two_fifths nopaddingtop nopaddingright nopaddingbottom">
				<div class="gray_message_box radius9 light_gray_txt">
					<div class="indicator"></div>
					Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
				</div>
			</div>
			<div class="clear"></div>
			
		</div>
		<div class="clear"></div>
			
</div> <!--end content-->
</div>
<div class="space45"></div>
<div class="clear"></div>
</div>
<div class="clear"></div>
<script type="text/javascript">
	var wrapper = $('<div/>').css({height:0,width:0,'overflow':'hidden'});
	var fileInput = jQuery(':file').wrap(wrapper);

	fileInput.change(function(){
		$this = $(this);
		jQuery('#file_ts').text("File attached");
	})
	 
	jQuery('#file_ts').click(function(){
		fileInput.click();
	}).show(); 
</script>
<?php
get_footer();
?>
