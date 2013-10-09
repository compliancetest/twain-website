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
                            <label for="price">Price(per hour)</label>
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
                        <form id="addpriority" method="post" action="">
                            <div class="form-field form-required">
                                <label for="priority">Name</label>
                                <input name="priority" id="priority" type="text" value="" size="40" maxlength="255" aria-required="true">
                                <p>The name is how it appears on your site.</p>
                            </div>
                            <div class="form-field form-required">
                                <label for="price">Price(per hour)</label>
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
    ?>
    <?php
    return true;
}

