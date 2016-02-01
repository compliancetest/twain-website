		        </div><!-- End Container -->
            <?php if(!is_home()): ?>
                <div class="space45"></div>
            <?php endif; ?>
            <div class="clear"></div>
            </div><!-- End Content-Wrapper -->
        </div><!-- End Content-Pattern -->

        <div id="footer-wrapper">
			<div class="footer">
                 <div class="left">
				    <div id="footer-company-info">
					    <h5>Company Information</h5>
					    <?php
					    wp_nav_menu( array(
							    'theme_location' => 'footer-menu',
							    'container' =>false,
							    'echo' => true,
							    'depth' => 0,
							    'walker' => new footer_walker()
						    )
					    );
					    ?>
				    </div>
				    <div id="footer-site-info">
					    <h5>Site Information</h5>
					    <?php
					    wp_nav_menu( array(
							    'theme_location' => 'footer-menu2',
							    'container' =>false,
							    'echo' => true,
							    'depth' => 0,
							    'walker' => new footer_walker()
						    )
					    );
					    ?>
				    </div>
                    <div class="clear"></div>
                    <div class="space10"></div>
                </div>
				<div id="footer-copyright">
                    <p class="copyright"><?php echo of_get_option('copyright'); ?></p>
                    <a href="https://twitter.com/ComplianceTest2" target="_blank" class="twitter-logo"></a>
                    <div class="clear"></div>
                    <img src="<?php echo CHILD_TEMPLATE_DIRECTORY ?>/images/logo.png" alt="ComplianceTest Logo" />
				</div>
                <div class="clear"></div>
                 <div class="space25"></div>
			</div>
		</div>
        <!-- **************** FOOTER *************** -->
	</div><!-- End Wrapper -->
	<?php 
        //Show Register And Login Popup
        do_action('cp_login_register_box') 
    ?>
	<?php wp_footer(); ?>
    <div id="require-registration" style="display: none; width: 350px" class="popup-box">        
        <div class="popup-box-header radius6 noradiusbottom">Notice</div>
        <div class="popup-box-content">
            <p>
                You must be a registered user to perform this action.
            </p>
        </div>
        <div class="popup-box-footer radius6 noradiustop">                        
            <a href="#" class="action-btn cancel-btn" onclick="jQuery('#under-construction .close_btn').click()"><span class="p"></span><span class="t">Close</span></a>
            <a href="#registration-popup" rel="custom-popup" cp-type="inline" class="action-btn continue-btn"><span class="p"></span><span class="t">Continue</span></a>
            <div class="clear"></div>
        </div>
        <a class="close_btn"></a>                
    </div> 
    <div id="under-construction" style="display: none; width: 350px" class="popup-box">        
        <div class="popup-box-header radius6 noradiusbottom">Notice!</div>
        <div class="popup-box-content">
            <p>
                This feature is under development.
            </p>
        </div>
        <div class="popup-box-footer radius6 noradiustop">                        
            <a href="#registration-popup" data-type="inline" class="action-btn cancel-btn" onclick="jQuery('#under-construction .close_btn').click()"><span class="p"></span><span class="t">Close</span></a>
            <div class="clear"></div>
        </div>
        <a class="close_btn"></a>                
    </div> 

</body>
</html>