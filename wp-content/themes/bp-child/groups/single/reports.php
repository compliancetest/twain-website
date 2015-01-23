<?php
/**
 * Reports
 */
global $groups_template;

$group = $groups_template->group;

$reports = new CP_Reports_Group_Extension();
?>
<div id="downloads-container" class="tab-content white_bcg padding10">
    <!-- Files List Page -->
    <div id="uploaded-files">
        <!--          <form name="fileListForm" action="" method="post">-->
        <div class="grid-list">
            <div class="grid-list-row grid-list-header">
                <div class="grid-list-cell width40P">Current Testing Status</div>
                <div class="grid-list-cell width40P">
                    <a class="left10 action-btn icon-btn download-btn has-tooltip" href="/?_download_report_nonce=<?php echo 'test';//wp_create_nonce( 'download_community_report' );?>&cid=<?php echo $groups_template->group->id;?>"><span class="p"></span><span class="simple_tooltip radius6" style="top: -27px;">Report<span></span></span></a>
                </div>
                <div class="clear"></div>
            </div>
            <?php
            ?>
        </div>
        <!--          </form>-->
    </div>
</div>
