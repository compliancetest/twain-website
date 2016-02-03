<?php
/**
 * Template Name:Add/Edit Service
 */


$psID = isset($_GET['id']) ? $_GET['id'] : null;

$isNew = true;

$service = new Service( $psID );
$service ->load();
if( ! $service->id )
    $isNew = true;
else
    $isNew = false;
    
if ( ! Service::can_edit( get_current_user_id(), $service->id ) ) {
    addMessage('You do not have the "' . ct_get_privilege_by_code('MAKE_AGREEMENTS', 'title') . '" privilege necessary for this action. Please contact your organisation administrator for the '.get_option('tw_site_title').' site.', 'error');

    wp_redirect("/");
    exit;
}
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
                                <b style="font-size: larger;">Owning Entity</b>
                            </div>
                            <div class="field-row">
                                <div class="grid-cell">
                                    <label>Entity Name:</label>
                                    <div class="has-field-tooltip">
                                        <input type="text" class="input required field-tooltip" name="service_owner" id="service_owner" value="<?php echo $service->service_owner?>" data-tooltip-content="The business entity that hosts this service - eg an employer name or fund product name." />
                                    </div>
                                </div>

                                <div class="grid-cell">
                                    <label>Entity Identifier:</label>
                                    <div class="has-field-tooltip">
                                        <input type="text" class="input required field-tooltip" name="service_id" id="service_id" value="<?php echo $service->service_id;?>" data-tooltip-content="The unique identifier for the entity - eg an employer ABN or fund product USI." />
                                    </div>
                                </div>

                                <div class="grid-cell styled_select">
                                    <label>Entity Type:</label>
                                    <div class="has-field-tooltip">
                                        <select name="type" id="type" class="required select width250 field-tooltip" data-tooltip-content="The type of identifier for the entity - please select the appropriate code">
                                            <option></option>
                                            <option <?php if( isset( $service->service_type ) && $service->service_type == 'ABN' ):?>selected="selected"<?php endif;?> value="ABN" >ABN</option>
                                            <option <?php if( isset( $service->service_type ) && $service->service_type == 'USI' ):?>selected="selected"<?php endif;?> value="USI" >USI</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="padding20"></div>
                                <div class="clear"></div>
                            </div>

                            <hr style="height:3px;border:none;color:lightgrey;background-color:lightgrey; margin-top: 20px;margin-bottom: 20px;" />

                            <div class="field-row">
                                <b style="font-size: larger;">Service Specification</b>
                            </div>

                            <div class="field-row">
                                <div class="grid-cell">
                                    <label>Service Name:</label>
                                    <div class="has-field-tooltip">
                                        <input type="text" class="input field-tooltip" name="service_name" id="service_name" value="<?php echo $service->service_name?>" data-tooltip-content="A name for the service - if left blank this will default to the 'suite:role' you select below - eg Contributions_v1.3:Fund" />
                                    </div>
                                    <br>
                                    <label class="default_name" <?php if( $service->service_name ):?>style="display: none;" <?php endif;?>>Default: <span class="process_value">{Suite}</span>:<span class="process_role">{Role}</span></label>
                                    <div class="styled_select" style="margin-top: 10px">
                                        <label>Visibility:</label>
                                        <div class="has-field-tooltip">
                                            <select name="visibility" class="select field-tooltip" data-tooltip-content="'Public' means anyone can see this service, 'Community' means visibility is limited to community members, 'Private' means that the service is only visible to your own organisation">
                                                <option <?php if( $service->service_visibility == 'Public' ):?> selected="selected" <?php endif;?> value="Public">Public</option>
                                                <option <?php if( $service->service_visibility == 'Community' ):?> selected="selected" <?php endif;?> value="Community">Community</option>
                                                <option <?php if( $service->service_visibility == 'Private' ):?> selected="selected" <?php endif;?> value="Private">Private</option>
                                            </select>
                                        </div>
                                        <div class="space10"></div>
                                    </div>
                                </div>

                                <div class="grid-cell width60P">
                                    <label>Service Description:</label>
                                    <div class="has-defined-tooltip">
                                        <textarea cols="" rows="" class="textarea" id="product_description" name="product_description"><?php echo $service->service_description?></textarea>
                                        <span style="width: 535px; margin-left: -270px; bottom: 112px; display: none;" class="simple_tooltip">Enter a brief description including any special instructions or limitations - eg 'Our SuperStream Contributions service. Contributions must include Member ID (SuperannuationFundDetails.MemberClient.Identifier) and registrations must include insurance salary (SuperannuationFundDetails.AnnualSalaryforInsurance.Amount)<span></span></span>
                                    </div>
                                </div>

