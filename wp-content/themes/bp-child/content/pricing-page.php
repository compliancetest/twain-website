<?php
    $read_only = true;
    if( is_user_logged_in() ) {
        $user_id = get_current_user_id();
        $subscription = ct_get_assigned_organisation_subscription($user_id, $suite->familyMark);

        if ( ! $subscription) {
            if ($organisation_id = ct_is_organisation_admin($user_id)) {
                $read_only = false;
            }
        }
    }
?>
<div id="pricing-plans" class="popup-box" style="display: none; width: 723px;">
    <div class="popup-box-header radius6 noradiusbottom"><?php if( $read_only ): ?>View <?php echo $suite->name; ?> Pricing Plans<?php else:?>Select <?php echo $suite->name; ?> Pricing Plan<?php endif;?></div>
    <div class="pricing-plans-header">
        <a href="#" class="plans-header-nav-prev">&lt;</a>
        <a href="#" class="plans-header-nav-next">&gt;</a>
        <div class="plans-title-wrapper">
            <ul class="plans-title-list">
                <?php foreach( $pricing_plans = PricingPlan::getAllPlans( $suite->test_suite_plans ) AS $k => $plan ): ?>
                        <li <?php if( $k == 0 ):?>class="active"<?php endif;?>><label data-plan-container='plan_<?php echo $k;?>' data-plan-id='<?php echo $plan->id;?>'><input type="radio" name="plan_name" /><?php echo $plan->id == 6 ? preg_replace('/ /', '<br>', $plan->title , 1 ) : strrev( preg_replace('/ /', '>rb<', strrev( $plan->title ), 1 ) );?></label></li>
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
                    <?php foreach( $plan->attribute_all AS $att_name => $att_value ):?>
                        <?php if( $att_value['type'] == 'itemcode' ):?>
                            <li><strong class="has-custom-tooltip" title="<?php echo $att_value['desc'];?>" data-tooltip-width="180" data-tooltip-height="34"><?php echo $att_name;?></strong>$<?php echo $att_value['value'];?></li>
                        <?php elseif( $att_value['type'] == 'number' || $att_value['type'] == 'string' ):?>
                            <li><strong class="has-custom-tooltip" title="<?php echo $att_value['desc'];?>" data-tooltip-width="180" data-tooltip-height="34"><?php echo $att_name;?></strong><?php echo $att_value['value'];?></li>
                        <?php elseif( $att_value['type'] == 'percent' ):?>
                            <li><strong class="has-custom-tooltip" title="<?php echo $att_value['desc'];?>" data-tooltip-width="180" data-tooltip-height="34"><?php echo $att_name;?></strong><?php echo $att_value['value'];?>%</li>
                        <?php elseif( $att_value['type'] == 'boolean' ):?>
                            <li><strong class="has-custom-tooltip" title="<?php echo $att_value['desc'];?>" data-tooltip-width="180" data-tooltip-height="34"><?php echo $att_name;?></strong><?php echo $att_value['value'] == 1 ? 'Yes' : 'No';?></li>
                        <?php endif;?>
                    <?php endforeach;?>
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
            <a href="<?php echo the_permalink() ?>?_organisation_nonce=<?php echo wp_create_nonce('subscribe') ?>&suite_id=<?php echo $suite->id ?>" class=" submit_all action-btn process-btn submit-btn" rel="custom-popup" cp-type="ajax" cp-closeWhenClickOveraly=0 cp-removeBoxAfterClose=1><span class="p"></span><span class="t">Confirm</span></a>
        <?php endif;?>
        <a href="#" class="action-btn cancel-btn" onclick="jQuery('#pricing-plans .close_btn').click()"><span class="p"></span><span class="t">Cancel</span></a>
        <div class="clear"></div>
    </div>
    <a class="close_btn"></a>
</div>