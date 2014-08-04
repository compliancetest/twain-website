<?php
/**
* Process Actions
*/

add_action('before_delete_post', 'remove_suite_name_id_map', 10, 1);
function remove_suite_name_id_map($postid)
{
    global $wpdb, $CPRest;
    
    $post = get_post($postid);
    
    if($post->post_type == 'test-suite')
    {    
        $esb = new ManageESB();
        $esb->deleteTestSuiteInfo($postid);
        
        $suite = new TestSuite($postid);
        $familyMark = $suite->loadfamilyMark();
        
        //Delete Conformance Level    
        $wpdb->delete($wpdb->postmeta, array('meta_key'=> 'conformance_level_' . $postid));
        //Delete Scenarios
        $wpdb->delete($wpdb->postmeta, array('meta_key'=> 'scenario_' . $postid));
        
        $wpdb->delete($wpdb->prefix . "test_suites", array('suite_id'=> $postid));
        
        //Delete Scenarios
        $wpdb->delete($wpdb->prefix . "test_suites_scenarios", array('suite_id' => $postid));
        
        cp_sort_test_suites($familyMark, get_post_meta($postid, 'ts_version_major', true));
        
        $query = $wpdb->prepare("SELECT suite_id FROM {$wpdb->prefix}test_suites WHERE family_mark = %d AND version_major=%d ORDER BY version_minor DESC, version_patch DESC LIMIT 1", $familyMark, get_post_meta($postid, 'ts_version_major', true));
        $next_suite_id = $wpdb->get_var($query);
        
        if($next_suite_id)
        {
            $query = "UPDATE {$wpdb->prefix}users_subscriptions SET suite_id={$next_suite_id} WHERE suite_id={$postid}";        
            $wpdb->query($query);    
        }
        
        //Gettnig Current Subscriptions
        $query = "SELECT * FROM {$wpdb->prefix}users_subscriptions WHERE suite_id NOT IN (SELECT suite_id FROM {$wpdb->prefix}test_suites)";
        $tSuites = $wpdb->get_results($query);
        foreach($tSuites as $s)
        {
            //Remove Backend Accounts
            $data = '<api:deleteUserRequest xmlns:api="http://compliancetest.net/api">
                        <api:user>
                            <api:userId>' . $s->esb_id . '</api:userId>                        
                        </api:user>
                    </api:deleteUserRequest>';
            
            $result = $CPRest->doUserAPI('user/delete', $data);
            
        }
        
        //Delete Subscriptions
        $wpdb->query("DELETE FROM {$wpdb->prefix}users_subscriptions WHERE suite_id NOT IN (SELECT suite_id FROM {$wpdb->prefix}test_suites)");
        
        //Delete Purchases
        $wpdb->query("DELETE FROM {$wpdb->prefix}users_purchases WHERE id NOT IN (SELECT purchase_id FROM {$wpdb->prefix}users_subscriptions");
        
    }
}

add_action('init', 'process_testsuite_actions', 100);
function process_testsuite_actions()
{
    $action = isset($_REQUEST['_wpnonce']) ? $_REQUEST['_wpnonce'] : null;
    if(wp_verify_nonce($action, 'get-brother-suites-and-profile-types'))
    {        
        getBrotherSuitesAndProfileTypes();
    }else if(wp_verify_nonce($action, 'save-suite')){
        saveSuite();        
    }else if(wp_verify_nonce($action, 'hide_testcase')){        
        if(!can_edit_suite($_POST['suite_id']))
        {
            echo 'Permission Denied!';
        }else{
            delete_post_meta($_POST['case_id'], 'test_suite');
            echo 'success';
        }
        exit;
    }else if(wp_verify_nonce($action, 'delete-suite')){
        if(!$_REQUEST['suite_id'])
        {
            addMessage('Invalid Request!', 'error');
        }else{
            if(!can_delete_suite($_REQUEST['suite_id']))
            {
                addMessage('Permission Denied!', 'error');
            }else{
                wp_delete_post($_REQUEST['suite_id']);                
                addMessage('The test suite was removed successfully.');                
            }            
        }
        $redirectUrl = base64_decode($_REQUEST['return']);
        wp_redirect($redirectUrl);
        exit;    
    }else if(wp_verify_nonce($action, 'get-available-templates')){
        global $CPRest;
        
        $templateList = $CPRest->getTemplateList($_POST['name'], $_POST['version_major']);
        echo '<option value="">Select a Template</option>';
        foreach($templateList as $t)
            echo '<option value="' . $t . '">' . $t . '</option>';
        
        exit;
    }
    
    
}


