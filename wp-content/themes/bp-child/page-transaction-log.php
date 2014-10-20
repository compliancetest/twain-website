<?php
/**
* Template Name:My Transaction Log
*/

if( ! function_exists( 'is_user_logged_in' ) || ! is_user_logged_in() ){
    wp_redirect(home_url());
    exit;
}
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

$html_render_limit = get_option('s3_xml_max_size');

$tSubscriptions = ct_get_user_viewable_subscriptions($user->ID);

//Set Default Subscription
if (is_super_admin() || ct_is_group_admin_or_support($user_id)) {
    $tOrganisations = ct_get_user_viewable_organisations();
    
    $default_organisation = isset($_GET['organisation']) ? htmlspecialchars($_GET['organisation']) : 'all';        
    
    $default_subscription = "all";
    $filterSubscription = isset($_GET['subscription']) ? htmlspecialchars($_GET['subscription']) : $default_subscription;

    if ($filterSubscription == 'all') {
        $filterOrganisation = $default_organisation;
    } else {
        foreach ($tSubscriptions as $s) {
            if ($s->id == $filterSubscription) {
                $filterOrganisation = $s->organisation_id;
                break;
            }
        }   
    }
} else {        
    if($tSubscriptions)
        $default_subscription = 'my';
    else
        $default_subscription = -1;
    
    $filterSubscription = isset($_GET['subscription']) ? htmlspecialchars($_GET['subscription']) : $default_subscription;
}



$filterProduct = isset($_GET['product']) ? htmlspecialchars($_GET['product']) : null;
$filterSuite = isset($_GET['suite']) ? htmlspecialchars($_GET['suite']) : null;
$filterCase = isset($_GET['case']) ? htmlspecialchars($_GET['case']) : null;
$filterService = isset($_GET['service']) ? htmlspecialchars($_GET['service']) : null;
$filterAction = isset($_GET['action']) ? htmlspecialchars($_GET['action']) : null;
$filterPartyId = isset($_GET['partyid']) ? htmlspecialchars($_GET['partyid']) : null;
$filterDate = isset($_GET['date']) ? htmlspecialchars($_GET['date']) : null;
$filterCustomer = isset($_GET['customer']) ? htmlspecialchars($_GET['customer']) : null;

$esb = new ManageESB();

$limit = isset($_GET['limit']) ? intval(htmlspecialchars($_GET['limit'])) : getItemsPerPage('transactions');
setItemsPerPage($limit, 'transactions');

$orderBy = isset($_GET['orderby']) ? htmlspecialchars($_GET['orderby']) : 'date';
if(!in_array($orderBy, array('product', 'case', 'suite', 'test_outcome', 'audit', 'service', 'action', 'message', 'date', 'from')))
    $orderBy = 'product';
    
$order = isset($_GET['order']) ? htmlspecialchars($_GET['order']) : ($orderBy == 'date' ? 'desc' : 'asc');


$page = get_query_var('paged') ? get_query_var('paged') : 1;

$esb->prepareTransactionWhereQuery(isset($filterOrganisation) ? $filterOrganisation : null, $filterSubscription, $filterProduct, $filterSuite, $filterCase, $filterService, $filterAction, $filterPartyId, $filterDate, $filterCustomer);

$log_results = $esb->getUserTransactionLog($page, $limit, $orderBy, $order);
var_dump($log_results);
$results = $log_results['data'];
$messages = $log_results['messages'];



$tProducts = $esb->getFilterOptionsForProduct();
$tSuites = $esb->getFilterOptionsForSuite();
$tCases = $esb->getFilterOptionsForCase();
$tServices = $esb->getFilterOptionsForService();
$tActions = $esb->getFilterOptionsForAction();
$tPartyIDs = $esb->getFilterOptionsForPartId();

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
if( isset( $filterSubscription ) ){
    $params[] = 'subscription=' . $filterSubscription;
}
if( isset( $filterOrganisation ) ){
    $params[] = 'organisation=' .$filterOrganisation ;
}
 
