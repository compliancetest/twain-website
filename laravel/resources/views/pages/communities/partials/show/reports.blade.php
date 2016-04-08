<div id="downloads-container" class="tab-content white_bcg padding10">
    <div id="uploaded-files">
        <div class="grid-list">
            <div class="grid-list-row grid-list-header">
                <div class="grid-list-cell width40P">Current Testing Status</div>
                <div class="grid-list-cell width40P">
                    <a class="left10 action-btn icon-btn download-btn has-tooltip" href="<?php echo S3Wrapper::getLink( 'reports/'.$community->title.'/'.get_option( 'reports_token_'.$community->id), $community->title.'TestProgress.xls' );?>"><span class="p"></span><span class="simple_tooltip radius6" style="top: -27px;">Report<span></span></span></a>
                </div>
                <div class="clear"></div>
            </div>
            <?php
            ?>
        </div>
    </div>
</div>