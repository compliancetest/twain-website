<?php
/**
* Support Ticket Plugin
*/

//Define Ticket Tables
if(!defined('TABLE_TICKET_CATEGORIES'))
    define('TABLE_TICKET_CATEGORIES', 'wp_ticket_categories');

if(!defined('TABLE_TICKET_PRIORITIES'))
    define('TABLE_TICKET_PRIORITIES', 'wp_ticket_priorities');
    
if(!defined('TABLE_TICKETS'))
    define('TABLE_TICKETS', 'wp_tickets');
    
if(!defined('TABLE_TICKET_STATUSES'))
    define('TABLE_TICKET_STATUSES', 'wp_ticket_statuses');
    

require_once(dirname(__FILE__) . "/functions.php");    

require_once(dirname(__FILE__) . "/class.category.php");    
require_once(dirname(__FILE__) . "/class.priority.php");    

$ct_ticket_category = new CT_TicketCategory();
$ct_ticket_priority = new CT_TicketPriority();

require_once(dirname(__FILE__) . "/admin/index.php");    


    
