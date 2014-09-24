<?php
    global $wpdb;
    $suite_id = $wpdb->get_var( $wpdb->prepare("SELECT suite_id FROM wp_test_suites WHERE family_mark = %d ORDER BY suite_id DESC LIMIT 1", $_REQUEST['suite_id']) );
    $suite = new TestSuite( $suite_id );
    $suite->load();
    $read_only = false;
    if( isset( $_REQUEST['plan_id'] ) ){
        $suite->test_suite_plans = array( intval( $_REQUEST['plan_id'] ) );
        $read_only = true;
    }
    wp_enqueue_script( 'plans-moving', get_stylesheet_directory_uri() . '/js/pricing-plans-moving.js', array('jquery'), '0.0.1');
?>

<div id="pricing-plans" class="popup-box" style="display: none; width: 723px;">
    <?php if( ! empty( $suite->test_suite_plans ) ):?>
        <div class="popup-box-header radius6 noradiusbottom"><?php echo $read_only ? 'View' : 'Select';?> <?php $suite->name; ?> Pricing Plan</div>
        <div class="pricing-plans-header">
            <a href="#" class="plans-header-nav-prev">&lt;</a>
            <a href="#" class="plans-header-nav-next">&gt;</a>
            <div class="plans-title-wrapper">
                <ul class="plans-title-list">
                    <?php foreach( $pricing_plans = PricingPlan::getAllPlans( $suite->test_suite_plans ) AS $k => $plan ): ?>
                            <li <?php if( $k == 0 ):?>class="active"<?php endif;?>><label data-plan-container='plan_<?php echo $k;?>' data-plan-id='<?php echo $plan->id;?>' data-plan-name='<?php echo $plan->title;?>'><input type="radio" name="plan_name" /><?php echo $plan->id == 6 ? preg_replace('/ /', '<br>', $plan->title , 1 ) : strrev( preg_replace('/ /', '>rb<', strrev( $plan->title ), 1 ) );?></label></li>
                    <?php endforeach;?>
                </ul>
            </div>
        </div>
        <div class="pricing-plans-content">
            <?php foreach( $pricing_plans AS $k => $p ):?>
                <?php $plan = New PricingPlan( $p->id );?>
                <div class="plan-content" id="plan_<?php echo $k;?>">
                    <p class="plan-description"><?php echo $plan->description;?></p>
                    <ul class="plan-subscription-prices">
                        <li><strong class="has-custom-tooltip" title="Once off fee charged when a subscription is initially purchased" data-tooltip-width="180" data-tooltip-height="34">Signup Fee</strong>$<?php echo $plan->getPriceByXeroCode( $plan->attribute_itemcodes['Signup']->value );?></li>
                        <li><strong class="has-custom-tooltip" title="Monthly Fee" data-tooltip-height="20">Monthly Fee</strong>$<?php echo $plan->getPriceByXeroCode( $plan->attribute_itemcodes['Monthly']->value );?></li>
                        <?php if( ! empty( $plan->attribute_period) ):?><li><strong class="has-custom-tooltip" title="Once off fee charged when a subscription is initially purchased" data-tooltip-width="180" data-tooltip-height="34">Prepaid / year</strong>$<?php echo $plan->getPriceByXeroCode( $plan->attribute_itemcodes['Monthly']->value ) * $plan->attribute_period ;?></li><?php endif;?>
                    </ul>
                    <table class="plans-pricing-table">
                        <thead>
                            <tr>
                                <th>
                                    <div class="diagonal-delimiter">
                                        <div class="horizontal-row">Roles</div>
                                        <div class="vertical-row">Levels</div>
                                    </div>
                                </th>
                                <th>
                                    <span class="has-custom-tooltip" title="An Australian Employer that has employees subject to mandatory superannuation guarantee legislation." data-tooltip-width="280" data-tooltip-height="35">Employer</span>
                                </th>
                                <th><span class="has-custom-tooltip" title="A superannuation fund to which employee contributions are paid." data-tooltip-width="240" data-tooltip-height="35">Fund</span></th>
                                <th><span class="has-custom-tooltip" title="A financial intermediary that aggregates payments from multiple employers into single payments to funds." data-tooltip-width="260" data-tooltip-height="55">Clearing House</span></th>
                                <th><span class="has-custom-tooltip" title="A Self-Managed Super Fund, identified by an ABN" data-tooltip-width="170" data-tooltip-height="34">SMSF</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $allowed_roles  = array( 'Employer', 'Fund', 'Clearing House', 'SMSF' );
                                $allowed_levels = array( 'A', 'B', 'AFF', 'BULK' );
                            ?>
                            <?php foreach( $allowed_levels AS $key => $level ):?>
                                <tr>
                                    <th><span class="has-custom-tooltip" data-tooltip-content-id="#b-description" data-tooltip-width="276" data-tooltip-height="108"><?php echo $level;?></span></th>
                                    <?php foreach( $allowed_roles AS $it => $role ):?>
                                            <?php if( isset( $plan->attribute_roles[$role] ) && in_array( $level, $plan->attribute_roles[$role]) ):?>
                                                <td><span class="feature-available"></span></td>
                                            <?php else:?>
                                                <td><span class="feature-unavailable"></span></td>
                                            <?php endif;?>
                                    <?php endforeach;?>
                                </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach;?>

            <div style="display:none;">
                <div id="b-description">
                    <p class="levels-tooltip-content">
                        The most basic conformance level designed to support the lowest entry level for Induction:<br>
                        - Basic registrations and contributions scenarios<br>
                        - No response messaging<br>
                        - Includes multi-fund employer<br>
                        - Clearing House
                    </p>
                </div>
                <div id="a-description">
                    <p class="levels-tooltip-content">
                        Expands on Conformance Level B to add: - "Progressive" and "Partial" response messages - "Warning" messages for business scenarios such as member identity mis-matches or fund specific mandatories.
                    </p>
                </div>
                <div id="aa-description" class="custom-tooltip-content">
                    <p class="levels-tooltip-content">
                        Expands on Conformance Level A to add: - Large files (10,000 contributions) - Multiple Partial and multiple progressive error responses
                    </p>
                </div>
            </div>

        </div>
        <div class="popup-box-footer radius6 noradiustop">
            <?php if( ! $read_only ):?>
                <a href="#" class="select_plan action-btn process-btn submit-btn"  cp-closeWhenClickOveraly=0 cp-removeBoxAfterClose=1><span class="p"></span><span class="t">Confirm</span></a>
            <?php endif;?>
            <a href="#" class="cancel_select_plan action-btn cancel-btn" onclick="jQuery('#pricing-plans .close_btn').click()"><span class="p"></span><span class="t">Cancel</span></a>
            <div class="clear"></div>
        </div>
        <a class="close_btn"></a>
    <?php else:?>
        <div class="popup-box-header radius6 noradiusbottom">This test suite hasn't configured plans</div>
        <div class="pricing-plans-header">
            <div class="pricing-plans-content">
                Please contact site administrator
            </div>
            <div class="popup-box-footer radius6 noradiustop">
                <a href="#" class="cancel_select_plan action-btn cancel-btn" onclick="jQuery('#pricing-plans .close_btn').click()"><span class="p"></span><span class="t">Cancel</span></a>
                <div class="clear"></div>
            </div>
        </div>
    <?php endif;?>
