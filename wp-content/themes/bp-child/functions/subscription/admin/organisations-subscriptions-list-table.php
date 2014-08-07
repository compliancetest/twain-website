<?php

/**
* User Subscription List Table
*/

require_once(ABSPATH . "/wp-admin/includes/class-wp-list-table.php");

class CT_Organisation_Subscriptions_List_Table extends WP_List_Table
{
    var $per_pages = 20;
    
    function __construct() {
        parent::__construct( array(
            'singular'=> strtolower(get_class($this)), //Singular label
            'plural' => strtolower(get_class($this)), //plural label, also this well be one of the table css class
            'ajax'  => true //We won't support Ajax for this table
        ) );
    }
    
    function get_columns()
    {
        return $column = array(
            "organisation_name" => __("Organisation Name"),
            "suite_title" => __('Test Suite'),
            "nickname" => __('Nickname'),
            "purchased_date" => __("Created Date"),
            "status" => __("Status"),                                    
            "payment_method" => __("Payment Method"),                        
            "user_id" => __("Assignee"),            
            "id" => __("ID"),  
        );
    }
    
    function get_sortable_columns($orderby)
    {
        return $sortable = array(
            "organisation_name" => array("organisation_name", $orderby == 'organisation_name'),
            "suite_title" => array("suite_family_mark", $orderby == 'suite_family_mark'),
            "nickname" => array("nickname", $orderby == 'nickname'),
            "purchased_date" => array("purchased_date", $orderby == 'purchased_date'),
            "status" => array("status", $orderby == 'status'),
            "payment_method" => array("payment_method", $orderby == 'payment_method'),
            "user_id" => array("user_id", $orderby == 'user_id'),
            "id" => array("ID", $orderby == 'ID')
        );
    }
    
    function column_cb($item){
        //return sprintf(
//            '<input type="checkbox" name="id[]" value="%1$s" />',
//            $item->id
//        );
    }
    
    function extra_tablenav($which)
    {
          if($which == "top")
          {
              global $wpdb;
              
              //Getting Organisations
              $query = "SELECT organisation_name, id from {$wpdb->prefix}organisations ORDER BY organisation_name";
              $organisations = $wpdb->get_results($query);
              
              $query = "SELECT distinct(family_mark), suite_title from {$wpdb->prefix}test_suites ORDER BY suite_title";
              $test_suites = $wpdb->get_results($query);
              
              $statuses = array('Active', 'InArrears', 'Fronzen', 'Unsubscribing');
              ?>
              <div style="float: left;">              
                  <label style="margin-right: 10px">
                      Organisations 
                      <select name="filter_organisation">
                          <option value="" <?php echo !$_REQUEST['filter_organisation']? 'selected="selected"' : '' ?>>All</option>
                          <?php foreach($organisations as $org): ?>
                          <option value="<?php echo $org->id?>" <?php echo $_REQUEST['filter_organisation'] == $org->id ? 'selected="selected"' : '' ?>><?php echo $org->organisation_name?></option>
                          <?php endforeach; ?>
                      </select>
                  </label>
                  
                  <label style="margin-right: 10px">
                      Test Suites 
                      <select name="filter_suite">
                          <option value="" <?php echo !$_REQUEST['filter_suite']? 'selected="selected"' : '' ?>>All</option>
                          <?php foreach($test_suites as $ts): ?>
                          <option value="<?php echo $ts->family_mark?>" <?php echo $_REQUEST['filter_suite'] == $ts->family_mark ? 'selected="selected"' : '' ?>><?php echo $ts->suite_title?></option>
                          <?php endforeach; ?>
                      </select>
                  </label>
                  
                  <label style="margin-right: 10px">
                      Status 
                      <select name="filter_status">
                          <option value="" <?php echo !$_REQUEST['filter_status']? 'selected="selected"' : '' ?>>All</option>
                          <?php foreach($statuses as $s): ?>
                          <option value="<?php echo $s?>" <?php echo $_REQUEST['filter_status'] == $s ? 'selected="selected"' : '' ?>><?php echo $s?></option>
                          <?php endforeach; ?>
                      </select>
                  </label>
                  
                  <input type="submit" value="Search" class="button-primary" >
              </div>              
              <?php
          }
    }
      
    function column_default($item, $column_name)
    {
        global $wpdb;
        
        switch($column_name)
        {
            case 'organisation_name':
                return $item->$column_name . $this->row_actions(array(
                    "<a href='admin.php?page=add-organisation-subscription&id=" . $item->id . "'>Edit</a>",
                    "<a href='admin.php?subscription_admin_action="  . wp_create_nonce('delete-organisation-subscription') ."&id=" . $item->id . "'>Delete</a>",
                ));
            case 'user_id': 
                return !$item->user_id ? "-" : get_user_meta($item->user_id, 'first_name', true) . " " . get_user_meta($item->user_id, 'last_name', true);
            case 'payment_method': 
                return $item->payment_method_name . "(" . (!$item->invoice_me ? $item->card_number : 'Invoice') . ")" ;
            default:
                return $item->$column_name;
        }
    }
    
    function prepare_items()
    {
        global $wpdb;
        
        $paged = $this->get_pagenum();
        
        $orderby = isset($_REQUEST['orderby']) ? $_REQUEST['orderby'] : 'organisation_name';
        $order = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'asc';

        // Query the user IDs for this page
        $query = "SELECT count(*) FROM {$wpdb->prefix}organisations_subscriptions";
        $totalItems = $wpdb->get_var($query);
        
        $totalPages = ceil($totalItems / $this->per_pages);
        if($totalPages < $paged)
            $paged = $totalPages;
        
        $this->set_pagination_args(array(
            "total_items"=>$totalItems,
            "total_pages"=>$totalPages,
            "per_page"=>$this->per_pages
        ));
      
        $query = "SELECT distinct(os.id), os.nickname, os.organisation_id, os.purchaser_id, os.purchased_date, os.status, os.suite_family_mark, os.payment_method, os.user_id, o.organisation_name, o.organisation_domain, t.suite_title, p.nickname AS payment_method_name, p.invoice_me, p.card_number FROM {$wpdb->prefix}organisations_subscriptions as os
                  LEFT JOIN {$wpdb->prefix}organisations AS o ON os.organisation_id=o.id                  
                  LEFT JOIN {$wpdb->prefix}test_suites AS t ON t.family_mark=os.suite_family_mark
                  LEFT JOIN {$wpdb->prefix}organisations_payment_methods AS p ON p.id=os.payment_method
                  WHERE 1 ";
                        
        if($_REQUEST['filter_organisation'])
            $query .= $wpdb->prepare(" AND os.organisation_id=%d", $_REQUEST['filter_organisation']);
        if($_REQUEST['filter_suite'])
            $query .= $wpdb->prepare(" AND os.suite_family_mark=%d", $_REQUEST['filter_suite']);
        if($_REQUEST['filter_status'])
            $query .= $wpdb->prepare(" AND os.status=%s", $_REQUEST['filter_status']);
        
        $query .= " ORDER BY $orderby $order ";
        $query .= " LIMIT " . ($paged-1) * $this->per_pages .  ", {$this->per_pages} ";
        
        $this->items = $wpdb->get_results($query);
      
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns($orderby);          
        $this->_column_headers = array($columns, $hidden, $sortable);
        
    }
}