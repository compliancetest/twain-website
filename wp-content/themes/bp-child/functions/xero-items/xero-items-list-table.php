<?php

/**
* Xero Items List Table
*/

require_once(ABSPATH . "/wp-admin/includes/class-wp-list-table.php");

class CT_Xeroitems_List_Table extends WP_List_Table
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
//            "cb" => "<input type='checkbox' />",            
            "code"         => __("Code"),
            "description"  => __('Description'),
            "unit_price"   => __('Unit Price'),
            "account_code" => __("Account Code"),
            "id"           => __("ID"),
        );
    }
    
    function get_sortable_columns( $orderby )
    {
        return $sortable = array(
            "code"         => array("code", $orderby == 'code'),
            "description"  => array("description", $orderby == 'description'),
            "unit_price"   => array("unit_price", $orderby == 'unit_price'),
            "account_code" => array("account_code", $orderby == 'account_code'),
            "id"           => array("ID", $orderby == 'ID')
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
          /*if($which == "top")
          {
              ?>
              <div style="float: left;">
              <?php              
              $this->search_box("Search", "search");
              ?>             
              </div>              
              <?php
          }*/
    }
      
    function column_default($item, $column_name)
    {
        global $wpdb;
        
        switch($column_name)
        {
            case 'code':
                return $item->code;
//                . $this->row_actions(array(
//                    "<a href='admin.php?page=add-xeroitem&id=" . $item->id . "'>Edit</a>"
//                ));
            default:
                return $item->$column_name;
        }
    }
    
    function prepare_items()
    {
        global $wpdb;

        $paged = $this->get_pagenum();
        
        $orderby = isset($_REQUEST['orderby']) ? $_REQUEST['orderby'] : 'code';
        $order   = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'asc';

        // Query items for this table
        $query = "SELECT count(*) FROM {$wpdb->prefix}xeroitems";
        $totalItems = $wpdb->get_var($query);
        
        $totalPages = ceil($totalItems / $this->per_pages);
        if($totalPages > $paged)
            $paged = $totalPages;
        
        $this->set_pagination_args(array(
            "total_items" => $totalItems,
            "total_pages" => $totalPages,
            "per_page"    => $this->per_pages
        ));
      
        $query  = "SELECT * FROM {$wpdb->prefix}xeroitems ";
        $query .= " ORDER BY $orderby $order ";
        $query .= " LIMIT " . ($paged-1) * $this->per_pages .  ", {$this->per_pages} ";
        
        $this->items = $wpdb->get_results($query);
      
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns($orderby);          
        $this->_column_headers = array($columns, $hidden, $sortable);
        
    }
}