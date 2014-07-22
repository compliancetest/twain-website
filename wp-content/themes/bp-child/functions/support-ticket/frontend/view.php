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
        //Getting Payments and subscribed test suites
        $query = $wpdb->prepare("SELECT s.*, p.post_title AS suite_title, up.monthly_fee, up.signup_fee, up.user_id AS purchaser_id FROM " . $wpdb->prefix . "users_subscriptions AS s 
                                 LEFT JOIN {$wpdb->posts} AS p ON p.ID=s.suite_id 
                                 LEFT JOIN {$wpdb->prefix}users_purchases AS up ON up.id=s.purchase_id 
                                 WHERE s.user_id=%d AND s.status = 'Active' ORDER BY suite_title", $user_id);
        $subscriptions = getUserSubscriptions($user_id);
        if(!$subscriptions)
        {
            $is_error = true;
            $error = "Sorry, you have not any active subscriptions.";
        }
        
    }
    
    if(!$is_error)
    {
        $cards = getUserCreditCards($user_id, true);  
        if(!$cards)
        {
            $is_error = true;
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
        $purchasedTokens = ct_get_prepurchased_tokens($user_id);
    ?>
    <div class="popup-box edit-ticket-box" id="submit-ticket-box" style="display: none; width: 700px;">
        <form name="ticketForm" id="ticketForm" action="" method="post" enctype="multipart/form-data">
            <div class="popup-box-header radius6 noradiusbottom">Submit a Request</div>        
                <div class="popup-box-content grid-box-body">                
                    <div class="field-row">
                        <div class="grid-cell">
                            <label>Test Suites:</label>
                            <select name="suite_id" id="suite_id" class="select">
                                <option value="">- Select -</option>
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

                    <?php if($purchasedTokens > 0): ?>
                    <div class="field-row" id="ticket-prepurchased-tokens-row">
                        <label>Prepurchased Tokens:</label>
                        <span><?php echo $purchasedTokens ?> Tokens</span>
                        <div class="clear"></div>
                    </div>
                    <?php endif; ?>
                    
<!--                    <div class="field-row">-->
<!--                        <div class="grid-cell">-->
<!--                            <label>Payment Methods:</label>-->
<!--                            <select name="ticket-card-id" id="ticket-card-id" class="select">-->
<!--                                <option value="">- Select -</option>-->
<!--                                --><?php //foreach($cards as $c): ?>
<!--                                <option value="--><?php //echo $c->id?><!--">--><?php //echo chunk_split($c->card_number, 4)?><!--(--><?php //echo chunk_split($c->nickname, 4)?><!--)</option>-->
<!--                                --><?php //endforeach; ?>
<!--                            </select>-->
<!--                        </div>-->
<!--                        <div class="clear"></div>-->
<!--                    </div>   -->
                    
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
                        <span class="left10">(1 Token = $<?php echo get_option('token_price')?>)</span>
                        <div class="clear"></div>
                    </div>
                    <div class="field-row">
                        <div class="attachments-wrap"></div>
                        <a href="#" id="add-attachment-link" class="small-plus-link">Add attachment</a>
                    </div>
                </div>

                <div class="popup-box-footer radius6 noradiustop">
                    <a href="#" class="action-btn process-btn submit-btn" id="submit-ticket-link"><span class="p"></span><span class="t">Submit Request</span></a>
                    <a href="#" class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>            
                    <div class="clear"></div>                    
                </div>
            <a class="close_btn"></a>                        
            <div class="loading loading-with-text"><div><b>SENDING YOUR MESSAGE</b><span>Please wait...</span></div></div>
            
            <input type="hidden" id="ct-ticket-create-action" value="<?php echo wp_create_nonce('submit-ticket')?>" />
            <input type="hidden" name="ct-ticket-action" id="ct-ticket-validate-action" value="<?php echo wp_create_nonce('validate-ticket')?>" />
            
            <input type="hidden" name="prepurchased-tokens" id="prepurchased-tokens" value="<?php echo $purchasedTokens?>" />
        </form>
    </div>
    <?php
    }
    
    exit;
}