</div>
<script>
    jQuery(document).ready(function($){

            $('.plans-title-list li label').click(function(){
                run($(this));
                return false;
            });


            $('.plans-header-nav-prev').click(function(){
                var active = $('.plans-title-list li.active');
                if (active.index != 0){
                    active.prev().find('label').click();

                    return false;
                }

            });

            $('.plans-header-nav-next').click(function(){
                var active = $('.plans-title-list li.active');
                if (active.index != 0){
                    active.next().find('label').click();

                    return false;

                }

            });

        function run(el){
            var previous = jQuery('.plans-title-list li.active');
            var current = el.parent();

            previous.removeClass('active');
            current.addClass('active');

            setSiblings(current.index());

            var shift_size = getShiftedSize(current);

            moveSlider(shift_size);
        }

        function setSiblings(index){
            jQuery('.plans-title-list li').removeClass('sibling_1 sibling_2');

            jQuery('.plans-title-list li:eq(' + (index+1) + ')').addClass('sibling_1');
            if (index != 0){
                jQuery('.plans-title-list li:eq(' + (index-1) + ')').addClass('sibling_1');
            }
            jQuery('.plans-title-list li:eq(' + (index+2) + ')').addClass('sibling_2');

            if (index > 1){
                jQuery('.plans-title-list li:eq(' + (index-2) + ')').addClass('sibling_2');
            }

        }
        /**
         * el is current element
         */
        function getShiftedSize(el){
            var size = 0;
            jQuery('.plans-title-list li').each(function(){
                if (jQuery(this).index() < el.index()){
                    size = size + jQuery(this).outerWidth();
                }
            });

            size = (-size + 361) - el.outerWidth()/2 ;
            return size;

        }

        function moveSlider(size){
            jQuery('.plans-title-list').animate({
                left: size
            }, 100, function() {
                showPlanDetails(jQuery('.plans-title-list li.active label'));
            });

        }

        function showPlanDetails(el){
            jQuery('.plan-content').hide();
            jQuery('#' + el.data('plan-container')).show().css({ opacity: 0.5 }).animate({ opacity: 1 });
        }

        jQuery('.plans-title-list li.active label').click();

        jQuery('.select_plan').on('click', function(e){
            jQuery('#pricing_plan_id_span').val(jQuery('.plans-title-list li.active label').attr('data-plan-name'));
            jQuery('#pricing_plan_id').val(jQuery('.plans-title-list li.active label').attr('data-plan-id'));
            jQuery('#pricing-plans .close_btn').click()
            setTimeout("jQuery('#purchase-subscribe').click()", 500 );
        })
        jQuery('.cancel_select_plan').on('click', function(e){
            jQuery('#pricing-plans .close_btn').click()
            setTimeout("jQuery('#purchase-subscribe').click()", 500 );
        })
    });
</script>
<?php exit();?>