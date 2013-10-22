<?php
/**
* Template Name:My Transaction Log
*/

if(is_user_logged_in()){
    global $current_user;
    
    $userInfo = get_user_meta( $current_user->ID );
    
    $user = get_userdata( $current_user->ID );
    $user_status = $user->user_status;
    
    if($user_status == 3)
    {
        //Goto My Profile Page
        addMessage('Please verify your email address.', 'warning');
        wp_redirect('/my-profile');
        exit;
    }
    
    
}else{
    wp_redirect(home_url());
    exit;
}
get_header();

$filterProduct = isset($_GET['product']) ? $_GET['product'] : null;
$filterSuite = isset($_GET['suite']) ? $_GET['suite'] : null;
$filterCase = isset($_GET['case']) ? $_GET['case'] : null;
$filterService = isset($_GET['service']) ? $_GET['service'] : null;
$filterAction = isset($_GET['action']) ? $_GET['action'] : null;
$filterPartyId = isset($_GET['partyid']) ? $_GET['partyid'] : null;
$filterDate = isset($_GET['date']) ? $_GET['date'] : null;
$filterCustomer = isset($_GET['customer']) ? $_GET['customer'] : null;

$esb = new ManageESB();

$limit = isset($_GET['limit']) ? intval($_GET['limit']) : getItemsPerPage('transactions');                    
setItemsPerPage($limit, 'transactions');

$orderBy = isset($_GET['orderby']) ? $_GET['orderby'] : 'date';
if(!in_array($orderBy, array('product', 'case', 'suite', 'test_outcome', 'audit', 'service', 'action', 'message', 'date', 'from')))
    $orderBy = 'product';
    
$order = isset($_GET['order']) ? $_GET['order'] : ($orderBy == 'date' ? 'desc' : 'asc');


$page = get_query_var('paged') ? get_query_var('paged') : 1;

$log_results = $esb->getUserTransactionLog($filterProduct, $filterSuite, $filterCase, $filterService, $filterAction, $filterPartyId, $filterDate, $filterCustomer, $page, $limit, $orderBy, $order);
$results = $log_results['data'];
$messages = $log_results['messages'];

$tProducts = $esb->getFilterOptionsForProduct($filterSuite, $filterCase, $filterService, $filterAction, $filterPartyId, $filterDate, $filterCustomer);
$tSuites = $esb->getFilterOptionsForSuite($filterProduct, $filterCase, $filterService, $filterAction, $filterPartyId, $filterDate, $filterCustomer);
$tCases = $esb->getFilterOptionsForCase($filterProduct, $filterSuite, $filterService, $filterAction, $filterPartyId, $filterDate, $filterCustomer);
$tServices = $esb->getFilterOptionsForService($filterProduct, $filterSuite, $filterCase, $filterAction, $filterPartyId, $filterDate, $filterCustomer);
$tActions = $esb->getFilterOptionsForAction($filterProduct, $filterSuite, $filterCase, $filterService, $filterPartyId, $filterDate, $filterCustomer);
$tPartyIDs = $esb->getFilterOptionsForPartId($filterProduct, $filterSuite, $filterCase, $filterService, $filterAction, $filterDate, $filterCustomer);
$tCustomers = getManagedCustomers();


$params = array();                 
    
$tbodyHTML = '';

if($filterProduct){ 
    $params[] = 'product=' .$filterProduct;
}
if($filterSuite){
    $params[] = 'suite=' . $filterSuite;
} 
if($filterCase){
    $params[] = 'case=' . $filterCase;
}
if($filterService){
    $params[] = 'service=' . $filterService;
}
if($filterAction){
    $params[] = 'action=' . $filterAction;
}
if($filterPartyId){
    $params[] = 'partyid=' . $filterPartyId;
}
if($filterDate){
    $params[] = 'date=' . $filterDate;
}
if($filterCustomer){
    $params[] = 'customer=' . $filterCustomer;
}
 

