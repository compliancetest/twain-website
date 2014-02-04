<?php
add_action('after_delete_post', 'remove_case_name_id_map', 10, 1);
function remove_case_name_id_map($postid)
{
    $esb = new ManageESB();
    $esb->deleteTestCaseNameIDMap($postid);
    
}


add_action('admin_init', 'do_test_case_admin_ajax_action');
function do_test_case_admin_ajax_action()
{
    if(!is_admin())     //If not admin, return;
        return;
        
    if(isset($_POST['suite_id']) && isset($_POST['post_id']))    
    {
        $postID = $_POST['post_id'];
        //Check _wpnounce
        if(wp_verify_nonce($_POST['_wpnonce'], 'get_suite_roles'))
        {   
            $case = new TestCase($postID);            
            $case->testSuite = $_POST['suite_id'];
            
            $allRoles = $case->getAvailableRoles();            
            
            echo '<option value="">Choose Tester Role</option>';
            foreach($allRoles as $role){            
                echo '<option value="'. $role['name']. '"  class="'.$test_suite.'">'.$role['name'].'</option>';                        
            }    
            exit;
        }else if(wp_verify_nonce($_POST['_wpnonce'], 'get_suite_conf_level')){   
            
            foreach ($_POST['suite_id'] as $test_suite){
                echo "<b>" . get_the_title($test_suite) . "</b><br />";
                $suiteObj = new TestSuite($test_suite);
                $levels = $suiteObj->loadConformanceLevel();
                echo '<select name="conformance_level' . $test_suite . '" id="checkconflvl">';
                echo '<option value="">Choose Conformance Level</option>';                
                foreach($levels as $row){
                    echo '<option value="'. $row['code'] .'">'.$row['code'].'</option>';        
                }    
                echo '</select><br /><br />';
            }
            exit;
        }else if(wp_verify_nonce($_POST['_wpnonce'], 'get_init_message')){   
            $case = new TestCase($postID);            
            $case->testSuite = $_POST['suite_id'];
            $initMessages = $case->getAvailableInitMessages();
            echo '<select name="choose_init_messages" id="checkinitmsg">';
            echo '<option value="">Choose Initiating Message</option>';            
            foreach($initMessages as $msg){
                echo '<option value="'.$msg.'">'.$msg.'</option>';             
            }
            
            echo '</select>';
            exit;
        }
    }
}

//Get selected test suites of the test case
function _get_current_test_suite($test_case_id)
{
      return get_post_meta($test_case_id, 'test_suite');

}


function get_next_sequence_number($suite_id = null)
{
    global $wpdb;
    
    if(!$suite_id)
        return  10;
    
    $query = $wpdb->prepare("SELECT MAX(meta_value) FROM " . $wpdb->postmeta . " WHERE meta_key='sequence_number' AND post_id IN (SELECT post_id FROM " . $wpdb->postmeta . " WHERE meta_key='test_suite' AND meta_value=%d)", $suite_id);
    $value = $wpdb->get_var($query);
    
    return $value + 10;
}


add_action('init', 'process_testcase_actions');
function process_testcase_actions()
{
    $action = isset($_REQUEST['_wpnonce']) ? $_REQUEST['_wpnonce'] : null;
    if(wp_verify_nonce($action, 'get-suite-info-for-case'))
    {        
        getTestSuiteInfoForCase();
    }else if(wp_verify_nonce($action, 'save-case')){
        saveCase();        
    }else if(wp_verify_nonce($action, 'delete-case')){
        deleteCase();        
    }else if(wp_verify_nonce($action, 'pre-delete-case')){   
        confirmDeletingCase();
        exit;
    }
}

