<?php
/***
* Test Suite Class
*/

if(!defined('TEST_SUITE_DEFAULT_CONFORMANCE_LEVEL_CODE'))
    define('TEST_SUITE_DEFAULT_CONFORMANCE_LEVEL_CODE', 'Default');
if(!defined('TEST_SUITE_DEFAULT_CONFORMANCE_LEVEL_DESCRIPTION'))
    define('TEST_SUITE_DEFAULT_CONFORMANCE_LEVEL_DESCRIPTION', 'All test cases created via this test suite are automatically associated with this conformance level. This association cannot be deleted.');

if(!defined('TEST_SUITE_DEFAULT_SCENARIO_CODE'))
    define('TEST_SUITE_DEFAULT_SCENARIO_CODE', 'Default');
if(!defined('TEST_SUITE_DEFAULT_SCENARIO_DESCRIPTION'))
    define('TEST_SUITE_DEFAULT_SCENARIO_DESCRIPTION', 'All test cases created via this test suite are initially associated with this scenario. In general, test cases will be associated with test suite specific scenarios, so it will usually be the case that no test cases are associated with this scenario.');



class TestSuite
{
    var $id = null;
    //Test Suite Information
    var $title = '';
    
    var $name = '';
    var $identifier = '';
    var $issueDate = '';
    var $issuer = '';
    var $status = 'Active';
    var $revisionDescription = '';
    var $version = '';
    
    var $version_major = '';
    var $version_minor = 0;
    var $version_patch = 0;
    
    var $description = '';
    
    var $initiatingMessage = '';
    
    var $conformanceLevel = array();
    
    var $profileTypes = array();
    
    var $testCases = array();
    
    var $relatedSuites = array();
    
    var $roles = array();
    
    var $specDocuments = array();
    
    var $monthlySubscriptionPrice = 0;
    
    var $monthlySubscriptionPriceValue = 0;
    
    var $excerpt = '';
    
    var $community_id = null;
    
    var $type = array();
    
    var $isRevision = false;
    
    var $scenarios = array();
    
    var $signupPrice = 0;
    
    var $signupPriceValue = 0;
    
    //This will be same for all versions
    var $familyMark = null;
    
    var $messageTemplates = array();
    
    public function __construct($id = null)
    {        
        if($id !== null)   
            $this->id = $id;        
        
        //Init Values
        $this->version_major = 0;
        $this->version_minor = 0;
        $this->version_patch = 0;
        $this->status = 'Draft';
        $this->isRevision = false;
    }
    
    public function load($id = null)
    {
        if($id !== null)   
            $this->id = $id;
        
        if(!$this->id)
            return;
            
        //Load Informations
        $this->community_id = $this->loadSingleValue('community_id');
        if(!$this->community_id)
        {
            $this->id = null;
            return;
        }
        $this->name = $this->loadSingleValue('ts_name');
        $this->identifier = $this->loadSingleValue('ts_identifier');
        $this->issueDate = $this->loadSingleValue('ts_issue_date');
        $this->issuer = $this->loadSingleValue('ts_issuer');
        
        $this->status = $this->loadSingleValue('ts_status');
        if(!$this->status)
            $this->status = 'Draft'; //Draft is default status
        
        $this->revisionDescription = $this->loadSingleValue('ts_revision_description');
        $this->description = $this->loadSingleValue('ts_description', false);
        
        $this->version_major = $this->loadSingleValue('ts_version_major');
        $this->version_minor = $this->loadSingleValue('ts_version_minor');
        $this->version_patch = $this->loadSingleValue('ts_version_patch');
        $this->signupPrice = $this->loadSingleValue('signup_price');
        $this->signupPriceValue = $this->getPriceFromXeroCode($this->signupPrice);
        
        
        $this->version_major = !$this->version_major ? 0 : $this->version_major;
        $this->version_minor = !$this->version_minor ? 0 : $this->version_minor;
        $this->version_patch = !$this->version_patch ? 0 : $this->version_patch;
        
        $versions = array();
        $versions[] = $this->version_major;
        $versions[] = $this->version_minor;
        if($this->version_patch)
            $versions[] = $this->version_patch;
        
        
        $this->version = implode(".", $versions);
        
        $this->initiatingMessage = $this->loadSingleValue('init_message');
        $this->monthlySubscriptionPrice = $this->loadSingleValue('monthly_subscription_price');
        if(!$this->monthlySubscriptionPrice)
            $this->monthlySubscriptionPrice = 0;
        $this->monthlySubscriptionPriceValue = $this->getPriceFromXeroCode($this->monthlySubscriptionPrice);
        
        
        $this->loadConformanceLevel();
        $this->loadTestCases();
        $this->loadRelatedSuites();
        $this->loadRoles();
        $this->loadSpecDocuments();
        $this->loadTypes();        
        $this->loadProfileTypes();
        $this->loadScenarios();
        $this->loadMessageTemplates();
        
        $this->loadfamilyMark();
        
        $p = get_post($this->id);
        
        $this->title = $p->post_title;
        
        $this->excerpt = $p->post_excerpt;
        
        $this->isRevision = intval($this->loadSingleValue('hide_suite')) == 1 ? true : false;
    }
    
