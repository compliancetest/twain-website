<?php
/*
 * Template Name: My Claims
 */
get_header();
?>

<div class="content" id="my_profile">
	<div class="space25"></div>
	<div class="column fifth left nopaddingleft nopaddingright sidebar">
		<?php get_sidebar('dashboard'); ?>
	</div>
		
	<div class="column four_fifths right container">
        <div class="default_grid">
		    <div class="grid_cell nopaddingleft">
                <a href="<?php echo esc_url( get_permalink( get_page_by_title( 'Create / Edit Product or Service' ) ) ); ?>" class="button button_small normal green_bcg white_txt right radius6"><span class="sign">+</span> Add new Product or Service</a>
		    </div>
	    </div>
        <div class="clear space25"></div>

        <div class="grid default_grid">
            <div class="grid_head blue_head">
                <div class="grid_row">
                    <div class="grid_cell grid_button grid_button_left expandable"><a href="#"><span class="simple_tooltip radius6">Expand<span></span></span></a></div>
                    <div class="grid_cell width60P"><h5><span class="normal">Product:</span> MCS v1.1</h5></div>
                    <div class="grid_cell grid_button grid_button_right"><a href="#"><img src="<?php echo get_bloginfo('stylesheet_directory');?>/images/grid_button_pencil_icon_w.png" /><span class="simple_tooltip radius6">Edit Section<span></span></span></a></div>
                    <div class="clear"></div>
                </div>
            </div>
            <div class=" expandable_content">
            <div class="grid_body">
                <div class="grid_row red_head nopadding">
                    <div class="grid_cell width20P grid_cell_fitted">Issuer</div>
                    <div class="grid_cell width15P grid_cell_fitted">Suite</div>
                    <div class="grid_cell width15P grid_cell_fitted">Role</div>
                    <div class="grid_cell width15P grid_cell_fitted">Status</div>
                    <div class="grid_cell width15P grid_cell_fitted">Date</div>
                    <div class="grid_cell width10P grid_cell_fitted">Audit</div>
                    <div class="grid_cell width10P grid_cell_fitted">Action</div>
                    <div class="clear"></div>
                </div>
                <div class="grid_row nopadding">
                    <div class="grid_cell width20P grid_cell_fitted">SBR</div>
                    <div class="grid_cell width15P grid_cell_fitted"><a href="#">MCS v1.0</a></div>
                    <div class="grid_cell width15P grid_cell_fitted">Emp AA</div>
                    <div class="grid_cell width15P grid_cell_fitted green_txt">Certified</div>
                    <div class="grid_cell width15P grid_cell_fitted">12 / 02 / 13</div>
                    <div class="grid_cell width10P grid_cell_fitted"><a href="#">Log</a></div>
                    <div class="grid_cell width10P grid_cell_fitted"><a href="#" class="grid_action radius3"><img src="<?php echo get_bloginfo('stylesheet_directory');?>/images/grid_button_pencil_icon_w.png" /></a></div>
                    <div class="clear"></div>
                </div>
                <div class="grid_row nopadding">
                    <div class="grid_cell width20P grid_cell_fitted">SBR</div>
                    <div class="grid_cell width15P grid_cell_fitted"><a href="#">MCS v1.0</a></div>
                    <div class="grid_cell width15P grid_cell_fitted">Emp AA</div>
                    <div class="grid_cell width15P grid_cell_fitted green_txt">Certified</div>
                    <div class="grid_cell width15P grid_cell_fitted">12 / 02 / 13</div>
                    <div class="grid_cell width10P grid_cell_fitted"><a href="#">Log</a></div>
                    <div class="grid_cell width10P grid_cell_fitted"><a href="#" class="grid_action radius3"><img src="<?php echo get_bloginfo('stylesheet_directory');?>/images/grid_button_pencil_icon_w.png" /></a></div>
                    <div class="clear"></div>
                </div>
                <div class="grid_row nopadding">
                    <div class="grid_cell width20P grid_cell_fitted">SBR</div>
                    <div class="grid_cell width15P grid_cell_fitted"><a href="#">MCS v1.0</a></div>
                    <div class="grid_cell width15P grid_cell_fitted">Emp AA</div>
                    <div class="grid_cell width15P grid_cell_fitted green_txt">Certified</div>
                    <div class="grid_cell width15P grid_cell_fitted">12 / 02 / 13</div>
                    <div class="grid_cell width10P grid_cell_fitted"><a href="#">Log</a></div>
                    <div class="grid_cell width10P grid_cell_fitted"><a href="#" class="grid_action radius3"><img src="<?php echo get_bloginfo('stylesheet_directory');?>/images/grid_button_pencil_icon_w.png" /></a></div>
                    <div class="clear"></div>
                </div>
                <div class="grid_row nopadding">
                    <div class="grid_cell width20P grid_cell_fitted">SBR</div>
                    <div class="grid_cell width15P grid_cell_fitted"><a href="#">MCS v1.0</a></div>
                    <div class="grid_cell width15P grid_cell_fitted">Emp AA</div>
                    <div class="grid_cell width15P grid_cell_fitted green_txt">Certified</div>
                    <div class="grid_cell width15P grid_cell_fitted">12 / 02 / 13</div>
                    <div class="grid_cell width10P grid_cell_fitted"><a href="#">Log</a></div>
                    <div class="grid_cell width10P grid_cell_fitted"><a href="#" class="grid_action radius3"><img src="<?php echo get_bloginfo('stylesheet_directory');?>/images/grid_button_pencil_icon_w.png" /></a></div>
                    <div class="clear"></div>
                </div>
                <div class="grid_row nopadding">
                    <div class="grid_cell width20P grid_cell_fitted">SBR</div>
                    <div class="grid_cell width15P grid_cell_fitted"><a href="#">MCS v1.0</a></div>
                    <div class="grid_cell width15P grid_cell_fitted">Emp AA</div>
                    <div class="grid_cell width15P grid_cell_fitted green_txt">Certified</div>
                    <div class="grid_cell width15P grid_cell_fitted">12 / 02 / 13</div>
                    <div class="grid_cell width10P grid_cell_fitted"><a href="#">Log</a></div>
                    <div class="grid_cell width10P grid_cell_fitted"><a href="#" class="grid_action radius3"><img src="<?php echo get_bloginfo('stylesheet_directory');?>/images/grid_button_pencil_icon_w.png" /></a></div>
                    <div class="clear"></div>
                </div>

            </div>

            <a href="#" class="button button_small normal green_bcg white_txt right radius6 noradiustop nomargintop"><span class="sign">+</span> New Compliance Claim</a>
            <div class="clear"></div>
            </div>

        </div>

    <div class="clear space25"></div>

    <div class="grid default_grid">
            <div class="grid_head blue_head">
                <div class="grid_row">
                    <div class="grid_cell grid_button grid_button_left expandable"><a href="#"><span class="simple_tooltip radius6">Expand<span></span></span></a></div>
                    <div class="grid_cell width60P"><h5><span class="normal">Product:</span> MCS v1.1</h5></div>
                    <div class="grid_cell grid_button grid_button_right"><a href="#"><img src="<?php echo get_bloginfo('stylesheet_directory');?>/images/grid_button_pencil_icon_w.png" /><span class="simple_tooltip radius6">Edit Section<span></span></span></a></div>
                    <div class="clear"></div>
                </div>
            </div>
            <div class="grid_body expandable_content">
                <div class="grid_row">
                    <div class="grid_cell width20P"><b>Name</b></div>
                    <div class="grid_cell width40P">Steve A. Capell</div>
                    <div class="clear"></div>
                </div>
                <div class="grid_row">
                    <div class="grid_cell width20P"><b>Email</b></div>
                    <div class="grid_cell width40P">steve.capell@acme.com</div>
                    <div class="clear"></div>
                </div>
                <div class="grid_row">
                    <div class="grid_cell width20P"><b>Password</b></div>
                    <div class="grid_cell width40P">*********</div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="clear space25"></div>
			
</div> <!--end content-->

<script type="text/javascript">
	var wrapper = $('<div/>').css({height:0,width:0,'overflow':'hidden'});
	var fileInput = jQuery(':file').wrap(wrapper);

	fileInput.change(function(){
		$this = $(this);
		jQuery('#file_ts').text("File attached");
	})
	 
	jQuery('#file_ts').click(function(){
		fileInput.click();
	}).show(); 
</script>
<?php
get_footer();
?>