function deleteCase()
{
    global $wpdb;
    
    $id = $_REQUEST['id'];
    
    $post = get_post($id);
    
    $testCaseId = get_post_meta($id, 'test_case_id', true);
    $majorVersion = get_post_meta($id, 'version_major', true);
    
    $return = isset($_REQUEST['return']) ? base64_decode($_REQUEST['return']) : "/";
    
    if(!$post || $post->post_type != 'test-case')
    {
        addMessage('Invalid Request!', 'error');
        wp_redirect($return);
        exit;
    }
    
    if(!can_delete_test_case($id))
    {
        addMessage('Permission Denied!', 'error');
        wp_redirect($return);
        exit;
    }
    
    $case = new TestCase($id);
    $familyMark = $case->loadfamilyMark();
    
    if(!wp_delete_post($id, true))
    {
        addMessage("There was an error while deleting the test case.", "error");
        wp_redirect($return);
        exit;
    }
    
    //Remove Data From Backend
    $esb = new ManageESB();
    $esb->deleteTestCaseNameIDMap($id);
    
    $wpdb->delete($wpdb->prefix . "test_cases", array('case_id' => $id));
    
    cp_sort_test_cases($familyMark, $majorVersion);
    
    addMessage("The test case was deleted.");
    wp_redirect($return);
    exit;
}

