<?php
/**
* Profile - My Organisation
*/
if(!defined('ABSPATH'))
    die('Invalid Request!');
    
?>

<div class="column left three_fifths nopadding">
    <div class="grid-box" id="my_org">
        <div class="grid-box-header">
            <h5 class="left">My Organisation</h5>
            <?php if($user_status != 3){?>
                <a class="gbh-btn gbh-btn-edit right" href="javascript: void(0);">Edit<span class="simple_tooltip radius6">Edit this section<span></span></span></a>
            <?php }?>
            <span class="header-text right">Role: <?php echo $urole;?></span>
            <div class="clear"></div>
        </div>
        <div class="grid-box-body">
            <form action="#" method="post">
                <div class="grid-row">
                    <div class="grid-cell width30P"><label>Name</label></div>
                    <div data-name="user_organisation" data-value="<?php echo $user_org;?>" class="grid-cell in_input width70P"><?php echo !$user_org ? '-' : $user_org;?></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-row">
                    <div class="grid-cell width30P"><label>Website</label></div>
                    <div data-name="user_organisation_web" data-value="<?php echo $user_org_web;?>" class="grid-cell in_input"><?php echo !$user_org_web ? '-' : $user_org_web;?></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-row">
                    <div class="grid-cell width30P"><label>Description</label></div>
                    <div data-name="user_organisation_desc" data-value="<?php echo $user_org_desc;?>" class="grid-cell in_input"><?php echo !$user_org_desc ? '-' : $user_org_desc;?></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-row">
                    <div class="grid-cell width30P"><label>ABN</label></div>
                    <div data-name="user_organisation_abn" data-value="<?php echo $user_org_abn;?>" class="grid-cell in_input"><?php echo !$user_org_abn ? '-' : $user_org_abn;?></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-row btn-row">
                    <a href="#" class="action-btn process-btn"><span class="p"></span><span class="t">Save</span></a>
                    <a href="#" class="action-btn cancel-btn edit-cancel-btn left10"><span class="p"></span><span class="t">Cancel</span></a>
                    <?php wp_nonce_field('my_organisation_edit', 'cp-action'); ?>
                    <div class="clear"></div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $my_organisation_desc = get_post_meta($post->ID, 'my_organisation_desc', true);?>
<?php if($my_organisation_desc): ?>
<div class="right two_fifths">
    <div class="gray_message_box radius9 light_gray_txt">
        <div class="indicator"></div>
        <?php echo $my_organisation_desc; ?>
    </div>
</div>
<?php endif; ?>