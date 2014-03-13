<?php

class TestCase
{
    var $id = null;
    
//    var $sequenceNumber = null;
    
    var $title = '';
    
    var $name = '';
    
    var $testSuite = null;
    
    var $conformanceLevel = '';
    
    var $testerRole = '';
    
    var $harnessRole = '';
    
    var $Initiator = '';
    
    var $initiationgMessage = '';
    
    var $testCaseID = '';
    
    var $version = '';
    
    var $version_major = '';
    var $version_minor = '';
    var $version_patch = '';
    
    var $publishedDate = '';
    
    var $testIntentDescription = '';
    
    var $testSteps = array();
    
    var $testData = array();
    
    var $messageTemplates = array();
    
    var $profileInstances = array();
    
    var $testEndpointURL = '';
    
    var $protocolBinding = '';
    
    var $testExecutionData = array();
    
    var $outcomeType = '';
    
    var $bulk = false;
    
    var $testPattern = '';
    
    var $status = '';
    
    var $scenario = '';
    
    var $familyMark = null;
    
    
    public function __construct($id = null)
    {        
        if($id !== null)   
            $this->id = $id;        
        
        $this->conformanceLevel = array('code' => TEST_SUITE_DEFAULT_CONFORMANCE_LEVEL_CODE, 'desc' => TEST_SUITE_DEFAULT_CONFORMANCE_LEVEL_DESCRIPTION);
    }
    
    public function load($id = null)
    {
        global $wpdb;
        
        if($id !== null)   
            $this->id = $id;
        
        //Set Default Values
        $this->status = 'Draft';
        $this->version_major = 0;
        $this->version_minor = 0;
        $this->version_patch = 0; 
        if(!$this->id)   
            return;
        
        //Check ID validation
        $query = $wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE ID=%d AND post_status='publish' AND post_type='test-case'", $this->id);
        $tID = $wpdb->get_var($query);
        
        if(!$tID)
        {
            $this->id = null;
            return;
        }
        
            
        $this->name = get_the_title($this->id);
//        $this->sequenceNumber = $this->loadSingleValue('sequence_number');
        $this->testSuite = $this->loadValues('test_suite');
        
        $this->testerRole = $this->loadSingleValue('choose_tester_role');
        $this->harnessRole = $this->loadSingleValue('choose_harness_role');
        $this->Initiator = $this->loadSingleValue('choose_initiator');
        $this->initiationgMessage = $this->loadSingleValue('choose_init_messages');
        $this->testCaseID = $this->loadSingleValue('test_case_id');
        
        
        $this->version_major = $this->loadSingleValue('version_major');
        $this->version_minor = $this->loadSingleValue('version_minor');
        $this->version_patch = $this->loadSingleValue('version_patch');
        
        if(!$this->version_major)
            $this->version_major = 0;
        if(!$this->version_minor)
            $this->version_minor = 0;
        
        
        $versions = array();
        
        $versions[] = $this->version_major;    
        $versions[] = $this->version_minor;
        
        if($this->version_patch)
            $versions[] = $this->version_patch;
        
        $this->version = implode(".", $versions);
        
        $this->publishedDate = $this->loadSingleValue('published');
        $this->testIntentDescription = $this->loadSingleValue('test_intent_description');
        $this->testEndpointURL = $this->loadSingleValue('test_url');
        $this->protocolBinding = $this->loadSingleValue('protocol_binding2');
        $this->outcomeType = $this->loadSingleValue('outcome_type');
        $this->bulk = $this->loadSingleValue('bulk');
        $this->testPattern = $this->loadSingleValue('message_count');
        $this->status = $this->loadSingleValue('test_case_status');
        
        $this->loadConformanceLevel();
        
        $this->loadTestSteps();
        $this->loadTestData();
        $this->loadTestExecutionData();
        $this->loadMessageTemplates();
        $this->loadProfileInstances();
        $this->loadScenario();
        
        $this->loadfamilyMark();
        
        $this->title = get_the_title($this->id);
        
    }
    