?>
<div class="content" id="my_transaction_log">
    <div class="column fifth left nopaddingleft nopaddingright sidebar">
        <?php get_sidebar('dashboard'); ?>
    </div>
    <div class="four_fifths right container">
        <div class="filter-box column">
            <div class="left right10"><label>Filter By:</label></div>
            <form name="filterForm" id="filterForm" method="get" action="<?php echo get_permalink()?>">
                <div class="left">
                    <div class="styled_select">
                        <label>Product / Service: <?php if($filterProduct != "" && $filterProduct != null){ ?><a href="#" class="clear-filter" title="Clear Filter">X</a><?php } ?></label>
                        <select name="product" id="product" autocomplete="off">
                            <option value="">- All -</option>
                          <?php foreach($tProducts as $t){ ?>                           
                            <option value="<?php echo !$t ? 0 : $t?>" <?php echo $filterProduct != "" && $t == intval($filterProduct) ? "selected='selected'" : "" ?>><?php echo !$t ? "Not assigned" : get_post_meta($t, 'product_name', true) ?></option>
                          <?php } ?>
                        </select>
                        
                    </div>
                    <div class="space10"></div>
                    <div class="styled_select">
                        <label>eb:Service: <?php if($filterService){ ?><a href="#" class="clear-filter" title="Clear Filter">X</a><?php } ?></label>
                        <select name="service" id="service" autocomplete="off">
                            <option value="">- All -</option>
                            <?php foreach($tServices as $s){ ?>
                            <option value="<?php echo $s?>" <?php echo $s == $filterService ? "selected='selected'" : "" ?>><?php echo $s ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="left">
                    <div class="styled_select">
                        <label>Test Suite: <?php if($filterSuite != "" && $filterSuite != null){ ?><a href="#" class="clear-filter" title="Clear Filter">X</a><?php } ?></label>
                        <select name="suite" id="suite" autocomplete="off">
                            <option value="">- All -</option>
                          <?php foreach($tSuites as $s){ ?>                           
                            <option value="<?php echo !$s->ID ? 0 : $s->ID?>" <?php echo $filterSuite != "" && $s->ID == intval($filterSuite) ? "selected='selected'" : "" ?>><?php echo !$s->NAME ? 'Not assigned' : $s->NAME?></option>                           
                          <?php } ?>
                        </select>
                    </div>
                    <div class="space10"></div>
                    <div class="styled_select">
                        <label>eb:Action: <?php if($filterAction){ ?><a href="#" class="clear-filter" title="Clear Filter">X</a><?php } ?></label>
                        <select name="action" id="action" autocomplete="off">
                            <option value="">- All -</option>
                            <?php foreach($tActions as $a){ ?>
                            <option value="<?php echo $a?>" <?php echo $a == $filterAction ? "selected='selected'" : "" ?>><?php echo $a ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="left">
                    <div class="styled_select">
                        <label>Test Case: <?php if($filterCase != "" && $filterCase != null){ ?><a href="#" class="clear-filter" title="Clear Filter">X</a><?php } ?></label>
                        <select name="case" id="case" autocomplete="off">
                            <option value="">- All -</option>
                          <?php foreach($tCases as $c){ ?>
                            <option value="<?php echo !$c->ID ? 0 : $c->ID?>" <?php echo $filterCase != "" && $c->ID == intval($filterCase) ? "selected='selected'" : "" ?>>
                                <?php echo $c->NAME == 'DEFAULT' ? 'Not Assigned' : $c->NAME ?>
                            </option>
                          <?php } ?>
                        </select>
                    </div>
                    <div class="space10"></div>
                    <div class="styled_select">
                        <label>eb:PartyID: <?php if($filterPartyId){ ?><a href="#" class="clear-filter" title="Clear Filter">X</a><?php } ?></label>
                        <select name="partyid" id="partyid" autocomplete="off">
                            <option value="">- All -</option>
                            <?php foreach($tPartyIDs as $p){ ?>
                            <option value="<?php echo $p?>" <?php echo $p == $filterPartyId ? "selected='selected'" : "" ?>><?php echo $p ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="last-div left">
                    <label>&nbsp;Date: <?php if($filterDate){ ?><a href="#" class="clear-filter" title="Clear Filter">X</a><?php } ?></label>
                    <input type="text" name="date" id="date" class="input datepicker" value="<?php echo $filterDate?>" />
                    <?php if($tCustomers){ ?>
                    <div class="space10"></div>
                    <label>&nbsp;Customer <?php if($filterCustomer){ ?><a href="#" class="clear-filter" title="Clear Filter">X</a><?php } ?></label>
                    <select name="customer" id="customer" class="select" style="width: 130px;">
                        <option value="">- All -</option>
                        <?php foreach($tCustomers as $c){ ?>
                        <option value="<?php echo $c->CUSTOMER_ID?>" <?php echo cp_selected($c->CUSTOMER_ID, $filterCustomer)?>><?php echo $c->CUSTOMER_NAME?></option>
                        <?php } ?>
                    </select>
                    <div class="space10"></div>                    
                    <?php }else{?>
                    <div class="space25"></div>                    
                    <?php } ?>
                    <a href="#" class="action-btn process-btn submit-btn" id="log-filter-btn"><span class="p"></span><span class="t">APPLY FILTER</span></a>
                </div>            
                <div class="clear"></div>
            </form>
        </div> 
        <div class="padding10">
            <a href="<?php echo get_site_url()?>?ct-message-action=<?php echo wp_create_nonce('trigger-message')?>" id="trigger-message-link" class="action-btn process-btn left"><span class="p"></span><span class="t">TRIGGER MESSAGE</span></a>
            <a href="#" id="delete-log-link" class="action-btn blue-delete-btn icon-btn right left5 has-tooltip"><span class="p"></span><span class="simple_tooltip radius6">Delete Selected Rows<span></span></span></a>
            <a href="#" id="edit-log-link" class="action-btn blue-edit-btn icon-btn right has-tooltip"><span class="p"></span><span class="simple_tooltip radius6">Edit Selected Rows<span></span></span></a>            
            <div class="clear"></div>
            <div class="space10"></div>
            <div class="grid-box table-box" id="log-result-table">               
               <div class="grid-box-body">
                   <div class="thead tr">
                       <div class="td td-chk tocenter"><input type="checkbox" class="chk-all" autocomplete="off" /></div>
                       <div class="td td-product td-sortable">
                           <a href="<?php echo get_permalink()?>?<?php echo implode("&", $params)?>&orderby=product&order=<?php echo $orderBy == 'product' && $order == 'asc' ? 'desc' : 'asc'?>" <?php if($orderBy == 'product'){ ?>class="<?php echo $order?>"<?php } ?>>Product Name <span class="sort"></span></a>
                       </div>
                       <div class="td td-case td-sortable">
                           <a href="<?php echo get_permalink()?>?<?php echo implode("&", $params)?>&orderby=case&order=<?php echo $orderBy == 'case' && $order == 'asc' ? 'desc' : 'asc'?>" <?php if($orderBy == 'case'){ ?>class="<?php echo $order?>"<?php } ?>>Test Case <span class="sort"></span></a>
                       </div>
                       <div class="td td-suite td-sortable">
                           <a href="<?php echo get_permalink()?>?<?php echo implode("&", $params)?>&orderby=suite&order=<?php echo $orderBy == 'suite' && $order == 'asc' ? 'desc' : 'asc'?>" <?php if($orderBy == 'suite'){ ?>class="<?php echo $order?>"<?php } ?>>Test Suite <span class="sort"></span></a>
                       </div>
                       <div class="td td-outcome td-two-lines tocenter td-sortable">
                           <a href="<?php echo get_permalink()?>?<?php echo implode("&", $params)?>&orderby=test_outcome&order=<?php echo $orderBy == 'test_outcome' && $order == 'asc' ? 'desc' : 'asc'?>" <?php if($orderBy == 'test_outcome'){ ?>class="<?php echo $order?>"<?php } ?>>Test<br />Outcome <span class="sort"></span></a>
                       </div>
                       <div class="td td-audit td-two-lines tocenter td-sortable">
                           <a href="<?php echo get_permalink()?>?<?php echo implode("&", $params)?>&orderby=audit&order=<?php echo $orderBy == 'audit' && $order == 'asc' ? 'desc' : 'asc'?>" <?php if($orderBy == 'audit'){ ?>class="<?php echo $order?>"<?php } ?>>Audit<br />Record <span class="sort"></span></a>
                       </div>                       
                       <div class="td td-convsn td-sortable">
                           <a href="<?php echo get_permalink()?>?<?php echo implode("&", $params)?>&orderby=message&order=<?php echo $orderBy == 'message' && $order == 'asc' ? 'desc' : 'asc'?>" <?php if($orderBy == 'message'){ ?>class="<?php echo $order?>"<?php } ?>>Conversation ID <span class="sort"></span></a>
                       </div>
                       <div class="td td-date td-sortable">
                           <a href="<?php echo get_permalink()?>?<?php echo implode("&", $params)?>&orderby=date&order=<?php echo $orderBy == 'date' && $order == 'asc' ? 'desc' : 'asc'?>" <?php if($orderBy == 'date'){ ?>class="<?php echo $order?>"<?php } ?>>Date/Time <span class="sort"></span></a>
                       </div>                       
<!--                       <div class="td td-to tocenter">To</div>-->
                       <div class="clear"></div>
                   </div>
                   <div class="tbody">
                   <?php if(!$results){ ?>
                       <div class="tr">
                           <div class="td td-full">No Transaction Found.</div>
                           <div class="clear"></div>
                       </div>
                   <?php }else{                       
                        foreach($results as $row){ 
                         ?>
                           <div class="tr">
                               <div class="td td-chk tocenter"><input type="checkbox" name="id[]" id="id<?php echo  $row->ID?>" value="<?php echo $row->ID?>" /></div>
                               <div class="td td-product">
                                   <a href="#" class="view-messages-link"></a>
                                   <?php if(!$row->PRODUCT_WP_ID){?>
                                   Not Assigned
                                   <?php }else{ ?>
                                   <a href="<?php echo get_permalink($row->PRODUCT_WP_ID)?>"><?php echo get_post_meta($row->PRODUCT_WP_ID, 'product_name', true)?></a>
                                   <?php } ?>
                               </div>
                               <div class="td td-case">
                                    <?php if(!$row->TEST_CASE_WP_ID) {?>
                                    Not Assigned
                                    <?php }else{ ?>
                                    <a href="<?php echo get_permalink($row->TEST_CASE_WP_ID)?>"><?php echo cp_wrap($row->TEST_CASE_ID, 10)?></a>
                                    <?php } ?>
                                   
                               </div>
                               <div class="td td-suite">
                                    <?php if($row->TEST_SUITE_ID){ ?>
                                    <a href="<?php echo get_permalink($row->TEST_SUITE_ID)?>"><?php echo cp_wrap($row->TEST_SUITE_NAME, 10)?></a>
                                    <?php }else if(!$row->TEST_SUITE_ID && $row->TEST_CASE_DB_ID){ ?>
                                    <?php 
                                        $tSuiteId = get_post_meta($row->TEST_CASE_DB_ID, 'test_suite', true); 
                                        $esb->updateTestSuiteID($row->ID, $tSuiteId);                                        
                                    ?>
                                    <a href="<?php echo get_permalink($tSuiteId)?>"><?php echo cp_wrap(get_post_meta($tSuiteId, 'ts_name', true), 10)?></a>
                                    <?php } ?>                                   
                               </div>
                               <div class="td td-outcome tocenter">
                                   <?php if($row->TEST_OUTCOME_CODE){ ?>
                                   <span class="status-<?php echo strtolower($row->TEST_OUTCOME_CODE) ?>"><?php echo $row->TEST_OUTCOME_LABEL?></span>                                   
                                   <?php }else{ ?>
                                   <span class="status-unverified">Not Performed</span>
                                   <?php } ?>                                   
                                   <br />
                                   <!--<a href="#" data-id="<?php echo $row->ID ?>" class="view-validation-log">View Log</a>                                   -->
                               </div>
                               <div class="td td-audit tocenter"><?php echo !$row->AUDIT_RECORD ? "No" : "Yes"?></div>
                               <div class="td td-convsn">
                                   <?php echo $row->CONVERSATION_ID ?>                                   
                               </div>
                               <div class="td td-date tocenter">
                                   <?php echo formatDate($row->CONVERSATION_TIMESTAMP, 'm/d/y')?><br />
                                   <?php echo date("H:i:s", strtotime($row->CONVERSATION_TIMESTAMP)) ?>
                               </div>                               
                               <div class="clear"></div> 
                               <?php if(isset($messages[$row->ID])){ ?>                               
                                   <div class="sub-table">
                                       <div class="table">
                                           <div class="thead tr">
                                               <div class="td td-from tocenter">From</div>
                                               <div class="td td-to tocenter">To</div>
                                               <div class="td td-service tocenter">Service</div>
                                               <div class="td td-action tocenter">Action</div>
                                               <div class="td td-message-outcome td-two-lines tocenter">Validation Status</div>
                                               <div class="td td-message-date">Date/Time</div>
                                               <div class="td td-message-view">View</div>
                                               <div class="clear"></div>
                                           </div>
                                           <div class="tbody">
                                             <?php foreach($messages[$row->ID] as $message) {?>
                                               <div class="tr">
                                                   <div class="td td-from"><?php echo cp_wrap($message->FROM_PARTY_ID, 11) ?></div>
                                                   <div class="td td-to"><?php echo cp_wrap($message->TO_PARTY_ID, 11)?></div>
                                                   <div class="td td-service"><?php echo cp_wrap($message->SERVICE, 21) ?></div>
                                                   <div class="td td-action"><?php echo $message->ACTION ?></div>
                                                   <div class="td td-message-outcome tocenter">
                                                       <?php if($message->MESSAGE_OUTCOME_CODE){ ?>
                                                       <span class="status-<?php echo strtolower($message->MESSAGE_OUTCOME_CODE) ?>"><?php echo $message->MESSAGE_OUTCOME_LABEL?></span>
                                                       <?php }else{ ?>
                                                       <span class="status-unverified">Not Processed</span>
                                                       <?php } ?>       
                                                       <br />    
                                                       <a href="#" data-id="<?php echo $message->ID ?>" class="view-message-validation-log">View Log</a>                                   
                                                   </div>
                                                   <div class="td td-message-date">
                                                       <?php echo formatDate($message->MESSAGE_TIMESTAMP, 'm/d/y')?><br />
                                                       <?php echo date("H:i:s", strtotime($message->MESSAGE_TIMESTAMP)) ?>
                                                   </div>
                                                   <div class="td td-message-view">                                                   
                                                      <a href="/message-envelope?id=<?php echo $message->ID?>" target="_blank">XML</a> 
                                                      | 
                                                      <a href="/message-envelope?id=<?php echo $message->ID?>&mode=html" target="_blank">HTML</a>
                                                   </div>
                                                   <div class="clear"></div>
                                               </div>
                                             <?php } ?>
                                           </div>
                                       </div>    
                                   </div>
                               <?php } ?>
                           </div>                       
                           
                        <?php 
                        }
                         
                       } 
                   ?>
                   </div>
               </div>
           </div>
           <div class="space10"></div>
           <?php if($log_results['total'] > 0) { ?>
           <div class="pagination-wrapper">
                <div class="pagination-limit">
                    <form method="get" action="<?php echo get_permalink()?>" name="pform">
                        Display # 
                        <select name="limit" class="select" onchange="document.pform.submit()">
                            <option value="10" <?php echo $limit == 10 ? 'selected="selected"' : ''?>>10</option>
                            <option value="20" <?php echo $limit == 20 ? 'selected="selected"' : ''?>>20</option>
                            <option value="50" <?php echo $limit == 50 ? 'selected="selected"' : ''?>>50</option>
                            <option value="100" <?php echo $limit == 100 ? 'selected="selected"' : ''?>>100</option>
                            <option value="-1" <?php echo $limit == -1 ? 'selected="selected"' : ''?>>All</option>
                        </select>
                        <?php if($filterProduct){ ?>
                        <input type="hidden" name="product" value="<?php echo $filterProduct?>" /> 
                        <?php } ?>
                        <?php if($filterSuite){ ?>
                        <input type="hidden" name="suite" value="<?php echo $filterSuite?>" /> 
                        <?php } ?>
                        <?php if($filterCase){ ?>
                        <input type="hidden" name="case" value="<?php echo $filterCase?>" /> 
                        <?php } ?>
                        <?php if($filterService){ ?>
                        <input type="hidden" name="service" value="<?php echo $filterService?>" /> 
                        <?php } ?>
                        <?php if($filterAction){ ?>
                        <input type="hidden" name="action" value="<?php echo $filterAction?>" /> 
                        <?php } ?>
                        <?php if($filterPartyId){ ?>
                        <input type="hidden" name="partyid" value="<?php echo $filterPartyId?>" /> 
                        <?php } ?>
                        <?php if($filterDate){ ?>
                        <input type="hidden" name="date" value="<?php echo $filterDate?>" /> 
                        <?php } ?>
                        <?php if($filterCustomer){ ?>
                        <input type="hidden" name="customer" value="<?php echo $filterCustomer?>" /> 
                        <?php } ?>
                    </form>
                </div>
                <div class="pagination">
                    <?php                
                    
                        $args = array(
                            'base'         => get_permalink() . '%_%?',
                            'format'       => 'page/%#%',
                            'total'        => ceil($log_results['total'] / $limit),
                            'current'      => $page,
                            'show_all'     => False,
                            'end_size'     => 5,
                            'mid_size'     => 5,
                            'prev_next'    => True,
                            'prev_text'    => __('« Previous'),
                            'next_text'    => __('Next »'),
                            'type'         => 'plain',
                            'add_args'     => false,
                            'add_fragment' => (count($params) > 0 ? '&' : '') . implode('&', $params)
                        ); 
                        echo paginate_links($args);
                    ?>
                </div>         
            </div>
            <div class="space15"></div>
            <?php } ?>
        </div>
    </div>
    <div class="clear"></div>
 <script type="text/javascript">
    jQuery(document).ready(function(){
        fixTdHeight(jQuery('#my_transaction_log .table-box'));
        
        //Edit Log
        jQuery('#edit-log-link').click(function(){
            var checked = jQuery('#log-result-table .tbody input[type="checkbox"]:checked').length;
            
            if(checked == 0)
            {
                alert("Please select a row.");
                return false;
            }else{
                var ids = new Array();
                jQuery('#log-result-table .tbody input[type="checkbox"]:checked').each(function(){
                    ids.push(this.value);
                })           
                jQuery('#my_transaction_log').append('<div class="loading1"></div>');
                jQuery('#my_transaction_log .loading1').show();
                
                jQuery.ajax({
                    url: '/',
                    data: {
                        'cp-action': '<?php echo wp_create_nonce('edit-transaction-log')?>',
                        'id': ids
                    },
                    type: 'post',
                    dataType: 'html',
                    success: function(rsp){                        
                        jQuery('#my_transaction_log .loading1').remove();
                        jQuery('#edit-transaction-log-box .tbody').html(rsp);                        
                        jQuery('#edit-transaction-log-box').showPopupBox({
                            closeWhenClickOveraly: false,
                            onClose: function(){
                                jQuery('#edit-transaction-log-box .tbody').html("");
                                jQuery('#edit-transaction-log-box .message').remove();
                            },
                            onLoad: function(){
                                fixTdHeight(jQuery('#edit-transaction-log-box .table-box'));
                            }
                        });
                    }
                })    
            }
            
            return false;
        })
        jQuery('.view-message-validation-log').click(function(){
            var link = jQuery(this);
            jQuery('#my_transaction_log').append('<div class="loading1"></div>');
            jQuery('#my_transaction_log .loading1').show();
            jQuery.ajax({
                url: '/',
                data: {
                    'cp-action': '<?php echo wp_create_nonce('view-validation-log')?>',
                    'id': link.attr('data-id')
                },
                type: 'post',
                dataType: 'html',
                success: function(rsp){                        
                    jQuery('#my_transaction_log .loading1').remove();
                    jQuery('#view-validation-log-box .tbody').html(rsp);                        
                    jQuery('#view-validation-log-box').showPopupBox({
                        closeWhenClickOveraly: false,
                        onClose: function(){
                            jQuery('#view-validation-log-box .tbody').html("");
                        },
                        onLoad: function(){
                            fixTdHeight(jQuery('#view-validation-log-box .table-box'));
                        }
                    });
                }
            })    
            
            return false;
        })
        
       jQuery('#editLogForm').submit(function(){
            jQuery('#edit-transaction-log-box .loading').show();
            jQuery.ajax({
                url: "/",
                type: 'post',
                data: jQuery('#editLogForm').serialize(),
                success: function(rsp){
                    jQuery('#edit-transaction-log-box .loading').hide();    
                    if(rsp == 'success')
                    {
                        jQuery('#edit-transaction-log-box .popup-box-footer').prepend('<p class="message success">Successfully Saved!</p>');
                        document.location.reload();
                    }else
                        jQuery('#edit-transaction-log-box .popup-box-footer').prepend(rsp);
                    
                    
                }
            })
            return false;
        })
        
        jQuery('#delete-log-link').click(function(){
            var checked = jQuery('#log-result-table .tbody input[type="checkbox"]:checked').length;            
            if(checked == 0)
            {
                alert("Please select a row.");
                return false;
            }else{
                var isAudit = false;
                
                var ids = new Array();
                jQuery('#log-result-table .tbody input[type="checkbox"]:checked').each(function(){
                    ids.push(this.value);
                    if(jQuery(this).parents('.tr').find('.td-audit').html() == 'Yes')
                        isAudit = true;
                })           
                if(isAudit && !confirm('Are you sure you want to delete? Some of the rows you have selected are marked as audit records'))
                {
                    return false;
                }
                
                jQuery('#my_transaction_log').append('<div class="loading1"></div>');
                jQuery('#my_transaction_log .loading1').show();
                
                jQuery.ajax({
                    url: '/',
                    data: {
                        'cp-action': '<?php echo wp_create_nonce('delete-transaction-log')?>',
                        'id': ids
                    },
                    type: 'post',
                    dataType: 'html',
                    success: function(rsp){                        
                        document.location.reload();
                    }
                })    
                return false;
            }
        });
        
        jQuery('#my_transaction_log .clear-filter').click(function(){
            jQuery(this).parent().parent().find('input, select').val('');
            jQuery('#filterForm').submit();
            return false;
        })
        
        jQuery('.chk-all').click(function(){
            jQuery('#log-result-table .tbody input[type="checkbox"]').prop('checked', this.checked);
        })
        jQuery('#log-result-table .view-messages-link').click(function(){
            jQuery(this).parents('.tr').find('.sub-table').animate({'height': 'toggle'});
            jQuery(this).toggleClass('expanded');
            return false;
        })
    })
 </script>       
</div> <!--end content-->

<div class="popup-box" id="edit-transaction-log-box" style="display: none; width: 900px">
<form name="editLogForm" id="editLogForm" action="">
    <div class="popup-box-header radius6 noradiusbottom">Edit Transaction Log</div>
    <div class="popup-box-content"> 
        <div class="space10"></div>
        <div class="grid-box table-box">               
           <div class="grid-box-body">
               <div class="thead tr">
                   <div class="td td-product">Product Name</div>
                   <div class="td td-case tocenter">Test Case</div>
                   <div class="td td-suite tocenter">Test Suite</div>
                   <div class="td td-outcome td-two-lines tocenter">Test<br />Outcome</div>
                   <div class="td td-audit td-two-lines tocenter">Audit<br />Record</div>
                   <div class="td td-convsn tocenter">Conversation ID</div>
                   <div class="td td-date tocenter">Date / Time</div>
<!--                   <div class="td td-to tocenter">To</div>-->
                   <div class="clear"></div>
               </div>
               <div class="tbody">
           
               </div>
           </div>
       </div>
       <div class="space10"></div>
    </div>
    <div class="popup-box-footer radius6 noradiustop">                                                    
        <a href="#make-claim-box" cp-type="inline"  class="action-btn process-btn submit-btn"><span class="p"></span><span class="t">SAVE</span></a>
        <a href="#make-claim-box" cp-type="inline"  class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Cancel</span></a>
        <div class="clear"></div>
    </div>
    <div class="loading"></div>
    <a class="close_btn"></a>      
    <input type="hidden" name="cp-action" value="<?php echo wp_create_nonce('save-transaction-log') ?>" />
</form>
</div>        

<div class="popup-box" id="view-validation-log-box" style="display: none; width: 410px">
    <div class="popup-box-header radius6 noradiusbottom">ESB validation log</div>
    <div class="popup-box-content"> 
        <div class="space10"></div>
        <div class="grid-box table-box">               
           <div class="grid-box-body">
               <div class="thead tr">
                   <div class="td td-phase">Phase Name</div>
                   <div class="td td-status tocenter">Status</div>
                   <div class="td td-result tocenter">Result</div>
                   <div class="clear"></div>
               </div>
               <div class="tbody">
                   
               </div>
           </div>
       </div>
       <div class="space10"></div>
    </div>
    <div class="popup-box-footer radius6 noradiustop">                                                    
        <a href="#" cp-type="inline"  class="action-btn cancel-btn close-popup-btn"><span class="p"></span><span class="t">Close</span></a>
        <div class="clear"></div>
    </div>
    <div class="loading"></div>
    <a class="close_btn"></a>      
</div> 



<?php
get_footer();
?>
