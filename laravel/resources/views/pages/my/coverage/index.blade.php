@extends('app')

@section('content')

    <div class="container main-container">

        <div class="tabs-menu">
            <ul>
                @if(is_organisation_admin())
                    <li class="organisation-tab"><a href="/my-organisation/" data-tooltip="tooltip" title="My Organisation">Organisation</a></li>
                @endif

                <li class="communities-tab"><a data-tooltip="tooltip" href="/my-communities/" title="My community memberships">Communities</a></li>
                <li class="test-suites-tab"><a href="/my-test-suites/" data-tooltip="tooltip" title="My test suite subscriptions">Test Suites</a></li>
                <li class="products-tab"><a href="/my-products/" data-tooltip="tooltip" title="My products under test">Products</a></li>
                <li class="coverage-tab"><a href="/test-suite-coverage/" class="active" data-tooltip="tooltip" title="Completeness of my testing">Coverage</a></li>
                <li class="transactions-tab"><a href="/my-transaction-log/" data-tooltip="tooltip" title="My test transactions">Transactions</a></li>
                <li class="support-tab"><a href="/my-support-tickets/" data-tooltip="tooltip" title="My support tickets">Support</a></li>
                <li class="profile-tab"><a href="/my-profile/" data-tooltip="tooltip" title="My profile">Profile</a></li>

            </ul>
        </div>

        <div class="main-content">

            <div class="test-coverage" id="testCoveragePlanList">

                {!! view('pages.my.coverage.test_plans_list', ['userSuites' => $userSuites]) !!}

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
                            <div class="block-loading loading-shown">
                                <div class="loading-content"><span class="loader"></span>

                                    <div class="loading-text">LOADING TEST CASE</div>
                                    <div class="loading-wait">Please wait...</div>
                                </div>
                            </div>
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
                            <div class="block-loading loading-shown">
                                <div class="loading-content"><span class="loader"></span>

                                    <div class="loading-text">LOADING AVAILABLE PLANS</div>
                                    <div class="loading-wait">Please wait...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Init modal scripts-->
                <script>
                    jQuery(document).ready(function ($) {
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

@stop