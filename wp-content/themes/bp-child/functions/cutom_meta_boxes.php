<?php

/*********************************************** HEADER ELEMENT CHOICE*********************************************************/

add_action( 'add_meta_boxes', 'page_opt_meta_box_add' );
function page_opt_meta_box_add() {
$post_types = get_post_types();
	foreach ( $post_types as $post_type ) {
	add_meta_box( 'page_option_choice', 'Page Options', 'page_opt_meta_box', $post_type, 'side', 'high' );
    }
}


function page_opt_meta_box( $post )
{
	$values = get_post_custom( $post->ID );
	$color = isset( $values['page_color_choice'] ) ? esc_attr( $values['page_color_choice'][0] ) : '';
	$selected = isset( $values['header_choice_select'] ) ? esc_attr( $values['header_choice_select'][0] ) : '';
	$position = isset( $values['page_position_choice'] ) ? esc_attr( $values['page_position_choice'][0] ) : '';
	$image = isset( $values['page_image_choice'] ) ? esc_attr( $values['page_image_choice'][0] ) : '';
	$selected_repeat = isset( $values['header_image_repeat'] ) ? esc_attr( $values['header_image_repeat'][0] ) : '';
	$check = isset( $values['content_sidebar_check'] ) ? esc_attr( $values['content_sidebar_check'][0] ) : '';
	wp_nonce_field( 'page_opt_meta_box_nonce', 'meta_box_nonce' );
	?>
	
	<p><span><strong>Select Header Element:</strong></span><br />
		<select name="header_choice_select" id="header_choice_select">
			<option value="" <?php selected( $selected, '' ); ?>>None</option>
			<option value="post-slider" <?php selected( $selected, 'post-slider' ); ?>>Post Slider</option>
			<option value="orbit-slider" <?php selected( $selected, 'orbit-slider' ); ?>>Orbit Slider</option>
			<option value="offer-slider" <?php selected( $selected, 'offer-slider' ); ?>>Offer Slider</option>
			<option value="custom-element" <?php selected( $selected, 'custom-element' ); ?>>Custom Header Element (NO background)</option>
			<option value="custom-element-bg" <?php selected( $selected, 'custom-element-bg' ); ?>>Custom Header Element (1st background)</option>
			<option value="custom-element-special-bg" <?php selected( $selected, 'custom-element-special-bg' ); ?>>Custom Header Element (2nd background)</option>
			<option value="custom-element-full" <?php selected( $selected, 'custom-element-full' ); ?>>Custom Header Element (FULL width)</option>
		</select>
	<br />
	<span style="font-size:11px; color:#999; line-height:1.3;">* To select header for blog go to Appearance / Theme options / Blog Options
	</span>
</p>
	<span><strong>Custom background color:</strong></span><br/>
	<input style="width:85px;" type="text" name="page_color_choice" id="page_color_choice" value="<?php echo $color; ?>" />
		<a href="javascript: void(0)" onclick="window.open('http://www.colorpicker.com/', 'windowname1', 'width=582, height=496, left=50px'); return false;" title="Color picker"><img src="<?php echo su_plugin_url(); ?>/images/generator/colorpicker.png" alt="" /></a>
	<br/>		
	<span style="font-size:11px; color:#999; line-height:1.3;">* Example: #FFCC66</span>

	<p><span><strong>Custom background image url:</strong></span><br />
	<input style="border:1px solid #27b5ea; width:100%;" type="text" name="page_image_choice" id="page_image_choice" value="<?php echo $image; ?>" /><br/>
	<span style="font-size:11px; color:#999; line-height:1.3;">* Make sure you input FULL urls to the image (including http://) </span></p>
		
	<p>
	<span><strong>Background repeat:</strong></span><br/>
	<select name="header_image_repeat" id="header_image_repeat">
			<option value="repeat" <?php selected( $selected_repeat, 'repeat' ); ?>>Repeat both</option>
			<option value="repeat-x" <?php selected( $selected_repeat, 'repeat-x' ); ?>>Repeat x</option>
			<option value="repeat-y" <?php selected( $selected_repeat, 'repeat-y' ); ?>>Repeat y</option>
			<option value="no-repeat" <?php selected( $selected_repeat, 'no-repeat' ); ?>>No repeat</option>
		</select>
	</p>
	
	<span><strong>Background position:</strong></span><br/>
	<input style="width:85px;" type="text" name="page_position_choice" id="page_position_choice" value="<?php echo $position; ?>" /><br/>		
	<span style="font-size:11px; color:#999; line-height:1.3;">* The first value is the horizontal position and the second value is the vertical. Example: 50% 0%</span>	
	
		<p>
		<input type="checkbox" name="content_sidebar_check" id="content_sidebar_check" <?php checked( $check, 'on' ); ?> />
		<label for="content_sidebar_check"><span><strong>Enable before content widget area</strong></span></label>
	</p>

	<?php	
}


add_action( 'save_post', 'cd_meta_box_save' );
function cd_meta_box_save( $post_id )
{
	// Bail if we're doing an auto save
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	
	// if our nonce isn't there, or we can't verify it, bail
	if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'page_opt_meta_box_nonce' ) ) return;
	
	// if our current user can't edit this post, bail
	if( !current_user_can( 'edit_post' ) ) return;
	
	// now we can actually save the data
	$allowed = array( 
		'a' => array( // on allow a tags
		'href' => array() // and those anchords can only have href attribute
		)
	);
	
	if( isset( $_POST['page_color_choice'] ) )
		update_post_meta( $post_id, 'page_color_choice', wp_kses( $_POST['page_color_choice'], $allowed ) );

	if( isset( $_POST['page_position_choice'] ) )
		update_post_meta( $post_id, 'page_position_choice', wp_kses( $_POST['page_position_choice'], $allowed ) );
	
	if( isset( $_POST['page_image_choice'] ) )
		update_post_meta( $post_id, 'page_image_choice', wp_kses( $_POST['page_image_choice'], $allowed ) );
		
	if( isset( $_POST['header_choice_select'] ) )
		update_post_meta( $post_id, 'header_choice_select', esc_attr( $_POST['header_choice_select'] ) );
	
	if( isset( $_POST['header_image_repeat'] ) )
		update_post_meta( $post_id, 'header_image_repeat', esc_attr( $_POST['header_image_repeat'] ) );

	$chk = ( isset( $_POST['content_sidebar_check'] ) && $_POST['content_sidebar_check'] ) ? 'on' : 'off';
	update_post_meta( $post_id, 'content_sidebar_check', $chk );
		
}


