<?php
/**
* User Transaction Log
*/

function cp_edit_transaction_log(){
    global $wpdb;    
    
    if(!is_user_logged_in())
        return '<p class="message error">Permission Denied!</p>';
    
    $ids = $_POST['id'];
    
    if(!$ids)
    {
        $rows = array();    
    }else{
        $esb = new ManageESB();
        $rows =$esb->getTransactionLogByID($ids);
    }
    
    if($rows)
    {
        //Getting User Products
        $products = getUserProductsAndServices();
        $testCases  = getUserSubscribedCases();
        
        $result = "";
        ob_start();
        foreach($rows as $row)
        {
            ?>
            <input type="hidden" name="id[]" value="<?php echo $row->ID?>" />
            <div class="tr">
               <div class="td td-product">                               
                   <select name="product<?php echo $row->ID?>" class="select">
                      <?php foreach($products as $p){?>
                      <option value="<?php echo $p->ID?>" <?php echo $row->PRODUCT_ID == $p->ID ? 'selected="selected"' : ''?>><?php echo get_post_meta($p->ID, 'product_name', true)?></option>
                      <?php } ?>
                   </select>       
               </div>
               <div class="td td-case">
               
                   <select name="case<?php echo $row->ID?>" class="select">
                       <option value="0">Not Assigned</option>
                       <?php foreach($testCases as $c){ ?>
                       <option value="<?php echo $c->ID?>" <?php echo $row->TEST_CASE_DB_ID == $c->ID ? 'selected="selected"' : ''?>><?php echo cp_wrap($c->caseName, 12)?></option>
                       <?php } ?>
                   </select>
               </div>
               <div class="td td-suite td-fixed">
                   <a href="<?php echo get_permalink($row->TEST_SUITE_ID)?>"><?php echo cp_wrap($row->TEST_SUITE_NAME, 12)?></a>
               </div>
               <div class="td td-outcome tocenter td-fixed">
                   <?php if($row->TEST_OUTCOME_CODE){ ?>
                   <span class="status-<?php echo strtolower($row->TEST_OUTCOME_CODE) ?>"><?php echo $row->TEST_OUTCOME_LABEL?></span>                                   
                   <?php }else{ ?>
                   <span class="status-unverified">Not Performed</span>
                   <?php } ?>                                   
               </div>
               <div class="td td-audit tocenter">
                   <select name="audit<?php echo $row->ID?>" class="select">
                       <option value="1" <?php echo $row->AUDIT_RECORD ? 'selected="selected"' : ''?>>Yes</option>
                       <option value="0" <?php echo !$row->AUDIT_RECORD ? 'selected="selected"' : ''?>>No</option>
                   </select>                   
               </div>               
               <div class="td td-convsn td-fixed">
                   <?php echo $row->CONVERSATION_ID ?>
               </div>
               <div class="td td-date tocenter td-fixed">                   
                   <?php echo formatDate($row->CONVERSATION_TIMESTAMP, 'm/d/y H:i:s')?><br />
               </div>           
               <!--<div class="td td-to td-fixed"><?php echo $row->TO_PARTY_ID?></div>-->
               <div class="clear"></div> 
           </div>                       
            <?php
        }
        $result = ob_get_contents();
        ob_end_clean();
        
        return $result;
    }else{
        return '<p class="message error">Invalid Request!</p>';
    }
}


function cp_view_validation_log()
{
    global $wpdb;
    
    $id = $_POST['id'];
    
    $esb = new ManageESB();
    
    $data = $esb->getValidationResult($id);
    
    if($data === null)
    {
        return '<p class="message error">Invalid Request!</p>';
    }
    
    ob_start();
    foreach($data as $row){
    ?>
    <div class="tr">
        <div class="td td-phase"><?php echo $row->PHASE_LABEL ?></div>
        <div class="td td-status tocenter">
            <span class="status-<?php echo strtolower($row->STATUS_CODE) ?>"><?php echo $row->STATUS_LABEL?></span>            
        </div>
        <div class="td td-result tocenter">
            <?php
                if(!$row->HARNESS_VALIDATION_ERROR)
                    echo '-';
                else{
                    echo '<a href="/view-validation-error?id=' . $row->ID . '" target="_blank">XML</a>' . ' &middot; '
                    . '<a href="/view-validation-error?id=' . $row->ID . '&mode=html" target="_blank">HTML</a>';;
                }
            ?>
        </div>
        <div class="clear"></div>    
    </div>
    <?php
    }
    if(!$data)
    {
        ?>
        <div class="tr">
            <div class="td td-full">No data found!</div>
            <div class="clear"></div>
        </div>
        <?php
    }
    $result = ob_get_contents();
    ob_end_clean();
    
    return $result;
}

