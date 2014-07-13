<?php

/**
* Organisations List Table
*/

require_once(ABSPATH . "/wp-admin/includes/class-wp-list-table.php");

class CT_Organisations_List_Table extends WP_List_Table
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
            "organisation_name" => __("Organisation Name"),
            "organisation_domain" => __('Organisation Domain'),
            "organisation_admin" => __('Organisation Admin'),
            "xero_contact_name" => __("Xero Contact Name"),
            "invoice_me" => __("Invoice Me"),                                    
            "contact_first_name" => __("First Name"),                        
            "contact_last_name" => __("Last Name"),                        
            "contact_email" => __("Email"),                        
            "abn" => __("ABN"),                        
            "billing_address" => __("Billing Address"),
            "phonenumber" => __("Telephone"),
            "contact_id" => __("Contact ID"),
            "id" => __("ID"),  
        );
    }
    
    function get_sortable_columns($orderby)
    {
        return $sortable = array(
            "organisation_name" => array("organisation_name", $orderby == 'organisation_name'),
            "organisation_domain" => array("organisation_domain", $orderby == 'organisation_domain'),
            "xero_contact_name" => array("xero_contact_name", $orderby == 'xero_contact_name'),
            "invoice_me" => array("invoice_me", $orderby == 'invoice_me'),
            "contact_first_name" => array("contact_first_name", $orderby == 'contact_first_name'),
            "contact_last_name" => array("contact_last_name", $orderby == 'contact_last_name'),
            "contact_email" => array("contact_email", $orderby == 'contact_email'),
            "abn" => array("abn", $orderby == 'abn'),
            "contact_id" => array("contact_id", $orderby == 'contact_id'),
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
            case 'organisation_name':
                return $item->organisation_name . $this->row_actions(array(
                    "<a href='admin.php?page=add-organisation&id=" . $item->id . "'>Edit</a>"                    
                ));
            case 'billing_address':
                return $item->billing_address_attention . " " . 
                        $item->billing_address1 . 
                        (!$item->billing_address2 ? "" : (", " . $item->billing_address2)) . 
                        (!$item->billing_address3 ? "" : (", " . $item->billing_address3)) . 
                        (!$item->billing_address4 ? "" : (", " . $item->billing_address4)) . 
                        ", " . $item->billing_city . ", " . $item->billing_state . " " . $item->billing_postcode . ", " . $item->billing_country;
            
            case 'phonenumber':
                return "(" . $item->phonenumber_countrycode . ") " . "(" . $item->phonenumber_areacode . ") " . $item->phonenumber;
            
            case 'organisation_admin':
                return $item->admin_name . "(" . $item->admin_email . ")";
            
            case 'invoice_me':
                return $item->invoice_me ? 'Yes' : 'No';
            
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
        $query = "SELECT count(*) FROM {$wpdb->prefix}organisations";
        $totalItems = $wpdb->get_var($query);
        
        $totalPages = ceil($totalItems / $this->per_pages);
        if($totalPages > $paged)
            $paged = $totalPages;
        
        $this->set_pagination_args(array(
            "total_items"=>$totalItems,
            "total_pages"=>$totalPages,
            "per_page"=>$this->per_pages
        ));
      
        $query = "SELECT o.*, u.user_email AS admin_email, u.display_name AS admin_name FROM {$wpdb->prefix}organisations as o LEFT JOIN {$wpdb->users} as u on u.ID=o.admin_id ";
        $query .= " ORDER BY $orderby $order ";
        $query .= " LIMIT " . ($paged-1) * $this->per_pages .  ", {$this->per_pages} ";
        
        $this->items = $wpdb->get_results($query);
      
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns($orderby);          
        $this->_column_headers = array($columns, $hidden, $sortable);
        
    }
}