/*********************************************** CUSTOM HEADER IMAGE ***************************************************/
  
 add_action('admin_menu', 'custom_header_add');
add_action('save_post', 'saveMetaData', 10, 2);
add_action('admin_head', 'embedUploaderCode');
 
//Define the metabox attributes.
$metaBox = array(
  'id'     => 'custom_header',
  'title'    => 'Custom Header Image',
  'page'     => 'page',
  'context'  => 'normal',
  'priority'   => 'high',
  'fields' => array(
    array(
      'name'   => 'Custom Header Image',
      'desc'   => 'Custom Header Image will only work if  "Custom Header Element" choosen.', 
      'id'  => 'custom_header_id',  //value is stored with this as key.
      'class' => 'image_upload_field',
      'type'   => 'media'
    )
  )
);
 
 
function custom_header_add() {
$post_types = get_post_types();
foreach ( $post_types as $post_type ) {
  global $metaBox;
  add_meta_box($metaBox['id'], $metaBox['title'], 'custom_header_b', 
     $post_type, $metaBox['context'], $metaBox['priority']);
 

    }
}


 
/**
* Create Metabox HTML.
*/
function custom_header_b($post) {
  global $metaBox;
  if (function_exists('wp_nonce_field')) {
    wp_nonce_field('awd_nonce_action','awd_nonce_field');
  }
 
  foreach ($metaBox['fields'] as $field) {
    echo '<div class="awdMetaBox">';
    //get attachment id if it exists.
    $meta = get_post_meta($post->ID, $field['id'], true);
    switch ($field['type']) {
      case 'media':
?>
        <span style="font-size:11px; color:#999; line-height:1.3;"><?php echo $field['desc']; ?></span><br/>
        <div class="awdMetaImage">
<?php 
        if ($meta) {
          echo wp_get_attachment_image( $meta, 'thumbnail', true);
          $attachUrl = wp_get_attachment_url($meta);
          echo 
          '<p>URL: <a target="_blank" href="'.$attachUrl.'">'.$attachUrl.'</a></p>';
        }
?>    
        </div><!-- end .awdMetaImage -->
        <p>
          <input type="hidden" 
            class="metaValueField" 
            id="<?php echo $field['id']; ?>" 
            name="<?php echo $field['id']; ?>"
            value="<?php echo $meta; ?>" /> 
          <input class="image_upload_button"  type="button" value="Choose File" /> 
          <input class="removeImageBtn" type="button" value="Remove File" />
        </p>
 
<?php
      break;
    }
    echo '</div> <!-- end .awdMetaBox -->';
  } //end foreach
}//end function custom_header_b
 
 
function saveMetaData($post_id, $post) {
  //make sure we're saving at the right time.
  //DOING_AJAX is set when saving a quick edit on the page that displays all posts/pages  
  //Not checking for this will cause our meta data to be overwritten with blank data.
  if ( empty($_POST)
    || !wp_verify_nonce(isset($_POST['awd_nonce_field']) && $_POST['awd_nonce_field'],'awd_nonce_action')
    || $post->post_type == 'revision'
    || defined('DOING_AJAX' )) {
    return;
  }
 
  global $metaBox;
  global $wpdb;
 
  foreach ($metaBox['fields'] as $field) {
    $value = $_POST[$field['id']];
	
 
    if ($field['type'] == 'media' && !is_numeric($value) ) {
      //Convert URL to Attachment ID.
      $value = $wpdb->get_var(
        "SELECT ID FROM $wpdb->posts 
         WHERE guid = '$value' 
         AND post_type='attachment' LIMIT 1");
    }
    update_post_meta($post_id, $field['id'], $value);
  }//end foreach
}//end function saveMetaData
 
/**
 * Add JavaScript to get URL from media uploader.
 */
function embedUploaderCode() {
  ?>
  <script type="text/javascript">
  jQuery(document).ready(function() {
 
    jQuery('.removeImageBtn').click(function() {
      jQuery(this).closest('p').prev('.awdMetaImage').html('');   
      jQuery(this).prev().prev().val('');
      return false;
    });
 
    jQuery('.image_upload_button').click(function() {
      inputField = jQuery(this).prev('.metaValueField');
      tb_show('', 'media-upload.php?TB_iframe=true');
      window.send_to_editor = function(html) {
        url = jQuery(html).attr('href');
        inputField.val(url);
        inputField.closest('p').prev('.awdMetaImage').html('<p>URL: '+ url + '</p>');  
        tb_remove();
      };
      return false;
    });
  });
 
  </script>
  <?php
}//end function embedUploaderCode()


/*********************************************** CUSTOM HEADER HTML ****************************************************/

$meta_box = array(
	'id' => 'custom_header_html_add',
	'title' => 'Custom Header HTML, insert anything you want.',
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(

		array(
			'name' => 'Textarea',
			'desc' => 'Custom Header HTML will only work if "Custom Header Element" choosen. Supports Flash objects and shortcodes!',
			'id' => 'custom_header_html',
			'type' => 'textarea',
			'std' => ''
		)		
	)
);

add_action('admin_menu', 'mytheme_add_box');

// Add meta box
function mytheme_add_box() {
$post_types = get_post_types();
foreach ( $post_types as $post_type ) {
	global $meta_box;
	
	add_meta_box($meta_box['id'], $meta_box['title'], 'mytheme_show_box', $post_type, $meta_box['context'], $meta_box['priority']);
	}
}

// Callback function to show fields in meta box
function mytheme_show_box() {
	global $meta_box, $post;
	
 if (function_exists('wp_nonce_field')) {
    wp_nonce_field('mytheme_nonce_action','mytheme_nonce_field');
  }

		

	foreach ($meta_box['fields'] as $field) {
		// get current post meta data
		$meta = get_post_meta($post->ID, $field['id'], true);
		
		
		switch ($field['type']) {
				case 'textarea':
				echo '<span style="font-size:11px; color:#999; line-height:1.3;">', $field['desc'],'</span><br/><br/><textarea name="', $field['id'], '" id="', $field['id'], '"  rows="6" style="width:99%">', $meta , '</textarea>';
				break;
			
		}
	
	}
	
	
}

add_action('save_post', 'mytheme_save_data');

// Save data from meta box
function mytheme_save_data($post_id) {
	global $meta_box;
	
	// verify nonce
  if ( empty($_POST)
    || !wp_verify_nonce(isset($_POST['mytheme_nonce_field']) && $_POST['mytheme_nonce_field'],'mytheme_nonce_action')
    || defined('DOING_AJAX' )) {
    return;
  }

	// check autosave
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return $post_id;
	}

	// check permissions
	if ('page' == $_POST['post_type']) {
		if (!current_user_can('edit_page', $post_id)) {
			return $post_id;
		}
	} elseif (!current_user_can('edit_post', $post_id)) {
		return $post_id;
	}
	
	foreach ($meta_box['fields'] as $field) {
		$old = get_post_meta($post_id, $field['id'], true);
		$new = $_POST[$field['id']];
		
		if ($new && $new != $old) {
			update_post_meta($post_id, $field['id'], $new);
		} elseif ('' == $new && $old) {
			delete_post_meta($post_id, $field['id'], $old);
		}
	}
}


