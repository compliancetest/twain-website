<ul class="tabs <?php echo $community->hasAccess() ? 'no-ajax' : '' ?>">
    <li class="<?php echo ($action == 'testsuites') ? 'active' : ''?>">
        <a href="<?php echo $community->getUrl()?>" rel="testsuites-container" class="<?php echo ($action == 'testsuites' || $action == '') ? 'selected' : ''?>">
            <span class="left icon" id="icon_test_suites"></span>
            <span class="right text">TEST SUITES</span>
            <span class="tabactive"></span>
            <span class="clear"></span>
        </a>
    </li>
    <li class="<?php echo ($action == 'testdata') ? 'active' : ''?>">
        <a href="<?php echo $community->getUrl()?>testdata" rel="testdata-container" class="<?php echo ($action == 'testdata') ? 'selected' : ''?>">
            <span class="left icon" id="icon_testdata"></span>
            <span class="right text">TEST DATA</span>
            <span class="tabactive"></span>
            <span class="clear"></span>
        </a>
    </li>

    @if(@$communityMeta['wiki-status'] == '1')

        <li class="<?php echo ($action == 'wiki') ? 'active' : ''?>">
            <a href="<?php echo $community->getUrl();?>wiki" rel="wiki-container" class="<?php echo ($action == 'wiki') ? 'selected' : ''?>">
                <span class="left icon" id="icon_wiki"></span>
                <span class="right text">ARTICLES</span>
                <span class="tabactive"></span>
                <span class="clear"></span>
            </a>
        </li>

    @endif

    @if(@$communityMeta['forum_id'] > 0)

        <li class="<?php echo ($action == 'forum') ? 'active' : ''?>">
            <a href="<?php echo $community->getUrl()?>forum" rel="forum-container" class="<?php echo ($action == 'forum') ? 'selected' : ''?>">
                <span class="left icon" id="icon_forum"></span>
                <span class="right text">FORUM</span>
                <span class="tabactive"></span>
                <span class="clear"></span>
            </a>
        </li>

    @endif

    <li class="<?php echo ($action == 'downloads') ? 'active' : ''?>">
        <a href="<?php echo $community->getUrl()?>downloads" rel="downloads-container" class="<?php echo ($action == 'downloads') ? 'selected' : ''?>">
            <span class="left icon" id="icon_downloads"></span>
            <span class="right text">DOWNLOADS</span>
            <span class="tabactive"></span>
            <span class="clear"></span>
        </a>
    </li>
    <li class="<?php echo ($action == 'reports') ? 'active' : ''?>">
        <a href="<?php echo $community->getUrl()?>reports" rel="reports-container" class="<?php echo ($action == 'reports') ? 'selected' : ''?>">
            <span class="left icon" id="icon_reports"></span>
            <span class="right text">REPORTS</span>
            <span class="tabactive"></span>
            <span class="clear"></span>
        </a>
    </li>
    <?php if(Auth::check() && $community->isAdmin(Auth::user()->ID)) { ?>
    <li class="<?php echo ($action == 'admin') ? 'active' : ''?>">
        <a href="<?php echo $community->getUrl()?>admin" rel="group_admin_page" class="<?php echo ($action == 'admin') ? 'selected' : ''?>">
            <span class="left icon" id="icon_admin"></span>
            <span class="right text">ADMIN</span>
            <span class="tabactive"></span>
            <span class="clear"></span>
        </a>
    </li>
    <?php } ?>

</ul>
