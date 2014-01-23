<?php get_header( 'buddypress' ); ?>

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
                            <div id="testdata-container" class="tab-content column white_bcg" style="display: none;">
                                <?php if(!is_user_logged_in() || !bp_group_is_member()){ ?>
                                <p>You need to join the community to participate in the test data section.</p>
                                <?php } ?>                            
                            </div>
                            
                            <div id="wiki-container" class="" style="display: none;">
                                <?php include( bp_docs_locate_template( 'docs-loop.php' ) ) ?>
                            </div>
                            <div id="forum-container" class="tab-content column white_bcg" style="display: none;">
                                <?php if(!is_user_logged_in() || !bp_group_is_member()){ ?>
                                <p>You need to join the community to participate in the forum section.</p>
                                <?php } ?>                            
                            </div>
                            <div id="downloads-container" class="tab-content column white_bcg" style="display: none;">
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
            <?php
            if(is_user_logged_in() && !bp_group_is_member()){
                 global $groups_template;
                    
            ?>
            <div id="community-registration<?php bp_group_id()?>" style="display: none;" class="community-registration-box popup-box" data-id="<?php bp_group_id()?>">                
                <div class="popup-box-header radius6 noradiusbottom">Community Registration</div>
                <div class="popup-box-content">
                    <form method="post" action="<?php echo wp_nonce_url( bp_get_group_permalink( ) . 'request-membership', 'groups_request_membership' )?>" id="join-community-form" data-group-id="<?php echo $groups_template->group->id?>">
                        <div class="grey-border-bottom">
                            <p>You need to join the community of interest in order to view Test Cases and Participate in the Forum</p>
                        </div>
                        <div class="top10">
                            <input type="checkbox" name="agree_terms" value="agree" id="agree_community_terms"> I agree with <a href="#community-terms-box<?php bp_group_id()?>" rel="custom-popup" class="normal" id="show-community-terms">Terms & Conditions</a>
                            <div class="clear"></div>
                            <div class="space5"></div>
                            <input type="checkbox" name="agree_license" value="agree_license" id="agree_community_license"> I agree with <a href="#community-license-box<?php bp_group_id()?>" rel="custom-popup" id="show-community-license" class="normal">License Agreement</a>
                            <div class="clear"></div>
                            <div class="space5"></div>
                            <div class="err_request"></div>
                        </div>
                        <div class="clear"></div>                            
                    </form>    
                </div>
                <div class="popup-box-footer radius6 noradiustop">                                                        
                    <a href="javascript: void(0)" class="action-btn process-btn" data-id="<?php bp_group_id()?>"><span class="p"></span><span class="t">REGISTER</span></a>
                    <a href="javascript: void(0)" class="action-btn cancel-btn close-popup-btn" data-id="<?php bp_group_id()?>"><span class="p"></span><span class="t">CANCEL</span></a>                    
                    <div class="clear"></div>
                    <div class="message" style="display: none;">Please aggree the Terms & Conditions and License Agreement.</div>
                </div>
                <div class="loading loading-with-text radius6"><div><b>SENDING REQUEST</b><p>Please wait...</p></div></div>
                <a id="close-popup-community" class="close_btn"></a>                
            </div>
            <div id="community-terms-box<?php bp_group_id()?>" style="display: none" class="community-terms-box popup-box redactor_editor">
                <div class="popup-box-header radius6 noradiusbottom">Terms and Conditions</div>
                <div class="popup-box-content">
                    <p>
                    <?php 
                        $terms = groups_get_groupmeta($groups_template->group->id, 'terms_and_conditions');
                        echo $terms;
                    ?>
                    </p>
                </div>
                <div class="popup-box-footer radius6 noradiustop">                                            
                    <a href="#" class="action-btn process-btn" data-id="<?php bp_group_id()?>"><span class="p"></span><span class="t">AGREE</span></a>
                    <a href="#" class="action-btn cancel-btn" data-id="<?php bp_group_id()?>"><span class="p"></span><span class="t">CANCEL</span></a>
                    <div class="clear"></div>
                </div>
            </div>
            <div id="community-license-box<?php bp_group_id()?>" class="popup-box redactor_editor community-license-box" style="display: none;">
                <div class="popup-box-header radius6 noradiusbottom">License Agreements</div>
                <div class="popup-box-content">
                    <p>
                    <?php 
                        $license = groups_get_groupmeta($groups_template->group->id, 'license_agreements');
                        echo $license;
                    ?>
                    </p>
                </div>
                <div class="popup-box-footer radius6 noradiustop">                        
                    <a href="#" class="action-btn process-btn" data-id="<?php bp_group_id()?>"><span class="p"></span><span class="t">AGREE</span></a>
                    <a href="#" class="action-btn cancel-btn" data-id="<?php bp_group_id()?>"><span class="p"></span><span class="t">CANCEL</span></a>                    
                    <div class="clear"></div>
                </div>
            </div>
            <?php } ?>
			<?php endwhile; endif; ?>

		</div><!-- .padder -->
	</div><!-- #content -->          
      
    <?php get_footer( 'buddypress' ); ?>

