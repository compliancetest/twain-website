<?php

/**
 * Batch Jobs List Table
 */
require_once( THE_FUNCTION . '/classes/BatchJob.php' );
require_once(ABSPATH . "/wp-admin/includes/class-wp-list-table.php");

class CT_Batch_Jobs_Table extends WP_List_Table
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
            "id"              => __("ID"),
            "identifier"      => __("Identifier"),
            "access_key"      => __('Access Key'),
            "function_name"   => __("Function Name"),
            "is_active"       => __("Is Active"),
            "options"         => __("Options"),
            "actions"         => __("Actions")
        );
    }

    function get_sortable_columns($orderby)
    {
        return $sortable = array(
            "id" => array("ID", $orderby == 'ID')
        );
    }

    function column_default($item, $column_name)
    {
        switch($column_name)
        {
            case 'options':
                $batchJob = new BatchJob();
                $options = $batchJob->_getCronjobOptions($item->id);
                $html = '';
                foreach ($options AS $optionName => $option) {
                    $html .= '<b>'.$optionName.': </b>' . $option .'<br>';
                }
                return $html;
            case 'actions':
                if ($item->identifier == 'SERVER_CONTROL') {
                    return '<a style="color:green" href="'.home_url().'/?jobid='.$item->identifier.'&key='.$item->access_key.'&action=start&is_active='.wp_create_nonce('is_active').'">Start</a> / <a style="color:red" href="'.home_url().'/?jobid='.$item->identifier.'&key='.$item->access_key.'&action=stop&is_active='.wp_create_nonce('is_active').'">Stop</a>';
                } else {
                    return '<a style="color:green" href="'.home_url().'/?jobid='.$item->identifier.'&key='.$item->access_key.'&is_active='.wp_create_nonce('is_active').'">Run</a>';
                }
                $batchJob = new BatchJob();
                $options = $batchJob->_getCronjobOptions($item->id);
                $html = '';
                foreach ($options AS $optionName => $option) {
                    $html .= '<b>'.$optionName.': </b>' . $option .'<br>';
                }
                return $html;
            case 'is_active':
                return '<input type="checkbox" class="batch_status" '.($item->is_active == '1' ? 'checked="checked"' : '').'>';
            default:
                return $item->$column_name;
        }
    }

    function prepare_items()
    {
        global $wpdb;

        $paged = $this->get_pagenum();

        $orderby = isset($_REQUEST['orderby']) ? $_REQUEST['orderby'] : 'id';
        $order   = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'asc';

        // Query items for this table
        $query = "SELECT count(*) FROM wp_batch_jobs";
        $totalItems = $wpdb->get_var($query);

        $totalPages = ceil($totalItems / $this->per_pages);
        if($totalPages < $paged)
            $paged = $totalPages;
        if(empty($paged) || !is_numeric($paged) || $paged <= 0 )
        {
            $paged = 1;
        }
        if($paged > $totalPages)
            $paged = $totalPages;

        if($paged < 1)
            $paged = 1;

        $this->set_pagination_args(array(
            "total_items" => $totalItems,
            "total_pages" => $totalPages,
            "per_page"    => $this->per_pages
        ));
        
        $query  = "SELECT * FROM wp_batch_jobs AS c
                    ";
        $query .= " ORDER BY $orderby $order LIMIT " . (($paged - 1) * $this->per_pages) . ", " . $this->per_pages;
        $this->items = $wpdb->get_results($query);

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns($orderby);
        $this->_column_headers = array($columns, $hidden, $sortable);

    }
}