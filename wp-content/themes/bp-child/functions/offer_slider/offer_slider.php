<div id="slider_offer">
	<div id="mover">
		<?php
		if ( function_exists( 'get_option_tree' ) ) {
		  $slides = get_option_tree( 'offer_slider_images','', false, true, -1 );
		  foreach( $slides as $slide ) {
			echo '
				<div class="slide_offer">';
					if(empty($slide['link'])){
					echo '
						<h1>'.$slide['title'].'</h1>
						<p>'.do_shortcode ($slide['description']).'</p>
						<img src="'.$slide['image'].'" alt="'.$slide['title'].'" />';
					}
					elseif(isset($slide['link'])){
					echo '
						<a href="'.$slide['link'].'"><h1>'.$slide['title'].'</h1></a>
						<p>'.do_shortcode ($slide['description']).'</p>
						<a href="'.$slide['link'].'"><img src="'.$slide['image'].'" alt="'.$slide['title'].'" /></a>';
				}
				echo '</div>';
		  }
		}
		?>
	</div>		
</div>		
