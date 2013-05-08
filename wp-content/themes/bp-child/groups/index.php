<?php

/**
 * BuddyPress - Groups Directory
 *
 * @package BuddyPress
 * @subpackage bp-default
 */

get_header( 'buddypress' ); ?>

<?php do_action( 'bp_before_directory_groups_page' ); ?>
<div class="space25"></div>
 <div class="content container">
  <div class="column">
   <div class="content_inner">

		<?php do_action( 'bp_before_directory_groups' ); ?>

		<form action="" method="post" id="groups-directory-form" class="dir-form">

			<h3><?php _e( 'Groups Directory', 'buddypress' ); ?></h3><?php if ( is_super_admin() && bp_user_can_create_groups() ) : ?> &nbsp;
			<a class="button button_small normal green_bcg white_txt radius3 left" href="<?php echo trailingslashit( bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/create' ); ?>">
			<span class="sign"><img src="<?php echo bloginfo('stylesheet_directory'); ?>/images/add_new_sign.png"></span>
			<?php _e( 'Create a Group', 'buddypress' ); ?></a>
			<?php endif; ?>
			<div class="clear"></div>
			<div class="space5"></div>
			

			<?php do_action( 'bp_before_directory_groups_content' ); ?>
			
			<div id="group-dir-search" class="dir-search width50P" role="search">

				<?php bp_directory_groups_search_form(); ?>

			</div><!-- #group-dir-search -->
			<div class="clear"></div>
			<div class="space15"></div>
			
			<?php do_action( 'template_notices' ); ?>

			<div class="item-list-tabs" role="navigation">
				<ul>
					<li class="selected" id="groups-all"><a href="<?php echo trailingslashit( bp_get_root_domain() . '/' . bp_get_groups_root_slug() ); ?>"><?php printf( __( 'All Groups <span>%s</span>', 'buddypress' ), bp_get_total_group_count() ); ?></a></li>

					<?php if ( is_user_logged_in() && bp_get_total_group_count_for_user( bp_loggedin_user_id() ) ) : ?>

						<li id="groups-personal"><a href="<?php echo trailingslashit( bp_loggedin_user_domain() . bp_get_groups_slug() . '/my-groups' ); ?>"><?php printf( __( 'My Groups <span>%s</span>', 'buddypress' ), bp_get_total_group_count_for_user( bp_loggedin_user_id() ) ); ?></a></li>

					<?php endif; ?>

					<?php do_action( 'bp_groups_directory_group_filter' ); ?>

				</ul>
			</div><!-- .item-list-tabs -->
			<div class="clear"></div>
			<div class="space15"></div>
			<div class="item-list-tabs" id="subnav" role="navigation">
				<ul>

					<?php do_action( 'bp_groups_directory_group_types' ); ?>

					<li id="groups-order-select" class="last filter">

						<label for="groups-order-by"><?php _e( 'Order By:', 'buddypress' ); ?></label>
						<div class="clear"></div>
						<div class="styled_select_dashboard left">
							<select id="groups-order-by">
								<option value="active"><?php _e( 'Last Active', 'buddypress' ); ?></option>
								<option value="popular"><?php _e( 'Most Members', 'buddypress' ); ?></option>
								<option value="newest"><?php _e( 'Newly Created', 'buddypress' ); ?></option>
								<option value="alphabetical"><?php _e( 'Alphabetical', 'buddypress' ); ?></option>
								<div class="clear"></div>
								<?php do_action( 'bp_groups_directory_order_options' ); ?>

							</select>
						</div>
						<div class="clear"></div>
					</li>
				</ul>
			</div>

			<div id="groups-dir-list" class="groups dir-list">

				<?php locate_template( array( 'groups/groups-loop.php' ), true ); ?>

			</div><!-- #groups-dir-list -->

			<?php do_action( 'bp_directory_groups_content' ); ?>

			<?php wp_nonce_field( 'directory_groups', '_wpnonce-groups-filter' ); ?>

			<?php do_action( 'bp_after_directory_groups_content' ); ?>

		</form><!-- #groups-directory-form -->

		<?php do_action( 'bp_after_directory_groups' ); ?>
			</div>
			<div class="clear"></div>
			
		</div><!-- .column -->
	</div><!-- #content -->
	<div class="space45"></div>

	<?php do_action( 'bp_after_directory_groups_page' ); ?>

<?php get_sidebar( 'buddypress' ); ?>
<?php get_footer( 'buddypress' ); ?>

