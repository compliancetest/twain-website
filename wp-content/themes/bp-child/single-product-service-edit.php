<?php
/*
Template Name: Product / Service Edit
*/
?>

<?php
    if ( isset($_GET['product_id']) ) {
        $product_id = $_GET['product_id'];
        if ( empty($product_id) )
            $error_msg = "Wrong ID !";
        else {
            $product_owner = get_post_meta($product_id,'product_owner',true);
            $product_type  = get_post_meta($product_id,'product_type',true);
            $product_name  = get_post_meta($product_id,'product_name',true);
            $product_date  = get_post_meta($product_id,'product_date',true);
            $product_date  = !empty($product_date) ? date("d-M-Y", strtotime($product_date)) : '';
            $product_url  = get_post_meta($product_id,'product_url',true);
            $product_description  = get_post_meta($product_id,'product_description',true);
            $product_thumb = get_the_post_thumbnail($product_id, 'thumbnail');
        }
    }
?>

<?php get_header(); ?>

<div class="content container" id="my_profile">
    <div class="grid  dark_gray_txt">
		<div class="grid_head column">
			<div class="grid_row nopadding">
				<h4>Create / Edit Product or Service</h4>
			</div>
		</div>
        <div class="column nopaddingtop width60P left">
            <form action="#" method="post">

		        <div class="grid_row">
			        <div class="grid_cell width20P"><b>Owner</b></div>
                    <input class="width300" type="text" value="<?php echo $product_owner; ?>" name="product_owner"/>
                    <span class="simple_tooltip radius6">Expand<span></span></span>
                    <div class="clear"></div>
                </div>
                <div class="grid_row">
                    <div class="grid_cell width20P"><b>Type</b></div>
                    <div class="styled_select_dashboard left">
                        <select class="req_field" name="product_type">
                            <option value=""></option>
                            <option value="Software"<?php if($product_type=='Software'){ echo 'selected="selected"'; } ?> >Software</option>
                            <option value="Product" <?php if($product_type=='Product') { echo 'selected="selected"'; } ?> >Product</option>
                            <option value="Process" <?php if($product_type=='Process') { echo 'selected="selected"'; } ?> >Process</option>
                            <option value="Service" <?php if($product_type=='Service') { echo 'selected="selected"'; } ?> >Service</option>
                        </select>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="grid_row">
                    <div class="grid_cell width20P"><b>Industry</b></div>
                    <div class="styled_select_dashboard left">
                        <select class="req_field" name="product_industry">
                            <option value=""></option>
                        </select>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="grid_row">
                    <div class="grid_cell width20P"><b>Name</b></div>
                    <input class="width300" type="text" value="<?php echo $product_name; ?>" name="product_name"/>
                    <div class="clear"></div>
                </div>
                <div class="grid_row">
                    <div class="grid_cell width20P"><b>Version</b></div>
                    <input class="width300" type="text" value="" name="product_version"/>
                    <div class="clear"></div>
                </div>
                <div class="grid_row">
                    <div class="grid_cell width20P"><b>Date</b></div>
                    <input class="width300" type="text" value="<?php echo $product_date; ?>" name="product_date"/>
                    <div class="clear"></div>
                </div>
                <div class="grid_row">
                    <div class="grid_cell width20P"><b>Website</b></div>
                    <input class="width300" type="text" value="<?php echo $product_url; ?>" name="product_url"/>
                    <div class="clear"></div>
                </div>
                <div class="grid_row">
                    <div class="grid_cell width20P"><b>Description</b></div>
                    <textarea class="width300" name="product_description"><?php echo $product_description; ?></textarea>
                    <div class="clear"></div>
                </div>
                <div class="grid_row">
                    <div class="grid_cell width20P"><b>Logo</b></div>
                    <?php echo $product_thumb; ?>
                    <input class="width300" type="file" value="" name="product_thumbnail"/>
                    <div class="clear"></div>
                </div>

                <div class="grey-border-bottom"></div>

                <div class="grid_row">
                    <div class="grid_cell width50P"><b>Related Product</b><br/>
                    <input class="width60P" type="text" value="" name="related_products" placeholder="Auto complete"/>
                    </div>
                    <div class="grid_cell width50P"><b>Relationship</b><br/>
                        <div class="styled_select_dashboard left">
                            <select class="req_field" name="product_relationship">
                                <option value="">Version of ...</option>
                            </select>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>

                <div class="grid_cell width50P">
                    <a href="#" class="blue_txt nodecoration">
                         <span class="sign button button_small blue_bcg2 white_txt radius30 button_sign">+</span> ADD Related Product</a>
                </div>
                <div class="clear"></div>


            </form>


        </div>
        <div class="column nopaddingtop width40P left"><span><?php echo "<pre>".print_r($meta_values,true)."</pre>"; ?></span></div>
        <div class="clear"></div>


        <div class="grid_row test_cases">
            <div class="width15P right">
            <div class="grid_cell width50P">
                <a class="button gray_bcg white_txt button_small radius3 normal"><span></span>Cancel</a>
            </div>

            <div class="grid_cell width50P">
                <a class="button green_bcg white_txt button_small radius3 normal"><span></span>Save</a>
            </div>
                </div>
            <div class="clear"></div>
        </div>

        <div class="clear"></div>

    </div>
</div> <!--end content container-->

<?php get_footer(); ?>
