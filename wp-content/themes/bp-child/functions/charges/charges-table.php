<?php

/**
 * Organisations Charge List Table
 */

require_once(ABSPATH . "/wp-admin/includes/class-wp-list-table.php");

class CT_Organisations_Charge_Table extends WP_List_Table
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
            "organisation_id" => __("Organisation ID"),
            "payment_id"      => __('Payment ID'),
            "item_code"       => __("Item Code"),
            "start_date"      => __("Start Date"),
            "end_date"        => __("End Date"),
            "reference_type"  => __("Reference Type"),
            "reference_id"    => __("Reference ID"),
            "quantity"        => __("Quantity"),
            "invoice_identifier" => __("Invoice Identifier"),
            "is_paid" => __("Paid Status"),
            "comment" => __("Comment"),
            "id"      => __("ID"),
        );
    }

    function get_sortable_columns($orderby)
    {
        return $sortable = array(
            "start_date"     => array("start_date", $orderby == 'start_date'),
            "end_date"       => array("end_date", $orderby == 'end_date'),
            "reference_type" => array("reference_type", $orderby == 'reference_type'),
            "is_paid"        => array("is_paid", $orderby == 'is_paid'),
            "id"             => array("ID", $orderby == 'ID')
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
<!--            <div style="float: left;">-->
<!--                --><?php
//                $this->search_box("Search", "search");
//                ?>
<!--            </div>-->
        <?php
        }
    }

    function column_default($item, $column_name)
    {
        global $wpdb;

        switch($column_name)
        {
            case 'organisation_id':
                return "<a href='admin.php?page=add-organisation&id=" . $item->organisation_id . "'>".$item->organisation_name."</a>" .
                $this->row_actions(array(
                    "<a href='admin.php?page=add-charge&id=" . $item->id . "'>Edit</a>"
                ));
            case 'item_code':
                $item_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}xeroitems WHERE code = %s", $item->item_code));
                return "<a href='admin.php?page=add-xeroitem&id=" . $item_data->id . "'>".$item_data->code."</a>";

            case 'start_date':
                return $item->start_date !== '0000-00-00 00:00:00' ? date( 'Y-m-d', strtotime( $item->start_date ) ) : '';
            case 'end_date':
                return $item->end_date !== '0000-00-00 00:00:00' ? date( 'Y-m-d', strtotime( $item->end_date ) ) : '';
            case 'is_paid':
                return $item->is_paid ? '<span style="color: green;">Paid</span>' : '<span style="color: red;">Not Paid</span>';
            case 'payment_id':
                return $item->payment_method  . ' ('. ( $item->invoice_me == '1' ? 'Invoice Me' : 'Credit Card' ).' )';
            default:
                return $item->$column_name;
        }
    }

    function prepare_items()
    {
        global $wpdb;

        $paged = $this->get_pagenum();

        $orderby = isset($_REQUEST['orderby']) ? $_REQUEST['orderby'] : 'organisation_id';
        $order   = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'asc';

        // Query items for this table
        $query = "SELECT count(*) FROM {$wpdb->prefix}organisations_charge";
        $totalItems = $wpdb->get_var($query);

        $totalPages = ceil($totalItems / $this->per_pages);
        if($totalPages < $paged)
            $paged = $totalPages;

        $this->set_pagination_args(array(
            "total_items" => $totalItems,
            "total_pages" => $totalPages,
            "per_page"    => $this->per_pages
        ));
        
        if( "$orderby $order" == 'organisation_id asc' ){
            $orderby = 'start_date DESC, item_code ASC';
            $order = '';
        }
        $query  = "SELECT c.*, o.organisation_name, p.nickname AS payment_method, p.invoice_me FROM {$wpdb->prefix}organisations_charge AS c 
                    LEFT JOIN {$wpdb->prefix}organisations AS o ON o.id=c.organisation_id 
                    LEFT JOIN {$wpdb->prefix}organisations_payment_methods AS p ON c.payment_id =p.id
                    ";
        $query .= " ORDER BY $orderby $order ";
        $query .= " LIMIT " . ($paged-1) * $this->per_pages .  ", {$this->per_pages} ";
        
        $this->items = $wpdb->get_results($query);

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns($orderby);
        $this->_column_headers = array($columns, $hidden, $sortable);

    }
}