/*********************************************** PORTFOLIO META BOXES **************************************************/

add_action( 'add_meta_boxes', 'pf_meta_box_add' );
function pf_meta_box_add()
{
	add_meta_box( 'portfolio_meta_box', 'Item Options', 'pf_meta_box', 'portfolio', 'normal', 'high' );
}

function pf_meta_box( $post )
{
	$pf_values = get_post_custom( $post->ID );
	$pf_text = isset( $pf_values['pf_meta_box_text'] ) ? esc_attr( $pf_values['pf_meta_box_text'][0] ) : 'Client:';
	$pf_text2 = isset( $pf_values['pf_meta_box_text2'] ) ? esc_attr( $pf_values['pf_meta_box_text2'][0] ) : '';
	$pf_text3 = isset( $pf_values['pf_meta_box_text3'] ) ? esc_attr( $pf_values['pf_meta_box_text3'][0] ) : 'Model:';
	$pf_text4 = isset( $pf_values['pf_meta_box_text4'] ) ? esc_attr( $pf_values['pf_meta_box_text4'][0] ) : '';
	$pf_text5 = isset( $pf_values['pf_meta_box_text5'] ) ? esc_attr( $pf_values['pf_meta_box_text5'][0] ) : 'URL:';
	$pf_text6 = isset( $pf_values['pf_meta_box_text6'] ) ? esc_attr( $pf_values['pf_meta_box_text6'][0] ) : '';
	$pf_text7 = isset( $pf_values['pf_meta_box_text7'] ) ? esc_attr( $pf_values['pf_meta_box_text7'][0] ) : '';
	$pf_selected = isset( $pf_values['pf_meta_box_select'] ) ? esc_attr( $pf_values['pf_meta_box_select'][0] ) : '';
	wp_nonce_field( 'pf_meta_box_nonce', 'meta_box_nonce2' );
	?>
	<p>
		<label for="pf_meta_box_select"><strong>Cover style</strong></label>
		<select name="pf_meta_box_select" id="pf_meta_box_select">
			<option value="magnifier" <?php selected( $pf_selected, 'magnifier' ); ?>>Magnifier</option>
			<option value="magnifier2" <?php selected( $pf_selected, 'magnifier2' ); ?>>Magnifier with title</option>
			<option value="fade" <?php selected( $pf_selected, 'fade' ); ?>>Fade</option>
			<option value="bar" <?php selected( $pf_selected, 'bar' ); ?>>Slide up</option>
			<option value="bar2" <?php selected( $pf_selected, 'bar2' ); ?>>Slide up (visible top)</option>
			<option value="bar3" <?php selected( $pf_selected, 'bar3' ); ?>>Slide down</option>
			<option value="cover" <?php selected( $pf_selected, 'cover' ); ?>>Cover (slide to left)</option>
			<option value="cover2" <?php selected( $pf_selected, 'cover2' ); ?>>Cover (slide down)</option>
			<option value="cover3" <?php selected( $pf_selected, 'cover3' ); ?>>Cover (slide up/left)</option>
		</select>
	</p>
	<span style="font-size:11px; color:#999; line-height:1.3;">Choose cover style for portfolio item thumbnail.</span>

	<br/>
	<p>
		<label for="pf_meta_box_text"><strong>Details:</strong></label><br/>
		<input type="text" name="pf_meta_box_text" id="pf_meta_box_text" value="<?php echo $pf_text; ?>" style="width:80px;" />
		<input type="text" name="pf_meta_box_text2" id="pf_meta_box_text2" value="<?php echo $pf_text2; ?>" style="width:170px;" />
	</p>
	<p>
		<input type="text" name="pf_meta_box_text3" id="pf_meta_box_text3" value="<?php echo $pf_text3; ?>" style="width:80px;" />
		<input type="text" name="pf_meta_box_text4" id="pf_meta_box_text4" value="<?php echo $pf_text4; ?>" style="width:170px;" />
	</p>
	<p>
		<input type="text" name="pf_meta_box_text5" id="pf_meta_box_text5" value="<?php echo $pf_text5; ?>" style="width:80px;" />
		<input type="text" name="pf_meta_box_text6" id="pf_meta_box_text6" value="<?php echo $pf_text6; ?>" style="width:170px;" />
	</p>
	<span style="font-size:11px; color:#999; line-height:1.3;">Leave second field blank to disable corresponding string. Default detail titles are changeable.</span>
	<br/>
	<p>
	<label for="pf_meta_box_phtml"><strong>HTML description for one column portfolio:</strong></label><br/>
	<textarea name="pf_meta_box_text7" id="pf_meta_box_text7"  rows="4" style="width:99%"><?php echo $pf_text7; ?></textarea>
	</p>
	<span style="font-size:11px; color:#999; line-height:1.3;">This content will be displayed next to thumbnail in portfolio one column view.</span>
	<?php	
}


