<?php
/**
* Priority List Table
*/

class CT_Tickets_Status_List_Table extends WP_List_Table
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
            "id" => __("ID"),  
            "status" => __("Status"),
            "sort_number" => __("Sort Number")
        );
    }
    
    function get_sortable_columns($orderby)
    {
        return $sortable = array(
            "id" => array("id", $orderby == 'id'),
            "status" => array("status", $orderby == 'status'),
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
            case 'status':
                return $item->status . $this->row_actions(array(
                    "<a href='admin.php?page=ct-tickets-statuses&ct-ticket-action=" . wp_create_nonce('edit-ticket-status') . "&id=" . $item->id . "'>Edit</a>",
                    "<a href='admin.php?page=ct-tickets-statuses&ct-ticket-action=" . wp_create_nonce('delete-ticket-status') . "&id=" . $item->id . "'>Delete</a>"
                    
                ));
            default:
                return $item->$column_name;
        }
    }
    
    function prepare_items()
    {
        global $wpdb;
        
        $orderby = isset($_REQUEST['orderby']) ? $_REQUEST['orderby'] : 'status';
        $order = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'asc';
        
        $query = "SELECT count(id) FROM " . TABLE_TICKET_STATUSES;
        $totalItems = $wpdb->get_var($query);
        
        $paged = isset($_REQUEST['paged']) ? $_REQUEST['paged'] : 1;
        
        $totalPages = ceil($totalItems / $this->per_pages);
        if(empty($paged) || !is_numeric($paged) || $paged <= 0 )
        { 
            $paged = 1; 
        }
        if($paged > $totalPages)
            $paged = $totalPages; 
        
        $query = "SELECT * FROM " . TABLE_TICKET_STATUSES . " ORDER BY $orderby $order LIMIT " . (($paged - 1) * $this->per_pages) . ", " . $this->per_pages;
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