<?php
    global $wpdb;
    $suite_id = $wpdb->get_var( $wpdb->prepare("SELECT suite_id FROM wp_test_suites WHERE family_mark = %d ORDER BY suite_id DESC LIMIT 1", $_REQUEST['suite_id']) );
    $suite = new TestSuite( $suite_id );
    $suite->load();
    $read_only = false;
    $suite_ids = false;
    if( isset( $_REQUEST['plan_id'] ) ){
        $suite_ids = $suite->test_suite_plans;
        $suite->test_suite_plans = array( intval( $_REQUEST['plan_id'] ) );
        $read_only = true;
    }
    if( isset( $_REQUEST['get_all'] ) ){
        $response = array();
        foreach( $suite->test_suite_plans AS $plan ){
            $planData = new PricingPlan( $plan );
            $response[$plan] = $planData->title;
        }
        exit( json_encode( $response ) );
    }
    $allowed = PricingPlan::getPlanRolesAndLevels( $suite->test_suite_plans, $suite_ids );
    $allowed_roles  = $allowed['roles'];
    $allowed_levels = $allowed['levels'];
    wp_enqueue_script( 'plans-moving', get_stylesheet_directory_uri() . '/js/pricing-plans-moving.js', array('jquery'), '0.0.1');
    $roles_desc = $levels_desc = array();
    foreach( $suite->roles AS $r ){
        $roles_desc[$r['name']] = $r['desc'];
    }
    foreach( $suite->conformanceLevel AS $l ){
        $levels_desc[$l['code']] = $l['desc'];
    }
?>

