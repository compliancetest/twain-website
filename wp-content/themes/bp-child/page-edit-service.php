<?php
/**
 * Template Name:Add/Edit Service
 */


$psID = isset($_GET['id']) ? $_GET['id'] : null;

if( ($psID != null && !can_edit_product_and_service($psID)) || ($psID == null && !can_create_product_and_service()) )
{
    if(!$psID)
        addMessage('Sorry, you are not allowed to create a Service.', 'error');
    else
        addMessage('Sorry, you are not allowed to edit the Service', 'error');

    wp_redirect("/");
    exit;
}

$isNew = true;

$service = new Service( $psID );
$service ->load();
if( ! $service->id )
    $isNew = true;
else
    $isNew = false;

get_header();

$myProducts = getUserProductsAndServices(null, $isNew ? array() : array($psID));

if( isset($_SESSION['service_data'] ) )
{
    $prevData = $_SESSION['service_data'];
    //Restore the previous form data
    $service->service_name = $prevData['service_name'];
    $service->service_endpoint = $prevData['service_endpoint'];
    $service->service_endpoint_type = $prevData['service_endpoint_type'];
    $service->service_description = $prevData['service_description'];
    $service->service_owner = $prevData['service_owner'];
    $service->service_visibility = $prevData['service_visibility'];
    $service->service_product_id = $prevData['service_product_id'];
    $service->service_suite_id = $prevData['service_suite_id'];

    $service->service_roles = $prevData['service_roles'];
    $service->service_levels = $prevData['service_levels'];
    $service->service_protocol = $prevData['service_protocol'];

    $_SESSION['service_data'] = null; unset($_SESSION['service_data']);
}
$user_products = getUserProductsAndServices();
$user_test_suites = get_suites_with_claims();
?>
    <div class="content edit-item-wrapper" id="edit_product_service_wrapper">
        <div class="column container">
            <form name="psForm" id="psForm" action="" class="validation-form" method="post" enctype="multipart/form-data">
                <?php if($isNew){ ?>
                    <h2>Add Service</h2>
                <?php }else{ ?>
                    <h2>Edit Service: <?php $service->service_name ?></h2>
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
                                <div class="grid-cell has-focus-tooltip">
                                    <label>Name:</label>
                                    <input type="text" class="input required" name="service_name" id="service_name" value="<?php echo $service->service_name?>" />
                                    <span class="focus-tooltip"><span></span>Enter your service name as it is known in the marketplace.</span>
                                </div>

                                <div class="grid-cell styled_select">
                                    <label>Product:</label>
                                    <select name="product_id" class="required" id="product_id">
                                        <option value=""></option>
                                        <?php foreach( $user_products AS $user_product ):?>
                                            <?php
                                                $product = new ProductAndService( $user_product->ID );
                                                $product->load();
                                            ?>
                                            <option <?php if( isset( $service->service_product_id ) && $service->service_product_id == $user_product->ID ):?> selected="selected" <?php endif;?>value="<?php echo $user_product->ID;?>" data-permission="<?php echo $product->services_not_permitted;?>"><?php echo $user_product->product_name;?></option>
                                        <?php endforeach;?>
                                    </select>
                                </div>

                                <div class="grid-cell styled_select has-focus-tooltip">
                                    <label>Test Suite:</label>
                                    <select name="suite_id" id="suite_id" class="required">
                                        <option></option>
                                        <?php foreach( $user_test_suites AS $suite_id ):?>
                                        <?php
                                            $suite = new TestSuite( $suite_id->suite_id );
                                            $suite->load();
                                            if( ! $suite->id ){
                                                continue;
                                            }
                                        ?>
                                            <option <?php if( isset( $service->service_suite_id ) && $service->service_suite_id == $suite_id->suite_id ):?>selected="selected"<?php endif;?> value="<?php echo $suite_id->suite_id;?>" data-productid="<?php echo $suite_id->product_id;?>" <?php if( $service->service_suite_id != $suite_id->suite_id || $service->service_product_id != $suite_id->product_id ):?>style="display: none;"<?php endif;?>><?php echo $suite->title;?></option>
                                        <?php endforeach;?>
                                    </select>
                                    <span class="focus-tooltip" style="left: 110%"><span></span>Form data entry - ABN or USI.</span>
                                </div>
                                <div class="clear"></div>
                            </div>
                            <div class="field-row">
                                <div class="grid-cell has-focus-tooltip">
                                    <label>Service ID:</label>
                                    <input type="text" class="input required" name="service_id" id="service_id" value="<?php echo $service->service_id;?>" />
                                    <span class="focus-tooltip" style="left: 110%"><span></span>Form data entry - ABN or USI.</span>
                                </div>
                                <div class="grid-cell has-focus-tooltip">
                                    <label>EndPoint URL:</label>
                                    <input type="text" class="input required" name="product_url" id="product_url" value="<?php echo $service->service_endpoint?>" />
                                    <span class="focus-tooltip"><span></span>Provide a link to the page on your website that describes this product or service.</span>
                                </div>
                                <div class="grid-cell styled_select">
                                    <label>EndPoint Type:</label>
                                    <select name="endpoint_type" class="required">
                                        <option></option>
                                        <option <?php if( $service->service_endpoint_type == 'Alias' ):?> selected="selected" <?php endif;?> value="Alias">Alias</option>
                                        <option <?php if( $service->service_endpoint_type == 'URL' ):?> selected="selected" <?php endif;?> value="URL">URL</option>
                                        <option <?php if( $service->service_endpoint_type == 'eMail' ):?> selected="selected" <?php endif;?> value="eMail">eMail</option>
                                    </select>
                                </div>
                                <div class="clear"></div>
                            </div>
                            <div class="field-row">
                                <div class="grid-cell" style="width: 278px;">
                                    <div class="styled_select">
                                        <label>Visibility:</label>
                                        <select name="visibility">
                                            <option <?php if( $service->service_visibility == 'Public' ):?> selected="selected" <?php endif;?> value="Public">Public</option>
                                            <option <?php if( $service->service_visibility == 'Community' ):?> selected="selected" <?php endif;?> value="Community">Community</option>
                                            <option <?php if( $service->service_visibility == 'Private' ):?> selected="selected" <?php endif;?> value="Private">Private</option>
                                        </select>
                                        <div class="space10"></div>
                                    </div>
                                    <div class="styled_select">
                                        <label>Protocol:</label>
                                        <select name="protocol">
                                            <option <?php if( $service->service_protocol == 'AS4-Gateway' ):?> selected="selected" <?php endif;?> value="AS4-Gateway">AS4-Gateway</option>
                                            <option <?php if( $service->service_protocol == 'AS4-LightClient' ):?> selected="selected" <?php endif;?> value="AS4-LightClient">AS4-LightClient</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid-cell has-focus-tooltip">
                                    <label>Description:</label>
                                    <textarea cols="" rows="" class="textarea" id="product_description" name="product_description"><?php echo $service->service_description?></textarea>
                                    <span class="focus-tooltip"><span></span>Provide a few paragraphs to describe your product or service. This information is displayed to users who may be searching CompliacneTest for certified products.</span>
                                </div>
                                <div class="clear"></div>
                            </div>
                            <div class="field-row">
                                <div class="grid-cell">
                                    <label>Service Owner:</label>
                                    <input type="text" class="input required" name="service_owner" id="service_owner" value="<?php echo $service->service_owner?>" />
                                </div>
                                <?php
                                    $processed_suites = array();
                                    foreach( $user_test_suites AS $suite_id ):?>
                                    <?php
                                    $suite = new TestSuite( $suite_id->suite_id );
                                    $suite->load();
                                    if( ! $suite->id ){
                                        continue;
                                    }
                                    if( ! in_array( $suite->id, $processed_suites ) ){
                                        array_push( $processed_suites, $suite->id );
                                    } else{
                                        continue;
                                    }
                                    ?>
                                        <div class="grid-cell">
                                            <label>Roles:</label>
                                            <?php foreach( $suite->roles AS $role ):?>
                                                <div class="roles_div" data-suiteid="<?php echo $suite->id;?>" <?php if( $isNew || $suite->id !== $service->service_suite_id ):?>style="display: none;"<?php endif;?>>
                                                    <input type="checkbox" name="roles[]" <?php if( ( $isNew || $suite->id !== $service->service_suite_id  ) || in_array( $role['name'], $service->service_roles ) ):?>checked="checked" <?php endif;?>value="<?php echo $role['name'];?>"><?php echo $role['name'];?>
                                                </div>
                                            <?php endforeach;?>
                                        </div>
                                        <div class="grid-cell">
                                            <label>Levels:</label>
                                            <?php foreach( $suite->conformanceLevel AS $level ):?>
                                                <div class="levels_div" data-suiteid="<?php echo $suite->id;?>" <?php if( $isNew || $suite->id !== $service->service_suite_id ):?>style="display: none;"<?php endif;?>>
                                                    <input type="checkbox" data-suiteid="<?php echo $suite->id;?>" name="levels[]" <?php if( ( $isNew || $suite->id !== $service->service_suite_id  ) || in_array( $level['code'], $service->service_levels ) ):?>checked="checked" <?php endif;?> value="<?php echo $level['code'];?>"><?php echo $level['code'];?>
                                                </div>
                                            <?php endforeach;?>
                                        </div>
                                    <?php endforeach;?>
                                <div class="clear"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="space20"></div>
                <div class="grid-box">
                    <div class="grid-box-footer nobackground noshadow">
                        <div class="btn-row nopaddingleft">
                            <a href="#" class="action-btn process-btn submit-btn"><span class="p"></span><span class="t">SAVE SERVICE</span></a>
                            <a href="javascript: history.go(-1)" class="action-btn cancel-btn"><span class="p"></span><span class="t">Cancel</span></a>
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="id" value="<?php echo $service->id?>" />
                <?php
                wp_nonce_field('save-service', '_psnonce');
                ?>
            </form>
        </div>
        <div class="clear"></div>
    </div>
    <a id="show_permissions_popup" href="#cant_create_service" rel="custom-popup" style="display: none;"></a>
    <div class="popup-box" id="cant_create_service" style="display: none; width: 500px">
        <div class="popup-box-header radius6 noradiusbottom">Services Not Permitted</div>
        <div class="popup-box-content">
            <p>Creation of services for this product is not permitted. Please contact the site administrator for further information.</p>
        </div>
        <div class="popup-box-footer radius6 noradiustop">
            <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>
            <div class="clear"></div>
        </div>
        <a class="close_btn"></a>
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function($){

            $('#suite_id').on('change', function(){
                $('.roles_div').hide();
                $('.levels_div').hide();
                if( $( this).val() ){
                    $('.roles_div').filter( '[data-suiteid="'+$( this).val()+'"]').show();
                    $('.levels_div').filter( '[data-suiteid="'+$( this).val()+'"]').show();
                }
            });
            $('#product_id').on('change', function(){
                $('#suite_id').val('');
                $('#suite_id option').hide();
                $('#suite_id option').filter('[data-productid="'+$(this).val()+'"]').show();
            });
            jQuery(".validation-form .required").each(function(){
                jQuery(this).parent().append('<span class="msg-required" style="display: none">This field is required.</span>');
            })

            jQuery('#psForm .input').focus(function(){
                $(this).removeClass('input-error');
            })

            var forceSubmit = false;
            jQuery('#psForm').submit(function(){
                if( $('#product_id option:selected').data('permission') == '1' ){
                    $('#show_permissions_popup').click();
                    return false;
                }
                $('#psForm .grid-box-footer .message').remove();
                var isValid = true;
                var errorMsg = '';

                jQuery(this).find('input.required').each(function(){
                    if(jQuery(this).val() == ''){
                        isValid = false;
                        jQuery(this).addClass('input-error');
                    }
                });
                jQuery(this).find('select.required').each(function(){
                    if(jQuery(this).val() == ''){
                        isValid = false;
                        jQuery(this).addClass('select-error');
                    }
                });
                if(!isValid)
                {
                    $('#psForm .grid-box-footer').append('<div class="message error" style="display: none">Please complete fields in red.</div>');
                    $('#psForm .grid-box-footer .message').fadeIn('fast');
                    return false;
                }


                //Validate product URL
                if (jQuery("#product_url").val() != '' && !isValidUrl(jQuery("#product_url").val())){
                    isValid = false;
                    jQuery("#product_url").addClass('input-error');
                    $('#psForm .grid-box-footer').append('<div class="message error" style="display: none">Please enter valid access url.</div>');
                    $('#psForm .grid-box-footer .message').fadeIn('fast');
                    return false;
                }

                //Product ID Validation
                if($('#psForm #service_id').val() != '')
                {
                    if(! /\d/.test($('#psForm #service_id').val()) )
                    {
                        $('#psForm .grid-box-footer').append('<div class="message warning" style="display: none">Product ID may only contain numbers.</div>');
                        jQuery("#product_id").addClass('input-error');
                        $('#psForm .grid-box-footer .message').fadeIn('fast');
                        return false;
                    }
                }

                return isValid;

            });
        })

    </script>
<?php

get_footer();