function getTestSuiteInfoForCase()
{
    $suiteID = $_POST['suite_id'];
    
    if(!$suiteID)
    {
        echo '<result><status>error</status></result>';
    }else{
        if(!is_array($suiteID))
            $suiteID = array($suiteID);
        
        $case = new TestCase($_POST['id']);
        $case->load();
        
        $case->testSuite = $suiteID;
        
        $suiteRoles = $case->getAvailableRoles();
        $suiteInitMessages = $case->getAvailableInitMessages();
        
        
        
        $confLevelHTML = '';
        ob_start();
        ?>
        <?php foreach($case->testSuite as $idx=> $sid){ ?>
        <div class="conf-level-suite-box">
           <p <?php if($idx > 0){?>style="border-top: solid 2px #e3e3e3; padding: 10px 0 5px; margin-top: 15px;"<?php } ?>><b><?php echo get_the_title($sid)?></b></p>
           <?php
               $suiteObj = new TestSuite($sid);
               $levels = $suiteObj->loadConformanceLevel();
           ?>
           <div class="field-row">
               <div class="grid-cell radio-cell">
                   <label><input type="checkbox" name="conformance_level<?php echo $sid?>[]" value="<?php echo TEST_SUITE_DEFAULT_CONFORMANCE_LEVEL_CODE?>" class="default-level" checked="checked" /> <?php echo TEST_SUITE_DEFAULT_CONFORMANCE_LEVEL_CODE?></label>
               </div>
               <div class="grid-cell width60P">
                   <?php echo TEST_SUITE_DEFAULT_CONFORMANCE_LEVEL_DESCRIPTION?>
               </div>
               <div class="clear"></div>
           </div>
           <?php foreach($levels as $row){ ?>
               <?php
                   if($row['code'] == TEST_SUITE_DEFAULT_CONFORMANCE_LEVEL_CODE)
                       continue;
               ?>
               <div class="field-row">
                   <div class="grid-cell radio-cell">
                       <label><input type="checkbox" name="conformance_level<?php echo $sid?>[]" value="<?php echo $row['code']?>" <?php echo isset($case->conformanceLevel[$sid]) && in_array($row['code'], $case->conformanceLevel[$sid]) ? 'checked="checked"' : ''?> /> <?php echo $row['code']?></label>
                   </div>
                   <div class="grid-cell width70P">
                       <?php echo $row['desc']?>
                   </div>
                   <div class="clear"></div>
               </div>
               <?php } ?>                   
           </div>
        <?php } ?>     
        <?php
        $confLevelHTML = ob_get_clean();
        ob_end_clean();
        
        ob_start();
        foreach($case->testSuite as $idx=> $sid){
            ?>
            <div class="scenario-box">
               <p <?php if($idx > 0){?>style="border-top: solid 2px #e3e3e3; padding: 10px 0 5px; margin-top: 15px;"<?php } ?>><b><?php echo get_the_title($sid)?></b></p>
               <?php
                   $suiteObj = new TestSuite($sid);
                   $scenarios = $suiteObj->loadScenarios();
                   
                   foreach($scenarios as $row){ 
               ?>
               <div class="field-row">
                   <div class="grid-cell radio-cell">
                       <label><input type="radio" name="scenario_<?php echo $sid?>" value="<?php echo $row['id']?>" <?php echo $case->scenario[$sid] == $row['id'] ? 'checked="checked"' : ''?> /> <?php echo $row['code']?></label>
                   </div>
                   <div class="grid-cell width60P">
                       <?php echo $row['desc']?>
                   </div>
                   <div class="clear"></div>
               </div>
               <?php } ?>                   
              </div> 
            <?php
        }
        $scenarioHTML = ob_get_clean();
        ob_end_clean();
        
        
        ob_start();
        $rolesHTML = '';
        ?>
        <div class="grid-cell">
           <label>Tester Role:</label>                           
           <select name="choose_tester_role" class="select">
               <option>- Select -</option>
               
               <?php foreach($suiteRoles as $row) {?>
               <option value="<?php echo $row['name']?>" <?php echo $case->testerRole == $row['name'] ? 'selected="selected"' : ''?>><?php echo $row['name']?></option>
               <?php } ?>
           </select>
       </div>
       <div class="grid-cell">
           <label>Harness Role:</label>
           <select name="choose_harness_role" class="select">
               <option>- Select -</option>
               <?php foreach($suiteRoles as $row) {?>
               <option value="<?php echo $row['name']?>" <?php echo $case->harnessRole == $row['name'] ? 'selected="selected"' : ''?>><?php echo $row['name']?></option>
               <?php } ?>
           </select>
       </div>
        <?php        
        $rolesHTML = ob_get_clean();
        ob_end_clean();
        ob_start();
        //Get Initiation Messages
        $initMsgHTML = '';
        ?>
        <select name="choose_init_message" class="select">
           <option>- Select -</option>
           <?php                                
           foreach($suiteInitMessages as $row) {?>
           <option value="<?php echo $row?>" <?php echo $case->initiationgMessage == $row ? 'selected="selected"' : ''?>><?php echo $row?></option>
           <?php } ?>
       </select>
        <?php
        $initMsgHTML = ob_get_clean();
        ob_end_clean();
        
        //Getting Profile Instances
        $profilesHTML = '';
        ob_start();
        $profileInstances = $case->getAvailableProfileInstances();
           foreach($profileInstances as $instance){
           $instanceObj = json_decode(base64_decode($instance->content));
       ?>
           <div class="field-row">
               <div class="grid-cell width15P">
                   <input type="checkbox" name="profile_instances[]" value="<?php echo $instance->id?>" <?php echo cp_checked($instance->id, $case->profileInstances) ?> />
                   <a href="<?php echo get_site_url()?>?td-action=<?php echo wp_create_nonce('view-profile-instance')?>&id=<?php echo $instance->id?>" rel="custom-popup" cp-type="ajax">
                   <?php echo $instance->profile_name?>
                   <?php
                        if($instanceObj->Profile->Version)
                        {
                            $version = array();
                            foreach(get_object_vars($instanceObj->Profile->Version) as $k=>$v)      
                            {
                                $version[] = $v;
                            }
                            echo " v" . implode(".", $version);
                        }
                    ?>
                   </a>
               </div>
               <div class="grid-cell width10P">
                   <label><?php echo $instanceObj->Profile->Purpose?></label>
               </div>
               <div class="grid-cell width15P">
                   <a href="<?php echo get_site_url()?>?td-action=<?php echo wp_create_nonce('view-profile-type')?>&id=<?php echo $instance->type_id?>" rel="custom-popup" cp-type="ajax" class="view-profile-type-link"><?php echo $instance->profile_type_title; ?></a> 
               </div>
               <div class="grid-cell width45P">
                   <input type="text" readonly="readonly" value="<?php echo get_site_url()?>/get-profile?id=<?php echo $instance->token?>" class="input width100P" />
               </div>                                                             
               <div class="clear"></div>
           </div>              
       <?php
           }
        $profilesHTML = ob_get_clean();
        ob_end_clean();
        
        header('content-type: application/xml');
        echo '<result>';
        echo '<status>success</status>';
        echo '<conflevel><![CDATA[' . $confLevelHTML . ']]></conflevel>';
        echo '<scenario><![CDATA[' . $scenarioHTML . ']]></scenario>';
        echo '<roles><![CDATA[' . $rolesHTML . ']]></roles>';
        echo '<initmsg><![CDATA[' . $initMsgHTML . ']]></initmsg>';
        echo '<profiles><![CDATA[' . $profilesHTML . ']]></profiles>';
        echo '</result>';
       
    }
    exit;
}


