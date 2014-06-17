<?php

/**
* User Subscription List Table
*/

require_once(ABSPATH . "/wp-admin/includes/class-wp-list-table.php");

class CT_Users_Purchases_List_Table extends WP_List_Table
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
            "username" => __("Username"),
            "name" => __('Name'),
            "email" => __("Email"),
            "payments_methods" => __("Payment Methods"),                                    
            "subscriptions" => __("Subscriptions"),                        
            "total_ticket_hours" => __("Total Ticket Hours<br />(Normal/High/Urgent)"),
            "pending_ticket_hours" => __("Pending Ticket Hours<br />(Normal/High/Urgent)"),
            "id" => __("ID"),  
        );
    }
    
    function get_sortable_columns($orderby)
    {
        return $sortable = array(
            "username" => array("login", $orderby == 'login'),
            "name" => array("name", $orderby == 'name'),
            "email" => array("email", $orderby == 'email'),
            "payments_methods" => array("cards", $orderby == 'cards'),
            "subscriptions" => array("subscriptions", $orderby == 'subscriptions'),
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
              ?>
              <div style="float: left;">
              <?php              
              $this->search_box("Search", "search");
              ?>                  
              <label style="margin-right: 10px">
                  Show 
                  <select name="filter_subscriptions" onchange="document.adminform.submit()">
                      <option value="1" <?php echo isset($_REQUEST['filter_subscriptions']) && $_REQUEST['filter_subscriptions'] == 1 ? 'selected="selected"' : '' ?>>All Users</option>
                      <option value="2" <?php echo !isset($_REQUEST['filter_subscriptions']) || $_REQUEST['filter_subscriptions'] == 2 ? 'selected="selected"' : '' ?>>Customers</option>
                  </select>
              </label>
              </div>              
              <?php
          }
    }
      
    function column_default($item, $column_name)
    {
        global $wpdb;
        
        switch($column_name)
        {
            case 'username':
                return get_avatar($item->ID, 22) . '<strong>' . $item->user_login . '</strong>' . $this->row_actions(array(
                    "<a href='admin.php?page=users&action=detail&id=" . $item->ID . "'>Detail</a>"                    
                ));
            case 'name':
                return $item->first_name . " " . $item->last_name;
            
            case 'email':
                return $item->user_email;
            
            case 'payments_methods':
                return !isset($item->cards) ? 0 : $item->cards;
            
            case 'subscriptions':
                return !isset($item->subscriptions) ? 0 : $item->subscriptions;
            
            case 'total_ticket_hours':
                return str_pad($item->total_ticket_hours_normal, 2, '0', STR_PAD_LEFT) . ' / ' . str_pad($item->total_ticket_hours_high, 2, '0', STR_PAD_LEFT) . ' / ' . str_pad($item->total_ticket_hours_urgent, 2, '0', STR_PAD_LEFT);
            case 'pending_ticket_hours':
                return $item->pending_ticket_hours_normal . '/' . $item->pending_ticket_hours_high . '/' . $item->pending_ticket_hours_urgent;
            
            default:
                return $item->$column_name;
        }
    }
    
    function prepare_items()
    {
        global $role, $usersearch, $wpdb;

        $usersearch = isset( $_REQUEST['s'] ) ? trim( $_REQUEST['s'] ) : '';

        $role = isset( $_REQUEST['role'] ) ? $_REQUEST['role'] : '';

        $per_page = 'users_per_page';
        $users_per_page = $this->get_items_per_page( $per_page );

        $paged = $this->get_pagenum();
        
        $orderby = isset($_REQUEST['orderby']) ? $_REQUEST['orderby'] : 'name';
        $order = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'asc';
        
        $filter_subscriptions = isset($_REQUEST['filter_subscriptions']) ? $_REQUEST['filter_subscriptions'] : 2;
        
        $args = array(
            'number' => $users_per_page,
            'offset' => ( $paged-1 ) * $users_per_page,
            'role' => $role,
            'search' => $usersearch,
            'orderby' => $orderby,
            'order' => $order,
            'fields' => 'all_with_meta'
        );

        if ( '' !== $args['search'] )
            $args['search'] = '*' . $args['search'] . '*';

        // Query the user IDs for this page
        $wp_user_search = new WP_User_Query( $args );
        
        //Add User Extra
        $wp_user_search->query_from .= " LEFT JOIN {$wpdb->prefix}users_extra AS ue ON ue.userID={$wpdb->users}.ID  ";
        
        $wp_user_search->query_fields .= " ,ue.* ";
        
        if($orderby == 'cards' || $orderby == 'subscriptions')
            $wp_user_search->query_orderby = ' ORDER BY ' . $orderby . ' ' . $order;
        
        if($filter_subscriptions == 2)
            $wp_user_search->query_where .= ' AND ue.subscriptions > 0 ';
        
        $wp_user_search->results = $wpdb->get_results("SELECT $wp_user_search->query_fields $wp_user_search->query_from $wp_user_search->query_where $wp_user_search->query_orderby $wp_user_search->query_limit");            
        
        if ( isset( $wp_user_search->query_vars['count_total'] ) && $wp_user_search->query_vars['count_total'] )
            $wp_user_search->total_users = $wpdb->get_var( apply_filters( 'found_users_query', 'SELECT FOUND_ROWS()' ) );

        if ( !$wp_user_search->results )
        {
            $results = array();
        }else{
            if ( 'all_with_meta' == $wp_user_search->query_vars['fields'] ) {
                cache_users( $wp_user_search->results );

                $r = array();
                foreach ( $wp_user_search->results as $urow ){
                    $r[ $urow->ID ] = new WP_User( $urow->ID, '', $wp_user_search->query_vars['blog_id'] );
                    
                    $r[ $urow->ID ]->subscriptions = $urow->subscriptions;
                    $r[ $urow->ID ]->cards = $urow->cards;
                    
                    $r[ $urow->ID ]->total_ticket_hours_normal = $urow->total_ticket_hours_normal;
                    $r[ $urow->ID ]->total_ticket_hours_high = $urow->total_ticket_hours_high;
                    $r[ $urow->ID ]->total_ticket_hours_urgent = $urow->total_ticket_hours_urgent;
                    $r[ $urow->ID ]->pending_ticket_hours_normal = $urow->pending_ticket_hours_normal;
                    $r[ $urow->ID ]->pending_ticket_hours_high = $urow->pending_ticket_hours_high;
                    $r[ $urow->ID ]->pending_ticket_hours_urgent = $urow->pending_ticket_hours_urgent;
                }

                $wp_user_search->results = $r;
            } 
            $results = $wp_user_search->get_results();
        }
        
        $uids = array();
        foreach($results as $u)
        {
            $uids[] = $u->ID;
        }
        
        $this->items = $results;
        
        $this->set_pagination_args( array(
            'total_items' => $wp_user_search->get_total(),
            'per_page' => $users_per_page,
        ) );
        
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns($orderby);          
        $this->_column_headers = array($columns, $hidden, $sortable);
        
    }
}