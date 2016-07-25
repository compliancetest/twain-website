<div class="tabs-menu">
    <ul>
        @if(Auth::check() && $community->hasAccess())
            <li class="test-suites-tab"><a href="{{ $community->getUrl() }}"
                                           @if(empty($action) || $action == 'testsuites') class="active" @endif>Test
                    Suites</a>
            </li>

            <li class="test-data-tab"><a href="{{ $community->getUrl() }}testdata"
                                         @if($action == 'testdata') class="active" @endif>Test Data</a></li>
            @if($community->articles_status)
                <li class="articles-tab"><a href="{{ $community->getUrl() }}wiki"
                                            @if($action == 'wiki') class="active" @endif>Articles</a></li>
            @endif
            <li class="forum-tab"><a href="{{ $community->getUrl() }}forum"
                                        @if($action == 'forum') class="active" @endif>Forum</a></li>
            <li class="downloads-tab"><a href="{{ $community->getUrl() }}downloads"
                                         @if($action == 'downloads') class="active" @endif>Downloads</a></li>
            <li class="surveys-tab"><a href="{{ $community->getUrl() }}surveys"
                                       @if($action == 'surveys') class="active" @endif>Surveys</a></li>

            @if(Auth::check() && $community->isModerator() || $community->isAdmin())

                @if($community->isAdmin())
                    <li class="admin-tab"><a href="{{ $community->getUrl() }}backups"
                                             @if($action == 'backups') class="active" @endif>Test Data Backups</a></li>
                @endif
                <li class="admin-tab"><a href="{{ $community->getUrl() }}admin"
                                         @if($action == 'admin' || $action == 'admin_page_for_support_users') class="active" @endif>Admin</a></li>
            @endif

        @else
            <li class="test-suites-tab"><a href="#testsuites" class="nav-tabs active" data-toggle="tab">Test Suites</a></li>
            <li class="test-data-tab"><a href="#testdata" class="nav-tabs" data-toggle="tab">Test Data</a></li>

            @if($community->articles_status)
                <li class="articles-tab"><a href="#wiki" class="nav-tabs" data-toggle="tab">Articles</a></li>
            @endif

            <li class="forum-tab"><a href="#forum" class="nav-tabs" data-toggle="tab">Forum</a></li>
            <li class="downloads-tab"><a href="#downloads" class="nav-tabs" data-toggle="tab">Downloads</a></li>
            <li class="surveys-tab"><a href="#survey" class="nav-tabs" data-toggle="tab">Surveys</a></li>
            <li class="reports-tab"><a href="#reports" class="nav-tabs" data-toggle="tab">Reports</a></li>

            <script>
                $('.tabs-menu a').click(function (e) {
                    $(this).tab('show');
                    $('.tabs-menu a').removeClass('active');
                    $(this).addClass('active');
                })
            </script>
        @endif
    </ul>
</div>