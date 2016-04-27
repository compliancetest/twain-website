<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Test Coverage | Twain</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="//fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800|Oswald:400,300,700" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="/laravel/resources/assets/css/style.css">

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="/laravel/resources/assets/js/vendor/jquery-1.11.2.min.js"><\/script>')</script>
        <script src="/laravel/resources/assets/js/vendor/bootstrap.min.js"></script>
        <script src="/laravel/resources/assets/js/vendor/jquery.slimmenu.min.js"></script>
        <script src="/laravel/resources/assets/js/vendor/jquery.validate.js"></script>
        <script src="/laravel/resources/assets/js/scripts.js"></script>
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
                    <li class="communities-tab"><a data-tooltip="tooltip" href="my-communities.php" title="My community memberships">Communities</a></li>
                    <li class="test-suites-tab"><a href="#" data-tooltip="tooltip" title="My test suite subscriptions">Test Suites</a></li>
                    <li class="test-data-tab"><a href="#" data-tooltip="tooltip" title="My profile data">Test Data</a></li>
                    <li class="products-tab"><a href="#" data-tooltip="tooltip" title="My products under test">Products</a></li>
                    <li class="coverage-tab"><a href="#" class="active" data-tooltip="tooltip" title="Completeness of my testing">Coverage</a></li>
                    <li class="transactions-tab"><a href="#" data-tooltip="tooltip" title="My test transactions">Transactions</a></li>
                    <li class="support-tab"><a href="#" data-tooltip="tooltip" title="My support tickets">Support</a></li>
                    <li class="profile-tab"><a href="#" data-tooltip="tooltip" title="My profile">Profile</a></li>
                    <li class="agreements-tab"><a href="#" data-tooltip="tooltip" title="My Agreements">Agreements</a></li>
                </ul>
            </div>

            <div class="main-content">

                <div class="test-coverage" id="testCoveragePlanList">
                    <div class="colored-box">
                        <div class="colored-box-header"><a href="#">TWAIN v2.3 Compliance - Data Sources v1.0</a></div>
                        <div class="colored-box-body">
                            <div class="table-responsive">
                                <table class="table colored-table">
                                    <thead>
                                    <tr>
                                        <th class="text-left">Product</th>
                                        <th class="col-sm-1">Level</th>
                                        <th class="col-sm-1 text-left">Role</th>
                                        <th>Coverage</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php for ($i = 1; $i <= rand(5, 10); $i++): ?>
                                        <tr id="coverage-plan-<?php echo $i; ?>">
                                            <td class="text-nowrap">Test Product v<?php echo $i; ?></td>
                                            <td class="text-center">A</td>
                                            <td>Fund</td>
                                            <td>
                                                <div class="coverage-progress">
                                                    <a data-toggle="modal" data-remote="true" data-ajax-modal data-target="#testDetailsModal" href="temp/test-case-details.php" data-tooltip="tooltip" class="coverage-test-case-item" title="SC-01 v1.0"></a>
                                                    <a data-toggle="modal" data-remote="true" data-ajax-modal data-target="#testDetailsModal" href="temp/test-case-details.php" data-tooltip="tooltip" class="coverage-test-case-item test-excluded" title="SC-01 v1.1"></a>
                                                    <a data-toggle="modal" data-remote="true" data-ajax-modal data-target="#testDetailsModal" href="temp/test-case-details.php" data-tooltip="tooltip" class="coverage-test-case-item test-failed" title="SC-01 v1.2"></a>
                                                    <a data-toggle="modal" data-remote="true" data-ajax-modal data-target="#testDetailsModal" href="temp/test-case-details.php" data-tooltip="tooltip" class="coverage-test-case-item test-passed" title="SC-01 v1.3"></a>
                                                    <a data-toggle="modal" data-remote="true" data-ajax-modal data-target="#testDetailsModal" href="temp/test-case-details.php" data-tooltip="tooltip" class="coverage-test-case-item test-optional" title="SC-01 v1.4"></a>
                                                    <?php for( $j = 1; $j <= rand(25, 150); $j++): ?>
                                                        <a data-toggle="modal" data-remote="true" data-ajax-modal data-target="#testDetailsModal" href="temp/test-case-details.php" data-tooltip="tooltip" class="coverage-test-case-item" title="SC-0<?php echo $j; ?> v1.0"></a>
                                                    <?php endfor; ?>
                                                </div>
                                            </td>
                                            <td class="col-sm-1 text-nowrap">
                                                <a href="#" class="btn btn-primary btn-icon btn-view" data-tooltip="tooltip" title="View log"></a>
                                                <a href="temp/edit-coverage-plan.php" data-toggle="modal" data-remote="true" data-ajax-modal data-target="#editPlanModal" class="btn btn-primary btn-icon btn-edit" data-tooltip="tooltip" title="Edit plan"></a>
                                                <a href="#removePlanModal-<?php echo $i; ?>" data-toggle="modal" class="btn btn-danger btn-icon btn-delete" data-tooltip="tooltip" title="Delete plan"></a>
                                                <a href="#" class="btn btn-success btn-icon btn-confirm" data-tooltip="tooltip" title="Claim"></a>

                                                <!-- Remove Plan Confirmation Modal-->
                                                <div class="modal fade" id="removePlanModal-<?php echo $i; ?>" tabindex="-1" role="dialog">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content block-loading-wrapper">
                                                            <div class="modal-header">
                                                                <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
                                                                Confirm Deletion
                                                            </div>
                                                            <div class="modal-body">
                                                                <p class="default-text">Are you sure that you want to delete this plan?</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <a href="temp/ajax.php" data-plan-id="<?php echo $i; ?>" class="btn btn-success btn-with-icon btn-confirm deleteTestCoveragePlan">Confirm</a>
                                                                <button type="button" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endfor; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <a href="temp/edit-coverage-plan.php" data-toggle="modal" data-remote="true" data-ajax-modal data-target="#editPlanModal"  class="btn btn-success btn-with-icon btn-add">Add</a>


                    <!-- Test Details Modal-->
                    <div class="modal fade" id="testDetailsModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content block-loading-wrapper">
                                <div class="modal-header">
                                    <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
                                    Test Case Details
                                </div>
                                <div class="modal-body"></div>
                                <div class="modal-footer">
                                    <a href="#" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</a>
                                </div>
                                <div class="block-loading loading-shown"><div class="loading-content"><span class="loader"></span><div class="loading-text">LOADING TEST CASE</div><div class="loading-wait">Please wait...</div></div></div>
                            </div>
                        </div>
                    </div>

                    <!-- Test Details Modal-->
                    <div class="modal fade" id="editPlanModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content block-loading-wrapper">
                                <div class="modal-header">
                                    <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
                                    Test Plan Form
                                </div>
                                <div class="modal-body">
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Confirm</button>
                                    <button type="button" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
                                </div>
                                <div class="block-loading loading-shown"><div class="loading-content"><span class="loader"></span><div class="loading-text">LOADING AVAILABLE PLANS</div><div class="loading-wait">Please wait...</div></div></div>
                            </div>
                        </div>
                    </div>

                    <!-- Init modal scripts-->
                    <script>
                        jQuery(document).ready(function($) {
                            //Test detail modal scripts
                            $('#testDetailsModal').on('modalContentLoaded', function () {
                                Page.testCoverage.loadTestCaseDetails();
                                Page.testCoverage.validateTestCaseDetailsForm();
                            }).on("show.bs.modal", function () {
                                $(this).find('.modal-content').append('<div class="block-loading loading-shown"><div class="loading-content"><span class="loader"></span><div class="loading-text">LOADING TEST CASE</div><div class="loading-wait">Please wait...</div></div></div>');
                            }).on('hidden.bs.modal', function () {
                                $(this).find('.modal-content').append('<div class="block-loading loading-shown"><div class="loading-content"><span class="loader"></span><div class="loading-text">LOADING TEST CASE</div><div class="loading-wait">Please wait...</div></div></div>');
                            });

                            //Edit plan modal scripts
                            $('#editPlanModal').on('modalContentLoaded', function () {
                                Page.testCoverage.validateEditPlanForm();
                            }).on("show.bs.modal", function () {
                                $(this).find('.modal-content').append('<div class="block-loading loading-shown"><div class="loading-content"><span class="loader"></span><div class="loading-text">LOADING AVAILABLE PLANS</div><div class="loading-wait">Please wait...</div></div></div>');
                            }).on('hidden.bs.modal', function () {
                                $(this).find('.modal-content').append('<div class="block-loading loading-shown"><div class="loading-content"><span class="loader"></span><div class="loading-text">LOADING AVAILABLE PLANS</div><div class="loading-wait">Please wait...</div></div></div>');
                            });

                            //Remove test coverage plan modal
                            $('.deleteTestCoveragePlan').click(function (e) {
                                var self = $(this);
                                e.preventDefault();

                                var testCoveragePlan = {
                                    id: self.data('plan-id'),
                                    link: self.attr('href')
                                };

                                jQuery('#removePlanModal-' + testCoveragePlan.id + ' .modal-content').append('<div class="block-loading loading-shown"><div class="loading-content"><span class="loader"></span><div class="loading-text">REMOVING PLAN</div><div class="loading-wait">Please wait...</div></div></div>');
                                jQuery.ajax({
                                    type: 'delete',
                                    url: testCoveragePlan.link,
                                    success: function (data) {
                                        $('.modal').modal('hide');
                                        if (data.status == 'success') {
                                            $('#coverage-plan-' + testCoveragePlan.id).addClass('removing').fadeTo("slow", 0.3, function () {
                                                $(this).remove();
                                                $('#testCoveragePlanList').prepend('<div class="success-message">Plan has been removed</div>');
                                                setTimeout(function () {
                                                    $('#testCoveragePlanList > .success-message').slideUp(function () {
                                                        $(this).remove();
                                                    });
                                                }, 2000);
                                            });
                                        }
                                    },
                                    error: function (jqXHR, status) {
                                        $('.modal').modal('hide');
                                        $('#testCoveragePlanList').prepend('<div class="error-message">' + formatErrorMessage(jqXHR, status) + '</div>');
                                        setTimeout(function () {
                                            $('#testCoveragePlanList > .error-message').slideUp(function () {
                                                $(this).remove();
                                            });
                                        }, 5000);
                                    }
                                })
                            });
                        });
                    </script>

                </div>

            </div>
        </div>

    </div>

    <?php include_once('parts/footer.php'); ?>

    </body>
</html>
