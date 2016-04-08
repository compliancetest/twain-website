<div class="tabs-menu">
    <ul>
        <li class="test-suites-tab"><a href="<?php echo $community->getUrl()?>"
                                       class="<?php echo (empty($action) || $action == 'testsuites') ? 'active' : ''?>">Test Suites</a>
        </li>

        <li class="test-data-tab"><a href="<?php echo $community->getUrl()?>testdata"
                                     class="<?php echo ($action == 'testdata') ? 'active' : ''?>">Test Data</a></li>
        @if($community->articles_status)
            <li class="articles-tab"><a href="<?php echo $community->getUrl()?>wiki"
                                        class="<?php echo ($action == 'wiki') ? 'active' : ''?>">Articles</a></li>
        @endif
        <li class="downloads-tab"><a href="<?php echo $community->getUrl()?>downloads"
                                     class="<?php echo ($action == 'downloads') ? 'active' : ''?>">Downloads</a></li>
        <li class="reports-tab"><a href="<?php echo $community->getUrl()?>reports"
                                   class="<?php echo ($action == 'reports') ? 'active' : ''?>">Reports</a></li>

        @if(Auth::check() && $community->isAdmin(Auth::user()->ID))
            <li class="admin-tab"><a href="<?php echo $community->getUrl()?>admin"
                                     class="<?php echo ($action == 'admin') ? 'active' : ''?>">Admin</a></li>
        @endif
    </ul>
</div>