add_action( 'save_post', 'pf_meta_box_save' );
function pf_meta_box_save( $post_id )
{
	// Bail if we're doing an auto save
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	
	// if our nonce isn't there, or we can't verify it, bail
	if( !isset( $_POST['meta_box_nonce2'] ) || !wp_verify_nonce( $_POST['meta_box_nonce2'], 'pf_meta_box_nonce' ) ) return;
	
	// if our current user can't edit this post, bail
	if( !current_user_can( 'edit_post' ) ) return;
	
	// now we can actually save the data
	$pf_allowed = array( 
		'a' => array( // on allow a tags
			'href' => array() // and those anchords can only have href attribute
		)
	);
	
	// Probably a good idea to make sure your data is set
	if( isset( $_POST['pf_meta_box_text'] ) )
		update_post_meta( $post_id, 'pf_meta_box_text', wp_kses( $_POST['pf_meta_box_text'], $pf_allowed ) );
		
	if( isset( $_POST['pf_meta_box_text2'] ) )
			update_post_meta( $post_id, 'pf_meta_box_text2', wp_kses( $_POST['pf_meta_box_text2'], $pf_allowed ) );
			
	if( isset( $_POST['pf_meta_box_text3'] ) )
			update_post_meta( $post_id, 'pf_meta_box_text3', wp_kses( $_POST['pf_meta_box_text3'], $pf_allowed ) );
			
	if( isset( $_POST['pf_meta_box_text4'] ) )	
		update_post_meta( $post_id, 'pf_meta_box_text4', wp_kses( $_POST['pf_meta_box_text4'], $pf_allowed ) );
		
	if( isset( $_POST['pf_meta_box_text5'] ) )	
		update_post_meta( $post_id, 'pf_meta_box_text5', wp_kses( $_POST['pf_meta_box_text5'], $pf_allowed ) );
		
	if( isset( $_POST['pf_meta_box_text6'] ) )
		update_post_meta( $post_id, 'pf_meta_box_text6', wp_kses( $_POST['pf_meta_box_text6'], $pf_allowed ) );
		
	if( isset( $_POST['pf_meta_box_text7'] ) )
		update_post_meta( $post_id, 'pf_meta_box_text7', esc_html( $_POST['pf_meta_box_text7'], $pf_allowed ) );
		
	if( isset( $_POST['pf_meta_box_select'] ) )
		update_post_meta( $post_id, 'pf_meta_box_select', esc_attr( $_POST['pf_meta_box_select'] ) );
		
}

