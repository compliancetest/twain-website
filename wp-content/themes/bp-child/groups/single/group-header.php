<?php

do_action( 'bp_before_group_header' );

?>
<div id="issuer_title_block" class="page-title-block">
    <div class="column four_fifths left">
        <div id="item-header-avatar" class="page-title-avatar">
	        <a href="<?php bp_group_permalink(); ?>" title="<?php bp_group_name(); ?>">

		        <?php bp_group_avatar(); ?>

	        </a>
        </div><!-- #item-header-avatar -->

        <div id="item-header-content" class="page-title-content redactor_editor">
	        <h3 class="dark_gray_txt"><?php bp_group_name(); ?></h3>	        
	        <?php do_action( 'bp_before_group_header_meta' ); ?>
            <?php bp_group_description(); ?>	        
        </div><!-- #item-header-content -->
    </div>
    <div class="fifth right">
        <div id="item-buttons" class="page-title-buttons">
            <?php 
                if(!is_user_logged_in())
                {                       
            ?>
            <div class="generic-button group-button private"><a href="#registration-popup" title="Join Community" class="group-button register popup button button_medium button_red white_txt radius6" rel="custom-popup" cp-type="inline">Join Community</a></div>
            <?php
                }else{
                    do_action( 'bp_group_header_actions' );
                }
            ?>

        </div><!-- #item-buttons -->
    </div>
    <div class="clear"></div>
</div>
<div class="popup-box" id="delete-community-box" style="display: none; width: 500px">
    <div class="popup-box-header radius6 noradiusbottom">Confirm Leaving Community</div>
    <div class="popup-box-content">
        Are you sure you want to leave the <?php bp_group_name();?> community?
    </div>
    <div class="popup-box-footer radius6 noradiustop">
        <a href="#" class="action-btn process-btn"><span class="p"></span><span class="t">Confirm</span></a>
        <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>
        <div class="clear"></div>
    </div>
    <a class="close_btn"></a>
</div>
<?php
do_action( 'bp_after_group_header' );
//do_action( 'template_notices' );
?>