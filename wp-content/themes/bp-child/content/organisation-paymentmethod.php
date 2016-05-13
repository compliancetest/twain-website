<?php
/**
* Profile - My Payment Method Tab
*/
if(!defined('ABSPATH'))
    die('Invalid Request!');
    
    $organisation_id = getOrganisationID();
    $organisation = getOrganisationById($organisation_id);
    
    $organisationClass = new CT_Organisation($organisation_id);
    $cards = $organisationClass->get_payment_methods();
?>
<div class="column left four_sixths nopadding">
    <div class="grid-box table-box" id="my_payment">
        <div class="grid-box-header">
            <h5 class="left">Payment Methods</h5>
            <?php if($user_status != 3){?>                            
                <a class="gbh-btn gbh-btn-add right" id="add-payment-method" href="javascript: void(0);">Add<span class="simple_tooltip radius6">Add Payment Method<span></span></span></a>
                <!--<a href="javascript: void(0);" class="gbh-btn gbh-btn-view-stats has-tooltip right">View<span class="simple_tooltip radius6">View Statement<span></span></span></a>-->
            <?php }?>
            <div class="clear"></div>
        </div>
        <div class="grid-box-body">
          <div id="cards-list">
            <div class="thead tr">
               <div class="td td-nickname">Nickname</div>
               <div class="td td-card-number">Card Number</div>
               <div class="td td-reference">Reference</div>
               <div class="td td-status tocenter">Status</div>
               <div class="td td-default tocenter">Default?</div>
               <div class="td td-action tocenter">Action</div>
               <div class="clear"></div>
            </div>
            <div class="tbody">
                <?php if(!$cards){ ?>
                <div class="tr">
                   <div class="td td-full">No Payment Method Found! Please add new one.</div>
                   <div class="clear"></div>
               </div> 
               <?php }else{ ?>
               <?php foreach($cards as $card){ ?>
                <div class="tr">
                    <div class="td td-nickname">
                        <?php echo stripslashes( $card->nickname ); ?>
                        <input type="hidden" id="cnumber" value="<?php echo $card->card_number?>" />                                    
                    </div>                    
                    <div class="td td-card-number">
                        <?php if ($card->invoice_me == 0): ?>
                            <?php echo chunk_split($card->card_number, 4)?>
                        <?php else: ?>
                            Invoice
                        <?php endif; ?>
                    </div>
                    <div class="td td-reference">
                        <?php echo stripslashes( $card->customer_reference );?>
                    </div>
                    <div class="td td-status tocenter">
                        <span class="status_btn status_<?php echo strtolower($card->status)?> has-tooltip">
                            <?php echo $card->status?>
                            <span class="simple_tooltip radius6">
                                <?php echo $card->status == 'Active' ? 'The last transaction attempted with this payment method was successful.' : 'A problem has been encountered in using this payment method. Please confirm the details are correct.'?>
                            <span></span></span>
                        </span>
                    </div>
                    <div class="td td-default tocenter">
                        <?php echo ($card->is_default == '1') ? ('Yes') : (''); ?>
                    </div>
                    <div class="td td-action">
                        <a href="<?php echo get_permalink()?>?cp-action=<?php echo wp_create_nonce('edit_payment_method')?>&id=<?php echo $card->id ?>" class="edit-payment-method action-btn edit-btn icon-btn has-tooltip" data-id="<?php echo $card->id?>"><span class="p"></span><span class="simple_tooltip radius6">Edit Payment Method<span></span></span></a>
                        <a href="<?php echo get_permalink()?>?cp-action=<?php echo wp_create_nonce('delete_payment_method')?>&id=<?php echo $card->id ?>" class="delete-payment-method action-btn delete-btn icon-btn has-tooltip left10" data-id="<?php echo $card->id?>"><span class="p"></span><span class="simple_tooltip radius6">Delete Payment Method<span></span></span></a>
                        
                    </div>
                    <div class="clear"></div>
                </div>
                <?php } ?>
               <?php } ?>
            </div>
            <div class="loading loading-with-text"><div><b>LOADING DATA</b><span>Please wait...</span></div></div>
          </div>
            <div id="edit-card-form" style="display: none;">
                <form action="#" method="post">
                    <div class="grid-row">
                        <div class="grid-cell width30P"><label>Nickname</label></div>
                        <input type="text" name="nickname" id="nickname" value="" class="input" autocomplete="off" />                                    
                        <div class="clear"></div>
                    </div>
                    <div class="grid-row">
                        <div class="grid-cell width30P"><label>Customer Reference</label></div>
                        <input type="text" name="customer_reference" id="customer_reference" value="" class="input" autocomplete="off" /> 
                        <div class="clear"></div> 
                    </div>
                    <?php if ($organisation->invoice_me == 1): ?>
                    <div class="grid-row">
                        <div class="grid-cell width30P"><label>Payment Type</label></div>
                        <select name="invoice_me" id="invoice_me" class="select">
                            <option value="0">Credit Card</option>
                            <option value="1">Invoice Me</option>
                        </select>
                        <div class="clear"></div>
                    </div>
                    <?php else: ?>
                    <input type="hidden" name="invoice_me" value="0">
                    <?php endif; ?>
                    <div id="payment-cc-section">
                        <div class="grid-row">
                            <div class="grid-cell width30P"><label>Email</label></div>
                            <input type="text" name="email" id="email" value="" data-default="<?php echo $organisation->contact_email; ?>" class="input" autocomplete="off" />                                    
                            <div class="grid-cell width30P">&nbsp;</div>
                            <span class="desc">(Credit card payment notifications will be sent to this email.)</span>
                            <div class="clear"></div>
                        </div>
                        <div class="grid-row">
                            <div class="grid-cell width30P"><label>Card Number</label></div>
                            <input type="text" name="card_number" id="card_number" value="" class="input" autocomplete="off" /> 
                            <small class="cnumber-desc"><i>(Don't change this if you want keep original number)</i></small>
                            <div class="clear"></div> 
                        </div>
                        <div class="grid-row">
                            <div class="grid-cell width30P"><label>Name on Card</label></div>
                            <input type="text" name="name_on_card" id="name_on_card" value="" class="input" autocomplete="off" />                                    
                            <div class="clear"></div>
                        </div>
                        <div class="grid-row">
                            <div class="grid-cell width30P"><label>Expiry</label></div>
                            <input type="text" name="card_expiry" id="card_expiry" value="" class="input small_input" placeholder="M / Y" autocomplete="off" /> 
                            <div class="clear"></div> 
                        </div>
                        <div class="grid-row"> 
                            <div class="grid-cell width30P"><label>CVC</label></div> 
                            <input type="text" name="card_cvc" id="card_cvc" value="" class="input small_input" autocomplete="off" /> 
                            <div class="clear"></div> 
                        </div>
                    </div> 
                    <div class="grid-row">
                        <div class="grid-cell width30P"><label>Is Default?</label></div>
                        <select name="is_default" id="is_default" class="select">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                        <div class="clear"></div>
                    </div>
                    <div class="grid-row btn-row">
                        <a href="#" class="action-btn process-btn"><span class="p"></span><span class="t">Save</span></a>
                        <a href="#" class="action-btn cancel-btn left15"><span class="p"></span><span class="t">Cancel</span></a>
                        <div class="clear"></div>
                    </div>
                    <input type="hidden" name="organisation_id" id="organisation_id" value="" data-organisation-id="<?=$organisation_id?>"/>
                    <?php wp_nonce_field('save_payment_method', 'cp-action'); ?>
                    <input type="hidden" name="id" id="id" value="" />
                </form>
            </div>
        </div>
    </div>
</div>
<?php $my_payment_method_desc = get_post_meta($post->ID, 'my_payment_method_desc', true);?>
<?php if ($my_payment_method_desc): ?>
<div class="right two_sixths">
    <div class="gray_message_box radius9 light_gray_txt">
        <div class="indicator"></div>
        <?php echo $my_payment_method_desc;?>
    </div>
</div>
<?php endif; ?>

<?php
    $productOrganisations = json_decode($organisation->products_organisations, true);
    if(!$productOrganisations){
        $productOrganisations = [$organisation->organisation_name];
    }
?>

<div class="clear"></div>
<div class="space25"></div>

<div class="column left four_sixths nopadding">
    <div class="grid-box table-box" id="my_payment">
        <div class="grid-box-header">
            <h5 class="left">Products organisations  list</h5>
            <div class="clear"></div>
        </div>
        <div class="grid-box-body">
            <div id="edit-produts-form">
                <form action="#" method="post" id="org_products_form">
                    <div class="grid-row">
                        <div class="grid-cell width30P"><label>Nickname</label></div>
                        <input type="text" name="product_organisations" id="nickname" value="<?php echo implode(',', $productOrganisations);?>" class="input" autocomplete="off" style="width: 55%; float: left;margin-right: 10px;">
                        <a href="#" class="action-btn process-btn submit_org_products"><span class="p"></span><span class="t">Save</span></a>
                        <div class="clear"></div>
                    </div>
                    <input type="hidden" name="organisation_id" id="organisation_id" value="<?=$organisation_id?>"/>
                    <?php wp_nonce_field('save_products_organisations', 'cp-action'); ?>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $my_payment_method_desc = get_post_meta($post->ID, 'my_payment_method_desc', true);?>
<?php if ($my_payment_method_desc): ?>
<div class="right two_sixths">
    <div class="gray_message_box radius9 light_gray_txt">
        <div class="indicator"></div>
        List of allowed organisations for products(comma separated)
    </div>
</div>
<?php endif; ?>

<script>
    jQuery(document).ready(function(){
        jQuery('.submit_org_products').on('click', function(e){
            e.preventDefault();
            jQuery('#org_products_form').ajaxSubmit({
                success: function(data){
                    console.log(data)
                }
            });
        });
    });
</script>
