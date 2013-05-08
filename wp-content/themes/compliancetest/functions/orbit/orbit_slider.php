<!-- ORBIT SLIDER -->	
<div class="container_orbit">
<div id="featured"> 
		<?php
		if ( function_exists( 'get_option_tree' ) ) {
		  $slides = get_option_tree( 'orbit_slider_images','', false, true, -1 );
		  foreach( $slides as $slide ) {
			if(empty($slide['link'])){
				echo  '<img src="'.$slide['image'].'" alt="'.$slide['title'].'" data-caption="#'.$slide['order'].'" />';
			} 
			elseif(isset($slide['link'])){
				echo  '<a href="'.$slide['link'].'"><img src="'.$slide['image'].'" alt="'.$slide['title'].'" data-caption="#'.$slide['order'].'" /></a>';
			} 
			echo '<span class="orbit-caption" id="'.$slide['order'].'">'.do_shortcode ($slide['description']).'</span>';
			}
		}
		?>
</div>		
</div>