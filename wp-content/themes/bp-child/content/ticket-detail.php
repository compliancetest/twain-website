<?php
/**
* Ticket Detail Page
*/


?>
<?php if(!$ticket): ?> <!-- Ticket does not exist -->

<div class="column ticket-detail">       
    <a href="/my-support-tickets" class="back-to-supports">Back to <b>My Support Tickets</b></a>
    <h2>Ticket #<?php echo $ticket_id?></h2>
    <p>Ticket not found. The ticket id is not correct or not your ticket.</p>
</div>

<?php else: ?> <!-- Ticket exist -->

<?php    
    $is_support = ct_is_support($ticket_id);    
    
    $customerDetail = get_userdata($ticket->customer_id);
    
    makeTicketRead($ticket_id, $ticket->customer_id == $user_id ? 'customer' : 'support');    
?>

<div class="column ticket-detail"> 
    <a href="/my-support-tickets" class="back-to-supports">Back to <b>My Support Tickets</b></a>
    <a href="<?php echo bp_core_get_user_domain($ticket->customer_id); ?>" class="ticket-creator-avatar"><?php echo cp_get_user_avatar($ticket->customer_id, 'type=thumb&width=77&height=77' ); ?></a>
    <div class="left">
        <h2>Ticket #<?php echo $ticket_id?> (<?php echo apply_filters('the_title', $ticket->title)?>)</h2>
        <span class="ticket-creator">Raised by: <a href="<?php echo bp_core_get_user_domain($ticket->customer_id); ?>"><b><?php echo cp_get_user_display_name(intval($ticket->customer_id))?></b></a></span>
        <span class="ticket-priorities">
        <?php 
            if($ticket->status_id == TICKET_STATUS_RESOLVED)
            {
                echo "<span class='solved'><span class='ticket-status-solved-label'></span><b>Solved</b></span>";
            }else{
                echo "<span class='" . sanitize_title($ticket->priority_title) . "'><span class='ticket-priority ticket-priority-" . sanitize_title($ticket->priority_title) . "'></span><b>" . $ticket->priority_title . "</b></span>";
            }
        ?>
        </span>
        <p class="ticket-info">
            <span><label>Requested Date:</label> <b><?php echo formatDate($ticket->created_date, 'F d, Y h:i A'); ?></b></span>             
            <span><label>Type: </label> <b><?php echo $ticket->category_title ?></b></span>
            <span><label>Status: </label> 
            <b class="ticket-status-<?php echo sanitize_title($ticket->status_title)?>-label"><?php echo $ticket->status_title ?></b></span>            
        </p>    
    </div>
    <div class="clear"></div>
    <br />
    <div class="ticket-content">
        <?php echo apply_filters("the_content", $ticket->content); ?>
    </div>
    <?php if( $ticket->has_attachment ): ?>
        <div class="ticket-attachments">
            <?php $attachments = getAttachmentsByTicketId( $ticket_id ); ?>
            <?php foreach($attachments as $file): ?>
                <a href="<?php echo S3Wrapper::getAttachmentLink( $file->token,  pathinfo( $file->file_name, PATHINFO_EXTENSION ), 'ticket', true );?>"><?php echo $file->file_name?></a><br />
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<div class="ticket-term-detail">
    <p class="ticket-info" id="ticket-term-info">
        <span><b>Price/hr:</b> <?php echo $ticket->price > 0 ? '$'.$ticket->price : 'Free'?></span>
        <span><b>Effort:</b> <?php echo $ticket->ttpay?> hour<?php echo $ticket->ttpay > 1 ? 's' : ''?></span>
        <span><b>Time to Respond:</b> <?php echo $ticket->ttresponse?> hour<?php echo $ticket->ttresponse > 1 ? 's' : ''?></span>
        <span><b>Time to Resolve:</b> <?php echo $ticket->ttresolve?> hour<?php echo $ticket->ttresolve > 1 ? 's' : ''?></span>        
        <a href="#" class="action-btn edit-btn icon-btn right has-tooltip" id="change-term-link"><span class="p"></span><span class="t">Edit</span><span class="simple_tooltip"><span></span>Edit Term</span></a>
        <?php if(!$ticket->term_accepted && $ticket->term_creator_id != $user_id): ?>
        <a href="/?ct-ticket-action=<?php echo wp_create_nonce('accept-term') ?>&id=<?php echo $ticket->id?>" class="action-btn process-btn icon-btn right has-tooltip"><span class="p"></span><span class="t">Accept</span><span class="simple_tooltip"><span></span>Accept Term</span></a>
        <?php endif; ?>
    </p>
    <div id="change-term-contr" style="display: none;">
        <form name="changeTermForm" id="changeTermForm" method="post">
            <div class="term-row">     
                <?php if(is_super_admin() || $is_support): ?>
                <span class="item">
                    <b>Type:</b>
                    <?php
                        echo $ct_ticket_category->getCategoriesSelectboxHTML('category', 'ticket-category', $ticket->category_id, null);
                    ?>
                </span>  
                <?php endif; ?>                 
                <span class="item">
                    <b>Priority:</b>
                    <?php
                        echo $ct_ticket_priority->getPrioritiesSelectboxHTML('priority', 'ticket-priority', $ticket->priority_id, null);
                    ?>
                </span>                   
                <span class="item" id="term_price">
                    <b>Price/hr:</b> 
                    <span><?php echo $ticket->price > 0 ? '$'.$ticket->price : 'Free'?></span>
                </span>
                <span class="item" id="term_ttpay">
                    <b>Effort:</b>
                    <?php if($is_support): ?>
                     <input type="text" name="ttpay" id="ttpay" value="<?php echo $ticket->ttpay?>"  class="input-text" /> hours
                    <?php else: ?>
                     <span><?php echo $ticket->ttpay?></span> hours
                    <?php endif; ?>
                </span>
                <span class="item" id="term_ttresponse">
                    <b>Time to Respond:</b>
                    <?php if($is_support): ?>                     
                     <input type="text" name="ttresponse" id="ttresponse" value="<?php echo $ticket->ttresponse?>" class="input-text" /> hours
                    <?php else: ?>
                     <span><?php echo $ticket->ttresponse?></span> hours                    
                    <?php endif; ?>                    
                </span>
                <span class="item" id="term_ttresolve">
                    <b>Time to Resolve:</b>                                        
                    <?php if($is_support): ?>                     
                     <input type="text" name="ttresolve" id="ttresolve" value="<?php echo $ticket->ttresolve?>" class="input-text" /> hours
                    <?php else: ?>
                     <span><?php echo $ticket->ttresolve?></span> hours                    
                    <?php endif; ?>
                </span>
                
            </div>
            <div class="field-row">
                <label><b>Comments:</b><br />(Optional)</label>
                <textarea cols="8" rows="3" class="textarea" id="message-content" name="content" placeholder="Write a Message"></textarea>
                <div class="clear"></div>
            </div>
            <div class="btn-row">
                <a href="#" class="action-btn cancel-btn icon-btn has-tooltip left10"><span class="p"></span><span class="t">Cancel</span><span class="simple_tooltip"><span></span>Canel</span></a>                        
                <a href="#" class="action-btn process-btn submit-btn icon-btn has-tooltip"><span class="p"></span><span class="t">Submit</span><span class="simple_tooltip"><span></span>Submit</span></a>                
                <div class="clear"></div>
            </div>
            <input type="hidden" name="ct-ticket-action" value="<?php echo wp_create_nonce('change-ticket-term')?>" />
            <input type="hidden" name="id" value="<?php echo $ticket->id ?>" />
            <div class="loading loading-with-text"><div><b>SAVING YOUR DATA</b><span>Please wait...</span></div></div>
        </form>
    </div>
