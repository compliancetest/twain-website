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
                        <a href="/edit-service?id=<?php echo $service->id?>" class="action-btn edit-btn right"><span class="p"></span><span class="t">Edit</span></a>
                    <?php } ?>
                    <a href="#e2e-test-request" class="action-btn green-btn e2e-test-request-link">Request E2E Test</a>
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
                        <div class="product-name">Service Name: <strong><?php echo $service->service_name;?></strong></div>
                        <div class="product-id">(Service ID: <strong><?php echo $service->service_id;?></strong>)</div>
                    </div>
                    <ul class="product-attributes">
                        <li>Service Provider: <strong><a href="<?php echo get_permalink( $service->service_product_id );?>"><?php echo get_the_title( $service->service_product_id );?></a></strong></li>
                        <li>Entity ID: <strong><?php echo $service->service_id;?></strong></li>
<!--                        <li>ID Type: <strong>--><?php //echo $service->service_id;?><!--</strong></li>-->
                        <li>Process: <strong><a href="<?php echo get_permalink( $service->service_suite_id );?>"><?php echo get_the_title( $service->service_suite_id );?></a></strong></li>
                        <li>Role: <strong><?php echo implode( ', ', $service->service_roles );?></strong></li>
                        <li>Level: <strong><?php echo implode( ', ', $service->service_levels );?></strong></li>
                        <li>Protocol: <strong><?php echo $service->service_protocol ;?></strong></li>
                        <li>End-Point: <strong><a href="<?php echo $service->service_endpoint;?>">Link</a></strong></li>
                        <li>End-Point-Type: <strong><?php echo $service->service_endpoint_type;?></strong></li>
                    </ul>
                    <div class="product-description"><?php echo $service->service_description;?></div>
                </div>
            </div>
            <div class="tabs-contr">
                <ul class="tab-nav">
<!--                    <li class="active">-->
<!--                        <a href="javascript: void(0)" rel="tab_related_services">Related Services</a>-->
<!--                    </li>-->
                    <li class="active">
                        <a href="javascript: void(0)" rel="tab_uses_products">Uses Products</a>
                    </li>
                </ul>
<!--                <div class="tab-content" id="tab_related_services" style="display: block;">-->
<!--                    <dl class="column related_products">-->
<!--                        --><?php //if( $product->relatedProducts ):?>
<!--                            --><?php //foreach ($product->relatedProducts as $rp): ?>
<!--                                <dt>Depends on:</dt>-->
<!--                                <dd><a href="--><?php //echo get_permalink($rp->related_product_id)?><!--">Service Name</a></dd>-->
<!--                            --><?php //endforeach; ?>
<!--                        --><?php //endif;?>
<!--                    </dl>-->
<!--                </div>-->
                <div class="tab-content" id="tab_uses_products" style="display: block;">
                    <dl class="column uses_products">
                        <dt><a href="<?php echo get_permalink( $service->service_product_id );?>"><?php echo get_the_title( $service->service_product_id );?></a></dt>
                        <dd>Owner: <?php echo $service->service_owner;?></dd>
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
            <tr>
                <td>44928361101:88765432:001</td>
                <td><a href="#">Contributions v1.2</a></td>
                <td>Bluescope Steel</td>
                <td class="centered">B</td>
                <td class="centered">Employer</td>
                <td class="centered"><span class="status-unverified">In Progress</span></td>
                <td class="centered">2014-11-12</td>
                <td class="centered row-actions">
                    <a href="#">View PDF</a>&nbsp;|&nbsp;<a href="#" target="_blank">Download</a>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<div id="e2e-test-request" class="popup-box e2e-test-request-popup" style="display: none; width: 329px;">
    <div class="popup-box-header radius6 noradiusbottom">End-to-End Test Request</div>
    <div class="popup-box-content">
        <div class="form-horizontal">
            <?php
                $requester_services = Service::get_user_services();
                $requester_profiles = getCustomerProfileInstances();
            ?>
            <div class="field-row">
                <label>Agreement ID:</label>
                <div class="field-box">
                    <input type="text" value="" id="agreement_id" class="input input-field" disabled="disabled" data-serviceid="<?php echo $service->service_id;?>">
                    <span class="info-icon has-tooltip" title="Some info"></span>
                </div>
            </div>
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
                                <option value="<?php echo $s->id;?>"><?php echo $s->service_name;?></option>
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
                            <option value="<?php echo $requester_profiles[0]->id;?>"><?php echo $requester_profiles[0]->profile_name;?></option>
                        <?php endforeach;?>
                    </select>
                    <span class="info-icon has-tooltip" title="Some info"></span>
                </div>
            </div>
            <div class="field-row">
                <label>Message:</label>
                <div class="field-box">
                    <textarea name="" id="" cols="30" rows="10" class="textarea input-field"></textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="popup-box-footer radius6 noradiustop">
        <a href="#" class="action-btn process-btn submit-btn" cp-type="ajax" cp-closeWhenClickOveraly=0 cp-removeBoxAfterClose=1><span class="p"></span><span class="t">Confirm</span></a>
        <a href="#" class="action-btn cancel-btn" onclick="jQuery('.popup-box .close_btn').click()"><span class="p"></span><span class="t">Cancel</span></a>
        <div class="clear"></div>
    </div>
    <a class="close_btn"></a>
</div>
<script>
    jQuery(document).ready(function($){
        $('#requester_service').on('change', function(){
            if( $('#requester_service').val() ){
                $('#agreement_id').val( $('#agreement_id').data( 'serviceid') + '.' + $('#requester_service').val() );
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
    });
</script>