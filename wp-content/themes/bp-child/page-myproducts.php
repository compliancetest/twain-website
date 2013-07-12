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

$products = getUserProductsAndServices();

get_header();
?>
<div class="content" id="my_products">
    <div class="column fifth left nopaddingleft nopaddingright sidebar">
        <?php get_sidebar('dashboard'); ?>
    </div>
    
    <div class="four_fifths right container"> <!--Temporary -->
        <div class="column">
           <?php if(can_create_product_and_service()){ ?>
           <a href="/add-new-product-and-service" class="action-btn process-btn"><span class="p"></span><span class="t">Add new Product or Service</span></a>
           <div class="clear"></div>
           <div class="space20"></div>
           <?php } ?>
           <?php foreach($products as $product){ ?>
           <div class="grid-box grid-box-expandable table-box grid-box-opened">
               <div class="grid-box-header">
                   <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
                   <h5 class="left">Products: <a href="<?php echo get_permalink($product->ID)?>" class="view-product"><b><?php echo get_the_title($product)?></b></a></h5>
                   <?php if(can_edit_product_and_service($product->ID)){ ?>
                   <a class="gbh-btn gbh-btn-edit right" href="/edit-product-and-service?id=<?php echo $product->ID?>">Edit<span class="simple_tooltip radius6">Edit<span></span></span></a>
                   <?php } ?>
                   <?php if(can_delete_product_and_service($product->ID)){ ?>
                   <a class="gbh-btn gbh-btn-delete right" href="<?php get_permalink()?>?id=<?php echo $product->ID?>&_psnonce=<?php echo wp_create_nonce('delete-product') ?>&return=<?php echo base64_encode(get_permalink()) ?>" onclick="return confirm('Are you sure that you want to delete this product?')">Edit<span class="simple_tooltip radius6">Delete<span></span></span></a>
                   <?php } ?>
                   <div class="clear"></div>
               </div>
               <div class="grid-box-body">
                   <div class="thead tr">
                       <div class="td td-issuer">Issuer</div>
                       <div class="td td-suite">Suite</div>
                       <div class="td td-level">Level</div>
                       <div class="td td-role">Role</div>
                       <div class="td td-status">Status</div>
                       <div class="td td-date">Date</div>
                       <div class="td td-audit">Audit</div>
                       <div class="td td-action">Action</div>
                       <div class="clear"></div>
                   </div>
                   <div class="tbody">
                   <?php
                       $claims = getClaimsByProductId($product->ID);
                       if(!$claims){
                           ?>
                           <div class="tr">
                               <div class="td td-full">No compliance claim recorded yet</div>
                               <div class="clear"></div>
                           </div>
                           <?php
                       }else{
                           foreach($claims as $claim){
                           ?>
                           <div class="tr">
                               <div class="td td-issuer"><?php echo $claim->issuer ?></div>
                               <div class="td td-suite"><a href="<?php echo get_permalink($claim->suite_id)?>"><?php echo get_the_title($claim->suite_id)?></a></div>
                               <div class="td td-level"><?php echo $claim->conformance_level?></div>
                               <div class="td td-role"><?php echo $claim->role?></div>
                               <div class="td td-status status-<?php echo convert_css_name($claim->status) ?>"><?php echo $claim->status?></div>
                               <div class="td td-date"><?php echo formatDate($claim->last_updated)?></div>
                               <div class="td td-audit"><?php //echo $claim->audit?></div>
                               <div class="td td-action">
                                   <a href="<?php echo get_permalink()?>?_claimnonce=<?php echo wp_create_nonce('edit-claim')?>&product_id=<?php echo $product->ID?>&id=<?php echo $claim->id?>" data-product-id="<?php echo $product->ID?>" cp-type="ajax" cp-removeBoxAfterClose=1 class="action-btn blue-edit-btn icon-btn edit-claim-btn"><span class="p"></span></a>
                                   <a href="<?php echo get_permalink()?>?_claimnonce=<?php echo wp_create_nonce('delete-claim')?>&product_id=<?php echo $product->ID?>&id=<?php echo $claim->id?>&return=<?php echo base64_encode(get_permalink()) ?>" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>
                               </div>
                               <div class="clear"></div>
                           </div>
                           <?php
                           }
                       }
                   ?>  
                   </div>
               </div>
               <?php if(can_make_compliance_claim($product->ID)){ ?>
               <a href="<?php echo get_permalink()?>?_claimnonce=<?php echo wp_create_nonce('edit-claim')?>&product_id=<?php echo $product->ID?>" data-product-id="<?php echo $product->ID?>" cp-type="ajax" cp-removeBoxAfterClose=1 class="action-btn process-btn add-claim-btn"><span class="p"></span><span class="t">New Compliance Claim</span></a>
               <?php } ?>
           </div>
           <div id="obligation<?php echo $product->ID?>" style="display: none;">
               <?php 
                   $pCommunityID = get_post_meta($product->ID, 'community_id', 'single');
                   echo groups_get_groupmeta($pCommunityID, 'obligation_for_claim') 
               ?>   
           </div>
           <div class="clear"></div>
           <div class="space20"></div>
           <?php } ?>
        </div>           
    </div>
    <div class="clear"></div>
    <div class="popup-box" id="obligation-box" style="display: none; width: 500px">
        <div class="popup-box-header radius6 noradiusbottom">Compliance Claim Form</div>
        <div class="popup-box-content"> 
            
        </div>
        <div class="popup-box-footer radius6 noradiustop">                                    
            <a href="#make-claim-box" cp-type="inline"  class="action-btn cancel-btn"><span class="p"></span><span class="t">Close</span></a>
            <a href="#make-claim-box" cp-type="inline"  class="action-btn process-btn"><span class="p"></span><span class="t">ACCEPT</span></a>
            <div class="clear"></div>
        </div>
        <a class="close_btn"></a>                
    </div>        
