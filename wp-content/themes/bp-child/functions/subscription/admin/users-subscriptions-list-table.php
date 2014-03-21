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
            "id" => __("ID"),  
        );
    }
    
    function get_sortable_columns($orderby)
    {
        return $sortable = array(
            "username" => array("login", $orderby == 'login'),
            "name" => array("name", $orderby == 'name'),
            "email" => array("email", $orderby == 'email'),
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
                return get_avatar($item->ID, 32) . '<strong>' . $item->user_login . '</strong>' . $this->row_actions(array(
                    "<a href='admin.php?page=users&action=view&id=" . $item->ID . "'>View</a>"                    
                ));
            case 'name':
                return $item->first_name . " " . $item->last_name;
            
            case 'email':
                return $item->user_email;
            
            case 'payments_methods':
                return !isset($item->cards) ? 0 : $item->cards;
            
            case 'subscriptions':
                return !isset($item->subscriptions) ? 0 : $item->subscriptions;
            
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

        $results = $wp_user_search->get_results();
        $uids = array();
        foreach($results as $u)
        {
            $uids[] = $u->ID;
        }
        
        //Getting Payments and Subscriptions
        $query = "SELECT count(id) AS count, user_id FROM {$wpdb->prefix}users_cards WHERE user_id IN (" . implode(",", $uids) . ") GROUP BY user_id";
        $cards = $wpdb->get_results($query);
        
        foreach($results as $i=>$row)
        {
            foreach($cards as $c)
            {
                if($c->user_id == $row->ID)
                {
                    $results[$i]->cards = $c->count;
                    break;
                }
            }
        }
        
        //Getting Subscriptions        
        $query = "SELECT count(id) AS count, user_id FROM {$wpdb->prefix}users_subscriptions WHERE user_id IN (" . implode(",", $uids) . ") GROUP BY user_id";
        $subscriptions = $wpdb->get_results($query);
        foreach($results as $i=>$row)
        {
            foreach($subscriptions as $c)
            {
                if($c->user_id == $row->ID)
                {
                    $results[$i]->subscriptions = $c->count;
                    break;
                }
            }
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