<?php
/**
* Manage Backend Data
*/
require_once(THE_FUNCTION . "/esb/config.php");

class ManageESB
{
    var $table_conversation_metadata = 'MSH_CONVERSATION_METADATA';
    var $table_test_outcome_status = 'MSH_TEST_OUTCOME_STATUS';
    var $table_message_metadata = 'MSH_MESSAGE_METADATA';
    var $table_message_outcome_status = 'MSH_MESSAGE_OUTCOME_STATUS';
    
    var $table_message_validation_results = 'MSH_MESSAGE_VALIDATION_RESULTS';    
    var $table_message_validation_phases = 'MSH_MESSAGE_VALIDATION_PHASES';
    var $table_message_validation_statuses = 'MSH_MESSAGE_VALIDATION_STATUSES';
    
    var $table_test_case_configuration = 'TEST_CASE_CONFIGURATION';
    var $table_test_suite_name_id_map = 'TEST_SUITE_NAME_ID_MAPS';
    var $table_product_name_id_map = 'PRODUCT_NAME_ID_MAPS';
    
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
    
    public function getCaseStatus($esb_user_id, $suite_id = null, $product_id = null, $case_id = null)
    {
        if($suite_id != null)
            $this->addTestSuiteIDToLog($suite_id);
        
        $query = ManageESB::$esbdb->prepare("SELECT m.TEST_SUITE_ID, p.ID AS PRODUCT_ID, ts.*, c.TEST_CASE_ID, c.TEST_CASE_WP_ID as TEST_CASE_DB_ID FROM " . $this->table_conversation_metadata . " AS m " .
                                            "LEFT JOIN " . $this->table_test_outcome_status . " AS ts ON ts.ID=m.MSH_TEST_OUTCOME_STATUS_ID " .
                                            "LEFT JOIN " . $this->table_product_name_id_map . " AS p ON p.NAME=m.PRODUCT_ID " .
                                            "LEFT JOIN " . $this->table_test_case_configuration . " AS c ON c.ID=m.TEST_CASE_CONFIGURATION_ID WHERE m.CUSTOMER_ID=%d", $esb_user_id);
        
        if($suite_id != null)
            $query .= ManageESB::$esbdb->prepare(" AND m.TEST_SUITE_ID=%d", $suite_id);
        
        if($product_id != null)
            $query .= ManageESB::$esbdb->prepare(" AND m.PRODUCT_ID=%d", $product_id);
        
        if($case_id != null)
            $query .= ManageESB::$esbdb->prepare(" AND c.TEST_CASE_DB_ID=%d", $case_id);
        
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
            
            if(!$row->TEST_OUTCOME_CODE)
                continue;
//                $row->TEST_OUTCOME = $this->getTestOutcomeStatus($row->TEST_CASE_DB_ID, get_post_meta($row->TEST_CASE_DB_ID, 'outcome_type', true));
            
            if($row->TEST_OUTCOME_CODE == 'PASS')
                $result[$row->TEST_SUITE_ID][$row->PRODUCT_ID][$row->TEST_CASE_DB_ID] = 'pass';
            else if($row->TEST_OUTCOME_CODE == 'FAIL')
                $result[$row->TEST_SUITE_ID][$row->PRODUCT_ID][$row->TEST_CASE_DB_ID] = 'fail';
        }
        
