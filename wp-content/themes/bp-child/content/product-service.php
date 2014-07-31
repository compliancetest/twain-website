<?php
/**
* Product Service Content
*/
?>
<div class="page-title-block">
  <?php if(!$isAjax){ ?>
    <div class="grid_head column">
        <div class="grid_row nopadding"> 
            <h4 class="left">Product / Service Details</h4>
            <a href="/my-products" class="left action-btn back-btn has-tooltip left15">
                <span class="p"></span>
                <span class="t">Back</span>
                <span class="simple_tooltip radius6" style="top: -27px;">Back to Products<span></span></span>
            </a>
            <a href="<?php echo addPrintParams(get_permalink(), 'product')?>" class="action-btn print-btn print-page-btn" id="print-product-btn"><span class="p"></span><span class="t">PRINT</span></a>
        </div>
    </div>
  <?php } ?>
    <div class="column nopaddingtop">
        <div class="nopadding">
        <?php if (has_post_thumbnail()) { ?>
            <div class="grid_cell width10P">                            
                <?php echo    the_post_thumbnail('post-thumb', array('class' => 'prod_serv_details')); ?>                            
            </div>
            <?php } ?>
            <div class="grid_cell <?php echo has_post_thumbnail() ? 'width90P' : 'width100P'?>">
                <div class="width100P grid_cell suite_view">
                    <div class="left">
                        <p><span class="normal">Product Name: </span><?php echo $product->name; ?></p>
                        <p><small>(<span class="normal">Product ID: </span> <?php echo $product->product_id; ?>)</small></p>                
                    </div>
                <?php if(can_delete_product_and_service(get_the_ID())){ ?>
                    <a href="<?php get_permalink()?>?id=<?php echo $product->id?>&_psnonce=<?php echo wp_create_nonce('delete-product') ?>&return=<?php echo base64_encode("/my-products") ?>" class="action-btn delete-btn right left10"><span class="p"></span><span class="t">DELETE</span></a>
                <?php } ?>                            
                <?php if(can_edit_product_and_service(get_the_ID())){ ?>
                    <a href="/edit-product-and-service?id=<?php echo $product->id?>" class="action-btn edit-btn right"><span class="p"></span><span class="t">EDIT</span></a>
                <?php } ?>                            
                </div>
                <div class="clear"></div>
                <div class="grey-border-bottom"></div>
                <div class="grid_cell width50P product_datails">
                    <p>
                        Product Owner: <span class="bold"><?php echo $product->owner?></span>
                    </p>                    
                    <p>Release Date: <span class="bold">
                    <?php 
                        echo formatDate($product->release_date, "M Y"); // format Nov 2012
                    ?>
                    </span></p>
                    <p>Product Version: <span class="bold"><?php echo $product->version; ?></span></p>
                    <p>Type: <span class="bold"><?php echo $product->type; ?></span></p>
                    <p>
                        Access URL: 
                        <?php
                            if($product->accessURL):
                        ?>
                            <a href="<?php echo get_valid_full_url($product->accessURL)?>" class="bold"><?php echo $product->accessURL; ?></a>
                        <?php
                            endif;
                        ?>
                    </p>
                    <p>Visibility: <span class="bold"><?php echo $product->visibility; ?></span></p>
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
<?php
    $claims = getClaimsByProductId($product->id);
    $testPlans = getTestPlansByProductId($product->id);
?>
<div class="grid_row test_cases">
    <div class="grid_cell width45P">
        <h4 class="blue_txt">Test Plans and Compliance Claims</h4>
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
        if(!$testPlans)
        {
    ?>
        <div class="tocenter">No Data Found!</div>
    <?php
        }else{
    ?>
        <div class="grid_head">
            <div class="grid_row nopaddingbottom nopaddingtop tocenter">
                <div class="grid_cell nopaddingtop width22P toleft">Claim ID</div>
                <div class="grid_cell nopaddingtop width10P toleft">Issuer</div>
                <div class="grid_cell nopaddingtop width20P toleft">Suite</div>
                <div class="grid_cell nopaddingtop width10P">Level</div>
                <div class="grid_cell nopaddingtop width10P">Role</div>                
                <div class="grid_cell nopaddingtop width12P">Status</div>
                <div class="grid_cell nopaddingtop width10P toleft">Date</div>
                <div class="grid_cell nopaddingtop width6P">Certificate</div>
                <div class="clear"></div>
            </div>
        </div>
        <div class="grids">
            <?php              
                foreach($testPlans as $testPlan){
                    if( ! get_the_title($testPlan->suite_id) ){
                        continue;
                    }
                    $group = groups_get_group(array('group_id' => get_post_meta($testPlan->suite_id, 'community_id', true)));
                    $claim = getClaimByTestPlanData( array( 'product_id' => $product->id, 'suite_id' => $testPlan->suite_id , 'level' => str_replace( ';;', '', $testPlan->level ), 'creator_id' => $testPlan->creator_id ) );
            ?>
                    <div class="grid_row white_bcg tocenter">
                        <div class="grid_cell nopaddingtop width22P toleft"><?php echo isset( $claim->claim_id ) ? $claim->claim_id : '';?></div>
                        <div class="grid_cell nopaddingtop width10P toleft"><a href="<?php echo bp_get_group_permalink($group)?>"><?php echo $testPlan->issuer?></a></div>
                        <div class="grid_cell nopaddingtop width20P toleft"><a href="<?php echo get_permalink($testPlan->suite_id)?>"><?php echo get_the_title($testPlan->suite_id)?></a></div>
                        <div class="grid_cell nopaddingtop width10P"><?php echo str_replace(';;', ' ', $testPlan->level);?></div>
                        
                        <div class="grid_cell nopaddingtop width10P"><?php echo str_replace(';;', ' ', $testPlan->role);?></div>
                        <div class="grid_cell nopaddingtop width12P">
                            <?php if( isset( $claim->status ) ){ ?>
                            <span class="status-certified"><?php echo $claim->status?></span>
                            <?php }else{ ?>
                            <span class="status-unverified">In Progress</span>
                            <?php } ?>
                        </div>
                        <div class="grid_cell nopaddingtop width10P toleft"><?php echo isset( $claim->last_updated ) ? formatDate( $claim->last_updated ) : formatDate($testPlan->created_date)?></div>
                        <div class="grid_cell nopaddingtop width6P">
                            <?php if( isset( $claim->claim_id ) ):?>
                                <a href="<?php echo get_site_url()?>/claims/<?php echo $claim->token?>.pdf" onclick="window.open('<?php echo get_site_url()?>/claims/<?php echo $claim->token?>.pdf', '', 'height=600');return false;">View PDF</a>
                                <a href="<?php echo get_site_url()?>/?download-certificate=1&claim=<?php echo $claim->token?>" target="_blank">Download</a>
                            <?php endif;?>
                        </div>
                        <div class="clear"></div>
                    </div>
            <?php                            
                }
          ?>
          </div>
          <?php            
            }            
         ?>        
    <!--end test_cases-->
    <div class="space25"></div>
</div>