function saveCase()
{    
    global $wpdb;
    
    require_once(ABSPATH . "/wp-admin/includes/post.php");
       
    $id = $_POST['id'];
    
    $case = new TestCase($id);
    $case->load();
    
    if(!$id)
        $isNew = true;
    else
        $isNew = false;
        
    $isAjax = isset($_POST['byAjax']) ? true : false;
    
    if(($isNew && !can_create_test_case()) || (!$isNew && !can_edit_test_case($id)))
    {
        echo json_encode(array('status' => 'error', 'message' => 'Permission Denied!'));
//        wp_redirect(get_site_url());
        exit;
    }
    
    if(!$_POST['suite_id'])
    {
        echo json_encode(array('status' => 'error', 'message' => 'Please select a test suite.'));
        exit;
    }
    
    $suiteID = $_POST['suite_id'];
    if(!is_array($suiteID))
        $suiteID = array($suiteID);
        
    $community_id = get_post_meta($suiteID[0], 'community_id', true);
    $user_id = get_current_user_id();
    if(!$community_id || (!groups_is_user_admin($user_id, $community_id) && !is_super_admin() && !is_admin()))
    {
        echo json_encode(array('status' => 'error', 'message' => 'Permission Denied!'));
        exit;
    }
    
    if($_POST['version_major'] == 0 && $_POST['test_case_status'] == 'Active')
    {
        //Change Verion to 1.0.0
        $_POST['version_major'] = 1;
        $_POST['version_minor'] = 0;
        $_POST['version_patch'] = 0;
    }
    
    //If this is not latest version, the version should not be udpated
    /*if(!$isNew && isNewVersionExist($case->testCaseID, $case->version_major, $case->version_minor, $case->version_patch))
    {
        $_POST['version_major'] = $case->version_major;
        $_POST['version_minor'] = $case->version_minor;
        $_POST['version_patch'] = $case->version_patch;
    }
    */
    
    //Check Version Updated or not
    $version_updated = false;
    if( intval($case->version_major) != intval($_POST['version_major']) || intval($case->version_minor) != intval($_POST['version_minor']) || intval($case->version_patch) != intval($_POST['version_patch']) )
    {
        $version_updated =  true;
    }
    
    $versions[] = !$_POST['version_major'] ? 0 : $_POST['version_major'];
    $versions[] = !$_POST['version_minor'] ? 0 : $_POST['version_minor'];
    
    if($_POST['version_patch'])
        $versions[] = $_POST['version_patch'];
    
    $version = " v" . implode(".", $versions);
    
    $testCaseId = $_POST['test_case_id'];
    if($isNew || $version_updated)
    {
        //Remove Space From the Test Case ID
        $testCaseId = str_replace(' ', '', $testCaseId);
        
        $case_title = $testCaseId . $version;
        
        if($isNew)
        {
            $pTestCase = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type=%s", $case_title, 'test-case') );
            if($pTestCase)
            {
                echo json_encode(array('status' => 'error', 'message' => 'The test case id with the version already exists!'));
                exit;
            }            
        }
        
        $id = wp_insert_post(array('post_title' => $case_title, 'post_type'=>'test-case', 'post_status' => 'publish'), true);
        if(is_wp_error($id))
        {
            echo json_encode(array('status' => 'error', 'message' => $id->get_error_message()));
            return;
        }   
         
        if($isNew){            
            cp_update_post_meta($id, 'hide_case', 0);
        }        
    }else{
        $case_title = $testCaseId . $version;
        
        $post = get_post($id);
        $case_name = wp_unique_post_slug(sanitize_title($case_title), $id, $post->post_status, $post->post_type, $post->post_parent);
        
        $guid = get_sample_permalink($post->ID, $case_title, $case_name);
        
        if( !wp_update_post(array('ID' => $id, 'post_title' => $case_title, 'post_name' => $guid[1], 'guid' => str_replace('%postname%', $guid[1], $guid[0]))) )
        {
            addMessage('There was an error while updating the test case.', true);
            return;
        }
        
    }
    
    if(!$isNew && $case->testCaseID != $testCaseId)
    {
        caseNameUpdated($case->familyMark, $testCaseId);
    }
    
    $esb = new ManageESB();
    $esb->saveTestCaseInfo($id, $testCaseId . "_V" . implode(".", $versions), $_POST['outcome_type'], $_POST['message_count']);        
    
    delete_post_meta($id, 'test_suite');
    
    //update post metas
    foreach($suiteID as $sid)
    {        
        add_post_meta($id, 'test_suite', $sid);   
        delete_post_meta($id, 'conformance_level_' . $sid);
        if(isset($_POST['conformance_level' . $sid]))     
        {
            foreach($_POST['conformance_level' . $sid] as $level)
                add_post_meta($id, 'conformance_level_' . $sid, $level);
        }
        
        //Update Scenario
        delete_post_meta($id, 'scenario_' . $sid);
        update_post_meta($id, 'scenario_' . $sid, $_POST['scenario_' . $sid]);
    }
    
    cp_update_post_meta($id, 'test_case_id', $testCaseId);   
    cp_update_post_meta($id, 'published', date("Y-m-d H:i:s", getUTCTimeStamp($_POST['published'])));
    
    cp_update_post_meta($id, 'version_major', $_POST['version_major']);
    cp_update_post_meta($id, 'version_minor', $_POST['version_minor']);
    cp_update_post_meta($id, 'version_patch', $_POST['version_patch']);
    
    update_post_meta($id, 'test_intent_description', $_POST['test_intent_description']);
    
    cp_update_post_meta($id, 'outcome_type', $_POST['outcome_type']);
    cp_update_post_meta($id, 'bulk', $_POST['bulk']);
    cp_update_post_meta($id, 'message_count', $_POST['message_count']);
    cp_update_post_meta($id, 'test_case_status', $_POST['test_case_status']);
    
    $tester_role = $_POST['choose_tester_role'];
    cp_update_post_meta($id, 'choose_tester_role',$tester_role);
    $harness_role = $_POST['choose_harness_role'];
    cp_update_post_meta($id, 'choose_harness_role',$harness_role);
    $initiator = $_POST['choose_initiator'];
    cp_update_post_meta($id, 'choose_initiator',$initiator);
    
    $message_type = $_POST['choose_init_message'] ;
    cp_update_post_meta($id, 'choose_init_messages',$message_type);
    
    $step_expected = $_POST['step_expected']; 
    update_post_meta($id, 'step_expected', $step_expected);
    $step_action = $_POST['step_action']; 
    update_post_meta($id, 'step_action', $step_action);
    
    $property_name_data = $_POST['message_template_name']; 
    cp_update_post_meta($id, 'message_template_name', $property_name_data);
    $property_value_data = $_POST['message_template_url']; 
    cp_update_post_meta($id, 'message_template_url', $property_value_data);
    
    cp_update_post_meta($id, 'profile_instances', cp_implode($_POST['profile_instances']));
    
    $test_url = $_POST['test_url']; 
    cp_update_post_meta($id, 'test_url', $test_url);
    $protocol_binding2 = $_POST['protocol_binding2']; 
    cp_update_post_meta($id, 'protocol_binding2', $protocol_binding2);
    
    $property_name_exec = $_POST['property_name_exec']; 
    cp_update_post_meta($id, 'property_name_exec', $property_name_exec);
    $property_value_exec = $_POST['property_value_exec']; 
    cp_update_post_meta($id, 'property_value_exec', $property_value_exec);
    
//    cp_update_post_meta($id, 'sequence_number', $_POST['sequence_number']);
    
    //Save Test Case to wp_test_cases table    
    $query = $wpdb->prepare("SELECT case_id FROM {$wpdb->prefix}test_cases WHERE case_id=%d", $id);
    $rid = $wpdb->get_var($query);
    if(!$rid)
    {
        if($version_updated)
        {
            $familyMark = $wpdb->get_var("SELECT family_mark FROM {$wpdb->prefix}test_cases WHERE case_id=" . $case->id);
        }else{
            $familyMark = $id;
        }
        $wpdb->insert($wpdb->prefix . "test_cases", 
                        array('case_id' => $id, 
                              'case_name' => $testCaseId, 
                              'version_major' => $_POST['version_major'], 
                              'version_minor' => $_POST['version_minor'], 
                              'version_patch' => $_POST['version_patch'],
                              'family_mark' => $familyMark)
                     );
        cp_sort_test_cases($testCaseId, $_POST['version_major']);
    }
    
    if($version_updated)
    {
        //If the major version is updated, remove the association to the old test suite versions
        if($case->version_major != $_POST['version_major'] && isset($_SESSION['test_suite_id']))
        {
            //Hide Major version 0
            if(intval($suite->version_major) == 0)
            {
                cp_update_post_meta($suite->id, 'hide_case', 1);
            }
            
            $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}test_suites WHERE suite_title=%s AND version_major < %d", 
                                    get_post_meta($_SESSION['test_suite_id'], 'ts_name', true), get_post_meta($_SESSION['test_suite_id'], 'ts_version_major', true));
            $rows = $wpdb->get_results($query);
            foreach($rows as $row){
                delete_post_meta($id, 'conformance_level_' . $row->suite_id);
                delete_post_meta($id, 'test_suite', $row->suite_id);
            }
            
        }
        
        cp_sort_test_cases($testCaseId, $_POST['version_major']);
    }
        
    addMessage('Test Case was saved successfully!');
