<?php
/**
* FrontEnd View Functions
*/

function showSumitTicketBox()
{
    global $ct_ticket_category, $ct_ticket_priority, $ct_ticket_status, $wpdb;
    
    $user_id = get_current_user_id();
     
    $is_error = false;
    $error = null;
    if(!ct_can_create_support_ticket($user_id))
    {
        $is_error = true;
        $error = 'Sorry, you need to purchase at least one subscription to create a support ticket.';
    }

    if(!$is_error)
    {
        $subscriptions = getUserSubscriptions($user_id);
        if(!$subscriptions)
        {
            $is_error = true;
            $error = "Sorry, you have not any active subscriptions.";
        }
        
    }
    
    if($is_error){
    ?>
    <div class="popup-box edit-ticket-box" id="submit-ticket-box" style="display: none; width: 700px;">
        <form name="ticketForm" id="ticketForm" action="">
            <div class="popup-box-header radius6 noradiusbottom">Submit a Request</div>        
                <div class="popup-box-content grid-box-body">                    
                    <p class="message notice"><?php echo $error ?></p>
                </div>
                <div class="popup-box-footer radius6 noradiustop">
                    <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>            
                    <div class="clear"></div>
                </div>
            <a class="close_btn"></a>                                    
        </form>
    </div>
    <?php
    }else{
    ?>
    <div class="popup-box edit-ticket-box" id="submit-ticket-box" style="display: none; width: 700px;">
        <form name="ticketForm" id="ticketForm" action="" method="post" enctype="multipart/form-data">
            <div class="popup-box-header radius6 noradiusbottom">Submit a Request</div>        
                <div class="popup-box-content grid-box-body">                
                    <div class="field-row">
                        <div class="grid-cell">
                            <label>Test Suite:</label>
                            <select name="suite_id" id="suite_id" class="select">
                                <option value="">- Select -</option>
                                <option value="general">General</option>
                                <?php foreach($subscriptions as $s): ?>
                                    <option value="<?php echo $s->suite_id?>"><?php echo $s->suite_title?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="clear"></div>
                    </div>                                                    
                    <div class="field-row">
                        <div class="grid-cell">
                            <label>Subject:</label>
                            <input type="text" name="subject" id="subject" maxlength="50" value="" class="input" /><br clear="all" />
                            <span class="field-desc">Maximum 50 characters</span>
                        </div>
                        <div class="clear"></div>
                    </div>                
                    <div class="field-row">
                        <div class="grid-cell">
                            <label>Your question:</label>
                            <textarea cols="5" rows="10" name="question" id="question" class="textarea input-error"></textarea>
                        </div>                
                        <div class="clear"></div>
                    </div>
                    <div class="field-row">
                        <label>Priority:</label>
                        <?php
                            echo $ct_ticket_priority->getPrioritiesSelectboxHTML('priority', 'ticket-priority', null, '- Select -');
                        ?>
                    </div>                         
                    <div class="field-row">
                        <label>Type:</label>
                        <?php
                            echo $ct_ticket_category->getCategoriesSelectboxHTML('category', 'ticket-category', null, '- Select -');
                            
                        ?>
                    </div>

                    <div class="field-row" id="ticket-time-row" style="display: none;">
                        <div class="grid_cell width50P">
                            <label>Time to Respond:</label>
                            <span id="ttresponse">24 hours</span>
                            <div class="clear"></div>
                        </div>
                        <div class="grid_cell width50P">
                            <label>Time to Resolve:</label>
                            <span id="ttresolve">2 hours</span>
                            <div class="clear"></div>
                        </div>
                        <div class="clear"></div>
                    </div>

                    <div class="field-row" id="ticket-price-row" style="display: none;">
                        <label>Price:</label>                    
                        <span id="ticket-price"></span>
<!--                        <span class="left10">(1 Token = $--><?php //echo get_option('token_price')?><!--)</span>-->
                        <div class="clear"></div>
                    </div>
                    <div class="field-row add-ticket-attachment-row">
                        <div class="attachments-wrap"></div>
                        <a href="#" id="add-attachment-link" class="small-plus-link">Add attachment</a>
                    </div>
                </div>

                <div class="popup-box-footer radius6 noradiustop">
                    <a href="#" class="action-btn process-btn" id="submit-ticket-link"><span class="p"></span><span class="t">Submit Request</span></a>
                    <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>            
                    <div class="clear"></div>                    
                </div>
            <a class="close_btn"></a>                        
            <div class="loading loading-with-text"><div><b>SENDING YOUR MESSAGE</b><span>Please wait...</span></div></div>
            
            <input type="hidden" id="ct-ticket-create-action" value="<?php echo wp_create_nonce('submit-ticket')?>" />
            <input type="hidden" name="ct-ticket-action" id="ct-ticket-validate-action" value="<?php echo wp_create_nonce('validate-ticket')?>" />
        </form>
    </div>
    <?php
    }
    
    exit;
}