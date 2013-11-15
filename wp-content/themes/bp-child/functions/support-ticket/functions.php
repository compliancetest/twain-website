<?php
/**
* Support Ticket Functions
*/

function ct_ticket_unique_category_slug($slug, $id)
{
    global $wpdb;
    
    $new_slug = $slug;
    
    $check_sql = "SELECT id FROM " . TABLE_TICKET_CATEGORIES . " WHERE category_name=%s AND id != %d";
    $query = $wpdb->prepare($check_sql, $new_slug, $id);
    $pid = $wpdb->get_var($query);    
    $idx = 2;
    while($pid)
    {
        $new_slug = $slug . "-" . $idx;
        $query = $wpdb->prepare($check_sql, $new_slug, $id);
        $pid = $wpdb->get_var($query);    
        $idx++;
    }
    
    return $new_slug;
}

/**
* Check Can Manage Customer
* 
* @param Int $customer_id
* @param Int $support_id
*/
function ct_can_manage_customer($customer_id, $support_id = null)
{
    global $wpdb;
    
    if(!$support_id)
        $support_id = get_current_user_id();
    
    $customers = getManagedCustomerWPIDs($support_id);
    
    return in_array($support_id, $customers);
    
}
