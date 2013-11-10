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
    
    if(!wp_delete_post($id, true))
    {
        addMessage("There was an error while deleting the test case.", "error");
        wp_redirect($return);
        exit;
    }
    
    //Remove Data From Backend
    $esb = new ManageESB();
    $esb->deleteTestCaseNameIDMap($id);
    
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
        
        //Getting Profile Instances
        $profilesHTML = '';
        ob_start();
        $profileInstances = getCommunityProfileInstatnces($suite->community_id);
           foreach($profileInstances as $instance){
               $instanceObj = json_decode(base64_decode($instance->content));
       ?>
           <div class="field-row">
               <div class="grid-cell width15P">
                   <input type="checkbox" name="profile_instances[]" value="<?php echo $instance->id?>" />
                   <a href="<?php echo get_site_url()?>?td-action=<?php echo wp_create_nonce('view-profile-instance')?>&id=<?php echo $instance->id?>" rel="custom-popup" cp-type="ajax"><?php echo $instance->profile_name?></a>
               </div>
               <div class="grid-cell width10P">
                   <label><?php echo $instanceObj->ProfilePurpose?></label>
               </div>
               <div class="grid-cell width15P">
                   <a href="<?php echo get_site_url()?>?td-action=<?php echo wp_create_nonce('view-profile-type')?>&id=<?php echo $instance->type_id?>" rel="custom-popup" cp-type="ajax" class="view-profile-type-link"><?php echo $instance->profile_type_title; ?></a> 
               </div>
               <div class="grid-cell width45P">
                   <input type="text" readonly="readonly" value="<?php echo get_site_url()?>/profiles/<?php echo $instance->type?>/<?php echo $instance->filename?>" class="input width100P" />
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
    
    $id = $_POST['id'];
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
    
    $suiteID = $_POST['suite_id'];
    $community_id = get_post_meta($suiteID, 'community_id', true);
    $user_id = get_current_user_id();
    if(!$community_id || (!groups_is_user_admin($user_id, $community_id) && !is_super_admin() && !is_admin()))
    {
        echo json_encode(array('status' => 'error', 'message' => 'Permission Denied!'));
//        wp_redirect(get_site_url());
        exit;
    }
    
    if($isNew)
    {
        //Check Test Case ID
        $testCaseId = $_POST['test_case_id'];
        //Remove Space From the Test Case ID
        $testCaseId = str_replace(' ', '', $testCaseId);
        
        $pTestCase = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type= %s", $testCaseId, 'test-case') );
        if($pTestCase)
        {
            echo json_encode(array('status' => 'error', 'message' => 'The Test Case Id already exists!'));
            exit;
        }
        
        $id = wp_insert_post(array('post_title' => $testCaseId, 'post_type'=>'test-case', 'post_status' => 'publish'), true);
        if(is_wp_error($id))
        {
            echo json_encode(array('status' => 'error', 'message' => $id->get_error_message()));
//            addMessage($id->get_error_message(), 'error');            
            return;
        }    
        
    }
    
    if($isNew)
        cp_update_post_meta($id, 'test_case_id', $testCaseId);        
    
    $esb = new ManageESB();
    $esb->saveTestCaseInfo($id, get_post_meta($id, 'test_case_id', true), $_POST['outcome_type'], $_POST['message_count']);        
        
    //update post metas
    cp_update_post_meta($id, 'test_suite', $suiteID);    
    cp_update_post_meta($id, 'conformance_level', $_POST['conformance_level']);

    cp_update_post_meta($id, 'published', $_POST['published']);
    cp_update_post_meta($id, 'version', $_POST['version']);
    cp_update_post_meta($id, 'test_intent_description', $_POST['test_intent_description']);
    
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
    cp_update_post_meta($id, 'step_expected', $step_expected);
    $step_action = $_POST['step_action']; 
    cp_update_post_meta($id, 'step_action', $step_action);
    
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
    
    cp_update_post_meta($id, 'sequence_number', $_POST['sequence_number']);
    
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

