<?php get_header( 'buddypress' ); ?>

	<div class="space25"></div>
    <div id="content" class="content container">
		<div class="padder">

			<?php if ( bp_has_groups() ) : while ( bp_groups() ) : bp_the_group(); ?>

			<?php do_action( 'bp_before_group_home_content' ); ?>

			<div id="item-header" role="complementary">

				<?php locate_template( array( 'groups/single/group-header.php' ), true ); ?>

			</div><!-- #item-header -->
            
            <div id="issuer_content_block" class="column">
                <div class="tabs_wrap light_gray_bcg radius6">
			        <div id="item-nav">
				        <div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
					        <?php locate_template( array('groups/single/group-nav.php'), true); ?>
                            <div class="clear"></div>
				        </div>
			        </div><!-- #item-nav -->

			        <div id="item-body">                                                
                        <?php 
                            if(bp_is_group_home())
                            {
                                locate_template( array('groups/single/testsuites.php'), true);     
                            }
                            
                            if(bp_group_is_visible()) //Verified Member
                            {
                                if( bp_is_group_admin_page() ) 
                                    locate_template( array( 'groups/single/admin.php'        ), true );
                                else if( bp_is_group_members()) 
                                    locate_template( array( 'groups/single/members.php'      ), true );
                                else if( bp_is_group_forum()      ) 
                                    locate_template( array( 'groups/single/forum.php'        ), true );
                                
                            }else{ //Show Tabs
                        ?>
                            <div id="wiki-container" class="tab-content column white_bcg"></div>
                            <div id="forum-container" class="tab-content column white_bcg">
                                <?php if(!is_user_logged_in() || !bp_group_is_member()){ ?>
                                <p>You need to join the community to participate in the forum section.</p>
                                <?php } ?>                            
                            </div>
                            <div id="downloads-container" class="tab-content column white_bcg">
                                <?php if(!is_user_logged_in() || !bp_group_is_member()){ ?>
                                <p>You need to join the community to participate in the download section.</p>
                                <?php } ?>                            
                            </div>
                        <?php
                            }
                            
                        ?>
			        </div><!-- #item-body -->
                    <?php
                         
                    ?>
                </div>
            </div>
			<?php do_action( 'bp_after_group_home_content' ); ?>

			<?php endwhile; endif; ?>

		</div><!-- .padder -->
	</div><!-- #content -->
<div id="mask_community">
    <div id="community-wrap">
        <div id="community_registration" class="radius6">
            <p class="headline nomarginbottom">Community Registration</p>
                <form method="post" action="" id="join-community-id">
                    <div id="community_content">
                        
                            <p>You need to join the community od interest in order to view Test Cases and Participate in the Forum</p>
                            <div class="grey-border-bottom"></div>
                            <div class="grid_cell width100P left">
                                        <span class="left padding5-10-5-0">Your Role: </span>
                                        <div class="styled_select left">
                                            <select name="role" id="role_id">
                                                 <option value="">Select a role</option>
                                                <?php 
                                                foreach($roles_select as $role_select){
                                                    echo '<option value="'.$role_select.'">'.$role_select.'</option>';
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="clear"></div>
                            </div>
                            <div class="grid_cell width100P left">
                                <input type="checkbox" name="agree_terms" value="agree" id="agree_terms_id"> I agree with <a href="http://nego-solutions.com/dev-clients/compliance/terms-conditions/" class="normal">Terms & Conditions</a>
                                <div class="clear"></div>
                                <div class="space5"></div>
                                <input type="checkbox" name="agree_license" value="agree_license" id="agree_license_id"> I agree with <a href="http://nego-solutions.com/dev-clients/compliance/license-agreement/" class="normal">License Agreement</a>
                                <div class="clear"></div>
                                <div class="space5"></div>
                                <div class="err_request"></div>
                            </div>
                            <div class="clear"></div>    
                    </div>
                    <div class="grid_row test_cases noradiusbottom">
                        <div class="register">
                            <input type="submit" id="join-community" value="Register" name="role_submit"/>
                        </div>
                        <div class="cancel"><a href="#" id="close-popup-community2">Cancel</a></div>
                        <div class="clear"></div>
                    </div>
                </form>    
                    
            
        <div id="close-popup-community" class="close_btn"></div>
        </div>
    
        </div> <!--end community_registration-->
    </div>    
    <?php get_footer( 'buddypress' ); ?>

