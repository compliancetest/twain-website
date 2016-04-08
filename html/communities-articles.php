<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Community - Articles | Twain</title>
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
                            <li class="test-suites-tab"><a href="communities-test-suites.php">Test Suites</a></li>
                            <li class="test-data-tab"><a href="communities-test-data.php">Test Data</a></li>
                            <li class="articles-tab"><a href="communities-articles.php" class="active">Articles</a></li>
                            <li class="forum-tab"><a href="#">Forum</a></li>
                            <li class="downloads-tab"><a href="communities-downloads.php">Downloads</a></li>
                            <li class="reports-tab"><a href="communities-reports.php">Reports</a></li>
                            <li class="admin-tab"><a href="communities-admin.php">Admin</a></li>
                        </ul>
                    </div>

                    <div class="community-tab-content">

                        <div class="community-articles row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th class="text-left"><a href="#" class="current-orderby asc">Title</a></th>
                                                <th><a href="#" class="current-orderby desc">Author</a></th>
                                                <th class="text-nowrap"><a href="#">Created</a></th>
                                                <th class="text-nowrap"><a href="#">Last Edited</a></th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="attachment-cell has-attachment">
                                                        <a href="#">SuperStream Contributions Test Data Context Model</a>
                                                        <p>This article provides an overview of the test data entities that are used by the contributions test suite. Each Employer [...]</p>
                                                    </div>
                                                </td>
                                                <td><a href="#">Steven</a></td>
                                                <td class="text-center">2014-01-24</td>
                                                <td class="text-center">2015-10-21</td>
                                                <td class="text-center text-nowrap">
                                                    <a href="#" class="btn btn-icon btn-primary btn-view" data-tooltip="tooltip" title="Edit Article"></a>
                                                    <a href="#" class="btn btn-icon btn-primary btn-edit" data-tooltip="tooltip" title="Read Article"></a>
                                                    <a href="#" class="btn btn-icon btn-primary btn-history" data-tooltip="tooltip" title="History of Article"></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="attachment-cell">
                                                        <a href="#">Gateway Conformance Testing</a>
                                                        <p>An increasing number of Funds and Payroll providers are taking the decision to participate directly in the Superannuation Transaction Network [...]</p>
                                                    </div>
                                                </td>
                                                <td><a href="#">Michael</a></td>
                                                <td class="text-center">2015-05-14</td>
                                                <td class="text-center">2015-05-14</td>
                                                <td class="text-center text-nowrap">
                                                    <a href="#" class="btn btn-icon btn-primary btn-view" data-tooltip="tooltip" title="Edit Article"></a>
                                                    <a href="#" class="btn btn-icon btn-primary btn-edit" data-tooltip="tooltip" title="Read Article"></a>
                                                    <a href="#" class="btn btn-icon btn-primary btn-history" data-tooltip="tooltip" title="History of Article"></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="attachment-cell has-attachment">
                                                        <a href="#">SuperStream Contributions Test Data Context Model</a>
                                                        <p>This article provides an overview of the test data entities that are used by the contributions test suite. Each Employer [...]</p>
                                                    </div>
                                                </td>
                                                <td><a href="#">Steven</a></td>
                                                <td class="text-center">2014-01-24</td>
                                                <td class="text-center">2015-10-21</td>
                                                <td class="text-center text-nowrap">
                                                    <a href="#" class="btn btn-icon btn-primary btn-view" data-tooltip="tooltip" title="Edit Article"></a>
                                                    <a href="#" class="btn btn-icon btn-primary btn-edit" data-tooltip="tooltip" title="Read Article"></a>
                                                    <a href="#" class="btn btn-icon btn-primary btn-history" data-tooltip="tooltip" title="History of Article"></a>
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

    <?php include_once('parts/footer.php'); ?>

    </body>
</html>
