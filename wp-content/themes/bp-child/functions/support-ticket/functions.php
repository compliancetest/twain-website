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

function ct_ticket_format_date($date)
{
    if(time() - strtotime($date) < 60 * 60 * 24)
        return date("m/d/y H:i", strtotime($date));
    else
        return date("m/d/y H:i", strtotime($date));
}