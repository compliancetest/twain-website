<?php
/**
* Ticket Admin View 
*/

function ct_ticket_display_categories()
{
    global $ct_ticket_category;
    
    $listTable = new CT_Tickets_Category_List_Table();
    $listTable->prepare_items();
    
    ?>
    <div class="wrap">
        <h2>Categories</h2>
        <?php if( isset($_GET['ct-ticket-action']) && wp_verify_nonce($_GET['ct-ticket-action'], 'edit-ticket-category') ){ ?>
            <?php
                $category = $ct_ticket_category->getCategoryById($_GET['id']);
            ?>
            <p>
                <a href="admin.php?page=ct-tickets-categories">Back to the category list page</a>
            </p>
            <form id='editcategoryform' action="" method="post">
                <table class="form-table">
                    <tbody>
                        <tr class="form-field form-required">
                        <th scope="row" valign="top"><label for="category-name">Name</label></th>
                        <td>
                            <input name="category-name" id="category-name" type="text" value="<?php echo $category->category_title?>" size="40" maxlength="255" aria-required="true">
                            <p>The name is how it appears on your site.</p>
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row" valign="top">
                            <label for="has-fee">Has Fee</label>             
                        </th>
                        <td>
                            <input name="has-fee" id="has-fee" type="checkbox" value="1" <?php echo $category->has_fee ? 'checked="checked"' : ''?> style="width: auto;" />             
                            <p>Customers should pay some fee for this category if checked</p>
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row" valign="top"><label for="sort-number">Sort Number</label></th>
                        <td>
                            <input name="sort-number" id="sort-number" type="text" value="<?php echo $category->sort_number?>" size="40" />
                            <p>The number is the position on your site</p>
                        </td>
                    </tr>
                </tbody></table>
                <p class="submit">
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Category">
                </p>
                <input type="hidden" name="id" value="<?php echo $category->id?>" />
                <input type="hidden" name="ct-ticket-action" value="<?php echo wp_create_nonce('save-ticket-category')?>" />
            </form>
        <?php }else{ ?>
            <div id="col-right">
                <form id="list-filter" action="" method="post">
                    <input type="hidden" name="page" value="ct-tickets-categories" />
                    <?php
                        $listTable->display();
                    ?>
                </form>
            </div>
            <div id="col-left">
                <div class="col-wrap">
                    <div class="form-wrap">
                        <h3>Add New Category</h3>
                        <form id="addcategory" method="post" action="">
                            <div class="form-field form-required">
                                <label for="category-name">Name</label>
                                <input name="category-name" id="category-name" type="text" value="" size="40" maxlength="255" aria-required="true">
                                <p>The name is how it appears on your site.</p>
                            </div>
                            <div class="form-field form-required">
                                <label for="has-fee"><input name="has-fee" id="has-fee" type="checkbox" value="1" style="width: auto;" /> Has Fee</label>                            
                                <p>Customers should pay some fee for this category if checked</p>
                            </div>
                            <div class="form-field form-required">
                                <label for="sort-number">Sort Number</label>
                                <input name="sort-number" id="sort-number" type="text" value="<?php echo $listTable->get_pagination_arg('total_items') + 1?>" size="40" />
                                <p>The number is the position on your site</p>
                            </div>
                            <p class="submit">
                                <input type="submit" name="submit" id="submit" class="button button-primary" value="Add New Category">
                            </p>
                            <input type="hidden" name="ct-ticket-action" value="<?php echo wp_create_nonce('save-ticket-category')?>" />
                        </form>
                    </div>
                </div>            
            </div>        
        <?php } ?>
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function(){
            jQuery('#addcategory').submit(function(){
                if(jQuery('#category-name').val() == '')
                {
                    jQuery('#category-name').parent().addClass('form-invalid');
                    jQuery('#category-name').focus();
                    return false;
                }
            })
        })
    </script>
    <?php
    
    return true;
}


