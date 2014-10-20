<?php

add_action('init', 'process_test_plan_actions', 100);
function process_test_plan_actions()
{
    if(wp_verify_nonce($_REQUEST['_plannonce'], 'edit-plan'))
    {
        editPlan();
    }else if(wp_verify_nonce($_REQUEST['_plannonce'], 'make-plan')){
        makePlan();
    }else if(wp_verify_nonce($_REQUEST['_plannonce'], 'delete-plan')){
        deletePlan();
    }else if(wp_verify_nonce($_REQUEST['_plannonce'], 'certify-plan')){
        certifyPlan();
    }
    
}

function certifyPlan()
{
    global $wpdb;
    
    $planID = isset($_GET['id']) ? $_GET['id'] : null;
    
    $user_id = get_current_user_id();
    
    $user_membership = ct_get_user_organisation_membership($user_id);
    
    $plan = new TestPlan($planID);
    $plan->load();
    
    $return = isset($_REQUEST['return']) ? base64_decode($_REQUEST['return']) : "/test-suite-coverage";
    $return_success = "/my-products";
    
    $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}users_subscriptions WHERE user_id=%d AND parent_id=%d", $user_id, $plan->organisation_subscription_id);
    $user_subscription = $wpdb->get_row($query);
    
    if(!$plan->load() || !$user_subscription)
    {
        addMessage('Permission Denied!', 'error');
        wp_redirect($return);
        exit;
    }
    
    $suite = new TestSuite($plan->suite_id);
    $cases = $suite->loadTestCases($plan->level, $plan->role, 'Active');                                   
    
    $esb = new ManageESB();
    $caseStatus = $esb->getCaseStatus($plan->organisation_subscription_id, $plan->suite_id);
    
    $all_verified = true;
    $has_exclusions = 0;
    foreach($cases as $case)
    {
        $is_optional = get_post_meta( $case->ID, 'testcase_status', true );
        $is_excluded = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM wp_test_plans_excluded_cases WHERE test_plan_id = %d AND test_case_id = %d ", $planID, $case->ID ) );
        if( ! $is_optional ) $is_optional = 'No';
        
        if( $is_excluded ){
            $has_exclusions = 1;
        }
        
        //If result is fail, can't certify
        if (isset($caseStatus[$plan->suite_id][$plan->product_id][$case->ID]) && $caseStatus[$plan->suite_id][$plan->product_id][$case->ID] == 'fail'){
            $all_verified = false;
            break;
        }
        
        if ($is_optional != 'Yes' && !$is_excluded && !isset($caseStatus[$plan->suite_id][$plan->product_id][$case->ID])) {
            $all_verified = false;
            break;
        }
        
    }

    //Create Compliance Claim
    if($all_verified)
    {
        $all_success = true;
        $level = ';;' . implode(';;', $plan->level) . ';;';
        $role = ';;' . implode(';;', $plan->role) . ';;';
        
        $query = $wpdb->prepare("SELECT id FROM " . $wpdb->prefix . "compliance_claims WHERE product_id=%d AND suite_id=%d AND conformance_level=%s AND role=%s", $plan->product_id, $plan->suite_id, $level, $role);
        $oId = $wpdb->get_var($query);
        if (!$oId) {
            if(!_saveClaim($plan->organisation_id, $plan->product_id, $plan->suite_id, $level, $role, 'Verified', $oId, $planID, $has_exclusions )) {
                wp_redirect($return);
            } else {
                //Delete Plan
                $wpdb->query( $wpdb->prepare("DELETE FROM {$wpdb->prefix}test_plans WHERE id = %d", $planID ) );
                //Delete Exclude Plan ID
                $wpdb->query( $wpdb->prepare("DELETE FROM {$wpdb->prefix}test_plans_excluded_cases WHERE test_plan_id = %d ", $planID ) );
                
                addMessage('The plan was certified successfully');
                wp_redirect($return_success);
            }
        } else {
            addMessage('An existing claim for this test plan already exists. Please delete it if you wish to update your claim for this test plan.', 'warning');
            wp_redirect($return);
        }
    }else{
        addMessage('You must complete the test plan before a claim can be made.', 'warning');
        wp_redirect($return);
    }
    
    exit;
}

