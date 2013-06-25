		        </div><!-- End Container -->
            <div class="space45"></div>
            <div class="clear"></div>
            </div><!-- End Content-Wrapper -->
        </div><!-- End Content-Pattern -->

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
                    <!--<a href="https://twitter.com/<?php echo of_get_option('twitter_username')?>" class="twitter-follow-button" data-show-count="false">Follow @<?php echo of_get_option('twitter_username')?></a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>                       
                    <ul id="twitter_update_list"></ul>                    
                    <script src="http://twitter.com/javascripts/blogger.js" type="text/javascript"></script>
                    <script src="https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=<?php echo of_get_option('twitter_username')?>&count=3" type="text/javascript"></script>-->
                    <!--<a class="twitter-timeline" href="https://twitter.com/<?php echo of_get_option('twitter_username')?>" data-widget-id="348686715474558976" height="150">Tweets by @<?php echo of_get_option('twitter_username')?></a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>-->

					<div class="clear"></div>
				</div>
                <div class="clear"></div>
                <p class="copyright"><?php echo of_get_option('copyright'); ?></p>                
				
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
	
<!-- Tiny scrollbar-->	
<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/jquery.tinyscrollbar.min.js"></script>	
<!--IF[ie]><script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/PIE.js"></script><![endif]-->

</body>
</html>