</div> <!--end content-->
<script type="text/javascript">
(function($){
    $(document).ready(function(){
        $('#my_products .grid-box-body .tbody').each(function(){
            $(this).find('.tr').each(function(){
                var h = Math.max(
                    $(this).find('.td:eq(0)').outerHeight(),
                    $(this).find('.td:eq(1)').outerHeight(),
                    $(this).find('.td:eq(2)').outerHeight(),
                    $(this).find('.td:eq(3)').outerHeight(),
                    $(this).find('.td:eq(4)').outerHeight(),
                    $(this).find('.td:eq(5)').outerHeight(),
                    $(this).find('.td:eq(6)').outerHeight()
                );
                $(this).find('.td:lt(7)').height(h - 16);
                $(this).find('.td:eq(7)').height(h - 6);
            })
        });
        
        $('.add-claim-btn, .edit-claim-btn').cplightbox({
            onLoad: function(){
                $('#obligation-box .popup-box-content').html($('#obligation' + this.self.attr('data-product-id')).html());
                $('#show-opligation-box').cplightbox({closeWhenClickOveraly: 0});
                $('#make-claim-box .process-btn').click(function(){
                    makeClaim();
                    return false;
                });
                
                //Getting Level and Roles
                $('#make-claim-box #suite_id').change(function(){
                    if($(this).val() == '')
                    {
                        $('#make-claim-box #level').replaceWith('<select class="select" name="level" id="level"><option value="">Select a Level</option></select>');
                        $('#make-claim-box #role').replaceWith('<select class="select" name="role" id="role"><option value="">Select a Role</option></select>');
                        return;
                    }
                    $('#make-claim-box .loading1').show();
                    $.ajax({
                        url: '<?php echo get_site_url()?>',
                        data: {
                            'suite_id': $(this).val(),
                            '_claimnonce': '<?php echo wp_create_nonce('get-suite-info-for-claim')?>'
                        },
                        type: 'POST',
                        dataType: 'xml',
                        complete: function(){
                            $('#make-claim-box .loading1').hide();
                        },
                        success: function(rsp)
                        {
                            if($(rsp).find('status').text() == 'success')
                            {
                                $('#make-claim-box #level').replaceWith($(rsp).find('conflevel').text());
                                $('#make-claim-box #role').replaceWith($(rsp).find('roles').text());
                                
                            }                
                        }
                    })
                })
            }
            
        });
        $('#obligation-box .cancel-btn').cplightbox();
        $('#obligation-box .process-btn').cplightbox({
            onClose: function(){
                $('#agree_obligation').prop('checked', true);
            }
        })
        
        //Make Claim
        function makeClaim()
        {
            var form = $('#makeClaimForm');
            form.find('.message').remove();
            if(!form.find('#suite_id').val() || !form.find('#level').val() || !form.find('#role').val())
            {
                form.find('.popup-box-content').append('<div class="message error">Please complete all fields in the form.</div>');    
                return false;
            }
            if(!$('#agree_obligation').prop('checked'))
            {
                form.find('.popup-box-content').append('<div class="message error">You must agree the Obligation.</div>');    
                return false;
            }
            form.submit();
        }
        
        //Custom popup for test case boxes
        $('.view-product').each(function(){
            $(this).cplightbox({
                href: $(this).attr('href') + '&is_ajax=true',
                removeBoxAfterClose: true,
                type: 'ajax'                
            })
        })
    })
})(jQuery)
    
</script>
<?php
get_footer();
?>