function deletePlan()
{
    global $wpdb;
    
    $planID = isset($_GET['id']) ? $_GET['id'] : null;
    
    $user_id = get_current_user_id();
    
    $plan = new TestPlan($planID);
    $plan->load();
    
    $return = isset($_REQUEST['return']) ? base64_decode($_REQUEST['return']) : "/test-suite-coverage";
    
    //Check User Subscription
    $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}organisations_subscriptions WHERE id=%d AND user_id=%d", $plan->organisation_subscription_id, $user_id);
    $org_subscription = $wpdb->get_row($query);
    
    if(!$org_subscription)
    {
        addMessage('Permission Denied!', 'error');
        wp_redirect($return);
        exit;
    }
    
    if(!$wpdb->delete($wpdb->prefix . "test_plans", array('id' => $planID)))
    {
        addMessage($wpdb->last_error, 'error');
    }else{
        //Delete Exclude Items
        $wpdb->delete($wpdb->prefix . "test_plans_excluded_cases", array('test_plan_id' => $planID));
        
        $cloud_search = new CloudSearch();
        $cloud_search->cloud_search_delete_item( $planID, 'test_plan' );
        addMessage("The plan was deleted.");
    }
    wp_redirect($return);
    exit;
}

function editPlan()
{
    global $wpdb;
    
    $suiteID = $_GET['suite_id'];
    
    $planID = isset($_GET['id']) ? $_GET['id'] : null;
    $plan = new TestPlan($planID);
    $plan->load();
    
    $user_id = get_current_user_id();
    
    //Check User Subscription
    $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}users_subscriptions WHERE user_id=%d AND suite_id=%d", $user_id, $suiteID);
    $user_subscription = $wpdb->get_row($query);
    
    if (!$user_subscription || ($plan->id && $user_subscription->parent_id != $plan->organisation_subscription_id)) {
        ?>
        <div class="popup-box" id="make-plan-box" style="display: none;">
            <div class="popup-box-header radius6 noradiusbottom">Permission Error!</div>
            <div class="popup-box-content">    
                You are not allowed to <?php echo !$planID ? 'make a plan to the suite.' : 'edit this plan.'?>
            </div>
            <div class="popup-box-footer radius6 noradiustop">                        
                <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Close</span></a>
                <div class="clear"></div>
            </div>
            <a class="close_btn"></a>                
            <div class="loading" style="display: none;"></div>
        </div>
        <?php
        exit;
    }
    
    
    $products = getUserProductsAndServices($user_id);
    
    $suite = new TestSuite($suiteID);
    $levels = $suite->loadConformanceLevel();
    $roles = $suite->loadRoles();
    ?>
    <div class="popup-box" id="make-plan-box" style="display: none;">
        <form name="makePlanForm" id="makePlanForm" action="" method="post">
            <div class="popup-box-header radius6 noradiusbottom">Test Plan Form</div>
            <div class="popup-box-content grid-box-body">    
                <div class="field-row">
                    <div class="grid-cell">
                        <label>Product/Service</label>
                        <select class="select" name="product_id" id="product_id">                            
                            <option value="">Select a Product/Service</option>
                            <?php foreach($products as $p){ ?>
                            <option value="<?php echo $p->ID?>" <?php echo $plan->product_id == $p->ID ? 'selected="selected"' : ''?>><?php echo get_the_title($p->ID)?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="field-row">                
                    <div class="grid-cell checkbox-cell">
                        <label>Level</label>
                        <?php                         
                            foreach($levels as $l){
                                if($l['code'] == 'Default')
                                    continue;
                        ?>
                         <span><input type="checkbox" name="level[]" class="level" value="<?php echo $l['code']?>" <?php echo $plan->level && in_array($l['code'], $plan->level) ? 'checked="checked"' : ''?>> <?php echo $l['code'] ?></span>
                        <?php 
                            }
                        ?>
                    </div>
                    <div class="grid-cell checkbox-cell left15">
                        <label>Role</label>
                        <?php 
                            foreach($roles as $r){
                        ?>
                        <span><input type="checkbox" name="role[]" class="role" value="<?php echo $r['name']?>" <?php echo $plan->role && in_array($r['name'], $plan->role) ? 'checked="checked"' : ''?>> <?php echo $r['name'] ?></span>
                        <?php 
                            }
                        ?>
                    </div>
                    <div class="clear"></div>
                </div>            
            </div>
            <div class="popup-box-footer radius6 noradiustop">                        
                <a href="#" class="action-btn process-btn"><span class="p"></span><span class="t">Submit</span></a>
                <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>
                <div class="clear"></div>
            </div>
            <a class="close_btn"></a>                
            <div class="loading1" style="display: none;"></div>
            <input type="hidden" name="id" value="<?php echo $planID?>" />
            <input type="hidden" name="suite_id" value="<?php echo $suiteID?>" />
            <?php wp_nonce_field('make-plan', '_plannonce'); ?>
        </form>
    </div>

    <?php
    exit;
}

