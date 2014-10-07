<?php
/**
 * Service Content
 */
?>
<?php
$prev_page = wp_get_referer() ? wp_get_referer() : '/products-and-services/';
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
                    <a href="#e2e-test-request" class="action-btn green-btn e2e-test-request-link">Request E2E Test</a>
                    <a href="<?php echo addPrintParams(get_permalink(), 'product')?>" class="action-btn print-btn print-page-btn" id="print-product-btn"><span class="p"></span><span class="t">Print</span></a>
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
                        <div class="product-name">Service Name: <strong>MLC MasterKey Business Super v1.0</strong></div>
                        <div class="product-id">(Service ID: <strong>44928361101001</strong>)</div>
                    </div>
                    <ul class="product-attributes">
                        <li>Service Provider: <strong>NAB</strong>
                        <li>Entity ID: <strong>44928361101001</strong></li>
                        <li>ID Type: <strong>USI</strong></li>
                        <li>Process: <strong><a href="#">Contributions v1.2</a></strong></li>
                        <li>Role: <strong>Fund</strong></li>
                        <li>Level: <strong>A</strong></li>
                        <li>Protocol: <strong>ebMS3-Gateway</strong></li>
                        <li>End-Point: <strong><a href="#">ClickSuper</a></strong></li>
                        <li>End-Point-Type: <strong>Alias</strong></li>
                    </ul>
                    <div class="product-description">MLC Master Key Business Super (including Personal Super)</div>
                </div>
            </div>
            <div class="tabs-contr">
                <ul class="tab-nav">
                    <li class="active">
                        <a href="javascript: void(0)" rel="tab_related_services">Related Services</a>
                    </li>
                    <li>
                        <a href="javascript: void(0)" rel="tab_uses_products">Uses Products</a>
                    </li>
                </ul>
                <div class="tab-content" id="tab_related_services" style="display: block;">
                    <dl class="column related_products">
                        <?php foreach ($product->relatedProducts as $rp): ?>
                            <dt>Depends on:</dt>
                            <dd><a href="<?php echo get_permalink($rp->related_product_id)?>">Service Name</a></dd>
                        <?php endforeach; ?>
                    </dl>
                </div>
                <div class="tab-content" id="tab_uses_products" style="display: none;">
                    <dl class="column uses_products">
                        <dt><a href="#">MLC MasterKey Business</a></dt>
                        <dd>Owner: <a href="#">NAB</a></dd>
                        <dt><a href="#">ClickSuper Gateway v1.1</a></dt>
                        <dd>Owner: <a href="#">Westpac</a></dd>
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
            <div class="field-row">
                <label>Agreement ID:</label>
                <div class="field-box">
                    <input type="text" value="" id="agreement_id" class="input input-field">
                    <span class="info-icon has-tooltip" title="Some info"></span>
                </div>
            </div>
            <div class="field-row">
                <label>My Service:</label>
                <div class="field-box">
                    <select name="" id="" class="select input-field">
                        <option value="">Option 1</option>
                        <option value="">Option 2</option>
                        <option value="">Option 3</option>
                    </select>
                    <span class="info-icon has-tooltip" title="Some info"></span>
                </div>
            </div>
            <div class="field-row">
                <label>My Profile:</label>
                <div class="field-box">
                    <select name="" id="" class="select input-field">
                        <option value="">Option 1</option>
                        <option value="">Option 2</option>
                        <option value="">Option 3</option>
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