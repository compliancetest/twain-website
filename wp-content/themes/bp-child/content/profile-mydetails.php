<?php
/**
* Profile - My Details Tab
*/
if(!defined('ABSPATH'))
    die('Invalid Request!');
    
?>
<div class="left three_fifths">                
    <div class="grid-box" id="my_details">
        <div class="grid-box-header">
            <h5 class="left">My Details</h5>
            <?php if($user_status != 3){?>
                <a class="gbh-btn gbh-btn-edit right" href="javascript: void(0);">Edit<span class="simple_tooltip radius6">Edit this section<span></span></span></a>
                <a href="<?php bp_loggedin_user_link() ?>" class="gbh-btn gbh-btn-view-stats has-tooltip right">View<span class="simple_tooltip radius6">View Public Profile<span></span></span></a>
            <?php }?>
            <div class="clear"></div>
        </div>
        <?php if($user_status != 3){?>
        <div class="grid-box-body">
            <form action="#" method="post">
                <div class="grid-row">
                    <div class="grid-cell width30P"><label>First Name</label></div>
                    <div data-name="first_name" data-value="<?php echo $fname;?>" class="grid-cell in_input"><?php echo $fname;?></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-row">
                    <div class="grid-cell width30P"><label>Last Name</label></div>
                    <div data-name="last_name" data-value="<?php echo $lname;?>" class="grid-cell in_input"><?php echo $lname;?></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-row">
                    <div class="grid-cell width30P"><label>Email</label></div>
                    <div data-name="email" data-value="<?php echo $uemail;?>" class="grid-cell in_input"><?php echo $uemail;?></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-row">
                    <div class="grid-cell width30P"><label>Phone Number</label></div>
                    <div data-name="phone_number" data-value="<?php echo $phone;?>" class="grid-cell in_input"><?php echo !$phone ? '-' : $phone;?></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-row">
                    <div class="grid-cell width30P"><label>Password</label></div>
                    <div data-name="new_pass" data-value="" class="grid-cell in_input input_pass" data-type="password">*********</div>
                    <div class="clear"></div>
                </div>
                <div class="grid-row">
                    <div class="grid-cell width30P"><label>Confirm Password</label></div>
                    <div data-name="conf_pass" data-value="" class="grid-cell in_input input_pass" data-type="password">*********</div>
                    <div class="clear"></div>
                </div>
                <div class="grid-row">
                    <div class="grid-cell width30P"><label>About me</label></div>
                    <div data-name="biography" data-value="<?php echo $biography?>" class="grid-cell in_input" data-type="textarea"><?php echo !$biography ? '-' : _convertLineSymbolToBR($biography)?></div>
                    <div class="clear"></div>
                    <?php wp_nonce_field('my_details_edit', 'cp-action'); ?>
                </div>
                <div class="grid-row field-row">
                    <div class="grid-cell width30P"><label>Timezone settings</label></div>
                    <div class="grid-cell timezone-text" data-type="select" data-value="<?php echo $timezone?>"><?php echo $timezone?></div>
                    <?php $timezone_list = DateTimeZone::listIdentifiers(DateTimeZone::ALL); ?>
                    <select name="timezone" id="timezone" class="select" style="display: none;">
                        <?php foreach($timezone_list as $t): ?>
                            <option value="<?php echo $t; ?>" <?php echo cp_selected($t, $timezone)?>><?php echo $t; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="clear"></div>
                </div>
                <div class="grid-row field-row">
                    <div class="grid-cell width30P"><label>Desired First Page</label></div>
                    <div class="grid-cell width70P">
                        <div id="dashboard-page-path"><?php echo $dashboard_page_title; ?></div>
                        <input type="hidden" name="dashboard_page_url" value="<?php echo $dashboard_page_url; ?>">
                        <input type="hidden" name="dashboard_page_title" value="<?php echo $dashboard_page_title; ?>">
                        <div id="dashboard-pages">
                            <a href="javascript:void(0)" class="action-btn file-btn has-tooltip" id="browse-dashboard-pages">
                                <span class="p"></span><span class="t">Browse</span>
                                <span class="simple_tooltip radius6 no-wrap">Browse Pages<span></span></span>
                            </a>
                            <div class="clear"></div>
                            <?php echo getDashboardMenuHTML(getDashboardPages('page'), 'dashboard-pages-dropdown'); ?>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="grid-row btn-row">
                    <a href="#" class="action-btn process-btn "><span class="p"></span><span class="t">Confirm</span></a>
                    <a href="#" class="action-btn cancel-btn edit-cancel-btn left10"><span class="p"></span><span class="t">Cancel</span></a>
                    <div class="clear"></div>
                </div>
            </form>
        </div>
        <?php } ?>
    </div>                
    <div class="clear"></div>            
</div>
<?php $my_details_desc = get_post_meta($post->ID, 'my_details_desc', true);?>
<?php if ($my_details_desc): ?>
<div class="right two_fifths">
    <div class="gray_message_box radius9 light_gray_txt">
        <div class="indicator"></div>
        <?php echo $my_details_desc;?>
    </div>
</div>
<?php endif; ?>