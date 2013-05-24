<?php
/*
 * Template Name: My Profile
 */
get_header();

if(is_user_logged_in()){
    global $current_user;
    
    $userInfo = get_user_meta( $current_user->ID );
    $fname = $userInfo['first_name'][0] =='' ? '-' : $userInfo['first_name'][0];
    $lname = $userInfo['last_name'][0] =='' ? '-' : $userInfo['last_name'][0];
    $uemail = $current_user->user_email;
    
    $user = get_userdata( $current_user->ID );

	$capabilities = $user->{$wpdb->prefix . 'capabilities'};

	if ( !isset( $wp_roles ) )
		$wp_roles = new WP_Roles();

	foreach ( $wp_roles->role_names as $role => $name ):
		if ( array_key_exists( $role, $capabilities ) )
			$urole = $role;
	endforeach;
    
}else{
    wp_redirect(home_url());
}
?>

<div class="space25"></div>
<div class="content" id="my_profile">
	<div class="space25"></div>
	<div class="column fifth left nopaddingleft nopaddingright sidebar">
		<?php get_sidebar('dashboard'); ?>
	</div>
		
	<div class="column four_fifths right container">
			<div class="column left three_fifths nopadding">
				<div class="default_grid" id="my_details">
					<div class="grid_head blue_head">
						<div class="grid_row">
							<div class="grid_cell width60P"><h5>My Details</h5></div>
							<div class="grid_cell">Role: <?php echo $urole;?></div>
							<div class="grid_cell grid_button grid_button_right"><a class="edit_btn" href="javascript: void(0);"><img src="<?php echo get_bloginfo('stylesheet_directory');?>/images/grid_button_pencil_icon_w.png" /><span class="simple_tooltip radius6">Edit Section<span></span></span></a></div>
							<div class="clear"></div>
						</div>
					</div>
					<div class="grid_body">
                        <form action="#" method="post">
                            <div class="grid_row">
                                <div class="grid_cell width20P"><b>Name</b></div>
                                <div name="uname" class="grid_cell in_input"><?php echo $lname.' '.$fname;?></div>
                                <input type="hidden" name="user_id" value="<?php echo $current_user->ID;?>"/>
                                <div class="clear"></div>
                            </div>
                            <div class="grid_row">
                                <div class="grid_cell width20P"><b>Email</b></div>
                                <div name="email" class="grid_cell in_input"><?php echo $uemail;?></div>
                                <div class="clear"></div>
                            </div>
                            <div class="grid_row">
                                <div class="grid_cell width20P"><b>Password</b></div>
                                <div name="new_pass" class="grid_cell in_input input_pass">*********</div>
                                <div class="clear"></div>
                            </div>
                            <div class="grid_row">
                                <div class="grid_cell width20P"><b>Confirm Password</b></div>
                                <div name="conf_pass" class="grid_cell in_input input_pass">*********</div>
                                <div class="clear"></div>
                                <input type="hidden" name="my_details_edit" value="1" />
                            </div>
                            <div class="grid_row">
                                <div class="err_red errors_msg"></div>
                                <a class="profile_btn button green_bcg white_txt button_small radius3">Save</a>
                                <div class="clear"></div>
                            </div>
						</form>
					</div>
				</div>
			</div>
			<div class="column right two_fifths nopaddingtop nopaddingright nopaddingbottom">
				<div class="gray_message_box radius9 light_gray_txt">
					<div class="indicator"></div>
					<?php echo get_post_meta($post->ID, 'my_details_text', true);?>
				</div>
			</div>
			<div class="clear"></div>
			
			<div class="space25"></div>
			
			<div class="column left three_fifths nopadding">
				<div class="default_grid" id="my_payment">
					<div class="grid_head blue_head">
						<div class="grid_row">
							<div class="grid_cell width60P"><h5>My Payment Method</h5></div>
							<div class="grid_cell grid_button grid_button_right"><a class="edit_btn" href="javascript: void(0);"><img src="<?php echo get_bloginfo('stylesheet_directory');?>/images/grid_button_pencil_icon_w.png" /><span class="simple_tooltip radius6">Edit Section<span></span></span></a></div>
							<div class="grid_cell grid_button grid_button_right"><a href="javascript: void(0);"><img src="<?php echo get_bloginfo('stylesheet_directory');?>/images/grid_button_magnifier_icon_w.png" /><span class="simple_tooltip radius6">View Statement<span></span></span></a></div>
							<div class="clear"></div>
						</div>
					</div>
					<div class="grid_body">
                        <form action="#" method="post">
                            <div class="grid_row">
                                <div class="grid_cell width20P"><b>Card Number</b></div>
                                <div name="card_number" class="grid_cell in_input"><?php echo get_user_meta($current_user->ID, 'card_number', true);?></div>
                                <div class="clear"></div>
                            </div>
                            <div class="grid_row">
                                <div class="grid_cell width20P"><b>Name on Card</b></div>
                                <div name="name_on_card" class="grid_cell in_input"><?php echo get_user_meta($current_user->ID, 'name_on_card', true);?></div>
                                <div class="clear"></div>
                            </div>
                            <div class="grid_row">
                                <div class="grid_cell width20P"><b>Expiry</b></div>
                                <div name="card_expiry" class="grid_cell in_input"><?php echo get_user_meta($current_user->ID, 'card_expiry', true);?></div>
                                <div class="clear"></div>
                            </div>
                            <div class="grid_row">
                                <div class="grid_cell width20P"><b>CVC</b></div>
                                <div name="card_cvc" class="grid_cell in_input"><?php echo get_user_meta($current_user->ID, 'card_cvc', true);?></div>
                                <div class="clear"></div>
                            </div>
                            <div class="grid_row">
                                <div class="err_red errors_msg"></div>
                                <a class="profile_btn button green_bcg white_txt button_small radius3">Save</a>
                                <div class="clear"></div>
                                <input type="hidden" name="my_payment_edit" value="1" />
                                <input type="hidden" name="user_id" value="<?php echo $current_user->ID;?>"/>
                            </div>
                        </form>
					</div>
				</div>
			</div>
			<div class="column right two_fifths nopaddingtop nopaddingright nopaddingbottom">
				<div class="gray_message_box radius9 light_gray_txt">
					<div class="indicator"></div>
					<?php echo get_post_meta($post->ID, 'my_payment_method', true);?>
				</div>
			</div>
			<div class="clear"></div>
			
			<div class="space25"></div>
			
			<div class="column left three_fifths nopadding">
				<div class="default_grid" id="my_org">
					<div class="grid_head blue_head">
						<div class="grid_row">
							<div class="grid_cell width60P"><h5>My Organization</h5></div>
							<div class="grid_cell">Role: Issuer</div>
							<div class="grid_cell grid_button grid_button_right"><a class="edit_btn" href="javascript:void(0);"><img src="<?php echo get_bloginfo('stylesheet_directory');?>/images/grid_button_pencil_icon_w.png" /><span class="simple_tooltip radius6">Edit Section<span></span></span></a></div>
							<div class="clear"></div>
						</div>
					</div>
					<div class="grid_body">
						<div class="grid_row">
							<div class="grid_cell width20P"><b>Name</b></div>
							<div class="grid_cell in_input width70P">ACME PTY LTD.</div>
							<div class="clear"></div>
						</div>
						<div class="grid_row">
							<div class="grid_cell width20P"><b>Website</b></div>
							<div class="grid_cell in_input width70P">www.acme.com</div>
							<div class="clear"></div>
						</div>
						<div class="grid_row">
							<div class="grid_cell width20P"><b>Description</b></div>
							<div class="grid_cell in_input width70P">ACME is a manufacturer of the payrole Software</div>
							<div class="clear"></div>
						</div>
						<div class="grid_row">
							<div class="grid_cell width20P"><b>ABN</b></div>
							<div class="grid_cell in_input width70P">41 234 955 895</div>
							<div class="clear"></div>
						</div>
                        <div class="grid_row">
                            <a class="profile_btn button green_bcg white_txt button_small radius3">Save</a>
                            <div class="clear"></div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="column right two_fifths nopaddingtop nopaddingright nopaddingbottom">
				<div class="gray_message_box radius9 light_gray_txt">
					<div class="indicator"></div>
					<?php echo get_post_meta($post->ID, 'my_organization', true);?>
				</div>
			</div>
			<div class="clear"></div>
			
			<div class="space25"></div>
			
			<div class="column left three_fifths nopadding">
				<div class="default_grid" id="my_org_mem">
					<div class="grid_head blue_head">
						<div class="grid_row">
							<div class="grid_cell width60P"><h5>My Organization Members</h5></div>
							<div class="grid_cell grid_button grid_button_right"><a class="edit_btn" href="javascript: void(0);"><img src="<?php echo get_bloginfo('stylesheet_directory');?>/images/grid_button_plus_icon_w.png" /><span class="simple_tooltip radius6">Add User<span></span></span></a></div>
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
					<?php echo get_post_meta($post->ID, 'my_organization_members', true);?>
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