function ct_ticket_priorities()
{
    global $ct_ticket_priority;
    
    $listTable = new CT_Tickets_Priority_List_Table();
    $listTable->prepare_items();
    ?>
    <div class="wrap">
        <h2>Priorities</h2>
        <?php if( isset($_GET['ct-ticket-action']) && wp_verify_nonce($_GET['ct-ticket-action'], 'edit-ticket-priority') ){ ?>
            <?php
                $priority = $ct_ticket_priority->getpriorityById($_GET['id']);
            ?>
            <p>
                <a href="admin.php?page=ct-tickets-priorities">Back to the priority list page</a>
            </p>
            <form id='editpriorityform' action="" method="post">
                (1 Token = $<?php echo get_option('token_price')?>)
                <table class="form-table">
                    <tbody>
                        <tr class="form-field form-required">
                        <th scope="row" valign="top">
                            <label for="priority">Name</label>
                        </th>
                        <td>
                            <input name="priority" id="priority" type="text" value="<?php echo $priority->priority?>" size="40" maxlength="255" aria-required="true">
                            <p>The name is how it appears on your site.</p>
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row" valign="top">
                            <label for="price">Price(Token / Hour)</label>
                        </th>
                        <td>
                            <input name="price" id="price" type="text" value="<?php echo $priority->price?>" size="40" maxlength="255" aria-required="true">
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row" valign="top">
                            <label for="ttresponse">Time to Response(hours)</label>
                        </th>
                        <td>
                            <input name="ttresponse" id="ttresponse" type="text" value="<?php echo $priority->ttresponse?>" size="40" maxlength="255" aria-required="true">                                
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row" valign="top">
                            <label for="ttresponse">Time to Resolve(hours)</label>
                        </th>
                        <td>
                            <input name="ttresolve" id="ttresolve" type="text" value="<?php echo $priority->ttresolve?>" size="40" maxlength="255" aria-required="true">
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row" valign="top">
                            <label for="sort-number">Sort Number</label>
                        </th>
                        <td>
                            <input name="sort-number" id="sort-number" type="text" value="<?php echo $priority->sort_number?>" size="40" />
                        </td>
                    </tr>
                    
                </tbody></table>
                <p class="submit">
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="Save priority">
                </p>
                <input type="hidden" name="id" value="<?php echo $priority->id?>" />
                <input type="hidden" name="ct-ticket-action" value="<?php echo wp_create_nonce('save-ticket-priority')?>" />
            </form>
        <?php }else{ ?>
            <div id="col-right">
                <form id="list-filter" action="" method="post">
                    <input type="hidden" name="page" value="ct-tickets-categories" />
                    <?php
                        $listTable->display();
                    ?>
                </form>
            </div>
            <div id="col-left">
                <div class="col-wrap">
                    <div class="form-wrap">
                        <h3>Add New Priority</h3>
                        (1 Token = $<?php echo get_option('token_price')?>)
                        <form id="addpriority" method="post" action="">
                            <div class="form-field form-required">
                                <label for="priority">Name</label>
                                <input name="priority" id="priority" type="text" value="" size="40" maxlength="255" aria-required="true">
                                <p>The name is how it appears on your site.</p>
                            </div>
                            <div class="form-field form-required">
                                <label for="price">Price(Token Per Hour)</label>
                                <input name="price" id="price" type="text" value="" size="40" maxlength="255" aria-required="true">
                                <p>This is the fee to handle ticket.</p>
                            </div>
                            <div class="form-field form-required">
                                <label for="ttresponse">Time to Response(hours)</label>
                                <input name="ttresponse" id="ttresponse" type="text" value="" size="40" maxlength="255" aria-required="true">                                
                            </div>
                            <div class="form-field form-required">
                                <label for="ttresponse">Time to Resolve(hours)</label>
                                <input name="ttresolve" id="ttresolve" type="text" value="" size="40" maxlength="255" aria-required="true">                                
                            </div>
                            <div class="form-field form-required">
                                <label for="sort-number">Sort Number</label>
                                <input name="sort-number" id="sort-number" type="text" value="<?php echo $listTable->get_pagination_arg('total_items') + 1?>" size="40" />
                                <p>The number is the position of the priority on your site</p>
                            </div>
                            <p class="submit">
                                <input type="submit" name="submit" id="submit" class="button button-primary" value="Add New priority">
                            </p>
                            <input type="hidden" name="ct-ticket-action" value="<?php echo wp_create_nonce('save-ticket-priority')?>" />
                        </form>
                    </div>
                </div>            
            </div>        
        <?php } ?>
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function(){
            jQuery('#addpriority').submit(function(){
                var isValid = true;                
                if(jQuery('#addpriority #priority').val() == '')
                {                    
                    jQuery('#addpriority #priority').parent().addClass('form-invalid');
                    isValid = false;                    
                }
                if(jQuery('#addpriority #price').val() == '')
                {
                    jQuery('#addpriority #price').parent().addClass('form-invalid');
                    isValid = false;                    
                }
                if(jQuery('#addpriority #ttresponse').val() == '')
                {
                    jQuery('#addpriority #ttresponse').parent().addClass('form-invalid');
                    isValid = false;                    
                }
                if(jQuery('#addpriority #ttresolve').val() == '')
                {
                    jQuery('#addpriority #ttresolve').parent().addClass('form-invalid');
                    isValid = false;                    
                }
                
                return isValid;
            })
        })
    </script>
    <?php
}

