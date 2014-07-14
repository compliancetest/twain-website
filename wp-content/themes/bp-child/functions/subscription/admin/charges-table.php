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
        global $wpdb;

        $paged = $this->get_pagenum();

        $orderby = isset($_REQUEST['orderby']) ? $_REQUEST['orderby'] : 'code';
        $order   = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'asc';

        // Query items for this table
        $query = "SELECT count(*) FROM {$wpdb->prefix}organisations_charge";
        $totalItems = $wpdb->get_var($query);

        $totalPages = ceil($totalItems / $this->per_pages);
        if($totalPages > $paged)
            $paged = $totalPages;

        $this->set_pagination_args(array(
            "total_items" => $totalItems,
            "total_pages" => $totalPages,
            "per_page"    => $this->per_pages
        ));

        $query  = "SELECT * FROM {$wpdb->prefix}organisations_charge ";
        $query .= " ORDER BY $orderby $order ";
        $query .= " LIMIT " . ($paged-1) * $this->per_pages .  ", {$this->per_pages} ";

        $this->items = $wpdb->get_results($query);

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns($orderby);
        $this->_column_headers = array($columns, $hidden, $sortable);

    }
}