function deleteTestSuite()
{
    return;
    
    $id = $_REQUEST['id'];
    
    $post = get_post($id);

    $redirectUrl = base64_decode($_REQUEST['return']);

    $return = isset($_REQUEST['return']) ? $redirectUrl : "/";
    
    //Check if it is test suite
    if(!$post || $post->post_type != 'test-suite')
    {
        addMessage("Invalid Request!", 'error');
        wp_redirect($return);
        exit;
    }
    
    if(!can_delete_suite($id))
    {
        addMessage("Permission Denied!", 'error');
        wp_redirect($return);
        exit;
    }
    
    if(!wp_delete_post($id, true))
    {
        addMessage("There was an error while deleting the suite", 'error');
        wp_redirect($return);
        exit;
    }
    
    $esb = new ManageESB();
    $esb->deleteTestSuiteNameIDMap($id);
    
    addMessage("The test suite was deleted");
    wp_redirect($return);
    exit;
}

function getBrotherSuitesAndProfileTypes()
{
    $groupID = $_POST['community_id'];
    $user_id = get_current_user_id();
    if(!groups_is_user_admin($user_id, $groupID))
    {
        exit;
    }
    $suite = new TestSuite($_POST['id']);
    
    $brotherSuites = $suite->getBrotherSuites($groupID);
    $suitesHtml = "";
    
    $suitesHtml .= '<select name="ts[]" class="select">' .
       '<option>- Select -</option>';
       foreach($brotherSuites as $row) {
           $suitesHtml .= '<option value="' . $row->ID . '">' . $row->post_title . '</option>';
       }
   $suitesHtml .= '</select>';
    
    //Getting Profile Instances
    $typesHtml = '';
    
    $profileTypes = getCommunityProfileTypes($groupID);
    foreach($profileTypes as $row){
                   
    $typesHtml .= '<div class="grid-cell width50P nopadding">' .
                  ' <input type="checkbox" class="checkbox-input" name="ts_profile_types[]" value="' . $row->id . '" />' . 
                  ' <a href="' . get_site_url() . '?td-action=' . wp_create_nonce("view-profile-type") . '&id=' . $row->id . '" rel="custom-popup" cp-type="ajax">' . $row->title . '</a>' .
                  '</div>';
    }
    header('application/xml');
    echo '<result>';
    echo '<suites><![CDATA[' . $suitesHtml . ']]></suites>';
    echo '<profileTypes><![CDATA[' . $typesHtml . ']]></profileTypes>';
    echo '</result>';
    exit;
}

