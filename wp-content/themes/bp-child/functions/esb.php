<?php
/**
* Manage Backend Data
*/

class ManageESB
{
    var $esb_db_host = 'esb.test.compliancetest.net';
    var $esb_db_username = 'root';
    var $esb_db_password = 'H1cubase666';
    var $esb_db_database = 'MSH_METADATA';
    
    var $table_metadata = 'MSH_METADATA';
    var $table_metadata_payload = 'MSH_METADATA_PAYLOAD';
    
    var $db = null;
    
    public static $esbdb = null;
    
    public function __construct()
    {
        if(ManageESB::$esbdb == null)
            $this->loadDatabase();
        
    }
    
    public function  loadDatabase()
    {
        $db = new wpdb($this->esb_db_username, $this->esb_db_password, $this->esb_db_database, $this->esb_db_host);
        ManageESB::$esbdb = $db;
        
        return $db;
    }
    
    public function getCaseStatus($customer_id, $suite_id = null, $product_id = null, $case_id = null)
    {
        
        $query = ManageESB::$esbdb->prepare("SELECT TEST_SUITE_ID, PRODUCT_ID, TEST_CASE_ID,`TEST_OUTCOME` FROM " . $this->table_metadata . " WHERE CUSTOMER_ID=%d", $customer_id);
        
        if($suite_id != null)
            $query .= ManageESB::$esbdb->prepare(" AND TEST_SUITE_ID=%d", $suite_id);
        
        if($product_id != null)
            $query .= ManageESB::$esbdb->prepare(" AND PRODUCT_ID=%d", $product_id);
        
        if($case_id != null)
            $query .= ManageESB::$esbdb->prepare(" AND TEST_CASE_ID=%d", $case_id);
        
        $rows = ManageESB::$esbdb->get_results($query);
        
        if(!$rows)
            return array();
    
        $result = array();
        
        foreach($rows as $row)
        {
            if(!isset($result[$row->TEST_SUITE_ID]))
                $result[$row->TEST_SUITE_ID] = array();
            
            if(!isset($result[$row->TEST_SUITE_ID][$row->PRODUCT_ID]))
                $result[$row->TEST_SUITE_ID][$row->PRODUCT_ID] = array();
            
            if($row->TEST_OUTCOME == 'SUCCESS')
                $result[$row->TEST_SUITE_ID][$row->PRODUCT_ID][$row->TEST_CASE_ID] = 'pass';
            else
                $result[$row->TEST_SUITE_ID][$row->PRODUCT_ID][$row->TEST_CASE_ID] = 'fail';
        }
        
        return $result;
    }
    
    public function  getUserTransactionLog($user_id = null, $product_id = null, $suite_id = null, $case_id = null, $service = null, $action = null, $partyid = null, $date = null, $id = null)
    {
        global $wpdb;
        
        if($user_id == null)
            $user_id = get_current_user_id();
        
        if(!$user_id)
            return array();
        
        //Getting User Customer IDs
        $query = $wpdb->prepare("SELECT esb_user_id FROM " . $wpdb->prefix . "users_purchases WHERE user_id=%d", $user_id);
        $esbIDs = $wpdb->get_col($query);
        
        if(!$esbIDs)
            return array();
        
        $query = "SELECT * FROM " . $this->table_metadata . " WHERE CUSTOMER_ID in (" . implode(",", $esbIDs) . ")";                
        
        if($id)   
        {
            if(is_array($id))
            {
                $query .= " AND ID in (" . implode(", ", $id) . ")";
            }else{
                $query .= ManageESB::$esbdb->prepare(" AND ID=%d", $id);
            }
        }
        
        if($product_id != null)
            $query .= ManageESB::$esbdb->prepare(" AND PRODUCT_ID=%d", $product_id);
        if($suite_id != null)
            $query .= ManageESB::$esbdb->prepare(" AND TEST_SUITE_ID=%d", $suite_id);
        if($case_id != null)
            $query .= ManageESB::$esbdb->prepare(" AND TEST_CASE_ID=%d", $case_id);
        if($service != null)
            $query .= ManageESB::$esbdb->prepare(" AND SERVICE=%s", $service);
        if($action != null)
            $query .= ManageESB::$esbdb->prepare(" AND ACTION=%s", $action);
        if($partyid != null)
            $query .= ManageESB::$esbdb->prepare(" AND (FROM_PARTY_ID=%s OR TO_PARTY_ID=%s)", $partyid, $partyid);
        if($date != null)
            $query .= ManageESB::$esbdb->prepare(" AND DATE(EXECUTION_DATE)=%s", date("Y-m-d", strtotime($date)));
               
        $rows = ManageESB::$esbdb->get_results($query);
        
        return $rows;
        
    }
}