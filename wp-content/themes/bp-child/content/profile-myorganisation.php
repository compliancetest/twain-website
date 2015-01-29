<?php
/**
* Profile - My Organisation
*/
if(!defined('ABSPATH'))
    die('Invalid Request!');
    
$org_membership = ct_get_user_organisation_membership($current_user->ID);

if (!$org_membership) {
    $user_org = get_user_meta($current_user->ID, 'user_organisation', true);    
    $user_org_abn = get_user_meta($current_user->ID, 'user_organisation_abn', true);            
    $user_org_web = get_user_meta($current_user->ID, 'user_organisation_web', true);
    $user_org_desc = get_user_meta($current_user->ID, 'user_organisation_desc', true);
} else {
    $org_detail = new CT_Organisation($org_membership->organisation_id);
    $user_org = $org_detail->organisation_name;
    $user_org_abn = $org_detail->abn;
    $user_org_web = $org_detail->organisation_website;
    $user_org_desc = $org_detail->organisation_description;
}
?>

<div class="column left three_fifths nopadding">
    <div class="grid-box" id="my_org">
        <div class="grid-box-header">
            <h5 class="left">My Organisation</h5>
            <?php if($user_status != 3 ){?>
                <?php if( ! $org_membership ):?>
                    <a class="gbh-btn right create_organisation gbh-btn-create-join" href="javascript: void(0);">Create Organisation<span class="simple_tooltip radius6" style="width: 130px;margin-left: -60px;">Create Organisationn<span></span></span></a>
                    <a class="gbh-btn right gbh-btn-create-org join_organisation" href="javascript: void(0);">Join Organisation<span class="simple_tooltip radius6">Join Organisation<span></span></span></a>
                <?php else:?>
                    <?php if( ! $org_membership->is_admin ):?>
                        <a href="<?php echo get_site_url()?>?cp-action=<?php echo wp_create_nonce('leave_organisation')?>" class="gbh-btn gbh-btn-leave right">Leave Organisation<span class="simple_tooltip radius6">Leave Organisation<span></span></span></a>
                    <?php endif;?>
                <?php endif;?>
            <?php }?>
            <div class="clear"></div>
        </div>
        <div class="grid-box-body">
            <?php if( ! $org_membership ):?>
                <div class="grid-row edit_org_text" data-state="1">
                    At present you are <b>not part of any organisation</b> known to ComplianceTest.
                    <div style="padding-bottom: 5px;"></div>
                    If you plan to undertake testing with ComplianceTest, you either need to <a href="#" class="join_organisation">join an existing organisation</a>
                    or <a href="#" class="create_organisation">create a new organisation</a> and become its administrator.
                    <div style="padding-bottom: 5px;"></div>
                    <i style="font-weight: lighter;">To join an existing organisation, you will need to know its organisation key, which your organisation administrator can provide.</br>
                        To create a new organisation, you will need its name and ABN as a minimum.</i>
                </div>
                <form action="/" method="get" class="edit_org" style="display: none;">
                    <div class="grid-row">
                        <div class="grid-cell width30P"><label>Name</label></div>
                        <input type="text" name="user_organisation" value="" class="grid-cell in_input width70P">
                        <div class="clear"></div>
                    </div>
                    <div class="grid-row">
                        <div class="grid-cell width30P"><label>Website</label></div>
                        <input type="text" name="user_organisation_web" value="" class="grid-cell in_input width70P">
                        <div class="clear"></div>
                    </div>
                    <div class="grid-row">
                        <div class="grid-cell width30P"><label>Description</label></div>
                        <input type="text" name="user_organisation_desc" value="" class="grid-cell in_input width70P">
                        <div class="clear"></div>
                    </div>
                    <div class="grid-row">
                        <div class="grid-cell width30P"><label>ABN</label></div>
                        <input type="text" name="user_organisation_abn" value="" class="grid-cell in_input width70P">
                        <div class="clear"></div>
                    </div>
                    <div class="grid-row btn-row">
                        <a href="#" class="action-btn process-btn do_not_process"><span class="p"></span><span class="t">Save</span></a>
                        <a href="#" class="action-btn cancel-btn left10 create_organisation"><span class="p"></span><span class="t">Cancel</span></a>
                        <?php wp_nonce_field('my_organisation_edit', 'cp-action'); ?>
                        <div class="clear"></div>
                    </div>
                </form>

                <form action="/" method="get" class="join_org" style="display: none;">
                    <div class="grid-row">
                        <div class="grid-cell width30P"><label>Organisation Key</label></div>
                        <input type="text" name="user_organisation_key" value="" class="grid-cell in_input width70P user_organisation_key">
                        <div class="has-defined-tooltip">
                            <span class="simple_tooltip" style="width: 370px; bottom: 33px; margin-left: -110px;"><span></span>If your organisation is already registered on ComplianceTest, ask your administrator for your organisation key to immediately become a member of your organisation on ComplianceTest. If not, you will be asked if you'd like to register your organisation when attempting to access the harness details on a test suite summary page.</span>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="grid-row btn-row">
                        <a href="#" class="action-btn process-btn join_organisation_submit do_not_process"><span class="p"></span><span class="t">Save</span></a>
                        <a href="#" class="action-btn cancel-btn left10 join_organisation"><span class="p"></span><span class="t">Cancel</span></a>
                        <?php wp_nonce_field('my_organisation_join', 'cp-action'); ?>
                        <div class="clear"></div>
                    </div>
                </div>
            <?php else:?>
                <div class="grid-row">
                    <div class="grid-cell width30P"><label>Name</label></div>
                    <div data-name="user_organisation_web" data-value="<?php echo $user_org;?>"  class="grid-cell in_input"><?php echo !$user_org ? '-' : $user_org;?></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-row">
                    <div class="grid-cell width30P"><label>Website</label></div>
                    <div data-name="user_organisation_web" data-value="<?php echo $user_org_web;?>"  class="grid-cell in_input"><?php echo !$user_org_web ? '-' : $user_org_web;?></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-row">
                    <div class="grid-cell width30P"><label>Description</label></div>
                    <div data-name="user_organisation_desc" data-value="<?php echo $user_org_desc;?>"  class="grid-cell width70P in_input"><?php echo !$user_org_desc ? '-' : $user_org_desc;?></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-row">
                    <div class="grid-cell width30P"><label>ABN</label></div>
                    <div data-name="user_organisation_abn" data-value="<?php echo $user_org_abn;?>" class="grid-cell in_input"><?php echo !$user_org_abn ? '-' : $user_org_abn;?></div>
                    <div class="clear"></div>
                </div>
            <?php endif;?>
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