<?php
/**
* Process Actions
*/

add_action('before_delete_post', 'remove_suite_name_id_map', 10, 1);
function remove_suite_name_id_map($postid)
{
    global $wpdb;
    
    $post = get_post($postid);
    
    if($post->post_type == 'test-suite')
    {    
        $esb = new ManageESB();
        $esb->deleteTestSuiteNameIDMap($postid);
        
        //Delete Conformance Level    
        $wpdb->delete($wpdb->postmeta, array('meta_key'=> 'conformance_level_' . $postid));
        
        $wpdb->delete($wpdb->prefix . "test_suites", array('suite_id'=> $postid));
        
        //Delete Scenarios
        $wpdb->delete($wpdb->prefix . "test_suites_scenarios", array('suite_id' => $postid));
        
        cp_sort_test_suites(get_post_meta($postid, 'ts_name', true), get_post_meta($postid, 'ts_version_major', true));
        
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
                addMessage('The test suite was removed successfully.', 'error');                
            }            
        }    
        wp_redirect(base64_decode($_REQUEST['return']));
        exit;    
    }
    
    
}

function deleteTestSuite()
{
    return;
    
    $id = $_REQUEST['id'];
    
    $post = get_post($id);
    
    $return = isset($_REQUEST['return']) ? base64_decode($_REQUEST['return']) : "/";
    
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
    
    if(!$isNew && isNewVersionExist($suite->name, $suite->version_major, $suite->version_minor, $suite->version_patch))
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
    
    $esb = new ManageESB();
    $esb->addTestSuiteNameIDMap($id, $post_title);
    
    //Save Types
    $suiteTypes = isset($_POST['test_suite_type']) ? $_POST['test_suite_type'] : array();
    
    $r = wp_set_post_terms($id, $suiteTypes, 'test_suite_type');
    
//    $identifier = sanitize_title($_POST['ts_identifier']);
    $identifier = $_POST['ts_identifier'];
    
    //Update Post Metas
    cp_update_post_meta($id, 'ts_name', $_POST['ts_name']);
    cp_update_post_meta($id, 'ts_identifier', $identifier);
    cp_update_post_meta($id, 'ts_issue_date', date('Y-m-d', strtotime($_POST['ts_issue_date'])));
    cp_update_post_meta($id, 'ts_issuer', $_POST['ts_issuer']);
    cp_update_post_meta($id, 'ts_status', $_POST['ts_status']);
    cp_update_post_meta($id, 'ts_revision_description', $_POST['ts_revision_description']);
    cp_update_post_meta($id, 'ts_version_major', $_POST['ts_version_major']);
    cp_update_post_meta($id, 'ts_version_minor', $_POST['ts_version_minor']);
    cp_update_post_meta($id, 'ts_version_patch', $_POST['ts_version_patch']);
    cp_update_post_meta($id, 'ts_description', $_POST['ts_description']);
    
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
    delete_post_meta($id, 'role_names');
    delete_post_meta($id, 'role_descs');
    
    if(isset($_POST['role_names']))
    {
        foreach($_POST['role_names'] as $i=>$rname)
        {
            if(trim($rname) != '')
            {
                $roleNames[] = $rname;
                $roleDescs[] = $_POST['role_descs'][$i];
            }
        }
        
        if(count($roleNames) > 0)
        {
            add_post_meta($id, 'role_names', '|' . implode('|', $roleNames) . '|', true);
            add_post_meta($id, 'role_descs', '|' . implode('|', $roleDescs) . '|', true);
        }
    }
    
    //Save Related Test Suites
    $ts = $_POST['ts'] ;
    $ts_desc = $_POST['ts_desc'];

    cp_update_post_meta($id, 'ts', $ts);
    cp_update_post_meta($id, 'ts_desc', $ts_desc);
    
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
    
    
    cp_update_post_meta($id, 'lvl_code', $lvl_code);
    cp_update_post_meta($id, 'lvl_desc', $lvl_desc);
    
    //Subscription Price
    cp_update_post_meta($id, 'monthly_subscription_price', $_POST['monthly_subscription_price']);
    
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
            $wpdb->insert($wpdb->prefix . "test_suites_scenarios", array('suite_id' =>  $id, 'code' => $_POST['scenario_code'][$idx], 'description' => $_POST['scenario_desc'][$idx], 'sequence' => $_POST['scenario_sequence'][$idx]));
        }else{
            $wpdb->update($wpdb->prefix . "test_suites_scenarios", array('suite_id' =>  $id, 'code' => $_POST['scenario_code'][$idx], 'description' => $_POST['scenario_desc'][$idx], 'sequence' => $_POST['scenario_sequence'][$idx]), array('id' => $_POST['scenario_id'][$idx]));
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
    
    $query = $wpdb->prepare("SELECT suite_id FROM {$wpdb->prefix}test_suites WHERE suite_id=%d", $id);
    $rid = $wpdb->get_var($query);
    if(!$rid)
    {
        $wpdb->insert($wpdb->prefix . "test_suites", 
                        array('suite_id' => $id, 
                              'suite_title' => $_POST['ts_name'], 
                              'version_major' => $_POST['ts_version_major'], 
                              'version_minor' => $_POST['ts_version_minor'], 
                              'version_patch' => $_POST['ts_version_patch'])
                     );
    }
    
    if(!$isNew && $suite->name != $_POST['ts_name'])
    {
        suiteTitleUpdated($suite->name, $_POST['ts_name']);
    }
    
    //If Name is updated, apply it to all versions
    if(!$isNew && $suite->identifier != $identifier)
    {
        suiteNameUpdated($suite->identifier, $identifier);
    }
    
    //Save Test Suite to wp_test_suites table
    if($version_updated)
    {
        
        //Hide Major version 0
        if(intval($suite->version_major) == 0)
        {
            cp_update_post_meta($suite->id, 'hide_suite', 1);
        }
        cp_sort_test_suites($_POST['ts_name'], $_POST['ts_version_major']);
        
        //Associate all test case that are linked to the old version to the new one with Default conformance level
        $query = "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key='conformance_level_" . $suite->id . "'";
        $rows = $wpdb->get_results($query);
        foreach($rows as $row)
        {
            $wpdb->insert($wpdb->postmeta, array('post_id'=> $row->post_id, 'meta_key' => 'conformance_level_' . $id, 'meta_value' => $row->meta_value));
            $wpdb->insert($wpdb->postmeta, array('post_id'=> $row->post_id, 'meta_key' => 'test_suite', 'meta_value' => $id));
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
function cp_sort_test_suites($title, $version_major)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}test_suites WHERE suite_title = %s AND version_major=%d ORDER BY version_minor DESC, version_patch DESC", $title, $version_major);
    $suites = $wpdb->get_results($query);
    foreach($suites as $i=>$s)
    {
        update_post_meta($s->suite_id, 'hide_suite', $i > 0 ? 1 : 0);
    }
}

function isNewSuiteVersionExist($title, $version_major, $version_minor = null, $version_patch = null)
{
    global $wpdb;
    
    if($version_minor === null && $version_patch === null)
    {
        $query = $wpdb->prepare("SELECT suite_id FROM {$wpdb->prefix}test_suites WHERE suite_title = %s AND 
                                 version_major > %d", $title, $version_major);
                              
    }else if($version_patch === null){
        $query = $wpdb->prepare("SELECT suite_id FROM {$wpdb->prefix}test_suites WHERE suite_title = %s AND                             
                                 version_major=%d AND version_minor > %d", $title, $version_major, $version_minor);
                              
    }else{
        $query = $wpdb->prepare("SELECT suite_id FROM {$wpdb->prefix}test_suites WHERE suite_title = %s AND 
                                 version_major=%d ANd version_minor=%d AND version_patch > %d", $title, $version_major, $version_minor, $version_patch);
                                  
    }
    
    if($wpdb->get_var($query))
        return true;
    else 
        return false;
}

function suiteTitleUpdated($old, $new)
{
    global $wpdb;
    
    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "test_suites WHERE suite_title=%s", $old);
    $suites = $wpdb->get_results($query);
    
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
    }
    
}

function suiteNameUpdated($old, $new)
{
    global $wpdb;
    
    $query = $wpdb->prepare("UPDATE " . $wpdb->postmeta . " SET meta_value=%s WHERE meta_key='ts_identifier' AND meta_value=%s", $new, $old);        
    $wpdb->query($query);    
    
}

/*if(isset($_GET['fix_case_scenario']))
{
    global $wpdb;
    
    //Getting Default Levels
    $query = "SELECT suite_id, id FROM wp_test_suites_scenarios WHERE `code`='" . TEST_SUITE_DEFAULT_SCENARIO_CODE . "'";
    $rows = $wpdb->get_results($query);
    $dScenarios = array();
    foreach($rows as $row)
        $dScenarios[$row->suite_id] = $row->id;
    
    $query = "SELECT * FROM {$wpdb->postmeta} WHERE meta_key='test_suite' order by post_id";
    $rows =  $wpdb->get_results($query);
    
    foreach($rows as $row)
    {
        if(!$dScenarios[$row->meta_value])
            continue;
        $wpdb->insert($wpdb->postmeta, array('post_id' => $row->post_id, 'meta_key' => 'scenario_' . $row->meta_value, 'meta_value' => $dScenarios[$row->meta_value]));
    }
    exit;
}*/