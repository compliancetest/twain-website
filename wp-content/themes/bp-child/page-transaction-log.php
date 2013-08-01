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

$esb = new ManageESB();

$limit = isset($_GET['limit']) ? intval($_GET['limit']) : getItemsPerPage('transactions');                    
setItemsPerPage($limit, 'transactions');

/*$orderBy = isset($_GET['orderby']) ? $_GET['orderby'] : 'PRODUCT_NAME';
if(!in_array($orderBy, array('PRODUCT_NAME'))
    $orderBy = 'PRODUCT_NAME';*/
    
$order = isset($_GET['order']) ? $_GET['order'] : 'asc';

$page = get_query_var('paged') ? get_query_var('paged') : 1;

$log_results = $esb->getUserTransactionLog($filterProduct, $filterSuite, $filterCase, $filterService, $filterAction, $filterPartyId, $filterDate, $page, $limit);
$results = $log_results['data'];
$params = array();                 

if($filterProduct === 'NULL')
    $filterProduct = null;
if($filterSuite === 'NULL')
    $filterSuite = null;
if($filterCase === 'NULL')
    $filterCase = null;
    
$tbodyHTML = '';

$tProducts = array();
$tSuites = array();
$tCases = array();
$tServices = array();
$tActions = array();
$tPartyIDs = array();


foreach($results as $row){ 
    $tProducts[] = $row->PRODUCT_ID;
    
    if(!isset($tSuites[$row->TEST_SUITE_ID]))
        $tSuites[$row->TEST_SUITE_ID] = $row->TEST_SUITE_NAME;
    if(!isset($tCases[$row->TEST_CASE_DB_ID]))
        $tCases[$row->TEST_CASE_DB_ID] = $row->TEST_CASE_ID;
        
    $tServices[] = $row->SERVICE;
    $tActions[] = $row->ACTION;
    $tPartyIDs[] = $row->FROM_PARTY_ID;
    $tPartyIDs[] = $row->TO_PARTY_ID;
}

$tProducts = array_unique($tProducts);
$tSuites = array_unique($tSuites);
$tCases = array_unique($tCases);
$tServices = array_unique($tServices);
$tActions = array_unique($tActions);
$tPartyIDs = array_unique($tPartyIDs);

asort($tProducts);
asort($tSuites);
asort($tCases);
asort($tServices);
asort($tActions);
asort($tPartyIDs);

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

