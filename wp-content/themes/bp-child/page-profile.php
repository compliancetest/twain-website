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
	<div class="column fifth left nopaddingleft nopaddingright sidebar">
		<?php get_sidebar('dashboard'); ?>
	</div>		
	<div class="four_fifths right container">
        <div class="column">
        <?php
            if(can_create_group()){
        ?>
        <a href="/groups/create/" class="action-btn add-new-btn"><span class="p"></span><span class="t">Create Community</span></a>
        <div class="clear"></div>
        <div class="space10"></div>
        <?php
            }                    
        ?>
          <?php if($user_status == 3){?>
                <div class="inner-warning">Your email is not verified yet, please check your email address! <span>(resend email <a id="resend_email_verification" href="javascript: void(0);">link verification</a>)</span></div>
            <?php }?>
                
            <input type="hidden" name="user_id" value="<?php echo $current_user->ID;?>"/>
            
			<div class="left three_fifths">                
                <div class="grid-box" id="my_details">
                    <div class="grid-box-header">
                        <h5 class="left">My Details</h5>
                        <?php if($user_status != 3){?>
                            <a class="gbh-btn gbh-btn-edit right" href="javascript: void(0);">Edit<span class="simple_tooltip radius6">Edit this section<span></span></span></a>
                        <?php }?>
                        <span class="header-text right">Role: <?php echo $urole;?></span>
                        <div class="clear"></div>
                    </div>
                    <?php if($user_status != 3){?>
                    <div class="grid-box-body">
                        <form action="#" method="post">
                            <div class="grid-row">
                                <div class="grid-cell width30P"><label>Name</label></div>
                                <div data-name="uname" data-value="<?php echo $lname.' '.$fname;?>" class="grid-cell in_input"><?php echo $lname.' '.$fname;?></div>
                                <div class="clear"></div>
                            </div>
                            <div class="grid-row">
                                <div class="grid-cell width30P"><label>Email</label></div>
                                <div data-name="email" data-value="<?php echo $uemail;?>" class="grid-cell in_input"><?php echo $uemail;?></div>
                                <div class="clear"></div>
                            </div>
                            <div class="grid-row">
                                <div class="grid-cell width30P"><label>Password</label></div>
                                <div data-name="new_pass" data-value="" class="grid-cell in_input input_pass" data-type="password">*********</div>
                                <div class="clear"></div>
                            </div>
                            <div class="grid-row">
                                <div class="grid-cell width30P"><label>Confirm Password</label></div>
                                <div data-name="conf_pass" data-value="" class="grid-cell in_input input_pass" data-type="password">*********</div>
                                <div class="clear"></div>
                                <?php wp_nonce_field('my_details_edit', 'cp-action'); ?>
                            </div>
                            <div class="grid-row btn-row">                                
                                <a href="#" class="action-btn process-btn"><span class="p"></span><span class="t">Save</span></a>                                
                                <div class="clear"></div>
                            </div>
                        </form>
                    </div>
                    <?php } ?>
                </div>				
			</div>
			<div class="right two_fifths">
				<div class="gray_message_box radius9 light_gray_txt">
					<div class="indicator"></div>
					<?php echo get_post_meta($post->ID, 'my_details_desc', true);?>
				</div>
			</div>
			<div class="clear"></div>			
			<div class="space25"></div>			
            <?php
                $cards = getUserCreditCards();
            ?>
			<div class="column left three_fifths nopadding">
				<div class="grid-box" id="my_payment">
					<div class="grid-box-header">
						<h5 class="left">My Payment Method</h5>
                        <?php if($user_status != 3){?>                            
                            <a class="gbh-btn gbh-btn-add right" id="add-payment-method" href="javascript: void(0);">Add<span class="simple_tooltip radius6">Add Payment Method<span></span></span></a>
                            <a href="javascript: void(0);" class="gbh-btn gbh-btn-view-stats has-tooltip right">View<span class="simple_tooltip radius6">View Statement<span></span></span></a>
                        <?php }?>
                        <div class="clear"></div>
					</div>
					<div class="grid-box-body">
                        <div id="cards-list">
                          <?php if(!$cards){ ?>
                            <div class="grid-row">
                                <div class="grid-cell width100P">No Payment Method Found! Please add new one.</div>
                                <div class="clear"></div>
                            </div>
                          <?php }else{ ?>
                            <?php foreach($cards as $card){ ?>
                            <div class="grid-row grid-action-row">
                                <div class="grid-cell width25P">
                                    <?php echo $card->name?>
                                    <input type="hidden" id="cname" value="<?php echo $card->name?>" />
                                </div>
                                <div class="grid-cell width35P">
                                    <?php echo chunk_split($card->card_number, 4)?>
                                    <input type="hidden" id="cnumber" value="<?php echo $card->card_number?>" />                                    
                                </div>
                                <div class="grid-cell width10P">
                                    <?php echo $card->expiry?>
                                    <input type="hidden" id="cexpiry" value="<?php echo $card->expiry?>" />
                                </div>
                                <div class="grid-cell width10P">
                                    <?php echo $card->cvc?>
                                    <input type="hidden" id="ccvc" value="<?php echo $card->cvc?>" />
                                </div>
                                <div class="grid-cell grid-action-cell width20P">
                                    <a href="<?php echo get_permalink()?>?cp-action=<?php echo wp_create_nonce('delete_payment_method')?>&id=<?php echo $card->id ?>" class="delete-payment-method gbh-btn gbh-btn-delete-grey has-tooltip" data-id="<?php echo $card->id?>">Delete<span class="simple_tooltip radius6">Delete Card<span></span></span></a>
                                    <a href="<?php echo get_permalink()?>?cp-action=<?php echo wp_create_nonce('edit_payment_method')?>&id=<?php echo $card->id ?>" class="edit-payment-method gbh-btn gbh-btn-edit-grey has-tooltip" data-id="<?php echo $card->id?>">Edit<span class="simple_tooltip radius6">Edit Card<span></span></span></a>
                                </div>
                                <div class="clear"></div>
                            </div>
                            <?php } ?>
                          <?php } ?>
                        </div>
                        <div id="edit-card-form" style="display: none;">
                            <form action="#" method="post">
                                <div class="grid-row">
                                    <div class="grid-cell width30P"><label>Card Number</label></div>
                                    <input type="text" name="card_number" id="card_number" value="" class="input" autocomplete="off" /> 
                                    <small class="cnumber-desc"><i>(Don't change this if you want keep original number)</i></small>
                                    <div class="clear"></div> 
                                </div>
                                <div class="grid-row">
                                    <div class="grid-cell width30P"><label>Name on Card</label></div>
                                    <input type="text" name="name_on_card" id="name_on_card" value="" class="input" autocomplete="off" />                                    
                                    <div class="clear"></div>
                                </div>
                                <div class="grid-row">
                                    <div class="grid-cell width30P"><label>Expiry</label></div>
                                    <input type="text" name="card_expiry" id="card_expiry" value="" class="input small_input" placeholder="M / Y" autocomplete="off" /> 
                                    <div class="clear"></div> 
                                </div>
                                <div class="grid-row"> 
                                    <div class="grid-cell width30P"><label>CVC</label></div> 
                                    <input type="text" name="card_cvc" id="card_cvc" value="" class="input small_input" autocomplete="off" /> 
                                    <div class="clear"></div> 
                                </div> 
                                <div class="grid-row btn-row">
                                    <a href="#" class="action-btn process-btn"><span class="p"></span><span class="t">Save</span></a>
                                    <a href="#" class="action-btn cancel-btn left15"><span class="p"></span><span class="t">Cancel</span></a>
                                    <div class="clear"></div>
                                </div>
                                <?php wp_nonce_field('save_payment_method', 'cp-action'); ?>
                                <input type="hidden" name="id" id="id" value="" />
                            </form>
                        </div>
					</div>
				</div>
			</div>
			<div class="right two_fifths">
				<div class="gray_message_box radius9 light_gray_txt">
					<div class="indicator"></div>
					<?php echo get_post_meta($post->ID, 'my_payment_method_desc', true);?>
				</div>
			</div>
			<div class="clear"></div>
			
			<div class="space25"></div>
			
			<div class="column left three_fifths nopadding">
				<div class="grid-box" id="my_org">
					<div class="grid-box-header">
						<h5 class="left">My Organisation</h5>
						<?php if($user_status != 3){?>
                            <a class="gbh-btn gbh-btn-edit right" href="javascript: void(0);">Edit<span class="simple_tooltip radius6">Edit this section<span></span></span></a>
                        <?php }?>
                        <span class="header-text right">Role: <?php echo $urole;?></span>
                        <div class="clear"></div>
					</div>
					<div class="grid-box-body">
                        <form action="#" method="post">
                            <div class="grid-row">
                                <div class="grid-cell width30P"><label>Name</label></div>
                                <div data-name="user_organisation" data-value="<?php echo $user_org;?>" class="grid-cell in_input width70P"><?php echo !$user_org ? '-' : $user_org;?></div>
                                <div class="clear"></div>
                            </div>
                            <div class="grid-row">
                                <div class="grid-cell width30P"><label>Website</label></div>
                                <div data-name="user_organisation_web" data-value="<?php echo $user_org_web;?>" class="grid-cell in_input"><?php echo !$user_org_web ? '-' : $user_org_web;?></div>
                                <div class="clear"></div>
                            </div>
                            <div class="grid-row">
                                <div class="grid-cell width30P"><label>Description</label></div>
                                <div data-name="user_organisation_desc" data-value="<?php echo $user_org_desc;?>" class="grid-cell in_input"><?php echo !$user_org_desc ? '-' : $user_org_desc;?></div>
                                <div class="clear"></div>
                            </div>
                            <div class="grid-row">
                                <div class="grid-cell width30P"><label>ABN</label></div>
                                <div data-name="user_organisation_abn" data-value="<?php echo $user_org_abn;?>" class="grid-cell in_input"><?php echo !$user_org_abn ? '-' : $user_org_abn;?></div>
                                <div class="clear"></div>
                            </div>
                            <div class="grid-row btn-row">
                                <a href="#" class="action-btn process-btn"><span class="p"></span><span class="t">Save</span></a>
                                <?php wp_nonce_field('my_organisation_edit', 'cp-action'); ?>
                                <div class="clear"></div>
                            </div>
						</form>
					</div>
				</div>
			</div>
			
			<div class="right two_fifths">
				<div class="gray_message_box radius9 light_gray_txt">
					<div class="indicator"></div>
					<?php echo get_post_meta($post->ID, 'my_organisation_desc', true);?>
				</div>
			</div>
			<div class="clear"></div>
			
			<div class="space25"></div>
			
			<div class="column left three_fifths nopadding" style="display: none;">
				<div class="grid-box">
					<div class="grid-box-header">
                        <h5 class="left">My Organisation Members</h5>
                        <?php if($user_status != 3){?>
                            <a class="gbh-btn gbh-btn-add right" href="javascript: void(0);">Add<span class="simple_tooltip radius6">Add User<span></span></span></a>
                        <?php }?>
                        <div class="clear"></div>
                    </div>
					<div class="grid-box-body">
						<div class="grid-row grid-action-row">
							<div class="grid-cell width55P"><label>Fred Smith</label></div>
							<div class="grid-cell width20P">Tester</div>
							<div class="grid-cell width15P status-active">Active</div>
							<div class="grid-cell width10P grid-action-cell"><a href="#" class="gbh-btn gbh-btn-edit-grey">Edit</a></div>
							<div class="clear"></div>
						</div>
					</div>
				</div>
			</div>			
			<div class="right two_fifths"  style="display: none;"><!--this is temporary hidden--->
				<div class="gray_message_box radius9 light_gray_txt">
					<div class="indicator"></div>
					<?php echo get_post_meta($post->ID, 'my_organization_members_desc', true);?>
				</div>
			</div>
			<div class="clear"></div>			
		</div>
		<div class="clear"></div>
	</div>
    <div class="clear"></div>
</div> <!--end content-->

<?php
get_footer();
?>
