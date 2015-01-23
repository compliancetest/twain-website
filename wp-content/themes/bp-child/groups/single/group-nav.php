<?php
    //Community Nav            
    $isMember  = is_user_logged_in() &&  bp_group_is_member();    
    //$isMember  = is_user_logged_in();    
?>
<ul class="tabs <?php echo $isMember ? 'no-ajax' : '' ?>">
    <li class="<?php echo (bp_current_action() == 'home' || bp_current_action() == '') ? 'active' : ''?>">
        <a href="<?php echo bp_get_group_permalink()?>" rel="testsuites-container" class="<?php echo (bp_current_action() == 'home' || bp_current_action() == '') ? 'selected' : ''?>">
            <span class="left icon" id="icon_test_suites"></span>
            <span class="right text">TEST SUITES</span>
            <span class="tabactive"></span>
            <span class="clear"></span>
        </a>
    </li>
    <li class="<?php echo (bp_current_action() == 'testdata') ? 'active' : ''?>">
        <a href="<?php echo bp_get_group_permalink()?>testdata" rel="testdata-container" class="<?php echo (bp_current_action() == 'testdata') ? 'selected' : ''?>">
            <span class="left icon" id="icon_testdata"></span>
            <span class="right text">TEST DATA</span>
            <span class="tabactive"></span>
            <span class="clear"></span>
        </a>
    </li>
    <li class="<?php echo (bp_current_action() == 'wiki') ? 'active' : ''?>">
        <a href="<?php echo bp_get_group_permalink();?>wiki" rel="wiki-container" class="<?php echo (bp_current_action() == 'wiki') ? 'selected' : ''?>">
            <span class="left icon" id="icon_wiki"></span>
            <span class="right text">ARTICLES</span>
            <span class="tabactive"></span>
            <span class="clear"></span>
        </a>
    </li>
    <li class="<?php echo (bp_current_action() == 'forum') ? 'active' : ''?>">
        <a href="<?php echo bp_get_group_permalink()?>forum" rel="forum-container" class="<?php echo (bp_current_action() == 'forum') ? 'selected' : ''?>">
            <span class="left icon" id="icon_forum"></span>
            <span class="right text">FORUM</span>
            <span class="tabactive"></span>
            <span class="clear"></span>
        </a>
    </li>
    <li class="<?php echo (bp_current_action() == 'downloads') ? 'active' : ''?>">
        <a href="<?php echo bp_get_group_permalink()?>downloads" rel="downloads-container" class="<?php echo (bp_current_action() == 'downloads') ? 'selected' : ''?>">
            <span class="left icon" id="icon_downloads"></span>
            <span class="right text">DOWNLOADS</span>
            <span class="tabactive"></span>
            <span class="clear"></span>
        </a>
    </li>
    <li class="<?php echo (bp_current_action() == 'reports') ? 'active' : ''?>">
        <a href="<?php echo bp_get_group_permalink()?>reports" rel="reports-container" class="<?php echo (bp_current_action() == 'reports') ? 'selected' : ''?>">
            <span class="left icon" id="icon_testdata"></span>
            <span class="right text">REPORTS</span>
            <span class="tabactive"></span>
            <span class="clear"></span>
        </a>
    </li>
    <?php if(bp_group_is_admin()) { ?>
    <li class="<?php echo (bp_current_action() == 'admin') ? 'active' : ''?>">
        <a href="<?php echo bp_get_group_permalink()?>admin" rel="group_admin_page" class="<?php echo (bp_current_action() == 'admin') ? 'selected' : ''?>">
            <span class="left icon" id="icon_admin"></span>
            <span class="right text">ADMIN</span>
            <span class="tabactive"></span>
            <span class="clear"></span>
        </a>
    </li>
    <?php } ?>

</ul>
                        