<!--                                <div class="grid-cell">-->
<!--                                    <label>Process:</label>-->
<!--                                    <input type="text" class="input" name="service_process" id="service_process" value="--><?php //if( $service->service_suite_id ) echo Process::get_full_name( Process::get_process_by_id( $suite->process ) );?><!--" readonly="readonly"  style="width: 200px;"/>-->
<!--                                </div>-->
                                <div class="clear"></div>
                            </div>
                            <div class="field-row">

                                <div class="grid-cell styled_select">
                                    <label>Based on Certified Software Product:</label>
                                    <div class="has-field-tooltip">
                                        <select name="product_id" class="required select field-tooltip" style="width: 280px" id="product_id" data-tooltip-content="Choose a certified software product which has been used as the basis for this service - eg your payroll solution or your fund registry solution">
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
                                </div>

                                <div class="grid-cell styled_select">
                                    <label>Supports Test Suite:</label>
                                    <div class="has-field-tooltip">
                                        <select name="suite_id" id="suite_id" class="required select field-tooltip" style="width: 150px" data-tooltip-content="This list is pre-populated based on the certifications of the supporting product - if there is no value shown then the product has not yet been certified">
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
                                                if( $service->service_suite_id != $suite_id->suite_id || $service->service_product_id != $suite_id->product_id ) {
                                                    continue;
                                                }
                                                ?>
                                                <option <?php if( isset( $service->service_suite_id ) && $service->service_suite_id == $suite_id->suite_id ):?>selected="selected"<?php endif;?> value="<?php echo $suite_id->suite_id;?>" data-productid="<?php echo $suite_id->product_id;?>" data-process="<?php echo Process::get_full_name( Process::get_process_by_id( $suite->process ) );?>"><?php echo $suite->title;?></option>
                                            <?php endforeach;?>
                                        </select>
                                    </div>
                                    <?php $suites_in_list = array();?>
                                    <select id="all_suites_id" class="required select" style="display: none">
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
                                            <option value="<?php echo $suite_id->suite_id;?>" data-productid="<?php echo $suite_id->product_id;?>" data-process="<?php echo Process::get_full_name( Process::get_process_by_id( $suite->process ) );?>"><?php echo $suite->title;?></option>
                                        <?php endforeach;?>
                                    </select>
                                </div>