function saveSuite()
{
    require_once(ABSPATH . "/wp-admin/includes/post.php");
    
    global $wpdb;
    
    $id = $_POST['id'];
    $suite = new TestSuite($id);
    $suite->load();
    $isNew = false;
    if(!$suite->id)
        $isNew = true;
        
    $user_id = get_current_user_id();
    
    if( ($isNew && !can_create_suite($user_id)) || (!$isNew && !can_edit_suite($suite->id, $user_id)) )
    {
        addMessage('Permission Denied!', 'error');
        wp_redirect(get_site_url());
        exit;
    }
    
    $group_id = $_POST['community_id'];
    
    if(!groups_is_user_admin($user_id, $group_id) && !is_super_admin() && is_admin())
    {
        addMessage('Permission Denied!', 'error');
        wp_redirect(get_site_url());
        exit;
    }
    
    //If the major version is 0 and status is Active, set the version to 1.0.0
    if(intval($_POST['ts_version_major']) == 0 && $_POST['ts_status'] == 'Active')
    {
        $_POST['ts_version_major'] = 1;
        $_POST['ts_version_minor'] = 0;
        $_POST['ts_version_patch'] = 0;
    }
    
    if(!$isNew && isNewSuiteVersionExist($suite->familyMark, $suite->version_major, $suite->version_minor, $suite->version_patch))
    {
        $_POST['ts_version_major'] = $suite->version_major;
        $_POST['ts_version_minor'] = $suite->version_minor;
        $_POST['ts_version_patch'] = $suite->version_patch;
    }
    
    //Check Version Updated or not
    $version_updated = false;
    if( intval($suite->version_major) != intval($_POST['ts_version_major']) || intval($suite->version_minor) != intval($_POST['ts_version_minor']) || intval($suite->version_patch) != intval($_POST['ts_version_patch']) )
    {
        $version_updated =  true;
    }
    
    $versions = array();
    $versions[] = !$_POST['ts_version_major'] ? 0 : $_POST['ts_version_major'];
//    if($_POST['ts_version_minor'])
    $versions[] = !$_POST['ts_version_minor'] ? 0 : $_POST['ts_version_minor'];
    
    if($_POST['ts_version_patch'])
        $versions[] = $_POST['ts_version_patch'];
    
    $version = implode(".", $versions);
    
    $post_title = $_POST['ts_name'] . " v" . $version;
    
    if( $isNew || $version_updated ) //Create New Suite
    {
        //Update Test Suite Title and Excerpt
        $id = wp_insert_post(array('post_title' => $post_title, 'post_excerpt' => $_POST['excerpt'], 'post_type'=>'test-suite', 'post_status' => 'publish'), true);
        if(is_wp_error($id))
        {
            addMessage($id->get_error_message(), 'error');            
            return;
        }
        
        if(!$version_updated){
            cp_update_post_meta($id, 'hide_suite', 0);
        }
                
    }else{  //Update Suite
        $post_name = sanitize_title($post_title);
        $post = get_post($id);
        //Update Post Name
        $post_name = wp_unique_post_slug($post_name, $id, $post->post_status, $post->post_type, $post->post_parent);
        
        $guid = get_sample_permalink($post->ID, $post_title, $post_name);
        
        if( !wp_update_post(array('ID' => $id, 'post_title' => $post_title, 'post_excerpt' => $_POST['excerpt'], 'post_name' => $guid[1], 'guid' => str_replace('%pagename%', $guid[1], $guid[0]))) )
        {
            addMessage('There was an error while updating the test suite.', true);
            return;
        }
        
    }
    
    //Save Types
    $suiteTypes = isset($_POST['test_suite_type']) ? $_POST['test_suite_type'] : array();
    
    $r = wp_set_post_terms($id, $suiteTypes, 'test_suite_type');
    
//    $identifier = sanitize_title($_POST['ts_identifier']);
    $identifier = $_POST['ts_identifier'];
    
    //Update Post Metas
    cp_update_post_meta($id, 'ts_name', $_POST['ts_name']);
    
    $esb = new ManageESB();
    $esb->saveTestSuiteInfo($id, $identifier . "_V" . $version, $post_title);    
    
    cp_update_post_meta($id, 'ts_identifier', $identifier);
    cp_update_post_meta($id, 'ts_issue_date', date('Y-m-d H:i:s', getUTCTimeStamp($_POST['ts_issue_date'])));
    cp_update_post_meta($id, 'ts_issuer', $_POST['ts_issuer']);
    cp_update_post_meta($id, 'ts_status', $_POST['ts_status']);
    cp_update_post_meta($id, 'ts_revision_description', $_POST['ts_revision_description']);
    cp_update_post_meta($id, 'ts_version_major', $_POST['ts_version_major']);
    cp_update_post_meta($id, 'ts_version_minor', $_POST['ts_version_minor']);
    cp_update_post_meta($id, 'ts_version_patch', $_POST['ts_version_patch']);
    update_post_meta($id, 'ts_description', $_POST['ts_description']);
    
    $property_name_data = $_POST['message_template_name']; 
    cp_update_post_meta($id, 'message_template_name', $property_name_data);
    $property_value_data = $_POST['message_template_url']; 
    cp_update_post_meta($id, 'message_template_url', $property_value_data);
    
    cp_update_post_meta($id, 'ts_profile_types', cp_implode($_POST['ts_profile_types']));
    
    //Remove the assigned group
    $wpdb->delete($wpdb->prefix . "bp_groups_testsuites", array("ts_ids"=>$id));
    if(!$group_id)
    {        
        //Remove Meta data
        delete_post_meta($id, "community_id");
    }else{
        //Insert Group Id to bp_groups_testsuites table
        $wpdb->insert($wpdb->prefix . "bp_groups_testsuites", array('group_id' => $group_id, 'ts_ids' => $id));
        //Update Post Meta
        cp_update_post_meta($id, 'community_id', $group_id);
    }
    
    //Save Initial Messages
    $init_message = $_POST['init_message'];
    cp_update_post_meta($id, 'init_message', $init_message);
    
    //Save Roles    
    $roleNames = array();
    $roleDescs = array();
    $roleProfileTypes = array();
    delete_post_meta($id, 'role_names');
    delete_post_meta($id, 'role_descs');
    delete_post_meta($id, 'role_profile_types');
    
    if(isset($_POST['role_names']))
    {
        foreach($_POST['role_names'] as $i=>$rname)
        {
            if(trim($rname) != '')
            {
                $roleNames[] = $rname;
                $roleDescs[] = $_POST['role_descs'][$i];
                $roleProfileTypes[] = trim( $_POST['role_types'][$i] );
            }
        }
        
        if(count($roleNames) > 0)
        {
            add_post_meta($id, 'role_names', '|' . implode('|', $roleNames) . '|', true);
            add_post_meta($id, 'role_descs', '|' . implode('|', $roleDescs) . '|', true);
            add_post_meta($id, 'role_profile_types', '|' . implode('|', $roleProfileTypes) . '|', true);
        }
    }
    
    //Save Related Test Suites
    $ts = $_POST['ts'] ;
    $ts_desc = $_POST['ts_desc'];

    update_post_meta($id, 'ts', $ts);
    update_post_meta($id, 'ts_desc', $ts_desc);
    
    $lvl_code = array();
    $lvl_desc = array() ;
    //Save Conformance Level
    foreach($_POST['lvl_code'] as $i=> $code)
    {
        if(!trim($code))
            continue;
        $lvl_code[] = $code;
        $lvl_desc[] = $_POST['lvl_desc'][$i];
    }
    
    
    update_post_meta($id, 'lvl_code', $lvl_code);
    update_post_meta($id, 'lvl_desc', $lvl_desc);
    
    //Subscription Price
    cp_update_post_meta($id, 'monthly_subscription_price', $_POST['monthly_subscription_price']);
    cp_update_post_meta($id, 'signup_price', $_POST['signup_price']);
    
    //Save Scenarios
    //Removed deleted scenarios
    if(!isset($_POST['scenario_id']))
    {
        $_POST['scenario_id'] = array();
        $_POST['scenario_code'] = array();
    }
    $query = "DELETE FROM {$wpdb->prefix}test_suites_scenarios WHERE suite_id=" . $id . " AND id NOT IN ('" . implode("', '", $_POST['scenario_id']) . "')";    
    $wpdb->query($query);
    
    foreach($_POST['scenario_code'] as $idx => $code)
    {
        if(!$code)
            continue;
        if(!$_POST['scenario_id'][$idx])                
        {
            $wpdb->insert($wpdb->prefix . "test_suites_scenarios", array('suite_id' =>  $id, 'code' => $_POST['scenario_code'][$idx], 'description' => stripslashes_deep($_POST['scenario_desc'][$idx]), 'sequence' => $_POST['scenario_sequence'][$idx]));
        }else{
            $wpdb->update($wpdb->prefix . "test_suites_scenarios", array('suite_id' =>  $id, 'code' => $_POST['scenario_code'][$idx], 'description' => stripslashes_deep($_POST['scenario_desc'][$idx]), 'sequence' => $_POST['scenario_sequence'][$idx]), array('id' => $_POST['scenario_id'][$idx]));
        }
    }
    
    //Save Spec Documents        
    $docs_names = $_POST['doc_name'];
    $docs_descs = $_POST['doc_desc'];
    $docs_locs = $_POST['doc_loc'];
    $docs_files = $_FILES['doc_file'];    
    //Remove Old documents
    $wpdb->query("DELETE FROM " . $wpdb->prefix . "ts_options_documents WHERE ts_id=" . $id);
    //Save New Data
    
    if(!$docs_names)
        $docs_names = array();
    
    if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );
    
    foreach ($docs_names as $idx => $doc_name) {
        $doc_desc = $docs_descs[$idx];
        $doc_loc = $docs_locs[$idx];
        $doc_file = array(
            'name' => $docs_files['name'][$idx],
            'tmp_name' => $docs_files['tmp_name'][$idx],
            'error' => $docs_files['error'][$idx],
            'size' => $docs_files['size'][$idx],
            'type' => $docs_files['type'][$idx],
        );
        $doc_file_path = '';
        $dest = '';
        if(!$doc_name && !$doc_loc && $doc_file['error'] != UPLOAD_ERR_OK)
            continue;
        if (!$doc_loc && $doc_file['error'] == UPLOAD_ERR_OK) {
            $uploaded = wp_handle_upload($doc_file, array('test_form' => false));
            if($uploaded){
                $doc_loc = $uploaded['url'];            
                $doc_file_path = $uploaded['file'];
            }
        }
        $wpdb->insert(
            $wpdb->prefix.'ts_options_documents', 
            array( 
                'ts_id' => $id,
                'doc_name' => $doc_name,
                'doc_desc' => $doc_desc,
                'doc_loc_url' => $doc_loc,
                'doc_file_name'=> $doc_file['name'],
                'doc_file_path' => $doc_file_path
                
            ), 
            array( 
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s'
            )
        );
    }
    
    $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}test_suites WHERE suite_id=%d", $id);
    $rid = $wpdb->get_var($query);
    if(!$rid)
    {
        //Calculate Bother Mark
        if(!$version_updated){
            $family_mark = $id;
        }else{
            $family_mark = $wpdb->get_var("SELECT family_mark FROM {$wpdb->prefix}test_suites WHERE suite_id=" . $suite->id);
        }
        
        $wpdb->insert($wpdb->prefix . "test_suites", 
                        array('suite_id' => $id, 
                              'suite_title' => $_POST['ts_name'], 
                              'version_major' => $_POST['ts_version_major'], 
                              'version_minor' => $_POST['ts_version_minor'], 
                              'version_patch' => $_POST['ts_version_patch'],
                              'family_mark' => $family_mark
                              )
                     );
    }
    
    if(!$isNew && $suite->name != $_POST['ts_name'])
    {
        suiteTitleUpdated($suite->familyMark, $_POST['ts_name']);
    }
    
    //If Name is updated, apply it to all versions
    if(!$isNew && $suite->identifier != $identifier)
    {
        suiteNameUpdated($suite->identifier, $identifier, $suite->familyMark);
    }
    
    //Save Test Suite to wp_test_suites table
    if($version_updated)
    {
        
        //Hide Major version 0
        if(intval($suite->version_major) == 0)
        {
            cp_update_post_meta($suite->id, 'hide_suite', 1);
        }
        cp_sort_test_suites($suite->familyMark, $_POST['ts_version_major']);
        cp_update_subscriptions($suite->familyMark, $_POST['ts_version_major']);
        
        //Associate all test case that are linked to the old version to the new one with Default conformance level
        $query = "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key='conformance_level_" . $suite->id . "'";
        $rows = $wpdb->get_results($query);
        foreach($rows as $row)
        {
            $wpdb->insert($wpdb->postmeta, array('post_id'=> $row->post_id, 'meta_key' => 'conformance_level_' . $id, 'meta_value' => $row->meta_value));
            $wpdb->insert($wpdb->postmeta, array('post_id'=> $row->post_id, 'meta_key' => 'test_suite', 'meta_value' => $id));            
        }
        $query = "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key='scenario_" . $suite->id . "'";
        $rows = $wpdb->get_results($query);
        foreach($rows as $row)
        {
            $wpdb->insert($wpdb->postmeta, array('post_id'=> $row->post_id, 'meta_key' => 'scenario_' . $id, 'meta_value' => $row->meta_value));
        }
        
        //Update Subscrition
//        updateSubscribedSuiteId($suite->familyMark);
        //Copy test plains to the new version
        $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}test_plans WHERE suite_id=%d", $suite->id);
        $rows = $wpdb->get_results($query, ARRAY_A);
        
        foreach ($rows as $row) {
            $t_data = $row;
            $t_data['suite_id'] = $id;
            $t_data['created_date'] = date('Y-m-d H:i:s');
            unset($t_data['id']);
            
            $wpdb->insert($wpdb->prefix . "test_plans", $t_data);
        }
    }
    
    //Send Notification Email
    if(!$isNew && isset($_POST['send-notification']))
    {
        
        $group = groups_get_group(array('group_id'=>$group_id));
        
        $emailData = array(
            '[community]' => bp_get_group_name($group),
            '[community_url]' => bp_get_group_permalink($group),
            '[suite_name]' => $_POST['ts_name'],
            '[suite_url]' => get_permalink($id),
            '[editor_name]' => cp_get_user_fullname($user_id)
        );
        //Getting Group Members
        $members = BP_Groups_Member::get_all_for_group($group_id, false, false, false);
        
        if($members && $members['count'] > 0)
        {
            foreach($members['members'] as $member)       
            {
                if($member->user_id != $user_id && get_user_meta($member->user_id, 'notify_suite_changes' . $id, true))
                {
                    $emailData['[name]'] = cp_get_user_fullname($member->user_id);                
                    cp_send_email(array('name' => $emailData['[name]'], 'email' => $member->user_email), 'suite_changed', $emailData);
                }
            }
        }
    }
    
    addMessage('Test Suite was saved successfully!');
    wp_redirect(get_permalink($id));
    exit;
}

