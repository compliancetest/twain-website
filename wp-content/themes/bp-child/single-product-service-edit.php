<?php
/*
Template Name: Product / Service Edit
*/
?>

<?php get_header(); ?>

<div class="content container" id="my_profile">
    <div class="grid  dark_gray_txt">

        <div class="column nopaddingtop width60P">
            <form action="#" method="post">

		        <div class="grid_row">
			        <div class="grid_cell width20P"><b>Owner</b></div>
                    <input class="width300" type="text" value="" name="product_owner"/>
                    <div class="clear"></div>
                </div>
                <div class="grid_row">
                    <div class="grid_cell width20P"><b>Type</b></div>
                    <div class="styled_select_dashboard left">
                        <select class="req_field" name="product_type">
                            <option value=""></option>
                            <option style="margin-right: 5px; margin-bottom: 5px;" value="Software">Software</option>
                            <option style="margin-right: 5px; margin-bottom: 5px;" value="Product">Product</option>
                            <option style="margin-right: 5px; margin-bottom: 5px;" value="Process">Process</option>
                            <option style="margin-right: 5px; margin-bottom: 5px;" value="Service">Service</option>
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
                    <input class="width300" type="text" value="" name="product_name"/>
                    <div class="clear"></div>
                </div>
                <div class="grid_row">
                    <div class="grid_cell width20P"><b>Version</b></div>
                    <input class="width300" type="text" value="" name="product_version"/>
                    <div class="clear"></div>
                </div>
                <div class="grid_row">
                    <div class="grid_cell width20P"><b>Date</b></div>
                    <input class="width300" type="text" value="" name="product_date"/>
                    <div class="clear"></div>
                </div>
                <div class="grid_row">
                    <div class="grid_cell width20P"><b>Website</b></div>
                    <input class="width300" type="text" value="" name="product_url"/>
                    <div class="clear"></div>
                </div>
                <div class="grid_row">
                    <div class="grid_cell width20P"><b>Description</b></div>
                    <textarea class="width300" value="" name="product_description"/></textarea>
                    <div class="clear"></div>
                </div>
                <div class="grid_row">
                    <div class="grid_cell width20P"><b>Logo</b></div>
                    <input class="width300" type="file" value="" name="product_thumbnail"/>
                    <div class="clear"></div>
                </div>

                <div class="grey-border-bottom"></div>

                <div class="grid_row">
                    <div class="grid_cell width50P"><b>Related Product</b><br/>
                    <input class="width60P" type="text" value="" name="related_products"/>
                    </div>
                    <div class="grid_cell width50P"><b>Relationship</b><br/>
                        <div class="styled_select_dashboard left">
                            <select class="req_field" name="product_relationship">
                                <option value=""></option>
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


    </div>
</div> <!--end content container-->

<?php get_footer(); ?>
