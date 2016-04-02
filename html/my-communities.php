<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>My Communities | Twain</title>
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
            <div class="tabs-menu">
                <ul>
                    <li class="organisation-tab"><a href="#" data-tooltip="tooltip" title="My Organisation">Organisation</a></li>
                    <li class="communities-tab"><a class="active" data-tooltip="tooltip" href="#" title="My community memberships">Communities</a></li>
                    <li class="test-suites-tab"><a href="#" data-tooltip="tooltip" title="My test suite subscriptions">Test Suites</a></li>
                    <li class="test-data-tab"><a href="#" data-tooltip="tooltip" title="My profile data">Test Data</a></li>
                    <li class="products-tab"><a href="#" data-tooltip="tooltip" title="My products under test">Products</a></li>
                    <li class="coverage-tab"><a href="#" data-tooltip="tooltip" title="Completeness of my testing">Coverage</a></li>
                    <li class="transactions-tab"><a href="#" data-tooltip="tooltip" title="My test transactions">Transactions</a></li>
                    <li class="support-tab"><a href="#" data-tooltip="tooltip" title="My support tickets">Support</a></li>
                    <li class="profile-tab"><a href="#" data-tooltip="tooltip" title="My profile">Profile</a></li>
                    <li class="agreements-tab"><a href="#" data-tooltip="tooltip" title="My Agreements">Agreements</a></li>
                </ul>
            </div>

            <div class="main-content">
                <div class="table-responsive">
                    <table class="table colored-table">
                        <thead>
                            <tr>
                                <th class="text-left">Name</th>
                                <th class="col-sm-1">Since</th>
                                <th class="col-sm-1 text-left">Role</th>
                                <th class="col-sm-1">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><a href="communities-detail.php">SuperStream</a></td>
                                <td class="text-center">2013-06-29</td>
                                <td>Admin</td>
                                <td class="text-center"><a id="btn-remove" class="btn btn-icon btn-danger btn-delete" href="#confirmRemoveMembership" data-href="http://compliancetest.lc" data-tooltip="tooltip" data-toggle="modal" title="Remove Membership">Remove</a></td>
                            </tr>
                            <tr>
                                <td><a href="communities-detail.php">SuperStream</a></td>
                                <td class="text-center">2013-06-29</td>
                                <td>Admin</td>
                                <td class="text-center"><a class="btn btn-icon btn-danger btn-delete" href="#confirmRemoveMembership" data-href="http://compliancetest.lc" data-tooltip="tooltip" data-toggle="modal" title="Remove Membership">Remove</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <a class="btn btn-success btn-with-icon btn-add">Add</a>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="confirmRemoveMembership" tabindex="-1" role="dialog">
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

    <?php include_once('parts/footer.php'); ?>

    </body>
</html>
