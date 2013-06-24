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
            <div class="page-title-block">
		        <div class="grid_head column">
			        <div class="grid_row nopadding">
				        <h4>Product / Service Detalils</h4>
			        </div>
		        </div>
		        <div class="column nopaddingtop">
			        <div class="nopadding">
                    <?php if (has_post_thumbnail()) { ?>
				        <div class="grid_cell width10P">
					        
					        <?php echo	the_post_thumbnail('post-thumb', array('class' => 'prod_serv_details')); ?>
					        
				        </div>
                        <?php } ?>
				        <div class="grid_cell <?php echo has_post_thumbnail() ? 'width90P' : 'width100P'?>">
					        <div class="width80P grid_cell suite_view">
						        <p><span class="normal">Product: </span><?php echo the_title(); ?></p>
					        </div>
					        <div class="width20P grid_cell nopadding">
                            <?php if(is_admin() || is_super_admin()){ ?>
                                <div class="edit_suite"><a href="/wp-admin/post.php?post=<?php  echo $post->ID; ?>&action=edit">EDIT</a></div>
						        <!--<div class="edit_suite"><a href="<?php echo esc_url( get_permalink( get_page_by_title( 'Create / Edit Product or Service' ) ) ) . '?product_id=' .$post->ID; ?>">EDIT</a></div>-->
                            <?php } ?>
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
				        
                        <div class="tabs-contr">
                            <ul class="tab-nav">
                                <li class="active">
                                    <a href="javascript: void(0)" rel="tabs_sv1">Related Products</a>
                                </li>
                                
                            </ul>
                            <div class="tab-content white_bcg" id="tabs_sv1" style="display: block; ">
                                <div class="column">                                        
                                    <div class="grid_cell width10P bold top3">Replaces: </div>
                                    <div class="grid_cell width90P">
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
                            </div> <!--end tab 1-->
                            <div class="clear"></div>
                        </div>
                
				        <!--end tabs-->
				        <div class="space25"></div>
				        <div class="clear"></div>
			        
			        </div>
			        
			        
		        </div>
            </div>
					
			<div class="grid_row test_cases">
					<div class="grid_cell width45P">
						<h5 class="blue_txt">Certifications</h5>
					</div>
					<div class="grid_cell width30P right selecteds_single">
						<!--<span class="left padding5-10">Filter By: </span>
						<div class="styled_select left width40P">
							<label>
							<select name="sort_status" class="sort_status">
							  <option value="select_status">Status</option>
							  <option value="active" <?php if($_GET['sort_status']=='active'){ echo 'selected="selected"';} ?> >Active</option>
							  <option value="on_hold" <?php if($_GET['sort_status']=='on_hold'){ echo 'selected="selected"';} ?> >On Hold</option>
							</select>
							</label>
						</div>-->
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
						$ts_assoc = _get_certified_test_suites(get_the_ID()) ; 
                        
						
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
									
									?>                                    
									<div class="grid_cell width10P">
                                        <?php                             
                                            if($status_ts == 'Active')
                                                echo '<span class="status_btn status_btn_active">ACTIVE</span>';
                                            else if($status_ts == 'On Hold')
                                                echo '<span class="status_btn status_btn_on_hold">ON HOLD</span>';
                                            ?>
                                    </div>
									<?php 
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
