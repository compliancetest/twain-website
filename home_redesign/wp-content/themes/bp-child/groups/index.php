<?php

/**
 * BuddyPress - Groups Directory
 *
 * @package BuddyPress
 * @subpackage bp-default
 */

get_header( 'buddypress' ); 

?>
<div class="content container">
	<?php do_action( 'bp_before_directory_groups_page' ); ?>
    <div id="search_title_block" class="page-title-block column noshadow">
        <?php cp_directory_groups_search_form(); ?>
        <p class="search_result_label">
            <?php if ( bp_has_groups( bp_ajax_querystring( 'groups' ) ) ) : ?>
                <?php
                    bp_groups_pagination_count();

                    if ( isset( $_REQUEST['group-filter-box'] ) && !empty( $_REQUEST['group-filter-box'] ) )
                        $term = $_REQUEST['group-filter-box'];
                    elseif ( isset( $_REQUEST['s'] ) && !empty( $_REQUEST['s'] ) )
                        $term = $_REQUEST['s'];
                    else
                        $term = false;
                    if($term)
                        echo " for \"<b>$term</b>\"";

                ?>
            <?php else: ?>
                No result found!
            <?php endif; ?>
        </p>
        <?php if (!empty( $_REQUEST['s'])):  ?>
            <a href="<?php echo get_permalink()?>" class="action-btn cancel-btn top10" id="clear-search-filter-btn"><span class="p"></span><span class="t">Clear All</span></a>
        <?php endif; ?>
        <?php if($term) {?>
            <?php if ( is_user_logged_in() && bp_user_can_create_groups() ) : ?> <a class="action-btn add-new-btn left15 right top10" href="<?php echo trailingslashit( bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/create' ); ?>"><span class="p"></span><span class="t"><?php _e( 'Create a Group', 'buddypress' ); ?></span></a><?php endif; ?>
        <?php } ?>
        <div class="clear"></div>        
    </div> <!-- end search_title_block -->
    
    
	<div id="search_result_block" class="search_result_block">

		<?php do_action( 'bp_before_directory_groups' ); ?>

			<?php do_action( 'bp_before_directory_groups_content' ); ?>
            <div class="column">
			    
			    <!--<div class="item-order-tabs right" id="subnav" role="navigation">
				    <ul>

					    <?php do_action( 'bp_groups_directory_group_types' ); ?>

					    <li id="groups-order-select" class="last filter">

						    <label for="groups-order-by"><?php _e( 'Order By:', 'buddypress' ); ?></label>
						    <select id="groups-order-by">
							    <option value="active"><?php _e( 'Last Active', 'buddypress' ); ?></option>
							    <option value="popular"><?php _e( 'Most Members', 'buddypress' ); ?></option>
							    <option value="newest"><?php _e( 'Newly Created', 'buddypress' ); ?></option>
							    <option value="alphabetical"><?php _e( 'Alphabetical', 'buddypress' ); ?></option>

							    <?php do_action( 'bp_groups_directory_order_options' ); ?>

						    </select>
					    </li>
				    </ul>
			    </div>
                <div class="clear"></div>-->
			    <?php do_action( 'bp_before_groups_loop' ); ?>

                <?php if ( bp_has_groups( bp_ajax_querystring( 'groups' ) ) ) : ?>

                    <?php do_action( 'bp_before_directory_groups_list' ); ?>
                    <div class="grid dark_gray_txt" id="groups-dir-list">
                        <div class="grid_head grid_head_border">                        
                            <div class="grid_cell nopaddingtop width50P">Community Name</div>
                            <div class="grid_cell nopaddingtop width15P tocenter">Test Suites</div>
                            <div class="grid_cell nopaddingtop width20P tocenter">Members</div>
                            <div class="grid_cell nopaddingtop width15P tocenter">Compliant Products</div>                        
                            <div class="clear"></div>                        
                        </div>
                        <div class="grid_body" id="groups-list">
                            <?php while ( bp_groups() ) : bp_the_group(); ?>
                                <div class="grid_row grid_row_border">
                                    <div class="grid_cell nopaddingtop width50P">
                                        <div class="item-avatar width15P left">
                                            <a href="<?php bp_group_permalink(); ?>"><?php bp_group_avatar( 'type=thumb&width=50&height=50' ); ?></a>
                                        </div>    
                                        <div class="width85P left">
                                            <h5><a href="<?php bp_group_permalink(); ?>"><?php bp_group_name(); ?></a></h5>
                                            <p><?php bp_group_description_excerpt(); ?></p>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                    <div class="grid_cell width50P">
                                        <div class="grid_cell nopaddingtop width30P tocenter">
                                            <?php 
                                                $suites = getCommunityTestSuites(bp_get_group_id());                                             
                                                echo count($suites);
                                            ?>
                                        </div>
                                        <div class="grid_cell nopaddingtop width40P tocenter"><?php bp_group_total_members(); ?></div>
                                        <div class="grid_cell nopaddingtop width30P tocenter"><?php echo getCommunityProductsCount(bp_get_group_id()) ?></div>                        
                                        <div class="clear space5"></div>
                                        <div class="action">
                                        <?php 
                                            if(!is_user_logged_in())
                                            {                       
                                        ?>
                                        <div class="generic-button group-button private"><a href="#registration-popup" title="Join Community" class="group-button register popup button button_medium button_red white_txt radius6" rel="custom-popup" cp-type="inline">Join Community</a></div>
                                        <?php
                                            }else{
                                                do_action( 'bp_directory_groups_actions' );     
                                            }
                                        ?>

                                        <?php //do_action( 'bp_directory_groups_actions' ); ?>
                                        </div>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <?php
                                    if(!groups_is_user_member(get_current_user_id(), bp_get_group_id())):
                                ?>
                                <!-- Jion Community, Terms and Conditions, License Agreement Table -->                                
                                <div id="community-registration<?php bp_group_id()?>" style="display: none; width: 305px;" class="popup-box community-registration-box">                
                                    <div class="popup-box-header radius6 noradiusbottom">Community Registration</div>
                                    <div class="popup-box-content">
                                        <form method="post" action="<?php echo wp_nonce_url( bp_get_group_permalink( ) . 'request-membership', 'groups_request_membership' )?>" id="join-community-form" data-group-id="<?php bp_group_id()?>">
                                            <div class="grey-border-bottom">
                                                <p>You need to join the community of interest in order to view Test Cases and Participate in the Forum</p>
                                            </div>
                                            <div class="top10">
                                                <input type="checkbox" name="agree_terms" value="agree" id="agree_community_terms"> I agree with <a href="#community-terms-box<?php bp_group_id()?>" rel="custom-popup" class="normal" id="show-community-terms">Terms & Conditions</a>
                                                <div class="clear"></div>
                                                <div class="space5"></div><!--
                                                <input type="checkbox" name="agree_license" value="agree_license" id="agree_community_license"> I agree with <a href="#community-license-box<?php bp_group_id()?>" rel="custom-popup" id="show-community-license" class="normal">License Agreement</a>
                                                <div class="clear"></div>
                                                <div class="space5"></div>-->
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
                                <div id="community-terms-box<?php bp_group_id()?>" style="display: none;" class="popup-box redactor_editor community-terms-box">
                                    <div class="popup-box-header radius6 noradiusbottom">Terms and Conditions</div>
                                    <div class="popup-box-content">
                                        <p>
                                        <?php 
                                            $terms = groups_get_groupmeta(bp_get_group_id(), 'terms_and_conditions');
                                            echo $terms;
                                        ?>
                                        </p>
                                    </div>
                                    <div class="popup-box-footer radius6 noradiustop">                        
                                        <a href="#" class="action-btn cancel-btn" data-id="<?php bp_group_id()?>"><span class="p"></span><span class="t">CANCEL</span></a>
                                        <a href="#" class="action-btn process-btn" data-id="<?php bp_group_id()?>"><span class="p"></span><span class="t">AGREE</span></a>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                                <div class="popup-box redactor_editor community-license-box"  id="community-license-box<?php bp_group_id()?>" style="display: none;">
                                    <div class="popup-box-header radius6 noradiusbottom">License Agreements</div>
                                    <div class="popup-box-content">
                                        <p>
                                        <?php 
                                            $license = groups_get_groupmeta(bp_get_group_id(), 'license_agreements');
                                            echo $license;
                                        ?>
                                        </p>
                                    </div>
                                    <div class="popup-box-footer radius6 noradiustop">                        
                                        <a href="#" class="action-btn cancel-btn" data-id="<?php bp_group_id()?>"><span class="p"></span><span class="t">Cancel</span></a>
                                        <a href="#" class="action-btn process-btn" data-id="<?php bp_group_id()?>"><span class="p"></span><span class="t">Agree</span></a>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            <?php endwhile; ?>
                        </div>
                    </div>
                
                    <?php do_action( 'bp_after_directory_groups_list' ); ?>
                    <?php
                        global $groups_template;
                        if($groups_template->pag_links)                        
                        {
                    ?>
                    <div class="space30"></div>
                    <div class="pagination-wrapper">
                        <div class="pagination">
                              <?php bp_groups_pagination_links(); ?>
                        </div>
                    </div>
                    <?php
                        }
                    ?>
                    <div class="space10"></div>
                <?php endif; ?>

                <?php do_action( 'bp_after_groups_loop' ); ?>



			    <?php do_action( 'bp_directory_groups_content' ); ?>

			    <?php wp_nonce_field( 'directory_groups', '_wpnonce-groups-filter' ); ?>

			    <?php do_action( 'bp_after_directory_groups_content' ); ?>
            </div>

		<?php do_action( 'bp_after_directory_groups' ); ?>

	</div><!-- #content -->

	<?php do_action( 'bp_after_directory_groups_page' ); ?>
</div>
<?php get_footer( 'buddypress' ); ?>

