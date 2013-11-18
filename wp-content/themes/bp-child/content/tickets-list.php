<?php
/**
* View Tickets List page
*/

$filterStatus = isset($_GET['status']) ? $_GET['status'] : null;
$filterCategory = isset($_GET['type']) ? $_GET['type'] : null;
$filterPriority = isset($_GET['priority']) ? $_GET['priority'] : null;

$limit = isset($_GET['limit']) ? intval($_GET['limit']) : getItemsPerPage('tickets');
setItemsPerPage($limit, 'tickets');

$orderBy = isset($_GET['orderby']) ? $_GET['orderby'] : 'last_updated';
if(!in_array($orderBy, array('id', 'title', 'created_date', 'last_updated', 'solved_date', 'status_id', 'category_id', 'priority_id')))
    $orderBy = 'last_updated';

$order = isset($_GET['order']) ? $_GET['order'] : ($orderBy == 'last_updated' ? 'desc' : 'asc');

$page = get_query_var('paged') ? get_query_var('paged') : 1;


$results = getUserTickets($filterCategory, $filterStatus, $filterPriority, $page, $limit, $orderBy, $order);
$tickets = $results['data'];
$totalItems = $results['total'];

$params = array();

if($filterStatus)
    $params[] = 'status=' . $filterStatus;
if($filterPriority)
    $params[] = 'priority=' . $filterPriority;
if($filterCategory)
    $params[] = 'type=' . $filterCategory;


?>
<div class="filter-box column">
    <div class="left right10"><label>Filter By:</label></div>
    <form name="filterForm" id="filterForm" method="get" action="<?php echo get_permalink()?>">
        <div class="left">
            <div class="styled_select">
                <label>Type: </label>
                <?php echo $ct_ticket_category->getCategoriesSelectboxHTML('type', 'ticket_type', $filterCategory); ?>
            </div>                    
            <?php if($filterCategory != "" && $filterCategory != null){ ?><a href="#" class="clear-filter" title="Clear Filter">X</a><?php } ?>
        </div>
        <div class="left">
            <div class="styled_select">
                <label>Status: </label>
                <?php echo $ct_ticket_status->getStatusesSelectboxHTML('status', 'ticket_status', $filterStatus); ?>
            </div>                    
            <?php if($filterStatus != "" && $filterStatus != null){ ?><a href="#" class="clear-filter" title="Clear Filter">X</a><?php } ?>
        </div>                
        <div class="left">
            <div class="styled_select">
                <label>Priority: </label>
                <?php echo $ct_ticket_priority->getPrioritiesSelectboxHTML('priority', 'ticket_priority', $filterPriority); ?>
            </div>                    
            <?php if($filterPriority != "" && $filterPriority != null){ ?><a href="#" class="clear-filter" title="Clear Filter">X</a><?php } ?>
        </div>                
        <a href="#" class="action-btn process-btn submit-btn" id="ticket-filter-btn"><span class="p"></span><span class="t">APPLY FILTER</span></a>                                
    </form>                
                    
    <div class="clear"></div>
