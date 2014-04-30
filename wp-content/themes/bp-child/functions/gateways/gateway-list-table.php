<?php
/**
 * gateways List Table class.
 *
 * @package WordPress
 * @subpackage List_Table
 * @since 3.1.0
 * @access private
 */
class CT_Gateway_List_Table extends WP_List_Table {

    function __construct( $args = array() ) {
        parent::__construct( array(
            'singular' => 'gateway',
            'plural'   => 'gateways',
            'screen'   => isset( $args['screen'] ) ? $args['screen'] : null,
        ) );
    }

    function prepare_items() {
        global $role, $gatewaysearch, $wpdb;

        $gatewaysearch = isset( $_REQUEST['s'] ) ? trim( $_REQUEST['s'] ) : '';

        $per_page = 'gateways_per_page';
        $gateways_per_page = $this->get_items_per_page( $per_page );
        
        $paged = $this->get_pagenum();

        $args = array(
            'number' => $gateways_per_page,
            'offset' => ( $paged-1 ) * $gateways_per_page,
            'search' => $gatewaysearch
        );

        if ( '' !== $args['search'] )
            $args['search'] = '*' . $args['search'] . '*';

        if ( isset( $_REQUEST['orderby'] ) )
            $args['orderby'] = $_REQUEST['orderby'];

        if ( isset( $_REQUEST['order'] ) )
            $args['order'] = $_REQUEST['order'];
            
        $query_str = "SELECT SQL_CALC_FOUND_ROWS * FROM " . $wpdb->prefix . "gateways WHERE 1 = 1";
        if ($gatewaysearch != '') {
            $query_str .= " AND (`name` LIKE '%$gatewaysearch%'";
            $query_str .= " OR `abn` LIKE '%$gatewaysearch%'";
            $query_str .= " OR `url` LIKE '%$gatewaysearch%')";
        }
        if (isset($args['orderby']) && $args['orderby'] != '') {
            $query_str .= ' ORDER BY ' . $args['orderby'] . ' ' . $args['order'];
        }
        
        $query_str .= ' LIMIT ' . ( $paged-1 ) * $gateways_per_page . ', ' . $gateways_per_page;
        
        $this->items = $wpdb->get_results($query_str);
        $found_rows = $wpdb->get_row('SELECT FOUND_ROWS() as total_count');        
            
        $this->set_pagination_args( array(
            'total_items' => $found_rows->total_count,
            'per_page' => $gateways_per_page,
        ) );
        
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns($args['orderby']);          
        $this->_column_headers = array($columns, $hidden, $sortable);
    }

    function no_items() {
        _e( 'No matching gateways were found.' );
    }

    function get_bulk_actions() {
        $actions = array();
        
        $actions['delete'] = __( 'Delete' );

        return $actions;
    }

    function extra_tablenav( $which ) {
        
    }

    function get_columns() {
        $c = array(
            'cb'       => '<input type="checkbox" />',
            'name'     => __( 'Name' ),
            'abn'    => __( 'ABN' ),
            'url'    => __( 'URL' )
        );

        return $c;
    }

    function get_sortable_columns($orderby) 
    {
        $c = array(
            "name" => array("name", $orderby == 'name'),
            "abn" => array("abn", $orderby == 'abn'),
        );

        return $c;
    }

    function display_rows() 
    {        
        $style = '';
        foreach ( $this->items as $gateway ) {
            $role = '';
            $style = ( ' class="alternate"' == $style ) ? '' : ' class="alternate"';
            echo "\n\t" . $this->single_row( $gateway, $style);
        }
    }

    
    function single_row( $gateway, $style = '') 
    {
        global $wpdb;

        // Set up the hover actions for this gateway
        $actions = array();

        $edit = "<strong>$gateway->name</strong><br />";
        $actions['edit'] = "<a class='submitedit' href='" . wp_nonce_url( "admin.php?page=gateway_edit&action=edit&gateway_id=$gateway->gateway_id", 'bulk-gateways' ) . "'>" . __( 'Edit' ) . "</a>";
        $actions['delete'] = "<a class='submitdelete' href='" . wp_nonce_url( "admin.php?page=gateways&action=delete&amp;gateway_id=$gateway->gateway_id", 'bulk-gateways' ) . "'>" . __( 'Delete' ) . "</a>";                
        
        $actions = apply_filters( 'gateway_row_actions', $actions, $gateway );
        $edit .= $this->row_actions( $actions );

        // Set up the checkbox ( because the gateway is editable, otherwise it's empty )
        $checkbox = '<label class="screen-reader-text" for="cb-select-' . $gateway->gateway_id . '">' . sprintf( __( 'Select %s' ), $gateway->name ) . '</label>'
                    . "<input type='checkbox' name='gateway_ids[]' id='gateway_{$gateway->gateway_id}' value='{$gateway->gateway_id}' />";

        $r = "<tr id='gateway-$gateway->gateway_id'$style>";

        list( $columns, $hidden ) = $this->get_column_info();

        foreach ( $columns as $column_name => $column_display_name ) {
            $class = "class=\"$column_name column-$column_name\"";

            $style = '';
            if ( in_array( $column_name, $hidden ) )
                $style = ' style="display:none;"';

            $attributes = "$class$style";

            switch ( $column_name ) {
                case 'cb':
                    $r .= "<th scope='row' class='check-column'>$checkbox</th>";
                    break;
                case 'name':
                    $r .= "<td $attributes>$edit</td>";
                    break;
                case 'abn':
                    $r .= "<td $attributes>$gateway->abn</td>";
                    break;
                case 'url':
                    $r .= "<td $attributes>$gateway->url</td>";
                    break;
                default:
                    $r .= "<td $attributes>";
                    $r .= apply_filters( 'manage_gateways_custom_column', '', $column_name, $gateway->gateway_id );
                    $r .= "</td>";
            }
        }
        $r .= '</tr>';
        

        return $r;
    }
}
