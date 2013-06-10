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

    <?php get_footer( 'buddypress' ); ?>
