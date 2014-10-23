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
if( ! $wpdb->get_row( $wpdb->prepare( "SELECT * FROM wp_users_privileges WHERE user_id = %d AND privilege_id = 4 ", get_current_user_id() ) ) ){
    addMessage('You do not have the "' . ct_get_privilege_by_code('MAKE_AGREEMENTS', 'title') . '" privilege necessary for this action. Please contact your organisation administrator for the ComplianceTest site.', 'error');
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

$myServices = getUserServices(null, $isNew ? array() : array($psID));

if( isset($_SESSION['service_data'] ) )
{
    $prevData = $_SESSION['service_data'];
    //Restore the previous form data
    $service->service_name = $prevData['service_name'];
//    $service->service_endpoint = $prevData['service_endpoint'];
//    $service->service_endpoint_type = $prevData['service_endpoint_type'];
    $service->service_endpoint = $prevData['service_endpoint'];
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
                                    <input type="text" class="input" name="service_name" id="service_name" value="<?php echo $service->service_name?>" />
                                    <span class="focus-tooltip"><span></span>Enter the service name you would like to appear in search results.</span>
                                    <br>
                                    <label class="default_name" <?php if( $service->service_name ):?>style="display: none;" <?php endif;?>>Default: <span class="process_value">{Process}</span>:<span class="process_role">{Role}</span></label>
                                </div>

                                <div class="grid-cell styled_select">
                                    <label>Product:</label>
                                    <select name="product_id" class="required select" style="width: 150px" id="product_id">
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
                                    <select name="suite_id" id="suite_id" class="required select" style="width: 150px">
                                        <option></option>
                                        <?php $suites_in_list = array();?>
                                        <?php foreach( $user_test_suites AS $suite_id ):?>
                                        <?php
                                            $suite = new TestSuite( $suite_id->suite_id );
                                            $suite->load();
                                            if( ! $suite->id ){
                                                continue;
                                            }
                                            if( ! in_array( $suite_id->suite_id.$suite_id->product_id, $suites_in_list ) ){
                                                array_push( $suites_in_list, $suite_id->suite_id.$suite_id->product_id );
                                            } else{
                                                continue;
                                            }
                                        ?>
                                            <option <?php if( isset( $service->service_suite_id ) && $service->service_suite_id == $suite_id->suite_id ):?>selected="selected"<?php endif;?> value="<?php echo $suite_id->suite_id;?>" data-productid="<?php echo $suite_id->product_id;?>" data-process="<?php echo Process::get_full_name( Process::get_process_by_id( $suite->process ) );?>" <?php if( $service->service_suite_id != $suite_id->suite_id || $service->service_product_id != $suite_id->product_id ):?>style="display: none;"<?php endif;?>><?php echo $suite->title;?></option>
                                        <?php endforeach;?>
                                    </select>
                                    <span class="focus-tooltip" style="left: 110%"><span></span>Test suite name.</span>
                                </div>
                                <div class="grid-cell">
                                    <label>Process:</label>
                                    <input type="text" class="input" name="service_process" id="service_process" value="<?php if( $service->service_suite_id ) echo Process::get_full_name( Process::get_process_by_id( $suite->process ) );?>" readonly="readonly"  style="width: 200px;"/>
                                </div>
                                <div class="clear"></div>
                            </div>
                            <div class="field-row">
                                <div class="grid-cell has-focus-tooltip">
                                    <label>Entity ID:</label>
                                    <input type="text" class="input required" name="service_id" id="service_id" value="<?php echo $service->service_id;?>" />
                                    <span class="focus-tooltip" style="left: 110%"><span></span>The identifier of the organisation that is the service provider.</span>
                                </div>
                                <div class="grid-cell styled_select has-focus-tooltip">
                                    <label>Type:</label>
                                    <select name="type" id="type" class="required select width250">
                                        <option></option>
                                        <option <?php if( isset( $service->service_type ) && $service->service_type == 'ABN' ):?>selected="selected"<?php endif;?> value="ABN" >ABN</option>
                                        <option <?php if( isset( $service->service_type ) && $service->service_type == 'USI' ):?>selected="selected"<?php endif;?> value="USI" >USI</option>
                                    </select>
                                    <span class="focus-tooltip" style="left: 110%"><span></span>Test suite name.</span>
                                </div>
                                <div class="grid-cell styled_select gateways_list gateways" <?php if( empty( $service->service_type ) || $service->service_type == 'ABN' ):?>style="display: none;"<?php endif;?>>
                                    <label>Gateway:</label>
                                    <?php $endpoints = $wpdb->get_results(  "SELECT * FROM wp_gateways" );?>
                                    <select name="endpoint_type" class="required select width250" id="gateways">
                                        <option></option>
                                        <?php foreach( $endpoints AS $endpoint ):?>
                                            <option <?php if( $service->service_endpoint == $endpoint->gateway_id ):?> selected="selected" <?php endif;?> value="<?php echo $endpoint->gateway_id;?>"><?php echo $endpoint->name;?></option>
                                        <?php endforeach;?>
                                    </select>
                                </div>
                                <div class="grid-cell styled_select gateways_list aliases" <?php if( empty( $service->service_type ) || $service->service_type == 'USI' ):?>style="display: none;"<?php endif;?>>
                                    <label>Alias:</label>
                                    <?php $aliases = $wpdb->get_results(  "SELECT * FROM wp_gateways" );?>
                                    <select name="endpoint_type_alias" class="required select" id="aliases">
                                        <option></option>
                                        <?php foreach( $aliases AS $aliase ):?>
                                            <?php $al = explode( '|', $aliase->alias_list );?>
                                            <?php foreach( $al AS $a ):?>
                                                <option <?php if( $service->service_endpoint == $a ):?> selected="selected" <?php endif;?> value="<?php echo $a;?>"><?php echo $a;?></option>
                                            <?php endforeach;?>
                                        <?php endforeach;?>
                                    </select>
                                </div>

                                <div class="clear"></div>
                            </div>
                            <div class="field-row">
                                <div class="grid-cell" style="width: 278px;">
                                    <div class="styled_select">
                                        <label>Visibility:</label>
                                        <select name="visibility" class="select">
                                            <option <?php if( $service->service_visibility == 'Public' ):?> selected="selected" <?php endif;?> value="Public">Public</option>
                                            <option <?php if( $service->service_visibility == 'Community' ):?> selected="selected" <?php endif;?> value="Community">Community</option>
                                            <option <?php if( $service->service_visibility == 'Private' ):?> selected="selected" <?php endif;?> value="Private">Private</option>
                                        </select>
                                        <div class="space10"></div>
                                    </div>
                                    <div class="styled_select" style="display: none;">
                                        <label>Protocol:</label>
                                        <select name="protocol" class="select">
                                            <option selected="selected" value="Gateway">Gateway</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid-cell has-focus-tooltip width60P">
                                    <label>Description:</label>
                                    <textarea cols="" rows="" class="textarea" id="product_description" name="product_description"><?php echo $service->service_description?></textarea>
                                    <span class="focus-tooltip"><span></span>Provide a few paragraphs to describe your service. This information is displayed to users who may be searching ComplianceTest for Services with which to test.</span>
                                </div>
                                <div class="clear"></div>
                            </div>
                            <div class="field-row">
                                <div class="grid-cell has-focus-tooltip">
                                    <label>Owner:</label>
                                    <input type="text" class="input required" name="service_owner" id="service_owner" value="<?php echo $service->service_owner?>" />
                                    <span class="focus-tooltip">Enter the name of the organisation responsible for the service.</span>
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
                                            $claims = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM wp_compliance_claims WHERE suite_id = %d ", $suite->id ) );
                                        ?>
                                            <div class="grid-cell">
                                                <label>Roles:</label>
                                                <?php foreach( $claims AS $claim ):?>
                                                    <?php $roles = explode( ';;', trim( $claim->role, ';;' ) );?>
                                                    <?php foreach( $roles AS $role ):?>
                                                        <div class="roles_div" data-suiteid="<?php echo $suite->id.'_'.$claim->product_id;?>" <?php if( $isNew || ( $suite->id !== $service->service_suite_id || $claim->product_id != $service->service_product_id ) ):?>style="display: none;"<?php endif;?>>
                                                            <span class="radio-checkbox-holder">
                                                                <input type="radio" name="roles[]" <?php if(  ( in_array( $role, $service->service_roles ) && $service->service_product_id == $claim->product_id  ) ):?> checked="checked" <?php endif;?>value="<?php echo $role;?>">
                                                                <span><?php echo $role;?></span>
                                                            </span>
                                                        </div>
                                                    <?php endforeach;?>
                                                <?php endforeach;?>
                                            </div>
                                            <div class="grid-cell">
                                                <label>Levels:</label>
                                                <?php foreach( $claims AS $claim ):?>
                                                    <?php $levels = explode( ';;', trim( $claim->conformance_level, ';;' ) );?>
                                                    <?php foreach( $levels AS $level ):?>
                                                        <?php if( $level == 'Default' ) continue;?>
                                                        <div class="levels_div" data-suiteid="<?php echo $suite->id.'_'.$claim->product_id;?>" <?php if( $isNew || ( $suite->id !== $service->service_suite_id || $claim->product_id != $service->service_product_id ) ):?>style="display: none;"<?php endif;?>>
                                                            <span class="radio-checkbox-holder">
                                                                <input type="checkbox" data-suiteid="<?php echo $suite->id;?>" name="levels[]" <?php if( ( in_array( $level, $service->service_levels ) && $service->service_product_id == $claim->product_id ) ):?>checked="checked" <?php endif;?> value="<?php echo $level;?>">
                                                                <span><?php echo $level;?></span>
                                                            </span>
                                                        </div>
                                                    <?php endforeach;?>
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
                            <a href="#" class="action-btn process-btn submit-btn has-tooltip" title="Save Service"><span class="p"></span><span class="t">Confirm</span></a>
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
            $('#product_description').redactor({
                  air: true,
                  minHeight: 80
                  
            });
            $('#suite_id, #product_id').on('change', function(){
                $('.roles_div').hide();
                $('.levels_div').hide();
                if( $( this).val() && $('#product_id').val() ){
                    $('.roles_div').filter( '[data-suiteid="'+$( this).val()+'_'+ $('#product_id').val() +'"]').show();
                    $('.levels_div').filter( '[data-suiteid="'+$( this).val()+'_'+ $('#product_id').val() +'"]').show();
                }
                if( $(this).val() ){
                    $('#service_process').val( $('#suite_id option:selected').attr('data-process') );
                } else{
                    $('#service_process').val( '' );
                }
                change_values();
            });
            $('input[name="roles[]"]').on('change', function(){
                change_values();
            });
            $('#product_id').on('change', function(){
                $('#suite_id').val('');
                $('#suite_id option').hide();
                $('#suite_id option').filter('[data-productid="'+$(this).val()+'"]').show();
            });
            $('#service_name').on('change', function(){
                change_values();
            });
            function change_values(){
                if( $('#service_name').val() ){
                    $('.default_name').hide();
                } else{
                    $('.default_name').show();
                    if( $('#service_process').val() ){
                        $('.process_value').html( $('#service_process').val() );
                    }
                    if( $('#suite_id').val() ){
                        $('.process_role').html( $('.roles_div').filter( '[data-suiteid="'+$('#suite_id').val()+'_'+ $('#product_id').val() +'"]').find('input:checked').val() );
                    }
                }
            }
            jQuery(".validation-form .required").each(function(){
                jQuery(this).parent().append('<span class="msg-required" style="display: none">This field is required.</span>');
            })

            jQuery('#psForm .input').focus(function(){
                $(this).removeClass('input-error');
            })
            jQuery('#type').on('change', function(){
                jQuery('.gateways_list').hide();
               if( jQuery(this).val() ){
                   if( jQuery(this).val() == 'ABN' ){
                        jQuery('.aliases').show();
                   } else if( jQuery(this).val() == 'USI' ){
                       jQuery('.gateways').show();
                   }
               }
            });
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
                    if( jQuery(this).attr( 'id' ) == 'gateways' &&  jQuery('#type').val() == 'ABN' ){
                        return false;
                    }
                    if( jQuery(this).attr( 'id' ) == 'aliases' &&  jQuery('#type').val() == 'USI' ){
                        return false;
                    }
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
//                if (jQuery("#product_url").val() != '' && !isValidUrl(jQuery("#product_url").val())){
//                    isValid = false;
//                    jQuery("#product_url").addClass('input-error');
//                    $('#psForm .grid-box-footer').append('<div class="message error" style="display: none">Please enter valid access url.</div>');
//                    $('#psForm .grid-box-footer .message').fadeIn('fast');
//                    return false;
//                }

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