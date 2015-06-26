<?php
/**
* Manage Backend Data
*/

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
    var $table_test_suite_configuration = 'TEST_SUITE_CONFIGURATION';
    var $table_product_configuration = 'PRODUCT_CONFIGURATION';

    var $table_schedules_runs = 'MSH_SCHEDULE_RUNS';

    public $table_tags_to_conversations = 'MSH_TAG_TO_CONVERSATION';
    public $table_tags = 'MSH_TAGS';

    public static $esbdb = null;
    
    public function __construct()
    {
        if(ManageESB::$esbdb == null)
            $this->loadDatabase();
        
    }
    
    public function  loadDatabase()
    {
        $esb_db_username = get_option('esb_username');
        $esb_db_password = get_option('esb_password');
        $esb_db_database = get_option('esb_database');
        $esb_db_host = get_option('esb_host');
        
        $db = new wpdb($esb_db_username, $esb_db_password, $esb_db_database, $esb_db_host);
        ManageESB::$esbdb = $db;
        
        return $db;
    }
    
    public function getCaseStatus($customer_id, $suite_id = null, $product_id = null, $case_id = null)
    {
        /*if($suite_id != null)
            $this->addTestSuiteIDToLog($suite_id);*/
        
        $query = "SELECT s.TEST_SUITE_WP_ID, p.PRODUCT_WP_ID, ts.*, c.TEST_CASE_ID, c.TEST_CASE_WP_ID as TEST_CASE_DB_ID FROM " . $this->table_conversation_metadata . " AS m " .
                                            "LEFT JOIN " . $this->table_test_outcome_status . " AS ts ON ts.ID=m.MSH_TEST_OUTCOME_STATUS_ID " .
                                            "LEFT JOIN " . $this->table_product_configuration . " AS p ON p.PRODUCT_ID=m.PRODUCT_ID " .
                                            "LEFT JOIN " . $this->table_test_suite_configuration . " AS s ON s.ID=m.TEST_SUITE_CONFIGURATION_ID " .
                                            "LEFT JOIN " . $this->table_test_case_configuration . " AS c ON c.ID=m.TEST_CASE_CONFIGURATION_ID WHERE m.AUDIT_RECORD=1";
        
        if (is_array($customer_id)) {
            $customer_id = ManageESB::$esbdb->escape($customer_id);            
            $query .= " AND m.ORGANISATION_SUBSCRIPTION_ID IN (" . implode(", ", $customer_id) . ")";
        } else {
            $query .= ManageESB::$esbdb->prepare(" AND m.ORGANISATION_SUBSCRIPTION_ID=%d", $customer_id);
        }
        
        
        if($suite_id != null)
            $query .= ManageESB::$esbdb->prepare(" AND s.TEST_SUITE_WP_ID=%d", $suite_id);
        
        if($product_id != null)
            $query .= ManageESB::$esbdb->prepare(" AND p.PRODUCT_WP_ID=%d", $product_id);
        
        if($case_id != null)
            $query .= ManageESB::$esbdb->prepare(" AND c.TEST_CASE_DB_ID=%d", $case_id);
        
        $rows = ManageESB::$esbdb->get_results($query);
    
        if(!$rows)
            return array();
    
        $result = array();
        
        foreach($rows as $row)
        {
            if(!isset($result[$row->TEST_SUITE_WP_ID]))
                $result[$row->TEST_SUITE_WP_ID] = array();
            
            if(!isset($result[$row->TEST_SUITE_WP_ID][$row->PRODUCT_WP_ID]))
                $result[$row->TEST_SUITE_WP_ID][$row->PRODUCT_WP_ID] = array();
            
            if(!$row->TEST_OUTCOME_CODE)
                continue;
//                $row->TEST_OUTCOME = $this->getTestOutcomeStatus($row->TEST_CASE_DB_ID, get_post_meta($row->TEST_CASE_DB_ID, 'outcome_type', true));
            
            if($row->TEST_OUTCOME_CODE == 'PASS')
                $result[$row->TEST_SUITE_WP_ID][$row->PRODUCT_WP_ID][$row->TEST_CASE_DB_ID] = 'pass';
            else if($row->TEST_OUTCOME_CODE == 'FAIL')
                $result[$row->TEST_SUITE_WP_ID][$row->PRODUCT_WP_ID][$row->TEST_CASE_DB_ID] = 'fail';
        }
        
        return $result;
    }
    
