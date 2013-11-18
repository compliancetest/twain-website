<?php
/**
* Ticket Detail Page
*/

$ticket = getTicketById($ticket_id);
$user_id = get_current_user_id();
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
            $is_customer = $ticket->customer_id == $user_id ? true : false;
    ?>
        <div class="ticket-detail">
            <div class="left width10P">
                <a href="<?php bp_loggedin_user_link(); ?>">                                    
                    <?php bp_loggedin_user_avatar( 'type=thumb' ); ?><br />
                    <?php echo cp_get_user_display_name($user_id); ?>
                </a><br />
                <?php echo formatDate($ticket->created_date, "M d"); ?>
            </div>
            <div class="left width90P">
                <h3><?php echo apply_filters('the_title', $ticket->title)?></h3>
                <p class="ticket-info">
                    <span><label>Requested Date:</label> <?php echo formatDate($ticket->created_date, 'F d, Y h:i A'); ?></span>             
                    <span><label>Type: </label> <?php echo $ticket->category_title ?></span>                                            
                    <span><label>Priority: </label> <?php echo $ticket->priority_title ?></span>
                    <span><label>Status: </label> <?php echo $ticket->status_title ?></span>                
                </p>                        
                <?php echo apply_filters('the_content', $ticket->content)?>            
                <p class="ticket-info">
                    <span><label>Price: </label> <?php echo $ticket->price > 0 ? ('$' . $ticket->price . '/hr') : 'Free' ?></span>
                    <span><label>Time to Pay: </label> <?php echo $ticket->ttpay ?> hours</span>
                    <span><label>Time to Resolve: </label> <?php echo $ticket->ttresolve ?> hours</span>
                    <span><label>Time to Response: </label> <?php echo $ticket->ttresponse ?> hours</span>                
                </p>            
                <p id="term-actions">
                    <?php
                        if(!$ticket->term_accepted && $ticket->term_creator_id != $user_id){
                    ?>
                        <a href="/?ct-ticket-action=<?php echo wp_create_nonce('accept-term') ?>&id=<?php echo $ticket->id?>" class="action-btn process-btn right10" id="accept-term-link"><span class="p"></span><span class="t">Accept Term</span></a>
                    <?php
                        }
                    ?>
                    <a href="#" class="action-btn edit-btn" id="change-term-link"><span class="p"></span><span class="t">Change Term</span></a>
                </p>
            </div>
            <div class="clear"></div>
            <div id="change-term-contr" style="display: none;">
                <form name="changeTermForm" id="changeTermForm" method="post">
                    <div class="field-row">                        
                        <div class="field-cell">
                            <label>Time to Pay:</label>
                            <input type="text" name="ttpay" id="ttpay" value="<?php echo $ticket->ttpay?>"  class="input-text" /> hours
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="field-row">
                        <div class="field-cell">
                            <label>Time to Resolve:</label>
                            <input type="text" name="ttresolve" id="ttresolve" value="<?php echo $ticket->ttresolve?>" class="input-text" /> hours
                        </div>                   
                        <div class="clear"></div>     
                    </div>
                    <div class="field-row">
                        <div class="field-cell">
                            <label>Time to Response:</label>
                            <input type="text" name="ttresponse" id="ttresponse" value="<?php echo $ticket->ttresponse?>" class="input-text" /> hours
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="field-row">
                        <div class="field-cell">
                            <label>Comments(optional): </label>
                            <textarea cols="8" rows="5" class="textarea width70P" id="message-content" name="content"></textarea>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="btn-row">
                        <a href="#" class="action-btn process-btn submit-btn"><span class="p"></span><span class="t">Submit Changes</span></a>                        
                        <a href="#" class="action-btn cancel-btn"><span class="p"></span><span class="t">Cancel</span></a>                        
                    </div>
                    <input type="hidden" name="ct-ticket-action" value="<?php echo wp_create_nonce('change-ticket-term')?>" />
                    <input type="hidden" name="id" value="<?php echo $ticket->id ?>" />
                </form>
            </div>
        </div>
        <?php   
            $messages = getTicketMessagesByTicketId($ticket->id);
        ?>
        <div class="new-message">
            <form name="newMessageForm" id="newMessageForm">
                
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