get_header();
?>
<div class="content" id="my_transaction_log">
    <div class="dashboard-tabs">
        <?php get_sidebar('dashboard'); ?>
    </div>
    <div class="container">
        <div class="filter-box column">
            <div class="left right10"><label>Filter By:</label></div>
            <form name="filterForm" id="filterForm" method="get" action="<?php echo get_permalink()?>">
                <div class="left">
                    <?php
                        if (isset($tOrganisations)) {
                    ?>
                        <div class="styled_select">
                            <label>Organisation: <?php if($filterOrganisation != "all"){ ?><a href="#" class="clear-filter" title="Clear Filter">X</a><?php } ?></label>
                            <select name="organisation" id="organisation" autocomplete="off">
                                <option value="all">- All -</option>
                                <?php if( is_iterable( $tOrganisations ) ):?>
                                  <?php foreach($tOrganisations as $o){ ?>
                                    <option value="<?php echo $o->id?>" <?php echo $filterOrganisation != "" && $o->id == intval($filterOrganisation) ? "selected='selected'" : "" ?>><?php echo $o->organisation_name ?></option>
                                  <?php } ?>
                                <?php endif;?>
                            </select>
                            
                        </div>
                        <div class="space10"></div>                        
                        <div class="styled_select">
                            <label>Subscription: <?php if($filterSubscription != "all"){ ?><a href="#" class="clear-filter" title="Clear Filter">X</a><?php } ?></label>
                            <select name="subscription" id="subscription" autocomplete="off">     
                                <option value="all">- All -</option>
                                <option value="my" <?php echo $filterSubscription != "" && $filterSubscription == 'my' ? "selected='selected'" : "" ?>>- My Subscriptions -</option>
                                <?php if( is_iterable( $tSubscriptions ) ):?>
                                    <?php foreach($tSubscriptions as $s){ ?>
                                        <?php
                                            if ($filterOrganisation != 'all' && $s->organisation_id != $filterOrganisation) {
                                                continue;
                                            }
                                        ?>
                                    <option value="<?php echo $s->id?>" data-org-id="<?php echo $s->organisation_id?>" <?php echo $filterSubscription != "" && $s->id == intval($filterSubscription) ? "selected='selected'" : "" ?>><?php echo $s->nickname ?></option>
                                    <?php } ?>
                                <?php endif;?>
                            </select>
                            
                            <select id="all_subscriptions" autocomplete="off" style="display: none;">
                                <option value="all">- All -</option>
                                <option value="my">- My Subscriptions -</option>
                                <?php if( is_iterable( $tSubscriptions ) ):?>
                                  <?php foreach($tSubscriptions as $s){ ?>
                                    <option value="<?php echo $s->id?>" data-org-id="<?php echo $s->organisation_id?>"><?php echo stripslashes( $s->nickname )?></option>
                                  <?php } ?>
                                <?php endif;?>
                            </select>
                            
                        </div>
                    <?php
                        } else {
                    ?>
                        <div class="styled_select">
                            <label>Subscription: <?php if($filterSubscription != "my" && $filterSubscription != "all"){ ?><a href="#" class="clear-filter" title="Clear Filter">X</a><?php } ?></label>
                            <select name="subscription" id="subscription" autocomplete="off">
                                <option value="all">- All -</option>
                                <option value="my"  <?php echo $filterSubscription != "" && $filterSubscription == 'my' ? "selected='selected'" : "" ?>>- My Subscriptions -</option>
                                <?php if( is_iterable( $tSubscriptions ) ):?>
                                  <?php foreach($tSubscriptions as $s){ ?>
                                    <option value="<?php echo $s->id?>" data-org-id="<?php echo $s->organisation_id?>" <?php echo $filterSubscription != "" && $s->id == intval($filterSubscription) ? "selected='selected'" : "" ?>><?php echo $s->nickname ?></option>
                                  <?php } ?>
                                <?php endif;?>
                            </select>
                        </div>
                    <?php   
                        }
                    ?>
                    
                    <div class="space10"></div>
                    <div class="styled_select">
                        <label>Date: <?php if($filterDate){ ?><a href="#" class="clear-filter" title="Clear Filter">X</a><?php } ?></label>
                        <input type="text" name="date" id="date" class="input datepicker" value="<?php echo !$filterDate  ?  '' : $filterDate; ?>" />
                    </div>
                </div>
                <div class="left">
                    <div class="styled_select">
                        <label>Product / Service: <?php if($filterProduct != "" && $filterProduct != null){ ?><a href="#" class="clear-filter" title="Clear Filter">X</a><?php } ?></label>
                        <select name="product" id="product" autocomplete="off">
                            <option value="">- All -</option>
                            <?php if( is_iterable( $tProducts ) ):?>
                              <?php foreach($tProducts as $t){ ?>
                                <option value="<?php echo !$t->PRODUCT_WP_ID ? 0 : $t->PRODUCT_WP_ID?>" <?php echo $filterProduct != "" && $t->PRODUCT_WP_ID == intval($filterProduct) ? "selected='selected'" : "" ?>><?php echo !$t->PRODUCT_WP_ID ? "Not assigned" : $t->PRODUCT_TITLE ?></option>
                              <?php } ?>
                            <?php endif;?>
                        </select>
                        
                    </div>
                    <div class="space10"></div>
                    <div class="styled_select">
                        <label>eb:Service: <?php if($filterService){ ?><a href="#" class="clear-filter" title="Clear Filter">X</a><?php } ?></label>
                        <select name="service" id="service" autocomplete="off">
                            <option value="">- All -</option>
                            <?php if( is_iterable( $tServices ) ):?>
                                <?php foreach($tServices as $s){ ?>
                                    <option value="<?php echo $s?>" <?php echo $s == $filterService ? "selected='selected'" : "" ?>><?php echo $s ?></option>
                                <?php } ?>
                            <?php endif;?>
                        </select>
                    </div>
                </div>
                <div class="left">
                    <div class="styled_select">
                        <label>Test Suite: <?php if($filterSuite != "" && $filterSuite != null){ ?><a href="#" class="clear-filter" title="Clear Filter">X</a><?php } ?></label>
                        <select name="suite" id="suite" autocomplete="off">
                            <option value="">- All -</option>
                            <?php if( is_iterable( $tSuites ) ):?>
                              <?php foreach($tSuites as $s){ ?>
                                <option value="<?php echo !$s->ID ? 0 : $s->ID?>" <?php echo $filterSuite != "" && $s->ID == intval($filterSuite) ? "selected='selected'" : "" ?>>
                                    <?php echo !$s->NAME ? 'Not assigned' : $s->NAME?></option>
                              <?php } ?>
                            <?php endif;?>
                        </select>
                    </div>
                    <div class="space10"></div>
                    <div class="styled_select">
                        <label>eb:Action: <?php if($filterAction){ ?><a href="#" class="clear-filter" title="Clear Filter">X</a><?php } ?></label>
                        <select name="action" id="action" autocomplete="off">
                            <option value="">- All -</option>
                            <?php if( is_iterable( $tActions ) ):?>
                                <?php foreach($tActions as $a){ ?>
                                    <option value="<?php echo $a?>" <?php echo $a == $filterAction ? "selected='selected'" : "" ?>><?php echo $a ?></option>
                                <?php } ?>
                            <?php endif;?>
                        </select>
                    </div>
                </div>
                <div class="left">
                    <div class="styled_select">
                        <label>Test Case: <?php if($filterCase != "" && $filterCase != null){ ?><a href="#" class="clear-filter" title="Clear Filter">X</a><?php } ?></label>
                        <select name="case" id="case" autocomplete="off">
                            <option value="">- All -</option>
                            <?php if( is_iterable( $tCases ) ):?>
                                  <?php foreach($tCases as $c){ ?>
                                    <option value="<?php echo !$c->ID ? 0 : $c->ID?>" <?php echo $filterCase != "" && $c->ID == intval($filterCase) ? "selected='selected'" : "" ?>>
                                        <?php
                                            if($c->NAME == 'DEFAULT')
                                            {
                                                echo 'Not Assigned';
                                            }else{
                                                echo str_replace("_V", " v", $c->NAME);
                                            }
                                        ?>
                                    </option>
                                  <?php } ?>
                            <?php endif;?>
                        </select>
                    </div>
                    <div class="space10"></div>
                    <div class="styled_select">
                        <label>eb:PartyID: <?php if($filterPartyId){ ?><a href="#" class="clear-filter" title="Clear Filter">X</a><?php } ?></label>
                        <select name="partyid" id="partyid" autocomplete="off">
                            <option value="">- All -</option>
                            <?php if( is_iterable( $tPartyIDs ) ):?>
                                <?php foreach($tPartyIDs as $p){ ?>
                                    <option value="<?php echo $p?>" <?php echo $p == $filterPartyId ? "selected='selected'" : "" ?>><?php echo $p ?></option>
                                <?php } ?>
                            <?php endif;?>
                        </select>
                    </div>
                </div>
                <div class="last-div right right13">                                        
                    <div class="space10"></div>
                    <a href="#" class="action-btn process-btn submit-btn" id="log-filter-btn"><span class="p"></span><span class="t">APPLY FILTER</span></a>
                </div>            
                <div class="clear"></div>
            </form>
        </div> 
        <div class="padding10">
            <a href="<?php echo get_site_url()?>?ct-message-action=<?php echo wp_create_nonce('trigger-message')?>" id="trigger-message-link" class="action-btn process-btn left" onclick="javascript: void(0)"><span class="p"></span><span class="t">TRIGGER MESSAGE</span></a>
            <a href="<?php echo get_site_url()?>?ct-message-action=<?php echo wp_create_nonce('upload-message')?>" id="upload-message-link" class="action-btn process-btn left left10" onclick="javascript: void(0)"><span class="p"></span><span class="t">UPLOAD MESSAGE</span></a>
            
            <a href="#" id="delete-log-link" class="action-btn delete-btn icon-btn right left5 has-tooltip"><span class="p"></span><span class="simple_tooltip radius6">Delete Selected Rows<span></span></span></a>
            <a href="#" id="edit-log-link" class="action-btn edit-btn icon-btn right has-tooltip"><span class="p"></span><span class="simple_tooltip radius6">Edit Selected Rows<span></span></span></a>            
            <div class="clear"></div>
            <div class="space10"></div>
            <div class="grid-box table-box" id="log-result-table">               
               <div class="grid-box-body">
                   <div class="thead tr">
                       <div class="td td-chk tocenter"><input type="checkbox" class="chk-all" autocomplete="off" /></div>
                       <div class="td td-product td-sortable">
                           <a href="<?php echo get_permalink()?>?<?php echo implode("&", $params)?>&orderby=product&order=<?php echo $orderBy == 'product' && $order == 'asc' ? 'desc' : 'asc'?>" <?php if($orderBy == 'product'){ ?>class="<?php echo $order?>"<?php } ?>>Product Name <span class="sort"></span></a>
                       </div>
                       <div class="td td-case td-sortable td-two-lines tocenter">
                           <a href="<?php echo get_permalink()?>?<?php echo implode("&", $params)?>&orderby=suite&order=<?php echo $orderBy == 'suite' && $order == 'asc' ? 'desc' : 'asc'?>" <?php if($orderBy == 'suite'){ ?>class="<?php echo $order?>"<?php } ?>>Test Suite <span class="sort"></span></a>
                           <a href="<?php echo get_permalink()?>?<?php echo implode("&", $params)?>&orderby=case&order=<?php echo $orderBy == 'case' && $order == 'asc' ? 'desc' : 'asc'?>" <?php if($orderBy == 'case'){ ?>class="<?php echo $order?>"<?php } ?>>Test Case <span class="sort"></span></a>
                       </div>