<div id="pricing-plans" class="popup-box" style="display: none; width: 723px;">
    <?php if( ! empty( $suite->test_suite_plans ) ):?>
        <div class="popup-box-header radius6 noradiusbottom"><?php echo $read_only ? 'View' : 'Select';?> <?php echo $suite->name; ?> Pricing Plan</div>
        <div class="pricing-plans-header">
            <a href="#" class="plans-header-nav-prev">&lt;</a>
            <a href="#" class="plans-header-nav-next">&gt;</a>
            <div class="plans-title-wrapper">
                <ul class="plans-title-list">
                    <?php foreach( $pricing_plans = PricingPlan::getAllPlans( $suite->test_suite_plans ) AS $k => $plan ): ?>
                            <li <?php if( ( isset( $_REQUEST['pricing_plan_id'] ) && $_REQUEST['pricing_plan_id'] == $plan->id ) || ( $k == 0 && ! isset( $_REQUEST['pricing_plan_id'] ) ) ):?>class="active"<?php endif;?>><label data-plan-container='plan_<?php echo $k;?>' data-plan-id='<?php echo $plan->id;?>' data-plan-name='<?php echo $plan->title;?>'><input type="radio" name="plan_name" /><?php echo $plan->id == 6 ? preg_replace('/ /', '<br>', $plan->title , 1 ) : strrev( preg_replace('/ /', '>rb<', strrev( $plan->title ), 1 ) );?></label></li>
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
                                <?php if( isset( $plan->attribute_all['Discount'] ) ):?>
                                    <li><strong class="has-tooltip" title="<?php echo $att_value['desc'];?>"><?php echo $att_name;?></strong>$<?php echo ( $att_value['value'] - ( $att_value['value'] * ( $plan->attribute_all['Discount']['value'] / 100 ) ) ) ;?></li>
                                <?php else:?>
                                    <li><strong class="has-tooltip" title="<?php echo $att_value['desc'];?>"><?php echo $att_name;?></strong>$<?php echo $att_value['value'];?></li>
                                <?php endif;?>
                            <?php elseif( $att_value['type'] == 'number' || $att_value['type'] == 'string' ):?>
                                <li><strong class="has-tooltip" title="<?php echo $att_value['desc'];?>"><?php echo $att_name;?></strong><?php echo $att_value['value'];?></li>
                            <?php elseif( $att_value['type'] == 'percent' ):?>
                                <li><strong class="has-tooltip" title="<?php echo $att_value['desc'];?>"><?php echo $att_name;?></strong><?php echo $att_value['value'];?>%</li>
                            <?php elseif( $att_value['type'] == 'boolean' ):?>
                                <li><strong class="has-tooltip" title="<?php echo $att_value['desc'];?>"><?php echo $att_name;?></strong><?php echo $att_value['value'] == 1 ? 'Yes' : 'No';?></li>
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
                                <?php foreach( $allowed_roles AS $role ):?>
                                    <th><span <?php if( isset( $roles_desc[$role] ) ):?>class="has-tooltip" title='<?php echo $roles_desc[$role];?>'<?php endif;?>><?php echo $role;?></span></th>
                                <?php endforeach;?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach( $allowed_levels AS $key => $level ):?>
                                <tr>
                                    <th><span <?php if( isset( $levels_desc[$level] ) ):?>class="has-tooltip" title='<?php echo $levels_desc[$level];?>'<?php endif;?>><?php echo $level;?></span></th>
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

        </div>
        <div class="popup-box-footer radius6 noradiustop">
            <?php if( ! $read_only ):?>
                <a href="#" class="select_plan action-btn process-btn submit-btn"  cp-closeWhenClickOveraly=0 cp-removeBoxAfterClose=1><span class="p"></span><span class="t">Confirm</span></a>
            <?php endif;?>
            <a href="#" class="cancel_select_plan action-btn cancel-btn"><span class="p"></span><span class="t">Cancel</span></a>
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
                <a href="#" class="cancel_select_plan action-btn cancel-btn"><span class="p"></span><span class="t">Cancel</span></a>
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


        jQuery('.plans-title-list li.active label').click();

        jQuery('.has-tooltip').each(function(){
            var tooltip_obj;
            if (jQuery(this).find('.simple_tooltip').length == 0) {
                tooltip_obj = '<span class="simple_tooltip radius6">' + jQuery(this).attr('title') + '<span></span></span>';
                jQuery(this).append(tooltip_obj);
                jQuery(this).attr('title', '');
            }
        });

        setTimeout("jQuery('.plans-title-list li.active label').click();", 500 );

        <?php if( ! isset( $_REQUEST['is_edit'] ) ):?>
            jQuery('.select_plan').on('click', function(e){
                jQuery('#pricing_plan_id_span').val(jQuery('.plans-title-list li.active label').attr('data-plan-id'));
                jQuery('#pricing_plan_id').val(jQuery('.plans-title-list li.active label').attr('data-plan-id'));
                jQuery('#pricing-plans .close_btn').click();
                if( jQuery('.submit_all').attr('href').indexOf( 'suite_id' ) == -1 ){
                    var new_url = jQuery('.submit_all').attr('href') + '&suite_id='+ $('#suite_family_mark').val();
                } else {
                    var new_url = jQuery('.submit_all').attr('href').split('&suite_id');
                    new_url = new_url[0] + '&suite_id='+ $('#suite_family_mark').val();
                }
                new_url = new_url+'&pricing_plan_id='+jQuery('#pricing_plan_id').val();
                jQuery('.submit_all').attr( 'href', new_url );
                jQuery(".submit_all").off("click").cplightbox( {'href': new_url});

                setTimeout("jQuery('#purchase-subscribe').click()", 500 );
            })
            jQuery('.cancel_select_plan').on('click', function(e){
                jQuery('#pricing-plans .close_btn').click()
                setTimeout("jQuery('#purchase-subscribe').click()", 500 );
            })
        <?php else:?>
            <?php $sid = intval( $_REQUEST['sid'] );?>
            jQuery('.select_plan').on('click', function(e){
                jQuery('.pricing_plan_id').val(jQuery('.plans-title-list li.active label').attr('data-plan-id'));
                jQuery('#pricing_plan_id_hidden').val(jQuery('.plans-title-list li.active label').attr('data-plan-id'));
                jQuery('#pricing-plans .close_btn').click();
                if( jQuery('.edit_subsc').attr('href').indexOf( 'pricing_plan_id' ) == -1 ){
                    var new_url = jQuery('.edit_subsc').attr('href') ;
                } else {
                    var new_url = jQuery('.edit_subsc').attr('href').split('&pricing_plan_id');
                    new_url = new_url[0] ;
                }
                new_url = new_url+'&pricing_plan_id='+jQuery('#pricing_plan_id_hidden').val();
                jQuery('.edit_subsc').attr( 'href', new_url );
                jQuery(".edit_subsc").off("click").cplightbox( {'href': new_url});

                setTimeout("jQuery('.edit_sub_<?php echo $sid;?>').click()", 500 );
            })
            jQuery('.cancel_select_plan').on('click', function(e){
                jQuery('#pricing-plans .close_btn').click()
                setTimeout("jQuery('.edit_sub_<?php echo $sid;?>').click()", 500 );
            })
        <?php endif;?>
    });
</script>
<?php exit();?>