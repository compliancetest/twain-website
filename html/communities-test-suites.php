<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Community - Test Suites | Twain</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="//fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800|Oswald:400,300,700" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
    <!--[if lt IE 8]>
        <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->
    <div id="main-wrapper">

        <?php include_once('parts/header.php'); ?>

        <div class="container main-container">
            <div class="main-content">

                <div class="community-header row">
                    <div class="community-membership-action hidden-desktop">
                        <a class="btn btn-danger btn-lg" href="#confirmCancelMembership" data-href="http://compliancetest.lc" data-toggle="modal">Cancel Membership</a>
                    </div>
                    <div class="community-logo">
                        <img src="images/gravatar.jpg" alt="SuperStream" />
                    </div>
                    <div class="community-short-description">
                        <h3>SuperStream</h3>
                        <p>SuperStream aims to improve efficiency in the Australian Superannuation industry through data standards. "Rollovers", where savings are transferred from one fund to another (mandatory for APRA regulated funds by July 1 2013 and all funds by July 1 2014). "Member Contributions", where regular payments are made by employers to funds of the employees choice (mandated for large employers by 1 July 2014 and all employers by 1 July 2015.</p>
                    </div>
                    <div class="community-membership-action hidden-mobile">
                        <a class="btn btn-danger btn-lg" href="#confirmCancelMembership" data-href="http://compliancetest.lc" data-toggle="modal">Cancel Membership</a>
                    </div>
                    <!-- Confirm Membership Cancellation -->
                    <div class="modal fade" id="confirmCancelMembership" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup" data-placement="left" data-dismiss="modal" aria-label="Close">Close</button>
                                    Confirm Community Membership Cancellation
                                </div>
                                <div class="modal-body">
                                    This will cancel your membership of the SuperStream community. Are you sure?
                                </div>
                                <div class="modal-footer">
                                    <a class="btn btn-success btn-with-icon btn-confirm">Confirm</a>
                                    <button type="button" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="community-tabs">
                    <div class="tabs-menu">
                        <ul>
                            <li class="test-suites-tab"><a href="communities-test-suites.php" class="active">Test Suites</a></li>
                            <li class="test-data-tab"><a href="communities-test-data.php">Test Data</a></li>
                            <li class="articles-tab"><a href="communities-articles.php">Articles</a></li>
                            <li class="forum-tab"><a href="communities-forum.php">Forum</a></li>
                            <li class="downloads-tab"><a href="communities-downloads.php">Downloads</a></li>
                            <li class="reports-tab"><a href="communities-reports.php">Reports</a></li>
                        </ul>
                    </div>

                    <div class="community-tab-content">

                        <div class="community-test-suites row">
                            <div class="col-md-12">

                                <div class="filter-panel">
                                    <div class="collapsible-panel-group" id="accordion">
                                        <div class="collapsible-panel">
                                            <div class="filter-title collapsed" data-toggle="collapse" data-target="#collapseType">Type</div>
                                            <div id="collapseType" class="filter-body collapse">
                                                <ul>
                                                    <li><label><input type="checkbox" value="type1" name="type[]"> Type 1</label></li>
                                                    <li><label><input type="checkbox" value="type2" name="type[]"> Type 2</label></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="collapsible-panel">
                                            <div class="filter-title" data-toggle="collapse" data-target="#collapseIssuer">Issuer</div>
                                            <div id="collapseIssuer" class="filter-body collapse in">
                                                <ul>
                                                    <li><label><input type="checkbox" value="option1" name="issuer[]"> ATO (2)</label></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="collapsible-panel">
                                            <div class="filter-title collapsed" data-toggle="collapse" data-target="#collapseYearOfIssue">Year of Issue</div>
                                            <div id="collapseYearOfIssue" class="filter-body collapse">
                                                <ul>
                                                    <li><label><input type="checkbox" value="option1" name="issue_year[]"> 2014 (1)</label></li>
                                                    <li><label><input type="checkbox" value="option2" name="issue_year[]"> 2013 (2)</label></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="collapsible-panel">
                                            <div class="filter-title collapsed" data-toggle="collapse" data-target="#collapseStatus">Status</div>
                                            <div id="collapseStatus" class="filter-body collapse">
                                                <ul>
                                                    <li><label><input type="checkbox" value="option1" name="status[]"> Active (1)</label></li>
                                                    <li><label><input type="checkbox" value="option2" name="status[]"> Draft (2)</label></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="result-content-panel">
                                    <div class="table-responsive result-content-panel-inner">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th class="text-left">Name</th>
                                                    <th>Published</th>
                                                    <th>Status</th>
                                                    <th>Products</th>
                                                    <th>Notify Changes</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <a href="#" class="test-suite-name">Contributions v1.0</a>
                                                        <p class="test-suite-description">SuperStream Contributions Test Suite</p>
                                                    </td>
                                                    <td class="text-center">2014-01-13</td>
                                                    <td class="text-center"><span class="status status-partial">Partial</span></td>
                                                    <td class="text-center"><a href="#" class="view-products-btn">view</a></td>
                                                    <td class="text-center"><input type="checkbox" value="" name="notify_changes"></td>
                                                    <td class="text-center">
                                                        <a href="#" class="btn btn-icon btn-primary btn-edit" data-tooltip="tooltip" title="Edit Suite"></a>
                                                        <a href="#" class="btn btn-icon btn-danger btn-delete" onclick="return confirm('Are you sure to delete this test suite?')"  data-tooltip="tooltip" title="Delete Suite"></a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <a href="#" class="test-suite-name">Contributions v1.01</a>
                                                        <p class="test-suite-description">SuperStream Contributions Test Suite</p>
                                                    </td>
                                                    <td class="text-center">2014-06-23</td>
                                                    <td class="text-center"><span class="status status-active">Active</span></td>
                                                    <td class="text-center"><a href="#" class="view-products-btn">view</a></td>
                                                    <td class="text-center"><input type="checkbox" value="" name="notify_changes"></td>
                                                    <td class="text-center">
                                                        <a href="#" class="btn btn-icon btn-primary btn-edit" data-tooltip="tooltip" title="Edit Suite"></a>
                                                        <a href="#" class="btn btn-icon btn-danger btn-delete" onclick="return confirm('Are you sure to delete this test suite?')"  data-tooltip="tooltip" title="Delete Suite"></a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <a href="#" class="test-suite-name">Contributions v1.1</a>
                                                        <p class="test-suite-description">SuperStream Contributions Test Suite</p>
                                                    </td>
                                                    <td class="text-center">2014-06-30</td>
                                                    <td class="text-center"><span class="status status-active">Active</span></td>
                                                    <td class="text-center"><a href="#" class="view-products-btn">view</a></td>
                                                    <td class="text-center"><input type="checkbox" value="" name="notify_changes"></td>
                                                    <td class="text-center">
                                                        <a href="#" class="btn btn-icon btn-primary btn-edit" data-tooltip="tooltip" title="Edit Suite"></a>
                                                        <a href="#" class="btn btn-icon btn-danger btn-delete" onclick="return confirm('Are you sure to delete this test suite?')"  data-tooltip="tooltip" title="Delete Suite"></a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <a href="#" class="test-suite-name">Contributions v1.2</a>
                                                        <p class="test-suite-description">SuperStream Contributions Test Suite</p>
                                                    </td>
                                                    <td class="text-center">2014-06-28</td>
                                                    <td class="text-center"><span class="status status-active">Active</span></td>
                                                    <td class="text-center"><a href="#" class="view-products-btn">view</a></td>
                                                    <td class="text-center"><input type="checkbox" value="" name="notify_changes"></td>
                                                    <td class="text-center">
                                                        <a href="#" class="btn btn-icon btn-primary btn-edit" data-tooltip="tooltip" title="Edit Suite"></a>
                                                        <a href="#" class="btn btn-icon btn-danger btn-delete" onclick="return confirm('Are you sure to delete this test suite?')"  data-tooltip="tooltip" title="Delete Suite"></a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <a href="#" class="test-suite-name">Contributions v1.3</a>
                                                        <p class="test-suite-description">SuperStream Contributions Test Suite</p>
                                                    </td>
                                                    <td class="text-center">2014-09-07</td>
                                                    <td class="text-center"><span class="status status-active">Active</span></td>
                                                    <td class="text-center"><a href="#" class="view-products-btn">view</a></td>
                                                    <td class="text-center"><input type="checkbox" value="" name="notify_changes"></td>
                                                    <td class="text-center">
                                                        <a href="#" class="btn btn-icon btn-primary btn-edit" data-tooltip="tooltip" title="Edit Suite"></a>
                                                        <a href="#" class="btn btn-icon btn-danger btn-delete" onclick="return confirm('Are you sure to delete this test suite?')"  data-tooltip="tooltip" title="Delete Suite"></a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <a href="#" class="test-suite-name">Rollovers v1.0</a>
                                                        <p class="test-suite-description">SuperStream Rollovers Test Suite</p>
                                                    </td>
                                                    <td class="text-center">2013-07-01</td>
                                                    <td class="text-center"><span class="status status-draft">Draft</span></td>
                                                    <td class="text-center"><a href="#" class="view-products-btn">view</a></td>
                                                    <td class="text-center"><input type="checkbox" value="" name="notify_changes"></td>
                                                    <td class="text-center">
                                                        <a href="#" class="btn btn-icon btn-primary btn-edit" data-tooltip="tooltip" title="Edit Suite"></a>
                                                        <a href="#" class="btn btn-icon btn-danger btn-delete" onclick="return confirm('Are you sure to delete this test suite?')"  data-tooltip="tooltip" title="Delete Suite"></a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>


            </div>
        </div>

    </div>

    <?php include_once('parts/footer.php'); ?>

    </body>
</html>
