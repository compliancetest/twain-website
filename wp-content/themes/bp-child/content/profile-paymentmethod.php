<?php
/**
* Profile - My Payment Method Tab
*/
if(!defined('ABSPATH'))
    die('Invalid Request!');
    
?>
<?php
    $cards = getUserCreditCards();
?>
<div class="column left three_fifths nopadding">
    <div class="grid-box" id="my_payment">
        <div class="grid-box-header">
            <h5 class="left">My Payment Method</h5>
            <?php if($user_status != 3){?>                            
                <a class="gbh-btn gbh-btn-add right" id="add-payment-method" href="javascript: void(0);">Add<span class="simple_tooltip radius6">Add Payment Method<span></span></span></a>
                <a href="javascript: void(0);" class="gbh-btn gbh-btn-view-stats has-tooltip right">View<span class="simple_tooltip radius6">View Statement<span></span></span></a>
            <?php }?>
            <div class="clear"></div>
        </div>
        <div class="grid-box-body">
            <div id="cards-list">
              <?php if(!$cards){ ?>
                <div class="grid-row">
                    <div class="grid-cell width100P">No Payment Method Found! Please add new one.</div>
                    <div class="clear"></div>
                </div>
              <?php }else{ ?>
                <?php foreach($cards as $card){ ?>
                <div class="grid-row grid-action-row">
                    <div class="grid-cell width80P">
                        <?php echo $card->nickname . " " . chunk_split($card->card_number, 4)?>
                        <input type="hidden" id="cnumber" value="<?php echo $card->card_number?>" />                                    
                    </div>
                    <div class="grid-cell grid-action-cell width20P">
                        <a href="<?php echo get_permalink()?>?cp-action=<?php echo wp_create_nonce('delete_payment_method')?>&id=<?php echo $card->id ?>" class="delete-payment-method gbh-btn gbh-btn-delete-grey has-tooltip" data-id="<?php echo $card->id?>">Delete<span class="simple_tooltip radius6">Delete Card<span></span></span></a>
                        <a href="<?php echo get_permalink()?>?cp-action=<?php echo wp_create_nonce('edit_payment_method')?>&id=<?php echo $card->id ?>" class="edit-payment-method gbh-btn gbh-btn-edit-grey has-tooltip" data-id="<?php echo $card->id?>">Edit<span class="simple_tooltip radius6">Edit Card<span></span></span></a>
                    </div>
                    <div class="clear"></div>
                </div>
                <?php } ?>
              <?php } ?>
            </div>
            <div id="edit-card-form" style="display: none;">
                <form action="#" method="post">
                    <div class="grid-row">
                        <div class="grid-cell width30P"><label>NickName</label></div>
                        <input type="text" name="nickname" id="nickname" value="" class="input" autocomplete="off" />                                    
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
                    <div class="grid-row btn-row">
                        <a href="#" class="action-btn process-btn"><span class="p"></span><span class="t">Save</span></a>
                        <a href="#" class="action-btn cancel-btn left15"><span class="p"></span><span class="t">Cancel</span></a>
                        <div class="clear"></div>
                    </div>
                    <?php wp_nonce_field('save_payment_method', 'cp-action'); ?>
                    <input type="hidden" name="id" id="id" value="" />
                </form>
            </div>
        </div>
    </div>
</div>
<?php $my_payment_method_desc = get_post_meta($post->ID, 'my_payment_method_desc', true);?>
<?php if ($my_payment_method_desc): ?>
<div class="right two_fifths">
    <div class="gray_message_box radius9 light_gray_txt">
        <div class="indicator"></div>
        <?php echo $my_payment_method_desc;?>
    </div>
</div>
<?php endif; ?>