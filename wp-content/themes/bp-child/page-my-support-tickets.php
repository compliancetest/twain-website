<?php
  /**
  * Template Name: My Support Tickets
  */
if(!is_user_logged_in()){
    wp_redirect(home_url());
    exit;
}  
  get_header();
?>
<div class="content" id="my_tickets">
    <div class="column fifth left nopaddingleft nopaddingright sidebar">
        <?php get_sidebar('dashboard'); ?>
    </div>        
    <div class="four_fifths right container">
        <div class="filter-box column">
            <div class="left right10"><label>Filter By:</label></div>
            <form name="filterForm" id="filterForm" method="get" action="<?php echo get_permalink()?>">
                <div class="left">
                    <div class="styled_select">
                        <label>Type: </label>
                        <?php echo $ct_ticket_category->getCategoriesSelectboxHTML('ticket_type', 'ticket_type'); ?>
                    </div>                    
                </div>
                <div class="left">
                    <div class="styled_select">
                        <label>Status: </label>
                        <?php echo $ct_ticket_category->getCategoriesSelectboxHTML('ticket_type', 'ticket_type'); ?>
                    </div>                    
                </div>                
                <a href="#" class="action-btn process-btn submit-btn" id="ticket-filter-btn"><span class="p"></span><span class="t">APPLY FILTER</span></a>                                
            </form>                
            <a href="<?php echo get_site_url()?>?ct-ticket-action=<?php echo wp_create_nonce('show-submit-form')?>" class="action-btn process-btn submit-btn" id="submit-ticket-btn"><span class="p"></span><span class="t">SUBMIT A REQUEST</span></a>                
            <div class="clear"></div>
        </div>
        <div id="tickets_list" class="column">
            <div class="grid-box table-box">
               <div class="grid-box-body">
                   <div class="thead tr">
                       <div class="td td-ticket-id td-sortable">
                           <a href="<?php echo get_permalink()?>?<?php echo implode("&", $params)?>&orderby=id&order=<?php echo $orderBy == 'id' && $order == 'asc' ? 'desc' : 'asc'?>" <?php if($orderBy == 'id'){ ?>class="<?php echo $order?>"<?php } ?>>ID <span class="sort"></span></a>
                       </div>
                       <div class="td td-ticket-subject td-sortable">
                           <a href="<?php echo get_permalink()?>?<?php echo implode("&", $params)?>&orderby=title&order=<?php echo $orderBy == 'title' && $order == 'asc' ? 'desc' : 'asc'?>" <?php if($orderBy == 'title'){ ?>class="<?php echo $order?>"<?php } ?>>Subject <span class="sort"></span></a>
                       </div>
                       <div class="td td-ticket-requested td-sortable">
                           <a href="<?php echo get_permalink()?>?<?php echo implode("&", $params)?>&orderby=created_date&order=<?php echo $orderBy == 'created_date' && $order == 'asc' ? 'desc' : 'asc'?>" <?php if($orderBy == 'created_date'){ ?>class="<?php echo $order?>"<?php } ?>>Requested <span class="sort"></span></a>
                       </div>
                       <div class="td td-ticket-type td-sortable">
                           <a href="<?php echo get_permalink()?>?<?php echo implode("&", $params)?>&orderby=category_id&order=<?php echo $orderBy == 'category_id' && $order == 'asc' ? 'desc' : 'asc'?>" <?php if($orderBy == 'category_id'){ ?>class="<?php echo $order?>"<?php } ?>>Type <span class="sort"></span></a>
                       </div>
                       <div class="td td-ticket-status td-sortable">
                           <a href="<?php echo get_permalink()?>?<?php echo implode("&", $params)?>&orderby=status_id&order=<?php echo $orderBy == 'status_id' && $order == 'asc' ? 'desc' : 'asc'?>" <?php if($orderBy == 'status_id'){ ?>class="<?php echo $order?>"<?php } ?>>Status <span class="sort"></span></a>
                       </div>
                       <div class="td td-ticket-solved td-sortable">
                           <a href="<?php echo get_permalink()?>?<?php echo implode("&", $params)?>&orderby=solved_date&order=<?php echo $orderBy == 'solved_date' && $order == 'asc' ? 'desc' : 'asc'?>" <?php if($orderBy == 'solved_date'){ ?>class="<?php echo $order?>"<?php } ?>>Solved <span class="sort"></span></a>
                       </div>
                               
                       <div class="clear"></div>
                   </div>
               </div>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>
<?php
get_footer();