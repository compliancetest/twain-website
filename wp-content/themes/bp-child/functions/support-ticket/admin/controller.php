<?php
/**
* Manage Support Ticket Admin Section
*/

function ct_ticket_create_admin_menus()
{
    //Create Menus
    add_menu_page('ComplianceTest Support Tickets', 'Support Tickets', 'administrator', 'ct-tickets', 'ct_ticket_display_tickets');
    add_submenu_page('ct-tickets', 'Support Tickets', 'Tickets', 'administrator', 'ct-tickets', 'ct_ticket_display_tickets');
    add_submenu_page('ct-tickets', 'Support Tickets Categories', 'Categories', 'administrator', 'ct-tickets-categories', 'ct_ticket_display_categories');
    add_submenu_page('ct-tickets', 'Support Tickets Priorities', 'Priorities', 'administrator', 'ct-tickets-priorities', 'ct_ticket_priorities');
    add_submenu_page('ct-tickets', 'Support Tickets Statues', 'Statuses', 'administrator', 'ct-tickets-statuses', 'ct_ticket_statuses');
}

/**
* Save Ticket Category
* 
*/
function ct_save_ticket_category()
{
    global $wpdb, $ct_ticket_category;
    
    $name = $_POST['category-name'];
    $has_fee = isset($_POST['has-fee']) ? 1 : 0;
    $sort_number = $_POST['sort-number'];
    
    $id = isset($_POST['id']) ? $_POST['id'] : 0;
    
    $slug = sanitize_title($name);    
    
    $slug = ct_ticket_unique_category_slug($slug, $id);
    
    if(!$id)
    {
        //Add Category
        $ct_ticket_category->addCategory(array(
            'category_title'    =>      $name,
            'category_name'     =>      $slug,
            'has_fee'           =>      $has_fee,
            'sort_number'       =>      $sort_number,
            'tickets'           =>      0,
            'created_date'      =>      date('Y-m-d H:i:s')
        ));
    }else{
        $ct_ticket_category->updateCategory($id, array(
            'category_title'    =>      $name,
            'category_name'     =>      $slug,
            'has_fee'           =>      $has_fee,
            'sort_number'       =>      $sort_number
        ));
    }
    
    $ct_ticket_category->sortCategories();
    
    return true;
}

function ct_delete_ticket_category()
{
    global $wpdb, $ct_ticket_category;
    
    $id = $_REQUEST['id'];
    if(is_array($id))
    {
        $query = "DELETE FROM " . TABLE_TICKET_CATEGORIES . " WHERE id IN (" . implode(",", $id) . ")";
    }else{
        $query = $wpdb->prepare("DELETE FROM " . TABLE_TICKET_CATEGORIES . " WHERE id=%d", $id);
    }
    
    $wpdb->query($query);
    
    $ct_ticket_category->sortCategories();
    
    return;
}

/**
* Priority Functions
* 
*/
function ct_save_ticket_priority()
{
    global $wpdb, $ct_ticket_priority;
    
    if(!isset($_POST['id']))
    {
        $ct_ticket_priority->addPriority(array(
            'priority' => $_POST['priority'],
            'item_code' => $_POST['item_code'],
            'ttresponse' => $_POST['ttresponse'],
            'ttresolve' => $_POST['ttresolve'],
            'sort_number' => $_POST['sort-number'],
        ));
    }else{
        $ct_ticket_priority->updatePriority($_POST['id'], array(
            'priority' => $_POST['priority'],
            'item_code' => $_POST['item_code'],
            'ttresponse' => $_POST['ttresponse'],
            'ttresolve' => $_POST['ttresolve'],
            'sort_number' => $_POST['sort-number'],
        ));
    }
    
    $ct_ticket_priority->sortPriorities();
    
    return;
}

function ct_delete_ticket_priority()
{
    global $wpdb, $ct_ticket_priority;
    
    $id = $_REQUEST['id'];
    if(is_array($id))
    {
        $query = "DELETE FROM " . TABLE_TICKET_PRIORITIES . " WHERE id IN (" . implode(",", $id) . ")";
    }else{
        $query = $wpdb->prepare("DELETE FROM " . TABLE_TICKET_PRIORITIES . " WHERE id=%d", $id);
    }
    
    $wpdb->query($query);
    
    $ct_ticket_priority->sortPriorities();
    
    return;
}

/**
* Status Functions
* 
*/
function ct_save_ticket_status()
{
    global $wpdb, $ct_ticket_status;
    
    if(!isset($_POST['id']))
    {
        $ct_ticket_status->addStatus(array(
            'status' => $_POST['status'],
            'sort_number' => $_POST['sort-number'],
        ));
    }else{
        $ct_ticket_status->updateStatus($_POST['id'], array(
            'status' => $_POST['status'],
            'sort_number' => $_POST['sort-number'],
        ));
    }
    
    $ct_ticket_status->sortStatues();
    
    return;
}

function ct_delete_ticket_status()
{
    global $wpdb, $ct_ticket_status;
    
    $id = $_REQUEST['id'];
    if(is_array($id))
    {
        $query = "DELETE FROM " . TABLE_TICKET_STATUSES . " WHERE id IN (" . implode(",", $id) . ")";
    }else{
        $query = $wpdb->prepare("DELETE FROM " . TABLE_TICKET_STATUSES . " WHERE id=%d", $id);
    }
    
    $wpdb->query($query);
    
    $ct_ticket_status->sortStatues();
    
    return;
}