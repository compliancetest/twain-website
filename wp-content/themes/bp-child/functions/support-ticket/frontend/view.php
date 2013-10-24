<?php
/**
* FrontEnd View Functions
*/

function showSumitTicketBox()
{
    global $ct_ticket_category, $ct_ticket_priority, $ct_ticket_status;
    
    $user_id = get_current_user_id();
    
    //Getting Subscription IDs and Manageable customer IDs
    $esbIDs = getUserAllCustomerESBIDs($user_id);    
    
    if(!$esbIDs)
    {
    ?>
    <div class="popup-box edit-ticket-box" id="submit-ticket-box" style="display: none; width: 700px;">
        <form name="ticketForm" id="ticketForm" action="">
            <div class="popup-box-header radius6 noradiusbottom">Submit a Request</div>        
                <div class="popup-box-content grid-box-body">                    
                    <p class="message notice">Sorry, you need to purchase at least one subscription to create a support ticket.</p>
                </div>
                <div class="popup-box-footer radius6 noradiustop">
                    <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>            
                    <div class="clear"></div>
                </div>
            <a class="close_btn"></a>                        
            <input type="hidden" name="suite_id" value="<?php echo $suite->id?>" />
            <input type="hidden" name="_paymentnonce" value="<?php echo wp_create_nonce('direct_payment')?>" />
        </form>
    </div>
    <?php
    }else{
    ?>
    <div class="popup-box edit-ticket-box" id="submit-ticket-box" style="display: none; width: 700px;">
        <form name="ticketForm" id="ticketForm" action="" method="post">
            <div class="popup-box-header radius6 noradiusbottom">Submit a Request</div>        
                <div class="popup-box-content grid-box-body">
                    
                    <!--<p class="message notice">Sorry, you need to purchase at least one subscription to create a support ticket.</p>-->
                    <div class="field-row">
                        <div class="grid-cell">
                            <label>Subject:</label>
                            <input type="text" name="subject" id="subject" maxlength="50" value="" class="input" />
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
                            <label>Time to Response:</label>
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
                        <div class="clear"></div>
                    </div>                
                </div>
                
                <div class="popup-box-footer radius6 noradiustop">
                    <a href="#" class="action-btn process-btn submit-btn" id="submit-ticket-link"><span class="p"></span><span class="t">Submit Request</span></a>
                    <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>            
                    <div class="clear"></div>
                </div>
            <a class="close_btn"></a>                        
            <div class="loading loading-with-text"><div><b>SENDING YOUR MESSAGE</b><span>Please wait...</span></div></div>
            <input type="hidden" name="suite_id" value="<?php echo $suite->id?>" />
            <input type="hidden" name="ct-ticket-action" value="<?php echo wp_create_nonce('submit-ticket')?>" />
        </form>
    </div>
    <?php
    }
    
    exit;
}