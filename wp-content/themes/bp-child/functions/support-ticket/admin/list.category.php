<?php
/**
* Category List Table
*/

class CT_Tickets_Category_List_Table extends WP_List_Table
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
            "title" => __("Name"),
            "has_fee" => __("Has Fee"),
            "sort_number" => __("Sort"),
            "created_date" => __("Created"),
            "tickets" => __("Tickets"),
            "id" => __("ID"),  
        );
    }
    
    function get_sortable_columns($orderby)
    {
        return $sortable = array(
            "id" => array("id", $orderby == 'id'),
            "title" => array("category_title", $orderby == 'category_title'),
            "has_fee" => array("has_fee", $orderby == 'has_fee'),
            "sort_number" => array("sort_number", $orderby == 'sort_number'),
            "created_date" => array("created_date", $orderby == 'created_date'),
            "tickets" => array("tickets", $orderby == 'tickets'),
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
            case 'title':
                return $item->category_title . $this->row_actions(array(
                    "<a href='admin.php?page=ct-tickets-categories&ct-ticket-action=" . wp_create_nonce('edit-ticket-category') . "&id=" . $item->id . "'>Edit</a>",
//                    "<a href='admin.php?page=ct-tickets&category=" . $item->id . "'>View Ticketes</a>",
                    "<a href='admin.php?page=ct-tickets-categories&ct-ticket-action=" . wp_create_nonce('delete-ticket-category') . "&id=" . $item->id . "'>Delete</a>"
                    
                ));
            case 'has_fee':
                return $item->has_fee ? 'Yes' : 'No';
            default:
                return $item->$column_name;
        }
    }
    
    function prepare_items()
    {
        global $wpdb;
        
        $orderby = isset($_REQUEST['orderby']) ? $_REQUEST['orderby'] : 'sort_number';
        $order = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'asc';
        
        $query = "SELECT count(id) FROM " . TABLE_TICKET_CATEGORIES;
        $totalItems = $wpdb->get_var($query);
        
        $paged = isset($_REQUEST['paged']) ? $_REQUEST['paged'] : 1;
        
        $totalPages = ceil($totalItems / $this->per_pages);
        if(empty($paged) || !is_numeric($paged) || $paged <= 0 )
        { 
            $paged = 1; 
        }
        if($paged > $totalPages)
            $paged = $totalPages; 
        
        $query = "SELECT * FROM " . TABLE_TICKET_CATEGORIES . " ORDER BY $orderby $order LIMIT " . (($paged - 1) * $this->per_pages) . ", " . $this->per_pages;
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