<?php
/**
* Manage Compliance Claim
*/
if(!defined('TABLE_CLAIM'))
    define('TABLE_CLAIM', 'wp_compliance_claims');

add_action('init', 'process_claim_actions', 100);
function process_claim_actions()
{
    if(wp_verify_nonce($_REQUEST['_claimnonce'], 'edit-claim'))
    {
        editClaim();
    }else if(wp_verify_nonce($_REQUEST['_claimnonce'], 'get-suite-info-for-claim')){
        getTestSuiteInfoForClaim();
    }else if(wp_verify_nonce($_REQUEST['_claimnonce'], 'make-claim')){
        makeClaim();
    }else if(wp_verify_nonce($_REQUEST['_claimnonce'], 'delete-claim')){
        deleteClaim();
    }
}

function deleteClaim()
{
    global $wpdb;
    
    $productID = $_REQUEST['product_id'];
    $claimID = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
    
    $user_id = get_current_user_id();
    
    $claim = new ComplianceClaim($claimID);
    $claim->load();
    
    $return = isset($_REQUEST['return']) ? base64_decode($_REQUEST['return']) : "/";
    
    if(!can_delete_compliance_claim($claimID))
    {
        addMessage('Permission Denied!', 'error');
        wp_redirect($return);
        exit;
    }
    
    if(!$wpdb->delete($wpdb->prefix . "compliance_claims", array('id' => $claimID)))
    {
        addMessage($wpdb->last_error, 'error');
    }else{
        addMessage("The claim was deleted.");
    }
    wp_redirect($return);
    exit;
}

function makeClaim()
{
    global $wpdb;
    
    $productID = $_POST['product_id'];
    $claimID = isset($_POST['id']) ? $_POST['id'] : null;
    
    $user_id = get_current_user_id();
    
    $claim = new ComplianceClaim($claimID);
    $claim->load();
    
    $is_allowed = false;
    if(!$claimID && can_make_compliance_claim($productID))
        $is_allowed = true;
    else if($claimID && $claim->creator_id == $user_id)
        $is_allowed = true;
    
    if(!$is_allowed)
    {
        addMessage('Permission Denied!', 'error');
        wp_redirect('/my-products');
        exit;
    }
    
    if(!$claimID) //Make Claim
    {
        $nId = $wpdb->insert(TABLE_CLAIM, array(
            'product_id'    =>  $productID,
            'creator_id'    =>  $user_id,
            'suite_id'    =>  $_POST['suite_id'],
            'conformance_level'    =>  $_POST['level'],
            'role'    =>  $_POST['role'],
            'status'    =>  'Self Assessed',
            'created_date'    =>  date('Y-m-d H:i:s'),
            'last_updated'    =>  date('Y-m-d H:i:s'),
            'audit'    =>  ''
        ));
    }else{  //Edit Claim
        $nId = $wpdb->update(TABLE_CLAIM, array(
            'suite_id'    =>  $_POST['suite_id'],
            'conformance_level'    =>  $_POST['level'],
            'role'    =>  $_POST['role'],
            'last_updated'    =>  date('Y-m-d H:i:s')
        ), array('id' => $claim->id));
    }
    if(!$nId)
    {
        addMessage($wpdb->last_error, 'error');
        wp_redirect('/my-products');
        exit;
    }
    addMessage('Compliance Claim was saved successfully!');
    wp_redirect('/my-products');
    exit;
}

