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
        
        //Add Default Privileges
        if(isset($_GET['fix_product_organisation_id'])){
            $results = $wpdb->get_results("SELECT p.ID, pm.organisation_id FROM wp_posts AS p LEFT JOIN wp_organisations_members AS pm ON pm.user_id=p.post_author WHERE p.post_type='product-service'");
            foreach($results as $r){
                update_post_meta($r->ID, 'product_organisation_id', $r->organisation_id);
            }
            die("Completed");
        }

        //Add Default Privileges
        if(isset($_GET['fix_privileges'])){
            $privileges = ct_get_privileges();
            $results = $wpdb->get_results("SELECT * FROM wp_organisations_members");
            foreach($results as $r){
                foreach($privileges as $p){
                    if($p->is_default)
                        $wpdb->insert($wpdb->prefix . "users_privileges", array("user_id" => $r->user_id, "organisation_id" => $r->organisation_id, "privilege_id" => $p->id), array("%d", "%d", "%d"));   
                }
            }
            die("Completed");
        }

        //Associate products with organisation
        if(isset($_GET['fix_test_plan_org_id'])){
            $results = $wpdb->get_results("SELECT p.id, os.`organisation_id` FROM wp_test_plans AS p LEFT JOIN wp_organisations_subscriptions AS os ON os.`user_id`=p.`creator_id`");
            foreach($results as $r){
                if($r->organisation_id)
                    $wpdb->update('wp_test_plans', array('organisation_id' => $r->organisation_id), array('id' => $r->id));
            }
            
            die('Completed!');
        }
        
        //Associate test plans with organisation
        if(isset($_GET['fix_test_plan_org_id'])){
            $results = $wpdb->get_results("SELECT p.id, os.`organisation_id` FROM wp_test_plans AS p LEFT JOIN wp_organisations_subscriptions AS os ON os.`user_id`=p.`creator_id`");
            foreach($results as $r){
                if($r->organisation_id)
                    $wpdb->update('wp_test_plans', array('organisation_id' => $r->organisation_id), array('id' => $r->id));
            }
            
            die('Completed!');
        }
        
        //Associate compliance claims with organisation
        if(isset($_GET['fix_claim_org_id'])){
            $results = $wpdb->get_results("SELECT p.id, os.`organisation_id` FROM wp_compliance_claims AS p LEFT JOIN wp_organisations_subscriptions AS os ON os.`user_id`=p.`creator_id`");
            foreach($results as $r){
                if($r->organisation_id)
                    $wpdb->update('wp_compliance_claims', array('organisation_id' => $r->organisation_id), array('id' => $r->id));
            }
            
            die('Completed!');
        }
        
        
        //Create Organisation Membership Records for the subscribers
        if(isset($_GET['fix_org_membership'])){
            $results = $wpdb->get_results("SELECT organisation_id, user_id FROM wp_users_subscriptions");
            foreach($results as $r){
                $mid = $wpdb->get_var("SELECT id FROM wp_organisations_members WHERE organisation_id=" . $r->organisation_id . " AND user_id=" . $r->user_id);
                if (!$mid) {
                    //Create New Membership Record
                    $wpdb->insert('wp_organisations_members', array('organisation_id' => $r->organisation_id, 'user_id' => $r->user_id,'is_admin' => 0, 'created_date' => date('Y-m-d H:i:s')));
                }
            }
            
            die('Completed!');
        }
        
        
        
        
        
        if(isset($_GET['fix_case_suite_link']))
        {
            
            //Delete Old Cases
            $results = $wpdb->get_results("SELECT COUNT(meta_id) AS c , meta_value, post_id FROM wp_postmeta WHERE meta_key='test_suite' GROUP BY post_id, meta_value HAVING c > 1");
            foreach($results as $r)
            {
                $wpdb->query("DELETE FROM wp_postmeta WHERE meta_value='" . $r->meta_value . "' AND post_id='" . $r->post_id . "' LIMIT 1 ");
            }
            
            die("Done!");                        
        }

        
        if(isset($_GET['fix_profile_instances']))
        {
            
            //Delete Old Cases
            $results = $wpdb->get_results("SELECT * FROM wp_community_profile_instances");
            foreach($results as $r)
            {
                $instanceObj = json_decode(base64_decode($r->content));    
                       $wpdb->update("wp_community_profile_instances", array('purpose' => $instanceObj->Profile->Purpose), array('id' => $r->id));                    
            }
            
            die("Done!");                        
        }
        
        if(isset($_GET['fix_claim']))
        {
            //Delete Old Cases
            $results = $wpdb->get_results("SELECT * FROM wp_compliance_claims");
            foreach($results as $r)
            {
                if(!$r->token)
                {
                    $token = createClaimToken();                    
                    $wpdb->update("wp_compliance_claims", array('token' => $token), array('id' => $r->id));                    
                }
                if(!$r->claim_id)
                       $wpdb->update("wp_compliance_claims", array('claim_id' => getClaimID($r->id, $r->suite_id)), array('id' => $r->id));                    
            }
            
            die("Done!");                        
        }
        if(isset($_GET['fix_users_extra']))
        {
            $results = $wpdb->get_results("SELECT * FROM wp_users");
            foreach($results as $r)
            {
                $wpdb->query("INSERT INTO {$wpdb->prefix}users_extra(`userID`)VALUES(" . $r->ID . ") ON DUPLICATE KEY UPDATE `userID`=" . $r->ID);
            }
            
            //Update Cards
            $results = $wpdb->get_results("SELECT count(*) AS c, user_id FROM wp_organisations_payment_methods GROUP BY user_id");
            foreach($results as $r)
            {
                $wpdb->update($wpdb->prefix . "users_extra", array('cards' => $r->c), array('userID' => $r->user_id));
            }
            
            //Update Subscriptions
            $results = $wpdb->get_results("SELECT count(*) AS c, user_id FROM wp_users_subscriptions GROUP BY user_id");
            foreach($results as $r)
            {
                $wpdb->update($wpdb->prefix . "users_extra", array('subscriptions' => $r->c), array('userID' => $r->user_id));
            }
            
            die("Done!");                        
        }
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

        if(isset($_GET['fix_hidden_cases']))
        {
            $query = "SELECT * FROM {$wpdb->prefix}test_cases WHERE family_mark = 0";
            $cases = $wpdb->get_results($query);
            $counter = 0;
            $processed = array();
            if( $cases ){
                foreach($cases as $i=>$s)
                {
                    $counter++;
                    array_push( $processed, $s->case_id );
                    update_post_meta($s->case_id, 'hide_case', 0);
                    $wpdb->update('wp_test_cases',
                        array(
                            'family_mark' => $s->case_id
                        ),
                        array(
                            'case_id' => $s->case_id
                        ),
                        array( '%d' ),
                        array( '%d' )
                    );
                }
            }
            echo '<pre>'.print_r( $processed, true ).'</pre>';
            echo "completed ".$counter;
            $processed = array();
            $counter = 0;
            $query = "SELECT * FROM wp_test_cases WHERE family_mark != case_id";
            $r = $wpdb->get_results($query);
            foreach( $r AS $case ){
                if( ! $wpdb->get_row( $wpdb->prepare( "SELECT * FROM wp_test_cases WHERE case_id = %d ", $case->family_mark ) ) ){
                    $counter++;
                    array_push( $processed, $case->case_id );
                    update_post_meta($case->case_id, 'hide_case', 0);
                } else{
                    if( $case->case_id == $wpdb->get_var( $wpdb->prepare( "SELECT case_id FROM wp_test_cases WHERE family_mark = %d ORDER BY case_id DESC LIMIT 1", $case->family_mark)) ){
                        $counter++;
                        update_post_meta($case->case_id, 'hide_case', 0 );
                    } else {
                        $counter++;
                        update_post_meta($case->case_id, 'hide_case', 1 );
                    }
                }
            }
            echo '<pre>'.print_r( $processed, true ).'</pre>';
            echo "completed ".$counter;
            exit;
        }

        if( isset( $_GET['update_test_suites'] ) ){
            $posts = $wpdb->get_results("SELECT * FROM wp_posts WHERE post_type = 'test-suite'");
            foreach( $posts AS $post ){
                update_post_meta( $post->ID, 'test_suite_plans', '1|2|3|4|5|6|7' );
            }
            $wpdb->query("UPDATE wp_organisations_subscriptions SET pricing_plan_id = 4 ");
            die;
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
        
        if(isset($_GET['fix_product_configuration'])){
            $esb = new ManageESB();
            
            //Getting Test Suites
            $args = array(
                'post_type' => 'product-service',         
                'posts_per_page' => -1
            );
            
            $all_posts = new WP_Query($args);
            $all = $all_posts->get_posts();
            
            foreach($all as $row)
            {
                $product_id  = get_post_meta($row->ID, 'product_id', true);
                $product_title = $row->post_title;
                $esb->saveProductInfo($row->ID, $product_id, $product_title);
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
                if (!$row->download_file) {
                    $data = array();
                    $data['download_file'] = file_get_contents($row->location);
                    $data = stripslashes_deep( $data );
                    
                    $wpdb->update($wpdb->prefix . 'bp_groups_downloads', $data, array('id' => $row->id));
                    unlink($row->location);
                }
            }
        }   
        
        if(isset($_GET['fix_transactions']))
        {
            $rows = $wpdb->get_results("SELECT id, esb_user_id FROM " . $wpdb->prefix . "users_subscriptions WHERE esb_user_id > 0");
            $esb = new ManageESB();
            
            foreach ($rows as $row) {            
                ManageESB::$esbdb->query("UPDATE MSH_CONVERSATION_METADATA SET CUSTOMER_ID='" . $row->id . "' WHERE CUSTOMER_ID='" . $row->esb_user_id . "'");                                
            }
            die("completed");
        }   
        
        if (isset($_GET['fix_profile_types_count']))
        {
            $rows = $wpdb->get_results("SELECT cpt.id, cpt.community_id, COUNT(cpt.id) as instance_count FROM " . $wpdb->prefix . "community_profile_types as cpt LEFT JOIN " . $wpdb->prefix . "community_profile_instances as cpi ON cpt.id=cpi.type_id AND cpt.community_id = cpi.community_id GROUP BY cpt.id");
            
            foreach ($rows as $row) {
                $wpdb->update($wpdb->prefix . 'community_profile_types', array('instances' => $row->instance_count), array('id' => $row->id, 'community_id' => $row->community_id));
            }
            die("completed");
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