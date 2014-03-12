<?php

/**
* User Suite List Table
*/
class CT_User_Suite_List_Table extends WP_List_Table
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
            "fee" => __("Signup Fee"),                        
            "organisation" => __("Organisation Pricing"),                        
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
                    "<a href='users.php?page=user_fee_overrides&action=edit&id=" . $item->ID . "'>Edit</a>"
                    
                ));
            case 'name':
                return $item->first_name . " " . $item->last_name;
            
            case 'email':
                return $item->user_email;
            
            case 'fee':
                $signup_fee = get_user_meta($item->ID, 'signup_fee', true);
                $data = array();
                if($signup_fee)
                {
                    foreach($signup_fee as $sid=>$fee)
                    {
                        $data[] = get_the_title($sid) . ": <b style='font-weight: bold'>\$" . $fee . "</b>";
                        
                    }
                }
                return implode('<br />', $data);
            case 'organisation':
                //Getting Old Organisations Data
                $query = $wpdb->prepare("SELECT op.*, p.post_title FROM {$wpdb->prefix}users_organisation_pricing AS op
                                         LEFT JOIN {$wpdb->prefix}test_suites AS ts ON ts.family_mark=op.family_mark
                                         LEFT JOIN {$wpdb->posts} AS p ON p.id=ts.suite_id
                                         WHERE user_id=%d", $item->ID);
                $oRows = $wpdb->get_results($query);
                
                $orgPrices = array();
                foreach($oRows as $iRow)
                {
                    $orgPrices[] = $iRow->post_title . ": <b style='font-weight: bold'>User Count: " . $iRow->user_count . "</b>". " ,<b style='font-weight: bold'>Joined: " . $iRow->joined_count . "</b>";
                }
                
                return implode('<br />', $orgPrices);
                
            default:
                return $item->$column_name;
        }
    }
    
    function prepare_items()
    {
        global $role, $usersearch;

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

        $this->items = $wp_user_search->get_results();

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