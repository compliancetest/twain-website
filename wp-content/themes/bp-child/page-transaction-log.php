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

$results = $esb->getUserTransactionLog(get_current_user_id(), $filterProduct, $filterSuite, $filterCase, $filterService, $filterAction, $filterPartyId, $filterDate);

$tbodyHTML = '';

$tProducts = array();
$tSuites = array();
$tCases = array();
$tServices = array();
$tActions = array();
$tPartyIDs = array();

ob_start();
foreach($results as $row){ 
    $tProducts[] = $row->PRODUCT_ID;
    $tSuites[] = $row->TEST_SUITE_ID;
    $tCases[] = $row->TEST_CASE_ID;
    $tServices[] = $row->SERVICE;
    $tActions[] = $row->ACTION;
    $tPartyIDs[] = $row->FROM_PARTY_ID;
    $tPartyIDs[] = $row->TO_PARTY_ID;
?>
   <div class="tr">
       <div class="td td-product">
           <!--<input type="checkbox" name="id[]" id="id<?php echo  $row->ID?>" value="<?php echo $row->ID?>" />-->
           <a href="<?php echo get_permalink($row->PRODUCT_ID)?>"><?php echo get_post_meta($row->PRODUCT_ID, 'product_name', true)?></a>
       </div>
       <div class="td td-case">
           <a href="<?php echo get_permalink($row->TEST_CASE_ID)?>"><?php echo cp_wrap(get_post_meta($row->TEST_CASE_ID, 'test_case_id', true), 10)?></a>
       </div>
       <div class="td td-suite">
           <a href="<?php echo get_permalink($row->TEST_SUITE_ID)?>"><?php echo cp_wrap(get_post_meta($row->TEST_SUITE_ID, 'ts_name', true), 10)?></a>
       </div>
       <div class="td td-outcome tocenter">
           <?php if($row->TEST_OUTCOME == 'SUCCESS'){ ?>
           <span class="status-certified">Pass</span>
           <?php }else{ ?>
           <span class="status-testing">Fail</span>
           <?php } ?>
       </div>
       <div class="td td-audit tocenter">
           <?php if(!$row->AUDIT_RECORD){ ?>
               No
           <?php }else{ ?>
               <a href="#">Yes</a>
           <?php } ?>
       </div>
       <div class="td td-service">
           <?php echo cp_wrap($row->SERVICE, 17)?>
       </div>
       <div class="td td-convsn">
           <?php echo  cp_wrap($row->CONVERSATION_ID, 12) ?>
       </div>
       <div class="td td-date tocenter">
           <?php echo formatDate($row->EXECUTION_DATE)?>
       </div>
       <div class="td td-from"><?php echo $row->FROM_PARTY_ID?></div>
       <div class="td td-to"><?php echo $row->TO_PARTY_ID?></div>
       <div class="clear"></div> 
   </div>                       
<?php 
}
 
$tbodyHTML = ob_get_clean();
ob_end_clean();

