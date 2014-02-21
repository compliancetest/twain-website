<?php
/**
* Tmp Functions to fix data
* Only Supper Admin Can access this page
*/

//Fix the family_mark of test cases

if(is_super_admin())
{
    add_action('init', 'process_tmp_function');
    
    function process_tmp_function(){
        global $wpdb, $CPRest;
        
        if(isset($_GET['fix_suite_family_mark']))
        {
            //Delete Old Cases
            $query = $wpdb->query("DELETE FROM wp_test_suites");
            
            ct_fix_whole_test_suites_table();
            
            die("Done!");                        
        }
        if(isset($_GET['fix_case_family_mark']))
        {
            //Delete Old Cases
            $query = $wpdb->query("DELETE FROM wp_test_cases");
            
            ct_fix_whole_test_cases_table();
            
            die("Done!");                        
        }
        if(isset($_GET['fix_case_scenario']))
        {        
            
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
            
            die("Done!");
        }
        
        //Fix Hide Case
        if(isset($_GET['fix_hide_case']))
        {
            $query = "SELECT * FROM {$wpdb->prefix}test_cases ORDER BY family_mark, version_major DESC, version_minor DESC, version_patch DESC";
            $cases = $wpdb->get_results($query);
            $familyMark = 0; $majorVersion = -1;
            foreach($cases as $i=>$s)
            {
                if($familyMark != $s->family_mark || $majorVersion != $s->version_major)
                {
                    update_post_meta($s->case_id, 'hide_case', 0);
                }else{
                    update_post_meta($s->case_id, 'hide_case', 1);
                }
                $familyMark = $s->family_mark;
                $majorVersion = $s->version_major;
            }
            echo "completed";
            exit;
        }
        
        
        if(isset($_GET['fix_test_suite_configuration'])){
            $esb = new ManageESB();
            
            //Getting Test Suites
            $args = array(
                'post_type' => 'test-suite',         
                'posts_per_page' => -1
            );
            
            $all_posts = new WP_Query($args);
            $allSuites = $all_posts->get_posts();
            
            foreach($allSuites as $row)
            {
                $version_major = get_post_meta($row->ID, 'ts_version_major', true);
                $version_minor = get_post_meta($row->ID, 'ts_version_minor', true);
                $version_patch = get_post_meta($row->ID, 'ts_version_patch', true);
                
                $versions = array();            
                $versions[] = !$version_major ? 0 : $version_major;
                $versions[] = !$version_minor ? 0 : $version_minor;
                if($version_patch)
                    $versions[] = $version_patch;
                
                $version = implode(".", $versions);
                
                $title = $row->post_title;
                $suite_id = get_post_meta($row->ID, 'ts_identifier', true) . '_V' . $version;
                $esb->saveTestSuiteInfo($row->ID, $suite_id, $title);
            }
            
            die('Completed!');
        }
        if(isset($_GET['fix_test_case_configuration'])){
            $esb = new ManageESB();
            
            //Getting Test Suites
            $args = array(
                'post_type' => 'test-case',         
                'posts_per_page' => -1
            );
            
            $all_posts = new WP_Query($args);
            $allCases = $all_posts->get_posts();
            
            foreach($allCases as $row)
            {
                $version_major = get_post_meta($row->ID, 'version_major', true);
                $version_minor = get_post_meta($row->ID, 'version_minor', true);
                $version_patch = get_post_meta($row->ID, 'version_patch', true);
                
                $versions = array();            
                $versions[] = !$version_major ? 0 : $version_major;
                $versions[] = !$version_minor ? 0 : $version_minor;
                if($version_patch)
                    $versions[] = $version_patch;
                
                $version = implode(".", $versions);
                
                $title = $row->post_title;
                $caseId = get_post_meta($row->ID, 'test_case_id', true) . '_V' . $version;
                $esb->saveTestCaseInfo($row->ID, $caseId, get_post_meta($newId, 'outcome_type', true), get_post_meta($newId, 'message_count', true));
            }
            
            die('Completed!');
        }
        
        if(isset($_GET['fix_purchases'])){
            
            //Getting Subscriptions
            $query = "SELECT * FROM {$wpdb->prefix}users_purchases_backup";
            $results = $wpdb->get_results($query);
            
            foreach($results as $row)
            {
                //Create Purchase
                $wpdb->insert("wp_users_purchases",
                    array(
                        'user_id' => $row->user_id,
                        'price' => $row->price,
                        'paid_amount' => $row->paid_amount,
                        'card_id' => $row->card_id,
                        'created_date' => $row->created_date,
                        'expiry_date' => $row->expiry_date,
                        'status' => $row->status,
                        'inarrears_count' => $row->inarrears_count,
                        'frozen_count' => $row->frozen_count
                    )
                );
                $purcase_id = $wpdb->insert_id;
                //Create Subscrption
                $wpdb->insert("wp_users_subscriptions",
                    array(
                        'user_id' => $row->user_id,
                        'suite_id' => $row->suite_id,
                        'purchase_id' => $purcase_id,
                        'subscribed_date' => $row->created_date,
                        'esb_user_id' => $row->esb_user_id,
                        'harness_username' => $row->harness_username,
                        'harness_password' => $row->harness_password,
                        'harness_endpoint_url' => $row->harness_endpoint_url,
                        'tester_username' => $row->tester_username,
                        'tester_password' => $row->tester_password,
                        'tester_endpoint_url' => $row->tester_endpoint_url,
                        'p_mode_agreement' => $row->p_mode_agreement,
                        'status' => $row->status
                    )
                );
            }
            
            die('Completed!');
        }
        
        if($_GET['fix_profile_meta'])
        {
            $wpdb->delete($wpdb->prefix . 'community_profile_meta', array('1'=>'1'), '%d');
            $results = $wpdb->get_results("SELECT * FROM $wpdb->prefix" . "community_profile_instances");

            foreach ($results as $row) {
                $content = json_decode(base64_decode($row->content));
                $profile_meta = getProfileMetaData($content);
                foreach ($profile_meta as $meta_key => $meta_value) {
                    $wpdb->insert($wpdb->prefix . "community_profile_meta", array(
                        'profile_id' => $row->id,
                        'meta_key' => $meta_key,
                        'meta_value' => $meta_value,
                    ));
                }
            }

            echo count($results) . ' profiles for searching.';
            die();
        }
        
        if($_GET['remove_esb_account'])
        {
            //Remove Backend Account
            $data = '<api:deleteUserRequest xmlns:api="http://compliancetest.net/api">
                        <api:user>
                            <api:userId>' . $_GET['esb_id'] . '</api:userId>                        
                        </api:user>
                    </api:deleteUserRequest>';
            
            $result = $CPRest->doUserAPI('user/delete', $data);
            
            echo $results;
            die();
        }
        
        if(isset($_GET['fix_group_download']))
        {
            $downloads = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "bp_groups_downloads");
            foreach ($downloads as $row) {
                $data = array();
                $data['download_file'] = file_get_contents($row->location);
                $data = stripslashes_deep( $data );
                
                $wpdb->update($wpdb->prefix . 'bp_groups_downloads', $data, array('id' => $row->id));
                unlink($row->location);
            }
        }        
    }
    
}
if(isset($_GET['download_profile_type']))
{
    header('content-type: text/json');
    global $wpdb;
    
    $id = $_REQUEST['type_id'];
    
    $user_id = get_current_user_id();
    
    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "community_profile_types WHERE id=%d", $id);
    $row = $wpdb->get_row($query);
    
    if(!$row)
    {
        addMessage('Invalid Request!', 'error');
        return;
    }
    
    $filename = sanitize_file_name($row->title);
    
    $schema = base64_decode($row->schema);
    $schema_json = json_decode($schema);
    if($schema_json->Version)
    {
        $version = array();
        foreach(get_object_vars($schema_json->Version) as $k=>$v)      
        {
            $version[] = $v;
        }
        $filename .= '_v' . implode(".", $version);
    }
    echo $schema;
    exit;
}