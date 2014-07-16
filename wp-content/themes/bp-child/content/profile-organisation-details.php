<?php
/**
* Profile - My Details Tab
*/
if(!defined('ABSPATH')) {
    die('Invalid Request!');
}
    
    global $current_user;
    
    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "organisations WHERE admin_id = %d", $current_user->ID));
        
    $organisation = new CT_Organisation($row->id);
?>
<div class="left three_fifths">                
    <div class="grid-box" id="my_details">
        <div class="grid-box-header">
            <h5 class="left">My Details</h5>
            <a class="gbh-btn gbh-btn-edit right" href="javascript: void(0);">Edit<span class="simple_tooltip radius6">Edit this section<span></span></span></a>
            <div class="clear"></div>
        </div>
        <div class="grid-box-body">
            <form action="#" method="post">
                <input type="hidden" name="organisation_id" value="<?php echo $organisation->id;?>">
                <div class="grid-row">
                    <div class="grid-cell width30P"><label>Organisation Name</label></div>
                    <div data-name="organisation_name" data-value="<?php echo $organisation->organisation_name;?>" class="grid-cell in_input"><?php echo $organisation->organisation_name;?></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-row">
                    <div class="grid-cell width30P"><label>First Name</label></div>
                    <div data-name="contact_first_name" data-value="<?php echo $organisation->contact_first_name;?>" class="grid-cell in_input"><?php echo $organisation->contact_first_name;?></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-row">
                    <div class="grid-cell width30P"><label>Last Name</label></div>
                    <div data-name="contact_last_name" data-value="<?php echo $organisation->contact_last_name;?>" class="grid-cell in_input"><?php echo $organisation->contact_last_name;?></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-row">
                    <div class="grid-cell width30P"><label>Email</label></div>
                    <div data-name="contact_email" data-value="<?php echo $organisation->contact_email;?>" class="grid-cell in_input"><?php echo $organisation->contact_email;?></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-row">
                    <div class="grid-cell width30P"><label>Billing Address Attention</label></div>
                    <div data-name="billing_address_attention" data-value="<?php echo $organisation->billing_address_attention;?>" class="grid-cell in_input"><?php echo $organisation->billing_address_attention;?></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-row">
                    <div class="grid-cell width30P"><label>Billing Address1</label></div>
                    <div data-name="billing_address1" data-value="<?php echo $organisation->billing_address1;?>" class="grid-cell in_input"><?php echo $organisation->billing_address1;?></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-row">
                    <div class="grid-cell width30P"><label>Billing Address2</label></div>
                    <div data-name="billing_address2" data-value="<?php echo $organisation->billing_address2;?>" class="grid-cell in_input"><?php echo $organisation->billing_address2;?></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-row">
                    <div class="grid-cell width30P"><label>Billing Address3</label></div>
                    <div data-name="billing_address3" data-value="<?php echo $organisation->billing_address3;?>" class="grid-cell in_input"><?php echo $organisation->billing_address3;?></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-row">
                    <div class="grid-cell width30P"><label>Billing Address4</label></div>
                    <div data-name="billing_address4" data-value="<?php echo $organisation->billing_address4;?>" class="grid-cell in_input"><?php echo $organisation->billing_address4;?></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-row">
                    <div class="grid-cell width30P"><label>City</label></div>
                    <div data-name="billing_city" data-value="<?php echo $organisation->billing_city;?>" class="grid-cell in_input"><?php echo $organisation->billing_city;?></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-row">
                    <div class="grid-cell width30P"><label>State</label></div>
                    <div data-name="billing_state" data-value="<?php echo $organisation->billing_state;?>" class="grid-cell in_input"><?php echo $organisation->billing_state;?></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-row">
                    <div class="grid-cell width30P"><label>Post Code</label></div>
                    <div data-name="billing_postcode" data-value="<?php echo $organisation->billing_postcode;?>" class="grid-cell in_input"><?php echo $organisation->billing_postcode;?></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-row">
                    <div class="grid-cell width30P"><label>Country</label></div>
                    <div data-name="billing_country" data-value="<?php echo $organisation->billing_country;?>" class="grid-cell in_input"><?php echo $organisation->billing_country;?></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-row">
                    <div class="grid-cell width30P"><label>Telephone Country Code</label></div>
                    <div data-name="phonenumber_countrycode" data-value="<?php echo $organisation->phonenumber_countrycode;?>" class="grid-cell in_input"><?php echo $organisation->phonenumber_countrycode;?></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-row">
                    <div class="grid-cell width30P"><label>Telephone Area Code</label></div>
                    <div data-name="phonenumber_areacode" data-value="<?php echo $organisation->phonenumber_areacode;?>" class="grid-cell in_input"><?php echo $organisation->phonenumber_areacode;?></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-row">
                    <div class="grid-cell width30P"><label>Telephone Number</label></div>
                    <div data-name="phonenumber" data-value="<?php echo $organisation->phonenumber;?>" class="grid-cell in_input"><?php echo $organisation->phonenumber;?></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-row btn-row">
                    <a href="#" class="action-btn process-btn "><span class="p"></span><span class="t">Save</span></a>
                    <a href="#" class="action-btn cancel-btn edit-cancel-btn left10"><span class="p"></span><span class="t">Cancel</span></a>
                    <div class="clear"></div>
                </div>
                <?php wp_nonce_field('organisation_detail_edit', 'cp-action'); ?>
            </form>
        </div>
    </div>                
    <div class="clear"></div>            
</div>
<?php $my_details_desc = get_post_meta($post->ID, 'my_organisation_desc', true);?>
<?php if ($my_details_desc): ?>
<div class="right two_fifths">
    <div class="gray_message_box radius9 light_gray_txt">
        <div class="indicator"></div>
        <?php echo $my_details_desc;?>
    </div>
</div>
<?php endif; ?>