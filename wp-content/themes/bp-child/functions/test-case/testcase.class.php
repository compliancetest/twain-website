<?php

class TestCase
{
    var $id = null;
    
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
        $this->testSuite = $this->loadSingleValue('test_suites');
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
        $this->testPattern = $this->loadSingleValue('message_pattern');
        
        $this->loadTestSteps();
        $this->loadTestData();
        $this->loadTestExecutionData();
        
    }
    
    public function loadTestSteps()
    {
        $stepActions = get_post_meta($this->id, 'step_action', true);
        $stepResults = get_post_meta($this->id, 'step_expected', true);
        
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
        $dataNames = get_post_meta($this->id, 'property_name_data', true);
        $dataValues = get_post_meta($this->id, 'property_value_data', true);
        
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
        $dataNames = get_post_meta($this->id, 'property_name_exec', true);
        $dataValues = get_post_meta($this->id, 'property_value_exec', true);
        
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
        return get_post_meta($this->id, $key, true);
    }
    
}