<header id="header">
    <div class="header-inner">
        <div class="container">
            <a href="#" class="logo"><img src="/laravel/resources/assets/images/drummond_group_logo.png" alt="" /></a>
                <div class="header-account">
                    <div class="header-actions">
                    @if(Auth::check())
                        <div class="header-user-info">
                            <img width="32" height="32" alt="Admin" class="avatar" src="https://secure.gravatar.com/avatar/0510c4f56be3635770ffbec748937368?d=https://www.compliancetest.net/wp-content/plugins/buddypress/bp-core/images/mystery-man.jpg&amp;s=32&amp;r=G">
                            <div class="header-welcome">Welcome <strong class="header-username">{{ Auth::user()->getFullName() }}</strong></div>
                        </div>
                        <ul class="account-menu">
                            <li class="dashboard-menu hidden-desktop">
                                <a href="#" class="btn btn-primary btn-dropdown" data-toggle-block="#header-dashboard-menu">Dashboard</a>
                                <ul class="dropdown-menu dashboard-dropdown-menu" id="header-dashboard-menu">

                                    @if(is_organisation_admin())
                                        <li class="first">
                                            <a data-title="Organisation" href="/my-organisation/" class="menu-organisation">Organisation</a>
                                            <ul class="dropdown-menu">
                                                <li class="first"><a href="/my-organisation/users/">Users</a></li>
                                                <li><a href="/my-organisation/test-suites/">Subscriptions</a></li>
                                                <li class="last"><a href="/my-organisation/">Profile</a></li>
                                            </ul>
                                        </li>
                                    @endif

                                    <li>
                                        <a data-title="Communities" href="/my-communities/" class="menu-communities">Communities</a>
                                        <ul class="dropdown-menu">

                                            @foreach(Auth::user()->confirmedSubscriptions() as $sub)
                                                <li class="first"><a href="#">{{ $sub->community->title }}</a>
                                                    <ul class="dropdown-menu">
                                                        <li class="first">
                                                            <a href="{{ $sub->community->getUrl() }}testsuites/">Test Suites</a>
                                                            <?php $testsuites = getCommunityTestSuites($sub->community->id);?>
                                                                @if(count($testsuites) > 0)
                                                                    <ul class="dropdown-menu">
                                                                        @foreach ($testsuites as $k => $row)
                                                                            <li @if($k == 0) class="first" @endif>
                                                                                <a href="{{ get_permalink($row->ID) }}">
                                                                                    {{ apply_filters('the_title', $row->post_title) }}
                                                                                </a>
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                @endif
                                                        </li>
                                                        <li><a href="{{ $sub->community->getUrl() }}testdata/">Test Data</a></li>
                                                        <li><a href="{{ $sub->community->getUrl() }}wiki/">Articles</a></li>
                                                        <li><a href="{{ $sub->community->getUrl() }}downloads/">Downloads</a></li>
                                                        <li><a href="{{ $sub->community->getUrl() }}reports/">Reports</a></li>
                                                        @if($sub->community->isAdmin())
                                                            <li class="last"><a href="{{ $sub->community->getUrl() }}admin/">Admin</a></li>
                                                        @endif
                                                    </ul>
                                                </li>
                                            @endforeach
                                            <li class="action-link last"><a href="/communities/create">+ Add</a></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a data-title="Test Suites" href="/me-test-suites/" class="menu-test-suites">Test Suites</a>
                                        <ul class="dropdown-menu">
                                            @foreach(getUserSubscriptions(null, true) as $subscription)
                                                <li class="first"><a href="{{ get_permalink($subscription->suite_id) }}">{{ $subscription->suite_title }}</a></li>
                                            @endforeach
                                            <li class="action-link last"><a href="/test-suites/" >+ Add</a></li>
                                        </ul>
                                    </li>
                                    <li><a data-title="Test Data" href="/my-test-data/" class="menu-test-data">Test Data</a></li>
                                    <li><a data-title="Products" href="/my-products/" class="menu-products">Products</a></li>
                                    <li><a data-title="Coverage" href="/test-suite-coverage/" class="menu-coverage">Coverage</a></li>
                                    <li><a data-title="Transactions" href="/my-transaction-log/" class="menu-transactions">Transactions</a></li>
                                    <li><a data-title="Support" href="/my-support-tickets/" class="menu-support">Support</a></li>
                                    <li><a data-title="Profile" href="/my-profile/" class="menu-profile">Profile</a></li>
                                    <li class="last"><a data-title="Agreements" href="/agreements/" class="menu-agreements">Agreements</a></li>
                                </ul>
                            </li>
                            <li class="hidden-mobile"><span id="mobile-search" data-toggle-block="#header-search" class="btn btn-default">Search</span></li>
                            <li><a href="{{ wp_logout_url( get_bloginfo('siteurl')) }}" class="btn btn-danger btn-logout">Logout</a></li>
                        </ul>
                    @else
                        <div class="header-authorise">
                            <div id="header-dashboard-menu"></div>
                            <div class="hidden-mobile"><span id="mobile-search" data-toggle-block="#header-search" class="btn btn-default">Search</span></div>
                            <div class="header-login-block">
                                <a href="#" class="btn btn-primary btn-login" id="headerLoginBtn">Login</a>
                                <div class="header-login-from" id="headerLoginBlock">
                                    <form method="post" action="http://twain.lc/wp-login.php" name="header_login_form" id="headerLoginForm">
                                        <div class="form-group login-group">
                                            <label for="user_login"></label>
                                            <input type="text" autocomplete="off" size="20" value="" id="user_login" name="log" placeholder="E-mail or User">
                                        </div>
                                        <div class="form-group password-group">
                                            <label for="user_pass"></label>
                                            <input type="password" size="20" value="" autocomplete="off" id="user_pass" name="pwd" placeholder="********">
                                        </div>
                                        <div id="header_login_error_msg" class="header-login-error">Wrong username or password, please try again!</div>
                                        <div class="header-login-form-actions">
                                            <input type="submit" value="Login" class="btn btn-primary pull-right" id="wp-submit2" name="wp-submit">
                                            <a class="pull-left" href="/reset-password/">Forgot Password?</a>
                                        </div>
                                        <div class="header-login-loading" id="headerLoginLoading"><div class="loader"></div></div>
                                    </form>
                                </div>
                            </div>
                            <a href="#" class="btn btn-danger btn-signup">Sign Up</a>
                        </div>
                    @endif
                    </div>
                <div id="header-search">
                    <form action="#">
                        <div class="header-search-box">
                            <input type="text" placeholder="Search" value="" class="header-search-field" name="q">
                            <div class="header-search-type">
                                <div class="header-search-type-inner">
                                    <select class="header-search-type-field">
                                        <option value="site">Site</option>
                                        <option value="registry">Registry</option>
                                    </select>
                                </div>
                            </div>
                            <button class="header-search-submit" type="submit"><span class="header-search-submit-icon">Search</span></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <nav id="navbar">
        <div class="navbar-wrapper">
            <div class="container">
                <ul class="menu">
                    <li><a href="/service-overview/">Service Overview</a></li>
                    <li><a href="/pricing">Pricing</a></li>
                    <li><a href="/documentation">Documentation</a></li>
                    <li><a href="/news-events/">News &amp; Events</a></li>
                    <li><a href="/contact-us/">Contact Us</a></li>
                </ul>
            </div>
        </div>
    </nav>

</header>