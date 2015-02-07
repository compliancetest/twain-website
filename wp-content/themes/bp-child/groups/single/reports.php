<?php
/**
 * Reports
 */
global $groups_template;

$group = $groups_template->group;

$reports = new CP_Reports_Group_Extension();
if( get_option( 'reports_date' ) !== date( 'Y-m-d' ) ){
    send_reports_to_s3();
}
$s3= new S3Wrapper();
?>
<div id="downloads-container" class="tab-content white_bcg padding10">
    <div id="uploaded-files">
        <div class="grid-list">
            <div class="grid-list-row grid-list-header">
                <div class="grid-list-cell width40P">Current Testing Status</div>
                <div class="grid-list-cell width40P">
                    <a class="left10 action-btn icon-btn download-btn has-tooltip" href="<?php echo $s3->getLink( 'reports/'.$groups_template->group->name.'/'.get_option( 'reports_token_'.$groups_template->group->id ), $groups_template->group->name.'TestProgress.xls' );?>"><span class="p"></span><span class="simple_tooltip radius6" style="top: -27px;">Report<span></span></span></a>
                </div>
                <div class="clear"></div>
            </div>
            <?php
            ?>
        </div>
    </div>
</div>