    public function loadfamilyMark()
    {
        global $wpdb;
        
        $query = $wpdb->prepare("SELECT family_mark FROM {$wpdb->prefix}test_suites WHERE suite_id=%d", $this->id);
        $this->familyMark = $wpdb->get_var($query);
        
        return $this->familyMark;
    }
    
    public function loadMessageTemplates()
    {
        $dataNames = cp_get_post_meta($this->id, 'message_template_name', true);
        $dataValues = cp_get_post_meta($this->id, 'message_template_url', true);
        
        $result = array();
        if($dataNames){
            foreach($dataNames as $i=>$name)
            {
                if(!$name)
                    continue;
                $result[] = array('name' => $name, 'url' => $dataValues[$i]);
            }
        }
        $this->messageTemplates = $result;
        
        return $result;
    }
    
    public function loadTypes()
    {
        
        $suiteTypes = wp_get_post_terms($this->id, 'test_suite_type', array('hide_empty' => false));
        $result = array();
        foreach($suiteTypes as $row)
        {
            $result[$row->term_id] = $row->name;
        }
        
        $this->type = $result;
        
        return $result;
    }
    
    
    public function getSuiteID()
    {        
        $this->identifier = $this->loadSingleValue('ts_identifier');
        
        $this->version_major = $this->loadSingleValue('ts_version_major');
        $this->version_minor = $this->loadSingleValue('ts_version_minor');
        $this->version_patch = $this->loadSingleValue('ts_version_patch');
        
        $this->version_major = !$this->version_major ? 0 : $this->version_major;
        $this->version_minor = !$this->version_minor ? 0 : $this->version_minor;
        $this->version_patch = !$this->version_patch ? 0 : $this->version_patch;
        
        $versions = array();
        $versions[] = $this->version_major;
        $versions[] = $this->version_minor;
        if($this->version_patch)
            $versions[] = $this->version_patch;
        
        
        $this->version = implode(".", $versions);
        
        $this->suiteID = $this->identifier . "_V" . $this->version;
        
        return $this->suiteID;
    }
    
