<?php
//Create menu for configure page
add_action('admin_menu', 'rps_admin_actions');

//To perfor action while publishing post i.e. creating the thumbnail of first image of post
add_action('publish_post','rps_publish_post');

//Add  the nedded styles & script
add_action('wp_print_styles', 'rps_add_style');


/** 
*Perform operations while publishing post
*/
function rps_publish_post(){
	$slider_content = get_option('rps_slider_content');
	if ( $slider_content== 1 || $slider_content== 3) {
		global $post;
		rps_post_img_thumb( (int) $post->ID );
	} else {
		return;
	}
}

/** 
*It creates thumbnails of first image of post
 * @param $post_id
 * @return void
*/
function rps_post_img_thumb($post_id = NULL ){
	
	$width = 960;
	$height = get_option('rps_height');
	$slider_content = get_option('rps_slider_content');
	$post_per_slide = get_option('rps_post_per_slide');
	$total_posts = get_option('rps_total_posts');
	$category_ids = get_option('rps_category_ids');
	$post_include_ids = get_option('rps_post_include_ids');
	$post_exclude_ids = get_option('rps_post_exclude_ids');
	
	$set_img_width = ($width/$post_per_slide) - 12;
	if($slider_content == 3){
		$set_img_width = (int)(($set_img_width/2) - 20);
	}
	$set_img_height = $height - 54;
	
	if ( empty($post_id) ) {
		$args = array(
			'numberposts'     => $total_posts,
			'offset'          => 0,
			'category'        => $category_ids,
			'orderby'         => 'post_date',
			'order'           => 'DESC',
			'include'         => $post_include_ids,
			'exclude'         => $post_exclude_ids,
			'post_type'       => 'post',
			'post_status'     => 'publish' );
		$recent_posts = get_posts( $args );
		if ( count($recent_posts)< $total_posts ) {
			$total_posts	= count($recent_posts);
		}
		
		foreach( $recent_posts as $key=>$val ) {
			$post_details[$key]['post_ID'] = $val->ID;
			$post_details[$key]['post_content'] = $val->post_content;
		}
	} else {
		$post_details['0']['post_ID'] = $post_id;
		$get_post_details = get_post( $post_id );
		$post_details['0']['post_content'] = $get_post_details->post_content;
	}
	
	foreach ( $post_details as $key_p=> $val_p ) {
		
		$first_img_name = '';
		$img_name='';
		$first_img_src = '';
		//$first_img_name = get_post_meta($val_p['post_ID'], 'rps_custom_thumb', true);
		$first_img_name_arr = get_post_custom_values('rps_custom_thumb', $val_p['post_ID']);
		$first_img_name = $first_img_name_arr['0'];

		if (function_exists('has_post_thumbnail') && has_post_thumbnail( $val_p['post_ID'] ) && empty($first_img_name)){
			$img_details = wp_get_attachment_image_src( get_post_thumbnail_id( $val_p['post_ID'] ), 'full' );
			$first_img_src = substr($img_details[0], (strrpos($img_details[0], 'uploads/')));
			$first_img_src = trim($first_img_src,'uploads/');
		}else{
			
			if(empty($first_img_name)){
				preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $val_p['post_content'], $matches);
			
				if ( count($matches) && isset($matches[1]) ) {
					$first_img_name = $matches[1][0];
				}	
			
				$img_files = get_children("post_parent=".$val_p['post_ID']."&post_type=attachment&post_mime_type=image");
				
				foreach ( $img_files as $key=>$val ) {
					//$img_details=wp_get_attachment_image_src($key,'full');
					$img_src = get_post_meta($key,'_wp_attached_file','true');
					$img_name = substr($img_src, 0, (strrpos($img_src, '.')));
				
					if ( strrpos($first_img_name, $img_name) ) {
						$first_img_src = $img_src;
					}
				}
			}else{
				$first_img_src = substr($first_img_name, (strrpos($first_img_name, 'uploads/')));
				$first_img_src = trim($first_img_src,'uploads/');
			}
		}
		
		$upload_dir = wp_upload_dir();
		if( !empty($first_img_src) ){	
			$size = @getimagesize( $upload_dir['basedir'].'/'.$first_img_src );
			
			if ( $set_img_width > 0 && $set_img_height > 0 && $size){
				if($size[0] <= $set_img_width && $size[1] <= $set_img_height){
					$img_file = $upload_dir['basedir'].'/'.$first_img_src;
					if ( !get_post_meta($val_p['post_ID'], '_rps_is_delete_img') ) {
						add_post_meta($val_p['post_ID'], '_rps_is_delete_img', 0);
					}
				}else {
					$img_file = image_resize($upload_dir['basedir'].'/'.$first_img_src,$set_img_width,$set_img_height,'true');
					if ( get_post_meta($val_p['post_ID'], '_rps_is_delete_img') ) {
						update_post_meta($val_p['post_ID'], '_rps_is_delete_img', 1);
					}
				}
			}
			
			if ( !empty($img_file) ) {
				$new_wrp_img_src = substr($img_file, (strrpos($img_file, 'uploads/')));
				$new_wrp_img_src = trim($new_wrp_img_src,'uploads');
				
				if ( $rps_image_src = get_post_custom_values('_rps_img_src', $val_p['post_ID']) ) {
					$old_wrp_img_src = $rps_image_src['0'];
					
					if ( $old_wrp_img_src != $new_wrp_img_src ) {
						$old_img_path = $upload_dir['basedir'].$old_wrp_img_src;
						if( !empty($old_wrp_img_src) ) {
							$is_delete = get_post_meta($val_p['post_ID'], '_rps_is_delete_img');
							if( is_file($old_img_path) && $is_delete[0] ){	
								@unlink($old_img_path);
							}			
						}
						update_post_meta($val_p['post_ID'], '_rps_img_src', $new_wrp_img_src);
					}
				} else {
					add_post_meta($val_p['post_ID'], '_rps_img_src', $new_wrp_img_src);
				}
			}
		}else{
			if ( $rps_image_src = get_post_custom_values('_rps_img_src', $val_p['post_ID']) ) {
				$old_wrp_img_src = $rps_image_src['0'];
				
				$old_img_path = $upload_dir['basedir'].$old_wrp_img_src;
				if( !empty($old_wrp_img_src) ) {
					$is_delete = get_post_meta($val_p['post_ID'], '_rps_is_delete_img');
					if( is_file($old_img_path) && $is_delete[0] ){	
						@unlink($old_img_path);
					}
				}
				delete_post_meta($val_p['post_ID'], '_rps_img_src', $old_wrp_img_src);
			}
		}
	}
	return;
}

