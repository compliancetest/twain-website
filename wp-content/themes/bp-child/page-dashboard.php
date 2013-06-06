<?php
/*
 * Template Name: My Suites
 */
get_header();
?>

<div class="content" id="my_profile">
	<div class="space25"></div>
	<div class="column fifth left nopaddingleft nopaddingright sidebar">
		<?php get_sidebar('dashboard'); ?>
	</div>
		
	<div class="column four_fifths right container">
				
	<?php 
	if (isset($_POST['new_ts'])){
		// Form For add New Test Suite
		?>
		<form name="add_new_ts" action="" method="post" enctype="multipart/form-data">
		<div class="column left nopadding">
				<div class="default_grid">
					<div class="grid_head blue_head">
						<div class="grid_row">
							<div class="grid_cell grid_button grid_button_left expandable"><a href="#"><span class="simple_tooltip radius6">Expand<span></span></span></a></div>
							<div class="grid_cell width60P"><h5>Test Suite Information</h5></div>
							<div class="clear"></div>
						</div>
					</div>
					<div class="grid_body">
						<div class="grid_row light_gray_bcg noborderbottom">
							<div class="grid_cell width30P left15 right10">
								<b>Name</b> <br>
								<input type="text" name="ts_name_frontend" id="ts_name_id" class="req_field">
							</div>
							<div class="grid_cell width30P left10 right10">
								<b>Identifier</b> <br>
								<input type="text" name="ts_identifier" id="ts_identifier_id" class="req_field">
							</div>
							<div class="grid_cell width30P left10">
								<b>Issue Date</b> <br>
								<input type="date" name="ts_issue_date" id="ts_issue_date_id" class="req_field">
							</div>
							<div class="clear"></div>
						</div>
						
						<div class="grid_row light_gray_bcg noborderbottom">
							<div class="grid_cell width30P left15 right10">
								<b>Issuer</b> <br>
								<input type="text" name="ts_issuer">
							</div>
							<div class="grid_cell width30P left10 right10">
								<b>Status</b> <br>
								<input class="" type="radio" name="ts_status" value="Active" class="req_field">Active
								<input class="left5" type="radio" name="ts_status" value="On Hold" class="req_field">On Hold
							</div>
							<div class="grid_cell width30P left10">
								<b>Revision Description</b> <br>
								<input type="text" name="ts_revision_description" id="ts_revision_description_id" class="req_field">
							</div>
							<div class="clear"></div>
						</div>
						
						<div class="grid_row light_gray_bcg noborderbottom">
							<div class="grid_cell width30P left15 right10" style="height:120px;">
								<b>Version Test Suite</b> <br>
								<input type="text" name="ts_version" id="" class="req_field"> <br /><br />
								<b>Set Image</b> <br />
								<div id="file_ts" class="btn button button_small normal green_bcg white_txt radius3 width50P">Upload Photo</div>
								<input type="file" name="upload_attachment[]" class="req_field"/>
								<div class="clear"></div>
							</div>
							<div class="grid_cell width625P left10">
								<b>Description</b> <br>
								<textarea name="ts_description"> </textarea>
							</div>
							<div class="clear"></div>
						</div>
					</div>
				</div>
				<div class="space20"></div>
				
				<div class="default_grid">
					<div class="grid_head blue_head">
						<div class="grid_row">
							<div class="grid_cell grid_button grid_button_left expandable"><a href="#"><span class="simple_tooltip radius6">Expand<span></span></span></a></div>
							<div class="grid_cell width60P"><h5>Initiating Message</h5></div>
							<div class="clear"></div>
						</div>
					</div>
					<div class="grid_body">
						<div class="grid_row light_gray_bcg noborderbottom">
							<div class="grid_cell width60P left15 right10">
								<b>Name</b> <br>
								<textarea name="init_message"> </textarea>
							</div>
							<div class="grid_cell width35P">
								<p class="desc_field">Type Initiating Messages (comma separated)</p>
							</div>
							<div class="clear"></div>
						</div>
					</div>
				</div>
				
				<div class="space20"></div>
				
				<div class="default_grid">
					<div class="grid_head blue_head">
						<div class="grid_row">
							<div class="grid_cell grid_button grid_button_left expandable"><a href="#"><span class="simple_tooltip radius6">Expand<span></span></span></a></div>
							<div class="grid_cell width60P"><h5>Conformance Levels</h5></div>
							<div class="clear"></div>
						</div>
					</div>
					<div class="grid_body">
						<div class="conformance_level"><div class="conformance_level">
							<div class="grid_row light_gray_bcg noborderbottom">
								<div class="grid_cell width30P left15 right10">
									<b>Comformance Level Code</b> <br>
									<input type="text" name="lvl_code[]" class="req_field">
								</div>
								<div class="grid_cell width55P left10">
									<b>Comformance Level Description</b> <br>
									<textarea name="lvl_desc[]" class="req_field"> </textarea>
								</div>
								<div class="grid_cell width10P">
									<a class="remove_lvl delete_icon"></a>
								</div>
								<div class="clear"></div>
								<div class="grey-border-bottom"></div>
							</div>
						</div></div>
						
						<div class="copy-correct-lvl">
						
						</div>
						
						<div class="grid_row light_gray_bcg noborderbottom">
							<div class="grid_cell width25P left10 bottom8">
								<a class="add_new_lvl button button_small normal green_bcg white_txt right radius3 width100P">
									<span class="sign"><img src="<?php echo bloginfo('stylesheet_directory'); ?>/images/add_new_sign.png"></span>
									New Conformance Level
								</a>
							</div>
							<div class="clear"></div>
						</div>		
					</div>
				</div>
			
				<div class="space20"></div>
				
				<div class="default_grid">
					<div class="grid_head blue_head">
						<div class="grid_row">
							<div class="grid_cell grid_button grid_button_left expandable"><a href="#"><span class="simple_tooltip radius6">Expand<span></span></span></a></div>
							<div class="grid_cell width60P"><h5>Test Cases</h5></div>
							<div class="clear"></div>
						</div>
					</div>
					<div class="grid_body" style="display: block; ">
						<div class="test_cases_associated"><div class="test_cases_associated">
							<div class="grid_row light_gray_bcg noborderbottom">
								<div class="grid_cell width30P left15">
									<div class="styled_select_dashboard left">
										<select name="test_cases[]" class="req_field">
										  <option value="">Select Test Cases</option>
										  <?php
										  $loop = new WP_Query( array( 'post_type' => 'test-case', 'posts_per_page' => -1) );
										  while ( $loop->have_posts() ) : $loop->the_post();
										  ?>
										  <option <?php if (get_the_ID() == $test_cases) { echo 'selected="selected"'; }; ?> value="<?php the_ID(); ?>" style="margin-right: 5px; margin-bottom: 5px;"><?php the_title(); ?> <br />
										  <?php
										  endwhile;
										  ?>
										</select>
									</div>
								</div>
								<div class="grid_cell width10P"><a class="remove_test_case delete_icon nomargintop"></a></div>
								<div class="clear"></div>
							</div>
						</div></div>
						
						<div class="copy-correct-test-cases">
						
						</div>
						
						<div class="grid_row light_gray_bcg noborderbottom">
							<div class="grid_cell width20P left15 bottom8">
								<a href="#" class="add_new_test_case button button_small normal green_bcg white_txt right radius3 width100P">
									<span class="sign"><img src="<?php echo bloginfo('stylesheet_directory'); ?>/images/add_new_sign.png"></span>
									New Test Case
								</a>
							</div>
							<div class="clear"></div>
						</div>
						
					</div>
				</div>
				
				<div class="space20"></div>
				
				<div class="default_grid">
					<div class="grid_head blue_head">
						<div class="grid_row">
							<div class="grid_cell grid_button grid_button_left expandable"><a href="#"><span class="simple_tooltip radius6">Expand<span></span></span></a></div>
							<div class="grid_cell width60P"><h5>Related Test Suites</h5></div>
							<div class="clear"></div>
						</div>
					</div>
					<div class="grid_body" style="display: block; ">
						<div class="test_suites_related"><div class="test_suites_related">
							<div class="grid_row light_gray_bcg noborderbottom">
								<div class="grid_cell width30P left15 right10">
									<b>Related Suite</b> <br>
									<div class="styled_select_dashboard left">
										<select name="ts[]" class="req_field">
											<option value="">Choose Related Suite</option>
											<?php
											$loop = new WP_Query( array( 'post_type' => 'test-suite', 'posts_per_page' => -1, 'post__not_in' =>array($post->ID) ) );
											while ( $loop->have_posts() ) : $loop->the_post();
												 ?>
												 <option <?php if (get_the_ID() == $post_sel) { echo 'selected="selected"'; }; ?> value="<?php the_ID(); ?>" style="margin-right: 5px; margin-bottom: 5px;"><?php the_title(); ?> <br />
												<?php
											endwhile;
											?>
										</select>
									</div>
								</div>
								<div class="grid_cell width55P left10 right10">
									<b>Related Suite Description:</b> <br>
									<textarea name="ts_desc[]" class="req_field"></textarea>
								</div>
								<div class="grid_cell width5P"><a class="remove_test_suite delete_icon"></a></div>
								<div class="clear"></div>
								<div class="grey-border-bottom"></div>
							</div>
						</div></div>	
						
						<div class="copy-correct-test-suites">
						
						</div>
						
						<div class="grid_row light_gray_bcg noborderbottom">
							<div class="grid_cell width25P left15 bottom8">
								<a class="add_new_test_suite button button_small normal green_bcg white_txt right radius3 width100P">
									<span class="sign"><img src="<?php echo bloginfo('stylesheet_directory'); ?>/images/add_new_sign.png"></span>
									New Related Test Suite
								</a>
							</div>
							<div class="clear"></div>
						</div>
						
					</div>
				</div>
				
				<div class="space20"></div>
				
				<div class="default_grid">
					<div class="grid_head blue_head">
						<div class="grid_row">
							<div class="grid_cell grid_button grid_button_left expandable"><a href="#"><span class="simple_tooltip radius6">Expand<span></span></span></a></div>
							<div class="grid_cell width60P"><h5>Roles</h5></div>
							<div class="clear"></div>
						</div>
					</div>
					<div class="grid_body" style="display: block; ">
						<div class="grid_row light_gray_bcg noborderbottom">
							<div class="grid_cell  width55P left15 right10">
								<b>Tester Roles</b> <br>
								<textarea name="tester_role_ts" class="req_field"></textarea>
							</div>
							<div class="grid_cell width40P">
								<p class="desc_field">Tester Roles (comma separated)</p>
							</div>
							<div class="clear"></div>
						</div>
						<div class="grid_row light_gray_bcg noborderbottom">
							<div class="grid_cell  width35P left15 right10">
								<b>Subscription Price</b> <br>
								<input type="text" name="sub_price_tr" />
							</div>
							<div class="clear"></div>
							<div class="grey-border-bottom"></div>
						</div>
						<div class="grid_row light_gray_bcg noborderbottom">
							<div class="grid_cell  width55P left15 right10">
								<b>Harness Roles</b> <br>
								<textarea name="harness_role_ts" class="req_field"></textarea> 
							</div>
							<div class="grid_cell width40P">
								<p class="desc_field">Harness Roles (comma separated)</p>
							</div>
							<div class="clear"></div>
						</div>
						<div class="grid_row light_gray_bcg noborderbottom">
							<div class="grid_cell  width35P left15 right10">
								<b>Subscription Price</b> <br>
								<input type="text" name="sub_price_hr" />
							</div>
							<div class="clear"></div>
							<div class="grey-border-bottom"></div>
						</div>
						<div class="grid_row light_gray_bcg noborderbottom">
							<div class="grid_cell  width55P left15 right10">
								<b>Initiators</b> <br>
								<textarea name="initiator_ts" class="req_field"></textarea>
							</div>
							<div class="grid_cell width40P">
								<p class="desc_field">Initiatos (comma separated)</p>
							</div>
							<div class="clear"></div>
						</div>		
						<div class="grid_row light_gray_bcg noborderbottom">
							<div class="grid_cell  width35P left15 right10">
								<b>Subscription Price</b> <br>
								<input type="text" name="sub_price_in" />
							</div>
							<div class="clear"></div>
						</div>			
					</div>
				</div>
				
				<div class="space20"></div>
				
				<div class="default_grid">
					<div class="grid_head blue_head">
						<div class="grid_row">
							<div class="grid_cell grid_button grid_button_left expandable"><a href="#"><span class="simple_tooltip radius6">Expand<span></span></span></a></div>
							<div class="grid_cell width60P"><h5>Specification Documents</h5></div>
							<div class="clear"></div>
						</div>
					</div>
					<div class="grid_body" style="display: block; ">
						<div class="documents_associated"><div class="documents_associated">
							<div class="grid_row light_gray_bcg noborderbottom">
								<div class="grid_cell width60P left15 right10">
									<div class="grid_cell width55P">
										<b>Specification Type</b> <br>
										<div class="styled_select_dashboard left">
											<select name="doc_type[]" class="req_field">
											  <option value="">Choose one</option>
											  <option value="1">Type 1</option>
											  <option value="2">Type 2</option>
											</select>
										</div>
									</div>
									<div class="grid_cell width45P">
										<b>Document Name</b> <br>
										<input type="text" name="doc_name[]" class="req_field">
									</div>
									<div class="clear"></div>
									<div class="grid_cell width100P">
										<b>Document Location:</b><br>
										<input type="text" name="doc_loc[]" class="req_field">
									</div>
									
								</div>
								<div class="grid_cell width30P left10">
									<b>Document Description</b>
									<textarea name="doc_desc[]" class="req_field"></textarea>
								</div>
								<div class="grid_cell width5P">
									<a class="remove_document delete_icon"></a>
								</div>
								<div class="clear"></div>
								<div class="grey-border-bottom"></div>
							</div>
						</div></div>
						
						<div class="copy-correct-documents">
						
						</div>
						
						<div class="grid_row light_gray_bcg noborderbottom">
							<div class="grid_cell width20P left15 bottom8">
								<a class="add_new_document button button_small normal green_bcg white_txt right radius3 width100P">
									<span class="sign"><img src="<?php echo bloginfo('stylesheet_directory'); ?>/images/add_new_sign.png"></span>
									New Document
								</a>
							</div>
							<div class="clear"></div>
						</div>
					</div>
				</div>
				<div class="clear"></div>
				<div class="space25"></div>
				<div class="err_new_suite">Please fill in all fields!</div>
				<div class="clear"></div>
				<div class="Space25"></div>
				<div class="grid_cell width60P right10"></div>
				<div class="grid_cell width10P left10 right10">
					<a onclick="history.go(-1);" class="button button_small normal gray_bcg dark_gray_txt right radius3 width100P">
						<span class="sign"><img src="<?php echo bloginfo('stylesheet_directory'); ?>/images/cancel_icon.png"></span>
						Cancel
					</a>
				</div>
				<div class="grid_cell width25P left5">
					<input type="submit" name="save_ts" id="save_ts" value="SAVE TEST SUITE">
				</div>
				
			</div>
		</form>
		<?php
		//End form
		}
		else {
	?>
	
	<div class="default_grid">
		<div class="grid_cell width20P nopaddingleft">
			<form name="new_test_suite" action="" method="post">
				<input type="submit" id="new_test_suite" name="new_ts" value="Add New Suite" />
			</form>
		</div>
	</div>
	<?php
	}
	?>
				
	</div>
	<div class="clear space25"></div>
			
</div> <!--end content-->

<script type="text/javascript">
	var wrapper = $('<div/>').css({height:0,width:0,'overflow':'hidden'});
	var fileInput = jQuery(':file').wrap(wrapper);

	fileInput.change(function(){
		$this = $(this);
		jQuery('#file_ts').text("File attached");
	})
	 
	jQuery('#file_ts').click(function(){
		fileInput.click();
	}).show(); 
</script>
<?php
get_footer();
?>