$tProducts = array_unique($tProducts);
$tSuites = array_unique($tSuites);
$tCases = array_unique($tCases);
$tServices = array_unique($tServices);
$tActions = array_unique($tActions);
$tPartyIDs = array_unique($tPartyIDs);

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
                        <select name="product" id="product" autocomplete="off">
                            <option value="">- Product -</option>
                            <?php foreach($tProducts as $t){ ?>
                            <option value="<?php echo $t?>" <?php echo $t == $filterProduct ? "selected='selected'" : "" ?>><?php echo get_post_meta($t, 'product_name', true) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="space10"></div>
                    <div class="styled_select">
                        <select name="service" id="service" autocomplete="off">
                            <option value="">eb:Service</option>
                            <?php foreach($tServices as $s){ ?>
                            <option value="<?php echo $s?>" <?php echo $s == $filterService ? "selected='selected'" : "" ?>><?php echo $s ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="left">
                    <div class="styled_select">
                        <select name="suite" id="suite" autocomplete="off">
                            <option value="">- Suite -</option>
                            <?php foreach($tSuites as $s){ ?>
                            <option value="<?php echo $s?>" <?php echo $s == $filterSuite ? "selected='selected'" : "" ?>><?php echo get_post_meta($s, 'ts_name', true) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="space10"></div>
                    <div class="styled_select">
                        <select name="action" id="action" autocomplete="off">
                            <option value="">eb:Action</option>
                            <?php foreach($tActions as $a){ ?>
                            <option value="<?php echo $a?>" <?php echo $a == $filterAction ? "selected='selected'" : "" ?>><?php echo $a ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="left">
                    <div class="styled_select">
                        <select name="case" id="case" autocomplete="off">
                            <option value="">- Case -</option>
                            <?php foreach($tCases as $c){ ?>
                            <option value="<?php echo $c?>" <?php echo $c == $filterCase ? "selected='selected'" : "" ?>><?php echo get_post_meta($c, 'test_case_id', true) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="space10"></div>
                    <div class="styled_select">
                        <select name="partyid" id="partyid" autocomplete="off">
                            <option value="">eb:PartyID</option>
                            <?php foreach($tPartyIDs as $p){ ?>
                            <option value="<?php echo $p?>" <?php echo $p == $filterPartyId ? "selected='selected'" : "" ?>><?php echo $p ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="left">
                    <input type="text" name="date" id="date" class="input datepicker" value="<?php echo $filterDate?>" />
                    <div class="space10"></div>
                    <a href="#" class="action-btn process-btn submit-btn" id="log-filter-btn"><span class="p"></span><span class="t">APPLY FILTER</span></a>
                </div>            
                <div class="clear"></div>
            </form>
        </div> 
        <div class="padding10">
            <!--<a href="#" id="delete-log-link" class="action-btn blue-delete-btn icon-btn right left5 has-tooltip"><span class="p"></span><span class="simple_tooltip radius6">Delete Selected Fields<span></span></span></a>
            <a href="#" id="edit-log-link" class="action-btn blue-edit-btn icon-btn right has-tooltip"><span class="p"></span><span class="simple_tooltip radius6">Edit Selected Fields<span></span></span></a>            
            <div class="clear"></div>
            <div class="space10"></div>-->
            <div class="grid-box table-box" id="log-result-table">               
               <div class="grid-box-body">
                   <div class="thead tr">
                       <div class="td td-product">Product Name</div>
                       <div class="td td-case td-two-lines tocenter">Test<br />Case</div>
                       <div class="td td-suite td-two-lines tocenter">Test<br />Suite</div>
                       <div class="td td-outcome td-two-lines tocenter">Test<br />Outcome</div>
                       <div class="td td-audit td-two-lines tocenter">Audit<br />Record</div>
                       <div class="td td-service tocenter">Service</div>
                       <div class="td td-convsn tocenter">Convsn</div>
                       <div class="td td-date tocenter">Date</div>
                       <div class="td td-from tocenter">From</div>
                       <div class="td td-to tocenter">To</div>
                       <div class="clear"></div>
                   </div>
                   <div class="tbody">
                   <?php if(!$results){ ?>
                       <div class="tr">
                           <div class="td td-full">No Transaction Found.</div>
                           <div class="clear"></div>
                       </div>
                   <?php }else{
                        echo $tbodyHTML;    
                       } 
                   ?>
                   </div>
               </div>
           </div>
           <div class="space10"></div>
        </div>
    </div>
    <div class="clear"></div>
 <script type="text/javascript">
    jQuery(document).ready(function(){
        fixTdHeight(jQuery('#my_transaction_log .table-box'));
                
        //Edit Log
        /*jQuery('#edit-log-link').click(function(){
            var checked = jQuery('#log-result-table input[type="checkbox"]:checked').length;
            
            if(checked == 0)
            {
                alert("Please select a field.");
                return false;
            }else{
                var ids = new Array();
                jQuery('#log-result-table input[type="checkbox"]:checked').each(function(){
                    ids.push(this.value);
                })           
                jQuery('#my_transaction_log').append('<div class="loading"></div>');
                jQuery('#my_transaction_log .loading').show();
                jQuery('.mask-wrapper').show();
                jQuery.ajax({
                    url: '/',
                    data: {
                        'cp-action': '<?php echo wp_create_nonce('edit-transaction-log')?>',
                        'id': ids
                    },
                    type: 'post',
                    dataType: 'html',
                    success: function(rsp){                        
                        jQuery('#edit-transaction-log-box .tbody').html(rsp);
                        jQuery('#edit-transaction-log-box').showPopupBox({
                            onClose: function(){
                                jQuery('#edit-transaction-log-box .tbody').html("");
                            }
                        });
                    }
                })    
            }
            
            return false;
        })*/
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
                   <div class="td td-convsn tocenter">Convsn</div>
                   <div class="td td-date tocenter">Date</div>
                   <div class="td td-from tocenter">From</div>
                   <div class="td td-to tocenter">To</div>
                   <div class="clear"></div>
               </div>
               <div class="tbody">
           
               </div>
           </div>
       </div>
       <div class="space10"></div>
    </div>
    <div class="popup-box-footer radius6 noradiustop">                                            
        <a href="#make-claim-box" cp-type="inline"  class="action-btn process-btn"><span class="p"></span><span class="t">SAVE</span></a>
        <a href="#make-claim-box" cp-type="inline"  class="action-btn cancel-btn"><span class="p"></span><span class="t">Cancel</span></a>
        <div class="clear"></div>
    </div>
    <a class="close_btn"></a>                
</form>
</div>        
<?php
get_footer();
?>
