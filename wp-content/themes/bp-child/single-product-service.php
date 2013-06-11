<?php
/*
Template Name Posts: Product / Service
*/
?>

<?php
get_header();
?>

	<div class="content container">
<div class="grid  dark_gray_txt">
					<div class="grid_head column">
						<div class="grid_row nopadding">
							<h4>Product / Service Detalils</h4>
						</div>
					</div>
					<div class="column nopaddingtop">
						<div class="grid_row nopadding">
							<div class="grid_cell width10P">
								<?php if (has_post_thumbnail()) {
									the_post_thumbnail('post-thumb', array('class' => 'prod_serv_details'));
								} ?>
							</div>
							<div class="grid_cell width90P">
								<div class="width80P grid_cell suite_view">
									<p><span class="normal">Product: </span><?php echo the_title(); ?></p>
								</div>
								<div class="width20P grid_cell nopadding">
									<div class="edit_suite"><a href="<?php echo esc_url( get_permalink( get_page_by_title( 'Create / Edit Product or Service' ) ) ) . '?product_id=' .$post->ID; ?>">EDIT</a></div>
								</div>
								<div class="clear"></div>
								<div class="grey-border-bottom"></div>
								<div class="grid_cell width50P product_datails">
									<p>Date: <span class="bold">
									<?php 
										$date=get_post_meta($post->ID, 'product_date', true); 
										echo date("M Y", strtotime($date)); // format Nov 2012
									?>
									</span></p>
									<p>Type: <span class="bold"><?php echo meta ('product_type'); ?></span></p>
									<p>Compliance Group: <a href="#" class="bold"><?php echo meta ('product_owner'); ?></a></p>
									<p>Access URL: <a href="#" class="bold"><?php echo meta ('product_url'); ?></a></p>
								</div>
								<div class="grid_cell width50P">
									<p><?php echo meta ('product_description'); ?></p>
								</div>
								<div class="clear"></div>
							</div>
							<div class="clear"></div>
							
							<div class="space20"></div>
							
							<div class="tabs_wrap lighter_gray_bcg">
								<ul class="tabs_sv">
									<li class="active">
										<a href="javascript: void(0)" rel="tabs1" class="defaulttab selected">
											<span class="left icon" id="icon_test_suites"></span>
											<span class="right text">Related Products</span>
											<span class="clear"></span>
										</a>
									</li>
								</ul>
								<div class="clear"></div>
								
								<div class="tab-content2 white_bcg" id="tabs1" style="display: block; ">
									<div class="column">
										<div class="grid_cell width10P">
											<p><span class="bold">Replaces: </span> </p>
										</div>
										<div class="grid_cell width60P">
											<?php 
												/* Get Related Products / Services */
												$rp_ids = get_post_custom_values ('related_products') ; 
												$rp_assoc = array();
												$rp_assoc = explode(',', $rp_ids[0]);
												foreach ($rp_assoc as $key => $rp){
													$permalink_rp = get_permalink( $rp ); 
													$title_rp = get_the_title( $rp );
													echo '<a href="'.$permalink_rp.'">'.$title_rp.'</a>';
													echo '<div class="clear"></div> <div class="space5"></div>';
												}
											 ?>											

										</div>
										<div class="clear"></div>
									</div>
									<div class="clear"></div>
								</div><!--end tab 1-->
							</div>
							<!--end tabs-->
							<div class="space25"></div>
							<div class="clear"></div>
						
						</div>
						
						
					</div>
					
					<div class="grid_row test_cases">
							<div class="grid_cell width45P">
								<h5 class="blue_txt">Certifications</h5>
							</div>
							<div class="grid_cell width30P right selecteds_single">
								<span class="left padding5-10">Filter By: </span>
								<div class="styled_select left width40P">
									<label>
									<select name="sort_status" class="sort_status">
									  <option value="select_status">Status</option>
									  <option value="active" <?php if($_GET['sort_status']=='active'){ echo 'selected="selected"';} ?> >Active</option>
									  <option value="on_hold" <?php if($_GET['sort_status']=='on_hold'){ echo 'selected="selected"';} ?> >On Hold</option>
									</select>
									</label>
								</div>
								<div class="clear"></div>
							</div>
							<div class="clear"></div>
							<div id="double_border"></div>
							<div class="grid_head">
								<div class="grid_row nopaddingbottom nopaddingtop tocenter">
									<div class="grid_cell nopaddingtop width30P toleft">Test Suite</div>
									<div class="grid_cell nopaddingtop width15P">Role</div>
									<div class="grid_cell nopaddingtop width15P">Level</div>
									<div class="grid_cell nopaddingtop width10P">Status</div>
									<div class="grid_cell nopaddingtop width25P toleft left5P">Date</div>
										<div class="clear"></div>
								</div>
							</div>
							<div class="grids">
							<?php 
								/* Get Test Suites */
								$ts_ids = get_post_custom_values ('test_suites') ; 
								$ts_assoc = array();
								$ts_assoc = explode(',', $ts_ids[0]);
								foreach ($ts_assoc as $key => $ts){
									$status_ts = get_post_meta($ts ,'ts_status', true); 
									if ( (isset($_GET['sort_status'])) && (($_GET['sort_status']) !=='select_status') ){ 
										/* Sort Columns */

										if( (strtoupper($status_ts)) == (str_replace("_", " ", strtoupper($_GET['sort_status']))) ) {	
											if ($key==(count($ts_assoc)-1)) $class_grid = 'last_grid_cell';
											else $class_grid = '';
											echo '<div class="grid_row white_bcg tocenter '.$class_grid.'">';
											$permalink_ts = get_permalink( $ts ); 
											$title_ts = get_the_title( $ts );
											$date_ts = get_post_meta($ts ,'ts_issue_date', true); 
											
											echo '<div class="grid_cell width30P toleft" ><a href="'. $permalink_ts.'" class="normal">'.$title_ts.'</a></div>
											<div class="grid_cell nopaddingtop width15P"> </div>
											<div class="grid_cell nopaddingtop width15P"> </div>';
											
											if($status_ts == 'Active') {
											?>
												<div class="grid_cell width10P"><a class="button green_bcg white_txt button_small radius3">ACTIVE</a></div>
											<?php } else { ?>
												<div class="grid_cell width10P"><a class="button orange_bcg white_txt button_small radius3">ON HOLD</a></div>
											<?php }
											echo '<div class="grid_cell nopaddingtop width25P toleft left5P">'.$date_ts.'</div>';
											echo '<div class="clear"></div></div>';
										}
										
										}
										else{
											if ($key==(count($ts_assoc)-1)) $class_grid = 'last_grid_cell';
											else $class_grid = '';
											echo '<div class="grid_row white_bcg tocenter '.$class_grid.'">';
											$permalink_ts = get_permalink( $ts ); 
											$title_ts = get_the_title( $ts );
											$date_ts = get_post_meta($ts ,'ts_issue_date', true); 
											
											echo '<div class="grid_cell width30P toleft" ><a href="'. $permalink_ts.'" class="normal">'.$title_ts.'</a></div>
											<div class="grid_cell nopaddingtop width15P"> </div>
											<div class="grid_cell nopaddingtop width15P"> </div>';
											
											if($status_ts == 'Active') {
											?>
												<div class="grid_cell width10P"><a class="button green_bcg white_txt button_small radius3">ACTIVE</a></div>
											<?php } else { ?>
												<div class="grid_cell width10P"><a class="button orange_bcg white_txt button_small radius3">ON HOLD</a></div>
											<?php }
											echo '<div class="grid_cell nopaddingtop width25P toleft left5P">'.$date_ts.'</div>';
											echo '<div class="clear"></div></div>';
											}
									
								}
							 ?>		
						</div>
						<div class="space15"></div>
						<!--end test_cases-->
				</div>
		
	</div> <!--end content container-->
	
</div>

<?php
get_footer();
?>
