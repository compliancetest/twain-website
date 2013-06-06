<?php
/*
 * Template Name: My Profile
 */


if(is_user_logged_in()){
    global $current_user;
    
    $userInfo = get_user_meta( $current_user->ID );
    
    $fname = $userInfo['first_name'][0];
    $lname = $userInfo['last_name'][0];
    $uemail = $current_user->user_email;
    
    $card_number = get_user_meta($current_user->ID, 'card_number', true);
    
    if($card_number)
    {
        $card_number_string = chunk_split($card_number, 4);
    }else{
        $card_number_string = '-';
    }
    
    
    
    $name_on_card = get_user_meta($current_user->ID, 'name_on_card', true);
    $card_expiry = get_user_meta($current_user->ID, 'card_expiry', true);
    $card_cvc = get_user_meta($current_user->ID, 'card_cvc', true);
    
    $user_org = get_user_meta($current_user->ID, 'user_organisation', true);
    $user_org_web = get_user_meta($current_user->ID, 'user_organisation_web', true);
    $user_org_desc = get_user_meta($current_user->ID, 'user_organisation_desc', true);
    $user_org_abn = get_user_meta($current_user->ID, 'user_organisation_abn', true);
    
    
    $user = get_userdata( $current_user->ID );
    $user_status = $user->user_status;

	$capabilities = $user->{$wpdb->prefix . 'capabilities'};

	if ( !isset( $wp_roles ) )
		$wp_roles = new WP_Roles();

	foreach ( $wp_roles->role_names as $role => $name ):
		if ( array_key_exists( $role, $capabilities ) )
			$urole = ucfirst($role);
	endforeach;
    
}else{
    wp_redirect(home_url());
    exit;
}
get_header();
?>

