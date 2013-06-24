<?php
/*
Template Name: Product / Service Edit
*/
exit();
?>

<?php
    $product_prop = array('owner','type','name','date','url','description');

    $meta['product_thumb'] = '';
    foreach ($product_prop as $prop)
        $meta['product_'.$prop] = '';

    if ( isset($_GET['product_id']) ) {
        $product_id = $_GET['product_id'];
        if ( empty($product_id) )
            $error_msg = "Wrong ID !";
        else {
            foreach ($product_prop as $prop)
                $meta['product_'.$prop] = get_post_meta($product_id,'product_'.$prop,true);

            //$meta['product_date']  = !empty($meta['product_date']) ? date("d-M-Y", strtotime($meta['product_date'])) : date("d-M-Y");
            $meta['product_thumb'] = get_the_post_thumbnail($product_id, 'thumbnail');
            $meta['related_products'] = explode(',',get_post_meta($product_id,'related_products',true));

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
        <div class="column nopaddingtop width50P left">
            <form action="#" method="post">

		        <div class="grid_row hint">
			        <div class="grid_cell width20P"><b>Owner</b></div>
                    <input class="width300" type="text" value="<?php echo $meta['product_owner']; ?>" name="product_owner"/>
                    <!-- hint -->
                    <span class="displaynone">
                        Product Owner<br/>
                        <?php echo $meta['product_description'];?>
                    </span>
                    <div class="clear"></div>
                </div>
                <div class="grid_row hint">
                    <div class="grid_cell width20P"><b>Type</b></div>
                    <div class="styled_select left width300">
                        <label>
                        <select class="req_field" name="product_type">
                            <option value=""></option>
                            <option value="Software"<?php if($meta['product_type']=='Software'){ echo 'selected="selected"'; } ?> >Software</option>
                            <option value="Product" <?php if($meta['product_type']=='Product') { echo 'selected="selected"'; } ?> >Product</option>
                            <option value="Process" <?php if($meta['product_type']=='Process') { echo 'selected="selected"'; } ?> >Process</option>
                            <option value="Service" <?php if($meta['product_type']=='Service') { echo 'selected="selected"'; } ?> >Service</option>
                        </select>
                        </label>
                    </div>
                    <!-- hint -->
                    <span class="displaynone">Product Type<br/>
                        <?php echo $meta['product_description'];?>
                    </span>
                    <div class="clear"></div>
                </div>
                <div class="grid_row">
                    <div class="grid_cell width20P"><b>Industry</b></div>
                    <div class="styled_select left width300">
                        <label>
                        <select class="req_field" name="product_industry">
                            <option value=""></option>
                        </select>
                        </label>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="grid_row">
                    <div class="grid_cell width20P"><b>Name</b></div>
                    <input class="width300" type="text" value="<?php echo $meta['product_name']; ?>" name="product_name"/>
                    <div class="clear"></div>
                </div>
                <div class="grid_row">
                    <div class="grid_cell width20P"><b>Version</b></div>
                    <input class="width300" type="text" value="" name="product_version"/>
                    <div class="clear"></div>
                </div>
                <div class="grid_row">
                    <div class="grid_cell width20P"><b>Date</b></div>
                    <input class="width300" type="text" value="<?php echo (!empty($meta['product_date'])?date("d-M-Y", strtotime($meta['product_date'])) : ''); ?>" name="product_date"/>
                    <div class="clear"></div>
                </div>
                <div class="grid_row">
                    <div class="grid_cell width20P"><b>Website</b></div>
                    <input class="width300" type="text" value="<?php echo $meta['product_url']; ?>" name="product_url"/>
                    <div class="clear"></div>
                </div>
                <div class="grid_row">
                    <div class="grid_cell width20P"><b>Description</b></div>
                    <textarea class="width300" name="product_description"><?php echo $meta['product_description']; ?></textarea>
                    <div class="clear"></div>
                </div>
                <div class="grid_row">
                    <div class="grid_cell width20P"><b>Logo</b></div>
                    <input class="width300" type="file" value="" name="product_thumbnail"/>
                    <?php echo $meta['product_thumb']; ?>
                    <div class="clear"></div>
                </div>

                <div class="grey-border-bottom"></div>

                <div class="grid_row">
                    <div class="grid_cell width50P"><b>Related Product</b><br/>
                        <?php if (!empty($meta['related_products'])) {
                            foreach ($meta['related_products'] as $prod) { ?>
                            <input class="width60P" type="text" value="<?php echo get_post_meta($prod,'product_name',true); ?>" name="related_products"/>
                        <?php }
                        } else { ?>
                            <input class="width60P" type="text" value="" name="related_products" placeholder="Auto complete"/>
                        <?php } ?>
                    </div>
                    <div class="grid_cell width50P"><b>Relationship</b><br/>
                        <div class="styled_select left">
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
        <div class="column nopaddingtop width40P left">
            <div id="field_hint" style="background-color: rgba(0,0,0,0.02);min-height: 50px;"><span>Choose a field to get hint.</span></div>
        </div>
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

<script type="text/javascript">
jQuery(document).ready(function() {

    jQuery('.hint').live('click', function() {
        var hint = jQuery(this).children('span.displaynone').html();
        jQuery('#field_hint').children('span').replaceWith( '<span>' + hint + '</span>' );
    });

});
</script>

<?php get_footer(); ?>
