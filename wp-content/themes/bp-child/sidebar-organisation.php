<div id="item-nav">
    <ul class="tabs no-ajax">
        <li <?php echo is_page('users') ? 'class="active"' : ''?>>
            <a href="/my-organisation/users" rel="organisation_users" <?php echo is_page('users') ? 'class="selected"' : ''?>>
                <span class="left icon" id="icon_users"></span>
                <span class="right text">Users</span>
<!--                <span class="tabactive"></span>-->
                <span class="clear"></span>
            </a>
        </li>        
        <li <?php echo is_page('test-suites') ? 'class="active"' : ''?>>
            <a href="/my-organisation/test-suites" rel="organisation_test_suties" <?php echo is_page('test-suites') ? 'class="selected"' : ''?>>
                <span class="left icon" id="icon_test_suites"></span>
                <span class="right text">Subscriptions</span>
<!--                <span class="tabactive"></span>-->
                <span class="clear"></span>
            </a>
        </li>
        <li <?php echo is_page('my-organisation') ? 'class="active"' : ''?>>
            <a href="/my-organisation" rel="organisation_profile" <?php echo is_page('my-organisation') ? 'class="selected"' : ''?>>
                <span class="left icon" id="icon_admin"></span>
                <span class="right text">Profile</span>
<!--                <span class="tabactive"></span>-->
                <span class="clear"></span>
            </a>
        </li>                
    </ul>
    <div class="clear"></div>
</div>
