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
    $timezone = get_user_meta($current_user->ID, 'timezone', true);
    $dashboard_page_url = get_user_meta($current_user->ID, 'dashboard_page_url', true);
    $dashboard_page_title = get_user_meta($current_user->ID, 'dashboard_page_title', true);
    $dashboard_page_url = ($dashboard_page_url == '') ? ('/my-profile') : ($dashboard_page_url);
    $dashboard_page_title = ($dashboard_page_title == '') ? ('Profile') : ($dashboard_page_title);
    
    $biography = get_user_meta($current_user->ID, 'description', true);
    
    $user_org = get_user_meta($current_user->ID, 'user_organisation', true);
    $user_org_web = get_user_meta($current_user->ID, 'user_organisation_web', true);
    $user_org_desc = get_user_meta($current_user->ID, 'user_organisation_desc', true);
    $user_org_abn = get_user_meta($current_user->ID, 'user_organisation_abn', true);
    
    
    $user = get_userdata( $current_user->ID );
    $user_status = $user->user_status;

	$capabilities = $user->{$wpdb->prefix . 'capabilities'};

}else{
    wp_redirect(home_url());
    exit;
}
get_header();
?>

<div class="content" id="my_profile">
	<div class="dashboard-tabs">
        <?php get_sidebar('dashboard'); ?>
    </div>
	<div class="container">
        <div class="column">
        <?php
            if(can_create_group()){
        ?>
        <a href="/communities/create" class="action-btn add-new-btn"><span class="p"></span><span class="t">New Community</span></a>
        <div class="clear"></div>
        <div class="space10"></div>
        <?php
            }                    
        ?>
          <?php if($user_status == 3){?>
                <div class="inner-warning">Your email is not verified yet, please check your email address! <span>(resend email <a id="resend_email_verification" href="<?php echo get_site_url()?>?cp-action=<?php echo wp_create_nonce('resend_email_verification')?>&uemail=<?php echo $current_user->user_email?>">link verification</a>)</span></div>
            <?php }?>
                
            <input type="hidden" name="user_id" value="<?php echo $current_user->ID;?>"/>
            
			<?php 
                include(dirname(__FILE__) . '/content/profile-mydetails.php');
            ?>			
            
            <div class="clear"></div>            
            <div class="space25"></div>            
            
            <?php 
                include(dirname(__FILE__) . '/content/profile-mypicture.php');
            ?>
            
            <div class="clear"></div>            
            <div class="space25"></div>
            
            <?php 
                include(dirname(__FILE__) . '/content/profile-myorganisation.php');
            ?>
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

<script type="text/javascript">
jQuery(document).ready(function(){
    fixTdHeight(jQuery('#cards-list'));
    //Fix Simple ToolTips
    jQuery('.td-status .simple_tooltip').each(function(){
        jQuery(this).css({'top': -1 * jQuery(this).outerHeight() - 6, 'margin-left': -1 * jQuery(this).outerWidth() / 2 + jQuery(this).parent().outerWidth() / 2});
    })
})
</script>
<?php
get_footer();
?>
