<?php
/*
Template Name Posts: Product / Service
*/

get_header();

global $post;

$product = new ProductAndService(get_the_ID());
$product->load();

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
						        <p><span class="normal">Product: </span><?php echo $product->name; ?></p>
					        </div>
					        <div class="width20P grid_cell nopadding">
                            <?php if(can_edit_product_and_service(get_the_ID())){ ?>
                                <div class="edit_suite"><a href="/edit-product-and-service?id=<?php  echo $product->id; ?>">EDIT</a></div>						        
                            <?php } ?>
					        </div>
					        <div class="clear"></div>
					        <div class="grey-border-bottom"></div>
					        <div class="grid_cell width50P product_datails">
						        <p>Release Date: <span class="bold">
						        <?php 
							        echo date("M Y", strtotime($product->release_date)); // format Nov 2012
						        ?>
						        </span></p>
                                <p>Product Version: <span class="bold"><?php echo $product->version; ?></span></p>
						        <p>Type: <span class="bold"><?php echo $product->type; ?></span></p>
						        <p>Access URL: <a href="<?php echo $product->accessURL?>" class="bold"><?php echo $product->accessURL; ?></a></p>
					        </div>
					        <div class="grid_cell width50P">
						        <p><?php echo $product->descrition; ?></p>
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
                                    <?php foreach ($product->relatedProducts as $rp){ ?>
                                    <div class="grid_cell width20P bold"><?php echo $rp->relationship?>: </div>
                                    <div class="grid_cell width80P">
                                        <a href="<?php echo get_permalink($rp->related_product_id)?>"><?php echo $rp->product_name?></a>                                                
                                    </div>
                                    <div class="clear"></div>
                                    <?php } ?>
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
						<h5 class="blue_txt">Compliance Claims</h5>
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
                    <?php
                        if(!$claims)
                        {
                    ?>
                        <div class="tocenter">No Data Found!</div>
                    <?php
                        }else{
                    ?>
					    <div class="grid_head">
						    <div class="grid_row nopaddingbottom nopaddingtop tocenter">
                                <div class="grid_cell nopaddingtop width20P toleft">Issuer</div>
							    <div class="grid_cell nopaddingtop width30P toleft">Suite</div>
							    <div class="grid_cell nopaddingtop width10P">Role</div>
							    <div class="grid_cell nopaddingtop width10P">Level</div>
							    <div class="grid_cell nopaddingtop width10P">Status</div>
							    <div class="grid_cell nopaddingtop width15P toleft left5P">Date</div>
							    <div class="clear"></div>
						    </div>
					    </div>
					    <div class="grids">
					    <?php                     
						    /* Get Test Suites */
						    /*foreach ($product->certifications as $key => $ts){
                                $status_ts = get_post_meta($ts ,'ts_status', true); 
                                        if ($key==(count($product->certifications)-1)) $class_grid = 'last_grid_cell';
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
                                
                            }*/
                            $claims = getClaimsByProductId($product->id);
                            
                            foreach($claims as $claim){
                                $group = groups_get_group(array('group_id' => get_post_meta($claim->suite_id, 'community_id', true)));
					    ?>
                                <div class="grid_row white_bcg tocenter">
                                    <div class="grid_cell nopaddingtop width20P toleft"><a href="<?php echo bp_get_group_permalink($group)?>"><?php echo $claim->issuer?></a></div>
                                    <div class="grid_cell nopaddingtop width30P toleft"><a href="<?php echo get_permalink($claim->suite_id)?>"><?php echo get_the_title($claim->suite_id)?></a></div>
                                    <div class="grid_cell nopaddingtop width10P"><?php echo $claim->conformance_level?></div>
                                    <div class="grid_cell nopaddingtop width10P"><?php echo $claim->role?></div>
                                    <div class="grid_cell nopaddingtop width10P"> - </div>
                                    <div class="grid_cell nopaddingtop width15P toleft left5P"><?php echo formatDate($claim->last_updated)?></div>    
                                    <div class="clear"></div>
                                </div>
                        <?php							
						    }            
                            ?>
                            <div class="space15"></div>
                            <?php
                        }            
					 ?>		
				</div>
				
				<!--end test_cases-->
		</div>
		
	</div> <!--end content container-->
	
</div>

<?php
get_footer();
?>
