<?php
wp_enqueue_script('Offer_slider_js', get_bloginfo('template_url') . '/functions/offer_slider/js/offer-slider.js', array('jquery'), '', false);

$delay = get_option_tree('offer_slider_delay', ''); 
	if ($delay) { $delay_full = $delay ; } else {$delay_full = '4000'; }

?>
<script type="text/javascript">   
	var delayLength = <?php echo $delay_full; ?>;
</script>