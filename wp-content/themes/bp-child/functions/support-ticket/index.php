<?php
/**
* Support Ticket Plugin
*/

//Define Ticket Tables
if(!defined('TABLE_TICKET_CATEGORIES'))
    define('TABLE_TICKET_CATEGORIES', $wpdb->prefix . 'ticket_categories');

if(!defined('TABLE_TICKET_PRIORITIES'))
    define('TABLE_TICKET_PRIORITIES', $wpdb->prefix . 'ticket_priorities');
    
if(!defined('TABLE_TICKETS'))
    define('TABLE_TICKETS', $wpdb->prefix . 'tickets');
    
if(!defined('TABLE_TICKET_STATUSES'))
    define('TABLE_TICKET_STATUSES', $wpdb->prefix . 'ticket_statuses');

if(!defined('TABLE_TICKET_STATUS_HISTORY'))
    define('TABLE_TICKET_STATUS_HISTORY', $wpdb->prefix . 'ticket_status_history');

if(!defined('TABLE_TICKET_MESSAGES'))
    define('TABLE_TICKET_MESSAGES', $wpdb->prefix . 'ticket_messages');

if(!defined('TABLE_TICKET_ATTACHMENTS'))
    define('TABLE_TICKET_ATTACHMENTS', $wpdb->prefix . 'ticket_attachments');

    
$dirs = wp_upload_dir();
define('TICKET_ATTACHMENTS_DIR', $dirs['basedir'] . "/ticket_attachments");

require_once(dirname(__FILE__) . "/functions.php");    

require_once(dirname(__FILE__) . "/class.category.php");    
require_once(dirname(__FILE__) . "/class.priority.php");    
require_once(dirname(__FILE__) . "/class.status.php");    

$ct_ticket_category = new CT_TicketCategory();
$ct_ticket_priority = new CT_TicketPriority();
$ct_ticket_status = new CT_TicketStatus();

$ct_ticket_status->defineStatusConstants();

require_once(dirname(__FILE__) . "/admin/index.php");    
require_once(dirname(__FILE__) . "/frontend/index.php");    


    
