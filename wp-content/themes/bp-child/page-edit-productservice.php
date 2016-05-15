<?php
/**
 * Template Name:Add/Edit Product&Service
 */


$psID = isset($_GET['id']) ? $_GET['id'] : null;

$isNew = true;

$product = new ProductAndService($psID);
$product->load();
if (!$product->id)
    $isNew = true;
else
    $isNew = false;

if (($isNew || !is_super_admin()) && !can_maintain_product_and_service(null, $product->id)) {
    addMessage('You do not have the "' . ct_get_privilege_by_code('MAINTAIN_PRODUCTS', 'title') . '" privilege necessary for this action. Please contact your organisation administrator for the ' . get_site_title() . ' site.', 'error');

    wp_redirect("/");
    exit;
}

get_header();

$myProducts = getUserProductsAndServices(null, $isNew ? array() : array($psID));

$user_id = get_current_user_id();

$user_organisation = ct_get_user_organisation($user_id);

$myServices = getUserServices(null, array());

if (isset($_SESSION['product_data'])) {
    $prevData = $_SESSION['product_data'];
    //Restore the previous form data
    $product->name = $prevData['product_name'];
    $product->release_date = $prevData['product_release_date'];
    $product->product_id = $prevData['product_id'];
    $product->type = $prevData['product_type'];
    $product->accessURL = $prevData['product_url'];
    $product->version = $prevData['product_version'];
    $product->owner = $prevData['product_owner'];
    $product->descrition = $prevData['product_description'];
    $product->organisation_id = $prevData['product_organisation_id'];
    $product->product_owner_override = $prevData['product_owner_override'];
    $product->product_override = $prevData['product_override'];

    $product->relatedProducts = array();
    if ($prevData['related-product']) {
        foreach ($prevData['related-product'] as $i => $rpid) {
            $row = new stdClass();
            $row->related_product_id = $rpid;
            $row->relationship = $prevData['related-product-relation'][$i];
            $product->relatedProducts[] = $row;
        }
    }

    $_SESSION['product_data'] = null;
    unset($_SESSION['product_data']);
}

