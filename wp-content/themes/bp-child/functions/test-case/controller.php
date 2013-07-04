<?php


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
            $suiteRoles = getTestSuiteRoles($_POST['suite_id']);
            echo '<option value="">Choose Tester Role</option>';
            foreach($suiteRoles as $role){            
                echo '<option value="'. $role['name']. '"  class="'.$test_suite.'">'.$role['name'].'</option>';                        
            }    
            exit;
        }else if(wp_verify_nonce($_POST['_wpnonce'], 'get_suite_init_messages')){   
            $metas_result = get_post_meta($_POST['suite_id'], 'init_message', true);
            echo '<select name="choose_init_messages" id="checkinitmsg">';
            echo '<option value="">Choose Initiating Message</option>';
            $metas_array = explode(',', $metas_result);
            if($metas_result)
            {
                foreach($metas_array as $ts_init_message){
                    echo '<option value="'.$ts_init_message.'">'.$ts_init_message.'</option>';             
                }
            }
            echo '</select>';
            exit;
        }else if(wp_verify_nonce($_POST['_wpnonce'], 'get_suite_conf_level')){   
            $metas_result = get_post_meta($_POST['suite_id'], 'lvl_code', true);
            echo '<select name="conformance_level" id="checkconflvl">';
            echo '<option value="">Choose Conformance Level</option>';
            
            if($metas_result)
            {
                foreach($metas_result as $v){
                    echo '<option value="'.$v.'">'.$v.'</option>';             
                }
            }
            echo '</select>';
            exit;
        }else if(wp_verify_nonce($_POST['_wpnonce'], 'get_init_message')){   
            $metas_result = get_post_meta($_POST['suite_id'], 'init_message', true);
            $metas_result = explode(',', $metas_result);
            echo '<select name="choose_init_messages" id="checkinitmsg">';
            echo '<option value="">Choose Initiating Message</option>';
            
            if($metas_result)
            {
                foreach($metas_result as $v){
                    echo '<option value="'.$v.'">'.$v.'</option>';             
                }
            }
            echo '</select>';
            exit;
        }
        
        
    }
}

//Get selected test suites of the test case
function _get_current_test_suite($test_case_id)
{
      return get_post_meta($test_case_id, 'test_suite', true);
//    $current_test_suites = get_post_meta($test_case_id, 'test_suites', true);
    
    //if(is_array($current_test_suites))
//        $current_test_suites = $current_test_suites[0];
//    if(!$current_test_suites)
//        $current_test_suites = null;
//    else
//        $current_test_suites = explode('|', $current_test_suites);
//    
//    if(!$current_test_suites)
//        $current_test_suites = array(isset($_GET['set_ts']) ? $_GET['set_ts'] : 0);
//    
//    $ids = array();
//    foreach($current_test_suites as $id)
//    {
//        if($id == '')
//            continue;
//        $ids[] = $id;
//    }
//    return $ids;
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
    }
}

function deleteCase()
{
    $id = $_REQUEST['id'];
    
    $post = get_post($id);
    
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
    
    if(!wp_trash_post($id))
    {
        addMessage("There was an error while deleting the test case.", "error");
        wp_redirect($return);
        exit;
    }
    
    addMessage("The test case was deleted.");
    wp_redirect($return);
    exit;
}

function getTestSuiteInfoForCase()
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
        <?php foreach($suite->conformanceLevel as $row){ ?>
       <div class="field-row">
           <div class="grid-cell radio-cell">
               <label><input type="radio" name="conformance_level" value="<?php echo $row['code']?>" <?php echo $case->conformanceLevel == $row['code'] ? 'selected="selected"' : ''?> /> <?php echo $row['code']?></label>
           </div>
           <div class="grid-cell width60P">
               <?php echo $row['desc']?>
           </div>
           <div class="clear"></div>
       </div>
       <?php } ?>
        <?php
        $confLevelHTML = ob_get_clean();
        ob_end_clean();
        ob_start();
        $rolesHTML = '';
        ?>
        <div class="grid-cell">
           <label>Tester Role:</label>
           <select name="choose_tester_role" class="select">
               <option>- Select -</option>
               <?php foreach($suite->roles as $row) {?>
               <option value="<?php echo $row['name']?>" <?php echo $case->testerRole == $row['name'] ? 'selected="selected"' : ''?>><?php echo $row['name']?></option>
               <?php } ?>
           </select>
       </div>
       <div class="grid-cell">
           <label>Harness Role:</label>
           <select name="choose_harness_role" class="select">
               <option>- Select -</option>
               <?php foreach($suite->roles as $row) {?>
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
           $messages = explode(',', $suite->initiatingMessage);
           foreach($messages as $row) {?>
           <option value="<?php echo $row?>" <?php echo $case->initiationgMessage == $row ? 'selected="selected"' : ''?>><?php echo $row?></option>
           <?php } ?>
       </select>
        <?php
        $initMsgHTML = ob_get_clean();
        ob_end_clean();
        
        header('content-type: application/xml');
        echo '<result>';
        echo '<status>success</status>';
        echo '<conflevel><![CDATA[' . $confLevelHTML . ']]></conflevel>';
        echo '<roles><![CDATA[' . $rolesHTML . ']]></roles>';
        echo '<initmsg><![CDATA[' . $initMsgHTML . ']]></initmsg>';
        echo '</result>';
       
    }
    exit;
}


