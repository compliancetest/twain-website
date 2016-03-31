<?php

/**
 * BuddyPress - Users Home
 *
 * @package BuddyPress
 * @subpackage bp-default
 */
if(!can_view_profile(bp_displayed_user_id()))
{
    addMessage('You are not allowed to view the profile', 'error');
    wp_redirect('/');
    exit;
}
get_header( 'buddypress' ); ?>

	<div id="content" class="content container">
        <div class="content_inner">
		
        <div class="padder">

			<?php do_action( 'bp_before_member_home_content' ); ?>

			<div id="item-header" role="complementary">

				<?php locate_template( array( 'members/single/member-header.php' ), true ); ?>

			</div><!-- #item-header -->
            <div class="column">
<!--                <div class="tabs_wrap light_gray_bcg radius6">-->
<!--                    --><?php
//                        $currentTab = '';
//                        if(bp_is_user_profile()){
//                            $currentTab = 'profile';
//                        }else if(bp_is_user_messages()){
//                            $currentTab = 'message';
//                        }
//                    ?>
<!--			        <div id="item-body">                    -->
<!--				        --><?php //do_action( 'bp_before_member_body' );
//
//				        if ( bp_is_user_activity() || !bp_current_component() ) :
//					        locate_template( array( 'members/single/activity.php'  ), true );
//
//				         elseif ( bp_is_user_blogs() ) :
//					        locate_template( array( 'members/single/blogs.php'     ), true );
//
//				        elseif ( bp_is_user_friends() ) :
//					        locate_template( array( 'members/single/friends.php'   ), true );
//
//				        elseif ( bp_is_user_groups() ) :
//					        locate_template( array( 'members/single/groups.php'    ), true );
//
//				        elseif ( bp_is_user_messages() ) :
//					        locate_template( array( 'members/single/messages.php'  ), true );
//
//				        elseif ( bp_is_user_profile() ) :
//					        locate_template( array( 'members/single/profile.php'   ), true );
//
//				        elseif ( bp_is_user_forums() ) :
//					        locate_template( array( 'members/single/forums.php'    ), true );
//
//				        elseif ( bp_is_user_settings() ) :
//					        locate_template( array( 'members/single/settings.php'  ), true );
//
//				        // If nothing sticks, load a generic template
//				        else :
//					        locate_template( array( 'members/single/plugins.php'   ), true );
//
//				        endif;
//
//				        do_action( 'bp_after_member_body' ); ?>
<!---->
<!--			        </div><!-- #item-body -->
<!---->
<!--			        --><?php //do_action( 'bp_after_member_home_content' ); ?>
<!--                </div>-->
            </div>
            <div class="clear"></div>
		</div><!-- .padder -->
        
        </div>
	</div><!-- #content -->

<?php // get_sidebar( 'buddypress' ); ?>
<?php get_footer( 'buddypress' ); ?>
