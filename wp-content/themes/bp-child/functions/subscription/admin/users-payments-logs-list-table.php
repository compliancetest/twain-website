<?php

/**
* User Subscription List Table
*/

require_once(ABSPATH . "/wp-admin/includes/class-wp-list-table.php");

class CT_Users_Payments_Logs_List_Table extends WP_List_Table
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
            "created_date" => __("Date"),
            "payments" => __('# of Payments'),
            "subscriptions" => __("# of Subscriptions"),
            "total_amount" => __("Total Amount")
        );
    }
    
    function get_sortable_columns($orderby)
    {
        return $sortable = array(
            "created_date" => array("created_date", $orderby == 'created_date'),
            "payments" => array("payments", $orderby == 'payments'),
            "subscriptions" => array("subscriptions", $orderby == 'subscriptions'),
            "total_amount" => array("total_amount", $orderby == 'total_amount')
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
              /*
              ?>
              <div style="float: left;">
              <?php              
              $this->search_box("Search", "search");
              ?>              
              </div>
              <?php
              */
          }
    }
      
    function column_default($item, $column_name)
    {
        global $wpdb;
        
        switch($column_name)
        {            
            default:
                return $item->$column_name;
        }
    }
    
    function prepare_items()
    {
        global $wpdb;

        $paged = $this->get_pagenum();
        
        $orderby = isset($_REQUEST['orderby']) ? $_REQUEST['orderby'] : 'created_date';
        $order = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'desc';

        // Query the user IDs for this page
        $query = "SELECT count(*) FROM {$wpdb->prefix}users_payments_logs";
        $totalItems = $wpdb->get_var($query);
        
        $totalPages = ceil($totalItems / $this->per_pages);
        if($totalPages > $paged)
            $paged = $totalPages;
        
        $this->set_pagination_args(array(
            "total_items"=>$totalItems,
            "total_pages"=>$totalPages,
            "per_page"=>$this->per_pages
        ));
      
        $this->items = $wpdb->get_results($query);
      
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns($orderby);          
        $this->_column_headers = array($columns, $hidden, $sortable);
        
    }
}