/*    public function addTestSuiteIDToLog($suite_id)
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
        
    }*/
    
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
            $esbIDs = $this->_getCustomerESBIds($user_id, $customer_id);
            
            if(!$esbIDs)
                return array();
            
            $customer_id = " AND c.ORGANISATION_SUBSCRIPTION_ID in (" . implode(",", $esbIDs) . ")";
        }        
        
        $query = "SELECT c.*, tc.TEST_CASE_WP_ID AS TEST_CASE_DB_ID, s.TEST_SUITE_TITLE, s.TEST_SUITE_WP_ID, ts.TEST_OUTCOME_CODE, ts.TEST_OUTCOME_LABEL, p.PRODUCT_WP_ID FROM " . $this->table_conversation_metadata . " AS c " .
                 "LEFT JOIN " . $this->table_test_case_configuration . " AS tc ON tc.ID=c.TEST_CASE_CONFIGURATION_ID " .
                 "LEFT JOIN " . $this->table_test_suite_configuration . " AS s ON s.ID=c.TEST_SUITE_CONFIGURATION_ID " .
                 "LEFT JOIN " . $this->table_product_configuration . " AS p ON p.PRODUCT_ID=c.PRODUCT_ID " .
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
    public function _getCustomerESBIds($user_id = null, $customer_id = null)
    {
        global $wpdb;
        
        if($user_id == null)
            $user_id = get_current_user_id();
        
        $query = "SELECT distinct(parent_id) FROM " . $wpdb->prefix . "users_subscriptions WHERE `status`='Active'";
        
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

    public function prepareTransactionWhereQuery($organisation_id = null, $subscription_id = null, $product_id = null, $suite_id = null, $case_id = null, $service = null, $action = null, $partyid = null, $date = null, $tag = null)
    {
        global $wpdb;
        
        $user_id = get_current_user_id();
        
        $where = array();
        $message_where = array();
        
        if ($subscription_id == 'all') { //All Subscriptions
            //Getting Manageable Users' Subscriptions
//                $query = $wpdb->prepare("SELECT DISTINCT(s.id) FROM {$wpdb->prefix}users_subscriptions AS s, {$wpdb->prefix}bp_groups_members AS bm
//                        WHERE
//                            s.user_id = bm.user_id AND bm.is_confirmed=1
//                            AND
//                            (bm.user_id=%d OR bm.group_id
//                                IN
//                                ( SELECT group_id FROM {$wpdb->prefix}bp_groups_members WHERE user_id=%d AND (is_mod = 1 OR is_admin = 1)))
//                        ", $user_id, $user_id);
            if( is_super_admin() ) {
                $query = " SELECT id FROM {$wpdb->prefix}organisations_subscriptions ";
            } elseif( ct_is_group_admin_or_support() ){
                $query = '';
                $orgs = ct_get_user_viewable_organisations();
                $org_ids = array();
                foreach( $orgs AS $org ){
                    $org_ids[] = $org->id;
                }
                if( $org_ids ){
                    $query = "SELECT DISTINCT(id) FROM {$wpdb->prefix}organisations_subscriptions WHERE organisation_id IN( ".implode(',', $org_ids)." ) ";
                }
            } else {
                $query = $wpdb->prepare("SELECT id FROM {$wpdb->prefix}organisations_subscriptions WHERE organisation_id IN( SELECT DISTINCT(organisation_id) FROM {$wpdb->prefix}organisations_subscriptions WHERE user_id = %d ) ", $user_id );
            }
            if ($organisation_id !== null && $organisation_id != "all") {
                if( is_super_admin() ) {
                    $query .= $wpdb->prepare(" WHERE organisation_id=%d", $organisation_id);
                } elseif( ct_is_group_admin_or_support() ){
                    if( $org_ids ){
                        $query .= $wpdb->prepare(" AND organisation_id=%d", $organisation_id);
                    }
                } else{
                    $query .= $wpdb->prepare(" AND organisation_id=%d", $organisation_id);

                }
            }

            $s_ids = $wpdb->get_col($query);
            if (!$s_ids){
                $where['subscription'] = " c.ORGANISATION_SUBSCRIPTION_ID = false ";
            } else {
                $where['subscription'] = " c.ORGANISATION_SUBSCRIPTION_ID IN (" . implode(", ", $s_ids) . ")";
            }

        } else if( $subscription_id == 'my' ){
            //Getting Manageable Users' Subscriptions
            $query = $wpdb->prepare("SELECT id FROM {$wpdb->prefix}organisations_subscriptions AS s WHERE user_id = %d", $user_id );
            if ($organisation_id !== null && $organisation_id != "all") {
                $query .= $wpdb->prepare(" AND s.organisation_id=%d", $organisation_id);
            }
            $s_ids = $wpdb->get_col($query);

            if (!$s_ids){
                $where['subscription'] = " c.ORGANISATION_SUBSCRIPTION_ID = false ";
            } else {
                $where['subscription'] = " c.ORGANISATION_SUBSCRIPTION_ID IN (" . implode(", ", $s_ids) . ")";
            }
        }else {
            $where['subscription'] = $wpdb->prepare(" c.ORGANISATION_SUBSCRIPTION_ID=%d", $subscription_id);

        }

        if ($product_id !== null && $product_id != "") {
            if($product_id == 0)
                $where['product'] = " IFNULL(p.PRODUCT_WP_ID, 0) = 0";
            else if($product_id != null)
                $where['product'] = $wpdb->prepare(" p.PRODUCT_WP_ID=%d", $product_id);    
        }
        
        if ($suite_id !== null && $suite_id != "") {
            if($suite_id == 0)
                $where['suite'] = " IFNULL(s.TEST_SUITE_WP_ID, '') = ''";
            else if($suite_id != null)
                $where['suite'] = $wpdb->prepare(" s.TEST_SUITE_WP_ID=%d", $suite_id);
        }
            
        if ($case_id !== "" && $case_id !== null) {
            $where['case'] = $wpdb->prepare(" cm.TEST_CASE_WP_ID=%d", $case_id);    
        }
            
        if($date != null)
            $where['date'] = $wpdb->prepare(" c.CONVERSATION_TIMESTAMP BETWEEN %s AND %s" , date( "Y-m-d H:i:s", getUTCTimeStamp( date("Y-m-d H:i:s", strtotime($date.' 00:00:00'))) ), date( "Y-m-d H:i:s", getUTCTimeStamp( date("Y-m-d H:i:s", strtotime($date.' 23:59:59')) )) );

        if($service != null)
        {
            $where['service'] = $wpdb->prepare(" m.SERVICE=%s", $service);
            $message_where['service'] = $wpdb->prepare(" m.SERVICE=%s", $service);            
        }
            
        if($action != null){
            $where['action'] = $wpdb->prepare(" m.ACTION=%s", $action);
            $message_where['action'] = $wpdb->prepare(" m.ACTION=%s", $action);
        }
            
        if($partyid != null){
            $where['party_id'] = $wpdb->prepare(" (m.FROM_PARTY_ID=%s OR m.TO_PARTY_ID=%s)", $partyid, $partyid);
            $message_where['party_id'] = $wpdb->prepare(" (m.FROM_PARTY_ID=%s OR m.TO_PARTY_ID=%s)", $partyid, $partyid);
        }
        if($tag != null){
            $where['tag'] = $wpdb->prepare(" t.ID = %d ", $tag);
            $message_where['tag'] = $wpdb->prepare(" t.ID = %d ", $tag);
        }
        $this->message_where = $message_where;
        $this->where_query = $where;
    }
    
    public function  getUserTransactionLog($page = 1, $limit = -1, $orderby = null, $order = 'asc')
    {
        global $wpdb;
        
        $user_id = get_current_user_id();
        
        if(!$user_id)
            return array();
        
        
        $where = array();
        
        
        $orderQuery = "";
        if($orderby)
        {
            switch($orderby)
            {
                case 'product':
                    $orderQuery .= ' ORDER BY PRODUCT_TITLE ' . $order . ', c.PRODUCT_ID ' . $order;
                    break;
                case 'case':
                    $orderQuery .= ' ORDER BY TEST_CASE_ID ' . $order;
                    break;
                case 'suite':
                    $orderQuery .= ' ORDER BY TEST_SUITE_TITLE ' . $order;
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
        
        $table_query = " FROM " . $this->table_conversation_metadata . " AS c " .
                     "LEFT JOIN " . $this->table_test_case_configuration . " AS cm ON cm.ID=c.TEST_CASE_CONFIGURATION_ID " .
                     "LEFT JOIN " . $this->table_test_suite_configuration . " AS s ON s.ID=c.TEST_SUITE_CONFIGURATION_ID " .
                     "LEFT JOIN " . $this->table_tags_to_conversations . " AS ttc ON ttc.MSH_CONVERSATION_ID = c.ID " .
                     "LEFT JOIN " . $this->table_tags . " AS t ON t.ID = ttc.MSH_TAG_ID " .
                     "LEFT JOIN " . $this->table_product_configuration . " AS p ON p.PRODUCT_ID=c.PRODUCT_ID " .
                     "LEFT JOIN " . $this->table_test_outcome_status . " AS ts ON ts.ID=c.MSH_TEST_OUTCOME_STATUS_ID ";
        if ($this->message_where)
            $table_query .= "LEFT JOIN " . $this->table_message_metadata . " AS m ON m.MSH_CONVERSATION_ID=c.ID ";
        
        if($limit > 0)
        {            
            
            //Getting Total Numbers
            $query = "SELECT count(DISTINCT(c.ID)) ";            
            $query .= $table_query;            
            if($this->where_query)
                $query .= " WHERE " . implode(" AND ", $this->where_query);            
                
            $totalItems = ManageESB::$esbdb->get_var($query);
            
            $query = "SELECT 
                        DISTINCT(c.ID), 
                        c.*,
                        cm.TEST_CASE_WP_ID, 
                        cm.TEST_CASE_ID, 
                        p.PRODUCT_WP_ID,
                        p.PRODUCT_TITLE,
                        s.TEST_SUITE_TITLE,
                        s.TEST_SUITE_WP_ID,
                        ts.TEST_OUTCOME_CODE, 
                        ts.TEST_OUTCOME_LABEL ";
            $query .= $table_query;
            
            if($this->where_query)
                $query .= " WHERE " . implode(" AND ", $this->where_query);
            
            $query .= $orderQuery . " LIMIT " . ($page -1 ) * $limit . ", " . $limit;
            
        }else{
            $query = "SELECT 
                        DISTINCT(c.ID), 
                        c.*,
                        cm.TEST_CASE_ID, 
                        cm.TEST_CASE_WP_ID, 
                        p.PRODUCT_WP_ID,
                        p.PRODUCT_TITLE,
                        s.TEST_SUITE_TITLE,
                        s.TEST_SUITE_WP_ID,
                        ts.TEST_OUTCOME_CODE, 
                        ts.TEST_OUTCOME_LABEL ";
            $query .= $table_query;
            
            if($this->where_query)
                $query .= " WHERE " . implode(" AND ", $this->where_query);
            $query .= $orderQuery;
        }
        $rows = ManageESB::$esbdb->get_results($query);

        //Getting Messages
        $ids = array();
        if( is_iterable( $rows) ){
            foreach($rows as $row){
                $ids[] = $row->ID;
            }
        }
        $messages = array();
        
        if($ids)
        {
            $query = "SELECT
                        DISTINCT(m.ID),
                        m.ID, m.MSH_CONVERSATION_ID, m.MESSAGE_TIMESTAMP, m.S3_PAYLOAD_LOCATION, m.S3_PAYLOAD_CONTENT_LENGTH, m.FROM_PARTY_ID, m.TO_PARTY_ID, m.SERVICE, m.ACTION, m.MSH_MESSAGE_OUTCOME_STATUS_ID, m.MESSAGE_ID,
                        m.ORIGINAL_MESSAGE_ID, m.REF_TO_MESSAGE_ID, m.PART_ID, m.ATTACHMENT_ID, m.UPLOAD_ID, m.CT_RECEIPT_MESSAGE_ID, m.GATEWAY_RECEIPT_MESSAGE_ID, m.FLAG, m.S3_ENVELOPE,
                        ms.MESSAGE_OUTCOME_CODE,
                        ms.MESSAGE_OUTCOME_LABEL
                      FROM " . $this->table_message_metadata . " AS m " .
                     "LEFT JOIN " . $this->table_message_outcome_status . " AS ms ON ms.ID=m.MSH_MESSAGE_OUTCOME_STATUS_ID " .
                     "LEFT JOIN " . $this->table_tags_to_conversations . " AS ttc ON ttc.MSH_CONVERSATION_ID = m.MSH_CONVERSATION_ID " .
                     "LEFT JOIN " . $this->table_tags . " AS t ON t.ID = ttc.MSH_TAG_ID " .
                     "LEFT JOIN " . $this->table_message_validation_results . " AS mv ON mv.MSH_MESSAGE_METADATA_ID=m.ID " .
                     "WHERE m.MSH_CONVERSATION_ID in (" . implode(", ", $ids) . ") " . (!$this->message_where ? "" : " AND " . implode(" AND ", $this->message_where)) .  " ORDER BY m.MESSAGE_TIMESTAMP";

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
    public function getFilterOptionsForProduct()
    {
        global $wpdb;
        
        $user_id = get_current_user_id();
        
        $query = "SELECT 
                    DISTINCT(p.ID), p.PRODUCT_WP_ID, p.PRODUCT_TITLE, p.PRODUCT_ID
                  FROM " . $this->table_conversation_metadata . " AS c
                  LEFT JOIN " . $this->table_tags_to_conversations . " AS ttc ON ttc.MSH_CONVERSATION_ID = c.ID
                  LEFT JOIN " . $this->table_tags . " AS t ON t.ID = ttc.MSH_TAG_ID
                  LEFT JOIN " . $this->table_test_suite_configuration . " AS s ON c.TEST_SUITE_CONFIGURATION_ID=s.ID 
                  LEFT JOIN " . $this->table_test_case_configuration . " AS cm ON c.TEST_CASE_CONFIGURATION_ID=cm.ID 
                  LEFT JOIN " . $this->table_product_configuration . " AS p ON c.PRODUCT_ID=p.PRODUCT_ID ";
                  
        if ($this->message_where)
            $query .= "LEFT JOIN " . $this->table_message_metadata . " AS m ON m.MSH_CONVERSATION_ID=c.ID ";
        
        $where = $this->where_query;
        
        //Remove Product Query From the where
        if (isset($where['product'])) {
            $where['product'] = null;
            unset($where['product']);            
        }
        
        
        if($where)
            $query .= " WHERE " . implode(" AND ", $where);
        
        $query .= ' ORDER BY p.PRODUCT_TITLE ';
        
        $results = ManageESB::$esbdb->get_results($query);
        
        return $results;
        
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
    public function getFilterOptionsForSuite()
    {
        global $wpdb;
        
        $user_id = get_current_user_id();
        
        $where = array();
        
        $query = "SELECT 
                    DISTINCT(IFNULL(c.TEST_SUITE_CONFIGURATION_ID, 0)) as TC_ID, s.TEST_SUITE_TITLE AS NAME, s.TEST_SUITE_WP_ID as ID
                  FROM " . $this->table_conversation_metadata . " AS c
                  LEFT JOIN " . $this->table_tags_to_conversations . " AS ttc ON ttc.MSH_CONVERSATION_ID = c.ID
                  LEFT JOIN " . $this->table_tags . " AS t ON t.ID = ttc.MSH_TAG_ID
                  LEFT JOIN " . $this->table_product_configuration . " AS p ON p.PRODUCT_ID = c.PRODUCT_ID
                  LEFT JOIN " . $this->table_test_suite_configuration . " AS s ON s.ID = c.TEST_SUITE_CONFIGURATION_ID
                  LEFT JOIN " . $this->table_test_case_configuration . " AS cm ON c.TEST_CASE_CONFIGURATION_ID=cm.ID ";
                
        if ($this->message_where)
            $query .= "LEFT JOIN " . $this->table_message_metadata . " AS m ON m.MSH_CONVERSATION_ID=c.ID ";
        
        $where = $this->where_query;
        
        //Remove Product Query From the where
        if (isset($where['suite'])) {
            $where['suite'] = null;
            unset($where['suite']);            
        }
        
        if($where)
            $query .= " WHERE " . implode(" AND ", $where);
        
        $query .= " ORDER BY NAME";
        
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
    public function getFilterOptionsForCase()
    {
        global $wpdb;
        
        $user_id = get_current_user_id();
        
        $where = array();
        
        $query = "SELECT 
                    DISTINCT(cm.TEST_CASE_ID) as NAME, cm.TEST_CASE_WP_ID as ID
                  FROM " . $this->table_conversation_metadata . " AS c
                  LEFT JOIN " . $this->table_tags_to_conversations . " AS ttc ON ttc.MSH_CONVERSATION_ID = c.ID
                  LEFT JOIN " . $this->table_tags . " AS t ON t.ID = ttc.MSH_TAG_ID
                  LEFT JOIN " . $this->table_product_configuration . " AS p ON p.PRODUCT_ID = c.PRODUCT_ID
                  LEFT JOIN " . $this->table_test_suite_configuration . " AS s ON s.ID = c.TEST_SUITE_CONFIGURATION_ID
                  LEFT JOIN " . $this->table_test_case_configuration . " AS cm ON c.TEST_CASE_CONFIGURATION_ID=cm.ID ";
                
        if ($this->message_where)
            $query .= "LEFT JOIN " . $this->table_message_metadata . " AS m ON m.MSH_CONVERSATION_ID=c.ID ";
        
        $where = $this->where_query;
        
        //Remove Product Query From the where
        if (isset($where['case'])) {
            $where['case'] = null;
            unset($where['case']);            
        }
        
        if($where)
            $query .= " WHERE " . implode(" AND ", $where);
        
        $query .= " ORDER BY NAME ASC";
        
        $rows = ManageESB::$esbdb->get_results($query);
        
        return $rows;
        
    }

    /**
     * Get Tags for Filter Navigation
     *
     * @return array
     */
    public function getFilterOptionsForTags()
    {
        $where = array();

        $query = "SELECT
                    DISTINCT(t.MSH_TAG) as NAME, t.ID
                  FROM " . $this->table_conversation_metadata . " AS c
                  LEFT JOIN " . $this->table_tags_to_conversations . " AS ttc ON ttc.MSH_CONVERSATION_ID = c.ID
                  LEFT JOIN " . $this->table_tags . " AS t ON t.ID = ttc.MSH_TAG_ID
                  LEFT JOIN " . $this->table_product_configuration . " AS p ON p.PRODUCT_ID = c.PRODUCT_ID
                  LEFT JOIN " . $this->table_test_suite_configuration . " AS s ON s.ID = c.TEST_SUITE_CONFIGURATION_ID
                  LEFT JOIN " . $this->table_test_case_configuration . " AS cm ON c.TEST_CASE_CONFIGURATION_ID=cm.ID ";

        if ($this->message_where)
            $query .= "LEFT JOIN " . $this->table_message_metadata . " AS m ON m.MSH_CONVERSATION_ID=c.ID ";

        $where = $this->where_query;

        //Remove Product Query From the where
        if (isset($where['case'])) {
            $where['case'] = null;
            unset($where['case']);
        }

        if($where)
            $query .= " WHERE " . implode(" AND ", $where);

        $query .= " ORDER BY NAME ASC";

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
    public function getFilterOptionsForService()
    {
        global $wpdb;
        
        $user_id = get_current_user_id();
        
        $where = array();
        
        $query = "SELECT 
                    DISTINCT(m.SERVICE)
                  FROM " . $this->table_conversation_metadata . " AS c
                  LEFT JOIN " . $this->table_tags_to_conversations . " AS ttc ON ttc.MSH_CONVERSATION_ID = c.ID
                  LEFT JOIN " . $this->table_tags . " AS t ON t.ID = ttc.MSH_TAG_ID
                  LEFT JOIN " . $this->table_product_configuration . " AS p ON p.PRODUCT_ID = c.PRODUCT_ID
                  LEFT JOIN " . $this->table_test_suite_configuration . " AS s ON s.ID = c.TEST_SUITE_CONFIGURATION_ID
                  LEFT JOIN " . $this->table_test_case_configuration . " AS cm ON c.TEST_CASE_CONFIGURATION_ID=cm.ID ";
                
        $query .= "LEFT JOIN " . $this->table_message_metadata . " AS m ON m.MSH_CONVERSATION_ID=c.ID ";
        
        $where = $this->where_query;
        
        //Remove Product Query From the where
        if (isset($where['service'])) {
            $where['service'] = null;
            unset($where['service']);            
        }
        
        //Remove Empty Fields
        $where[] = " m.SERVICE IS NOT NULL ";
        
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
    public function getFilterOptionsForAction()
    {
        global $wpdb;
        
        $user_id = get_current_user_id();
        
        $where = array();
        
        $query = "SELECT 
                    DISTINCT(m.ACTION)
                  FROM " . $this->table_conversation_metadata . " AS c
                  LEFT JOIN " . $this->table_tags_to_conversations . " AS ttc ON ttc.MSH_CONVERSATION_ID = c.ID
                  LEFT JOIN " . $this->table_tags . " AS t ON t.ID = ttc.MSH_TAG_ID
                  LEFT JOIN " . $this->table_product_configuration . " AS p ON p.PRODUCT_ID = c.PRODUCT_ID
                  LEFT JOIN " . $this->table_test_suite_configuration . " AS s ON s.ID = c.TEST_SUITE_CONFIGURATION_ID
                  LEFT JOIN " . $this->table_test_case_configuration . " AS cm ON c.TEST_CASE_CONFIGURATION_ID=cm.ID ";
                
        $query .= "LEFT JOIN " . $this->table_message_metadata . " AS m ON m.MSH_CONVERSATION_ID=c.ID ";
        
        $where = $this->where_query;
        
        //Remove Product Query From the where
        if (isset($where['action'])) {
            $where['action'] = null;
            unset($where['action']);            
        }
        
        //Remove Empty Fields
        $where['action'] = " ACTION IS NOT NULL ";
        
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
    public function getFilterOptionsForPartId()
    {
        global $wpdb;
        
        $user_id = get_current_user_id();
        
        $where = array();
        
        $query = "SELECT 
                    DISTINCT(m.FROM_PARTY_ID), m.TO_PARTY_ID
                  FROM " . $this->table_conversation_metadata . " AS c
                  LEFT JOIN " . $this->table_tags_to_conversations . " AS ttc ON ttc.MSH_CONVERSATION_ID = c.ID
                  LEFT JOIN " . $this->table_tags . " AS t ON t.ID = ttc.MSH_TAG_ID
                  LEFT JOIN " . $this->table_product_configuration . " AS p ON p.PRODUCT_ID = c.PRODUCT_ID
                  LEFT JOIN " . $this->table_test_suite_configuration . " AS s ON s.ID = c.TEST_SUITE_CONFIGURATION_ID
                  LEFT JOIN " . $this->table_test_case_configuration . " AS cm ON c.TEST_CASE_CONFIGURATION_ID=cm.ID ";
                
        $query .= "LEFT JOIN " . $this->table_message_metadata . " AS m ON m.MSH_CONVERSATION_ID=c.ID ";
        
        $where = $this->where_query;
        
        //Remove Product Query From the where
        if (isset($where['party_id'])) {
            $where['party_id'] = null;
            unset($where['party_id']);            
        }        
        
        if($where)
            $query .= " WHERE " . implode(" AND ", $where);
        
        $rows = ManageESB::$esbdb->get_results($query);
        $results = array();
        if( is_iterable( $rows ) ){
            foreach($rows as $row)
            {
                if($row->FROM_PARTY_ID)
                    $results[] = $row->FROM_PARTY_ID;
                if($row->TO_PARTY_ID)
                    $results[] = $row->TO_PARTY_ID;
            }
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

    public function getFileName( $id ){
        return  ManageESB::$esbdb->get_var( ManageESB::$esbdb->prepare( "SELECT UPLOAD_ID FROM MSH_UPLOAD WHERE ID = %d", $id ) );
    }
    public function  getMessageEnvelope($id, $user_id = null)
    {
        global $wpdb;
        
        if($user_id == null)
            $user_id = get_current_user_id();
        
        if(!$user_id)
            return null;
        
        //Getting User Customer IDs        
        $esbIDs = getUserAllCustomerESBIDs($user_id);
        
        if(!$esbIDs)
            return array();
        
        $query = "SELECT m.PAYLOAD, m.S3_PAYLOAD_LOCATION, m.S3_PAYLOAD_CONTENT_LENGTH FROM " . $this->table_message_metadata . " AS m, " . $this->table_conversation_metadata . " AS c WHERE m.MSH_CONVERSATION_ID=c.ID AND m.ID=" . intval($id) . " AND c.ORGANISATION_SUBSCRIPTION_ID in (" . implode(",", $esbIDs) . ")";                
        
        $data = ManageESB::$esbdb->get_row($query);
        
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
        
        if (is_super_admin()) {
            //Getting User Customer IDs        
            $esbIDs = getUserAllCustomerESBIDs($user_id);        
            if(!$esbIDs)
                return null;

            $query = "SELECT mv.*, mvp.PHASE_CODE, mvp.PHASE_LABEL, mvs.STATUS_CODE, mvs.STATUS_LABEL, mv.FLAG " .
                     "FROM " . $this->table_message_metadata . " AS m, " . $this->table_conversation_metadata . " AS c, " . $this->table_message_validation_results . " AS mv " .
                     "LEFT JOIN " . $this->table_message_validation_phases . " AS mvp ON mvp.ID = mv.MSH_MESSAGE_VALIDATION_PHASES_ID " .
                     "LEFT JOIN " . $this->table_message_validation_statuses . " AS mvs ON mvs.ID = mv.MSH_MESSAGE_VALIDATION_STATUSES_ID " .
                     "WHERE m.ID=" . intval($id) . " AND m.MSH_CONVERSATION_ID=c.ID AND m.ID=mv.MSH_MESSAGE_METADATA_ID ORDER BY mvp.ORDER_ID";
            
        } else {
            //Getting User Customer IDs        
            $esbIDs = getUserAllCustomerESBIDs($user_id);        
            if(!$esbIDs)
                return null;
            
            $query = "SELECT mv.*, mvp.PHASE_CODE, mvp.PHASE_LABEL, mvs.STATUS_CODE, mvs.STATUS_LABEL " . 
                     "FROM " . $this->table_message_metadata . " AS m, " . $this->table_conversation_metadata . " AS c, " . $this->table_message_validation_results . " AS mv " .
                     "LEFT JOIN " . $this->table_message_validation_phases . " AS mvp ON mvp.ID = mv.MSH_MESSAGE_VALIDATION_PHASES_ID " .
                     "LEFT JOIN " . $this->table_message_validation_statuses . " AS mvs ON mvs.ID = mv.MSH_MESSAGE_VALIDATION_STATUSES_ID " .
                     "WHERE m.ID=" . intval($id) . " AND m.MSH_CONVERSATION_ID=c.ID AND m.ID=mv.MSH_MESSAGE_METADATA_ID AND c.ORGANISATION_SUBSCRIPTION_ID in (" . implode(", ", $esbIDs) . ") ORDER BY mvp.ORDER_ID";
                
        }
        
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
        $esbIDs = getUserAllCustomerESBIDs($user_id);
        
        if(!$esbIDs)
            return null;
        
        $query = "SELECT mv.VALIDATION_ERROR, mv.S3_VALIDATION_RESULTS_LOCATION, mv.FLAG " . 
                 "FROM " . $this->table_message_metadata . " AS m, " . $this->table_conversation_metadata . " AS c, " . $this->table_message_validation_results . " AS mv " .
                 "WHERE mv.ID=" . intval($id) . " AND m.MSH_CONVERSATION_ID=c.ID AND m.ID=mv.MSH_MESSAGE_METADATA_ID AND c.ORGANISATION_SUBSCRIPTION_ID in (" . implode(", ", $esbIDs) . ")";
        
        $data = ManageESB::$esbdb->get_row($query);
        
        if($data->FLAG == 'IS_EMPTY' || !$data->S3_VALIDATION_RESULTS_LOCATION)
            return $data->VALIDATION_ERROR;
        
        //Getting XML FROM Amazon S3 URL
        $result = ct_read_xml_from_amazon_s3($data->S3_VALIDATION_RESULTS_LOCATION);
        
        return $result;
    }
    
    
    public function updateTestSuiteID($id, $suiteID)
    {
        //Getting Configuration ID        
        $cID = $this->getTestSuiteConfigurationID($suiteID);
        
        ManageESB::$esbdb->update($this->table_conversation_metadata, array('TEST_SUITE_CONFIGURATION_ID'=>$cID), array('ID' => $id));
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
            ManageESB::$esbdb->insert($this->table_test_case_configuration, 
                array('TEST_CASE_WP_ID' => $id, 'TEST_CASE_ID' => $name, 'TEST_OUTCOME_TYPE' => strtoupper($outcome_type), 'TEST_CASE_PATTERN_ID' => $pattern)
            );
            $result = ManageESB::$esbdb->insert_id;
        
        }else{
            ManageESB::$esbdb->update($this->table_test_case_configuration, array('TEST_CASE_WP_ID' => $id, 'TEST_CASE_ID' => $name, 'TEST_OUTCOME_TYPE' => strtoupper($outcome_type), 'TEST_CASE_PATTERN_ID' => $pattern), array('ID' => $esb_id));    
            
            $result = $esb_id;
        }
        
        return $result;        
    }
    
    public function deleteTestCaseNameIDMap($id)
    {
        $result = ManageESB::$esbdb->delete($this->table_test_case_configuration, array('TEST_CASE_WP_ID' => $id));
        echo ManageESB::$esbdb->last_query . "<br />";
        return $result;        
    }
    
    
    public function saveTestSuiteInfo($id, $suite_id, $suite_title)
    {
        //Check if the id already exists on the ESB DB
        $esb_id = ManageESB::$esbdb->get_var("SELECT ID FROM " . $this->table_test_suite_configuration . " WHERE TEST_SUITE_WP_ID=" . $id . " OR TEST_SUITE_ID='" .  $suite_id . "'");
        if(!$esb_id) //New
        {
            $result = ManageESB::$esbdb->insert($this->table_test_suite_configuration, array('TEST_SUITE_WP_ID' => $id, 'TEST_SUITE_TITLE' => $suite_title, 'TEST_SUITE_ID' => $suite_id));    
        }else{            
            $result = ManageESB::$esbdb->update($this->table_test_suite_configuration, array('TEST_SUITE_WP_ID' => $id, 'TEST_SUITE_TITLE' => $suite_title, 'TEST_SUITE_ID' => $suite_id), array('ID' => $esb_id));
        }
        
        
        return $result;
    }
    
    public function deleteTestSuiteInfo($wp_id)
    {
        
        $result = ManageESB::$esbdb->delete($this->table_test_suite_configuration, array('TEST_SUITE_WP_ID' => $wp_id));
        
        return $result;
    }
    
    public function saveProductInfo($product_wp_id, $product_id, $product_title)
    {
        //Delete Old Data
        $this->deleteProductInfo($product_wp_id);
        
        $result = ManageESB::$esbdb->insert($this->table_product_configuration, array('PRODUCT_WP_ID' => $product_wp_id, 'PRODUCT_ID' => $product_id, 'PRODUCT_TITLE' => $product_title));
        
        return $result;
    }
    
    public function deleteProductInfo($product_wp_id)
    {
        
        $result = ManageESB::$esbdb->delete($this->table_product_configuration, array('PRODUCT_WP_ID' => $product_wp_id));        
        return $result;
    }
    
    public function getTestCaseConfigurationID($case_id)
    {
        $query = "SELECT ID FROM " . $this->table_test_case_configuration . " WHERE TEST_CASE_WP_ID=" . $case_id;
        $id = ManageESB::$esbdb->get_var($query);
        
        return $id;
    }
    
    public function getTestSuiteConfigurationID($suite_id)
    {
        
        $query = ManageESB::$esbdb->prepare("SELECT ID FROM " . $this->table_test_suite_configuration . " WHERE TEST_SUITE_WP_ID=%d", $suite_id);
        $id = ManageESB::$esbdb->get_var($query);
        
        return $id;
    }
    
    public function updateAuditRecordSuiteId($old_id, $new_id)
    {   
        if (!$old_id || !$new_id)
        {
            return;
        }
            
        if(!is_array($old_id)) {
            $old_id = array($old_id);
        }
        
        $query = "SELECT ID FROM " . $this->table_test_suite_configuration . " WHERE TEST_SUITE_WP_ID IN (" . implode(",", $old_id) . ")"  ;
        $old_config_id = ManageESB::$esbdb->get_col($query);
        
        $new_config_id = $this->getTestSuiteConfigurationID($new_id);
        
        if(!$new_config_id || !$old_config_id)
        {
            return;        
        }
           
        $query = "UPDATE " . $this->table_conversation_metadata . " SET TEST_SUITE_CONFIGURATION_ID=" . $new_config_id . " WHERE TEST_SUITE_CONFIGURATION_ID IN (" . implode(
        ",", $old_config_id) . ")";
        
        ManageESB::$esbdb->query($query);
        
        return;
    }
    
    public function getTransactionCountBySuiteId($suite_id)
    {
        $suite_conf_id = $this->getTestSuiteConfigurationID($suite_id);
        
        $query = ManageESB::$esbdb->prepare("SELECT count(ID) FROM " . $this->table_conversation_metadata . " WHERE TEST_SUITE_CONFIGURATION_ID=%d", $suite_conf_id);
        
        $count = ManageESB::$esbdb->get_var($query);
        
        return $count;
    }

    /**
     * @param $user_id
     * @return mixed
     */
    public function getUserSchedules( $user_id )
    {
        global $wpdb;
        $subscription_id  = $wpdb->get_var( $wpdb->prepare("SELECT id FROM wp_organisations_subscriptions WHERE user_id = %d ", $user_id ) );
        $query = ManageESB::$esbdb->prepare( "SELECT SR.*, SS.SCHEDULE_STATUS_CODE, SS.SCHEDULE_STATUS_LABEL FROM " . $this->table_schedules_runs . " AS SR
                                                JOIN MSH_SCHEDULE_STATUSES AS SS ON SS.ID = SR.SCHEDULE_STATUS
                                                WHERE ORGANISATION_SUBSCRIPTION_ID = %d AND IS_DELETED = 0 ", $subscription_id );
        return ManageESB::$esbdb->get_results($query);
    }

    public function updateStatus( $runId, $status, $prevstatus )
    {
        if( $status == 'DELETED' ){
            $query = ManageESB::$esbdb->prepare("UPDATE " . $this->table_schedules_runs . " SET IS_DELETED = 1 WHERE ID = %d ", $runId);
        } else {
            $query = ManageESB::$esbdb->prepare("UPDATE " . $this->table_schedules_runs . " SET SCHEDULE_STATUS = (SELECT ID FROM MSH_SCHEDULE_STATUSES WHERE SCHEDULE_STATUS_CODE = %s ) WHERE ID = %d AND SCHEDULE_STATUS = (SELECT ID FROM MSH_SCHEDULE_STATUSES WHERE SCHEDULE_STATUS_CODE = %s ) ", $status, $runId, $prevstatus );
        }
        ManageESB::$esbdb->query($query);
        return ManageESB::$esbdb->get_row(ManageESB::$esbdb->prepare("SELECT PROFILE_S3_URL FROM " . $this->table_schedules_runs . " WHERE ID = %d ", $runId));
    }

    public function updateStatusByProfileS3Url($token, $status, $prevStatus)
    {
        $query = ManageESB::$esbdb->prepare("UPDATE " . $this->table_schedules_runs . " SET SCHEDULE_STATUS = (SELECT ID FROM MSH_SCHEDULE_STATUSES WHERE SCHEDULE_STATUS_CODE = %s ) WHERE PROFILE_S3_URL LIKE ('%s') AND SCHEDULE_STATUS = (SELECT ID FROM MSH_SCHEDULE_STATUSES WHERE SCHEDULE_STATUS_CODE = %s ) ", $status, '%'.$token.'.json', $prevStatus );
        ManageESB::$esbdb->query($query);
    }
    
    
}