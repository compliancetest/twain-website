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

$esb = new ManageESB();

$results = $esb->getUserTransactionLog();
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
            <div class="left">
                <div class="styled_select">
                    <select name="producct" id="product">
                        <option value="">- Product -</option>
                    </select>
                </div>
                <div class="space10"></div>
                <div class="styled_select">
                    <select name="eb-service" id="eb-service">
                        <option value="">eb:Service</option>
                    </select>
                </div>
            </div>
            <div class="left">
                <div class="styled_select">
                    <select name="suite" id="Suite">
                        <option value="">- Suite -</option>
                    </select>
                </div>
                <div class="space10"></div>
                <div class="styled_select">
                    <select name="ebaction" id="ebaction">
                        <option value="">eb:Action</option>
                    </select>
                </div>
            </div>
            <div class="left">
                <div class="styled_select">
                    <select name="case" id="case">
                        <option value="">- Case -</option>
                    </select>
                </div>
                <div class="space10"></div>
                <div class="styled_select">
                    <select name="ebpartyid" id="ebpartyid">
                        <option value="">eb:PartyID</option>
                    </select>
                </div>
            </div>
            <div class="left">
                <input type="text" name="date" id="date" class="input datepicker" />
                <div class="space10"></div>
                <a href="#" class="action-btn process-btn left15"><span class="p"></span><span class="t">APPLY FILTER</span></a>
            </div>            
            <div class="clear"></div>
        </div> 
        <div class="padding20-10">
            <div class="grid-box table-box">               
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
                       <div class="td td-customer tocenter">Customer</div>
                       <div class="clear"></div>
                   </div>
                   <div class="tbody">
                   <?php if(!$results){ ?>
                       <div class="tr">
                           <div class="td td-full">No Transaction Found.</div>
                           <div class="clear"></div>
                       </div>
                   <?php }else{?>
                     <?php foreach($results as $row){ ?>
                       <div class="tr">
                           <div class="td td-product">
                               <a href="<?php echo get_permalink($row->PRODUCT_ID)?>"><?php echo get_post_meta($row->PRODUCT_ID, 'product_name', true)?></a>
                           </div>
                           <div class="td td-case">
                               <a href="<?php echo get_permalink($row->TEST_CASE_ID)?>"><?php echo get_post_meta($row->TEST_CASE_ID, 'test_case_id', true)?></a>
                           </div>
                           <div class="td td-suite">
                               <a href="<?php echo get_permalink($row->TEST_SUITE_ID)?>"><?php echo get_post_meta($row->TEST_SUITE_ID, 'ts_name', true)?></a>
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
                               <?php echo $row->SERVICE?>
                           </div>
                           <div class="td td-convsn">
                               <?php echo  $row->CONVERSATION_ID ?>
                           </div>
                           <div class="td td-date tocenter">
                               <?php echo formatDate($row->EXECUTION_DATE)?>
                           </div>
                           <div class="td td-from"><?php echo $row->FROM_PARTY_ID?></div>
                           <div class="td td-to"><?php echo $row->TO_PARTY_ID?></div>
                           <div class="td td-customer">Customer</div>
                           <div class="clear"></div> 
                       </div>                       
                     <?php } ?>
                   <?php } ?>
                   </div>
               </div>
           </div>
        </div>
    </div>
    <div class="clear"></div>
            
</div> <!--end content-->

<?php
get_footer();
?>