    public function loadfamilyMark()
    {
        global $wpdb;
        
        $query = $wpdb->prepare("SELECT family_mark FROM {$wpdb->prefix}test_cases WHERE case_id=%d", $this->id);
        $this->familyMark = $wpdb->get_var($query);
        
        return $this->familyMark;
    }
    
    public function loadConformanceLevel()
    {
        if(!$this->testSuite)
        {
            $this->testSuite = $this->loadValues('test_suite');
        }
        $this->conformanceLevel = array();
        
        if(is_array($this->testSuite))
        {
            foreach($this->testSuite as $sid)    
            {
                $this->conformanceLevel[$sid] = $this->loadValues('conformance_level_' . $sid);
            }
        }        
        return;
        
    }
    
    public function loadScenario()
    {
        if(!$this->testSuite)
        {
            $this->testSuite = $this->loadValues('test_suite');
        }
        $this->scenario = array();
        
        if(is_array($this->testSuite))
        {
            foreach($this->testSuite as $sid)    
            {
                $this->scenario[$sid] = $this->loadSingleValue('scenario_' . $sid);
            }
        }       
        
        return;
        
    }
    
    
    
    public function loadTestSteps()
    {
        $stepActions = cp_get_post_meta($this->id, 'step_action', true);
        $stepResults = cp_get_post_meta($this->id, 'step_expected', true);
        
        $result = array();
        if($stepActions)
        {
            foreach($stepActions as $i=>$action)
            {
                if(!$action)
                    continue;
                $result[] = array('action' => $action, 'result' => $stepResults[$i]);
            }
        }
        $this->testSteps = $result;
        
        return $result;
    }
    
