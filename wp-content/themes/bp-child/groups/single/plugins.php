<?php get_header( 'buddypress' ); ?>
    <div class="space25"></div>
	<div id="content" class="content container">
		<div class="padder">
			<?php if ( bp_has_groups() ) : while ( bp_groups() ) : bp_the_group(); ?>

			<?php do_action( 'bp_before_group_plugin_template' ); ?>

			<div id="item-header">
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

				        <?php do_action( 'bp_before_group_body' ); ?>

				        <?php 
                            do_action( 'bp_template_content' ); 
                        ?>

				        <?php do_action( 'bp_after_group_body' ); ?>
			        </div><!-- #item-body -->

			        <?php do_action( 'bp_after_group_plugin_template' ); ?>
                </div>
            </div>     
			<?php endwhile; endif; ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php //get_sidebar( 'buddypress' ); ?>

<?php get_footer( 'buddypress' ); ?>