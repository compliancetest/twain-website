<?php
/**
* Priority List Table
*/

class CT_Tickets_Priority_List_Table extends WP_List_Table
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
            "priority" => __("Priority"),
            "item_code" => __('Item Code'),
            "ttresponse" => __("TTResponse"),
            "ttresolve" => __("TTResolve"),            
            "sort_number" => __("Sort Number"),            
            "id" => __("ID"),  
        );
    }
    
    function get_sortable_columns($orderby)
    {
        return $sortable = array(
            "id" => array("id", $orderby == 'id'),
            "priority" => array("priority", $orderby == 'priority'),
            "item_code" => array("item_code", $orderby == 'item_code'),
            "ttresponse" => array("ttresponse", $orderby == 'ttresponse'),
            "ttresolve" => array("ttresolve", $orderby == 'ttresolve'),
            "sort_number" => array("sort_number", $orderby == 'sort_number')
        );
    }
    
    function column_cb($item){
        //return sprintf(
//            '<input type="checkbox" name="id[]" value="%1$s" />',
//            $item->id
//        );
    }
    
    
    function column_default($item, $column_name)
    {
        switch($column_name)
        {
            case 'priority':
                return $item->priority . $this->row_actions(array(
                    "<a href='admin.php?page=ct-tickets-priorities&ct-ticket-action=" . wp_create_nonce('edit-ticket-priority') . "&id=" . $item->id . "'>Edit</a>",
//                    "<a href='admin.php?page=ct-tickets&priority=" . $item->id . "'>View Ticketes</a>",
                    "<a href='admin.php?page=ct-tickets-priorities&ct-ticket-action=" . wp_create_nonce('delete-ticket-priority') . "&id=" . $item->id . "'>Delete</a>"
                    
                ));
            default:
                return $item->$column_name;
        }
    }
    
    function prepare_items()
    {
        global $wpdb;
        
        $orderby = isset($_REQUEST['orderby']) ? $_REQUEST['orderby'] : 'priority';
        $order = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'asc';
        
        $query = "SELECT count(id) FROM " . TABLE_TICKET_PRIORITIES;
        $totalItems = $wpdb->get_var($query);
        
        $paged = isset($_REQUEST['paged']) ? $_REQUEST['paged'] : 1;
        
        $totalPages = ceil($totalItems / $this->per_pages);
        if(empty($paged) || !is_numeric($paged) || $paged <= 0 )
        { 
            $paged = 1; 
        }
        if($paged > $totalPages)
            $paged = $totalPages; 
        
        $query = "SELECT * FROM " . TABLE_TICKET_PRIORITIES . " ORDER BY $orderby $order LIMIT " . (($paged - 1) * $this->per_pages) . ", " . $this->per_pages;
        $this->items = $wpdb->get_results($query);
        
        $this->set_pagination_args(array(
            "total_items"=>$totalItems,
            "total_pages"=>$totalPages,
            "per_page"=>$this->per_pages
        ));
        
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns($orderby);          
        $this->_column_headers = array($columns, $hidden, $sortable);
        
    }
}