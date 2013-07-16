<?php

class TestCase
{
    var $id = null;
    
    var $sequenceNumber = null;
    
    var $name = '';
    
    var $testSuite = null;
    
    var $conformanceLevel = '';
    
    var $testerRole = '';
    
    var $harnessRole = '';
    
    var $Initiator = '';
    
    var $initiationgMessage = '';
    
    var $testCaseID = '';
    
    var $version = '';
    
    var $publishedDate = '';
    
    var $testIntentDescription = '';
    
    var $testSteps = array();
    
    var $testData = array();
    
    var $testEndpointURL = '';
    
    var $protocolBinding = '';
    
    var $testExecutionData = array();
    
    var $outcomeType = '';
    
    var $bulk = false;
    
    var $testPattern = '';
    
    public function __construct($id = null)
    {        
        if($id !== null)   
            $this->id = $id;        
    }
    
    public function load($id = null)
    {
        if($id !== null)   
            $this->id = $id;
        
        if(!$this->id)
            return;
            
        $this->name = get_the_title($this->id);
        $this->sequenceNumber = $this->loadSingleValue('sequence_number');
        $this->testSuite = $this->loadSingleValue('test_suite');
        $this->conformanceLevel = $this->loadSingleValue('conformance_level');
        $this->testerRole = $this->loadSingleValue('choose_tester_role');
        $this->harnessRole = $this->loadSingleValue('choose_harness_role');
        $this->Initiator = $this->loadSingleValue('choose_initiator');
        $this->initiationgMessage = $this->loadSingleValue('choose_init_messages');
        $this->testCaseID = $this->loadSingleValue('test_case_id');
        $this->version = $this->loadSingleValue('version');
        $this->publishedDate = $this->loadSingleValue('published');
        $this->testIntentDescription = $this->loadSingleValue('test_intent_description');
        $this->testEndpointURL = $this->loadSingleValue('test_url');
        $this->protocolBinding = $this->loadSingleValue('protocol_binding2');
        $this->outcomeType = $this->loadSingleValue('outcome_type');
        $this->bulk = $this->loadSingleValue('bulk');
        $this->testPattern = $this->loadSingleValue('message_count');
        
        $this->loadTestSteps();
        $this->loadTestData();
        $this->loadTestExecutionData();
        
    }
    
    public function loadTestSteps()
    {
        $stepActions = cp_get_post_meta($this->id, 'step_action', true);
        $stepResults = cp_get_post_meta($this->id, 'step_expected', true);
        
        $result = array();
        foreach($stepActions as $i=>$action)
        {
            if(!$action)
                continue;
            $result[] = array('action' => $action, 'result' => $stepResults[$i]);
        }
        
        $this->testSteps = $result;
        
        return $result;
    }
    
    public function loadTestData()
    {
        $dataNames = cp_get_post_meta($this->id, 'property_name_data', true);
        $dataValues = cp_get_post_meta($this->id, 'property_value_data', true);
        
        $result = array();
        foreach($dataNames as $i=>$name)
        {
            if(!$name)
                continue;
            $result[] = array('name' => $name, 'value' => $dataValues[$i]);
        }
        
        $this->testData = $result;
        
        return $result;
    }
    
    public function loadTestExecutionData()
    {
        $dataNames = cp_get_post_meta($this->id, 'property_name_exec', true);
        $dataValues = cp_get_post_meta($this->id, 'property_value_exec', true);
        
        $result = array();
        foreach($dataNames as $i=>$name)
        {
            if(!$name)
                continue;
            $result[] = array('name' => $name, 'value' => $dataValues[$i]);
        }
        
        $this->testExecutionData = $result;
        
        return $result;
    }
    
    public function loadSingleValue($key)
    {
        return cp_get_post_meta($this->id, $key, true);
    }
    
    
    public function getAvailableTestSuites()
    {
                
        $groups = getUserAdminGroups(get_current_user_id());
        if(!$groups)
            return array();
        
        $args = array(
            'post_type' => 'test-suite', 
            'posts_per_page' => -1,
            'post__not_in' => array($this->id),
            'meta_query' => array(
                'relation' => 'or'
                
            )
        );
        
        foreach($groups as $group)
        {
            $args['meta_quer'][] = array(
                    'key' => 'community_id',
                    'value' => $group->id,
                    'compare' => '='
            );
        }
        
        
        $testsuites = get_posts( $args );
        
        return $testsuites;
    }
}