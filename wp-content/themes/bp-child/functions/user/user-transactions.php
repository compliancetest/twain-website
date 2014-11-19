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
        
        $allSuites = array();
        $currentCaseSuites = array();
        
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
                      <option value="<?php echo $p->ID?>" <?php echo $row->PRODUCT_WP_ID == $p->ID ? 'selected="selected"' : ''?>><?php echo get_post_meta($p->ID, 'product_name', true)?></option>
                      <?php } ?>
                   </select>       
               </div>
               <div class="td td-case">
               
                   <select name="case<?php echo $row->ID?>" class="select">
                       <option value="0">Not Assigned</option>
                       <?php foreach($testCases as $c){ 
                           $tSuiteIDs = ct_get_active_suite_ids_of_case($c->ID);                  
                           $allSuites = array_merge($allSuites, $tSuiteIDs);
                           ?>
                       <option value="<?php echo $c->ID?>" <?php echo $row->TEST_CASE_DB_ID == $c->ID ? 'selected="selected"' : ''?> data-suites="<?php echo implode(',', $tSuiteIDs)?>"><?php echo cp_wrap($c->post_title, 12)?></option>
                       <?php } ?>
                   </select>
               </div>
               <div class="td td-suite td-fixed" data-id="<?php echo $row->ID?>">
                   <?php
                       if($row->TEST_CASE_DB_ID)
                       {
                           $cSuiteIDs = ct_get_active_suite_ids_of_case($row->TEST_CASE_DB_ID);
                           if($cSuiteIDs)
                           {
                           ?>
                           <select name="suite<?php echo $row->ID?>" class="select">
                           <?php
                               foreach($cSuiteIDs as $s){
                                       echo '<option value="' . $s . '" ' . ($row->TEST_SUITE_ID == $s ? 'selected="selected"' : '') . '>' . get_the_title($s) . '</option>';
                               }
                           ?>
                           </select>
                           <?php             
                           }
                           
                       }                       
                   ?>
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
                   <?php 
                        if(strlen($row->CONVERSATION_ID) > 27)
                        {
                            echo '<span title="' . $row->CONVERSATION_ID . '">' . substr($row->CONVERSATION_ID, 0, 10) . "....." . substr($row->CONVERSATION_ID, -10) . '</span>';
                        }else{
                            echo $row->CONVERSATION_ID;
                        }                                    
                   ?>   
               </div>
               <div class="td td-date tocenter td-fixed">                   
                   <?php echo formatDate($row->CONVERSATION_TIMESTAMP, 'Y-m-d H:i:s')?><br />
               </div>           
               <!--<div class="td td-to td-fixed"><?php echo $row->TO_PARTY_ID?></div>-->
               <div class="clear"></div> 
           </div>                       
            <?php
        }
        echo '<select id="all-suites" style="display: none">';
        $allSuites = array_unique($allSuites);        
        foreach($allSuites as $s)
        {
            echo '<option value="' . $s .'" data-permalink="' . get_permalink($s) . '">' . get_the_title($s) . '</option>';
        }
        echo '</select>';
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
    
    $html_render_limit = get_option('s3_xml_max_size');
    
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
                if(!$row->VALIDATION_ERROR && ($row->FLAG == 'IS_EMPTY' || !$row->S3_VALIDATION_RESULTS_LOCATION)){
                    echo '-';
                }else{
                    if ($row->FLAG == 'IS_EMPTY' || !$row->S3_VALIDATION_RESULTS_LOCATION){
                        echo '<a href="/view-validation-error?id=' . $row->ID . '" target="_blank">XML</a>';
                    } else {
                        echo '<a href="' . $row->S3_VALIDATION_RESULTS_LOCATION . '" target="_blank">XML</a>';
                    }
                    echo ' &middot; ';
                    if ($row->FLAG != 'IS_EMPTY' && $row->S3_VALIDATION_RESULT_CONTENT_LENGTH > $html_render_limit) {
                        echo '<a href="' . $row->S3_VALIDATION_RESULTS_LOCATION . '" class="html-view-error">HTML</a>';
                    } else {
                        echo '<a href="/view-validation-error?id=' . $row->ID . '&mode=html" target="_blank">HTML</a>';
                    }
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
            
            if(isset($_POST['suite' . $row->ID])){
                $suiteId = $_POST['suite' . $row->ID];
            }else if($caseDBId > 0){
                $suiteId = get_post_meta($caseDBId, 'test_suite', true);                
            }else{
                $suiteId = '';
            }
            
            $productId = get_post_meta($_POST['product' . $row->ID], 'product_id', true);
            $productName = get_post_meta($_POST['product' . $row->ID], 'product_name', true);
            $audit = $_POST['audit' . $row->ID];
            
            $esb = new ManageESB();
            
            $case_conf_id = $esb->getTestCaseConfigurationID($caseDBId);
            $suite_conf_id = $esb->getTestSuiteConfigurationID($suiteId);
            
            $query = ManageESB::$esbdb->prepare("UPDATE " . $esb->table_conversation_metadata . " SET TEST_CASE_CONFIGURATION_ID=%d, TEST_SUITE_CONFIGURATION_ID=%d, PRODUCT_ID=%s, PRODUCT_NAME=%s, AUDIT_RECORD=%d WHERE ID=%d", $case_conf_id, $suite_conf_id, $productId, $productName, $audit, $row->ID);
            
            ManageESB::$esbdb->query($query);
            
            //Recalculate Test Outcome
            if($row->TEST_CASE_DB_ID != $caseDBId && $caseDBId)
            {
                //Getting Versions
                $version_major = get_post_meta($caseDBId, 'version_major', true);
                $version_minor = get_post_meta($caseDBId, 'version_minor', true);
                $version_patch = get_post_meta($caseDBId, 'version_patch', true);
                
                $versions = array();
        
                $versions[] = $version_major;    
                $versions[] = $version_minor;
                
                if($version_patch)
                    $versions[] = $version_patch;
                
                $case_name = get_post_meta($caseDBId, 'test_case_id', true);
                
                $testCaseId = $case_name . "_V" . implode(".", $versions);
                
                $xmlData = '<api:calculateTestCaseOutcomeRequest xmlns:api="http://compliancetest.net/api">
                              <api:testCaseId>' . $testCaseId . '</api:testCaseId>
                              <api:conversationId>' . $row->CONVERSATION_ID . '</api:conversationId>
                            </api:calculateTestCaseOutcomeRequest>';
                $result = $rest->doMetadataAPI("testcase/outcome", $xmlData, true, true, true);                
                
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
    
    $args = array(
        'post_type' => 'product-service', 
        'posts_per_page' => -1
    );
    
    if (!is_super_admin()) {
        //Getting User Membership
        $membership = ct_get_user_organisation_membership($user_id);
        if (!$membership) {
            return array();
        }

        $args['meta_query'] = array(
                            array(
                                'key' => 'product_organisation_id',
                                'value' => $membership->organisation_id,
                                'compare' => "=",
                            )
        );
    } 
    
    
    if (isset($exclusive)) {
        $args['post__not_in'] = $exclusive;
    }
    
    
    $rows = get_posts($args);
    
    return $rows;
}

function getUserServices($user_id = null, $exclusive = array())
{
    if($user_id == null)
        $user_id = get_current_user_id();

    if(is_admin() || is_super_admin($user_id)) //Get All Products and Services
    {
        $args = array(
            'post_type' => 'service',
            'posts_per_page' => -1,
        );
    }else{
        $args = array(
            'post_type' => 'service',
            'posts_per_page' => -1,
            'author' => $user_id
            );
    }

    $rows = get_posts($args);
    $results = array();

    if( ! $exclusive)
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
        
    $select = "SELECT DISTINCT(p.ID), p.post_title FROM " . $wpdb->posts . " AS p ";

    $where = " WHERE p.post_type='test-case' AND p.post_status='publish' ";

    $left_join = " LEFT JOIN " . $wpdb->postmeta . " AS pm_v ON p.ID=pm_v.post_id AND pm_v.meta_key='test_case_status' ";
    $where .= " AND pm_v.meta_value = 'Active' ";

    if(!is_super_admin())
    {
        $query = $wpdb->prepare("SELECT DISTINCT(s.suite_id) FROM {$wpdb->prefix}users_subscriptions AS s, {$wpdb->prefix}bp_groups_members AS bm
                        WHERE 
                            s.user_id = bm.user_id AND bm.is_confirmed=1 
                            AND
                            (bm.user_id=%d OR bm.group_id 
                                IN 
                                ( SELECT group_id FROM {$wpdb->prefix}bp_groups_members WHERE user_id=%d AND (is_mod = 1 OR is_admin = 1)))
                        ", $user_id, $user_id);
        
        $suite_ids = $wpdb->get_col($query);
        
        if(!$suite_ids)
            return array();
        
        $left_join .= " LEFT JOIN " . $wpdb->postmeta . " AS pm ON p.ID=pm.post_id AND pm.meta_key='test_suite' ";
        $where .= " AND pm.meta_value IN (" . implode(", ", $suite_ids) . ") ";
        $left_join .= " LEFT JOIN " . $wpdb->postmeta . " AS pm_h ON p.ID=pm_h.post_id AND pm_h.meta_key='hide_case' ";            
        $where .= " AND pm_h.meta_value=0 ";


        
        foreach($suite_ids as $sid)
        {
            $left_join .= " LEFT JOIN " . $wpdb->postmeta . " AS pm" . $sid . " ON p.ID=pm" . $sid . ".post_id AND pm" . $sid . ".meta_key='conformance_level_" . $sid . "' " 
                            . " AND pm" . $sid . ".meta_value!='" . TEST_SUITE_DEFAULT_CONFORMANCE_LEVEL_CODE . "' ";    
        }
        
    }

    $query = $select . $left_join . $where . " ORDER BY post_title";
    
    $rows = $wpdb->get_results($query);
    
    return $rows;
}


function getAssociatedSuitesFromCases($cases)
{
    global $wpdb;
    
    //Getting Case IDS
    $ids = array();
    foreach($cases as $c)
        $ids[] = $c->ID;
    
    if(is_admin() || is_super_admin())
    {
        $query = "SELECT DISTINCT(p.ID), p.post_title FROM {$wpdb->posts} AS p LEFT JOIN {$wpdb->postmeta} AS pm ON p.ID=pm.meta_value AND pm.meta_key='test_suite' WHERE p.post_type='test-suite' AND p.post_status='publish' AND pm.post_id IN (" . implode(", ", $ids) . ")";    
    }else{
        $query = "SELECT DISTINCT(p.ID), p.post_title FROM {$wpdb->posts} AS p 
                  LEFT JOIN {$wpdb->postmeta} AS pm ON p.ID=pm.meta_value AND pm.meta_key='test_suite' 
                  LEFT JOIN {$wpdb->postmeta} AS pm1 ON p.ID=pm1.post_id AND pm1.meta_key='hide_suite' 
                  WHERE p.post_type='test-suite' AND p.post_status='publish' AND pm1.meta_value='0' AND pm.post_id IN (" . implode(", ", $ids) . ")";
    }
    
    $rows = $wpdb->get_results($query);
    
    return $rows;
}