?>
    <div class="content edit-item-wrapper" id="edit_product_service_wrapper">
        <div class="column container">
            <form name="psForm" id="psForm" action="" class="validation-form" method="post" enctype="multipart/form-data">
                <?php if ($isNew) { ?>
                    <h2>Add New Product</h2>
                <?php } else { ?>
                    <h2>Edit Product: <?php $product->name ?></h2>
                <?php } ?>
                <div class="grid-box grid-box-expandable grid-box-opened" id="ps-info-box">
                    <div class="grid-box-header">
                        <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
                        <h5 class="left">Information</h5>

                        <div class="clear"></div>
                    </div>
                    <div class="grid-box-body">
                        <div class="column">
                            <div class="field-row">
                                <div class="grid-cell">
                                    <label>Name:</label>
                                    <div class="has-field-tooltip">
                                        <input type="text" class="input required field-tooltip"
                                               data-tooltip-content="Enter your product or service name as it is known in the marketplace."
                                               name="product_name" id="product_name" value="<?php echo $product->name ?>" readonly="readonly"
                                               disabled="disabled"/>
                                    </div>
                                </div>
                                <div class="grid-cell">
                                    <label>Release Date:</label>

                                    <div class="has-field-tooltip">
                                        <input type="text" class="input datepicker field-tooltip"
                                               data-tooltip-content="Enter the date that this version of your product or service was released to the market."
                                               name="product_release_date" id="product_release_date"
                                               value="<?php echo !$product->release_date ? formatDate(date('Y-m-d')) : formatDate($product->release_date) ?>"/>
                                    </div>
                                </div>
                                <div class="grid-cell radio-cell" id="ps-type-cell">
                                    <input type="hidden" name="product_type" id="product_type_software" value="Software Product"/>
                                </div>
                                <div class="clear"></div>
                            </div>
                            <div class="field-row">
                                <div class="grid-cell">
                                    <label>Version:</label>

                                    <div class=" product-version-field">
                                        <input type="text" class="input required" name="product_version" id="product_version" value="<?php echo $product->version ?>"
                                               readonly="readonly" disabled="disabled"/>
                                        <span style="width: 278px; margin-left: -139px; bottom: 52px; display: none;" class="simple_tooltip">Enter the version of your product or service. Want to test multiple versions? Create a product for each.<span></span></span>
                                    </div>
                                </div>
                                <div class="grid-cell">
                                    <label>Access URL:</label>

                                    <div class="has-field-tooltip">
                                        <input type="text" class="input medium-input field-tooltip" name="product_url" id="product_url" value="<?php echo $product->accessURL ?>"
                                               data-tooltip-content="Provide a link to the page on your website that describes this product."/>
                                    </div>
                                </div>
                                <div class="clear"></div>
                            </div>
                            <div class="field-row">
                                <div class="grid-cell">
                                    <label>Organisation:</label>

                                    <div class="has-field-tooltip">
                                        <?php
                                        if(is_super_admin()) {
                                            $organisations = ct_get_all_organisations();
                                    ?>
                                        <select name="product_owner" style="width: 195px; margin-right: 10px;" id="product_owner" class="select field-tooltip admin_entry_select" data-tooltip-content="The owner of your product or service. It is set to the same as the organisation name from your profile.">
                                            <option value="">- Select Organisation -</option>
                                            <?php foreach($organisations as $org): ?>
                                                 <option value="<?php echo $org->id?>" <?php echo $org->id == $product->organisation_id ? 'selected="selected"' : '' ?>><?php echo $org->organisation_name?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <input type="checkbox" class="allow_override" name="allow_override" style="margin-right: 3px;" <?php if( $product->product_override == 'yes' ):?> checked="checked" <?php endif;?>>Override </br>
                                        <input type="text" style="width: 195px; margin-right: 10px; <?php if( $product->product_override != 'yes' ):?> display: none; <?php endif;?> margin-top: 10px;" class="input  <?php if( $product->product_override == 'yes' ):?> required <?php endif;?>field-tooltip admin_entry_input" name="product_owner_override" id="product_owner_override" value="<?php echo $product->product_override == 'yes' ? $product->product_owner_override : $user_organisation->organisation_name?>" data-tooltip-content="The owner of your product or service. It is set to the same as the organisation name from your profile." />
                                    <?php }else{ ?>
                                        <input type="text" style="width: 195px; margin-right: 10px;" class="input required field-tooltip" name="product_owner" id="product_owner" readonly value="<?php echo $product->product_override == 'yes' ? $product->product_owner_override : $user_organisation->organisation_name?>" data-default="<?php echo $user_organisation->organisation_name?>" data-tooltip-content="The owner of your product or service. It is set to the same as the organisation name from your profile." />
                                        <input type="checkbox" class="allow_override" name="allow_override" <?php if( $product->product_override == 'yes' ):?> checked="checked" <?php endif;?>style="margin-right: 3px;">Override
                                        <?php } ?>
                                    </div>
                                    <div class="space10"></div>
                                    <label>Manufacturer:</label>

                                    <div class="has-field-tooltip">
                                        <input type="text" style="width: 275px; margin-right: 10px; margin-top: 10px;" class="input field-tooltip" name="manufacturer"
                                               id="manufacturer" value="<?php echo $product->manufacturer?>" readonly="readonly" disabled="disabled"
                                               data-tooltip-content="Product manufacturer"/>
                                    </div>
                                    <div class="space10"></div>
                                    <label>Visibility:</label>

                                    <div class="has-field-tooltip">
                                        <select id="product_visibility" name="product_visibility" class="select field-tooltip"
                                                data-tooltip-content="'Public' means anyone can see this product, 'Community' means visibility is limited to community members, 'Private' means that the product is only visible to your own organisation">
                                            <option <?php if ($product->visibility == 'Public'): ?> selected="selected" <?php endif; ?> value="Public">Public</option>
                                            <option <?php if ($product->visibility == 'Community'): ?> selected="selected" <?php endif; ?> value="Community">Community</option>
                                            <option <?php if ($product->visibility == 'Private'): ?> selected="selected" <?php endif; ?> value="Private">Private</option>
                                        </select>
                                    </div>
                                    <div class="space10"></div>
                                    <?php if (is_super_admin()): ?>
                                        <label><input type="checkbox" name="services_not_permitted"
                                                      id="services_not_permitted" <?php echo isset($product->services_not_permitted) && $product->services_not_permitted == '1' ? 'checked="checked"' : '' ?> />
                                            Services not permitted</label>
                                    <?php endif; ?>
                                </div>
                                <div class="grid-cell width60P">
                                    <label>Description:</label>

                                    <div class="has-defined-tooltip">
                                        <textarea cols="" rows="" class="textarea field-tooltip" id="product_description"
                                                  name="product_description"><?php echo $product->descrition ?></textarea>
                                        <span class="simple_tooltip" style="width:540px; margin-left: -270px; bottom: 115px;"><span></span>Provide a few paragraphs to describe your product or service. This information is displayed to users who may be searching CompliacneTest for certified products.</span>
                                    </div>
                                </div>
                                <div class="clear"></div>
                            </div>
                            <div class="field-row">
                            </div>
                            <div class="field-row">
                                <div class="grid-cell">
                                   <label>Product Type:</label>

                                    <div class="has-field-tooltip">
                                        <select name="product_type" class="select field-tooltip" data-tooltip-content="Product Type" id="productType" disabled="disabled">
                                            <option <?php if ($product->product_type == 'DataSource'): ?> selected="selected" <?php endif; ?> value="DataSource">DataSource</option>
                                            <option <?php if ($product->product_type == 'Application'): ?> selected="selected" <?php endif; ?> value="Application">Application</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid-cell width60P div_type_ds">
                                    <label>Capabilities(comma separated):</label>

                                    <div>
                                        <textarea cols="" rows="" class="textarea field-tooltip" id="product_caps" name="capabilities" readonly="readonly" disabled="disabled"><?php if(is_array($product->capabilities)) echo implode(',', $product->capabilities) ?></textarea>
                                        <span class="simple_tooltip" style="width:540px; margin-left: -270px; bottom: 115px;"><span></span>Product capabilities list</span>
                                    </div>

                                </div>
                                <div class="grid-cell width60P div_type_app">
                                   <div class="field-row div_type_app" <?php if(!$product->id || $product->product_type != 'Application'):?>style="display: none;" <?php endif;?>>
                                     <label>Product Features:</label>
                                        <?php foreach(getUserSubscribedSuites() as $suite):?>
                                            <?php
                                                $suite = new TestSuite($suite->suite_id);
                                                $suite->load();
                                                if($suite->ts_tester_role !== 'Application') {
                                                    continue;
                                                }?>
                                                <label style="margin-left: 10px;">
                                                    <input type="checkbox" name="product_suites[]" class="product_suites" <?php echo isset($product->product_suites) && in_array($suite->id, $product->product_suites) ? 'checked="checked"' : '' ?> value="<?php echo $suite->id;?>"/>
                                                        <?php echo $suite->title;?>
                                                </label>
                                                <?php

                                                foreach($suite->featuresList as $feature):
                                            ?>
                                                    <label class="tooltip-label has-tooltip">
                                                        <input type="checkbox" name="product_features[]" class="product_features"
                                                               data-suiteid="<?php echo $suite->id;?>" <?php echo isset($product->product_features) && in_array($feature['name'], $product->product_features) ? 'checked="checked"' : '' ?>
                                                               value="<?php echo $feature['name'];?>"
                                                        />
                                                            <?php echo $feature['name'];?>
                                                        <span class="simple_tooltip radius6"><?php echo $feature['description'];?><span></span></span>
                                                    </label>
                                                <?php endforeach;?>
                                        <?php endforeach;?>
                                    </div>
                                </div>
                                <div class="clear"></div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="space20"></div>
                <div class="grid-box">
                    <div class="grid-box-footer nobackground noshadow">
                        <div class="btn-row nopaddingleft">
                            <a href="#" class="action-btn process-btn submit-btn has-tooltip"><span class="simple_tooltip radius6" style="margin-left: -50px; width: 90px;">Save Product<span></span></span><span
                                    class="p"></span><span class="t">Confirm</span></a>
                            <a href="javascript: history.go(-1)" class="action-btn cancel-btn"><span class="p"></span><span class="t">Cancel</span></a>

                            <div class="clear"></div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="id" value="<?php echo $product->id ?>"/>
                <?php
                wp_nonce_field('save-product-service', '_psnonce');
                ?>
            </form>
        </div>
        <div class="clear"></div>
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {

            $('#productType').on('change', function(){
                if($(this).val() == 'Application'){
                    $('.div_type_app').show();
                    $('.div_type_ds').hide();
                } else {
                    $('.div_type_app').hide();
                    $('.div_type_ds').show();
                }
            });

            $('#productType').change();

            $('.product_suites').on('change', function(){
                if(!$('.product_suites').is(':checked')){
                     $(".product_features[data-suiteid='" + jQuery(this).val() + "']").attr('checked', false);
                }
                $('.product_features').attr('disabled', 'disabled');
                $.each($('.product_suites:checked'), function(index, el){
                    $(".product_features[data-suiteid='" + jQuery(el).val() + "']").removeAttr('disabled');
                })
            });

            $('.product_suites').change();

            $('#product_description').redactor({
                air: true,
                minHeight: 80

            });
            <?php if( is_super_admin() ):?>
            $('.allow_override').on('click', function () {
                if ($(this).is(':checked')) {
                    $('.admin_entry_input').show();
                } else {
                    $('.admin_entry_input').hide();
                }
            });
            <?php else:?>
            $('.allow_override').on('click', function () {
                if ($(this).is(':checked')) {
                    $('#product_owner').removeAttr('readonly');
                } else {
                    $('#product_owner').attr('readonly', 'readonly');
                    $('#product_owner').val($('#product_owner').attr('data-default'));
                }
            });
            <?php endif;?>
            jQuery('#add-related-product').click(function () {
                jQuery('#ps-related-box .btn-row').before('<div class="field-row new-row">' +
                    '<div class="grid-cell width55P">' +
                    '<label>Related Product: </label>' +
                    '<select class="combobox select" name="related-product[]">' +
                    '<option value=""></option>' +
                    <?php foreach($myProducts as $p){ ?>
                    '<option value="<?php echo $p->ID?>"><?php echo str_replace("'", "&#39;", get_post_meta($p->ID, 'product_name', true))?></option>' +
                    <?php } ?>
                    '</select>' +
                    '</div>' +
                    '<div class="grid-cell width30P">' +
                    '<label>Relation Ship: </label>' +
                    '<select class="select" name="related-product-relation[]">' +
                    '<option value="Depends On">Depends On</option>' +
                    '<option value="Newer Version Of">Newer Version Of</option>' +
                    '</select>' +
                    '</div>' +
                    '<div class="grid-cell right">' +
                    '<label>&nbsp;</label>' +
                    '<a href="#" class="action-btn delete-btn icon-btn has-tooltip" title="Delete Related Product"><span class="p"></span></a>' +
                    '</div>' +
                    '<div class="clear"></div>' +
                    '</div>');
                jQuery('#ps-related-box .combobox:last').combobox();
                return false;
            });
            jQuery('#ps-related-box').on('click', '.delete-btn', function () {
                //if(jQuery(this).parents('.field-row').hasClass('new-row'))
//        {

                jQuery(this).parents('.field-row').fadeOut('fast', function () {
                    jQuery(this).remove();
                })
//        }

                return false;
            });

            jQuery('.delete_service').on('click', function () {
                jQuery('#services_to_delete').val(jQuery('#services_to_delete').val() + ',' + jQuery(this).attr('data-serviceid'))
                jQuery(this).parents('.field-row').fadeOut('fast', function () {
                    jQuery(this).remove();
                })
                return false;
            })

            jQuery('#ps-related-box .combobox').combobox();


            jQuery(".validation-form .required").each(function () {
                jQuery(this).parent().append('<span class="msg-required" style="display: none">This field is required.</span>');
            })

            jQuery('#psForm .input').focus(function () {
                $(this).removeClass('input-error');
            })

            var forceSubmit = false;
            jQuery('#psForm').submit(function () {
                $('#psForm .grid-box-footer .message').remove();
                var isValid = true;
                var errorMsg = '';

                jQuery(this).find('.required').each(function () {
                    if (jQuery(this).val() == '') {
                        isValid = false;
                        jQuery(this).addClass('input-error');
                    }
                });

                <?php if($product->id && $product->visibility == 'Private'):?>
                    var organisations = <?php echo $user_organisation->products_organisations;?>;
                    if(jQuery('#product_visibility').val() == 'Public' && jQuery.inArray(jQuery('#manufacturer').val(), organisations) == -1){
                        if(!confirm('Manufacturer is not from the manufacturer list of your organisation. Do you want to add it? If yes, all new products of this manufacturer will be created with "Public" visibility.')){
                            return false;
                        }
                    }
                <?php endif;?>
                if($('#productType').val() == 'Application' && ( !$('.product_suites:checked').length || !$('.product_features:checked').length)){
                    $('#psForm .grid-box-footer').append('<div class="message error" style="display: none">Please select supported features / test suites.</div>');
                    $('#psForm .grid-box-footer .message').fadeIn('fast');
                    return false;
                }

                 if($('#productType').val() == 'DataSource' && ! $('#product_caps').val()){
                    $('#psForm .grid-box-footer').append('<div class="message error" style="display: none">Please list supported capabilities.</div>');
                    $('#psForm .grid-box-footer .message').fadeIn('fast');
                    return false;
                }

                if (!isValid) {
                    $('#psForm .grid-box-footer').append('<div class="message error" style="display: none">Please complete fields in red.</div>');
                    $('#psForm .grid-box-footer .message').fadeIn('fast');
                    return false;
                }

                //Validate date format
                if (jQuery("#product_release_date").val() != '' && !isValidDate(jQuery("#product_release_date").val())) {
                    isValid = false;
                    jQuery("#product_release_date").addClass('input-error');
                    $('#psForm .grid-box-footer').append('<div class="message error" style="display: none">Please enter valid release date.</div>');
                    $('#psForm .grid-box-footer .message').fadeIn('fast');
                    return false;
                }

                //Validate product URL
                if (jQuery("#product_url").val() != '' && !isValidUrl(jQuery("#product_url").val())) {
                    isValid = false;
                    jQuery("#product_url").addClass('input-error');
                    $('#psForm .grid-box-footer').append('<div class="message error" style="display: none">Please enter valid access url.</div>');
                    $('#psForm .grid-box-footer .message').fadeIn('fast');
                    return false;
                }

                var productIDReg = /^[a-zA-Z0-9-_.]+$/;
                //Product ID Validation
                if ($('#psForm #product_id').val() != '') {
                    if (!productIDReg.test($('#psForm #product_id').val())) {
                        $('#psForm .grid-box-footer').append('<div class="message error" style="display: none">Product ID may only contain letters, numbers, dot, dash and underscore characters([a-zA-Z0-9.-_]+). Upper case letters will be converted to lower case.</div>');
                        jQuery("#product_id").addClass('input-error');
                        $('#psForm .grid-box-footer .message').fadeIn('fast');
                        return false;
                    }
                } else {
                    if ($('#psForm #product_id').val() == '') {
                        return true;
                    }
                    if (!forceSubmit && !productIDReg.test($('#psForm #product_owner').val() + "_" + $('#psForm #product_name').val() + "_" + $('#psForm #product_version').val())) {
                        $('#psForm .grid-box-footer').append('<div class="message error" style="display: none">Product ID may only contain letters, numbers, dot, dash and underscore characters([a-zA-Z0-9.-_]+). Upper case letters will be converted to lower case.</div>');
                        $('#psForm .grid-box-footer .message').fadeIn('fast');
                        forceSubmit = true;
                        setTimeout(function () {
                            $('#psForm').submit();
                        }, 2000);
                        return false;
                    }

                }

                return isValid;

            });
        })

    </script>
<?php

get_footer();