//Hide all versions except the latest one
function cp_sort_test_suites($familyMark, $version_major)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}test_suites WHERE family_mark = %d AND version_major=%d ORDER BY version_minor DESC, version_patch DESC", $familyMark, $version_major);
    $suites = $wpdb->get_results($query);
    foreach($suites as $i=>$s)
    {
        update_post_meta($s->suite_id, 'hide_suite', $i > 0 ? 1 : 0);
    }
}

/**
* Update the suite id by the latest version in the subscriptions
* 
* @param Int $familyMark
* @param Int $version_major
*/
function cp_update_subscriptions($familyMark, $version_major)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT suite_id FROM {$wpdb->prefix}test_suites WHERE family_mark = %d AND version_major=%d ORDER BY version_minor DESC, version_patch DESC", $familyMark, $version_major);
    $ids = $wpdb->get_col($query);
    
    if(!$ids)
        return;
        
    $query = "UPDATE {$wpdb->prefix}users_subscriptions SET suite_id={$ids[0]} WHERE suite_id IN (" . implode(", ", $ids) . ")";
    
    $wpdb->query($query);
    
    return;
}

function isNewSuiteVersionExist($familyMark, $version_major, $version_minor = null, $version_patch = null)
{
    global $wpdb;
    
    if($version_minor === null && $version_patch === null)
    {
        $query = $wpdb->prepare("SELECT suite_id FROM {$wpdb->prefix}test_suites WHERE family_mark = %d AND 
                                 version_major > %d", $familyMark, $version_major);
                              
    }else if($version_patch === null){
        $query = $wpdb->prepare("SELECT suite_id FROM {$wpdb->prefix}test_suites WHERE family_mark = %d AND                             
                                 version_major=%d AND version_minor > %d", $familyMark, $version_major, $version_minor);
                              
    }else{
        $query = $wpdb->prepare("SELECT suite_id FROM {$wpdb->prefix}test_suites WHERE family_mark = %d AND 
                                 version_major=%d ANd version_minor=%d AND version_patch > %d", $familyMark, $version_major, $version_minor, $version_patch);
                                  
    }
    
    if($wpdb->get_var($query))
        return true;
    else 
        return false;
}

function suiteTitleUpdated($family_mark, $new)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "test_suites WHERE family_mark=%d", $family_mark);
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
        $post = get_post($row->suite_id);
        //Update Post Name
        $post_name = wp_unique_post_slug($post_name, $row->suite_id, $post->post_status, $post->post_type, $post->post_parent);
        
        $guid = get_sample_permalink($post->ID, $post_title, $post_name);
        
        wp_update_post(array('ID' => $post->ID, 'post_title' => $post_title, 'post_name' => $guid[1], 'guid' => str_replace('%pagename%', $guid[1], $guid[0])));
        $wpdb->update($wpdb->prefix . "test_suites", array('suite_title' => $new), array('suite_id' => $row->suite_id));
        cp_update_post_meta($post->ID, 'ts_name', $new);
        
        //Update ESB Table
        $esb->saveTestSuiteInfo($post->ID, get_post_meta($post->ID, 'ts_identifier', true) . '_V' . $version, $post_title);
    }
    
}