        return $result;
    }
    
    public function addTestSuiteIDToLog($suite_id)
    {
        $suiteObj = new TestSuite($suite_id);
        $testCases = $suiteObj->loadTestCases();
        
        $where = array();
        foreach($testCases as $c)
        {
            $where[] = ManageESB::$esbdb->prepare(" c.ID=%s ", $c->ID);
        }
        
        if($where)
        {
            $query = "UPDATE " . $this->table_conversation_metadata . " AS m " . 
            "LEFT JOIN " . $this->table_test_case_configuration . " AS c ON c.ID=m.TEST_CASE_CONFIGURATION_ID SET m.TEST_SUITE_ID=" . $suite_id . " WHERE " . implode(" OR ", $where);
            
            ManageESB::$esbdb->query($query);
        }
        
    }
    
    public function  getTransactionLogByID($id = null, $user_id = null)
    {
        global $wpdb;
        
        if($user_id == null)
            $user_id = get_current_user_id();
        
        if(!$user_id)
            return array();
        
        $customerWhere = '';
        if( ($customer_id !== null && $customer_id != "") || (!is_super_admin() || is_admin()) )
        {
            $esbIDs = $this->_getUserESBIds($user_id, $customer_id);
            
            if(!$esbIDs)
                return array();
            
            $customer_id = " AND c.CUSTOMER_ID in (" . implode(",", $esbIDs) . ")";
        }        
        
        $query = "SELECT c.*, tc.TEST_CASE_WP_ID AS TEST_CASE_DB_ID, s.NAME as TEST_SUITE_NAME,ts.TEST_OUTCOME_CODE, ts.TEST_OUTCOME_LABEL FROM " . $this->table_conversation_metadata . " AS c " .
                 "LEFT JOIN " . $this->table_test_case_configuration . " AS tc ON tc.ID=c.TEST_CASE_CONFIGURATION_ID " .
                 "LEFT JOIN " . $this->table_test_suite_name_id_map . " AS s ON s.ID=c.TEST_SUITE_ID " .
                 "LEFT JOIN " . $this->table_product_name_id_map . " AS p ON p.NAME=c.PRODUCT_ID " .
                 "LEFT JOIN " . $this->table_test_outcome_status . " AS ts ON ts.ID=c.MSH_TEST_OUTCOME_STATUS_ID " .
                 "WHERE 1 " . $customerWhere;                
        
        $id = $wpdb->escape($id);
        
        if($id)   
        {
            if(is_array($id))
            {
                $query .= " AND c.ID in (" . implode(", ", $id) . ")";
            }else{
                $query .= ManageESB::$esbdb->prepare(" AND c.ID=%d", $id);
            }
        }
        
        
        $rows = ManageESB::$esbdb->get_results($query);
        
        return $rows;
        
    }
    
    /**
    * Getting User 
    * 
    * @param Int $user_id
    * @param Int $customer_id
    */
    public function _getUserESBIds($user_id = null, $customer_id = null)
    {
        global $wpdb;
        
        if($user_id == null)
            $user_id = get_current_user_id();
        
        $query = "SELECT DISTINCT(esb_user_id) FROM " . $wpdb->prefix . "users_purchases WHERE `status`='Active'";
        
        if($customer_id != null)
            $query .= " AND " . $wpdb->prepare("user_id=%d", $customer_id);
        
        if(!is_super_admin($user_id) && !is_admin())
        {
            $suite_ids = getAssignedSuiteIds($user_id);
        
            if(!$suite_ids)
                $query .= " AND " . $wpdb->prepare("user_id=%d", $user_id);
            else
                $query .= " AND (user_id=$user_id OR suite_id IN (" . implode(", ", $suite_ids) . ") )";
        }
        
        $ids = $wpdb->get_col($query);
        
        return $ids;
    }


    
    public function  getUserTransactionLog($product_id = null, $suite_id = null, $case_id = null, $service = null, $action = null, $partyid = null, $date = null, $customer_id = null, $page = 1, $limit = -1, $orderby = null, $order = 'asc')
    {
        global $wpdb;
        
        $user_id = get_current_user_id();
        
        if(!$user_id)
            return array();
        
        
        $where = array();
        $message_where = array();
        $has_message_query = false;
        
        if( ($customer_id !== null && $customer_id != "") || (!is_super_admin() || is_admin()) )
        {
            $esbIDs = $this->_getUserESBIds($user_id, $customer_id);
            if(!$esbIDs)
                return array();
            
            $where[] = "c.CUSTOMER_ID in (" . implode(",", $esbIDs) . ")";
        }        
        
        if($id)   
        {
            if(is_array($id))
            {
                $where[] = " c.ID in (" . implode(", ", $id) . ")";
            }else{
                $where[] = ManageESB::$esbdb->prepare(" AND c.ID=%d", $id);
            }
        }
        
        if($product_id !== null && $product_id != "")
        {
            if($product_id == 0)
                $where[] = " IFNULL(p.ID, 0) = 0";
            else if($product_id != null)
                $where[] = ManageESB::$esbdb->prepare(" p.ID=%d", $product_id);    
        }
        
        if($suite_id !== null && $suite_id != "")
        {
            if($suite_id == 0)
                $where[] = " IFNULL(c.TEST_SUITE_ID, '') = ''";
            else if($suite_id != null)
                $where[] = ManageESB::$esbdb->prepare(" c.TEST_SUITE_ID=%d", $suite_id);
        }
            
        if($case_id !== "" && $case_id !== null)
            $where[] = ManageESB::$esbdb->prepare(" cm.TEST_CASE_WP_ID=%d", $case_id);
            
        if($date != null)
            $where[] = ManageESB::$esbdb->prepare(" DATE(c.CONVERSATION_TIMESTAMP)=%s", date("Y-m-d", strtotime($date)));
                
        if($service != null)    
        {
            $where[] = ManageESB::$esbdb->prepare(" m.SERVICE=%s", $service);
            $message_where[] = ManageESB::$esbdb->prepare(" m.SERVICE=%s", $service);
            $has_message_query = true;
        }
            
        if($action != null){
            $where[] = ManageESB::$esbdb->prepare(" m.ACTION=%s", $action);
            $message_where[] = ManageESB::$esbdb->prepare(" m.ACTION=%s", $action);
            $has_message_query = true;
        }
            
        if($partyid != null){
            $where[] = ManageESB::$esbdb->prepare(" (m.FROM_PARTY_ID=%s OR m.TO_PARTY_ID=%s)", $partyid, $partyid);
            $message_where[] = ManageESB::$esbdb->prepare(" (m.FROM_PARTY_ID=%s OR m.TO_PARTY_ID=%s)", $partyid, $partyid);
            $has_message_query = true;
        }
        
        $orderQuery = "";
        if($orderby)
        {
            switch($orderby)
            {
                case 'product':
                    $orderQuery .= ' ORDER BY PRODUCT_NAME ' . $order . ', c.PRODUCT_ID ' . $order;
                    break;
                case 'case':
                    $orderQuery .= ' ORDER BY TEST_CASE_ID ' . $order;
                    break;
                case 'suite':
                    $orderQuery .= ' ORDER BY TEST_SUITE_NAME ' . $order;
                    break;
                case 'test_outcome':
                    $orderQuery .= ' ORDER BY TEST_OUTCOME_LABEL ' . $order;
                    break;
                case 'audit':
                    $orderQuery .= ' ORDER BY AUDIT_RECORD ' . $order;
                    break;
                /*case 'service':
                    $orderQuery .= ' ORDER BY SERVICE ' . $order;
                    break;
                case 'action':
                    $orderQuery .= ' ORDER BY ACTION ' . $order;
                    break;*/
                case 'message':
                    $orderQuery .= ' ORDER BY CONVERSATION_ID ' . $order;
                    break;
                case 'date':
                    $orderQuery .= ' ORDER BY CONVERSATION_TIMESTAMP ' . $order;
                    break;
                //case 'from':
//                    $orderQuery .= ' ORDER BY FROM_PARTY_ID ' . $order;
                    break;                
            }
        }
        
        if($limit > 0)
        {            
            $query = "SELECT count(DISTINCT(c.ID)) FROM " . $this->table_conversation_metadata . " AS c ";
            if($has_message_query > 0) 
                $query .= " LEFT JOIN " . $this->table_message_metadata . " AS m ON m.MSH_CONVERSATION_ID=c.ID ";
            
            if($where)
                $query .= " WHERE " . implode(" AND ", $where);            
            $totalItems = ManageESB::$esbdb->get_var($query);
            
            $query = "SELECT 
                        DISTINCT(c.ID), 
                        c.*,
                        cm.TEST_CASE_WP_ID, 
                        cm.TEST_CASE_ID, 
                        p.ID as PRODUCT_WP_ID,
                        s.NAME as TEST_SUITE_NAME, 
                        ts.TEST_OUTCOME_CODE, 
                        ts.TEST_OUTCOME_LABEL 
                     FROM " . $this->table_conversation_metadata . " AS c " .
                     "LEFT JOIN " . $this->table_test_case_configuration . " AS cm ON cm.ID=c.TEST_CASE_CONFIGURATION_ID " .
                     "LEFT JOIN " . $this->table_test_suite_name_id_map . " AS s ON s.ID=c.TEST_SUITE_ID " .
                     "LEFT JOIN " . $this->table_product_name_id_map . " AS p ON p.NAME=c.PRODUCT_ID " .
                     "LEFT JOIN " . $this->table_test_outcome_status . " AS ts ON ts.ID=c.MSH_TEST_OUTCOME_STATUS_ID ";
            if($has_message_query > 0) 
                $query .= " LEFT JOIN " . $this->table_message_metadata . " AS m ON m.MSH_CONVERSATION_ID=c.ID ";
            if($where)
                $query .= " WHERE " . implode(" AND ", $where);
            $query .= $orderQuery . " LIMIT " . ($page -1 ) * $limit . ", " . $limit;
            
        }else{
            $query = "SELECT 
                        DISTINCT(c.ID), 
                        c.*,
                        cm.TEST_CASE_ID, 
                        cm.TEST_CASE_WP_ID, 
                        p.ID as PRODUCT_WP_ID,
                        s.NAME as TEST_SUITE_NAME, 
                        ts.TEST_OUTCOME_CODE, 
                        ts.TEST_OUTCOME_LABEL 
                     FROM " . $this->table_conversation_metadata . " AS c " .
                     "LEFT JOIN " . $this->table_test_case_configuration . " AS cm ON cm.ID=c.TEST_CASE_CONFIGURATION_ID " .
                     "LEFT JOIN " . $this->table_test_suite_name_id_map . " AS s ON s.ID=c.TEST_SUITE_ID " .
                     "LEFT JOIN " . $this->table_product_name_id_map . " AS p ON p.NAME=c.PRODUCT_ID " .
                     "LEFT JOIN " . $this->table_test_outcome_status . " AS ts ON ts.ID=c.MSH_TEST_OUTCOME_STATUS_ID ";
            if($has_message_query > 0) 
                $query .= " LEFT JOIN " . $this->table_message_metadata . " AS m ON m.MSH_CONVERSATION_ID=c.ID ";
            if($where)
                $query .= " WHERE " . implode(" AND ", $where);
            $query .= $orderQuery;
        }        
        
        $rows = ManageESB::$esbdb->get_results($query);
        
        //Getting Messages
        $ids = array();
        foreach($rows as $row)
            $ids[] = $row->ID;
        
        $messages = array();
        
        if($ids)
        {
            $query = "SELECT 
                        DISTINCT(m.ID), 
                        m.*, 
                        ms.MESSAGE_OUTCOME_CODE, 
                        ms.MESSAGE_OUTCOME_LABEL 
                     FROM " . $this->table_message_metadata . " AS m " . 
                     "LEFT JOIN " . $this->table_message_outcome_status . " AS ms ON ms.ID=m.MSH_MESSAGE_OUTCOME_STATUS_ID " . 
                     "LEFT JOIN " . $this->table_message_validation_results . " AS mv ON mv.MSH_MESSAGE_METADATA_ID=m.ID " . 
                     "WHERE m.MSH_CONVERSATION_ID in (" . implode(", ", $ids) . ") " . ($has_message_query ? " AND " . implode(", ", $message_where) : "") .  " ORDER BY m.MSH_CONVERSATION_ID";
            
            $results = ManageESB::$esbdb->get_results($query);

            foreach($results as $m)
            {
                if(!isset($messages[$m->MSH_CONVERSATION_ID]))
                    $messages[$m->MSH_CONVERSATION_ID] = array();
                    
                $messages[$m->MSH_CONVERSATION_ID][] = $m;
            }
        }
        
        if(!isset($totalItems))
            $totalItems = count($rows);
        
        return array('total' => $totalItems, 'data' => $rows, 'messages' => $messages);
        
    }
    
    /**
    * Get Product IDs For Filter Nav
    * 
    * @param Int $suite_id
    * @param Int $case_id
    * @param String $service
    * @param String $action
    * @param String $partyid
    * @param Date $date
    */
    public function getFilterOptionsForProduct($suite_id = null, $case_id = null, $service = null, $action = null, $partyid = null, $date = null, $customer_id = null)
    {
        global $wpdb;
        
        $user_id = get_current_user_id();
        
        $query = "SELECT 
                    DISTINCT(c.PRODUCT_ID)
                  FROM " . $this->table_conversation_metadata . " AS c 
                  LEFT JOIN " . $this->table_message_metadata . " AS m ON m.MSH_CONVERSATION_ID = c.ID
                  LEFT JOIN " . $this->table_test_case_configuration . " AS cm ON c.TEST_CASE_CONFIGURATION_ID=cm.ID ";
        
        $where = array();
                
        if( ($customer_id !== null && $customer_id != "") || (!is_super_admin() || is_admin()) )
        {
            $esbIDs = $this->_getUserESBIds($user_id, $customer_id);
            if(!$esbIDs)
                return array();
            
            $where[] = "c.CUSTOMER_ID in (" . implode(",", $esbIDs) . ")";
        }
        
        
        if($suite_id !== null && $suite_id != "")
        {
            if($suite_id == 0)
                $where[] = " IFNULL(c.TEST_SUITE_ID, '') = ''";
            else if($suite_id != null)
                $where[] = ManageESB::$esbdb->prepare(" c.TEST_SUITE_ID=%d", $suite_id);
        }
            
        if($case_id !== "" && $case_id !== null)
            $where[] = ManageESB::$esbdb->prepare(" cm.TEST_CASE_WP_ID=%d", $case_id);
            
        if($date != null)
            $where[] = ManageESB::$esbdb->prepare(" DATE(c.CONVERSATION_TIMESTAMP)=%s", date("Y-m-d", strtotime($date)));
        
        if($service != null)    
        {
            $where[] = ManageESB::$esbdb->prepare(" m.SERVICE=%s", $service);            
        }
            
        if($action != null){
            $where[] = ManageESB::$esbdb->prepare(" m.ACTION=%s", $action);
        }
            
        if($partyid != null){
            $where[] = ManageESB::$esbdb->prepare(" (m.FROM_PARTY_ID=%s OR m.TO_PARTY_ID=%s)", $partyid, $partyid);
        }
        
        if($where)
            $query .= " WHERE " . implode(" AND ", $where);
        
        $ids = ManageESB::$esbdb->get_col($query);
        
        return $ids;
        
    }
    
    /**
    * Get Suites For Filter Options
    * 
    * @param Int $product_id
    * @param Int $case_id
    * @param String $service
    * @param String $action
    * @param String $partyid
    * @param Date $date
    * @param mixed $product_id
    */
    public function getFilterOptionsForSuite($product_id = null, $case_id = null, $service = null, $action = null, $partyid = null, $date = null)
    {
        global $wpdb;
        
        $user_id = get_current_user_id();
        
        $where = array();
        
        $query = "SELECT 
                    DISTINCT(IFNULL(c.TEST_SUITE_ID, '')) as ID, tm.NAME
                  FROM " . $this->table_conversation_metadata . " AS c 
                  LEFT JOIN " . $this->table_test_suite_name_id_map . " AS tm ON tm.id = c.TEST_SUITE_ID
                  LEFT JOIN " . $this->table_message_metadata . " AS m ON m.MSH_CONVERSATION_ID = c.ID
                  LEFT JOIN " . $this->table_test_case_configuration . " AS cm ON c.TEST_CASE_CONFIGURATION_ID=cm.ID ";
                
        if( ($customer_id !== null && $customer_id != "") || (!is_super_admin() || is_admin()) )
        {
            $esbIDs = $this->_getUserESBIds($user_id, $customer_id);
            if(!$esbIDs)
                return array();
            
            $where[] = "c.CUSTOMER_ID in (" . implode(",", $esbIDs) . ")";
        }
        
        if($product_id !== null && $product_id != "")
        {
            if($product_id == 0)
                $where[] = " IFNULL(c.PRODUCT_ID, 0) = 0";
            else if($product_id != null)
                $where[] = ManageESB::$esbdb->prepare(" c.PRODUCT_ID=%d", $product_id);    
        }
            
        if($case_id !== "" && $case_id !== null)
            $where[] = ManageESB::$esbdb->prepare(" cm.TEST_CASE_WP_ID=%d", $case_id);
            
        if($date != null)
            $where[] = ManageESB::$esbdb->prepare(" DATE(c.CONVERSATION_TIMESTAMP)=%s", date("Y-m-d", strtotime($date)));
        
        if($service != null)    
        {
            $where[] = ManageESB::$esbdb->prepare(" m.SERVICE=%s", $service);            
        }
            
        if($action != null){
            $where[] = ManageESB::$esbdb->prepare(" m.ACTION=%s", $action);
        }
            
        if($partyid != null){
            $where[] = ManageESB::$esbdb->prepare(" (m.FROM_PARTY_ID=%s OR m.TO_PARTY_ID=%s)", $partyid, $partyid);
        }
        
        if($where)
            $query .= " WHERE " . implode(" AND ", $where);
        
        $rows = ManageESB::$esbdb->get_results($query);
        
        return $rows;
        
    }
    
    /**
    * Get Test Cases for Filter Navigation
    * 
    * @param mixed $product_id
    * @param mixed $suite_id
    * @param mixed $service
    * @param mixed $action
    * @param mixed $partyid
    * @param mixed $date
    * @return array
    */
    public function getFilterOptionsForCase($product_id = null, $suite_id = null, $service = null, $action = null, $partyid = null, $date = null)
    {
        global $wpdb;
        
        $user_id = get_current_user_id();
        
        $where = array();
        
        $query = "SELECT 
                    DISTINCT(cm.TEST_CASE_ID) as NAME, cm.TEST_CASE_WP_ID as ID
                  FROM " . $this->table_conversation_metadata . " AS c 
                  LEFT JOIN " . $this->table_test_suite_name_id_map . " AS tm ON tm.id = c.TEST_SUITE_ID
                  LEFT JOIN " . $this->table_message_metadata . " AS m ON m.MSH_CONVERSATION_ID = c.ID
                  LEFT JOIN " . $this->table_test_case_configuration . " AS cm ON c.TEST_CASE_CONFIGURATION_ID=cm.ID ";
                
        if( ($customer_id !== null && $customer_id != "") || (!is_super_admin() || is_admin()) )
        {
            $esbIDs = $this->_getUserESBIds($user_id, $customer_id);
            if(!$esbIDs)
                return array();
            
            $where[] = "c.CUSTOMER_ID in (" . implode(",", $esbIDs) . ")";
        }
        
        if($product_id !== null && $product_id != "")
        {
            if($product_id == 0)
                $where[] = " IFNULL(c.PRODUCT_ID, 0) = 0";
            else if($product_id != null)
                $where[] = ManageESB::$esbdb->prepare(" c.PRODUCT_ID=%d", $product_id);    
        }
            
        if($suite_id !== null && $suite_id != "")
        {
            if($suite_id == 0)
                $where[] = " IFNULL(c.TEST_SUITE_ID, '') = ''";
            else if($suite_id != null)
                $where[] = ManageESB::$esbdb->prepare(" c.TEST_SUITE_ID=%d", $suite_id);
        }
            
        if($date != null)
            $where[] = ManageESB::$esbdb->prepare(" DATE(c.CONVERSATION_TIMESTAMP)=%s", date("Y-m-d", strtotime($date)));
        
        if($service != null)    
        {
            $where[] = ManageESB::$esbdb->prepare(" m.SERVICE=%s", $service);            
        }
            
        if($action != null){
            $where[] = ManageESB::$esbdb->prepare(" m.ACTION=%s", $action);
        }
            
        if($partyid != null){
            $where[] = ManageESB::$esbdb->prepare(" (m.FROM_PARTY_ID=%s OR m.TO_PARTY_ID=%s)", $partyid, $partyid);
        }
        
        if($where)
            $query .= " WHERE " . implode(" AND ", $where);
        
        $rows = ManageESB::$esbdb->get_results($query);
        
        return $rows;
        
    }
    
    /**
    * Get Services for Filter Options
    * 
    * @param mixed $product_id
    * @param mixed $suite_id
    * @param mixed $case_id
    * @param mixed $action
    * @param mixed $partyid
    * @param mixed $date
    */
    public function getFilterOptionsForService($product_id = null, $suite_id = null, $case_id = null, $action = null, $partyid = null, $date = null)
    {
        global $wpdb;
        
        $user_id = get_current_user_id();
        
        $where = array();
        
        $query = "SELECT 
                    DISTINCT(m.SERVICE)
                  FROM " . $this->table_conversation_metadata . " AS c 
                  LEFT JOIN " . $this->table_test_suite_name_id_map . " AS tm ON tm.id = c.TEST_SUITE_ID
                  LEFT JOIN " . $this->table_message_metadata . " AS m ON m.MSH_CONVERSATION_ID = c.ID
                  LEFT JOIN " . $this->table_test_case_configuration . " AS cm ON c.TEST_CASE_CONFIGURATION_ID=cm.ID ";
                
        if( ($customer_id !== null && $customer_id != "") || (!is_super_admin() || is_admin()) )
        {
            $esbIDs = $this->_getUserESBIds($user_id, $customer_id);
            if(!$esbIDs)
                return array();
            
            $where[] = "c.CUSTOMER_ID in (" . implode(",", $esbIDs) . ")";
        }
        
        if($product_id !== null && $product_id != "")
        {
            if($product_id == 0)
                $where[] = " IFNULL(c.PRODUCT_ID, 0) = 0";
            else if($product_id != null)
                $where[] = ManageESB::$esbdb->prepare(" c.PRODUCT_ID=%d", $product_id);    
        }
            
        if($suite_id !== null && $suite_id != "")
        {
            if($suite_id == 0)
                $where[] = " IFNULL(c.TEST_SUITE_ID, '') = ''";
            else if($suite_id != null)
                $where[] = ManageESB::$esbdb->prepare(" c.TEST_SUITE_ID=%d", $suite_id);
        }
        
        if($case_id !== "" && $case_id !== null)
            $where[] = ManageESB::$esbdb->prepare(" cm.TEST_CASE_WP_ID=%d", $case_id);
            
        if($date != null)
            $where[] = ManageESB::$esbdb->prepare(" DATE(c.CONVERSATION_TIMESTAMP)=%s", date("Y-m-d", strtotime($date)));
            
        if($action != null){
            $where[] = ManageESB::$esbdb->prepare(" m.ACTION=%s", $action);
        }
            
        if($partyid != null){
            $where[] = ManageESB::$esbdb->prepare(" (m.FROM_PARTY_ID=%s OR m.TO_PARTY_ID=%s)", $partyid, $partyid);
        }
        
        //Remove Empty Fields
        $where[] = " SERVICE IS NOT NULL ";
        
        if($where)
            $query .= " WHERE " . implode(" AND ", $where);
        
        $rows = ManageESB::$esbdb->get_col($query);
        
        return $rows;
        
    }
    
    /**
    * Getting Actions for Filter Options
    * 
    * @param mixed $product_id
    * @param mixed $suite_id
    * @param mixed $case_id
    * @param mixed $service
    * @param mixed $partyid
    * @param mixed $date
    * @return array
    */
    public function getFilterOptionsForAction($product_id = null, $suite_id = null, $case_id = null, $service = null, $partyid = null, $date = null)
    {
        global $wpdb;
        
        $user_id = get_current_user_id();
        
        $where = array();
        
        $query = "SELECT 
                    DISTINCT(m.ACTION)
                  FROM " . $this->table_conversation_metadata . " AS c 
                  LEFT JOIN " . $this->table_test_suite_name_id_map . " AS tm ON tm.id = c.TEST_SUITE_ID
                  LEFT JOIN " . $this->table_message_metadata . " AS m ON m.MSH_CONVERSATION_ID = c.ID
                  LEFT JOIN " . $this->table_test_case_configuration . " AS cm ON c.TEST_CASE_CONFIGURATION_ID=cm.ID ";
                
        if( ($customer_id !== null && $customer_id != "") || (!is_super_admin() || is_admin()) )
        {
            $esbIDs = $this->_getUserESBIds($user_id, $customer_id);
            if(!$esbIDs)
                return array();
            
            $where[] = "c.CUSTOMER_ID in (" . implode(",", $esbIDs) . ")";
        }
        
        if($product_id !== null && $product_id != "")
        {
            if($product_id == 0)
                $where[] = " IFNULL(c.PRODUCT_ID, 0) = 0";
            else if($product_id != null)
                $where[] = ManageESB::$esbdb->prepare(" c.PRODUCT_ID=%d", $product_id);    
        }
            
        if($suite_id !== null && $suite_id != "")
        {
            if($suite_id == 0)
                $where[] = " IFNULL(c.TEST_SUITE_ID, '') = ''";
            else if($suite_id != null)
                $where[] = ManageESB::$esbdb->prepare(" c.TEST_SUITE_ID=%d", $suite_id);
        }
        
        if($case_id !== "" && $case_id !== null)
            $where[] = ManageESB::$esbdb->prepare(" cm.TEST_CASE_WP_ID=%d", $case_id);
            
        if($date != null)
            $where[] = ManageESB::$esbdb->prepare(" DATE(c.CONVERSATION_TIMESTAMP)=%s", date("Y-m-d", strtotime($date)));
            
        if($service != null){
            $where[] = ManageESB::$esbdb->prepare(" m.SERVICE=%s", $service);
        }
            
        if($partyid != null){
            $where[] = ManageESB::$esbdb->prepare(" (m.FROM_PARTY_ID=%s OR m.TO_PARTY_ID=%s)", $partyid, $partyid);
        }
        
        //Remove Empty Fields
        $where[] = " ACTION IS NOT NULL ";
        
        if($where)
            $query .= " WHERE " . implode(" AND ", $where);
        
        $rows = ManageESB::$esbdb->get_col($query);
        
        return $rows;
        
    }
    
    /**
    * Get Party ID 
    * 
    * @param mixed $product_id
    * @param mixed $suite_id
    * @param mixed $case_id
    * @param mixed $service
    * @param mixed $action
    * @param mixed $date
    */
    public function getFilterOptionsForPartId($product_id = null, $suite_id = null, $case_id = null, $service = null, $action = null, $date = null)
    {
        global $wpdb;
        
        $user_id = get_current_user_id();
        
        $where = array();
        
        $query = "SELECT 
                    DISTINCT(m.FROM_PARTY_ID), m.TO_PARTY_ID
                  FROM " . $this->table_conversation_metadata . " AS c 
                  LEFT JOIN " . $this->table_test_suite_name_id_map . " AS tm ON tm.id = c.TEST_SUITE_ID
                  LEFT JOIN " . $this->table_message_metadata . " AS m ON m.MSH_CONVERSATION_ID = c.ID
                  LEFT JOIN " . $this->table_test_case_configuration . " AS cm ON c.TEST_CASE_CONFIGURATION_ID=cm.ID ";
                
        if( ($customer_id !== null && $customer_id != "") || (!is_super_admin() || is_admin()) )
        {
            $esbIDs = $this->_getUserESBIds($user_id, $customer_id);
            if(!$esbIDs)
                return array();
            
            $where[] = "c.CUSTOMER_ID in (" . implode(",", $esbIDs) . ")";
        }
        
        if($product_id !== null && $product_id != "")
        {
            if($product_id == 0)
                $where[] = " IFNULL(c.PRODUCT_ID, 0) = 0";
            else if($product_id != null)
                $where[] = ManageESB::$esbdb->prepare(" c.PRODUCT_ID=%d", $product_id);    
        }
            
        if($suite_id !== null && $suite_id != "")
        {
            if($suite_id == 0)
                $where[] = " IFNULL(c.TEST_SUITE_ID, '') = ''";
            else if($suite_id != null)
                $where[] = ManageESB::$esbdb->prepare(" c.TEST_SUITE_ID=%d", $suite_id);
        }
        
        if($case_id !== "" && $case_id !== null)
            $where[] = ManageESB::$esbdb->prepare(" cm.TEST_CASE_WP_ID=%d", $case_id);
            
        if($date != null)
            $where[] = ManageESB::$esbdb->prepare(" DATE(c.CONVERSATION_TIMESTAMP)=%s", date("Y-m-d", strtotime($date)));
            
        if($service != null){
            $where[] = ManageESB::$esbdb->prepare(" m.SERVICE=%s", $service);
        }
            
        if($action != null){
            $where[] = ManageESB::$esbdb->prepare(" m.ACTION=%s", $action);
        }
        
        if($where)
            $query .= " WHERE " . implode(" AND ", $where);
        
        $rows = ManageESB::$esbdb->get_results($query);
        $results = array();
        foreach($rows as $row)
        {
            if($row->FROM_PARTY_ID)
                $results[] = $row->FROM_PARTY_ID;    
            if($row->TO_PARTY_ID)
                $results[] = $row->TO_PARTY_ID;    
        }
        $results = array_unique($results);
        
        return $results;
        
    }
    
    
    
    public function getTestOutcomeStatus($id, $outcomeType)
    {        
        $rows = ManageESB::$esbdb->get_col("SELECT `STATUS` FROM " . $this->table_message_validation_results . " WHERE MSH_METADATA_ID = " . $id);
        
        $status = null;
        if($rows)
        {
            $status = true;
            foreach($rows as $r)
            {
                if($r != 'Passed')
                    $status = false;
            }
        }
        
        if($outcomeType == 'Positive')
        {
            if($status === false)
                return 'FAILURE';                
            else
                return 'SUCCESS';
        }else if($outcomeType == 'Negative'){
            if($status === false)
                return 'SUCCESS';
            else
                return 'FAILURE';
            
        }
        
        
    }
    
    
    
    public function  getMessageEnvelope($id, $user_id = null)
    {
        global $wpdb;
        
        if($user_id == null)
            $user_id = get_current_user_id();
        
        if(!$user_id)
            return null;
        
        //Getting User Customer IDs
        /*$query = $wpdb->prepare("SELECT esb_user_id FROM " . $wpdb->prefix . "users_purchases WHERE user_id=%d AND `status`='Active'", $user_id);
        $esbIDs = $wpdb->get_col($query);*/
        $esbIDs = getUserAllCustomerESBIDs($user_id);
        
        if(!$esbIDs)
            return array();
        
        $query = "SELECT m.PAYLOAD FROM " . $this->table_message_metadata . " AS m, " . $this->table_conversation_metadata . " AS c WHERE m.MSH_CONVERSATION_ID=c.ID AND m.ID=" . intval($id) . " AND c.CUSTOMER_ID in (" . implode(",", $esbIDs) . ")";                
        
        $data = ManageESB::$esbdb->get_var($query);
        
        return $data;
        
    }
    
    /**
    * Get Validation Rows
    * 
    * @param Int $id:Message Metadata ID
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
        /*$query = $wpdb->prepare("SELECT esb_user_id FROM " . $wpdb->prefix . "users_purchases WHERE user_id=%d AND `status`='Active'", $user_id);
        $esbIDs = $wpdb->get_col($query);*/
        $esbIDs = getUserAllCustomerESBIDs($user_id);
        if(!$esbIDs)
            return null;
        
        $query = "SELECT mv.*, mvp.PHASE_CODE, mvp.PHASE_LABEL, mvs.STATUS_CODE, mvs.STATUS_LABEL " . 
                 "FROM " . $this->table_message_metadata . " AS m, " . $this->table_conversation_metadata . " AS c, " . $this->table_message_validation_results . " AS mv " .
                 "LEFT JOIN " . $this->table_message_validation_phases . " AS mvp ON mvp.ID = mv.MSH_MESSAGE_VALIDATION_PHASES_ID " .
                 "LEFT JOIN " . $this->table_message_validation_statuses . " AS mvs ON mvs.ID = mv.MSH_MESSAGE_VALIDATION_STATUSES_ID " .
                 "WHERE m.ID=" . intval($id) . " AND m.MSH_CONVERSATION_ID=c.ID AND m.ID=mv.MSH_MESSAGE_METADATA_ID AND c.CUSTOMER_ID in (" . implode(", ", $esbIDs) . ") ORDER BY mvp.ID";
        
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
        /*$query = $wpdb->prepare("SELECT esb_user_id FROM " . $wpdb->prefix . "users_purchases WHERE user_id=%d AND `status`='Active'", $user_id);
        $esbIDs = $wpdb->get_col($query);*/
        $esbIDs = getUserAllCustomerESBIDs($user_id);
        
        if(!$esbIDs)
            return null;
        
        $query = "SELECT mv.HARNESS_VALIDATION_ERROR " . 
                 "FROM " . $this->table_message_metadata . " AS m, " . $this->table_conversation_metadata . " AS c, " . $this->table_message_validation_results . " AS mv " .
                 "WHERE mv.ID=" . intval($id) . " AND m.MSH_CONVERSATION_ID=c.ID AND m.ID=mv.MSH_MESSAGE_METADATA_ID AND c.CUSTOMER_ID in (" . implode(", ", $esbIDs) . ")";
        
        $data = ManageESB::$esbdb->get_var($query);
        
        return $data;
    }
    
    
    public function updateTestSuiteID($id, $suiteID)
    {
        ManageESB::$esbdb->update($this->table_conversation_metadata, array('TEST_SUITE_ID'=>$suiteID), array('ID' => $id));
    }
    
    public function updateTestOutcome($id, $outcome)
    {
        ManageESB::$esbdb->update($this->table_conversation_metadata, array('TEST_OUTCOME'=>$suiteID), array('ID' => $id));
    }
    
    
    public function saveTestCaseInfo($id, $name, $outcome_type, $pattern)
    {
        //Check if the id already exists on the ESB DB
        $esb_id = ManageESB::$esbdb->get_var("SELECT ID FROM " . $this->table_test_case_configuration . " WHERE TEST_CASE_WP_ID=" . $id . " OR TEST_CASE_ID='" .  $name . "'");
        if(!$esb_id) //New
        {
            $result = ManageESB::$esbdb->insert($this->table_test_case_configuration, 
                array('TEST_CASE_WP_ID' => $id, 'TEST_CASE_ID' => $name, 'TEST_OUTCOME_TYPE' => strtoupper($outcome_type), 'TEST_CASE_PATTERN_ID' => $pattern)
            );
        
        }else{
            ManageESB::$esbdb->update($this->table_test_case_configuration, array('TEST_CASE_WP_ID' => $id, 'TEST_CASE_ID' => $name, 'TEST_OUTCOME_TYPE' => strtoupper($outcome_type), 'TEST_CASE_PATTERN_ID' => $pattern), array('ID' => $esb_id));    
        }
        
        return $result;        
    }
    
    public function deleteTestCaseNameIDMap($id)
    {
        $result = ManageESB::$esbdb->delete($this->table_test_case_configuration, array('ID' => $id));
        
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
    
    public function addProductNameIDMap($id, $name)
    {
        //Delete Old Data
        $this->deleteProductNameIDMap($id);
        
        $result = ManageESB::$esbdb->insert($this->table_product_name_id_map, array('ID' => $id, 'NAME' => $name));
        
        return $result;
    }
    
    public function deleteProductNameIDMap($id)
    {
        
        $result = ManageESB::$esbdb->delete($this->table_product_name_id_map, array('ID' => $id));
        
        return $result;
    }
    
    public function getTestCaseConfigurationID($case_id)
    {
        $query = "SELECT ID FROM " . $this->table_test_case_configuration . " WHERE TEST_CASE_WP_ID=" . $case_id;
        $id = ManageESB::$esbdb->get_var($query);
        
        return $id;
    }
    
    
}