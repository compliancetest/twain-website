<?php
/*
Template Name Posts: Test Case
*/
?>

<?php
get_header();
?>

	<div class="content container">
		<div class="infos">
				<h3 class="dark_gray_txt normal">Test case ID: <span class="dark_blue_txt bold"><?php echo meta ('test_case_id') ; ?></span></h3>
				<p class="dark_gray_txt"><?php echo meta ('test_intent_description') ; ?></p>
				<div class="grids noradiusbottom">
					<div class="grid_row white_bcg noborderbottom">
						<div class="grid_cell width10P left size13 bold dark_blue_txt">Info:</div>
						<div class="grid_cell width30P left">
							<p>Version: <span><?php meta ('version'); ?></span></p>
						</div>
						<div class="grid_cell width30P left">
							<p>Published: <span>
								<?php 
										$date=get_post_meta($post->ID, 'published', true); 
										echo date("j-M-Y", strtotime($date)); // format Nov 2012
									?>
								</span></p>
						</div>
						<div class="grid_cell width30P left">
							<p>Initiating Messsage: <span><?php meta ('choose_init_messages'); ?></span></p>
						</div>
						<div class="clear"></div>
					</div>
					
					<div class="grey-border-bottom width98P"></div>
										
					<div class="grid_row white_bcg noborderbottom">
						<div class="grid_cell width10P left size13 bold dark_blue_txt">Roles:</div>
						<div class="grid_cell width30P left"> 
							<p>Tester Role: <span><?php meta ('choose_tester_role'); ?></span></p>
						</div>
						<div class="grid_cell width30P left">
							<p>Harness Role: <span><?php meta ('choose_harness_role'); ?></span></p>
						</div>
						<div class="grid_cell width30P left">
							<p>Initiator: <span><?php meta ('choose_initiator'); ?></span></p>
						</div>
						<div class="clear"></div>
					</div>
					
					<div class="grey-border-bottom width98P"></div>
						
					<div class="grid_row white_bcg noborderbottom">
						<div class="grid_cell width10P left size13 bold dark_blue_txt">Properties:</div>
						<div class="grid_cell width30P left">
							<p>Conformance Level: <span><?php meta ('conformance_level'); ?></span></p>
						</div>
						<div class="grid_cell width30P left">
							<p>Outcome Type: <span><?php meta ('outcome_type'); ?></span></p>
						</div>
						<div class="grid_cell width15P left">
							<p>Test Pattern: <span><?php meta ('message_count'); ?></span></p>
						</div>
						<div class="grid_cell width15P left">	
							<p>Bulk: <span><?php meta ('bulk'); ?></span></p>
						</div>
						<div class="clear"></div>
					</div>
				</div>	
						
		</div> <!--end infos-->
		
		<div class="clear"></div>
		
		<div class="column">
			<div class="grid_cell width100P toleft"> 
							<div class="grid_head lighter_gray_bcg2 related">
								<div class="grid_row nopaddingbottom nopaddingtop ">
									<div class="grid_cell width100P size14 normal shadowwhite">Test Execution</div>
									<div class="clear"></div>
								</div>
							</div>
							<div class="grids noradiusbottom">
								<div class="grid_row white_bcg nopaddingbottom noborderbottom">
									<div class="grid_cell width15P left size13 bold">Test endpoint URL:</div>
									<div class="grid_cell width80P left"><a href="#" class="blue_txt"><?php echo meta ('test_url') ; ?></a></div>
									<div class="clear"></div>
								</div>
								
								<div class="grid_row white_bcg nopaddingbottom noborderbottom">
									<div class="grid_cell width15P left size13 bold">Protocol Binding:</div>
									<div class="grid_cell width80P left"><?php echo meta ('protocol_binding2') ; ?></div>
									<div class="clear"></div>
								</div>
								<?php
								$property_name_exec_array = get_post_meta (get_the_ID(), 'property_name_exec', true) ; 		
								$property_value_exec_array = get_post_meta (get_the_ID(), 'property_value_exec', true);
								
								foreach($property_name_exec_array as $key => $property_name_exec){
											foreach ($property_value_exec_array as $key2 => $property_value_exec){
												if($key == $key2) { ?>
												<div class="grid_row white_bcg nopaddingbottom noborderbottom <?php if($key == (count($property_name_exec_array)-1)) {echo "paddingbottom10";} ?>">
													<div class="grid_cell width15P left size13 bold"><?php  echo $property_name_exec.':';?></div>
													<div class="grid_cell width80P left"><a href="<?php echo $property_value_exec; ?>" class="blue_txt"><?php echo $property_value_exec; ?></a></div>
													<div class="clear"></div>
												</div>	
										<?php	}
											}
								} ?>
							</div>
						
			</div>
			
			<div class="clear"></div>
			<div class="space7"></div>
			<div class="grid_cell width100P toleft"> 
							<div class="grid_head lighter_gray_bcg2 related">
								<div class="grid_row nopaddingbottom nopaddingtop">
									<div class="grid_cell width100P size14 normal shadowwhite">Test Data</div>
									<div class="clear"></div>
								</div>
							</div>
							<div class="grids noradiusbottom">
								<?php
								$property_name_data_array = get_post_meta (get_the_ID(), 'property_name_data', true) ; 		
								$property_value_data_array = get_post_meta (get_the_ID(), 'property_value_data', true);
								foreach($property_name_data_array as $key => $property_name_data){
											foreach ($property_value_data_array as $key2 => $property_value_data){
												if($key == $key2) { ?>
												<div class="grid_row white_bcg nopaddingbottom noborderbottom <?php if($key == (count($property_name_data_array)-1)) {echo "paddingbottom10";} ?>">
													<div class="grid_cell width15P left size13 bold"><?php  echo $property_name_data.':';?></div>
													<div class="grid_cell width80P left"><a href="<?php echo $property_value_data; ?>" class="blue_txt"><?php echo $property_value_data; ?></a></div>
													<div class="clear"></div>
												</div>	
										<?php	}
											}
								} ?>
 
							</div>
			</div>
			<div class="clear"></div>
			<div class="space15"></div>
			<!-- Steps -->
			
			<div class="grid_cell width100P toleft"> 
							<div class="grid_head blue_bcg2 related">
								<div class="grid_row nopaddingbottom nopaddingtop">
									<div class="grid_cell width100P size14 normal white_txt shadowblue">Test Steps</div>
									<div class="clear"></div>
								</div>
							</div>
							<div class="grid_head">
								<div class="grid_row padding5-10">
									<div class="grid_cell width10P tocenter">Steps</div>
									<div class="grid_cell width35P left5P">Action</div>
									<div class="grid_cell width35P left5P">Expected Result</div>
									<div class="clear"></div>
								</div>
							</div>
							<div class="grids">
								<?php
								$step_action_array = get_post_meta (get_the_ID(), 'step_action', true) ; 		
								$step_expected_array = get_post_meta (get_the_ID(), 'step_expected', true);
								foreach($step_action_array as $key => $step_action){
									foreach ($step_expected_array as $key2 => $step_expected){
										if($key == $key2) { ?>
										<div class="grid_row white_bcg padding5-10">
											<div class="grid_cell width10P tocenter"><?php echo ($key+1); ?></div>
											<div class="grid_cell width35P left5P"><?php echo $step_action; ?></div>
											<div class="grid_cell width35P left5P"><?php echo $step_expected; ?></div>
											<div class="clear"></div>
										</div>	
								<?php	}
									}
								} ?>
							</div>
						
				</div>
			
		</div><!--end column-->
		
		<div class="clear"></div>
		<div class="space15"></div>
	</div> <!--end content container-->

<?php
get_footer();
?>