    public function loadTestData()
    {
        $dataNames = cp_get_post_meta($this->id, 'property_name_data', true);
        $dataValues = cp_get_post_meta($this->id, 'property_value_data', true);
        
        $result = array();
        if($dataNames)
        {
            foreach($dataNames as $i=>$name)
            {
                if(!$name)
                    continue;
                $result[] = array('name' => $name, 'value' => $dataValues[$i]);
            }
        }
        $this->testData = $result;
        
        return $result;
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
    
    public function loadProfileInstances()
    {
        $instances = cp_get_post_meta($this->id, 'profile_instances', true);
        
        $this->profileInstances = cp_explode($instances);    
        return $roles;
    }
    
    public function getProfileInstanceRows($type = 'harness')
    {
        global $wpdb;
        
        if(!$this->profileInstances)
            return array();
            
        $ids = $wpdb->escape($this->profileInstances);
        
        $query = "SELECT pi.*, pt.title AS profile_type_title, pt.schema FROM " . $wpdb->prefix . "community_profile_instances AS pi LEFT JOIN " . $wpdb->prefix . "community_profile_types AS pt ON pt.id=pi.type_id WHERE pi.id IN (" . implode(", ", $ids) . ")";        
        $rows = $wpdb->get_results($query);
        
        return $rows;
        
    }
    
    public function loadTestExecutionData()
    {
        $dataNames = cp_get_post_meta($this->id, 'property_name_exec', true);
        $dataValues = cp_get_post_meta($this->id, 'property_value_exec', true);
        
        $result = array();
        if($dataNames)
        {
            foreach($dataNames as $i=>$name)
            {
                if(!$name)
                    continue;
                $result[] = array('name' => $name, 'value' => $dataValues[$i]);
            }
        }
        
        $this->testExecutionData = $result;
        
        return $result;
    }
    
    public function loadSingleValue($key)
    {
        return cp_get_post_meta($this->id, $key, true);
    }
    
    public function loadValues($key)
    {
        return cp_get_post_meta($this->id, $key);
    }
    
    
    public function getAvailableTestSuites($current_id = null)
    {
                
        $groups = getUserAdminGroups(get_current_user_id());
        
        if(!$groups)
            return array();
        
        $args = array(
            'post_type' => 'test-suite', 
            'posts_per_page' => -1,
            'orderby' => 'title', 
            'order' => 'ASC',
            'post__not_in' => array($this->id),
            'meta_query' => array(
                'relation' => 'or'
                
            )
        );
        
        if($current_id)
            $current_group = get_post_meta($current_id, "community_id", true);
        else
            $current_group = null;
        
        if(!$current_group)
        {
            foreach($groups as $group)
            {
                $args['meta_query'][] = array(
                        'key' => 'community_id',
                        'value' => $group->id,
                        'compare' => '='
                );
            }
        }else{
            $args['meta_query'][] = array(
                    'key' => 'community_id',
                    'value' => $current_group,
                    'compare' => '='
            );
        }
        
        $testsuites = get_posts( $args );
        
        return $testsuites;
    }
    
    public function getAvailableRoles()
    {
        $roles = array();
        
        foreach($this->testSuite as $sid)
        {
            $roleNames = cp_get_post_meta($sid, 'role_names', true);
            $roleDescs = cp_get_post_meta($sid, 'role_descs', true);
            
            if(!$roleNames)
            {
                continue;
            }else{        
                $arrName = explode('|', $roleNames);
                $arrDescs = explode('|', $roleDescs);
                
                foreach($arrName as $i=>$n)
                {
                    if(!$arrName[$i])
                        continue;
                    
                    if(!in_array(array('name' => $arrName[$i], 'desc' => $arrDescs[$i]), $roles))
                    {
                        $roles[] = array('name' => $arrName[$i], 'desc' => $arrDescs[$i]);    
                    }
                    
                }
            }    
        }
        
        $this->availableRoles = $roles;
        
        return $roles;
    }
    
    public function getAvailableInitMessages()
    {
        $messages = array();
        
        foreach($this->testSuite as $sid)
        {
            $msgs = explode(',',  cp_get_post_meta($sid, 'init_message', true));
            foreach($msgs as $m)
            {
                if(!trim($m) || in_array(trim($m), $messages))
                    continue;
                    
                $messages[] = trim($m);
            }
        }
        
        $this->availableInitMessages = $messages;
        
        return $messages;
    }
    
    public function getAvailableProfileInstances()
    {
        global $wpdb;
                
        $instances = array();
        
        foreach($this->testSuite as $sid)
        {
            $types = cp_get_post_meta($sid, 'ts_profile_types', true);
            $types = cp_explode($types);
            
            $ids = $wpdb->escape($types);    
            
            $query = "SELECT pi.*, pt.title AS profile_type_title, pt.schema FROM " . $wpdb->prefix . "community_profile_instances AS pi LEFT JOIN " . $wpdb->prefix . "community_profile_types AS pt ON pt.id=pi.type_id WHERE pi.type='harness' AND pt.id IN (" . implode(", ", $ids) . ")";                    
            $rows = $wpdb->get_results($query);
            
            foreach($rows as $row)
            {
                $instances[$row->id] = $row;
            }
            
        }
        
        return $instances;
    }
    
    public function getAvailableMessageTemplates()
    {
        global $wpdb;
                
        $templates = array();
        
        foreach($this->testSuite as $sid)
        {
            $dataNames = cp_get_post_meta($sid, 'message_template_name', true);
            $dataValues = cp_get_post_meta($sid, 'message_template_url', true);
            
            
            if($dataNames){
                foreach($dataNames as $i=>$name)
                {
                    if(!$name)
                        continue;
                    $templates[] = array('name' => $name, 'url' => $dataValues[$i]);
                }
            }
            
        }
        
        $templates = array_unique($templates, SORT_REGULAR);
        
        return $templates;
    }
    
    public function getScenario($suite_id)
    {
        global $wpdb;
        
        $sid = isset($this->scenario[$suite_id]) ? $this->scenario[$suite_id] : null;
        if(!$sid)
            return '';
        
        $query = $wpdb->prepare("SELECT code, description FROM {$wpdb->prefix}test_suites_scenarios WHERE id=%d", $sid);
        $row = $wpdb->get_row($query);
        
        return $row;
    }
    
}