<!--                                <div class="grid-cell styled_select gateways_list gateways" --><?php //if( empty( $service->service_type ) || $service->service_type == 'ABN' ):?><!--style="display: none;"--><?php //endif;?><!-->
<!--                                    <label>Gateway:</label>-->
<!--                                    --><?php //$endpoints = $wpdb->get_results(  "SELECT * FROM wp_gateways" );?>
<!--                                    <select name="endpoint_type" class="required select width250" id="gateways">-->
<!--                                        <option></option>-->
<!--                                        --><?php //foreach( $endpoints AS $endpoint ):?>
<!--                                            <option --><?php //if( $service->service_endpoint == $endpoint->gateway_id ):?><!-- selected="selected" --><?php //endif;?><!-- value="--><?php //echo $endpoint->gateway_id;?><!--">--><?php //echo $endpoint->name;?><!--</option>-->
<!--                                        --><?php //endforeach;?>
<!--                                    </select>-->
<!--                                </div>-->
<!--                                <div class="grid-cell styled_select gateways_list aliases" --><?php //if( empty( $service->service_type ) || $service->service_type == 'USI' ):?><!--style="display: none;"--><?php //endif;?><!-->
<!--                                    <label>Alias:</label>-->
<!--                                    --><?php //$aliases = $wpdb->get_results(  "SELECT * FROM wp_gateways" );?>
<!--                                    <select name="endpoint_type_alias" class="required select" id="aliases">-->
<!--                                        <option></option>-->
<!--                                        --><?php //foreach( $aliases AS $aliase ):?>
<!--                                            --><?php //$al = explode( '|', $aliase->alias_list );?>
<!--                                            --><?php //foreach( $al AS $a ):?>
<!--                                                <option --><?php //if( $service->service_endpoint == $a ):?><!-- selected="selected" --><?php //endif;?><!-- value="--><?php //echo $a;?><!--">--><?php //echo $a;?><!--</option>-->
<!--                                            --><?php //endforeach;?>
<!--                                        --><?php //endforeach;?>
<!--                                    </select>-->
<!--                                </div>-->
                                <?php
                                $roles_html = $levels_html = '';
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
                                    <?php $processed_roles = array();?>
                                    <?php ob_start();?>
                                    <?php foreach( $claims AS $claim ):?>
                                        <?php $roles = explode( ';;', trim( $claim->role, ';;' ) );?>
                                        <?php foreach( $roles AS $role ):?>
                                            <?php
                                            if( in_array( $role.$claim->product_id, $processed_roles ) ) {
                                                continue;
                                            }else{
                                                array_push( $processed_roles, $role.$claim->product_id );
                                            }
                                            ?>
                                            <div class="roles_div" data-suiteid="<?php echo $suite->id.'_'.$claim->product_id;?>" <?php if( $isNew || ( $suite->id !== $service->service_suite_id || $claim->product_id != $service->service_product_id ) ):?>style="display: none;"<?php endif;?>>
                                                                <span class="radio-checkbox-holder">
                                                                    <input type="radio" name="roles[]" <?php if( is_array( $service->service_roles ) && ( in_array( $role, $service->service_roles ) && $service->service_product_id == $claim->product_id  ) ):?> checked="checked" <?php endif;?>value="<?php echo $role;?>">
                                                                    <span><?php echo $role;?></span>
                                                                </span>
                                            </div>
                                        <?php endforeach;?>
                                    <?php endforeach;?>
                                    <?php $roles_html .= ob_get_clean();?>

                                    <?php $processed_levels = array();?>
                                    <?php ob_start();?>
                                    <?php foreach( $claims AS $claim ):?>
                                        <?php $levels = explode( ';;', trim( $claim->conformance_level, ';;' ) );?>
                                        <?php foreach( $levels AS $level ):?>
                                            <?php
                                            if( in_array( $level.$claim->product_id, $processed_levels ) ) {
                                                continue;
                                            }else{
                                                array_push( $processed_levels, $level.$claim->product_id );
                                            }
                                            ?>
                                            <?php if( $level == 'Default' ) continue;?>
                                            <div class="levels_div" data-suiteid="<?php echo $suite->id.'_'.$claim->product_id;?>" <?php if( $isNew || ( $suite->id !== $service->service_suite_id || $claim->product_id != $service->service_product_id ) ):?>style="display: none;"<?php endif;?>>
                                                                <span class="radio-checkbox-holder">
                                                                    <input type="radio" data-suiteid="<?php echo $suite->id;?>" name="levels[]" <?php if( is_array( $service->service_levels ) && ( in_array( $level, $service->service_levels ) && $service->service_product_id == $claim->product_id ) ):?>checked="checked" <?php endif;?> value="<?php echo $level;?>">
                                                                    <span><?php echo $level;?></span>
                                                                </span>
                                            </div>
                                        <?php endforeach;?>
                                    <?php endforeach;?>
                                    <?php $levels_html .= ob_get_clean();?>
                                <?php endforeach;?>
                                <div class="grid-cell has-tooltip" title="Pick the role that represents this service implementation - eg employer, smsf, fund, clearing house. The roles shown will be those for which the supporting product has been certified.">
                                    <label>Supports Role:</label>
                                    <?php echo $roles_html;?>
                                </div>
                                <div class="grid-cell has-tooltip" title="Pick the conformance level(s) that your service supports. These will usually be the same as your supporting product but could be a subset (eg of the product supports response messages but your service does not)">
                                    <label>Supports Levels:</label>
                                    <?php echo $levels_html;?>
                                </div>
                                <div class="clear"></div>
                            </div>
<!--                            <div class="field-row">-->
<!--                                <div class="grid-cell" style="width: 278px;">-->
<!---->
<!--                                    <div class="styled_select" style="display: none;">-->
<!--                                        <label>Protocol:</label>-->
<!--                                        <select name="protocol" class="select">-->
<!--                                            <option selected="selected" value="Gateway">Gateway</option>-->
<!--                                        </select>-->
<!--                                    </div>-->
<!--                                </div>-->
<!---->
<!--                                <div class="clear"></div>-->
<!--                            </div>-->
                            <div class="field-row">


                                <div class="clear"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="space20"></div>

                <div class="grid-box">
                    <div class="grid-box-footer nobackground noshadow">
                        <div class="btn-row nopaddingleft">
                            <a href="#" class="action-btn process-btn submit-btn has-tooltip"><span class="simple_tooltip radius6" style="margin-left: -50px; width: 90px;">Save Service<span></span></span><span class="p"></span><span class="t">Confirm</span></a>
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
            <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Close</span></a>
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
            $('#product_id').on('change', function(){
                $('#suite_id option:gt(0)').remove();
                
                $('#all_suites_id option').each(function(){
                    if ($(this).attr('data-productid') == $('#product_id').val()) {
                        $('#suite_id').append($(this).clone());
                    }
                })
                
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
            $('#suite_id').on('change', function(){
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
                    if( $('#suite_id').val() ){
                        $('.process_value').html( $('#suite_id').text().trim().replace(/\s+/g,"_") );
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
                    if(jQuery(this).val().trim() == ''){
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
                if( jQuery('input[name="roles[]"]:checked').length == 0 ){
                    isValid = false;
                    jQuery('input[name="roles[]"]').addClass('radio-error');
                }
                if( jQuery('input[name="levels[]"]:checked').length == 0 ){
                    isValid = false;
                    jQuery('input[name="levels[]"]').addClass('radio-error');
                }
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