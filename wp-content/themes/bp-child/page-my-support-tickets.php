<?php
  /**
  * Template Name: My Support Tickets
  */
if(!is_user_logged_in()){
    wp_redirect(home_url());
    exit;
} 

$user_id = get_current_user_id();

$ticket_id = get_query_var('ticket');

if($ticket_id)
{
    //Validate ticket id
    $ticket = getTicketById($ticket_id);
    if(!$ticket)    
    {
        addMessage("Invalid Request!", 'error');
        wp_redirect('/my-support-tickets');
        exit;
    }

    if (!is_super_admin() && !doesUserAdminInAnyCommunity(get_current_user_id()) && $ticket->customer_id != $user_id) //Permission Denied
    {
        addMessage("Invalid Request!", 'error');
        wp_redirect('/my-support-tickets');
        exit;
    }
}

get_header();

?>
<div class="content" id="my_tickets">
    <div class="dashboard-tabs">
        <?php get_sidebar('dashboard'); ?>
    </div>
    <div class="container">        
        <?php 
            if(!$ticket_id)
                include('content/tickets-list.php'); 
            else
                include('content/ticket-detail.php');
        ?>
    </div>
    <div class="clear"></div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('.clear-filter').click(function(){
            jQuery(this).closest('li').find('select').val('');
            jQuery('#filterForm').submit();
            return false;
        })
    })
</script>
<?php
get_footer();