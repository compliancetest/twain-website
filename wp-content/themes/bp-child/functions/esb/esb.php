<?php
/**
* Manage Backend Data
*/
require_once(THE_FUNCTION . "/esb/config.php");

class ManageESB
{
    var $table_metadata = 'MSH_METADATA';
    var $table_metadata_payload = 'MSH_METADATA_PAYLOAD';
    var $table_metadata_validation_result = 'MSH_METADATA_VALIDATION_RESULTS';
    
    public static $esbdb = null;
    
    public function __construct()
    {
        if(ManageESB::$esbdb == null)
            $this->loadDatabase();
        
    }
    
    public function  loadDatabase()
    {
        $db = new wpdb(ESB_DB_USERNAME, ESB_DB_PASSWORD, ESB_DB_DATABASE, ESB_DB_HOST);
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
    
    public function  getTransactionLogByID($id = null, $user_id = null)
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
        
        if($product_id == 'NULL')
            $query .= " AND PRODUCT_ID IS NULL";
        else if($product_id != null)
            $query .= ManageESB::$esbdb->prepare(" AND PRODUCT_ID=%d", $product_id);
            
        if($suite_id == 'NULL')
            $query .= " AND TEST_SUITE_ID IS NULL";
        else if($suite_id != null)
            $query .= ManageESB::$esbdb->prepare(" AND TEST_SUITE_ID=%d", $suite_id);
            
        if($case_id == 'NULL')
            $query .= " AND TEST_CASE_ID IS NULL";
        else if($case_id != null)
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
    
    
    public function  getUserTransactionLog($product_id = null, $suite_id = null, $case_id = null, $service = null, $action = null, $partyid = null, $date = null, $page = 1, $limit = -1)
    {
        global $wpdb;
        
        $user_id = get_current_user_id();
        
        if(!$user_id)
            return array();
        
        //Getting User Customer IDs
        $query = $wpdb->prepare("SELECT esb_user_id FROM " . $wpdb->prefix . "users_purchases WHERE user_id=%d", $user_id);
        $esbIDs = $wpdb->get_col($query);
        
        if(!$esbIDs)
            return array();
        
        $query = " FROM " . $this->table_metadata . " WHERE CUSTOMER_ID in (" . implode(",", $esbIDs) . ")";                
        
        if($id)   
        {
            if(is_array($id))
            {
                $query .= " AND ID in (" . implode(", ", $id) . ")";
            }else{
                $query .= ManageESB::$esbdb->prepare(" AND ID=%d", $id);
            }
        }
        
        if($product_id == 'NULL')
            $query .= " AND PRODUCT_ID IS NULL";
        else if($product_id != null)
            $query .= ManageESB::$esbdb->prepare(" AND PRODUCT_ID=%d", $product_id);
            
        if($suite_id == 'NULL')
            $query .= " AND TEST_SUITE_ID IS NULL";
        else if($suite_id != null)
            $query .= ManageESB::$esbdb->prepare(" AND TEST_SUITE_ID=%d", $suite_id);
            
        if($case_id == 'NULL')
            $query .= " AND TEST_CASE_ID IS NULL";
        else if($case_id != null)
            $query .= ManageESB::$esbdb->prepare(" AND TEST_CASE_ID=%d", $case_id);
            
        if($service != null)
            $query .= ManageESB::$esbdb->prepare(" AND SERVICE=%s", $service);
        if($action != null)
            $query .= ManageESB::$esbdb->prepare(" AND ACTION=%s", $action);
        if($partyid != null)
            $query .= ManageESB::$esbdb->prepare(" AND (FROM_PARTY_ID=%s OR TO_PARTY_ID=%s)", $partyid, $partyid);
        if($date != null)
            $query .= ManageESB::$esbdb->prepare(" AND DATE(EXECUTION_DATE)=%s", date("Y-m-d", strtotime($date)));
        
        
        if($limit > 0)
        {            
            $query1 = "SELECT count(*) " . $query;
            $totalItems = ManageESB::$esbdb->get_var($query1);
            $query2 .= "SELECT * " . $query . " LIMIT " . ($page -1 ) * $limit . ", " . $limit;
        }else{
            $query2 = "SELECT * " . $query;
        }
        
        $rows = ManageESB::$esbdb->get_results($query2);
        
        if(!isset($totalItems))
            $totalItems = count($rows);
        
        return array('total' => $totalItems, 'data' => $rows);
        
    }
    
    public function  getMessageEnvelope($id, $user_id = null)
    {
        global $wpdb;
        
        if($user_id == null)
            $user_id = get_current_user_id();
        
        if(!$user_id)
            return null;
        
        //Getting User Customer IDs
        $query = $wpdb->prepare("SELECT esb_user_id FROM " . $wpdb->prefix . "users_purchases WHERE user_id=%d", $user_id);
        $esbIDs = $wpdb->get_col($query);
        
        if(!$esbIDs)
            return array();
        
        $query = "SELECT ml.PAYLOAD FROM " . $this->table_metadata_payload . " AS ml, " . $this->table_metadata . " AS m WHERE ml.MSH_METADATA_ID=m.ID AND m.ID=" . intval($id) . " AND m.CUSTOMER_ID in (" . implode(",", $esbIDs) . ")";                
        
        $data = ManageESB::$esbdb->get_var($query);
        
        return $data;
        
    }
    
    /**
    * Get Validation Rows
    * 
    * @param Int $id: Metadata ID
    * @param Int $user_id
    */
    public function  getValidationResult($id, $user_id = null)
    {
        global $wpdb;
        
        if($user_id == null)
            $user_id = get_current_user_id();
        
        if(!$user_id)
            return null;
        
        //Getting User Customer IDs
        $query = $wpdb->prepare("SELECT esb_user_id FROM " . $wpdb->prefix . "users_purchases WHERE user_id=%d", $user_id);
        $esbIDs = $wpdb->get_col($query);
        
        if(!$esbIDs)
            return null;
        
        $query = "SELECT ml.* FROM " . $this->table_metadata_validation_result . " AS ml, " . $this->table_metadata . " AS m WHERE ml.MSH_METADATA_ID=m.ID AND m.ID=" . intval($id) . " AND m.CUSTOMER_ID in (" . implode(",", $esbIDs) . ")";                
        
        $data = ManageESB::$esbdb->get_results($query);
        
        return $data;
        
    }
    
    /**
    * Get Validation Error
    * 
    * @param Int $id: Validation Row ID
    * @param Int $user_id
    */
    public function getValidationError($id, $user_id = null)
    {
        global $wpdb;
        
        if($user_id == null)
            $user_id = get_current_user_id();
        
        if(!$user_id)
            return null;
        
        //Getting User Customer IDs
        $query = $wpdb->prepare("SELECT esb_user_id FROM " . $wpdb->prefix . "users_purchases WHERE user_id=%d", $user_id);
        $esbIDs = $wpdb->get_col($query);
        
        if(!$esbIDs)
            return null;
        
        $query = "SELECT ml.VALIDATION_ERROR FROM " . $this->table_metadata_validation_result . " AS ml, " . $this->table_metadata . " AS m WHERE ml.MSH_METADATA_ID=m.ID AND ml.ID=" . intval($id) . " AND m.CUSTOMER_ID in (" . implode(",", $esbIDs) . ")";                
        
        $data = ManageESB::$esbdb->get_var($query);
        
        return $data;
    }
    
    
}