function ct_ticket_statuses()
{
    global $ct_ticket_status;
    
    $listTable = new CT_Tickets_Status_List_Table();
    $listTable->prepare_items();
    ?>
    <div class="wrap">
        <h2>Statuses</h2>
        <?php if( isset($_GET['ct-ticket-action']) && wp_verify_nonce($_GET['ct-ticket-action'], 'edit-ticket-status') ){ ?>
            <?php
                $status = $ct_ticket_status->getStatusById($_GET['id']);
            ?>
            <p>
                <a href="admin.php?page=ct-tickets-statuses">Back to the status list page</a>
            </p>
            <form id='editstatusform' action="" method="post">
                <table class="form-table">
                    <tbody>
                        <tr class="form-field form-required">
                        <th scope="row" valign="top">
                            <label for="status">Name</label>
                        </th>
                        <td>
                            <input name="status" id="status" type="text" value="<?php echo $status->status?>" size="40" maxlength="255" aria-required="true">
                            <p>The name is how it appears on your site.</p>
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row" valign="top">
                            <label for="sort-number">Sort Number</label>
                        </th>
                        <td>
                            <input name="sort-number" id="sort-number" type="text" value="<?php echo $status->sort_number?>" size="40" />
                        </td>
                    </tr>
                    
                </tbody></table>
                <p class="submit">
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Status">
                </p>
                <input type="hidden" name="id" value="<?php echo $status->id?>" />
                <input type="hidden" name="ct-ticket-action" value="<?php echo wp_create_nonce('save-ticket-status')?>" />
            </form>
        <?php }else{ ?>
            <div id="col-right">
                <form id="list-filter" action="" method="post">
                    <input type="hidden" name="page" value="ct-tickets-categories" />
                    <?php
                        $listTable->display();
                    ?>
                </form>
            </div>
            <div id="col-left">
                <div class="col-wrap">
                    <div class="form-wrap">
                        <h3>Add New Status</h3>
                        <form id="addpriority" method="post" action="">
                            <div class="form-field form-required">
                                <label for="status">Name</label>
                                <input name="status" id="status" type="text" value="" size="40" maxlength="255" aria-required="true">
                                <p>The name is how it appears on your site.</p>
                            </div>                            
                            <div class="form-field form-required">
                                <label for="sort-number">Sort Number</label>
                                <input name="sort-number" id="sort-number" type="text" value="<?php echo $listTable->get_pagination_arg('total_items') + 1?>" size="40" />
                                <p>The number is the position of the status on your site</p>
                            </div>
                            <p class="submit">
                                <input type="submit" name="submit" id="submit" class="button button-primary" value="Add New Status">
                            </p>
                            <input type="hidden" name="ct-ticket-action" value="<?php echo wp_create_nonce('save-ticket-status')?>" />
                        </form>
                    </div>
                </div>            
            </div>        
        <?php } ?>
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function(){
            jQuery('#addpriority').submit(function(){
                var isValid = true;                
                if(jQuery('#addpriority #status').val() == '')
                {                    
                    jQuery('#addpriority #status').parent().addClass('form-invalid');
                    isValid = false;                    
                }
                
                return isValid;
            })
        })
    </script>
    <?php
}


