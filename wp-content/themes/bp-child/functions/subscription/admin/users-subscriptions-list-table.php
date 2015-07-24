<?php

/**
* User Subscription List Table
*/

require_once(ABSPATH . "/wp-admin/includes/class-wp-list-table.php");

class CT_User_Subscriptions_List_Table extends WP_List_Table
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
            "organisation_name" => __("Organisation"),
            "suite_title" => __('Test Suite'),
            "subscription_name" => __('Nickname'),
            "user_id" => __("User"),
            "status" => __("Status"),
            "created_date" => __("Subscribed Date"),
            "id" => __("ID")  
        );
    }
    
    function get_sortable_columns($orderby)
    {
        return $sortable = array(
            "organisation_name" => array("organisation_name", $orderby == 'organisation_name'),
            "suite_title" => array("suite_title", $orderby == 'suite_title'),
            "subscription_name" => array("os.nickname", $orderby == 'os.nickname'),
            "created_date" => array("us.created_date", $orderby == 'us.created_date'),
            "status" => array("os.status", $orderby == 'os.status'),            
            "user_id" => array("us.user_id", $orderby == 'us.user_id'),
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
                    "<a href='admin.php?page=add-user-subscription&id=" . $item->id . "'>Edit</a>",
                    "<a href='admin.php?subscription_admin_action="  . wp_create_nonce('delete-user-subscription') ."&id=" . $item->id . "' onclick='return confirm(\"Are you sure you want to delete this subscription?\")'>Delete</a>",
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
      
        $query = "SELECT 
                        us.id,
                        us.organisation_id, 
                        us.user_id, 
                        us.suite_id, 
                        us.created_date, 
                        os.nickname as subscription_name, 
                        os.status, 
                        p.post_title AS suite_title,
                        o.organisation_name
                  FROM {$wpdb->prefix}users_subscriptions as us
                  LEFT JOIN {$wpdb->posts} AS p ON p.ID=us.suite_id
                  LEFT JOIN {$wpdb->prefix}organisations_subscriptions as os ON os.id=us.parent_id             
                  LEFT JOIN {$wpdb->prefix}organisations AS o ON os.organisation_id=o.id             
                  LEFT JOIN {$wpdb->prefix}organisations_payment_methods AS pm ON pm.id=os.payment_method                  
                  WHERE 1 ";
                        
        if($_REQUEST['filter_organisation'])
            $query .= $wpdb->prepare(" AND os.organisation_id=%d", $_REQUEST['filter_organisation']);
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