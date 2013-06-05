<?php
/*
Template Name Posts: Test Suite
*/
?>

<?php
get_header();
?>
	<?php
	$ts_id = get_the_ID();
	$ts_result = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "bp_groups_testsuites WHERE ts_ids={$ts_id}");
	$current_group_id = $ts_result -> group_id;
	
	global $bp;
	$group = groups_get_group( array( 'group_id' => $current_group_id ) );
	$group_url = home_url( $bp->groups->slug . '/' . $group -> slug );
	?>
	
	<div class="space25"></div>
	<div class="content container">
		<div class="infos">
				<div class="grid_cell width10P">
					<?php if (has_post_thumbnail()) {
						the_post_thumbnail('post-thumb', array('class' => 'sbr'));
					} ?>
				</div> 
				<div class="grid_cell width90P">
					<h3 class="dark_gray_txt bold"><?php the_title(); ?> <?php // or echo meta ('ts_name'); ?></h3>	
					<a href="<?php echo get_permalink( $post->ID ); ?>" class="bold blue_txt nodecoration"><?php echo get_permalink( $post->ID ); ?> <?php  // or echo meta ('ts_identifier') ;?></a>
					<div class="space15"></div>
					
					<div class="grids noradiusbottom">
						<div class="grid_row white_bcg noborderbottom">
							<div class="grid_cell width100P left">
								<p>Version: <span><?php echo get_post_meta(get_the_ID(), 'ts_version', true); ?></span>
								Issue Date: <span><?php echo get_post_meta(get_the_ID(), 'ts_issue_date', true); ?></span>
								Issuer: <a href="<?php echo $group_url; ?>"><span class="blue_txt"><?php echo get_post_meta(get_the_ID(), 'ts_issuer', true); ?></span></a>
								Status: <span class="green_txt"><?php echo get_post_meta(get_the_ID(), 'ts_status', true); ?></span> 
								Revision: <span><?php echo get_post_meta(get_the_ID(), 'ts_revision_description', true); ?></span> 
								</p>
							</div>
							<div class="clear"></div>
						</div>
					</div>
					<div class="space15"></div>
					
					<div class="grids noborder nobackground">
					<p class="size13"><?php echo get_post_meta(get_the_ID(), 'ts_description', true); ?> </p>
					</div>
				</div>
				
				<div class="clear"></div>
						
		</div> <!--end infos-->
		<div class="clear"></div>
		
		
		<div class="column nopaddingbottom">
							<!-- tabs -->
							<div class="tabs_wrap lighter_gray_bcg">
								<ul class="tabs_sv">
									<li class="active">
										<a href="javascript: void(0)" rel="tabs_sv1" class="defaulttab selected">
											<span class="left icon" id="icon_test_suites"></span>
											<span class="right text">Related Compliance Suites</span>
											<span class="clear"></span>
										</a>
									</li>
									<li class="">
										<a href="javascript: void(0)" rel="tabs_sv2" class="">
											<span class="left icon" id="icon_wiki"></span>
											<span class="right text">Specification Documents &amp; Materials</span>
											<span class="clear"></span>
										</a>
									</li>
									<li class="">
										<a href="javascript: void(0)" rel="tabs_sv3" class="">
											<span class="left icon" id="icon_wiki"></span>
											<span class="right text">Comformance Levels</span>
											<span class="clear"></span>
										</a>
									</li>
								</ul>
								
								<div class="clear"></div>
								
								<div class="tab-content white_bcg" id="tabs_sv1" style="display: block; ">
									<div class="column">										
										<div class="grid_cell width10P bold top3">Related To: </div>
										<div class="grid_cell width90P">
										<?php 
										
											$ts_sel_array = get_post_meta(get_the_ID(), 'ts', true) ; 
											$ts_desc_array = get_post_meta(get_the_ID(), 'ts_desc', true);
											foreach($ts_sel_array as $key => $ts_sel){
												foreach ($ts_desc_array as $key2 => $ts_desc){
													if($key == $key2) {
														echo '<p class="underline blue_txt">';
														echo get_the_title($ts_sel);
														echo '</p>';
														echo $ts_desc;
														echo '<div class="space7"></div>';
														}
													}
											} ?>
										</div>
										<div class="clear"></div>
									</div>
									<div class="clear"></div>
								</div> <!--end tab 1-->
								
								<div class="tab-content white_bcg" id="tabs_sv2" style="display: none; ">
									<div class="column">
										<?php 
										$the_post_id= get_the_ID();
										$myrows = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "ts_options_documents WHERE ts_id={$the_post_id}");
										foreach($myrows as $row){
											$doc_name = $row->doc_name;
											$doc_desc = $row->doc_desc;
											$doc_loc = $row->doc_loc_url;
											$doc_file_name = $row->doc_file_name;
											$doc_file_url = $row->doc_loc_url;
											echo '<div class="grid_cell width100P">';
											echo '<a href="'.$doc_loc.'" target="_blank" class="underline blue_txt file">';
											echo $doc_name;
											echo '</a><p class="paddingleft20">'.$doc_desc.'</p>';
											echo '</div><div class="clear"></div>';
										}
											//$doc_type_array = get_post_meta(get_the_ID(), 'doc_type', true); 
											$doc_name_array = get_post_meta(get_the_ID(), 'doc_name', true); 
											$doc_loc_array = get_post_meta(get_the_ID(), 'doc_loc', true); 
											$doc_desc_array = get_post_meta(get_the_ID(), 'doc_desc', true); 
										?>
									</div>
								</div> <!--end tab 2-->
								
								<div class="tab-content white_bcg" id="tabs_sv3" style="display: none; ">
									<div class="column padding15-20">
										
										<?php
										$lvl_code_array = get_post_meta(get_the_ID(), 'lvl_code', true); 
										$lvl_desc_array = get_post_meta(get_the_ID(), 'lvl_desc', true); 
										
										foreach($lvl_code_array as $key => $lvl_code){
											foreach ($lvl_desc_array as $key2 => $lvl_desc){
														if( $key == $key2 ){ ?>
														<div class="grid_cell width10P bold blue_txt size26px top5 <?php if ($key == ((count($lvl_code_array)) -1 )) { echo 'top0bottom5';} ?>"><?php echo $lvl_code; ?></div>
														<div class="grid_cell width90P">
															<p><?php echo $lvl_desc; ?></p>
														</div>
														<div class="clear"></div> 
														<div class="grey-border-bottom <?php if ($key == ((count($lvl_code_array)) -1 )) { echo 'displaynone';} ?>"></div>																	
												<?php	}
													}
												}
										?>
									</div>
									<div class="clear"></div>
								</div><!--end tab 3-->
								
							</div>
							<!--end tabs-->
							
							<div class="grid_cell width50P">
								<div class="blue_button "><a href="#" class="view_compliant">View Compliant Products</a></div>
							</div>
							<div class="grid_cell width50P">
								<div id="three_boxes_sv"><a class="payment_popup" target="_blank">
									<div id="box_1_red" class="left">
									</div>
									
									<div id="box_2_blue" class="column third left">
										<h4>$200</h4>
										<p>per mounth</p>
									<img src="<?php echo bloginfo('stylesheet_directory'); ?>/images/blue_box_left.png" id="box_shadow_blue_left">
									<img src="<?php echo bloginfo('stylesheet_directory'); ?>/images/blue_box_right.png" id="box_shadow_blue_right">
									</div>
									
									<div id="box_3_red" class="left">
										<p class="subscribe">SUBSCRIBE</p>
										<p>to Test Hardness</p>
										
									</div>
									<div class="right-triangle right"></div>
									<div class="clear"></div>
									</a>
								</div>
							</div>
							<div class="clear"></div>
		</div> 
		
		<!-- Test Cases -->
		<div class="grid_row searchfilters">
		
		</div>
		<div class="clear"></div>
		<div class="grid_row test_cases">
							
				<div class="grid_head blue_grid special_grid_big">
					<div class="grid_row nopaddingbottom nopaddingtop tocenter testcases_grid special_grid_inner">
						<div class="grid_cell nopaddingtop width10P toleft single_line">Test Case ID</div>
						<div class="grid_cell nopaddingtop width5P toleft tocenter single_line">Version</div>
						<div class="grid_cell nopaddingtop width10P toleft tocenter single_line">Published</div>
						<div class="grid_cell nopaddingtop width10P toleft tocenter">Tester<br/>Role</div>
						<div class="grid_cell nopaddingtop width10P toleft tocenter">Harness<br/>Role(s)</div>
						<div class="grid_cell nopaddingtop width5P toleft tocenter single_line">Initiator</div>
						<div class="grid_cell nopaddingtop width5P toleft tocenter">Conf<br/>Level</div>
						<div class="grid_cell nopaddingtop width10P toleft tocenter">Outcome<br/>Type</div>
						<div class="grid_cell nopaddingtop width5P toleft tocenter">Test<br/>Pattern</div>
						<div class="grid_cell nopaddingtop width5P toleft tocenter single_line">Bulk</div>
						<div class="grid_cell nopaddingtop width10P toleft tocenter">Initiating<br/>Message</div>
						<div class="grid_cell nopaddingtop width15P toleft single_line">Test Intent Description</div>
						<div class="clear"></div>	
					</div>
				</div>
				
				
				<div class="clear"></div>
							
				<div class="grids">
							<!-- Append Select Filters
							-->
							<div class="append_filter">
							</div>
							<?php 
							/*global $wpdb;
							$thepostid = get_the_ID();
							$results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}postmeta WHERE meta_key='test_suites' AND meta_value LIKE '%{$thepostid}%'");
							foreach($results as $res){			
								}*/
							$thepostid = get_the_ID();
							$loop = new WP_Query( array( 'post_type' => 'test-case', 'posts_per_page' => -1) );
							$found = false;
							$all_tester_roles = array();
							$all_conf_lvl = array();
							while ( $loop->have_posts() ) : $loop->the_post();
								$id = get_the_ID();
								$test_cases_assoc = get_post_meta($id, 'test_suites', true);
								if (in_array($thepostid, $test_cases_assoc)){
									$perma = get_permalink( $id );
									$tc_id = get_post_meta($id ,'test_case_id', true); 
									$tc_version = get_post_meta($id ,'version', true); 
									$tc_published = get_post_meta($id ,'published', true); 
									$tc_tester_role = get_post_meta($id ,'choose_tester_role', true); 
									/* Push all Tester Roles for filter
									 * */
									if (!in_array($tc_tester_role,$all_tester_roles)){
										array_push($all_tester_roles, $tc_tester_role);
									}
									$tc_harness_role = get_post_meta($id ,'choose_harness_role', true); 
									$tc_initiator = get_post_meta($id ,'choose_initiator', true); 
									$tc_conformance_level = get_post_meta($id ,'conformance_level', true);
									/* Push all Conformance Levels for filter
									* */
									if (!in_array($tc_conformance_level,$all_conf_lvl)){
										array_push($all_conf_lvl, $tc_conformance_level);
									}
									$tc_outcome_type = get_post_meta($id ,'outcome_type', true); 
									$tc_message_count = get_post_meta($id ,'message_count', true); 
									$bulk = get_post_meta($id ,'bulk', true); 
									$initiating_message = get_post_meta($id ,'choose_init_messages', true); 
									$test_intent_description = get_post_meta($id ,'test_intent_description', true); 
									$found = true;
									echo '<div class="grid_row white_bcg tocenter testcase_line ">';
									/*echo '<a href="'.get_permalink().'" target="_blank"><b>'.get_the_title().'</b></a>';*/
									
									if ( is_user_logged_in() ) {
										echo '<div class="grid_cell nopaddingtop width10P toleft" ><a href="'. $perma.'">'.get_the_title().'</a></div>';
										}
										else echo '<div class="grid_cell nopaddingtop width10P toleft" >'.get_the_title().'</div>';
										
									echo '<div class="grid_cell nopaddingtop width5P toleft tocenter ">'.$tc_version.'</div>
										<div class="grid_cell nopaddingtop width10P toleft tocenter ">'.$tc_published.'</div>
										<div class="grid_cell nopaddingtop width10P toleft tocenter">'.$tc_tester_role.'</div>
										<div class="grid_cell nopaddingtop width10P toleft tocenter">'.$tc_harness_role.'</div>
										<div class="grid_cell nopaddingtop width5P toleft tocenter ">'.$tc_initiator.'</div>
										<div class="grid_cell nopaddingtop width5P toleft tocenter">'.$tc_conformance_level.'</div>
										<div class="grid_cell nopaddingtop width10P toleft tocenter">'.$tc_outcome_type.'</div>
										<div class="grid_cell nopaddingtop width5P toleft tocenter">'.$tc_message_count.'</div>
										<div class="grid_cell nopaddingtop width5P toleft tocenter ">'.$bulk.'</div>
										<div class="grid_cell nopaddingtop width10P toleft tocenter">'.$initiating_message.'</div>
										<div class="grid_cell nopaddingtop width15P toleft ">'.$test_intent_description.'</div>
							        <div class="clear"></div>';
									echo '</div>';
									
								}
							endwhile;
						//	print_r($all_tester_roles);
						//	print_r($all_conf_lvl);
							if (!$found){
								echo 'No test cases associated';
							}
							?>				
					</div>
					
		</div>
		<div class="the_filters">
						<div class="grid_row">
							<div class="grid_cell width45P">
										<h5 class="blue_txt">Test Cases</h5>
							</div>
							<div class="grid_cell width55P right selecteds">
								<span class="left padding5-10">Filter By: </span>
								<div class="styled_select left">
									<select>
									  <option value="">Tester Role</option>
									  <?php 
									  foreach($all_tester_roles as $single_tester){
										  $option_val_tester =  str_replace(' ','_',$single_tester); 
										  echo '<option value="'.$option_val_tester.'">'.$single_tester.'</option>';
									  }
									  ?>
									</select>
								</div>
								<div class="styled_select left">
									<select name="test">
									  <option value="">Conformance Level</option>
									  <?php 
									  foreach($all_conf_lvl as $single_conf_lvl){
										  $option_val_lvl =  str_replace(' ','_',$single_conf_lvl); 
										  echo '<option value="'.$option_val_lvl.'">'.$single_conf_lvl.'</option>';
									  }
									  ?>
									</select>
								</div>
								<div class="clear"></div>
							</div>
							<div class="clear"></div>
							<div id="double_border"></div>
						</div>
	</div>
		
		
	</div> <!--end content container-->
	
</div>
<div class="space45"></div>
<div class="clear"></div>
</div>
<?php
get_footer();
?>