<!--                       <div class="td td-suite td-sortable">-->
<!--                       </div>-->
                       <div class="td td-outcome td-two-lines tocenter td-sortable">
                           <a href="<?php echo get_permalink()?>?<?php echo implode("&", $params)?>&orderby=test_outcome&order=<?php echo $orderBy == 'test_outcome' && $order == 'asc' ? 'desc' : 'asc'?>" <?php if($orderBy == 'test_outcome'){ ?>class="<?php echo $order?>"<?php } ?>>Test<br />Outcome <span class="sort"></span></a>
                       </div>
                       <div class="td td-audit td-two-lines tocenter td-sortable">
                           <a href="<?php echo get_permalink()?>?<?php echo implode("&", $params)?>&orderby=audit&order=<?php echo $orderBy == 'audit' && $order == 'asc' ? 'desc' : 'asc'?>" <?php if($orderBy == 'audit'){ ?>class="<?php echo $order?>"<?php } ?>>Audit<br />Record <span class="sort"></span></a>
                       </div>                       
                       <div class="td td-convsn td-sortable tocenter<?php if( is_super_admin() || ct_is_group_admin_or_support($user_id) ):?> td-two-lines<?php endif;?>">
                           <?php if( is_super_admin() || ct_is_group_admin_or_support($user_id) ):?>
                               Organisation<br>
                               Subscription Nickname<br>
                           <?php endif;?>
                           <a href="<?php echo get_permalink()?>?<?php echo implode("&", $params)?>&orderby=message&order=<?php echo $orderBy == 'message' && $order == 'asc' ? 'desc' : 'asc'?>" <?php if($orderBy == 'message'){ ?>class="<?php echo $order?>"<?php } ?>>Conversation ID<span class="sort"></span></a>
                       </div>
                       <div class="td td-date td-sortable tocenter">
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
                               <div class="td td-case tocenter">
                                   <?php if($row->TEST_SUITE_WP_ID){ ?>
                                       <a href="<?php echo get_permalink($row->TEST_SUITE_WP_ID)?>"><?php echo cp_wrap($row->TEST_SUITE_TITLE, 25)?></a>
                                   <?php }else if(!$row->TEST_SUITE_WP_ID && $row->TEST_CASE_WP_ID){ ?>
                                       <?php
                                       $tSuiteId = get_post_meta($row->TEST_CASE_WP_ID, 'test_suite');
                                       if($tSuiteId && count($tSuiteId) == 1)
                                       {
                                           $esb->updateTestSuiteID($row->ID, $tSuiteId[0]);
                                           ?>
                                           <a href="<?php echo get_permalink($tSuiteId[0])?>"><?php echo cp_wrap(get_the_title($tSuiteId[0]), 25)?></a>
                                       <?php
                                       } else {
                                           echo 'Not Assigned';
                                       }
                                   } else {
                                       echo 'Not Assigned';
                                   }?>
                                   </br>
                                   <?php if(!$row->TEST_CASE_WP_ID) {?>
                                    Not Assigned
                                    <?php }else{ ?>
                                    <a href="<?php echo get_permalink($row->TEST_CASE_WP_ID)?>"><?php echo cp_wrap($row->TEST_CASE_ID, 22)?></a>
                                    <?php } ?>
                                   
                               </div>
