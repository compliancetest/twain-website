<?php
/***
* Test Suite Class
*/

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
    
    var $excerpt = '';
    
    var $community_id = null;
    
    var $type = array();
    
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
        //Load Informations
        $this->community_id = $this->loadSingleValue('community_id');
        $this->name = $this->loadSingleValue('ts_name');
        $this->identifier = $this->loadSingleValue('ts_identifier');
        $this->issueDate = $this->loadSingleValue('ts_issue_date');
        $this->issuer = $this->loadSingleValue('ts_issuer');
        $this->status = $this->loadSingleValue('ts_status');
        $this->revisionDescription = $this->loadSingleValue('ts_revision_description');
        $this->description = $this->loadSingleValue('ts_description');
        
        $this->version_major = $this->loadSingleValue('ts_version_major');
        $this->version_minor = $this->loadSingleValue('ts_version_minor');
        $this->version_patch = $this->loadSingleValue('ts_version_patch');
        
        $versions = array();
        if($this->version_major)
            $versions[] = $this->version_major;
        if($this->version_minor)
            $versions[] = $this->version_minor;
        if($this->version_patch)
            $versions[] = $this->version_patch;
        
        
        $this->version = implode(".", $versions);
        
        $this->initiatingMessage = $this->loadSingleValue('init_message');
        $this->monthlySubscriptionPrice = $this->loadSingleValue('monthly_subscription_price');
        if(!$this->monthlySubscriptionPrice)
            $this->monthlySubscriptionPrice = 0;
        
        $this->loadConformanceLevel();
        $this->loadTestCases();
        $this->loadRelatedSuites();
        $this->loadRoles();
        $this->loadSpecDocuments();
        $this->loadTypes();        
        $this->loadProfileTypes();
        
        $p = get_post($this->id);
        
        $this->title = $p->post_title;
        
        $this->excerpt = $p->post_excerpt;
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
        $roles = array();
        
        if(!$roleNames)
        {
            return $roles;
        }else{        
            $arrName = explode('|', $roleNames);
            $arrDescs = explode('|', $roleDescs);
            
            foreach($arrName as $i=>$n)
            {
                if(!$arrName[$i])
                    continue;
                
                $roles[] = array('name' => $arrName[$i], 'desc' => $arrDescs[$i]);
            }
        }
        $this->roles = $roles;    
        return $roles;
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
        $query = "SELECT * FROM " . $wpdb->prefix . "community_profile_types WHERE id IN (" . implode(", ", $ids) . ")";
        $rows = $wpdb->get_results($query);
        
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
                'order_by'  => 'title',
                'order'     => 'ASC',                
                'meta_query' => array(
                                    'relation' => 'AND',
                                    array('key' => 'test_suite', 
                                          'value' => $this->id, 
                                          'compare' => '=')
                                )
        );
        
        if(!empty($level))
        {
            if(!is_array($level))
                $level = array($level);
            $args['meta_query'][] = array('key' => 'conformance_level', 'value' => $level, 'compare'=> 'IN');
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
    
    public function loadSingleValue($key)
    {
        return cp_get_post_meta($this->id, $key, true);
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
}