function ct_ticket_display_tickets()
{
    if(isset($_GET['id']))
    {
        $ticket = getTicketById($_GET['id']);
        
        ?>
        <div class="wrap">
            <a href="<?php echo admin_url()?>admin.php?page=ct-tickets" class="back-to-supports right">Back to <b>Support Tickets</b></a>
            <br />
            <h2>Ticket #<?php echo $ticket->id?> (<?php echo apply_filters('the_title', $ticket->title)?>)</h2>
            <span class="ticket-creator">Rasied by: <a href="<?php echo bp_core_get_user_domain($ticket->customer_id); ?>"><b><?php echo cp_get_user_display_name(intval($ticket->customer_id))?></b></a></span>
            <br />
            <span class="ticket-priorities">
            Priority: 
            <?php 
                
                echo "<span class='" . sanitize_title($ticket->priority_title) . "'><span class='ticket-priority ticket-priority-" . sanitize_title($ticket->priority_title) . "'></span><b>" . $ticket->priority_title . "</b></span>";
                
            ?>
            </span>
            <br />
            <p class="ticket-info">
                <span><label>Requested Date:</label> <b><?php echo formatDate($ticket->created_date, 'F d, Y h:i A'); ?></b></span>   <br />          
                <span><label>Type: </label> <b><?php echo $ticket->category_title ?></b></span>  <br />
                <span><label>Status: </label>
                <b class="ticket-status-<?php echo sanitize_title($ticket->status_title)?>-label"><?php echo $ticket->status_title ?></b></span>            
            </p>
            <hr />
            <span><b>Price:</b> <?php echo $ticket->price > 0 ? '$' . $ticket->price . "/hr" : 'Free'?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span><b>Time to Pay:</b> <?php echo $ticket->ttpay?> hour<?php echo $ticket->ttpay > 1 ? 's' : ''?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span><b>Time to Resolve:</b> <?php echo $ticket->ttresolve?> hour<?php echo $ticket->ttresolve > 1 ? 's' : ''?></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <span><b>Time to Response:</b> <?php echo $ticket->ttresponse?> hour<?php echo $ticket->ttresponse > 1 ? 's' : ''?></span>
            <hr />
            <div class="ticket-content">
                <?php echo apply_filters("the_content", $ticket->content); ?>
            </div>
            <hr />
            <?php 
                $customer = get_userdata($ticket->customer_id);
                $messages = getTicketMessagesByTicketId($ticket->id); 
            ?>
            <h2>Comments</h2>
            <?php
            foreach($messages as $message){         
            ?>
                <div class="ticket-message" style="border-bottom: dotted 1px #333">
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
                        <?php echo apply_filters("the_content", str_replace('[customer]', $customer->first_name . " " . $customer->last_name, $message->message)); ?>                
                        <?php if($message->has_attachment): ?>
                        <div class="ticket-attachments">
                            <?php $attachments = getAttachmentsByMessageId($message->id); ?>
                            <?php foreach($attachments as $file): ?>
                            <a href="<?php echo get_site_url(null, null, 'https')?>/?ct-ticket-action=<?php echo wp_create_nonce('download-attachment')?>&file=<?php echo $file->token?>"><?php echo $file->file_name?></a><br />
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>                
                    <div class="clear"></div>
                </div>
            <?php 
                }
            ?>
        </div>
        <?php    
    }else{
        $listTable = new CT_Tickets_Ticket_List_Table();
        $listTable->prepare_items();
        
        ?>
        <style type="text/css">
            .ticket-priority b{
                font-size: 12px;
                line-height: 18px;
    /*            margin-left: 5px;*/
            }
            .ticket-priority span{
                border-width: 1px;
                border-style: solid;    
                height: 12px;
                width: 12px;
                border-radius: 9px;
                -moz-border-radius: 9px;
                -webkit-border-radius: 9px;
                display: inline-block;
                box-shadow: 0 1px 1px rgba(255, 255, 255, 0.7) inset;
                vertical-align: top;
                font-weight: bold;
            }
            .ticket-priority-normal b{
                color: #aaa;
            }
            .ticket-priority-normal span{            
                border-color: #aaa;
                background: #b7b7b7;
                background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#c4c4c4), to(#ababab));
                background: -webkit-linear-gradient(top, #c4c4c4, #ababab);
                background: -moz-linear-gradient(top, #c4c4c4, #ababab);
                background: -ms-linear-gradient(top, #c4c4c4, #ababab);
                background: -o-linear-gradient(top, #c4c4c4, #ababab);
            }
            .ticket-priority-high b{
                color: #f26522;
            }
            .ticket-priority-high span{            
                border-color: #f26522;
                background: #f67234;
                background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#f98047), to(#f36623));
                background: -webkit-linear-gradient(top, #f98047, #f36623);
                background: -moz-linear-gradient(top, #f98047, #f36623);
                background: -ms-linear-gradient(top, #f98047, #f36623);
                background: -o-linear-gradient(top, #f98047, #f36623);
            }
            .ticket-priority-urgent b{
                color: #c51e1e;
            }
            .ticket-priority-urgent span{            
                border-color: #c51e1e;
                background: #dc1516;
                background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#f00e0e), to(#c61d1d));
                background: -webkit-linear-gradient(top, #f00e0e, #c61d1d);
                background: -moz-linear-gradient(top, #f00e0e, #c61d1d);
                background: -ms-linear-gradient(top, #f00e0e, #c61d1d);
                background: -o-linear-gradient(top, #f00e0e, #c61d1d);
            }
        </style>
        <div class="wrap">
            <h2>Tickets</h2>
            <?php
                echo $listTable->display();
            ?>
        </div>
        <?php    
    }
    
    return true;
}

function ct_ticket_display_ticket_detail()
{
    
}