function makePlan()
{
    global $wpdb;
    
    $suiteID = $_POST['suite_id'];
    $planID = isset($_POST['id']) ? $_POST['id'] : null;
    $productID = $_POST['product_id'];
    
    $user_id = get_current_user_id();
    
    $plan = new TestPlan($planID);
    $plan->load();
    
    if(!$suiteID || !$_POST['product_id'] || !$_POST['level'] || !$_POST['role'] || ($planID && $plan->id == null))
    {
        addMessage('Invalid Request!', 'error');
        wp_redirect('/test-suite-coverage');
        exit;
    }
    
    //Check User Subscription
    $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}users_subscriptions WHERE user_id=%d AND suite_id=%d", $user_id, $suiteID);
    $user_subscription = $wpdb->get_row($query);
    
    if (!$user_subscription) {
        addMessage('Invalid Request!', 'error');
        wp_redirect('/test-suite-coverage');
        exit;
    }
    
    $product = get_post($productID);
    
    $product_org_id = get_post_meta($productID, 'product_organisation_id', true);
    
    //Getting User Organisation Id
    $user_membership = ct_get_user_organisation_membership($user_id);
    
    //Check Product
    if($user_membership->organisation_id != $product_org_id)
    {
        addMessage('Please select valid product id.', 'error');
        wp_redirect('/test-suite-coverage');
        exit;
    }
    
    if ($planID && $plan->organisation_id!= $user_membership->organisation_id) {
        addMessage('Permission Denied!', 'error');
        wp_redirect('/test-suite-coverage');
        exit;
    }
    
    //Serialize Level
    $cloud_search = new CloudSearch();

    if(!$planID) //Make Plan
    {
        $nId = $wpdb->insert($wpdb->prefix . "test_plans", array(
            'suite_id'    =>  $suiteID,
            'organisation_subscription_id'    =>  $user_subscription->parent_id,
            'product_id'    =>  $productID,
            'level'    =>  cp_implode($_POST['level']),
            'role'    =>  cp_implode($_POST['role']),
            'created_date'    =>  date('Y-m-d H:i:s')
        ), array("%d", "%d", "%d", "%s", "%s", "%s"));
        $cloud_search->cloud_search_update_test_plan( $nId );

    }else{  //Edit Claim
        $nId = $wpdb->update($wpdb->prefix . "test_plans", array(
            'product_id'    =>  $productID,
            'level'    =>  cp_implode($_POST['level']),
            'role'    =>  cp_implode($_POST['role'])
        ), array('id' => $plan->id), array("%d", "%s", "%s"), array("%d"));
        $cloud_search->cloud_search_update_test_plan( $plan->id );
    }
    if(!$nId)
    {
        addMessage($wpdb->last_error, 'error');
        wp_redirect('/test-suite-coverage');
        exit;
    }


    addMessage('Test Plan was saved successfully!');
    wp_redirect('/test-suite-coverage');
    exit;
}
