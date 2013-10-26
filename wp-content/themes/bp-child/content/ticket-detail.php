<?php
/**
* Ticket Detail Page
*/

$ticket = getTicketById($ticket_id);

?>
<div class="column">
    <a href="/my-support-tickets" class="right"><b>Back to the Tickets Page</b></a>    
    <div class="clear"></div>        
    <h2>Ticket #<?php echo $ticket_id?></h2>
    <div class="space15"></div>
    <?php
        if(!$ticket)
        {
    ?>
        <p>Ticket not found. The ticket id is not correct or not your ticket.</p>
    <?php
        }else{
            $is_customer = $ticket->customer_id == get_current_user_id() ? true : false;
    ?>
        <div class="ticket-detail">
            <h3><?php echo apply_filters('the_title', $ticket->title)?></h3>
            <p class="ticket-info">
                <span><label>Requested Date:</label> <?php echo date("F d, Y h:i A", strtotime($ticket->created_date)); ?></span>             
                <span><label>Type: </label> <?php echo $ticket->category_title ?></span>                                            
                <span><label>Priority: </label> <?php echo $ticket->priority_title ?></span>
                <span><label>Status: </label> <?php echo $ticket->status_title ?></span>                
            </p>                        
            <?php echo apply_filters('the_content', $ticket->content)?>            
            <p class="ticket-info">
                <span><label>Price: </label> <?php echo $ticket->price > 0 ? ('$' . $ticket->price . '/hr') : 'Free' ?></span>
                <span><label>Time to Resolve: </label> <?php echo $ticket->ttresolve ?> hours</span>
                <span><label>Time to Response: </label> <?php echo $ticket->ttresponse ?> hours</span>                
            </p>
            <a href="#" class="action-btn process-btn"><span class="p"></span><span class="t">Change Term</span></a>
            <a href="#" class="action-btn process-btn"><span class="p"></span><span class="t">Accept Term</span></a>
            <div class="clear"></div>
        </div>
    <?php   
        $messages = getTicketMessagesByTicketId($ticket->id);
    ?>
        <div class="new-message">
            <form name="newMessageForm" id="newMessageForm">
                <div class="field-row" id="terms-row">
                    <div class="field-cell">
                        <label>Time to Response:</label>
                        <input type="text" name="ttresponse" id="ttresponse" value="<?php echo $ticket->ttresponse?>" class="input-text" /> hours
                    </div>
                    <div class="field-cell">
                        <label>Time to Resolve:</label>
                        <input type="text" name="ttresolve" id="ttresolve" value="<?php echo $ticket->ttresolve?>" class="input-text" /> hours
                    </div>
                    <div class="field-cell">
                        <label>Time to Pay:</label>
                        <input type="text" name="ttpay" id="ttpay" value="<?php echo $ticket->ttpay?>"  class="input-text" /> hours
                    </div>
                    <div class="field-cell">
                        <a href="#" class="action-btn process-btn"><span class="p"></span><span class="t">Submit Changes</span></a>                        
                    </div>
                </div>
                <div class="field-row">
                    <textarea cols="8" rows="5" class="textarea" id="message-content" name="content"></textarea>
                </div>
                <div class="btn-row">
                    
                </div>
            </form>
        </div>
    <?php            
        }
    ?>
    <div class="clear"></div>
</div>
<?php
