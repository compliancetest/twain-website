<?php
/**
 * Service Content
 */
?>
<?php
$prev_page = wp_get_referer() ? wp_get_referer() : '/';
?>
<div class="product-page">
    <div class="page-title-block">
        <?php if(!$isAjax){ ?>
            <div class="page-title clearfix">
                <h4>Service Details</h4>
                <a href="<?php echo $prev_page;?>" class="left action-btn back-btn has-tooltip left15" title="Back to Services">
                    <span class="p"></span>
                    <span class="t">Back</span>
                </a>
                <div class="page-title-block-actions">
                    <?php if(can_edit_product_and_service(get_the_ID())){ ?>
                        <?php if( ! $wpdb->get_row( $wpdb->prepare( "SELECT * FROM wp_users_privileges WHERE user_id = %d AND privilege_id = 4 ", get_current_user_id() ) ) ):?>
                            <a  href="/?cp-action=<?php echo wp_create_nonce("insufficient-privilege") ?>&privilege=<?php echo base64_encode('MAKE_AGREEMENTS')?>" rel="custom-popup" cp-type="ajax" cp-removeBoxAfterClose=1 class="action-btn edit-btn right"><span class="p"></span><span class="t">Edit</span></a>
                        <?php else:?>
                            <a href="/edit-service?id=<?php echo $service->id?>" class="action-btn edit-btn right"><span class="p"></span><span class="t">Edit</span></a>
                        <?php endif;?>
                    <?php } ?>
                    <?php if( is_user_logged_in() && $service->service_user_id != get_current_user_id() ):?>
                        <?php if( ! $wpdb->get_row( $wpdb->prepare( "SELECT * FROM wp_users_privileges WHERE user_id = %d AND privilege_id = 4 ", get_current_user_id() ) ) ):?>
                            <a  href="/?cp-action=<?php echo wp_create_nonce("insufficient-privilege") ?>&privilege=<?php echo base64_encode('MAKE_AGREEMENTS')?>" rel="custom-popup" cp-type="ajax" cp-removeBoxAfterClose=1 class="action-btn green-btn"><span class="t">Request E2E Test</span></a>
                        <?php else:?>
                            <a href="#e2e-test-request" class="action-btn green-btn e2e-test-request-link">Request E2E Test</a>
                        <?php endif;?>
                    <?php endif;?>
                    <a href="<?php echo addPrintParams(get_permalink(), 'service')?>" class="action-btn print-btn print-page-btn" id="print-product-btn"><span class="p"></span><span class="t">Print</span></a>
                </div>
            </div>
        <?php } ?>
        <div class="column nopaddingtop">
            <div class="product-data clearfix">
                <?php //todo-ivan: add ability upload thumbnails from frontend ?>
                <?php if (has_post_thumbnail()) { ?>
                    <div class="product-thumbnail">
                        <?php the_post_thumbnail('post-thumb', array('class' => 'prod_serv_details')); ?>
                    </div>
                <?php } ?>
                <div class="product-info">
                    <div class="product-identifiers">
                        <div class="product-name">Name: <strong><?php echo $service->service_name;?></strong></div>
                        <div class="product-id">(ID: <strong><?php echo $service->service_type.':'.$service->service_id;?></strong>)</div>
                    </div>
                    <?php
                        $suite = new TestSuite( $service->service_suite_id );
                        $suite->load();
                    ?>    
                    <ul class="product-attributes">
                        <li>Owner: <strong><?php echo $service->service_owner?></strong></li>
                        <li>Process: <strong><?php echo Process::get_full_name( Process::get_process_by_id( $suite->process ) ) ;?></strong></li>
                        <li>Role: <strong><?php echo implode( ', ', $service->service_roles );?></strong></li>
                        <li>Level: <strong><?php echo implode( ', ', $service->service_levels );?></strong></li>
                                            
                        <li>Protocol: <strong><?php echo $service->service_protocol ;?></strong></li>
                        <?php if( $service->service_type == 'USI' ):?>
                            <?php $gateway = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM wp_gateways WHERE gateway_id = %d ", $service->service_endpoint ) );?>
                            <li>End-Point: <strong><a href="<?php echo $gateway->test_url;?>"><?php echo $gateway->name;?></a></strong></li>
                        <?php else:?>
                            <li>Alias: <strong><?php echo $service->service_endpoint;?></strong></li>
                        <?php endif;?>
                    </ul>
                    <div class="product-description"><?php echo $service->service_description;?></div>
                </div>
            </div>
            <div class="tabs-contr">
                <ul class="tab-nav">
                    <?php $is_active = true;?>