function saveCase()
{
    global $wp;
    
    $id = $_POST['id'];
    if(!$id)
        $isNew = true;
    else
        $isNew = false;
    
    if(($isNew && !can_create_test_case()) || (!$isNew && !can_edit_test_case($id)))
    {
        addMessage('Permission Denied!', 'error');
        wp_redirect(get_site_url());
        exit;
    }
    
    $suiteID = $_POST['suite_id'];
    $community_id = get_post_meta($suiteID, 'community_id', true);
    $user_id = get_current_user_id();
    if(!$community_id || !groups_is_user_admin($user_id, $community_id))
    {
        addMessage('Permission Denied!', 'error');
        wp_redirect(get_site_url());
        exit;
    }
    
    if($isNew)
    {
        $id = wp_insert_post(array('post_title' => $_POST['test_case_id'], 'post_type'=>'test-case', 'post_status' => 'publish'), true);
        if(is_wp_error($id))
        {
            addMessage($id->get_error_message(), 'error');            
            return;
        }    
    }else{
        if(!wp_update_post(array('ID' => $id, 'post_title' =>$_POST['test_case_id'], 'post_name' => sanitize_title($_POST['test_case_id']))))
        {
            addMessage('There was an error while updating the test suite.', true);
            return;
        }
    }
    
    //update post metas
    update_post_meta($id, 'test_suite', $suiteID);    
    update_post_meta($id, 'conformance_level', $_POST['conformance_level']);
    
    update_post_meta($id, 'test_case_id', $_POST['test_case_id']);
    update_post_meta($id, 'published', $_POST['published']);
    update_post_meta($id, 'version', $_POST['version']);
    update_post_meta($id, 'test_intent_description', $_POST['test_intent_description']);
    
    update_post_meta($id, 'outcome_type', $_POST['outcome_type']);
    update_post_meta($id, 'bulk', $_POST['bulk']);
    update_post_meta($id, 'message_count', $_POST['message_count']);
    
    $tester_role = $_POST['choose_tester_role'];
    update_post_meta($id, 'choose_tester_role',$tester_role);
    $harness_role = $_POST['choose_harness_role'];
    update_post_meta($id, 'choose_harness_role',$harness_role);
    $initiator = $_POST['choose_initiator'];
    update_post_meta($id, 'choose_initiator',$initiator);
    
    $message_type = $_POST['choose_init_message'] ;
    update_post_meta($id, 'choose_init_messages',$message_type);
    
    $step_expected = $_POST['step_expected']; 
    update_post_meta($id, 'step_expected', $step_expected);
    $step_action = $_POST['step_action']; 
    update_post_meta($id, 'step_action', $step_action);
    
    $property_name_data = $_POST['property_name_data']; 
    update_post_meta($id, 'property_name_data', $property_name_data);
    $property_value_data = $_POST['property_value_data']; 
    update_post_meta($id, 'property_value_data', $property_value_data);
    
    $test_url = $_POST['test_url']; 
    update_post_meta($id, 'test_url', $test_url);
    $protocol_binding2 = $_POST['protocol_binding2']; 
    update_post_meta($id, 'protocol_binding2', $protocol_binding2);
    
    $property_name_exec = $_POST['property_name_exec']; 
    update_post_meta($id, 'property_name_exec', $property_name_exec);
    $property_value_exec = $_POST['property_value_exec']; 
    update_post_meta($id, 'property_value_exec', $property_value_exec);
    
    addMessage('Test Case was saved successfully!');
    wp_redirect(get_permalink($id));
    exit;
}