/** Create menu for options page */
function rps_admin_actions() {
   add_submenu_page( 'option_tree', 'PostSlider', 'Posts Slider Settings', 'manage_options', 'recent-posts-slider', 'rps_admin');
}

/** To perform admin page functionality */
function rps_admin() {
    	if ( !current_user_can('manage_options') )
      		wp_die( __('You do not have sufficient permissions to access this page.') );
	include('recent-posts-slider-admin.php');
}

/** Link the needed stylesheet */
function rps_add_style() {
	wp_enqueue_style('rps-style', get_bloginfo('template_url').'/functions/recent-posts-slider/css/style.css');
}



/** To show slider 
 * @return output
*/
function rps_show() {	
	$width = 960;
	$height = get_option('rps_height');
	$post_per_slide = get_option('rps_post_per_slide');
	$total_posts = get_option('rps_total_posts');
	$slider_content = get_option('rps_slider_content');
	$category_ids = get_option('rps_category_ids');
	$post_include_ids = get_option('rps_post_include_ids');
	$post_exclude_ids = get_option('rps_post_exclude_ids');
	$post_title_color = get_option('rps_post_title_color');
	$slider_speed = get_option('rps_slider_speed');
	$pagination_style = get_option('rps_pagination_style');
	$excerpt_words = get_option('rps_excerpt_words');
	
	if ( empty($height) ) {
		$height = 250;
	}else{
		$height = $height;
	}
	
	if ( empty($total_posts) ) {
		$total_posts = 4;
	}else{
		$total_posts = $total_posts;
	}
	
	if ( empty($slider_speed) ) {
		$slider_speed = 7000;
	}else{
		$slider_speed = $slider_speed * 1000;
	}
	if ( empty($post_title_color) ){
		$post_title_color = "#666";
	}else{
		$post_title_color = "#".$post_title_color;
	}
	if ( empty($excerpt_words) ){
		$excerpt_words = 40;
	}else{
		$excerpt_words = $excerpt_words;
	}
	
	$excerpt_length = '';
	$excerpt_length = abs( (($width-40)/20) * (($height-55)/15) );
	/*if ( ($width) > $height)
	$excerpt_length = $excerpt_length - (($excerpt_length * 5) /100);
	else
	$excerpt_length = $excerpt_length - (($excerpt_length * 30) /100);*/
	
	$post_details = NULL;
	$args = array(
			'numberposts'     => $total_posts,
			'offset'          => 0,
			'category'        => $category_ids,
			'orderby'         => 'post_date',
			'order'           => 'DESC',
			'include'         => $post_include_ids,
			'exclude'         => $post_exclude_ids,
			'post_type'       => 'post',
			'post_status'     => 'publish' );
	$recent_posts = get_posts( $args );
	
	if ( count($recent_posts)< $total_posts ) {
		$total_posts	= count($recent_posts);
	}
	
	if ( ($total_posts%$post_per_slide)==0 )
		$paging  = $total_posts/$post_per_slide; 
	else
		$paging  = ($total_posts/$post_per_slide) + 1;  
			
	foreach ( $recent_posts as $key=>$val ) {
		$post_details[$key]['post_title'] = $val->post_title;
		$post_details[$key]['post_permalink'] = get_permalink($val->ID);
		
		if ( $slider_content == 2 ) {
			if ( !empty($val->post_excerpt) ) 
				$post_details[$key]['post_excerpt'] = create_excerpt($val->post_excerpt, $excerpt_length, $post_details[$key]['post_permalink'], $excerpt_words);
			else
				$post_details[$key]['post_excerpt'] = create_excerpt($val->post_content, $excerpt_length, $post_details[$key]['post_permalink'], $excerpt_words);
		} elseif ( $slider_content == 1 ) {
			$post_details[$key]['post_first_img'] = get_post_meta($val->ID, '_rps_img_src');
		}elseif ( $slider_content == 3 ) {
			$post_details[$key]['post_first_img'] = get_post_meta($val->ID, '_rps_img_src');
			if ( !empty($val->post_excerpt) ) 
				$post_details[$key]['post_excerpt'] = create_excerpt($val->post_excerpt, ($excerpt_length/2)-10, $post_details[$key]['post_permalink'], $excerpt_words);
			else
				$post_details[$key]['post_excerpt'] = create_excerpt($val->post_content, ($excerpt_length/2)-10, $post_details[$key]['post_permalink'], $excerpt_words);
		}
	}
	
	$upload_dir = wp_upload_dir();
	echo '<!--Automatic Image Slider w/ CSS & jQuery with some customization-->';
	echo'<script type="text/javascript">
	$j = jQuery.noConflict();
	$j(document).ready(function() {

	//Set Default State of each portfolio piece
	$j("#rps .paging").show();
	$j("#rps .paging a:first").addClass("active");
	
	$j(".slide_rps").css({"width" : '.$width.'});
	$j("#rps .window").css({"width" : '.($width).'});
	$j("#rps .window").css({"height" : '.$height.'});

	$j("#rps .col").css({"width" : '.(($width/$post_per_slide)-15).'});
	$j("#rps .col").css({"height" : '.($height-4).'});
	$j("#rps .col p.post-title span").css({"color" : "'.($post_title_color).'"});
	
	var imageWidth = $j("#rps .window").width();
	//var imageSum = $j("#rps .slider_rps div").size();
	var imageReelWidth = imageWidth * '.$paging.';
	
	//Adjust the image reel to its new size
	$j("#rps .slider_rps").css({"width" : imageReelWidth});

	//Paging + Slider Function
	rotate = function(){	
		var triggerID = $active.attr("rel") - 1; //Get number of times to slide
		
		var sliderPosition = triggerID * imageWidth; //Determines the distance the image reel needs to slide

		$j("#rps .paging a").removeClass("active"); 
		$active.addClass("active");
		
		//Slider Animation
		$j("#rps .slider_rps").animate({ 
			left: -sliderPosition
		}, 500 );
		
	}; 
	
	//Rotation + Timing Event
	rotateSwitch = function(){		
		play = setInterval(function(){ //Set timer - this will repeat itself every 3 seconds
			$active = $j("#rps .paging a.active").next();
			if ( $active.length === 0) { //If paging reaches the end...
				$active = $j("#rps .paging a:first"); //go back to first
			}
			rotate(); //Trigger the paging and slider function
		}, '.$slider_speed.');
	};
	
	rotateSwitch(); //Run function on launch
	
	//On Hover
	$j("#rps .slider_rps a").hover(function() {
		clearInterval(play); //Stop the rotation
	}, function() {
		rotateSwitch(); //Resume rotation
	});	
	
	//On Click
	$j("#rps .paging a").click(function() {	
		$active = $j(this); //Activate the clicked paging
		//Reset Timer
		clearInterval(play); //Stop the rotation
		rotate(); //Trigger rotation immediately
		rotateSwitch(); // Resume rotation
		return false; //Prevent browser jump to link anchor
	});	
});

</script>';

echo '<div id="rps">
            <div class="window">	
                <div class="slider_rps">';
		$p=0;
		for ( $i = 1; $i <= $total_posts; $i+=$post_per_slide ) {
			echo '<div class="slide_rps">';
					for ( $j = 1; $j <= $post_per_slide; $j++ ) {
						echo '<div class="col">';
						if ( $slider_content == 2 ){
							echo '<p class="recent_post-title cont1"><a href="'.$post_details[$p]['post_permalink'].'">'.$post_details[$p]['post_title'].'</a></p><br/><p class="slider-content">'.$post_details[$p]['post_excerpt'].'</p></div>';
						}elseif ( $slider_content == 1 ){
							echo '<p class="slider-content-img">';
							if( !empty($post_details[$p]['post_first_img']['0']) ){
								$rps_img_src_path = $upload_dir['baseurl'].$post_details[$p]['post_first_img']['0'];
								echo '<a href="'.$post_details[$p]['post_permalink'].'"><center><img src="'.$rps_img_src_path.'" /></center></a>';
							}
							echo '</p></div>';			
						}elseif ( $slider_content == 3 ){
							echo '<p class="slider-content-both">';
							
							if( !empty($post_details[$p]['post_first_img']['0']) || !empty($post_details[$p]['post_excerpt'])){
								$rps_img_src_path = $upload_dir['baseurl'].$post_details[$p]['post_first_img']['0'];
								echo '<a href="'.$post_details[$p]['post_permalink'].'"><img class="recent_post_image" src="'.$rps_img_src_path.'"  /></a><p class="recent_post-title"><a href="'.$post_details[$p]['post_permalink'].'">'.$post_details[$p]['post_title'].'</a></p><br />';
								echo $post_details[$p]['post_excerpt'];
							}
							echo '</p></div>';			
						}	
						$p++;
						if ( $p == $total_posts )
							$p = 0;
					}
					echo '<div class="clr"></div>
				</div>';
		}
		echo '
                </div>
            </div>
            <div class="paging">';
				for ( $p = 1; $p <= $paging; $p++ ) {
					if( $pagination_style == '2' ){
						echo '<a href="#" rel="'.$p.'">&bull;</a>';
					}else{
						echo '<a href="#" rel="'.$p.'">&bull;</a>';
					}
				}
            echo '</DIV>
        </div><div class="rps-clr"></div>'; 
}