/*********************************************** NON SORTABLE PORTFOLIO CATEGORY CHOOSE *********************************/

add_action( 'add_meta_boxes', 'add_portfolio_cat' );
function add_portfolio_cat() {
	add_meta_box( 'portfolio_cat', 'Portfolio categories to show in paged portfolio template', 'portfolio_cat_id', 'page', 'normal', 'default' );
}


function portfolio_cat_id( $post )
{
	$cat_values = get_post_custom( $post->ID );
	$cat_id = isset( $cat_values['portfolio_cat_id_value'] ) ? esc_attr( $cat_values['portfolio_cat_id_value'][0] ) : '';
	wp_nonce_field( 'portfolio_cat_id_nonce', 'meta_box_nonce3' );
	?>
	
	
	<span style="font-size:11px; color:#999; line-height:1.3;">Enter SLUG names of categories (comma separated) you want to show in paged portfolio template or leave blank to show all.</span>
	<p><input type="text" name="portfolio_cat_id_value" id="portfolio_cat_id_value" value="<?php echo $cat_id; ?>" style="width:100%;" />	</p>

	
	<?php	
}


add_action( 'save_post', 'portfolio_cat_id_save' );
function portfolio_cat_id_save( $post_id )
{
	// Bail if we're doing an auto save
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	
	// if our nonce isn't there, or we can't verify it, bail
	if( !isset( $_POST['meta_box_nonce3'] ) || !wp_verify_nonce( $_POST['meta_box_nonce3'], 'portfolio_cat_id_nonce' ) ) return;
	
	// if our current user can't edit this post, bail
	if( !current_user_can( 'edit_post' ) ) return;
	
	// now we can actually save the data
	$allowed = array( 
		'a' => array( // on allow a tags
		'href' => array() // and those anchords can only have href attribute
		)
	);
	
	if( isset( $_POST['portfolio_cat_id_value'] ) )
		update_post_meta( $post_id, 'portfolio_cat_id_value', wp_kses( $_POST['portfolio_cat_id_value'], $allowed ) );
	
		
}

?>