</div>
<?php 
    $messages = getTicketMessagesByTicketId($ticket->id); 
?>
<div class="column" id="ticket-messages-list">
    <h2>Comments</h2>
    <?php
    foreach($messages as $message){                 
    ?>
        <div class="ticket-message">
            <div class="left width10P">
                <a href="<?php echo bp_core_get_user_domain($message->sender); ?>">                                    
                    <?php echo cp_get_user_avatar($message->sender, 'type=thumb' ); ?>                    
                </a>
            </div>
            <div class="left width90P">
                <a href="<?php echo bp_core_get_user_domain($message->sender); ?>" class="left"><b><?php echo cp_get_user_display_name(intval($message->sender)); ?></b></a>                
                <span class="right"><b><?php echo formatDate($message->created_date, "Y-m-d h:i A"); ?></b></span>                
                <div class="clear"></div>
                <div class="space7"></div>
                <?php 
                    if($is_support)
                        $message_content = str_replace('[customer]', $customerDetail->first_name . " " . $customerDetail->last_name, $message->message);
                    else
                        $message_content = str_replace('[customer]', "you", $message->message);
                        
                    echo apply_filters("the_content", $message_content);
                ?>
                <?php if($message->has_attachment): ?>
                <div class="ticket-attachments">
                    <?php $attachments = getAttachmentsByMessageId($message->id); ?>
                    <?php foreach($attachments as $file): ?>
                    <a href="<?php echo S3Wrapper::getAttachmentLink( $file->token,  pathinfo( $file->file_name, PATHINFO_EXTENSION ), 'tickets', true );?>"><?php echo $file->file_name?></a><br />
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>                
            <div class="clear"></div>
        </div>
    <?php 
        }
    ?>
    <div class="ticket-message">
        <div id="new-message-wrap">
            <form name="newMessageForm" id="newMessageForm" method="post" enctype="multipart/form-data">
                <div class="field-row">
                    <div class="field-cell left width10P">
                        <a href="<?php bp_loggedin_user_link(); ?>">                                    
                            <?php echo cp_get_user_avatar($user_id, 'type=thumb' ); ?>
                        </a>
                    </div>
                    <div class="field-cell left width90P">
                        <textarea cols="8" rows="3" class="textarea" id="message-content" name="content" placeholder="Write a Message"></textarea>                        
                        <div class="clear"></div>
                        <div class="btn-row">
                            <div class="left">
                                <div class="attachments-wrap"></div>
                                <a href="#" id="add-attachment-link" class="small-plus-link">Add attachment</a>                                
                            </div>
                            <div class="right">
                                <?php
                                    if($ticket->customer_id == $user_id ):
                                        if($ticket->status_id != TICKET_STATUS_RESOLVED && $ticket->status_id != TICKET_STATUS_CLOSED):
                                ?>
                                <label>Please consider this request resolved. <input type="checkbox" name="resolved" value="1" /></label><br />
                                <?php
                                        endif;
                                    else: 
                                ?>
                                <div>
                                    <?php if($ticket->status_id != TICKET_STATUS_CLOSED): ?>
                                    <b>Update Status:</b>                                     
                                    <?php if($ticket->status_id != TICKET_STATUS_RESOLVED && $ticket->status_id != TICKET_STATUS_CLOSED): ?>
                                    <label class="left5"><input type="radio" name="status_change" value="in_progress" autocomplete="off" <?php echo cp_checked($ticket->status_id, TICKET_STATUS_IN_PROGRESS)?> /> In Progress</label>
                                    <label class="left5"><input type="radio" name="status_change" value="feedback" autocomplete="off" <?php echo cp_checked($ticket->status_id, TICKET_STATUS_FEEDBACK)?> /> Feedback</label>                                    
                                    <label class="left5"><input type="radio" name="status_change" value="resolved" autocomplete="off"  <?php echo cp_checked($ticket->status_id, TICKET_STATUS_RESOLVED)?> /> Resolved</label>
                                    <?php endif; ?>
                                    
                                    <label class="left5"><input type="radio" name="status_change" value="closed" autocomplete="off" <?php echo cp_checked($ticket->status_id, TICKET_STATUS_CLOSED)?> /> Closed</label>
                                    <?php endif; ?>
                                </div>
                                <?php
                                    endif;
                                ?>
                                <a href="#" class="action-btn process-btn submit-btn right"><span class="p"></span><span class="t">Send</span></a>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    <input type="hidden" name="ct-ticket-action" value="<?php echo wp_create_nonce('send-ticket-message')?>" />
                    <input type="hidden" name="id" value="<?php echo $ticket->id ?>" />
                </div>
            </form>
            <div class="loading loading-with-text"><div><b>SENDING YOUR MESSAGE</b><span>Please wait...</span></div></div>
        </div>
    </div>
</div>

<?php endif;?>