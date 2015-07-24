<?php
/**
* Ticket List Table
*/


class CT_Tickets_Ticket_List_Table extends WP_List_Table
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
            "title" => __("Title"),
            "customer_name" => __("Customer Name"),  
            "customer_email" => __("Customer Email"),  
            "support_name" => __("Support Name"),  
            "support_email" => __("Support Email"),              
            "created_date" => __("Requested"),
            "category_title" => __("Type"),
            "status_title" => __("Status"),
            "priority_title" => __("Priority"),
            "last_updated" => __("Updated")
            
        );
    }
    
    function get_sortable_columns($orderby)
    {
        return $sortable = array(
            "id" => array("t.id", $orderby == 't.id'),
            "customer_name" => array("customer_name", $orderby == 'customer_name'),
            "customer_email" => array("customer_email", $orderby == 'customer_email'),
            "support_name" => array("support_name", $orderby == 'support_name'),
            "support_email" => array("support_email", $orderby == 'support_email'),
            "title" => array("t.title", $orderby == 't.title'),
            "created_date" => array("t.created_date", $orderby == 't.created_date'),
            "category_title" => array("category_title", $orderby == 'category_title'),
            "status_title" => array("status_title", $orderby == 'status_title'),
            "priority_title" => array("priority_title", $orderby == 'priority_title'),
            "last_updated" => array("t.ttpay", $orderby == 't.last_updated')
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
                return $item->title . $this->row_actions(array(
                    "<a href='admin.php?page=ct-tickets&id=" . $item->id . "'>View</a>"                    
                ));
            case 'id':
                return str_pad($item->id, 8, 0, STR_PAD_LEFT);            
            case 'customer':
                return $item->customer_name . " (" . $item->customer_email . ")";            
            case 'priority_title':
                return "<span class='ticket-priority ticket-priority-" . sanitize_title($item->priority_title) . "'><b>" . $item->priority_title . "</b></span><br />"
                    . "<b>Price:</b> " . ($item->price > 0 ? '$' . $item->price . '/hr' : 'Free') . "<br />"
                    . "<b>ttPay:</b> " . $item->ttpay . " hrs" . "<br />"
                    . "<b>ttResponse:</b> " . $item->ttresponse . " hrs" . "<br />"
                    . "<b>ttResolve:</b> " . $item->ttresolve . " hrs" . "<br />"
                    ;
                ;            
            default:
                return $item->$column_name;
        }
    }
    
    function extra_tablenav($which)
    {
        global $ct_ticket_status, $ct_ticket_category, $ct_ticket_priority;
          if($which == "top")
          {
              $category = isset($_GET['type']) ? $_GET['type'] : '';
              $priority = isset($_GET['priority']) ? $_GET['priority'] : '';
              $status = isset($_GET['status']) ? $_GET['status'] : '';
              $customer = isset($_GET['customer']) ? $_GET['customer'] : '';
              
              $customers = $this->getAllCustomers();
              
          ?>
              <div style="float: left;">
              <form name="adminform" method="get">
              <?php              
                $this->search_box("Search", "fc-search");
                
              ?>
              <label style="white-space: nowrap;">
                  Support: 
                  <select name="support" autocomplete="off">
                      <option value="">- Select -</option>
                  </select>
              </label>
              
              <label style="white-space: nowrap;">
                  Customer: 
                  <select name="customer" autocomplete="off">
                    <option value="" <?php echo !$customer ? 'selected="selected"' : '' ?>>- Select -</option>
                    <?php
                        foreach($customers as $row){
                    ?>
                      <option value="<?php echo $row->user_id?>" <?php if($customer == $row->user_id){?>selected="selected"<?php }?>><?php echo $row->display_name?></option>
                    <?php
                      }
                    ?>
                  </select>
              </label>
              
              <label style="white-space: nowrap;">
                  Type:     
                  <?php
                      echo $ct_ticket_category->getCategoriesSelectboxHTML("type", "type", $category, "- Select -");
                  ?>
              </label>
              
              <label style="white-space: nowrap;">
                  Priority:     
                  <?php
                      echo $ct_ticket_priority->getPrioritiesSelectboxHTML("priority", "priority", $priority, "- Select -");
                  ?>
              </label>
              
              <label style="white-space: nowrap;">
                  Status:     
                  <?php
                      echo $ct_ticket_status->getStatusesSelectboxHTML("status", "status", $status, "- Select -");
                  ?>
              </label>
              
              
              <input type="button" value="Filter" onclick="document.adminform.submit()" class="button" style="margin-right: 20px" >
              <input type="hidden" name="page" value="ct-tickets" />
              </form>
              </div>
              <?php
          }
    }
    
    function prepare_items()
    {
        global $wpdb;
        
        $orderby = isset($_REQUEST['orderby']) ? $_REQUEST['orderby'] : 't.title';
        $order = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'asc';
        
        $query = "SELECT count(id) FROM " . TABLE_TICKETS;
        $totalItems = $wpdb->get_var($query);
        
        $paged = isset($_REQUEST['paged']) ? $_REQUEST['paged'] : 1;
        
        $totalPages = ceil($totalItems / $this->per_pages);
        if(empty($paged) || !is_numeric($paged) || $paged <= 0 )
        { 
            $paged = 1; 
        }
        if($paged > $totalPages)
            $paged = $totalPages; 
        
        if($paged < 1)
            $paged = 1;
        
        $query = "SELECT t.*, ts.status AS status_title, tc.category_title, tp.priority AS priority_title, u.display_name AS customer_name, u.user_email as customer_email, u1.display_name AS support_name, u1.user_email AS support_email FROM " . $wpdb->prefix . "tickets AS t "
                . "LEFT JOIN " . TABLE_TICKET_STATUSES . " AS ts ON ts.id=t.status_id "
                . "LEFT JOIN " . TABLE_TICKET_CATEGORIES . " AS tc ON tc.id=t.category_id "
                . "LEFT JOIN " . TABLE_TICKET_PRIORITIES . " AS tp ON tp.id=t.priority_id "
                . "LEFT JOIN " . $wpdb->users . " AS u ON u.ID=t.customer_id "
                . "LEFT JOIN " . $wpdb->users . " AS u1 ON u1.ID=t.support_id ";
        
        $where = array();
        
        $category = isset($_GET['type']) ? $_GET['type'] : NULL;
        $priority = isset($_GET['priority']) ? $_GET['priority'] : NULL;
        $status = isset($_GET['status']) ? $_GET['status'] : NULL;
        $customer = isset($_GET['customer']) ? $_GET['customer'] : NULL;
        
        if($category)
            $where[] = $wpdb->prepare(" t.category_id = %d", $category);
        
        if($priority)
            $where[] = $wpdb->prepare(" t.priority_id = %d", $priority);
        
        if($status)
            $where[] = $wpdb->prepare(" t.status_id = %d", $status);
        
        if($customer)
            $where[] = $wpdb->prepare(" t.customer_id = %d", $customer);
        
        
        
        if($where)
            $query .= "WHERE " . implode(" AND ", $where);
                
        $query .= " ORDER BY $orderby $order LIMIT " . (($paged - 1) * $this->per_pages) . ", " . $this->per_pages;
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
    
    function getAllCustomers()
    {
        global $wpdb;
        
        $query = "SELECT DISTINCT(s.user_id), u.display_name, u.user_email  FROM " . $wpdb->prefix . "users_subscriptions AS s "
                ."LEFT JOIN {$wpdb->users} AS u ON u.ID=s.user_id WHERE s.status='Active' ORDER BY display_name";
        
        $results = $wpdb->get_results($query);
        
        return $results;
    }   
}