</div>
<div id="tickets_list" class="padding10">
    <a href="<?php echo get_site_url()?>?ct-ticket-action=<?php echo wp_create_nonce('show-submit-form')?>" class="action-btn process-btn submit-btn" id="submit-ticket-btn"><span class="p"></span><span class="t">SUBMIT A REQUEST</span></a>
    <div class="ticket-priorities right">
        <?php
            $priorities = $ct_ticket_priority->getPriorities('sort_number', 'desc');
            foreach($priorities as $row){
                ?><span class="<?php echo sanitize_title($row->priority)?>"><span class="ticket-priority ticket-priority-<?php echo sanitize_title($row->priority)?>"></span><b><?php echo $row->priority?></b></span><?php
            }
        ?>
    </div>
    <div class="clear"></div>
    <div class="space10"></div>
    <div class="grid-box table-box" id="tickets_table">
       <div class="grid-box-body">
           <div class="thead tr">
               <div class="td td-ticket-id td-sortable tocenter">
                   <a href="<?php echo get_permalink()?>?<?php echo implode("&", $params)?>&orderby=id&order=<?php echo $orderBy == 'id' && $order == 'asc' ? 'desc' : 'asc'?>" <?php if($orderBy == 'id'){ ?>class="<?php echo $order?>"<?php } ?>>ID <span class="sort"></span></a>
               </div>
               <div class="td td-ticket-subject td-sortable">
                   <a href="<?php echo get_permalink()?>?<?php echo implode("&", $params)?>&orderby=title&order=<?php echo $orderBy == 'title' && $order == 'asc' ? 'desc' : 'asc'?>" <?php if($orderBy == 'title'){ ?>class="<?php echo $order?>"<?php } ?>>Subject <span class="sort"></span></a>
               </div>
               <div class="td td-ticket-requested td-sortable tocenter">
                   <a href="<?php echo get_permalink()?>?<?php echo implode("&", $params)?>&orderby=created_date&order=<?php echo $orderBy == 'created_date' && $order == 'asc' ? 'desc' : 'asc'?>" <?php if($orderBy == 'created_date'){ ?>class="<?php echo $order?>"<?php } ?>>Requested <span class="sort"></span></a>
               </div>
               <div class="td td-ticket-type td-sortable tocenter">
                   <a href="<?php echo get_permalink()?>?<?php echo implode("&", $params)?>&orderby=category_id&order=<?php echo $orderBy == 'category_id' && $order == 'asc' ? 'desc' : 'asc'?>" <?php if($orderBy == 'category_id'){ ?>class="<?php echo $order?>"<?php } ?>>Type <span class="sort"></span></a>
               </div>
               <div class="td td-ticket-status td-sortable tocenter">
                   <a href="<?php echo get_permalink()?>?<?php echo implode("&", $params)?>&orderby=status_id&order=<?php echo $orderBy == 'status_id' && $order == 'asc' ? 'desc' : 'asc'?>" <?php if($orderBy == 'status_id'){ ?>class="<?php echo $order?>"<?php } ?>>Status <span class="sort"></span></a>
               </div>
               <!--<div class="td td-ticket-solved td-sortable tocenter">
                   <a href="<?php echo get_permalink()?>?<?php echo implode("&", $params)?>&orderby=solved_date&order=<?php echo $orderBy == 'solved_date' && $order == 'asc' ? 'desc' : 'asc'?>" <?php if($orderBy == 'solved_date'){ ?>class="<?php echo $order?>"<?php } ?>>Solved <span class="sort"></span></a>
               </div>-->
               <div class="td td-ticket-updated td-sortable tocenter">
                   <a href="<?php echo get_permalink()?>?<?php echo implode("&", $params)?>&orderby=last_updated&order=<?php echo $orderBy == 'last_updated' && $order == 'asc' ? 'desc' : 'asc'?>" <?php if($orderBy == 'last_updated'){ ?>class="<?php echo $order?>"<?php } ?>>Updated <span class="sort"></span></a>
               </div>                               
               <div class="clear"></div>
           </div>
           <div class="tbody">
           <?php
               if($totalItems > 0){
                   foreach($tickets as $ticket)
                   {
                       $new_messages = false;
                       if($ticket->customer_id == get_current_user_id() && $ticket->customer_new_messages > 0)
                           $new_messages = $ticket->customer_new_messages;
                       else if($ticket->customer_id != get_current_user_id() && $ticket->support_new_messages > 0)
                           $new_messages = $ticket->support_new_messages; 
                       
           ?>
                        <div class="tr priority-<?php echo sanitize_title($ticket->priority_title) ?> <?php echo $new_messages > 0 ? 'has-new' : ''?>" onclick="document.location.href='/my-support-tickets/<?php echo $ticket->id?>'">
                            <div class="td td-ticket-id tocenter">
                                <?php echo str_pad($ticket->id, 8, 0, STR_PAD_LEFT)?>
                                <?php
                                    if($new_messages > 0)
                                    {
                                        echo '<span class="new">' . $new_messages . '</span>';
                                    }
                                ?>
                            </div>
                            <div class="td td-ticket-subject">
                                <?php 
                                    if($ticket->status_id == TICKET_STATUS_SOLVED)
                                    {
                                        echo "<span class='ticket-status-solved-label'></span>";
                                    }else{
                                        echo "<span class='ticket-priority ticket-priority-" . sanitize_title($ticket->priority_title) . "'></span>";
                                    }
                                ?>
                                <a href="/my-support-tickets/<?php echo $ticket->id?>"><?php echo $ticket->title?></a>
                            </div>
                            <div class="td td-ticket-requested"><?php echo formatDate($ticket->created_date, 'Y-m-d H:i') ?></div>
                            <div class="td td-ticket-type"><?php echo $ticket->category_title ?></div>
                            <div class="td td-ticket-status tocenter"><?php echo $ticket->status_title ?></div>                            
                            <div class="td td-ticket-updated"><?php echo formatDate($ticket->last_updated, 'Y-m-d H:i') ?></div>
                            <div class="clear"></div>
                        </div>
           <?php
                   }
           ?>
           <?php
               }else{
           ?>
               <div class="tr">
                   <div class="td td-full">No Ticket Found.</div>
                   <div class="clear"></div>
               </div>
           <?php
               }
           ?>
           </div>
       </div>
    </div>
    <div class="space10"></div>
   <?php if($totalItems > 0) { ?>
   <div class="pagination-wrapper">
        <div class="pagination-limit">
            <form method="get" action="<?php echo get_permalink()?>" name="pform">
                Display # 
                <select name="limit" class="select" onchange="document.pform.submit()">
                    <option value="10" <?php echo $limit == 10 ? 'selected="selected"' : ''?>>10</option>
                    <option value="20" <?php echo $limit == 20 ? 'selected="selected"' : ''?>>20</option>
                    <option value="50" <?php echo $limit == 50 ? 'selected="selected"' : ''?>>50</option>
                    <option value="100" <?php echo $limit == 100 ? 'selected="selected"' : ''?>>100</option>
                    <option value="-1" <?php echo $limit == -1 ? 'selected="selected"' : ''?>>All</option>
                </select>
                <?php if($filterStatus){ ?>
                <input type="hidden" name="status" value="<?php echo $filterStatus?>" /> 
                <?php } ?>
                <?php if($filterCategory){ ?>
                <input type="hidden" name="category" value="<?php echo $filterCategory?>" /> 
                <?php } ?>
                <?php if($filterCategory){ ?>
                <input type="hidden" name="priority" value="<?php echo $filterPriority?>" /> 
                <?php } ?>
                
            </form>
        </div>
        <div class="pagination">
            <?php                
            
                $args = array(
                    'base'         => get_permalink() . '%_%?',
                    'format'       => 'page/%#%',
                    'total'        => $limit > 0 ? ceil($totalItems / $limit) : 1,
                    'current'      => $page,
                    'show_all'     => False,
                    'end_size'     => 5,
                    'mid_size'     => 5,
                    'prev_next'    => True,
                    'prev_text'    => __('« Previous'),
                    'next_text'    => __('Next »'),
                    'type'         => 'plain',
                    'add_args'     => false,
                    'add_fragment' => (count($params) > 0 ? '&' : '') . implode('&', $params)
                ); 
                echo paginate_links($args);
            ?>
        </div>         
    </div>
    <div class="space15"></div>
    <?php } ?>
</div>