function suiteNameUpdated($old, $new, $family_mark)
{
    global $wpdb;
    
    $query = $wpdb->prepare("UPDATE " . $wpdb->postmeta . " SET meta_value=%s WHERE meta_key='ts_identifier' AND meta_value=%s", $new, $old);        
    $wpdb->query($query);    
    
    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "test_suites WHERE family_mark=%d", $family_mark);
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
        
        $post = get_post($row->suite_id);
        $post_title = $post->post_title;
        
        $esb->saveTestSuiteInfo($post->ID, get_post_meta($post->ID, 'ts_identifier', true) . '_V' . $version, $post_title);
    }
}

/**
* Update subscribed suite id by the latest version in the same family
* 
* @param mixed $familyMark
*/
function updateSubscribedSuiteId($familyMark)
{
    global $wpdb;
    
    //find Subscribed suite id in the family
    $query = $wpdb->prepare("SELECT sp.* FROM {$wpdb->prefix}users_subscriptions AS sp INNER JOIN {$wpdb->prefix}test_suites as st ON sp.suite_id=st.suite_id WHERE st.family_mark=%d", $familyMark);
    $row = $wpdb->get_row($query);
    
    if($row)
    {
        //Getting Latest Version in the family
        $query = $wpdb->prepare("SELECT suite_id FROM {$wpdb->prefix}test_suites WHERE family_mark=%d ORDER BY version_major DESC, version_minor DESC, version_patch DESC LIMIT 1", $familyMark);
        $latestId =  $wpdb->get_var($query);
        
        $wpdb->update($wpdb->prefix . "users_subscriptions", array("suite_id" => $latestId), array('id' => $row->id));
    }
}


