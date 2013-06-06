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
	
<!-- styles needed by jScrollPane -->
<link type="text/css" href="style/jquery.jscrollpane.css" rel="stylesheet" media="all" />

<!-- latest jQuery direct from google's CDN -->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js">
</script>

<!-- the mousewheel plugin - optional to provide mousewheel support -->
<script type="text/javascript" src="js/jquery.mousewheel.js"></script>

<!-- the jScrollPane script -->
<script type="text/javascript" src="js/jquery.jscrollpane.min.js"></script>	

<script type="text/javascript" src="js/mwheelIntent.js"></script>	
<script type="text/javascript">
jQuery(document).ready(function($) {
	jQuery('#terms_co').click(function(){
		alert('test');	
	});	
});
</script>

</body>
</html>