function cp_save_transaction_log()
{
    global $wpdb;    
    
    if(!is_user_logged_in())
        return '<p class="message error">Permission Denied!</p>';
    
    $ids = $_POST['id'];
    
    if(!$ids)
    {
        return '<p class="message error">Invalid Request!</p>';
    }else{
        $esb = new ManageESB();
        $rows =$esb->getTransactionLogByID($ids);
        
        $rest = new CPRest();
        
        foreach($rows as $row)
        {
            $caseDBId = intval($_POST['case' . $row->ID]);
            
            if($caseDBId > 0){
                $suiteId = get_post_meta($caseDBId, 'test_suite', true);                
            }else{
                $suiteId = '';
                
            }
            
            $productId = get_post_meta($_POST['product' . $row->ID], 'product_id', true);
            $productName = get_post_meta($_POST['product' . $row->ID], 'product_name', true);
            $audit = $_POST['audit' . $row->ID];
            
            $esb = new ManageESB();
            
            $case_conf_id = $esb->getTestCaseConfigurationID($caseDBId);
            
            $query = ManageESB::$esbdb->prepare("UPDATE " . $esb->table_conversation_metadata . " SET TEST_CASE_CONFIGURATION_ID=%d, TEST_SUITE_ID=%s, PRODUCT_ID=%s, PRODUCT_NAME=%s, AUDIT_RECORD=%d WHERE ID=%d", $case_conf_id, $suiteId, $productId, $productName, $audit, $row->ID);
            
            ManageESB::$esbdb->query($query);
            
            //Recalculate Test Outcome
            if($row->TEST_CASE_DB_ID != $caseDBId && $caseDBId)
            {
                $xmlData = '<api:calculateTestCaseOutcomeRequest xmlns:api="http://compliancetest.net/api">
                              <api:testCaseId>' . get_post_meta($caseDBId, 'test_case_id', true) . '</api:testCaseId>
                              <api:conversationId>' . $row->CONVERSATION_ID . '</api:conversationId>
                            </api:calculateTestCaseOutcomeRequest>';
                $result = $rest->doMetadataAPI("testcase/outcome", $xmlData);                
            }
        }
        
    }
    
    
    return 'success';
    
}

function cp_delete_transaction_log(){
    global $wpdb;    
    
    if(!is_user_logged_in())
    {
        addMessage('Permission Denied!', 'error');
        return false;
    }
    
    $ids = $_POST['id'];
    
    if(!$ids)
    {
        addMessage('Invalid Request!', 'error');
        return false;
    }else{
        $esb = new ManageESB();
        $rows =$esb->getTransactionLogByID($ids);
    }
    
    if(!$rows)
    {
        addMessage('Invalid Request!', 'error');
        return false;
    }
    
    $lIds = array();
    foreach($rows as $row)
    {
        $lIds[] = $row->ID;
    }
    
    $esb = new ManageESB();
    
    //DELETE FROM MSH_METADATA_VALIDATION_RESULT
/*    
    $query = "DELETE FROM " . $esb->table_message_validation_results . " WHERE MSH_MESSAGE_METADATA_ID in (SELECT ID FROM " . $esb->table_message_metadata . ")";    
    ManageESB::$esbdb->query($query);
    
    */
    //Delete MSH_METADATA_PAYLOAD            
    /*$query = "DELETE FROM " . $esb->table_message_metadata . " WHERE MSH_CONVERSATION_ID in (" . implode(", ", $lIds) . ")";    
    ManageESB::$esbdb->query($query);*/
    
    
    $query = "DELETE FROM " . $esb->table_conversation_metadata . " WHERE ID in (" . implode(", ", $lIds) . ")";    
    ManageESB::$esbdb->query($query);
    
    
    addMessage("Selected data was removed!");
    
    return true;
}

//Get User Test Suites
function getUserTestSuites($user_id = null)
{
    if($user_id == null)
        $user_id = get_current_user_id();
    
    //Getting User Groups
    $groups = groups_get_groups( array('user_id' => $user_id) );
    
    $args = array(
        'post_type' => 'test-suite', 
        'posts_per_page' => -1,
        'meta_query' => array(
            'relation' => 'OR'            
        )
    );
    
    if(!is_admin() && !is_super_admin())
    {        
        foreach($groups['groups'] as $group)
        {
            $args['meta_query'][] = array(
                    'key' => 'community_id',
                    'value' => $group->id,
                    'compare' => '='
                );
        }
    }
    
    $testsuites = get_posts( $args );
    
    return $testsuites;
}


function getUserProductsAndServices($user_id = null, $exclusive = array())
{
    if($user_id == null)
        $user_id = get_current_user_id();
    
    if(is_admin() || is_super_admin($user_id)) //Get All Products and Services
    {
        $args = array(
            'post_type' => 'product-service', 
            'posts_per_page' => -1,
        );
    }else{
        $customerIDs = getManagedCustomerWPIDs($user_id);
        if(!$customerIDs)
        {
            $args = array(
                'post_type' => 'product-service', 
                'posts_per_page' => -1,
                'author' => $user_id
            );
        }else{
            $customerIDs[] = $user_id;
            $args = array(
                'post_type' => 'product-service', 
                'posts_per_page' => -1,
                'author' => implode(",", $customerIDs)
            );
        }
    }
    
    $rows = get_posts($args);
    $results = array();
    
    if(!$exclusive)
    {        
        $results = $rows;
    }else{
        foreach($rows as $row)
        {
            if(in_array($row->ID, $exclusive))
                continue;
            $results[] = $row;
        }    
    }
    
    return $results;
}


/**
* Getting the test cases that belong to the test suites that the user subscribed or can manage if the user is support staff
* This is used for Transaction Log Edit Section
* 
* @param mixed $user_id
* @return []
*/
function getUserSubscribedCases($user_id = null)
{
    global $wpdb;
    
    if($user_id == null)
        $user_id = get_current_user_id();
        
    $query = "SELECT DISTINCT(p.ID), pm1.meta_value as caseName FROM " . $wpdb->posts . " AS p LEFT JOIN " . $wpdb->postmeta . " AS pm ON p.ID=pm.post_id AND pm.meta_key='test_suite' LEFT JOIN " . 
            $wpdb->postmeta ." AS pm1 ON p.ID=pm1.post_id AND pm1.meta_key='test_case_id' WHERE p.post_type='test-case' AND p.post_status='publish'";
    if(!is_super_admin() && !is_admin())
    {
        $suite_ids = getUserAllSuiteIDs($user_id);
    
        if(!$suite_ids)
            return array();
        
        $query .= " AND pm.meta_value IN (" . implode(", ", $suite_ids) . ")";
    }
    
    $rows = $wpdb->get_results($query);
    
    return $rows;
}