    public function loadSpecDocuments()
    {
        global $wpdb;
        
        $query = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "ts_options_documents WHERE ts_id=%d ORDER BY id", $this->id);    
        $this->specDocuments = $wpdb->get_results($query);
    }
    
    public function loadRoles()
    {
        $roleNames = cp_get_post_meta($this->id, 'role_names', true);
        $roleDescs = cp_get_post_meta($this->id, 'role_descs', true);
        $roleProfileTypes = cp_get_post_meta($this->id, 'role_profile_types', true);
        $roles = array();
        
        if(!$roleNames)
        {
            return $roles;
        }else{        
            $arrName = explode('|', $roleNames);
            $arrDescs = explode('|', $roleDescs);
            $arrProfileTypes = explode('|', $roleProfileTypes);
            
            foreach($arrName as $i=>$n)
            {
                if(!$arrName[$i])
                    continue;
                
                $roles[] = array('name' => $arrName[$i], 'desc' => $arrDescs[$i], 'profileTypes' => $arrProfileTypes[$i] );
            }
        }
        $this->roles = $roles;    
        return $roles;
    }

    /**
     * @param $suitesIdsArray
     * @return array
     */
    public function loadProfileTypesToRoles( $suitesIdsArray ){
        $testSuitesRolesProfilesTypes = array();
        $isEmpty = true;
        foreach ( $suitesIdsArray as $test_suite ){
            $suiteObj = new TestSuite( $test_suite );
            $roles = $suiteObj->loadRoles();
            foreach( $roles AS $role ){
                if( ! isset( $testSuitesRolesProfilesTypes[$role['name']] ) ){
                    $testSuitesRolesProfilesTypes[$role['name'] ] = array();
                }
                if( ! empty( $role['profileTypes'] ) ){
                    $profileTypes = explode( ',', $role['profileTypes'] );
                    foreach( $profileTypes AS $profileType ){
                        if( ! in_array( $profileType, $testSuitesRolesProfilesTypes[$role['name']] ) ){
                            $isEmpty = false;
                            array_push( $testSuitesRolesProfilesTypes[$role['name']], $profileType );
                        }
                    }
                }

            }
        }
        if( $isEmpty ){
            return array();
        }
        return $testSuitesRolesProfilesTypes;
    }

    public function loadProfileTypes()
    {
        $types = cp_get_post_meta($this->id, 'ts_profile_types', true);
        
        $this->profileTypes = cp_explode($types);    
        return $roles;
    }
    
    public function getProfileTypesRows()
    {
        global $wpdb;
        
        $ids = $wpdb->escape($this->profileTypes);
        if (count($ids) > 0) {
            $query = "SELECT * FROM " . $wpdb->prefix . "community_profile_types WHERE id IN (" . implode(", ", $ids) . ")";
            $rows = $wpdb->get_results($query);
        } else {
            $rows = array();
        }
        
        return $rows;
        
    }
    
    public function getProfileInstancesRows()
    {
        global $wpdb;
        
        if(!$this->profileTypes)
            return array();
            
        $ids = $wpdb->escape($this->profileTypes);
        
        $query = "SELECT pi.*, pt.title AS profile_type_title, pt.schema FROM " . $wpdb->prefix . "community_profile_instances AS pi LEFT JOIN " . $wpdb->prefix . "community_profile_types AS pt ON pt.id=pi.type_id WHERE pi.type='harness' AND pt.id IN (" . implode(", ", $ids) . ")";        
        $rows = $wpdb->get_results($query);
        
        return $rows;
        
    }
    
    public function loadRelatedSuites()
    {
        $suiteIDs = cp_get_post_meta($this->id, 'ts', true);
        $suiteDescs = cp_get_post_meta($this->id, 'ts_desc', true);
        $result = array();
        if(is_array($suiteIDs))
        {
            foreach($suiteIDs as $i=>$sid)
            {
                $result[] = array('id' => $sid, 'desc' => $suiteDescs[$i]);
            }
        }
        
        $this->relatedSuites = $result;
        
        return $result;
    }
    
    public function loadTestCases($level = array(), $role = array())
    {        
        $args = array(
                'post_type' => 'test-case',         
                'posts_per_page' => -1,
                'orderby'  => 'title',
                'order'     => 'ASC',                
                'meta_query' => array(
                                    'relation' => 'AND',
                                    array('key' => 'test_suite', 
                                          'value' => $this->id, 
                                          'compare' => '=')
                                )
        );
        
        if(!$this->community_id)
            $this->community_id = $this->loadSingleValue('community_id');
        
        if(!groups_is_user_admin(get_current_user_id(), $this->community_id)){
            $args['meta_query'][] = array(
                                        'key' => 'hide_case',
                                        'value' => 0,
                                        'compare' => '='
                                    ); 
            $args['meta_query'][] = array(
                                        'key' => 'conformance_level_' . $this->id,
                                        'value' => TEST_SUITE_DEFAULT_CONFORMANCE_LEVEL_CODE,
                                        'compare' => '!='
                                    );  
              
        }
        
        if(!empty($level))
        {
            if(!is_array($level))
                $level = array($level);
            $args['meta_query'][] = array('key' => 'conformance_level_' . $this->id, 'value' => $level, 'compare'=> 'IN');
        }
        
        if(!empty($role))
        {
            if(!is_array($role))
                $role = array($role);
            $args['meta_query'][] = array('key' => 'choose_tester_role', 'value' => $role, 'compare'=> 'IN');
        }
        
        $case_query = new WP_Query($args);
        $this->testCases = $case_query->get_posts();
        
        return $this->testCases;
    }
    
    public function loadHarnessInitiatedTestCases()
    {        
        $args = array(
                'post_type' => 'test-case',         
                'posts_per_page' => -1,
                'orderby'  => 'title',
                'order'     => 'ASC',                
                'meta_query' => array(
                                    'relation' => 'AND',
                                    array('key' => 'test_suite', 
                                          'value' => $this->id, 
                                          'compare' => '=')
                                )
        );
        
        
        $args['meta_query'][] = array('key' => 'test_case_status', 'value' => 'Active', 'compare' => '=');
        $args['meta_query'][] = array('key' => 'choose_initiator', 'value' => 'harness', 'compare' => '=');
        
        
        if(!$this->community_id)
            $this->community_id = $this->loadSingleValue('community_id');
        
        if(!groups_is_user_admin(get_current_user_id(), $this->community_id)){
            $args['meta_query'][] = array(
                                        'key' => 'hide_case',
                                        'value' => 0,
                                        'compare' => '='
                                    ); 
            $args['meta_query'][] = array(
                                        'key' => 'conformance_level_' . $this->id,
                                        'value' => TEST_SUITE_DEFAULT_CONFORMANCE_LEVEL_CODE,
                                        'compare' => '!='
                                    );  
              
        }
        
        $case_query = new WP_Query($args);
        $this->testCases = $case_query->get_posts();
        
        
        return $this->testCases;
    }
    
    public function loadTesterInitiatedTestCases()
    {        
        $args = array(
                'post_type' => 'test-case',         
                'posts_per_page' => -1,
                'orderby'  => 'title',
                'order'     => 'ASC',                
                'meta_query' => array(
                                    'relation' => 'AND',
                                    array('key' => 'test_suite', 
                                          'value' => $this->id, 
                                          'compare' => '=')
                                )
        );
        
        
        $args['meta_query'][] = array('key' => 'test_case_status', 'value' => 'Active', 'compare' => '=');
        $args['meta_query'][] = array('key' => 'choose_initiator', 'value' => 'tester', 'compare' => '=');
        
        
        if(!$this->community_id)
            $this->community_id = $this->loadSingleValue('community_id');
        
        if(!groups_is_user_admin(get_current_user_id(), $this->community_id)){
            $args['meta_query'][] = array(
                                        'key' => 'hide_case',
                                        'value' => 0,
                                        'compare' => '='
                                    ); 
            $args['meta_query'][] = array(
                                        'key' => 'conformance_level_' . $this->id,
                                        'value' => TEST_SUITE_DEFAULT_CONFORMANCE_LEVEL_CODE,
                                        'compare' => '!='
                                    );  
              
        }
        
        $case_query = new WP_Query($args);
        $this->testCases = $case_query->get_posts();
        
        
        return $this->testCases;
    }
    
    public function loadConformanceLevel()
    {
        $lvl_code = cp_get_post_meta($this->id, 'lvl_code', true);
        $lvl_desc = cp_get_post_meta($this->id, 'lvl_desc', true);
        
        $result = array();
        
        if($lvl_code)
        {
            foreach($lvl_code as $i=>$code)
            {
                $result[] = array('code' => $code, 'desc' => $lvl_desc[$i]);
            }    
        }
        
        
        $this->conformanceLevel = $result;
        
        return $result;
    }
    
    public function loadSingleValue($key, $use_bbcode = true)
    {
        return cp_get_post_meta($this->id, $key, true, $use_bbcode);
    }
    
    /**
    * Getting the Test Suites that are belonged to same community
    * 
    */
    public function getBrotherSuites($community_id = null)
    {
        //Getting Community ID
        if($community_id !== null)
            $this->community_id = $community_id;                        
        
        if(!$this->community_id)
                return array();        
                
        $args = array(
            'post_type' => 'test-suite', 
            'posts_per_page' => -1,
            'post__not_in' => array($this->id),
            'meta_query' => array(
                array(
                    'key' => 'community_id',
                    'value' => $this->community_id,
                    'compare' => '='
                )
            )
        );
        
        $testsuites = get_posts( $args );
        
        return $testsuites;
    }
    
    
    public function getAllTestSuiteTypes()
    {
        $types = get_terms('test_suite_type', array('hide_empty' => false));
        
        return $types;
    }
    
    public function loadScenarios()
    {
        global $wpdb;
        
        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "test_suites_scenarios WHERE suite_id=%d ORDER BY sequence", $this->id);        
        $this->scenarios = $wpdb->get_results($query, ARRAY_A);
        
        return $this->scenarios;
    }
    
    public function getAvailableTemplates()
    {
        global $CPRest;
        
        if(!$this->identifier)
            return array();
        
        $availableTemplates = $CPRest->getTemplateList($this->identifier, $this->version_major);
        
        $this->availableTemplates = $availableTemplates;
        
        return $availableTemplates;
        
    }
    
    
    public function getPriceFromXeroCode($code)
    {
        global $wpdb;
 
        if($code == '0')
            return 0;
            
        if($code == '-1')       
            return -1;
        
        $query = $wpdb->prepare("SELECT unit_price FROM {$wpdb->prefix}xeroitems WHERE code=%s", $code);        
        $price = $wpdb->get_var($query);
        
        return $price;
    }
}