//    wp_redirect(get_permalink($id));

    //Send Notification Email
    if(!$isNew && isset($_POST['send-notification']))
    {
        $group = groups_get_group(array('group_id'=>$group_id));
        
        $emailData = array(
            '[case_name]' => get_post_meta($id, 'test_case_id', true),
            '[case_url]' => get_permalink($id),
            '[suite_name]' => get_post_meta($suiteID, 'ts_name', true),
            '[suite_url]' => get_permalink($suiteID),
            '[editor_name]' => cp_get_user_fullname($user_id)
        );
        
        //Getting Subscribers
        $subscribers = getSubscribersBySuiteId($suiteID);
        
        foreach($subscribers as $subscriber)
        {
            $emailData['[name]'] = cp_get_user_fullname($subscriber->user_id);                
            cp_send_email(array('name' => $emailData['[name]'], 'email' => $subscriber->user_email), 'case_changed', $emailData);
        }
    }
    echo json_encode(array('status' => 'success', 'link' => get_permalink($id)));
    exit;
}

//Hide all versions except the latest one
function cp_sort_test_cases($familyMark, $version_major)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}test_cases WHERE family_mark = %d AND version_major=%d ORDER BY version_minor DESC, version_patch DESC", $familyMark, $version_major);
    $cases = $wpdb->get_results($query);
    foreach($cases as $i=>$s)
    {
        update_post_meta($s->case_id, 'hide_case', $i > 0 ? 1 : 0);
    }
}

