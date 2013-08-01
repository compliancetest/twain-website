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
    var $table_test_case_name_id_map = 'TEST_CASE_NAME_ID_MAPS';
    var $table_test_suite_name_id_map = 'TEST_SUITE_NAME_ID_MAPS';
    
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
        
        $query = "SELECT m.*, c.ID AS TEST_CASE_DB_ID, s.NAME as TEST_SUITE_NAME FROM " . $this->table_metadata . " AS m " .
                 "LEFT JOIN " . $this->table_test_case_name_id_map . " AS c ON c.NAME=m.TEST_CASE_ID " .
                 "LEFT JOIN " . $this->table_test_suite_name_id_map . " AS s ON s.ID=m.TEST_SUITE_ID " .
                 "WHERE m.CUSTOMER_ID in (" . implode(",", $esbIDs) . ")";                
        
        if($id)   
        {
            if(is_array($id))
            {
                $query .= " AND m.ID in (" . implode(", ", $id) . ")";
            }else{
                $query .= ManageESB::$esbdb->prepare(" AND m.ID=%d", $id);
            }
        }
        
        
        $rows = ManageESB::$esbdb->get_results($query);
        
        return $rows;
        
    }
    
    
    public function  getUserTransactionLog($product_id = null, $suite_id = null, $case_id = null, $service = null, $action = null, $partyid = null, $date = null, $page = 1, $limit = -1)
    {
        global $wpdb;
        
        $user_id = get_current_user_id();
        
        if(!$user_id)
            return array();
        
        
        $where = array();
        
        //Getting User Customer IDs
        $query = $wpdb->prepare("SELECT esb_user_id FROM " . $wpdb->prefix . "users_purchases WHERE user_id=%d", $user_id);
        $esbIDs = $wpdb->get_col($query);
        
        if(!$esbIDs)
            return array();
        
        $query = " FROM " . $this->table_metadata . " AS m WHERE m.CUSTOMER_ID in (" . implode(",", $esbIDs) . ")";                
        $where[] = "m.CUSTOMER_ID in (" . implode(",", $esbIDs) . ")";
        
        if($id)   
        {
            if(is_array($id))
            {
                $where[] = " m.ID in (" . implode(", ", $id) . ")";
            }else{
                $where[] = ManageESB::$esbdb->prepare(" AND m.ID=%d", $id);
            }
        }
        
        if($product_id == 'NULL')
            $where[] = " m.PRODUCT_ID IS NULL";
        else if($product_id != null)
            $where[] = ManageESB::$esbdb->prepare(" m.PRODUCT_ID=%d", $product_id);
            
        if($suite_id == 'NULL')
            $where[] = " m.TEST_SUITE_ID IS NULL";
        else if($suite_id != null)
            $where[] = ManageESB::$esbdb->prepare(" m.TEST_SUITE_ID=%d", $suite_id);
            
        if($case_id == 'NULL')
            $where[] = " m.TEST_CASE_ID IS NULL";
        else if($case_id != null)
            $where[] = ManageESB::$esbdb->prepare(" c.ID=%d", $case_id);
            
        if($service != null)
            $where[] = ManageESB::$esbdb->prepare(" m.SERVICE=%s", $service);
        if($action != null)
            $where[] = ManageESB::$esbdb->prepare(" m.ACTION=%s", $action);
        if($partyid != null)
            $where[] = ManageESB::$esbdb->prepare(" (m.FROM_PARTY_ID=%s OR m.TO_PARTY_ID=%s)", $partyid, $partyid);
        if($date != null)
            $where[] = ManageESB::$esbdb->prepare(" DATE(m.EXECUTION_DATE)=%s", date("Y-m-d", strtotime($date)));
        
        
        if($limit > 0)
        {            
            $query = "SELECT count(m.ID) FROM " . $this->table_metadata . " AS m WHERE " . implode(" AND ", $where);
            $totalItems = ManageESB::$esbdb->get_var($query);
            $query = "SELECT m.*, c.ID AS TEST_CASE_DB_ID, s.NAME as TEST_SUITE_NAME FROM " . $this->table_metadata . " AS m " .
                     "LEFT JOIN " . $this->table_test_case_name_id_map . " AS c ON c.NAME=m.TEST_CASE_ID " .
                     "LEFT JOIN " . $this->table_test_suite_name_id_map . " AS s ON s.ID=m.TEST_SUITE_ID " .
                     "WHERE " . implode(" AND ", $where) . " LIMIT " . ($page -1 ) * $limit . ", " . $limit;
        }else{
            $query = "SELECT m.*, c.ID AS TEST_CASE_DB_ID, s.NAME as TEST_SUITE_NAME FROM " . $this->table_metadata . " AS m " .
                     "LEFT JOIN " . $this->table_test_case_name_id_map . " AS c ON c.NAME=m.TEST_CASE_ID " .
                     "LEFT JOIN " . $this->table_test_suite_name_id_map . " AS s ON s.ID=m.TEST_SUITE_ID " .
                     "WHERE " . implode(" AND ", $where);
        }
        
        $rows = ManageESB::$esbdb->get_results($query);
        
        //Check if the rows has validation result or not
        $ids = array();
        foreach($rows as $row)
            $ids[] = $row->ID;
        
        if($ids)
        {
            $hasVResults = ManageESB::$esbdb->get_col("SELECT DISTINCT(MSH_METADATA_ID) FROM " . $this->table_metadata_validation_result . " WHERE VALIDATION_ERROR IS NOT NULL AND MSH_METADATA_ID IN (" . implode(", ", $ids) .")");
            if($hasVResults)            
            {
                foreach($rows as $k=>$row)
                {
                    if(in_array($row->ID, $hasVResults))
                        $rows[$k]->HAS_VALIDATION_LOG = 1;
                }
            }
        }
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
    
    
    public function addTestCaseNameIDMap($id, $name)
    {
        $result = ManageESB::$esbdb->insert($this->table_test_case_name_id_map, array('ID' => $id, 'NAME' => $name));
        
        return $result;        
    }
    public function deleteTestCaseNameIDMap($id)
    {
        $result = ManageESB::$esbdb->delete($this->table_test_case_name_id_map, array('ID' => $id));
        
        return $result;        
    }
    
    
    public function addTestSuiteNameIDMap($id, $name)
    {
        //Delete Old Data
        $this->deleteTestSuiteNameIDMap($id);
        
        $result = ManageESB::$esbdb->insert($this->table_test_suite_name_id_map, array('ID' => $id, 'NAME' => $name));
        
        return $result;
    }
    
    public function deleteTestSuiteNameIDMap($id)
    {
        
        $result = ManageESB::$esbdb->delete($this->table_test_suite_name_id_map, array('ID' => $id));
        
        return $result;
    }
    
    
    
}