function editClaim()
{
    $productID = $_GET['product_id'];
    $claimID = isset($_GET['id']) ? $_GET['id'] : null;
    
    $user_id = get_current_user_id();
    
    $claim = new ComplianceClaim($claimID);
    $claim->load();
    
    $is_allowed = false;
    if(!$claimID && can_make_compliance_claim($productID))
        $is_allowed = true;
    else if($claimID && $claim->creator_id == $user_id)
        $is_allowed = true;
    
    if(!$is_allowed)
    {
        ?>
        <div class="popup-box" id="make-claim-box" style="display: none;">
            <div class="popup-box-header radius6 noradiusbottom">Permission Error!</div>
            <div class="popup-box-content">    
                You are not allowed to <?php echo !$claimID ? 'make a claim to the product.' : 'edit the claim.'?>
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
    
    
    $product = new ProductAndService($productID);
    $product->load();
    $suites = getUserTestSuites();
    
    $suite = new TestSuite($claim->suite_id);
    $levels = $suite->loadConformanceLevel();
    $roles = $suite->loadRoles();
    ?>
    <div class="popup-box" id="make-claim-box" style="display: none;">
        <form name="makeClaimForm" id="makeClaimForm" action="" method="post">
            <div class="popup-box-header radius6 noradiusbottom">Compliance Claim Form</div>
            <div class="popup-box-content grid-box-body">    
                <div class="field-row">
                    <div class="grid-cell">
                        <label>Suite</label>
                        <select class="select" name="suite_id" id="suite_id">                            
                            <option value="">Select a Suite</option>
                            <?php foreach($suites as $s){ ?>
                            <option value="<?php echo $s->ID?>" <?php echo $claim->suite_id == $s->ID ? 'selected="selected"' : ''?>><?php echo get_the_title($s->ID)?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="grid-cell left15">
                        <label>Level</label>
                        <select class="select" name="level" id="level">
                            <?php if(!$levels){ ?>
                                <option value="">Select a Level</option>
                            <?php 
                                }else{ 
                                    foreach($levels as $l){
                            ?>
                                <option value="<?php echo $l['code']?>" <?php echo $claim->conformance_level == $l['code'] ? 'selected="selected"' : ''?>><?php echo $l['code']?></option>
                            <?php 
                                    }
                                } 
                                
                            ?>
                        </select>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="field-row">
                    <div class="grid-cell">
                        <label>Role</label>
                        <select class="select" name="role" id="role">
                            <?php if(!$roles){ ?>
                                <option value="">Select a Role</option>
                            <?php 
                                }else{ 
                                    foreach($roles as $r){
                            ?>
                                <option value="<?php echo $r['name']?>" <?php echo $claim->role == $r['name'] ? 'selected="selected"' : ''?>><?php echo $r['name']?></option>
                            <?php 
                                    }
                                } 
                                
                            ?>
                        </select>
                    </div>
                    <div class="grid-cell left15">
                        <label>&nbsp;</label>
                        <input type="checkbox" name="agree_obligation" id="agree_obligation" value="1" <?php echo $claim->id ? 'checked="checked"' : '' ?> /> I agree to the <a href="#obligation-box" id="show-opligation-box" cp-type="inline" rel="custom-popup">Obligation</a>.
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
            <input type="hidden" name="id" value="<?php echo $claimID?>" />
            <input type="hidden" name="product_id" value="<?php echo $productID?>" />
            <?php wp_nonce_field('make-claim', '_claimnonce'); ?>
        </form>
    </div>

    <?php
    exit;
}

function getTestSuiteInfoForClaim()
{
    $suiteID = $_POST['suite_id'];
    
    $suite = new TestSuite($suiteID);
        
    $suite->load();
    
    if(!$suite->id)
    {
        echo '<result><status>error</status></result>';
    }else{
        $confLevelHTML = '';
        ob_start();
        ?>
        <select class="select" name="level" id="level">
            <?php if(!$suite->conformanceLevel){ ?>
                <option value="">Select a Level</option>
            <?php }else{ ?>
                <?php foreach($suite->conformanceLevel as $row){ ?>
                <option value="<?php echo $row['code']?>"><?php echo $row['code']?></option>
               <?php } ?>
           <?php } ?>
        </select>
        
        <?php
        $confLevelHTML = ob_get_clean();
        ob_end_clean();
        ob_start();
        $rolesHTML = '';
        ?>
        <select class="select" name="role" id="role">
            <?php if(!$suite->roles){ ?>
                <option value="">Select a Role</option>
            <?php }else {?> 
                <?php foreach($suite->roles as $row){ ?>
                <option value="<?php echo $row['name']?>"><?php echo $row['name']?></option>
               <?php } ?>
           <?php } ?>
        </select>
       <?php
        $rolesHTML = ob_get_clean();
        ob_end_clean();
        
        header('content-type: application/xml');
        echo '<result>';
        echo '<status>success</status>';
        echo '<conflevel><![CDATA[' . $confLevelHTML . ']]></conflevel>';
        echo '<roles><![CDATA[' . $rolesHTML . ']]></roles>';
        echo '</result>';
       
    }
    exit;
}


function getClaimsByProductId($product_id)
{
    global $wpdb;
        
    $query = $wpdb->prepare("SELECT c.*, pm.meta_value as `issuer` FROM " . TABLE_CLAIM . " AS c LEFT JOIN " . $wpdb->postmeta . " as pm on pm.post_id=c.suite_id AND pm.meta_key='ts_issuer'  WHERE product_id=%d", $product_id);
    $rows = $wpdb->get_results($query);
    
    return $rows;
}

function getClaimsBySuiteId($suite_id)
{
    global $wpdb;
        
    $query = $wpdb->prepare("SELECT c.*, pm.meta_value as `product_name` FROM " . TABLE_CLAIM . " AS c LEFT JOIN " . $wpdb->postmeta . " as pm on pm.post_id=c.product_id AND pm.meta_key='product_name'  WHERE suite_id=%d", $suite_id);
    $rows = $wpdb->get_results($query);
    
    return $rows;
}

function getTestPlansBySuiteId($suite_id)
{
    global $wpdb;
        
    $query = $wpdb->prepare("SELECT p.*, pm.meta_value as `product_name` FROM " . $wpdb->prefix . "test_plans AS p LEFT JOIN " . $wpdb->postmeta . " as pm on pm.post_id=p.product_id AND pm.meta_key='product_name'  WHERE p.suite_id=%d", $suite_id);
    $rows = $wpdb->get_results($query);
    
    return $rows;
}