function add_community_join_query($join, $object)
{
    global $wpdb, $post;
    
    $join .= " INNER JOIN {$wpdb->postmeta} AS community_meta ON community_meta.post_id={$wpdb->posts}.ID AND community_meta.meta_key='community_id' ";
    $join .= " INNER JOIN {$wpdb->prefix}bp_groups AS groups ON community_meta.meta_value=groups.id ";
    
    return $join;
}

function add_community_orderby_query($orderby, $object)
{
    global $wpdb, $post;
    
    $orderby = " groups.name ASC, " . $orderby;
    
    return $orderby;
}

function add_community_fields_query($fields, $object)
{
    global $wpdb, $post;
    
    if($fields)
        $fields .= ", ";
    $fields .= " groups.name AS communityName ";
    
    return $fields;
}

function add_suite_family_mark_join_query($join, $object)
{
    global $wpdb, $post;
    
    $join .= " INNER JOIN {$wpdb->prefix}test_suites ON {$wpdb->posts}.ID={$wpdb->prefix}test_suites.suite_id ";
    
    return $join;
}

function add_suite_family_mark_fields_query($fields, $object)
{
    global $wpdb, $post;
    
    if($fields)
        $fields .= ", ";
    $fields .= " {$wpdb->prefix}test_suites.family_mark ";
    
    return $fields;    
}
function add_suite_family_mark_orderby_query($orderby, $object)
{
    global $wpdb, $post;
    
    $orderby = " family_mark ASC, " . $orderby;
    
    return $orderby;
}

/**
* Get Xero Items
* 
* @return array()
* 
*/
function ct_get_xero_items()
{
    global $wpdb;
    
    $query = "SELECT * FROM {$wpdb->prefix}xeroitems ORDER BY `code` ASC";
    $rows = $wpdb->get_results($query);
    
    return $rows;    
}

function ct_get_suite_max_version( $suite_id, $return_suite_name = false ){
    global $wpdb;
    $current_suite_info = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}test_suites WHERE suite_id = %d", $suite_id));
    $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}test_suites WHERE suite_title = %s AND version_major = %d ORDER BY version_minor DESC, version_patch DESC", $current_suite_info->suite_title, $current_suite_info->version_major );
    $rows = $wpdb->get_row($query);
    if( ! $rows ){
        return '';
    }
    if( $return_suite_name ){
        $str = $rows->suite_title.' v'.$rows->version_major.'.'.$rows->version_minor;
        if( $rows->version_patch !== '0' ){
            $str .= $str.'.'.$rows->version_patch;
        }
        return $str;
    }
    $str = $rows->version_major.'.'.$rows->version_minor;
    if( $rows->version_patch !== '0' ){
        $str .= $str.'.'.$rows->version_patch;
    }
    return $str;
}