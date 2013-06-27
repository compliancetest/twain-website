<?php
/**
* Process Actions
*/

add_action('init', 'process_testsuite_actions', 100);
function process_testsuite_actions()
{
    $action = isset($_POST['_wpnonce']) ? $_POST['_wpnonce'] : null;
    if(wp_verify_nonce($action, 'get-brother-suites'))
    {        
        getBrotherSuites();
    }else if(wp_verify_nonce($action, 'save-suite')){
        saveSuite();        
    }
}

function getBrotherSuites()
{
    $groupID = $_POST['community_id'];
    $user_id = get_current_user_id();
    if(!groups_is_user_admin($user_id, $groupID))
    {
        exit;
    }
    $suite = new TestSuite($_POST['id']);
    
    $brotherSuites = $suite->getBrotherSuites($groupID);
    
    ?>
    <select name="ts[]" class="select">
       <option>- Select -</option>
       <?php foreach($brotherSuites as $row) { 
           echo '<option value="' . $row->ID . '">' . $row->post_title . '</option>';
       } ?>
   </select>
    <?php
    exit;
}

function saveSuite()
{
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
    
    if(!groups_is_user_admin($user_id, $group_id))
    {
        addMessage('Permission Denied!', 'error');
        wp_redirect(get_site_url());
        exit;
    }
    if(!$id) //Create New Suite
    {
        //Update Test Suite Title and Excerpt
        $id = wp_insert_post(array('post_title' => $_POST['ts_name'], 'post_excerpt' => $_POST['excerpt'], 'post_type'=>'test-suite', 'post_status' => 'publish'), true);
        if(is_wp_error($id))
        {
            addMessage($id->get_error_message(), 'error');            
            return;
        }
    }else{  //Update Suite
        if(!wp_update_post(array('ID' => $id, 'post_title' =>$_POST['ts_name'], 'post_excerpt' => $_POST['excerpt'], 'post_name' => sanitize_title($_POST['ts_name']))))
        {
            addMessage('There was an error while updating the test suite.', true);
            return;
        }
    }
    //Save Types
    $suiteTypes = isset($_POST['test_suite_type']) ? $_POST['test_suite_type'] : array();
    
    $r = wp_set_post_terms($id, $suiteTypes, 'test_suite_type');
    
    //Update Post Metas
    update_post_meta($id, 'ts_name', $_POST['ts_name']);
    update_post_meta($id, 'ts_identifier', $_POST['ts_identifier']);
    update_post_meta($id, 'ts_issue_date', date('Y-m-d', strtotime($_POST['ts_issue_date'])));
    update_post_meta($id, 'ts_issuer', $_POST['ts_issuer']);
    update_post_meta($id, 'ts_status', $_POST['ts_status']);
    update_post_meta($id, 'ts_revision_description', $_POST['ts_revision_description']);
    update_post_meta($id, 'ts_version', $_POST['ts_version']);
    update_post_meta($id, 'ts_description', $_POST['ts_description']);
    
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
        update_post_meta($id, 'community_id', $group_id);
    }
    
    //Save Initial Messages
    $init_message = $_POST['init_message'];
    update_post_meta($id, 'init_message', $init_message);
    
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

    update_post_meta($id, 'ts', $ts);
    update_post_meta($id, 'ts_desc', $ts_desc);
    
    //Save Conformance Level
    $lvl_code = $_POST['lvl_code'];
    $lvl_desc = $_POST['lvl_desc'] ;
    
    update_post_meta($id, 'lvl_code', $lvl_code);
    update_post_meta($id, 'lvl_desc', $lvl_desc);
    
    //Subscription Price
    update_post_meta($id, 'monthly_subscription_price', $_POST['monthly_subscription_price']);
    
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
        
        $dest = '';
        if(!$doc_name && !$doc_loc && $doc_file['error'] != UPLOAD_ERR_OK)
            continue;
        if (!$doc_loc && $doc_file['error'] == UPLOAD_ERR_OK) {
            $uploaded = wp_handle_upload($doc_file, array('test_form' => false));
            if($uploaded)
                $doc_loc = $uploaded['url'];            
        }
        $wpdb->insert(
            $wpdb->prefix.'ts_options_documents', 
            array( 
                'ts_id' => $id,
                'doc_name' => $doc_name,
                'doc_desc' => $doc_desc,
                'doc_loc_url' => $doc_loc,
                'doc_file_name'=> $doc_file['name'],
                'doc_file_path' => $dest
                
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
    
    addMessage('Test Suite was saved successfully!');
    wp_redirect(get_permalink($id));
    exit;
}