/** Create post excerpt manually
 * @param $post_content
 * @param $excerpt_length
 * @return post_excerpt or  void
*/
function create_excerpt( $post_content, $excerpt_length, $post_permalink, $excerpt_words=NULL){
	$post_excerpt = strip_shortcodes($post_content);
	$post_excerpt = str_replace(']]>', ']]&gt;', $post_excerpt);
	$post_excerpt = strip_tags($post_excerpt);
	$read_more = get_option_tree('blog_read', ''); 
	
	if( !empty($excerpt_words) ){	
		if ( !empty($post_excerpt) ) {
			$words = explode(' ', $post_excerpt, $excerpt_words );
			array_pop($words);
			if($read_more){
			array_push($words, ' <a href="'.$post_permalink.'" class="rps_read_more">'.$read_more.'</a>');
			}
			else{
			array_push($words, ' <a href="'.$post_permalink.'" class="rps_read_more">Read more</a>');
			}
			$post_excerpt_rps = implode(' ', $words);
			return $post_excerpt_rps;
		} else {
			return;
		}
	}else{
		$post_excerpt_rps = substr( $post_excerpt, 0, $excerpt_length );
		if ( !empty($post_excerpt_rps) ) {
			if ( strlen($post_excerpt) > strlen($post_excerpt_rps) ){
				$post_excerpt_rps =substr( $post_excerpt_rps, 0, strrpos($post_excerpt_rps,' '));
			}	
			if($read_more){
			array_push($words, ' <a href="'.$post_permalink.'" class="rps_read_more">'.$read_more.'</a>');
			}
			else{
			array_push($words, ' <a href="'.$post_permalink.'" class="rps_read_more">Read more</a>');
			}
			return $post_excerpt_rps;
		} else {
			return;
		}
	}
}

?>