function isNewVersionExist($familyMark, $version_major, $version_minor = null, $version_patch = null)
{
    global $wpdb;
    
    if($version_minor === null && $version_patch === null)
    {
        $query = $wpdb->prepare("SELECT case_id FROM {$wpdb->prefix}test_cases WHERE family_mark = %d AND 
                            version_major > %d", $familyMark, $version_major, $version_major);
    }else if($version_patch === null){
        $query = $wpdb->prepare("SELECT case_id FROM {$wpdb->prefix}test_cases WHERE family_mark = %d AND 
                              version_major=%d AND version_minor > %d", $familyMark, $version_major, $version_minor);
    }else{
        $query = $wpdb->prepare("SELECT case_id FROM {$wpdb->prefix}test_cases WHERE family_mark = %d AND                             
                              version_major=%d ANd version_minor=%d AND version_patch > %d", $familyMark, $version_major, $version_minor,$version_patch);
    }
    
                              
    if($wpdb->get_var($query))
        return true;
    else 
        return false;
}

function caseNameUpdated($familyMark, $new)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "test_cases WHERE familyMark=%d", $familyMark);
    $suites = $wpdb->get_results($query);
    
    $esb = new ManageESB();
    
    foreach($suites as $row)
    {
        $versions = array();
        $versions[] = $row->version_major;
        $versions[] = $row->version_minor;
        
        if($row->version_patch)
            $versions[] = $row->version_patch;
        
        $version = implode(".", $versions);
        
        $post_title = $new . " v" . $version;
        $post_name = sanitize_title($post_title);
        $post = get_post($row->case_id);
        //Update Post Name
        $post_name = wp_unique_post_slug($post_name, $row->case_id, $post->post_status, $post->post_type, $post->post_parent);
        
        $guid = get_sample_permalink($post->ID, $post_title, $post_name);
        
        wp_update_post(array('ID' => $post->ID, 'post_title' => $post_title, 'post_name' => $guid[1], 'guid' => str_replace('%pagename%', $guid[1], $guid[0])));
        $wpdb->update($wpdb->prefix . "test_cases", array('case_name' => $new), array('case_id' => $row->case_id));
        cp_update_post_meta($post->ID, 'test_case_id', $new);
        
        
        $esb->saveTestCaseInfo($post->ID, $new . "_V" . implode(".", $versions), get_post_meta($post->ID, 'outcome_type', true), get_post_meta($post->ID, 'message_count', true));        
    
    }
    
}

function add_scenario_join_query($join, $object)
{
    global $wpdb, $post;
    
    $join .= " INNER JOIN {$wpdb->postmeta} AS scenario_meta ON scenario_meta.post_id={$wpdb->posts}.ID AND scenario_meta.meta_key='scenario_" . $post->ID . "' ";
    $join .= " INNER JOIN {$wpdb->prefix}test_suites_scenarios AS scenario ON scenario_meta.meta_value=scenario.id ";
    
    return $join;
}

function add_scenario_orderby_query($orderby, $object)
{
    global $wpdb, $post;
    
    $orderby = " scenario.sequence ASC, " . $orderby;
    
    return $orderby;
}

function add_scenario_fields_query($fields, $object)
{
    global $wpdb, $post;
    
    if($fields)
        $fields .= ", ";
    $fields .= " scenario.code AS scenarioCode, scenario.description as scenarioDescription, scenario.id as scenarioId ";
    
    return $fields;
}


function confirmDeletingCase()
{
    global $wpdb;
    
    $id = $_REQUEST['id'];
    $return = $_REQUEST['return'];
    
    $testCase = new TestCase($id);
    $suites = $testCase->loadValues('test_suite');
    ?>
    <div id="deleting-case-confirm-box<?php echo $id?>" class="popup-box deleting-case-confirm-box" style="display: none; width: 450px">                
        <div class="popup-box-header radius6 noradiusbottom">Delete Test Case</div>
        <div class="popup-box-content">
            <form method="post" action="">                        
                <p>Warning: This test case is currently included in the following test suites; 
                <br />
                <br />
                <?php 
                    foreach($suites as $sid){
                
                ?>
                    <a href="<?php echo get_permalink($sid)?>"><?php echo get_the_title($sid)?></a><br />
                <?php
                    }
                ?>                            
                </p>
                <p>Deleting the test case will remove it from all test suites. Do you wish to proceed?</p>
                <?php
                    wp_nonce_field('delete-case');                                  
                ?>                             
                <input type="hidden" name="id" value="<?php echo $id?>" />
                <input type="hidden" name="return" value="<?php echo $return ?>" />
                <div class="clear"></div>                            
            </form>    
        </div>
        <div class="popup-box-footer radius6 noradiustop">                                                        
            <a href="javascript: void(0)" class="action-btn process-btn" onclick="processDeleteCase('<?php echo $id?>')"><span class="p"></span><span class="t">Confirm</span></a>
            <a href="javascript: void(0)" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>                    
            <div class="clear"></div>
            <div class="message error" style="display: none;">Please aggree the License Agreement.</div>
        </div>
        <div class="loading"></div>
        <a id="close-popup-community" class="close_btn"></a>                
    </div>
    <?php
    
}