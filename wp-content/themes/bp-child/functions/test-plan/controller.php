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
    
    $plan = new TestPlan($planID);
    $plan->load();
    
    $return = isset($_REQUEST['return']) ? base64_decode($_REQUEST['return']) : "/test-suite-coverage";
    
    if($plan->creator_id != $user_id)
    {
        addMessage('Permission Denied!', 'error');
        wp_redirect($return);
        exit;
    }
    
    $suite = new TestSuite($plan->suite_id);
    $cases = $suite->loadTestCases($plan->level, $plan->role);                                   
    
    //Getting Esb Customer ID
    $query = $wpdb->prepare("SELECT id FROM " . $wpdb->prefix . "users_subscriptions WHERE suite_id=%d AND user_id=%d AND `status`='Active'", $plan->suite_id, $user_id);
    $esbUserId = $wpdb->get_var($query);
    
    $esb = new ManageESB();
    $caseStatus = $esb->getCaseStatus($esbUserId, $plan->suite_id);
    
    $all_verified = true;
    foreach($cases as $case)
    {
        if(!isset($caseStatus[$plan->suite_id][$plan->product_id][$case->ID]) || $caseStatus[$plan->suite_id][$plan->product_id][$case->ID] != 'pass') 
        {
            $all_verified = false;   
            break; 
        }
    }
    
    //Create Compliance Claim
    if($all_verified)
    {
        foreach($plan->level as $level)
        {
            foreach($plan->role as $role)
            {
                $query = $wpdb->prepare("SELECT id FROM " . $wpdb->prefix . "compliance_claims WHERE product_id=%d AND suite_id=%d AND conformance_level=%s AND role=%s", $plan->product_id, $plan->suite_id, $level, $role);
                $oId = $wpdb->get_var($query);
                if(!$oId)
                {
                    $wpdb->insert($wpdb->prefix . "compliance_claims", array(
                        'product_id'    =>  $plan->product_id,
                        'creator_id'    =>  $user_id,
                        'suite_id'    =>  $plan->suite_id,
                        'conformance_level'    =>  $level,
                        'role'    =>  $role,
                        'status'    =>  'Verified',
                        'created_date'    =>  date('Y-m-d H:i:s'),
                        'last_updated'    =>  date('Y-m-d H:i:s'),
                        'audit'    =>  ''
                    ));
                }else{
                    $nId = $wpdb->update(TABLE_CLAIM, array(
                        'status'    =>  'Verified',
                        'last_updated'    =>  date('Y-m-d H:i:s')
                    ), array('id' => $oId));
                }
            }
        }
    }
    
    addMessage('The plan was certified successfully');
    
    wp_redirect($return);
    
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
    
    if($plan->creator_id != $user_id)
    {
        addMessage('Permission Denied!', 'error');
        wp_redirect($return);
        exit;
    }
    
    if(!$wpdb->delete($wpdb->prefix . "test_plans", array('id' => $planID)))
    {
        addMessage($wpdb->last_error, 'error');
    }else{
        addMessage("The plan was deleted.");
    }
    wp_redirect($return);
    exit;
}

function editPlan()
{
    $suiteID = $_GET['suite_id'];
    $planID = isset($_GET['id']) ? $_GET['id'] : null;
    
    $user_id = get_current_user_id();
    
    $plan = new TestPlan($planID);
    $plan->load();
    
    $is_allowed = false;
    if(!$planID && is_customer($suiteID, $user_id))
        $is_allowed = true;
    else if($planID && $plan->creator_id == $user_id)
        $is_allowed = true;
    
    if(!$is_allowed)
    {
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
                <a href="#" class="action-btn process-btn"><span class="p"></span><span class="t">SUBMIT</span></a>
                <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Close</span></a>
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
    
    $user_id = get_current_user_id();
    
    $plan = new TestPlan($planID);
    $plan->load();
    
    if(!$suiteID || !$_POST['product_id'] || !$_POST['level'] || !$_POST['role'])
    {
        addMessage('Invalid Request!', 'error');
        wp_redirect('/test-suite-coverage');
        exit;
    }
    
    $product = get_post($_POST['product_id']);
    
    //Product/Service
    if(!is_super_admin() && !is_admin() && $user_id != $product->post_author)
    {
        addMessage('Permission Denied!', 'error');
        wp_redirect('/test-suite-coverage');
        exit;
    }
    
    $is_allowed = false;
    if(!$planID && is_customer($suiteID, $user_id))
        $is_allowed = true;
    else if($planID && $plan->creator_id == $user_id)
        $is_allowed = true;
    
    if(!$is_allowed)
    {
        addMessage('Permission Denied!', 'error');
        wp_redirect('/test-suite-coverage');
        exit;
    }
    
    //Serialize Level
    
    if(!$planID) //Make Plan
    {
        $nId = $wpdb->insert($wpdb->prefix . "test_plans", array(
            'suite_id'    =>  $suiteID,
            'creator_id'    =>  $user_id,
            'product_id'    =>  $_POST['product_id'],
            'level'    =>  cp_implode($_POST['level']),
            'role'    =>  cp_implode($_POST['role']),
            'created_date'    =>  date('Y-m-d H:i:s')
        ));
    }else{  //Edit Claim
        $nId = $wpdb->update($wpdb->prefix . "test_plans", array(
            'product_id'    =>  $_POST['product_id'],
            'level'    =>  cp_implode($_POST['level']),
            'role'    =>  cp_implode($_POST['role'])
        ), array('id' => $plan->id));
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
