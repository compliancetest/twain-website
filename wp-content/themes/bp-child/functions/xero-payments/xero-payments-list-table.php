<?php

/**
* Xero Payments List Table
*/

require_once(ABSPATH . "/wp-admin/includes/class-wp-list-table.php");

class CT_Xero_Payments_Table extends WP_List_Table
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
            "cb"                => "<input type='checkbox' />",
            "invoice_id"        => __("Invoice ID"),
            "date_added"        => __('Date Added'),
            "amount"            => __('Amount'),
            "reference"         => __("Reference"),
            "is_reconciled"     => __("Is Reconsiled"),
            "organisation_id"   => __("Organisation"),
            "payment_method_id" => __("Payment Method ID"),
            "is_paid"           => __("Is Paid?"),
            "date_paid"         => __("Date Paid"),
            "payment_id"        => __("Payment ID"),
            "id"                => __("ID")
        );
    }
    
    function get_sortable_columns( $orderby )
    {
        return $sortable = array(
            "invoice_id"    => array("invoice_id", $orderby == 'invoice_id'),
            "date"          => array("date", $orderby == 'date'),
            "amount"        => array("amount", $orderby == 'amount'),
            "reference"     => array("reference", $orderby == 'reference'),
            "is_reconciled" => array("is_reconciled", $orderby == 'is_reconciled'),
            "reference"     => array("reference", $orderby == 'reference'),
            "organisation_id"   => array("organisation_id", $orderby == 'organisation_id'),
            "payment_method_id" => array("payment_method_id", $orderby == 'payment_method_id'),
            "is_paid"           => array("is_paid", $orderby == 'is_paid'),
            "date_paid"         => array("date_paid", $orderby == 'date_paid'),
        );
    }
    
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="id[]" value="%1$s" />',
            $item->id
        );
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
            case 'is_reconciled':
                return $item->is_reconciled ? 'Yes' : 'No';
            case 'is_paid':
                return $item->is_paid ? 'Yes' : 'No';
            case 'organisation_id':
                $org_name = $wpdb->get_var( $wpdb->prepare("SELECT organisation_name FROM {$wpdb->prefix}organisations WHERE id = %d", $item->organisation_id ) );
                return $org_name . $this->row_actions(array(
                    "<a href='admin.php?page=add-organisation&id=" . $item->organisation_id . "'>Edit</a>"
                ));
            case 'date_added':
                return date( 'Y-m-d', strtotime( $item->date_added ) );
            case 'date_paid':
                return $item->date_paid != '0000-00-00 00:00:00' ? date( 'Y-m-d', strtotime( $item->date_paid ) ) : '';
            default:
                return $item->$column_name;
        }
    }
    
    function prepare_items()
    {
        global $wpdb;

        $paged = $this->get_pagenum();
        
        $orderby = isset($_REQUEST['orderby']) ? $_REQUEST['orderby'] : 'id';
        $order   = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'asc';

        // Query items for this table
        $query = "SELECT count(*) FROM {$wpdb->prefix}xero_payments";
        $totalItems = $wpdb->get_var($query);
        
        $totalPages = ceil($totalItems / $this->per_pages);
        if($totalPages < $paged)
            $paged = $totalPages;
        
        $this->set_pagination_args(array(
            "total_items" => $totalItems,
            "total_pages" => $totalPages,
            "per_page"    => $this->per_pages
        ));
      
        $query  = "SELECT * FROM {$wpdb->prefix}xero_payments ";
        $query .= " ORDER BY $orderby $order ";
        $query .= " LIMIT " . ($paged-1) * $this->per_pages .  ", {$this->per_pages} ";
        
        $this->items = $wpdb->get_results($query);
      
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns($orderby);          
        $this->_column_headers = array($columns, $hidden, $sortable);
        
    }
}