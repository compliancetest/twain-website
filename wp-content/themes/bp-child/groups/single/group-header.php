<?php

do_action( 'bp_before_group_header' );

?>
<div id="issuer_title_block">
    <div class="column four_fifths left">
        <div id="item-header-avatar">
	        <a href="<?php bp_group_permalink(); ?>" title="<?php bp_group_name(); ?>">

		        <?php bp_group_avatar(); ?>

	        </a>
        </div><!-- #item-header-avatar -->

        <div id="item-header-content">
	        <h3 class="dark_gray_txt"><?php bp_group_name(); ?></h3>	        
	        <?php do_action( 'bp_before_group_header_meta' ); ?>
            <?php bp_group_description(); ?>	        
        </div><!-- #item-header-content -->
    </div>
    <div class="column fifth right">
        <div id="item-buttons">

            <?php do_action( 'bp_group_header_actions' ); ?>

        </div><!-- #item-buttons -->
    </div>
    <div class="clear"></div>
</div>

<?php
do_action( 'bp_after_group_header' );
do_action( 'template_notices' );
?>