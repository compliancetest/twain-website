<?php
/**
* Profile - My Details Tab
*/
if(!defined('ABSPATH')) {
    die('Invalid Request!');
}
    
    global $current_user;
    
    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "organisations_members WHERE is_admin=1 AND user_id=%d", $current_user->ID));
        
    $organisation = new CT_Organisation($row->organisation_id);
?>
               
    <div class="grid-box" id="my_details">
        <div class="grid-box-header">
            <h5 class="left">Billing Details</h5>
            <a class="gbh-btn gbh-btn-edit right" href="javascript: void(0);">Edit<span class="simple_tooltip radius6">Edit this section<span></span></span></a>
            <div class="clear"></div>
        </div>
        <div class="grid-box-body">
            <form action="#" method="post">
                <input type="hidden" name="organisation_id" value="<?php echo $organisation->id;?>">
                <div class="grid-row grid-row-complex">
                    <div class="grid-cell width30P">
                        <label>Organisation</label>
                    </div>
                    <div class="grid-cell width70P">
                        <div class="grid-row">
                            <div class="grid-cell width30P"><label>Name</label></div>
                            <div data-name="organisation_name" data-value="<?php echo ctE($organisation->organisation_name);?>" class="grid-cell in_input" data-required="true"><?php echo ctE($organisation->organisation_name);?></div>
                            <div class="clear"></div>
                        </div>
                        <div class="grid-row">
                            <div class="grid-cell width30P"><label>Key</label></div>
                            <div id="orgKey" data-name="organisation_key" data-value="<?php echo ctE($organisation->organisation_key);?>" data-type="skip" class="grid-cell in_input">
                                <?php echo ctE($organisation->organisation_key);?>
                            </div>
                            <button style="margin-top: -2px !important;float: right" class="btn btn-success btn-with-icon btn-confirm copyProfileLink" data-clipboard-text="<?php echo $organisation->organisation_key;?>" onclick="return false;">Copy Key</button>
                            <div class="clear"></div>
                        </div>
                        <div class="grid-row">
                            <div class="grid-cell width30P"><label>Description</label></div>
                            <div data-name="organisation_description" data-value="<?php echo ctE($organisation->organisation_description);?>" class="grid-cell width70P in_input"><?php echo ctE($organisation->organisation_description);?></div>
                            <div class="clear"></div>
                        </div>
                        <div class="grid-row">
                            <div class="grid-cell width30P"><label>Website</label></div>
                            <div data-name="organisation_website" data-value="<?php echo ctE($organisation->organisation_website);?>" class="grid-cell width70P in_input"><?php echo ctE($organisation->organisation_website);?></div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="grid-row grid-row-complex">
                    <div class="grid-cell width30P">
                        <label>Primary Contact</label>
                    </div>
                    <div class="grid-cell width70P">
                        <div class="grid-row">
                            <div class="grid-cell width30P"><label>First Name</label></div>
                            <div data-name="contact_first_name" data-value="<?php echo ctE($organisation->contact_first_name);?>" class="grid-cell in_input"><?php echo ctE($organisation->contact_first_name);?></div>
                            <div class="clear"></div>
                        </div>
                        <div class="grid-row">
                            <div class="grid-cell width30P"><label>Last Name</label></div>
                            <div data-name="contact_last_name" data-value="<?php echo ctE($organisation->contact_last_name);?>" class="grid-cell in_input"><?php echo ctE($organisation->contact_last_name);?></div>
                            <div class="clear"></div>
                        </div>
                        <div class="grid-row">
                            <div class="grid-cell width30P"><label>Email</label></div>
                            <div data-name="contact_email" data-value="<?php echo ctE($organisation->contact_email);?>" class="grid-cell in_input"><?php echo ctE($organisation->contact_email);?></div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="grid-row grid-row-complex">
                    <div class="grid-cell width30P">
                        <label>Secondary Contact</label>
                    </div>
                    <div class="grid-cell width70P">
                        <div class="grid-row">
                            <div class="grid-cell width30P"><label>First Name</label></div>
                            <div data-name="secondary_contact_first_name" data-value="<?php echo ctE($organisation->secondary_contact_first_name);?>" class="grid-cell in_input"><?php echo ctE($organisation->secondary_contact_first_name);?></div>
                            <div class="clear"></div>
                        </div>
                        <div class="grid-row">
                            <div class="grid-cell width30P"><label>Last Name</label></div>
                            <div data-name="secondary_contact_last_name" data-value="<?php echo ctE($organisation->secondary_contact_last_name);?>" class="grid-cell in_input"><?php echo ctE($organisation->secondary_contact_last_name);?></div>
                            <div class="clear"></div>
                        </div>
                        <div class="grid-row">
                            <div class="grid-cell width30P"><label>Email</label></div>
                            <div data-name="secondary_contact_email" data-value="<?php echo ctE($organisation->secondary_contact_email);?>" class="grid-cell in_input"><?php echo ctE($organisation->secondary_contact_email);?></div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="grid-row grid-row-complex">
                    <div class="grid-cell width30P">
                        <label>Billing Address</label>
                    </div>
                    <div class="grid-cell width70P">
                        <div class="grid-row">
                            <div class="grid-cell width30P"><label>Attention</label></div>
                            <div data-name="billing_address_attention" data-value="<?php echo ctE($organisation->billing_address_attention);?>" class="grid-cell in_input"><?php echo ctE($organisation->billing_address_attention);?></div>
                            <div class="clear"></div>
                        </div>
                        <div class="grid-row">
                            <div class="grid-cell width30P"><label>Address Line 1</label></div>
                            <div data-name="billing_address1" data-value="<?php echo ctE($organisation->billing_address1);?>" class="grid-cell in_input"><?php echo ctE($organisation->billing_address1);?></div>
                            <div class="clear"></div>
                        </div>
                        <div class="grid-row">
                            <div class="grid-cell width30P"><label>Address Line 2</label></div>
                            <div data-name="billing_address2" data-value="<?php echo ctE($organisation->billing_address2);?>" class="grid-cell in_input"><?php echo ctE($organisation->billing_address2);?></div>
                            <div class="clear"></div>
                        </div>
                        <div class="grid-row">
                            <div class="grid-cell width30P"><label>Address Line 3</label></div>
                            <div data-name="billing_address3" data-value="<?php echo ctE($organisation->billing_address3);?>" class="grid-cell in_input"><?php echo ctE($organisation->billing_address3);?></div>
                            <div class="clear"></div>
                        </div>
                        <div class="grid-row">
                            <div class="grid-cell width30P"><label>Address Line 4</label></div>
                            <div data-name="billing_address4" data-value="<?php echo ctE($organisation->billing_address4);?>" class="grid-cell in_input"><?php echo ctE($organisation->billing_address4);?></div>
                            <div class="clear"></div>
                        </div>
                        <div class="grid-row">
                            <div class="grid-cell width30P"><label>City</label></div>
                            <div data-name="billing_city" data-value="<?php echo ctE($organisation->billing_city);?>" class="grid-cell in_input"><?php echo ctE($organisation->billing_city);?></div>
                            <div class="clear"></div>
                        </div>
                        <div class="grid-row">
                            <div class="grid-cell width30P"><label>State</label></div>
                            <div data-name="billing_state" data-value="<?php echo ctE($organisation->billing_state);?>" class="grid-cell in_input"><?php echo ctE($organisation->billing_state);?></div>
                            <div class="clear"></div>
                        </div>
                        <div class="grid-row">
                            <div class="grid-cell width30P"><label>Post Code</label></div>
                            <div data-name="billing_postcode" data-value="<?php echo ctE($organisation->billing_postcode);?>" class="grid-cell in_input"><?php echo ctE($organisation->billing_postcode);?></div>
                            <div class="clear"></div>
                        </div>
                        <div class="grid-row">
                            <div class="grid-cell width30P"><label>Country</label></div>
                            <div data-name="billing_country" data-value="<?php echo ctE($organisation->billing_country);?>" class="grid-cell in_input"><?php echo ctE($organisation->billing_country);?></div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="grid-row grid-row-complex">
                    <div class="grid-cell width30P">
                        <label>Telephone</label>
                    </div>
                    <div class="grid-cell width70P">
                        <div class="grid-row">
                            <div class="grid-cell width30P"><label>Country Code</label></div>
                            <div data-name="phonenumber_countrycode" data-value="<?php echo ctE($organisation->phonenumber_countrycode);?>" class="grid-cell in_input"><?php echo ctE($organisation->phonenumber_countrycode);?></div>
                            <div class="clear"></div>
                        </div>
                        <div class="grid-row">
                            <div class="grid-cell width30P"><label>Area Code</label></div>
                            <div data-name="phonenumber_areacode" data-value="<?php echo ctE($organisation->phonenumber_areacode);?>" class="grid-cell in_input"><?php echo ctE($organisation->phonenumber_areacode);?></div>
                            <div class="clear"></div>
                        </div>
                        <div class="grid-row">
                            <div class="grid-cell width30P"><label>Number</label></div>
                            <div data-name="phonenumber" data-value="<?php echo ctE($organisation->phonenumber);?>" class="grid-cell in_input"><?php echo ctE($organisation->phonenumber);?></div>
                            <div class="clear"></div>
                        </div>
                    </div>
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
    <script src="/laravel/resources/assets/js/vendor/clipboard.js"></script>
    <script>
        var clipboard = new Clipboard('.copyProfileLink');
        clipboard.on('success', function(e) {
           jQuery('#orgKey').append( '<div class="message success copiedMessage">Organisation Key has been copied to clipboard.</div>');
            var messageTimeoutHandler = setTimeout(function() {
                jQuery('.copiedMessage').slideUp().remove();
            }, 2000);
        });

    </script>

