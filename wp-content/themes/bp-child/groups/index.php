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

	<div id="content">
		<div class="padder">

		<?php do_action( 'bp_before_directory_groups' ); ?>

		<form action="" method="post" id="groups-directory-form" class="dir-form">
            <div class="page-title-block column">
			    <h2 class="nomarginbottom left"><?php _e( 'Groups Directory', 'buddypress' ); ?></h2>
                <?php if ( is_user_logged_in() && bp_user_can_create_groups() ) : ?> <a class="action-btn add-new-btn left15" href="<?php echo trailingslashit( bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/create' ); ?>"><?php _e( 'Create a Group', 'buddypress' ); ?></a><?php endif; ?>
                <div id="group-dir-search" class="dir-search right" role="search">
                    <?php bp_directory_groups_search_form(); ?>
                </div><!-- #group-dir-search -->
                <div class="clear"></div>
            </div>
			<?php do_action( 'bp_before_directory_groups_content' ); ?>
            <div class="column">
            <div class="tabs_wrap radius6 light_gray_bcg system-section">
			    <?php do_action( 'template_notices' ); ?>
                
			    <div class="item-list-tabs left" role="navigation">
				    <ul>
					    <li class="selected" id="groups-all"><a href="<?php echo trailingslashit( bp_get_root_domain() . '/' . bp_get_groups_root_slug() ); ?>"><?php printf( __( 'All Groups <span>%s</span>', 'buddypress' ), bp_get_total_group_count() ); ?></a><div class="loading"></div></li>

					    <?php if ( is_user_logged_in() && bp_get_total_group_count_for_user( bp_loggedin_user_id() ) ) : ?>

						    <li id="groups-personal"><a href="<?php echo trailingslashit( bp_loggedin_user_domain() . bp_get_groups_slug() . '/my-groups' ); ?>"><?php printf( __( 'My Groups <span>%s</span>', 'buddypress' ), bp_get_total_group_count_for_user( bp_loggedin_user_id() ) ); ?></a><div class="loading"></div></li>

					    <?php endif; ?>

					    <?php do_action( 'bp_groups_directory_group_filter' ); ?>

				    </ul>
			    </div><!-- .item-list-tabs -->

			    <div class="item-order-tabs right" id="subnav" role="navigation">
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
                <div class="clear"></div>
			    <div id="groups-dir-list" class="groups dir-list tab-content white_bcg padding10">

				    <?php locate_template( array( 'groups/groups-loop.php' ), true ); ?>

			    </div><!-- #groups-dir-list -->

			    <?php do_action( 'bp_directory_groups_content' ); ?>

			    <?php wp_nonce_field( 'directory_groups', '_wpnonce-groups-filter' ); ?>

			    <?php do_action( 'bp_after_directory_groups_content' ); ?>
            </div>
            </div>
		</form><!-- #groups-directory-form -->

		<?php do_action( 'bp_after_directory_groups' ); ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php do_action( 'bp_after_directory_groups_page' ); ?>
</div>
<?php get_footer( 'buddypress' ); ?>