<!--                               <div class="td td-suite">-->
<!---->
<!--                               </div>-->
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
                               <div class="td td-convsn tocenter<?php if( is_super_admin() || ct_is_group_admin_or_support($user_id) ):?> td-two-lines<?php endif;?>">
                                   <a href="javascript:void(0)">
                                   <?php
                                        if( is_super_admin() || ct_is_group_admin_or_support($user_id) ){
                                            $organisation = ct_get_organisation_by_subscription_id( $row->ORGANISATION_SUBSCRIPTION_ID );                                            
                                            echo $organisation ? $organisation->organisation_name : ' - ';
                                            echo '<br>';
                                            $subscription = ct_get_organisation_subscription_by_id( $row->ORGANISATION_SUBSCRIPTION_ID );
                                            echo $subscription ? $subscription->nickname : ' - ';
                                            echo '<br>';
                                        }
                                        if(strlen($row->CONVERSATION_ID) > 38)
                                        {
                                            echo '<span title="' . $row->CONVERSATION_ID . '">' . substr($row->CONVERSATION_ID, 0, 15) . "....." . substr($row->CONVERSATION_ID, -15) . '</span>';
                                        }else{
                                            echo $row->CONVERSATION_ID;
                                        }                                    
                                   ?>
                                   </a>
                                   <input type="text" value="<?php echo $row->CONVERSATION_ID; ?>" readonly="readonly">
                               </div>
                               <div class="td td-date tocenter">
                                   <?php echo formatDate($row->CONVERSATION_TIMESTAMP, 'Y-m-d H:i:s')?><br />                                   
                               </div>                               
                               <div class="clear"></div> 
                               <?php if(isset($messages[$row->ID])){ ?>                               
                                   <div class="sub-table">
                                       <div class="table">
                                           <div class="thead tr">
                                               <div class="td td-from td-two-lines tocenter">From</br>To</div>
<!--                                               <div class="td td-to tocenter">To</div>-->
                                               <div class="td td-service td-two-lines tocenter">Service</br>Action</div>
<!--                                               <div class="td td-action tocenter">Action</div>-->
                                               <div class="td td-message-outcome td-two-lines tocenter">Validation Status</div>
                                               <div class="td td-message-date">Date/Time</div>
                                               <div class="td td-message-part tocenter">Part ID</div>
                                               <div class="td td-message-view">View</div>
                                               <div class="clear"></div>
                                           </div>
                                           <div class="tbody">
                                           <?php if( isset( $messages[$row->ID] ) && is_iterable( $messages[$row->ID] ) ):?>
                                             <?php foreach($messages[$row->ID] as $message) {?>
                                                <?php if( $message->FLAG === 'IS_EMPTY' ) continue;?>
                                               <div class="tr">
                                                   <div class="td td-from td-two-lines"><?php echo cp_wrap($message->FROM_PARTY_ID, 15).'</br>'.cp_wrap($message->TO_PARTY_ID, 15) ?></div>
<!--                                                   <div class="td td-to">--><?php //echo cp_wrap($message->TO_PARTY_ID, 15)?><!--</div>-->
                                                   <div class="td td-two-lines td-service tocenter"><?php echo $message->SERVICE.'</br>'.$message->ACTION ?></div>
<!--                                                   <div class="td td-action">--><?php //echo $message->ACTION ?><!--</div>-->
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
                                                       <?php echo formatDate($message->MESSAGE_TIMESTAMP, 'Y-m-d H:i:s')?>                                                       
                                                   </div>
                                                   <div class="td td-message-part tocenter">
                                                       <a href="javascript:void(0)">
                                                       <?php
                                                       if(strlen($message->PART_ID) > 28)
                                                       {
                                                           echo '<span title="' . $message->PART_ID . '">' . substr($message->PART_ID, 0, 10) . "....." . substr($message->PART_ID, -10) . '</span>';
                                                       }else{
                                                           echo $message->PART_ID;
                                                       }
                                                       ?>
                                                       </a>
                                                       <input type="text" value="<?php echo $message->PART_ID; ?>" readonly="readonly">
                                                   </div>
                                                   <div class="td td-message-view">
                                                      <a href="<?php echo $message->S3_PAYLOAD_LOCATION ? $message->S3_PAYLOAD_LOCATION : "/message-envelope?id=" . $message->ID?>" target="_blank">XML</a> 
                                                      | 
                                                      <?php if($message->S3_PAYLOAD_CONTENT_LENGTH > $html_render_limit) { ?>
                                                        <a href="<?php echo $message->S3_PAYLOAD_LOCATION?>" class="html-view-error">HTML</a>
                                                      <?php } else { ?>
                                                        <a href="/message-envelope?id=<?php echo $message->ID?>&mode=html" target="_blank">HTML</a>
                                                      <?php } ?>
                                                       <br>
                                                       <a class="show_transaction_receipts" data-ctreceipt="<?php echo is_null( $message->CT_RECEIPT_MESSAGE_ID ) ? 'No value' : $message->CT_RECEIPT_MESSAGE_ID ;?>" data-gateway="<?php echo is_null( $message->GATEWAY_RECEIPT_MESSAGE_ID ) ? 'No value' : $message->GATEWAY_RECEIPT_MESSAGE_ID;?>" href="#">Receipts</a>
                                                   </div>
                                                   <div class="clear"></div>
                                               </div>
                                             <?php } ?>
                                           <?php endif;?>
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
                            'total'        => $limit > 0 ? ceil($log_results['total'] / $limit) : 1,
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
        
        jQuery('a.html-view-error').each(function(){
            jQuery(this).click(function(){
                return false;
            })
            
            var s_url = jQuery(this).attr("href");
            
            jQuery(this).cplightbox({
                type: 'inline',
                href: '#html-render-limit-error-box',
                onStart: function(){
                    jQuery('#html-render-limit-error-box .popup-box-content a').attr("href", s_url);
                }
            })
        })
        
        <?php if (isset($tOrganisations)): ?> //Has Organisation Filter
            function update_subscription_filter()
            {
                //Remove Old Values
                jQuery('#filterForm #subscription option').remove();
                
                //Selected Org Id
                var org_id = jQuery('#filterForm #organisation').val();
                
                jQuery('#filterForm #all_subscriptions option').each(function(){
                    if (org_id == 'all' || jQuery(this).val() == 'all' || jQuery(this).val() == 'my' || jQuery(this).attr('data-org-id') == org_id) {
                        jQuery('#filterForm #subscription').append(jQuery(this).clone());
                    }    
                })
                
            }
            jQuery('#filterForm #organisation').change(update_subscription_filter);
        <?php endif; ?>
        
        fixTdHeight(jQuery('#my_transaction_log .table-box'));

        jQuery('.td-convsn a, .td-message-part a').click(function(){
            jQuery(this).hide();
            jQuery(this).next().show();
            jQuery(this).next().click();
        });
        
        jQuery('.td-convsn input[type=text], .td-message-part input[type=text]').click(function(){
            jQuery(this).select();
        });
        
        jQuery('.td-convsn input[type=text], .td-message-part input[type=text]').blur(function(){
            jQuery(this).hide();
            jQuery(this).prev().show();
        });
        
        jQuery('.td-convsn input[type=text], .td-message-part input[type=text]').keyup(function(e){
            if (e.keyCode == 27) {
                jQuery(this).hide();
                jQuery(this).prev().show();
            } else {
                jQuery(this).select();
                return false;
            }
        });
        
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
                                jQuery('#edit-transaction-log-box .table-box .tbody .td-suite').each(function(){
                                    if(jQuery(this).find('select').length > 0)
                                    {
                                        jQuery(this).removeClass('td-fixed');
                                    }
                                });
                                jQuery('#edit-transaction-log-box .table-box .tbody .td-case select').change(function(){
                                    var sids = jQuery(this).find('option:selected').attr('data-suites').split(",");
                                    var tdSuite = jQuery(this).parent().parent().find('.td-suite');
                                    if(sids.length >= 1)
                                    {
                                        tdSuite.removeClass('td-fixed');
                                        tdSuite.html('<select name="suite' + tdSuite.attr('data-id') + '" class="select"></select>');
                                        jQuery('#edit-transaction-log-box #all-suites option').each(function(){
                                            if(sids.indexOf(jQuery(this).val()) > -1)
                                            {                                                
                                                tdSuite.find('select').append(jQuery(this).clone());
                                            }
                                        })
                                    /*}else if(sids.length == 1){
                                        tdSuite.addClass('td-fixed');
                                        jQuery('#edit-transaction-log-box #all-suites option').each(function(){
                                            if(jQuery(this).val() == sids[0])
                                            {
                                                tdSuite.html('<a href="' + jQuery(this).attr('data-permalink') + '">' + jQuery(this).text() + '</a>')
                                            }
                                        })*/
                                    }else{
                                        tdSuite.addClass('td-fixed');
                                        tdSuite.html('');
                                    }
                                })
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
                            jQuery('#view-validation-log-box a.html-view-error').each(function(){
                                jQuery(this).click(function(){
                                    return false;
                                })
                                
                                var s_url = jQuery(this).attr("href");
                                
                                jQuery(this).cplightbox({
                                    type: 'inline',
                                    href: '#html-render-limit-error-box',
                                    onStart: function(){
                                        jQuery('#html-render-limit-error-box .popup-box-content a').attr("href", s_url);
                                    }
                                })
                            })
                        }
                    });
                }
            })    
            
            return false;
        })
        jQuery('.show_transaction_receipts').click(function(e){
            e.preventDefault();
            jQuery('#view-transaction_details_box').width( 1200 );
            jQuery('#receipt_compliancetest').text(jQuery(this).data('ctreceipt'));
            jQuery('#receipt_gateway').text(jQuery(this).data('gateway'));
            jQuery('#view-transaction_details_box').showPopupBox({
                closeWhenClickOveraly: false
            });
            var additional_width = jQuery('#receipt_compliancetest').width();
            if( jQuery('#receipt_gateway').width() > additional_width ) additional_width = jQuery('#receipt_gateway').width();
            jQuery('#view-transaction_details_box').width( 180 + additional_width );
        });

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

<div class="popup-box" id="edit-transaction-log-box" style="display: none; width: 1000px">
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

<div class="popup-box" id="view-transaction_details_box" style="display: none; width: 410px">
    <div class="popup-box-header radius6 noradiusbottom">Receipt Identifiers</div>
    <div class="popup-box-content">
        <div class="space10"></div>
        <div class="grid-box-body">
            <div class="tbody">
                <div><span class="bold">ComplianceTest: </span><span id="receipt_compliancetest"></span></div>
                <div class="space10"></div>
                <div><span class="bold">Gateway Network: </span><span id="receipt_gateway"></span></div>
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

<div class="popup-box" id="html-render-limit-error-box" style="display: none; width: 410px">
    <div class="popup-box-header radius6 noradiusbottom">HTML View Not Available</div>
    <div class="popup-box-content"> 
        HTML View is not available due to content size of 25 kB. Click <a href="#" target="_blank">here</a> to download and process locally
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
