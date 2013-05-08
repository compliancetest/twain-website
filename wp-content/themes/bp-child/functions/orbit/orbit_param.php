<?php
wp_enqueue_script('Orbit', get_bloginfo('template_url') . '/functions/orbit/jquery.orbit-1.2.3.js', array('jquery'), '1.2.3', false);

$animation = get_option_tree('animation', ''); 
	if ($animation) { $animation_full = $animation ; } else {$animation_full = 'horizontal-push'; }
$animationSpeed = get_option_tree('animationspeed', ''); 
	if ($animationSpeed) { $animationSpeed_full = $animationSpeed ; } else {$animationSpeed_full = '600'; }
$timer = get_option_tree('timer', ''); 
	if ($timer) { $timer_full = $timer ; } else {$timer_full = 'true'; }
$advanceSpeed = get_option_tree('advancespeed', ''); 
	if ($advanceSpeed) { $advanceSpeed_full = $advanceSpeed ; } else {$advanceSpeed_full = '4000'; }
$pauseOnHover = get_option_tree('pauseonhover', ''); 
	if ($pauseOnHover) { $pauseOnHover_full = $pauseOnHover ; } else {$pauseOnHover_full = 'false'; }
$startClockOnMouseOut = get_option_tree('startclockonmouseout', ''); 
	if ($startClockOnMouseOut) { $startClockOnMouseOut_full = $startClockOnMouseOut ; } else {$startClockOnMouseOut_full = 'false'; }
$startClockOnMouseOutAfter = get_option_tree('startclockonmouseoutafter', ''); if ($startClockOnMouseOutAfter) { $startClockOnMouseOutAfter_full = $startClockOnMouseOutAfter ; } else {$startClockOnMouseOutAfter_full = '1000'; }
$directionalNav = get_option_tree('directionalnav', ''); 
	if ($directionalNav) { $directionalNav_full = $directionalNav ; } else {$directionalNav_full = 'true'; }
$captions = get_option_tree('captions', ''); 
	if ($captions) { $captions_full = $captions ; } else {$captions_full = 'true'; }
$captionAnimation = get_option_tree('captionanimation', ''); 
	if ($captionAnimation) { $captionAnimation_full = $captionAnimation ; } else {$captionAnimation_full = 'fade'; }
$captionAnimationSpeed = get_option_tree('captionanimationspeed', ''); 
	if ($captionAnimationSpeed) { $captionAnimationSpeed_full = $captionAnimationSpeed ; } else {$captionAnimationSpeed_full = '600'; }
$bullets = get_option_tree('bullets', ''); 
	if ($bullets) { $bullets_full = $bullets ; } else {$bullets_full = 'true'; }
$bulletThumbs = get_option_tree('bulletthumbs', ''); 
	if ($bulletThumbs) { $bulletThumbs_full = $bulletThumbs ; } else {$bulletThumbs_full = 'false';};
$bulletThumbLocation = get_option_tree('bulletthumblocation', '');

?>
<script type="text/javascript">    
	    //Defaults to extend options
        var defaults = {  
            animation: '<?php echo $animation_full ?>', 		// fade, horizontal-slide, vertical-slide, horizontal-push
            animationSpeed: <?php echo $animationSpeed_full ?>, 				// how fast animtions are
            timer: <?php echo $timer_full ?>, 						// true or false to have the timer
            advanceSpeed: <?php echo $advanceSpeed_full ?>, 				// if timer is enabled, time between transitions 
            pauseOnHover: <?php echo $pauseOnHover_full ?>, 				// if you hover pauses the slider
            startClockOnMouseOut: <?php echo $startClockOnMouseOut_full ?>, 		// if clock should start on MouseOut
            startClockOnMouseOutAfter: <?php echo $startClockOnMouseOutAfter_full ?>, 	// how long after MouseOut should the timer start again
            directionalNav: <?php echo $directionalNav_full ?>, 				// manual advancing directional navs
            captions: <?php echo $captions_full ?>, 					// do you want captions?
            captionAnimation: '<?php echo $captionAnimation_full ?>', 			// fade, slideOpen, none
            captionAnimationSpeed: <?php echo $captionAnimationSpeed_full ?>, 		// if so how quickly should they animate in
            bullets: <?php echo $bullets_full ?>,						// true or false to activate the bullet navigation
            bulletThumbs: <?php echo $bulletThumbs_full ?>,				// thumbnails for the bullets
            bulletThumbLocation: '',			// location from this file where thumbs will be
            afterSlideChange: function(){} 		// empty function 
     	}; 
</script>