?>
<div class="content" id="my_transaction_log">
    <div class="column fifth left nopaddingleft nopaddingright sidebar">
        <?php get_sidebar('dashboard'); ?>
    </div>
    <!--<div class="four_fifths right container">
        <div class="column">
            <h2>My Transaction Log</h2>
            <div class="space10"></div>
            <div class="message notice">This function is under construction</div>
        </div>
        <div class="space100"></div>
    </div>-->
    <div class="four_fifths right container">
        <div class="filter-box column">
            <div class="left"><label>Filter By:</label></div>
            <form name="filterForm" id="filterForm" method="get">
                <div class="left">
                    <div class="styled_select">
                        <label>Product / Service:</label>
                        <select name="product" id="product" autocomplete="off">
                            <option value="">- All -</option>
                          <?php foreach($tProducts as $t){ ?>
                           <?php if($t === null){ ?>
                            <option value="NULL" <?php echo $filterProduct === null ? "selected='selected'" : ""?>>Not Assigned</option>
                           <?php }else{ ?>
                            <option value="<?php echo $t?>" <?php echo $t == $filterProduct ? "selected='selected'" : "" ?>><?php echo get_post_meta($t, 'product_name', true) ?></option>
                           <?php } ?>
                          <?php } ?>
                        </select>
                    </div>
                    <div class="space10"></div>
                    <div class="styled_select">
                        <label>eb:Service:</label>
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
                        <label>Test Suite:</label>
                        <select name="suite" id="suite" autocomplete="off">
                            <option value="">- All -</option>
                          <?php foreach($tSuites as $k=>$s){ ?>
                           <?php if($k === null){ ?>
                            <option value="NULL" <?php echo $filterSuite === null ? "selected='selected'" : ""?>>Not Assigned</option> 
                           <?php }else{ ?>
                            <option value="<?php echo $k?>" <?php echo $k == $filterSuite ? "selected='selected'" : "" ?>><?php echo $s ?></option>
                           <?php } ?>
                          <?php } ?>
                        </select>
                    </div>
                    <div class="space10"></div>
                    <div class="styled_select">
                        <label>eb:Action:</label>
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
                        <label>Test Case:</label>
                        <select name="case" id="case" autocomplete="off">
                            <option value="">- All -</option>
                          <?php foreach($tCases as $k=>$c){ ?>
                           <?php if($k === null){ ?>
                            <option value="NULL" <?php echo $filterCase === null ? "selected='selected'" : ""?>>Not Assigned</option> 
                           <?php }else{ ?>
                            <option value="<?php echo $k?>" <?php echo $k == $filterCase ? "selected='selected'" : "" ?>><?php echo $c ?></option>
                           <?php } ?>
                          <?php } ?>
                        </select>
                    </div>
                    <div class="space10"></div>
                    <div class="styled_select">
                        <label>eb:PartyID:</label>
                        <select name="partyid" id="partyid" autocomplete="off">
                            <option value="">- All -</option>
                            <?php foreach($tPartyIDs as $p){ ?>
                            <option value="<?php echo $p?>" <?php echo $p == $filterPartyId ? "selected='selected'" : "" ?>><?php echo $p ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="left">
                    <label class="left15">&nbsp;Date:</label>
                    <input type="text" name="date" id="date" class="input datepicker" value="<?php echo $filterDate?>" />
                    <div class="space25"></div>
                    <a href="#" class="action-btn process-btn submit-btn" id="log-filter-btn"><span class="p"></span><span class="t">APPLY FILTER</span></a>
                </div>            
                <div class="clear"></div>
            </form>
        </div> 
        <div class="padding10">
            <a href="#" id="delete-log-link" class="action-btn blue-delete-btn icon-btn right left5 has-tooltip"><span class="p"></span><span class="simple_tooltip radius6">Delete Selected Rows<span></span></span></a>
            <a href="#" id="edit-log-link" class="action-btn blue-edit-btn icon-btn right has-tooltip"><span class="p"></span><span class="simple_tooltip radius6">Edit Selected Rows<span></span></span></a>            
            <div class="clear"></div>
            <div class="space10"></div>
            <div class="grid-box table-box" id="log-result-table">               
               <div class="grid-box-body">
                   <div class="thead tr">
                       <div class="td td-product td-sortable">
                           <a href="#" class="asc">Product Name <span class="sort"></span></a>
                       </div>
                       <div class="td td-case td-two-lines tocenter td-sortable">
                           <a href="#">Test<br />Case <span class="sort"></span></a>
                       </div>
                       <div class="td td-suite td-two-lines tocenter td-sortable">
                           <a href="#">Test<br />Suite <span class="sort"></span></a>
                       </div>
                       <div class="td td-outcome td-two-lines tocenter td-sortable">
                           <a href="#">Test<br />Outcome <span class="sort"></span></a>
                       </div>
                       <div class="td td-audit td-two-lines tocenter td-sortable">
                           <a href="#">Audit<br />Record <span class="sort"></span></a>
                       </div>
                       <div class="td td-service tocenter td-sortable">
                           <a href="#">Service <span class="sort"></span></a>
                       </div>
                       <div class="td td-action tocenter td-sortable">
                           <a href="#">Action <span class="sort"></span></a>
                       </div>
                       <div class="td td-convsn td-two-lines tocenter td-sortable">
                           <a href="#">Message<br />Envelope <span class="sort"></span></a>
                       </div>
                       <div class="td td-date tocenter td-sortable">
                           <a href="#">Date / Time <span class="sort"></span></a>
                       </div>
                       <div class="td td-from tocenter td-sortable">
                           <a href="#">From / To <span class="sort"></span></a>
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
                           <div class="tr has-actions">
                               <div class="td td-product">
                                   <input type="checkbox" name="id[]" id="id<?php echo  $row->ID?>" value="<?php echo $row->ID?>" />
                                   <a href="<?php echo get_permalink($row->PRODUCT_ID)?>"><?php echo get_post_meta($row->PRODUCT_ID, 'product_name', true)?></a>
                               </div>
                               <div class="td td-case">
                                   <a href="<?php echo get_permalink($row->TEST_CASE_DB_ID)?>"><?php echo cp_wrap($row->TEST_CASE_ID, 10)?></a>
                               </div>
                               <div class="td td-suite">
                                    <?php if($row->TEST_SUITE_ID){ ?>
                                    <a href="<?php echo get_permalink($row->TEST_SUITE_ID)?>"><?php echo cp_wrap($row->TEST_SUITE_NAME, 10)?></a>
                                    <?php }else if(!$row->TEST_SUITE_ID && $row->TEST_CASE_DB_ID){ ?>
                                    <?php $tSuiteId = get_post_meta($row->TEST_CASE_DB_ID, 'test_suite', true); ?>
                                    <a href="<?php echo get_permalink($tSuiteId)?>"><?php echo cp_wrap(get_post_meta($tSuiteId, 'ts_name', true), 10)?></a>
                                    <?php } ?>
                                   
                               </div>
                               <div class="td td-outcome tocenter">
                                   <?php if($row->TEST_OUTCOME == 'SUCCESS'){ ?>
                                   <span class="status-certified">Pass</span>
                                   <?php }else if($row->TEST_OUTCOME == 'FAILURE'){ ?>
                                   <span class="status-testing">Fail</span>
                                   <?php }else if($row->TEST_CASE_DB_ID){ 
                                       $outComeType = get_post_meta($row->TEST_CASE_DB_ID, 'outcome_type', true);
                                       $outComeStatus = $esb->getTestOutcomeStatus($row->ID, $outComeType);                                       
                                       if($outComeStatus == 'SUCCESS')
                                           echo '<span class="status-certified">Pass</span>';
                                       else if($outComeStatus == 'FAILURE')
                                           echo '<span class="status-testing">Fail</span>';
                                       
                                   } ?>
                                   <?php if(isset($row->HAS_VALIDATION_LOG)){ ?>
                                   <br />
                                   <a href="#" data-id="<?php echo $row->ID ?>" class="view-validation-log">View Log</a>
                                   <?php } ?>
                               </div>
                               <div class="td td-audit tocenter"><?php echo !$row->AUDIT_RECORD ? "No" : "Yes"?></div>
                               <div class="td td-service">
                                   <?php echo cp_wrap($row->SERVICE, 17)?>
                               </div>
                               <div class="td td-action">
                                   <?php echo cp_wrap($row->ACTION, 14)?>
                               </div>
                               
                               <div class="td td-convsn">
                                   <?php echo  cp_wrap($row->CONVERSATION_ID, 12) ?>
                                   <br />
                                   <a href="/message-envelope?id=<?php echo $row->ID?>" target="_blank">XML</a> | <a href="/message-envelope?id=<?php echo $row->ID?>&mode=html" target="_blank">HTML</a>
                               </div>
                               <div class="td td-date tocenter">
                                   <?php echo formatDate($row->EXECUTION_DATE, 'm/d/y')?><br />
                                   <?php echo date("H:i:s", strtotime($row->EXECUTION_DATE)) ?>
                               </div>
                               <div class="td td-from tocenter">
                                   <div style="border-bottom: solid 1px #ccc; padding-bottom: 3px; margin-bottom: 3px;"><?php echo $row->FROM_PARTY_ID?></div>
                                   <?php echo $row->TO_PARTY_ID?>
                               </div>
                               <!--<div class="td td-to"><?php echo $row->TO_PARTY_ID?></div>-->
                               <div class="clear"></div> 
                               
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
            var checked = jQuery('#log-result-table input[type="checkbox"]:checked').length;
            
            if(checked == 0)
            {
                alert("Please select a row.");
                return false;
            }else{
                var ids = new Array();
                jQuery('#log-result-table input[type="checkbox"]:checked').each(function(){
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
        jQuery('.view-validation-log').click(function(){
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
            var checked = jQuery('#log-result-table input[type="checkbox"]:checked').length;            
            if(checked == 0)
            {
                alert("Please select a row.");
                return false;
            }else{
                var isAudit = false;
                
                var ids = new Array();
                jQuery('#log-result-table input[type="checkbox"]:checked').each(function(){
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
                   <div class="td td-service tocenter">Service</div>
                   <div class="td td-action tocenter">Action</div>
                   <div class="td td-convsn td-two-lines tocenter">Message<br />Envelope</div>
                   <div class="td td-date tocenter">Date / Time</div>
                   <div class="td td-from tocenter">From / To</div>
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
