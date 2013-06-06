		<div id="footer-wrapper">
			<div class="footer">
				<div id="footer-company-info">
					<h5>Company Information</h5>
					<?php
					wp_nav_menu( array(
							'theme_location' => 'footer-menu',
							'container' =>false,
							'echo' => true,
							'depth' => 0,
							'fallback_cb'=>'footermenu',
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
							'fallback_cb'=>'footermenu',
							'walker' => new footer_walker()
						)
					);
					?>
				</div>
				<div id="footer-twitters">
					<h5>Follow us on Twitter</h5>
                    <!--<a href="https://twitter.com/<?php echo of_get_option('twitter_username')?>" class="twitter-follow-button" data-show-count="false">Follow @twitter</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>                       
                    <ul id="twitter_update_list"></ul>                    
                    <script src="http://twitter.com/javascripts/blogger.js" type="text/javascript"></script>
                    <script src="https://api.twitter.com/1/statuses/user_timeline/<?php echo of_get_option('twitter_username')?>.json?callback=twitterCallback2&count=3" type="text/javascript"></script>-->
					<div class="clear"></div>
				</div>
                <div class="clear"></div>
                <p class="copyright"><?php echo of_get_option('copyright'); ?></p>                
				
				<div class="space25"></div>
			</div>
		</div>
        <!-- **************** FOOTER *************** -->
	</div>
	<?php 
        //Show Register And Login Popup
        do_action('cp_login_register_box') 
    ?>
	<?php wp_footer(); ?>
	
<!-- Tiny scrollbar-->	
<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/jquery.tinyscrollbar.min.js"></script>	
	
<script type="text/javascript">
jQuery(document).ready(function() {
	
	jQuery('#terms_co').click(function(){
		jQuery('#registration').css('display', 'none');
		jQuery('#scrollbar1').css('display', 'block');
		setTimeout(function(){
			jQuery('#scrollbar1').tinyscrollbar();
		},200);
	});	
	
	/*Accept*/
	jQuery('#accept_terms').click(function(){
		jQuery('#scrollbar1').css('display', 'none');
		jQuery('#acc_tc_id').attr('checked','checked');
		jQuery('#registration').css('display', 'block');
	});	
	
	/*Reject*/
	jQuery('#reject_terms').click(function(){
		jQuery('#scrollbar1').css('display', 'none');
		jQuery('#acc_tc_id').removeAttr('checked','checked');
		jQuery('#registration').css('display', 'block');
	});
	
	jQuery('#close_terms').click(function(){
		jQuery('#scrollbar1').css('display', 'none');
		jQuery('#registration').css('display', 'block');
	});
	
	
	
});
</script>

</body>
</html>
