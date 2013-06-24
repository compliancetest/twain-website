<?php
/**
* Template Name: My Products
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
<div class="content" id="my_products">
    <div class="space25"></div>
    <div class="column fifth left nopaddingleft nopaddingright sidebar">
        <?php get_sidebar('dashboard'); ?>
    </div>
    
    <div class="four_fifths right container">
        <div class="column">
            <h2>My Products</h2>
            <div class="space10"></div>
            <div class="message notice">This function is under construction</div>
        </div>
        <div class="space100"></div>
    </div>
    
    <div class="four_fifths right container" style="display: none;"> <!--Temporary -->
        <div class="column">
           <a href="<?php echo esc_url( get_permalink( get_page_by_title( 'Create / Edit Product or Service' ) ) ); ?>" class="action-btn process-btn">
            <span class="p"></span><span class="t">Add new Product or Service</span>
           </a>
           <div class="clear"></div>
           <div class="space20"></div>
           
           <div class="grid-box grid-box-expandable table-box grid-box-closed">
               <div class="grid-box-header">
                   <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
                   <h5 class="left">Products: <b>SuperStream MCS v1.1</b></h5>
                   <a class="gbh-btn gbh-btn-edit right" href="javascript: void(0);">Edit<span class="simple_tooltip radius6">Edit this section<span></span></span></a>
                   <div class="clear"></div>
               </div>
               <div class="grid-box-body" style="display: none;">
                   <div class="thead tr">
                       <div class="td td-issuer">Issuer</div>
                       <div class="td td-suite">Suite</div>
                       <div class="td td-role">Role</div>
                       <div class="td td-status">Status</div>
                       <div class="td td-date">Date</div>
                       <div class="td td-audit">Audit</div>
                       <div class="td td-action">Action</div>
                       <div class="clear"></div>
                   </div>
                   <div class="tbody">
                       <div class="tr">
                           <div class="td td-issuer">Issuer</div>
                           <div class="td td-suite"><a href="#">Suite</a></div>
                           <div class="td td-role">Role</div>
                           <div class="td td-status"><span class="status-certified">Certified</span></div>
                           <div class="td td-date">Date</div>
                           <div class="td td-audit">Audit</div>
                           <div class="td td-action">Action</div>
                           <div class="clear"></div>
                       </div>
                       <div class="tr">
                           <div class="td td-issuer">Issuer</div>
                           <div class="td td-suite">Suite</div>
                           <div class="td td-role">Role</div>
                           <div class="td td-status"><span class="status-testing">Testing</span></div>
                           <div class="td td-date">Date</div>
                           <div class="td td-audit">Audit</div>
                           <div class="td td-action">Action</div>
                           <div class="clear"></div>
                       </div>
                   </div>
               </div>
           </div>
           <div class="space20"></div>
           
           <div class="grid-box grid-box-expandable table-box grid-box-closed">
               <div class="grid-box-header">
                   <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
                   <h5 class="left">Products: <b>SuperStream MCS v1.1</b></h5>
                   <a class="gbh-btn gbh-btn-edit right" href="javascript: void(0);">Edit<span class="simple_tooltip radius6">Edit product<span></span></span></a>
                   <div class="clear"></div>
               </div>
               <div class="grid-box-body" style="display: none;">
                   <div class="thead tr">
                       <div class="td td-issuer">Issuer</div>
                       <div class="td td-suite">Suite</div>
                       <div class="td td-role">Role</div>
                       <div class="td td-status">Status</div>
                       <div class="td td-date">Date</div>
                       <div class="td td-audit">Audit</div>
                       <div class="td td-action">Action</div>
                       <div class="clear"></div>
                   </div>
                   <div class="tbody">
                       <div class="tr">
                           <div class="td td-issuer">Issuer</div>
                           <div class="td td-suite">Suite</div>
                           <div class="td td-role">Role</div>
                           <div class="td td-status">Status</div>
                           <div class="td td-date">Date</div>
                           <div class="td td-audit">Audit</div>
                           <div class="td td-action">Action</div>
                           <div class="clear"></div>
                       </div>
                       <div class="tr">
                           <div class="td td-issuer">Issuer</div>
                           <div class="td td-suite">Suite</div>
                           <div class="td td-role">Role</div>
                           <div class="td td-status">Status</div>
                           <div class="td td-date">Date</div>
                           <div class="td td-audit">Audit</div>
                           <div class="td td-action">Action</div>
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
