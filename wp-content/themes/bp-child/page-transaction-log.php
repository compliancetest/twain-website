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
            </div>            
            <div class="clear"></div>
        </div> 
        <div class="grid-box table-box">
       <div class="grid-box-header">
           <h5><b>SuperStream MCS v1.1</b></h5>                   
           <div class="clear"></div>
       </div>
       <div class="grid-box-body">
           <div class="thead tr">
               <div class="td td-product">Product</div>
               <div class="td td-conflevel">Conf Level</div>
               <div class="td td-coverage">Coverage</div>
               <div class="td td-action">Action</div>
               <div class="clear"></div>
           </div>
           <div class="tbody">
               <div class="tr">
                   <div class="td td-product">Product</div>
                   <div class="td td-conflevel">Conf Level</div>
                   <div class="td td-coverage">
                       <div class="coverage-progress"><span class="bar0"></span></div>    
                   </div>
                   <div class="td td-action">
                      <a href="#" class="action-btn view-log-btn"><span class="p"></span><span class="t">View Log</span></a>
                      <a href="#" class="action-btn certify-btn"><span class="p"></span><span class="t">Certify</span></a>
                   </div>
                   <div class="clear"></div>
               </div>
               <div class="tr">
                   <div class="td td-product">Product</div>
                   <div class="td td-conflevel">Conf Level</div>
                   <div class="td td-coverage">Coverage</div>
                   <div class="td td-action">
                      <a href="#" class="action-btn view-log-btn"><span class="p"></span><span class="t">View Log</span></a>
                      <a href="#" class="action-btn certify-grey-btn"><span class="p"></span><span class="t">Certify</span></a>
                   </div>
                   <div class="clear"></div>
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
