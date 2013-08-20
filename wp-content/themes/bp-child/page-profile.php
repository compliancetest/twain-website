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
    $phone = get_user_meta($current_user->ID, 'phone_number', true);
    
    $biography = get_user_meta($current_user->ID, 'description', true);
    
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
                <div class="inner-warning">Your email is not verified yet, please check your email address! <span>(resend email <a id="resend_email_verification" href="<?php echo get_site_url()?>?cp-action=<?php echo wp_create_nonce('resend_email_verification')?>&uemail=<?php echo $current_user->user_email?>">link verification</a>)</span></div>
            <?php }?>
                
            <input type="hidden" name="user_id" value="<?php echo $current_user->ID;?>"/>
            
			<div class="left three_fifths">                
                <div class="grid-box" id="my_details">
                    <div class="grid-box-header">
                        <h5 class="left">My Details</h5>
                        <?php if($user_status != 3){?>
                            <a class="gbh-btn gbh-btn-edit right" href="javascript: void(0);">Edit<span class="simple_tooltip radius6">Edit this section<span></span></span></a>
                            <a href="<?php bp_loggedin_user_link() ?>" class="gbh-btn gbh-btn-view-stats has-tooltip right">View<span class="simple_tooltip radius6">View Public Profile<span></span></span></a>
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
                                <div class="grid-cell width30P"><label>Phone Number</label></div>
                                <div data-name="phone_number" data-value="<?php echo $phone;?>" class="grid-cell in_input"><?php echo !$phone ? '-' : $phone;?></div>
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
                            </div>
                            <div class="grid-row">
                                <div class="grid-cell width30P"><label>About me</label></div>
                                <div data-name="biography" data-value="<?php echo $biography?>" class="grid-cell in_input" data-type="textarea"><?php echo !$biography ? '-' : _convertLineSymbolToBR($biography)?></div>
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
                <div class="clear"></div>            
			</div>
			<div class="right two_fifths">
				<div class="gray_message_box radius9 light_gray_txt">
					<div class="indicator"></div>
					<?php echo get_post_meta($post->ID, 'my_details_desc', true);?>
				</div>
			</div>
			
            <div class="clear"></div>            
            <div class="space25"></div>            
            
            <div class="column left three_fifths nopadding">
                <div class="grid-box" id="my_avatar">
                    <div class="grid-box-header">
                        <h5>My Picture</h5>
                        <div class="clear"></div>
                    </div>
                    <div class="grid-box-body">
                        <div class="grid-row">
                          <form action="" method="post" id="avatar-upload-form" class="standard-form" enctype="multipart/form-data">                                        
                            <?php if ( 'crop-image' == bp_get_avatar_admin_step() ){ ?> <!-- Crop Image -->
                                <p><?php _e( 'Crop Your New Avatar', 'buddypress' ); ?></p>

                                <img src="<?php bp_avatar_to_crop(); ?>" id="avatar-to-crop" class="avatar" alt="<?php _e( 'Avatar to crop', 'buddypress' ); ?>" />

                                <div id="avatar-crop-pane" class="left">
                                    <img src="<?php bp_avatar_to_crop(); ?>" id="avatar-crop-preview" class="avatar" alt="<?php _e( 'Avatar preview', 'buddypress' ); ?>" />
                                </div>

                                <a href="#" class="action-btn submit-btn process-btn right" style="margin-top: 120px; margin-right: 10px;"><span class="p"></span><span class="t">Crop Image</span></a>
                                <div class="clear"></div>
                                <div class="space10"></div>
                                <input type="hidden" name="avatar-crop-submit" id="avatar-crop-submit" value="<?php _e( 'Crop Image', 'buddypress' ); ?>" />

                                <input type="hidden" name="image_src" id="image_src" value="<?php bp_avatar_to_crop_src(); ?>" />
                                <input type="hidden" id="x" name="x" value="" />
                                <input type="hidden" id="y" name="y" value="" />
                                <input type="hidden" id="w" name="width" value="" />
                                <input type="hidden" id="h" name="height" value="" />

                                <?php wp_nonce_field( 'bp_avatar_cropstore' ); ?>
                            <?php }else{?> <!-- Upload Avatar -->
                                <div class="grid-cell width40P">
                                    <a href="<?php bp_loggedin_user_link(); ?>">                                    
                                        <?php bp_loggedin_user_avatar( 'type=full' ); ?>
                                    </a>
                                </div>
                                <div class="grid-cell width60P">
                                    <p>Your avatar will be used on your profile and throughout the site.</p>
                                    <?php if($user_status != 3){?>            
                                    <p>Click below to select a JPG, GIF or PNG format photo from your computer and then click 'Upload Image' to proceed.</p>
                                    <p>
                                        
                                            <input type="file" name="file" id="file" class="left input-file" /><br />
                                            <a href="#" class="action-btn submit-btn process-btn"><span class="p"></span><span class="t">Upload Image</span></a>
                                            <?php if ( bp_get_user_has_avatar($current_user->ID) ){ ?>
                                            <a href="<?php echo get_permalink()?>?cp-action=<?php echo wp_create_nonce('delete-avatar')?>" class="action-btn delete-btn left15"><span class="p"></span><span class="t">Delete My Avatar</span></a>
                                            <?php } ?>
                                            <input type="hidden" name="action" id="action" value="bp_avatar_upload" />
                                            <input type="hidden" name="upload" id="action" value="<?php _e( 'Upload Image', 'buddypress' ); ?>" />
                                            <?php wp_nonce_field( 'bp_avatar_upload' ); ?>                                    
                                    </p>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                            <div class="clear"></div>
                          </form>  
                        </div>
                    </div>
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
                <div class="grid-box table-box" id="my_community_memberships">
                    <div class="grid-box-header">
                        <h5>My Community Memberships</h5>
                        <div class="clear"></div>
                    </div>
                    <div class="grid-box-body">
                        <div class="thead tr">
                           <div class="td td-name">Name</div>
                           <div class="td td-since">Since</div>
                           <div class="td td-role">Role</div>
                           <div class="td td-action">Action</div>
                           <div class="clear"></div>
                       </div>
                       <div class="tbody">
                       <?php
                           $groups =  groups_get_user_groups($current_user->ID);
                           if($groups['total'] < 1)
                           {
                       ?>
                           <div class="tr">
                               <div class="td td-full">There is no community that you joined.</div>
                               <div class="clear"></div>
                           </div> 
                       <?php
                           }else{
                               foreach($groups['groups'] as $gID)
                               {
                                   $group = groups_get_group(array('group_id'=>$gID));
                                   $member = getGroupMemberDetail($gID, $current_user->ID);
                                   
                       ?>
                            <div class="tr">
                                <div class="td td-name">
                                    <a href="<?php echo bp_get_group_permalink($group)?>"><?php echo bp_get_group_name($group) ?></a>
                                </div>
                                <div class="td td-since"><?php echo formatDate($member->date_modified); ?></div>
                                <div class="td td-role">
                                    <?php
                                        if($member->is_admin)
                                            echo '<span class="group-admin">Admin</span>';
                                        else if($member->is_mod)
                                            echo '<span class="group-moderator">Moderator</span>';
                                        else 
                                            echo '<span class="group-member">Member</span>';
                                    ?>
                                </div>
                                <div class="td td-action">
                                    <a href="?cp-action=<?php echo wp_create_nonce('leave-group') ?>&group_id=<?php echo $gID ?>" class="leave-community-link">Remove</a>
                                </div>
                                <div class="clear"></div>
                            </div>
                       <?php
                               }
                           }
                       ?>
                       <div class="loading1"></div>
                       </div>
                       
                    </div>                    
                </div>
            </div>
            <div class="right two_fifths">
                <div class="gray_message_box radius9 light_gray_txt">
                    <div class="indicator"></div>
                    <?php echo get_post_meta($post->ID, 'my_community_memberships_desc', true);?>
                </div>
            </div>
            <div class="clear"></div>            
            <div class="space25"></div>            
            
            <div class="column left three_fifths nopadding">
                <div class="grid-box table-box" id="my_subscriptions">
                    <div class="grid-box-header">
                        <h5>My Test Suite Subscriptions</h5>
                        <div class="clear"></div>
                    </div>
                    <div class="grid-box-body">
                        <div class="thead tr">
                           <div class="td td-suite">Test Suite</div>
                           <div class="td td-fee">Fee</div>
                           <div class="td td-action">Action</div>
                           <div class="clear"></div>
                       </div>
                       <div class="tbody">
                       <?php
                           $subscriptions =  getUserSubscriptions();
                           if(count($subscriptions) < 1)
                           {
                       ?>
                           <div class="tr">
                               <div class="td td-full">No subscription recorded yet.</div>
                               <div class="clear"></div>
                           </div> 
                       <?php
                           }else{
                               foreach($subscriptions as $row)
                               {
                                   
                       ?>
                            <div class="tr">
                                <div class="td td-suite">
                                    <a href="<?php echo get_permalink($row->suite_id)?>"><?php echo get_post_meta($row->suite_id, 'ts_name',  true) ?></a>
                                </div>
                                <div class="td td-fee">$<?php echo get_post_meta($row->suite_id, 'monthly_subscription_price', true); ?>/m</div>
                                <div class="td td-action">
                                    <a href="?_paymentnonce=<?php echo wp_create_nonce('unsubscribe') ?>&id=<?php echo $row->id ?>" class="harness-detail-link" data-id="<?php echo $row->id?>">Harness Detail</a>
                                    | 
                                    <a href="?_paymentnonce=<?php echo wp_create_nonce('unsubscribe') ?>&id=<?php echo $row->id ?>" class="unsubscribe-link">Unsubscribe</a>
                                </div>
                                <input type="hidden" id="msh_p_mode<?php echo $row->id?>" value="<?php echo $row->msh_p_mode?>" />
                                <input type="hidden" id="msh_url<?php echo $row->id?>" value="<?php echo $row->msh_url?>" />
                                <input type="hidden" id="msh_username<?php echo $row->id?>" value="<?php echo $row->msh_username?>" />
                                <input type="hidden" id="msh_password<?php echo $row->id?>" value="<?php echo $row->msh_password?>" />
                                <div class="clear"></div>
                            </div>
                       <?php
                               }
                           }
                       ?>
                       <div class="loading1"></div>
                       </div>
                       
                    </div>                    
                </div>
            </div>
            <div class="right two_fifths">
                <div class="gray_message_box radius9 light_gray_txt">
                    <div class="indicator"></div>
                    <?php echo get_post_meta($post->ID, 'my_subscriptions_desc', true);?>
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
<div class="popup-box" id="harness-detail-box" style="display: none; width: 370px;">
    <div class="popup-box-header radius6 noradiusbottom">Test Harness Access Detail.</div>        
    <form name="harness-form" id="harness-form" action="">
        <div class="popup-box-content grid-box-body">    
            <div class="field-row">
                <div class="grid-cell">
                    <label>P Mode:</label>
                    <select name="msh_p_mode" id="msh_p_mode" class="select">
                        <option value="PUSH">PUSH</option>
                        <option value="PULL">PULL</option>
                    </select>
                </div>
                <div class="clear"></div>
            </div>
            <div class="field-row">
                <div class="grid-cell">
                    <label>EndPoint URL:</label>
                    <input class="input" type="text" name="msh_url" id="msh_url" value="" />
                </div>
                <div class="clear"></div>
            </div>
            <div class="field-row">
                <div class="grid-cell">
                    <label>Username:</label>
                    <input class="input" type="text" name="msh_username" id="msh_username" value="" />
                </div>
                <div class="clear"></div>
            </div>            
            <div class="field-row">
                <div class="grid-cell">
                    <label>Password:</label>
                    <input class="input" type="text" name="msh_password" id="msh_password" value="" />
                </div>
                <div class="clear"></div>
            </div>                        
        </div>
        <div class="popup-box-footer radius6 noradiustop">                                    
            <a href="#" class="action-btn process-btn submit-btn"><span class="p"></span><span class="t">SAVE</span></a>            
            <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Close</span></a>            
            <div class="clear"></div>
        </div>
        <div class="loading"></div>
        <a class="close_btn"></a>
        <input type="hidden" name="id" id="harness-id" value="" />
        <?php wp_nonce_field('save-harness', 'cp-action'); ?>
    </form>
</div>
<script type="text/javascript">
jQuery(document).ready(function(){
    fixTdHeight(jQuery('#my_community_memberships'));
    fixTdHeight(jQuery('#my_subscriptions'));
})
</script>
<?php
get_footer();
?>