<div class="content" id="my_profile">
	<div class="space25"></div>
	<div class="column fifth left nopaddingleft nopaddingright sidebar">
		<?php get_sidebar('dashboard'); ?>
	</div>
		
	<div class="column four_fifths right container">
          <?php if($user_status == 3){?>
                <div class="warning">Your email is not verified yet, please check your email address! <span>(resend email <a id="resend_email_verification" href="javascript: void(0);">link verification</a>)</span></div>
            <?php }?>
                
            <input type="hidden" name="user_id" value="<?php echo $current_user->ID;?>"/>
            
			<div class="column left three_fifths nopadding">
				<div class="default_grid" id="my_details">
					<div class="grid_head blue_head">
						<div class="grid_row">
							<div class="grid_cell width60P"><h5>My Details</h5></div>
							<div class="grid_cell">Role: <?php echo $urole;?></div>
                            <?php if($user_status != 3){?>
                                <div class="grid_cell grid_button grid_button_right"><a class="edit_btn" href="javascript: void(0);"><img src="<?php echo get_bloginfo('stylesheet_directory');?>/images/grid_button_pencil_icon_w.png" /><span class="simple_tooltip radius6">Edit Section<span></span></span></a></div>
                            <?php }?>
                            <div class="clear"></div>
						</div>
					</div>
                    <?php if($user_status != 3){?>
					<div class="grid_body">
                        <form action="#" method="post">
                            <div class="grid_row">
                                <div class="grid_cell width30P"><b>Name</b></div>
                                <div data-name="uname" data-value="<?php echo $lname.' '.$fname;?>" class="grid_cell in_input"><?php echo $lname.' '.$fname;?></div>
                                <div class="clear"></div>
                            </div>
                            <div class="grid_row">
                                <div class="grid_cell width30P"><b>Email</b></div>
                                <div data-name="email" data-value="<?php echo $uemail;?>" class="grid_cell in_input"><?php echo $uemail;?></div>
                                <div class="clear"></div>
                            </div>
                            <div class="grid_row">
                                <div class="grid_cell width30P"><b>Password</b></div>
                                <div data-name="new_pass" data-value="" class="grid_cell in_input input_pass">*********</div>
                                <div class="clear"></div>
                            </div>
                            <div class="grid_row">
                                <div class="grid_cell width30P"><b>Confirm Password</b></div>
                                <div data-name="conf_pass" data-value="" class="grid_cell in_input input_pass">*********</div>
                                <div class="clear"></div>
                                <input type="hidden" value="my_details_edit" name="cp-action" />
                            </div>
                            <div class="grid_row">                                
                                <a class="profile_btn button green_bcg white_txt button_small radius3">Save</a>
                                <div class="err_red errors_msg"></div>
                                <div class="clear"></div>
                            </div>
						</form>
					</div>
                    <?php } ?>
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
                            <?php if($user_status != 3){?>
                                <div class="grid_cell grid_button grid_button_right"><a class="edit_btn" href="javascript: void(0);"><img src="<?php echo get_bloginfo('stylesheet_directory');?>/images/grid_button_pencil_icon_w.png" /><span class="simple_tooltip radius6">Edit Section<span></span></span></a></div>
                                <div class="grid_cell grid_button grid_button_right"><a href="javascript: void(0);"><img src="<?php echo get_bloginfo('stylesheet_directory');?>/images/grid_button_magnifier_icon_w.png" /><span class="simple_tooltip radius6">View Statement<span></span></span></a></div>
                            <?php }?>
                            <div class="clear"></div>
						</div>
					</div>
					<div class="grid_body">
                        <form action="#" method="post">
                            <div class="grid_row">
                                <div class="grid_cell width30P"><b>Card Number</b></div>
                                <div data-name="card_number" data-value="<?php echo $card_number;?>" class="grid_cell in_input card_no"><?php echo !$card_number_string ? '-' : $card_number_string;?></div>
                                <div class="clear"></div>
                            </div>
                            <div class="grid_row">
                                <div class="grid_cell width30P"><b>Name on Card</b></div>
                                <div data-name="name_on_card" data-value="<?php echo $name_on_card;?>" class="grid_cell in_input"><?php echo !$name_on_card ? '-' : $name_on_card;?></div>
                                <div class="clear"></div>
                            </div>
                            <div class="grid_row">
                                <div class="grid_cell width30P"><b>Expiry</b></div>
                                <div data-name="card_expiry" data-value="<?php echo $card_expiry;?>" data-placeholder="M / Y" class="grid_cell in_input small_input card_expiry"><?php echo !$card_expiry ? '-' : $card_expiry;?></div>
                                <div class="clear"></div>
                            </div>
                            <div class="grid_row">
                                <div class="grid_cell width30P"><b>CVC</b></div>
                                <div data-name="card_cvc" data-value="<?php echo $card_cvc;?>" class="grid_cell in_input small_input"><?php echo !$card_cvc ? '-' : $card_cvc;?></div>
                                <div class="clear"></div>
                            </div>
                            <div class="grid_row">                                
                                <a class="profile_btn button green_bcg white_txt button_small radius3">Save</a>
                                <div class="err_red errors_msg"></div>
                                <div class="clear"></div>
                                <input type="hidden" name="cp-action" value="my_payment_edit" />
                                <input type="hidden" name="card_no" value="<?php echo $get_card_number;?>"/>
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
							<div class="grid_cell width60P"><h5>My Organisation</h5></div>
							<div class="grid_cell">Role: Issuer</div>
                            <?php if($user_status != 3){?>
                                <div class="grid_cell grid_button grid_button_right"><a class="edit_btn" href="javascript:void(0);"><img src="<?php echo get_bloginfo('stylesheet_directory');?>/images/grid_button_pencil_icon_w.png" /><span class="simple_tooltip radius6">Edit Section<span></span></span></a></div>
                            <?php }?>
                            <div class="clear"></div>
						</div>
					</div>
					<div class="grid_body">
                        <form action="#" method="post">
                            <div class="grid_row">
                                <div class="grid_cell width30P"><b>Name</b></div>
                                <div data-name="user_organisation" data-value="<?php echo $user_org;?>" class="grid_cell in_input width70P"><?php echo !$user_org ? '-' : $user_org;?></div>
                                <div class="clear"></div>
                            </div>
                            <div class="grid_row">
                                <div class="grid_cell width30P"><b>Website</b></div>
                                <div data-name="user_organisation_web" data-value="<?php echo $user_org_web;?>" class="grid_cell in_input"><?php echo !$user_org_web ? '-' : $user_org_web;?></div>
                                <div class="clear"></div>
                            </div>
                            <div class="grid_row">
                                <div class="grid_cell width30P"><b>Description</b></div>
                                <div data-name="user_organisation_desc" data-value="<?php echo $user_org_desc;?>" class="grid_cell in_input"><?php echo !$user_org_desc ? '-' : $user_org_desc;?></div>
                                <div class="clear"></div>
                            </div>
                            <div class="grid_row">
                                <div class="grid_cell width30P"><b>ABN</b></div>
                                <div data-name="user_organisation_abn" data-value="<?php echo $user_org_abn;?>" class="grid_cell in_input"><?php echo !$user_org_abn ? '-' : $user_org_abn;?></div>
                                <div class="clear"></div>
                            </div>
                            <div class="grid_row">
                                <div class="err_red errors_msg"></div>
                                <a class="profile_btn button green_bcg white_txt button_small radius3">Save</a>
                                <input type="hidden" name="cp-action" value="my_organisation_edit" />
                                <div class="clear"></div>
                            </div>
						</form>
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
				<div class="default_grid" id="my_org_mem" style="display: none;"><!--this is temporary hidden--->
					<div class="grid_head blue_head">
						<div class="grid_row">
							<div class="grid_cell width60P"><h5>My Organisation Members</h5></div>
                            <?php if($user_status != 3){?>
                                <div class="grid_cell grid_button grid_button_right"><a class="popup add_user_btn" href="javascript: void(0);"><img src="<?php echo get_bloginfo('stylesheet_directory');?>/images/grid_button_plus_icon_w.png" /><span class="simple_tooltip radius6">Add User<span></span></span></a></div>
                            <?php }?>
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
			
			<div class="column right two_fifths nopaddingtop nopaddingright nopaddingbottom" style="display: none;"><!--this is temporary hidden--->
				<div class="gray_message_box radius9 light_gray_txt">
					<div class="indicator"></div>
					<?php echo get_post_meta($post->ID, 'my_organization_members', true);?>
				</div>
			</div>
			<div class="clear"></div>
			
		</div>
		<div class="clear"></div>
			
</div> <!--end content-->

<script type="text/javascript">
	var wrapper = jQuery('<div/>').css({height:0,width:0,'overflow':'hidden'});
	var fileInput = jQuery(':file').wrap(wrapper);

	fileInput.change(function(){
		$this = jQuery(this);
		jQuery('#file_ts').text("File attached");
	})
	 
	jQuery('#file_ts').click(function(){
		fileInput.click();
	}).show(); 
</script>
<?php
get_footer();
?>