<!--                    --><?php //if( $service->service_related_services ):?>
<!--                        <li class="active">-->
<!--                            <a href="javascript: void(0)" rel="tab_related_services">Related Services</a>-->
<!--                        </li>-->
<!--                        --><?php //$is_active = false;?>
<!--                    --><?php //endif;?>
                    <li <?php if( $is_active ):?>class="active"<?php endif;?> >
                        <a href="javascript: void(0)" rel="tab_uses_products">Uses Products</a>
                    </li>
                </ul>
<!--                --><?php //if( $service->service_related_services ):?>
<!--                    <div class="tab-content" id="tab_related_services" style="display: block;">-->
<!--                        <dl class="column related_products">-->
<!--                            --><?php //if( $service->service_related_services ):?>
<!--                                --><?php //foreach ($service->service_related_services as $rp): ?>
<!--                                    <dt>--><?php //echo $rp->relationship;?><!--:</dt>-->
<!--                                    <dd><a href="--><?php //echo get_permalink($rp->related_service_id)?><!--">--><?php //echo get_the_title( $rp->related_service_id );?><!--</a></dd>-->
<!--                                --><?php //endforeach; ?>
<!--                            --><?php //endif;?>
<!--                        </dl>-->
<!--                    </div>-->
<!--                --><?php //endif;?>
                <div class="tab-content" id="tab_uses_products" <?php if( ! $is_active ):?>style="display: none;"<?php else:?>style="display: block;"<?php endif;?>>
                    <dl class="column uses_products">
                        <dt><a href="<?php echo get_permalink( $service->service_product_id );?>"><?php echo get_the_title( $service->service_product_id );?></a></dt>
                        <?php $prod = new ProductAndService( $service->service_product_id ); $prod->load();?>

                        <dd>Owner: <?php echo $prod->owner;?></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
    <div class="claims-list-wrapper">
        <div class="claims-list-title">
            <h4 class="blue_txt">End-to-End Test Agreements and Claims</h4>
        </div>
        <table class="claims-list">
            <thead>
            <tr>
                <th>Agreement ID</th>
                <th>Other Service</th>
                <th>Other Party</th>
                <th class="centered">Level</th>
                <th class="centered">Role</th>
                <th class="centered">Status</th>
                <th class="centered">Date</th>
                <th class="centered">Certificate</th>
            </tr>
            </thead>
            <tbody>
                <?php
                    $agreements_model = new Agreement();
                    $successfull_agreements = $agreements_model->get_service_agreements( $service->id );
                ?>
                <?php if( $successfull_agreements ):?>
                    <?php foreach( $successfull_agreements AS $agreement ):?>
                        <tr>
                            <td><?php echo $agreement->str_id;?></td>
                            <td><a href="<?php echo $agreement->entry_status == 'Responder' ? get_permalink( $agreement->requester_service->id ) : get_permalink( $agreement->responder_service->id ) ;?>"><?php echo $agreement->entry_status == 'Responder' ? get_the_title( $agreement->requester_service->id ) : get_the_title( $agreement->responder_service->id ) ;?></a></td>
                            <td><?php echo $agreement->entry_status == 'Responder' ? $agreement->requester_service->service_owner : $agreement->responder_service->service_owner  ;?></td>
                            <td class="centered"><?php echo $agreement->entry_status == 'Responder' ? implode( ', ', $agreement->requester_service->service_levels ) : implode( ', ', $agreement->responder_service->service_levels )  ;?></td>
                            <td class="centered"><?php echo $agreement->entry_status == 'Responder' ? implode( ', ', $agreement->requester_service->service_roles ) : implode( ', ', $agreement->responder_service->service_roles )  ;?></td>
                            <td class="centered"><span class="status-<?php echo strtolower($agreement->status)?>"><?php echo $agreement->status;?></span></td>
                            <td class="centered"><?php if( $agreement->claim_date ) echo formatDate( $agreement->claim_date );?></td>
                            <td class="centered row-actions">
                                <?php if(strtolower($agreement->status) == 'verified') { ?>
                                <a href="#">View</a>&nbsp;|&nbsp;<a href="#" target="_blank">Download</a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php endforeach;?>
                <?php else:?>
                    <tr>
                        <td colspan="8" class="centered">No data</td>
                    </tr>
                <?php endif;?>
            </tbody>
        </table>
    </div>
</div>

<div id="e2e-test-request" class="popup-box e2e-test-request-popup" style="display: none; width: 329px;">
    <div class="popup-box-header radius6 noradiusbottom">End-to-End Test Request</div>
    <form name="generate_agreement_form" id="generate_agreement_form" action="">
        <div class="popup-box-content">
            <?php $can_request = Service::can_request_e2e( get_current_user_id(), $service->id );?>
                <div class="form-horizontal">
                    <?php if( $can_request ):?>
                            <?php
                                $requester_services = Service::get_user_services( get_current_user_id(), $service->id);
                                $requester_profiles = getCustomerProfileInstances();
                            ?>
                            <div class="field-row">
                                <label>Agreement ID:</label>
                                <div class="field-box">
                                    <input type="text" value="" id="agreement_id" name="agreement_id" class="input input-field" data-serviceid="<?php echo $service->service_id;?>">
                                    <span class="info-icon has-tooltip" title="Some info"></span>
                                </div>
                            </div>
                            <input type="hidden" name="responder_service" value="<?php echo $service->id;?>">
                            <div class="field-row">
                                <label>My Service:</label>
                                <div class="field-box">
                                    <select name="requester_service" id="requester_service" class="select input-field">
                                        <option></option>
                                        <?php if( $requester_services ):?>
                                            <?php foreach( $requester_services AS $serv ):?>
                                                <?php
                                                    $s = new Service( $serv->ID );
                                                    $s->load();
                                                if( $s->service_id == $service->service_id ){
                                                    continue;
                                                }
                                                ?>
                                                <option value="<?php echo $s->id;?>" data-serviceid="<?php echo $s->service_id;?>"><?php echo $s->service_name;?></option>
                                            <?php endforeach;?>
                                        <?php endif;?>
                                    </select>
                                    <span class="info-icon has-tooltip" title="Some info"></span>
                                </div>
                            </div>
                            <div class="field-row">
                                <label>My Profile:</label>
                                <div class="field-box">
                                    <select name="requester_profiles" id="requester_profiles" class="select input-field">
                                        <option></option>
                                        <?php foreach( $requester_profiles AS $requester_profile ):?>
                                            <option value="<?php echo $requester_profile->id;?>"><?php echo $requester_profile->profile_name;?></option>
                                        <?php endforeach;?>
                                    </select>
                                    <span class="info-icon has-tooltip" title="Some info"></span>
                                </div>
                            </div>
                            <div class="field-row">
                                <label>Message:</label>
                                <div class="field-box">
                                    <textarea name="agreement_message" id="agreement_message" cols="30" rows="10" class="textarea input-field"></textarea>
                                </div>
                            </div>
                        <?php echo wp_nonce_field( 'save-agreement', '_psnonce');?>
                        <div class="loading loading-with-text radius6"><div><b>LOADING DATA</b><p>Please wait...</p></div></div>
                    <?php else:?>
                        In order to request an end-to-end test agreement, you must have already defined a complementary service. A complementary service is one that has been tested against the same test suite, to at least the same conformance level, and with a complementary (ie opposite) role.
                    <?php endif;?>
                </div>
        </div>
        <div class="popup-box-footer radius6 noradiustop">
            <div class="loading loading-with-text radius6 loading_agreement"><div><b>PROCESSING</b><span>Please wait...</span></div></div>
            <?php if( $can_request ):?>
                <a href="#" class="action-btn process-btn submit-btn submit_agreement"><span class="p"></span><span class="t">Confirm</span></a>
            <?php endif;?>
            <a href="#" class="action-btn cancel-btn" onclick="jQuery('.popup-box .close_btn').click()"><span class="p"></span><span class="t">Cancel</span></a>
            <div class="clear"></div>
        </div>
        <a class="close_btn"></a>
    </form>
</div>
<script>
    jQuery(document).ready(function($){
        $('#requester_service').on('change', function(){
            if( $('#requester_service').val() ){
                $('#agreement_id').val( $('#agreement_id').data( 'serviceid') + '.' + $('option:selected', this).attr('data-serviceid') );
            } else{
                $('#agreement_id').val( '' );
            }
        });
        $(".e2e-test-request-link").cplightbox({
            onLoad: function(){
                jQuery('.simple_tooltip').each(function(){
                    var tooltipLeft = -1 * jQuery(this).width()/2-5;
                    var tooltipTop = -1 * jQuery(this).outerHeight() - 6;
                    jQuery(this).css({'top': tooltipTop, 'margin-left': tooltipLeft});
                });
            }
         });
        jQuery('.submit_agreement').click(function(){
            var is_valid = true;
            jQuery('.case_exclude_reason').removeClass('input-error');
            if( ! jQuery('#requester_service').val() ){
                jQuery('#requester_service').addClass('select-error');
                return false;
            }
            if( ! jQuery('#agreement_message').val() ){
                jQuery('#agreement_message').addClass('select-error');
                return false;
            }
            jQuery('.loading_agreement').show();
            jQuery('#generate_agreement_form').submit();
        });
    });
</script>