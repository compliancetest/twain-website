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
?>

<div class="content" id="my_transaction_log">
    <div class="column fifth left nopaddingleft nopaddingright sidebar">
        <?php get_sidebar('dashboard'); ?>
    </div>
    <div class="four_fifths right container">
        <div class="column">
            <h2>My Transaction Log</h2>
            <div class="space10"></div>
            <div class="message notice">This function is under construction</div>
        </div>
        <div class="space100"></div>
    </div>
    <div class="four_fifths right container" style="display: none;">
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
                       <div class="tr">
                           <div class="td td-product">Product Name</div>
                           <div class="td td-case">Test<br />Case</div>
                           <div class="td td-suite">Test<br />Suite</div>
                           <div class="td td-outcome tocenter">Test<br />Outcome</div>
                           <div class="td td-audit tocenter">Audit<br />Record</div>
                           <div class="td td-service">Service</div>
                           <div class="td td-convsn">Convsn</div>
                           <div class="td td-date tocenter">Date</div>
                           <div class="td td-from">From</div>
                           <div class="td td-to">To</div>
                           <div class="td td-customer">Customer</div>
                           <div class="clear"></div> 
                       </div>
                       <div class="tr">
                           <div class="td td-product">Product Name</div>
                           <div class="td td-case">Test<br />Case</div>
                           <div class="td td-suite">Test<br />Suite</div>
                           <div class="td td-outcome">Test<br />Outcome</div>
                           <div class="td td-audit">Audit<br />Record</div>
                           <div class="td td-service">Service</div>
                           <div class="td td-convsn">Convsn</div>
                           <div class="td td-date">12/22/15</div>
                           <div class="td td-from">AMP0195AU</div>
                           <div class="td td-to">AMP0195AU</div>
                           <div class="td td-customer">Customer</div>
                           <div class="clear"></div> 
                       </div>
                       
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
