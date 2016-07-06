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

                @foreach($userSuites as $suiteName => $userSuite)
                    <div class="colored-box">
                        <div class="colored-box-header"><a href="/test-suite/{{ $userSuite['testSuite']->post_name }}/">{{ $suiteName }}</a></div>
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

                                    @if($userSuite['testPlans'])
                                        @foreach($userSuite['testPlans'] as $userPlan)
                                            <tr id="coverage-plan-{{ $userPlan['testPlan']->id }}">
                                                <td class="text-nowrap">
                                                    {{ $userPlan['product']->post_title }}
                                                    @if($userPlan['product']) {{ ' v' . $userPlan['product']->getMetaByKey('product_version') }}@endif
                                                </td>
                                                <td class="text-center">{{ $userPlan['testPlan']->level }}</td>
                                                <td>{{ $userPlan['testPlan']->role }}</td>
                                                <td>
                                                    <div class="coverage-progress">
                                                        @foreach($userSuite['testSuite']->getTestCases($userPlan['testPlan']->level, $userPlan['testPlan']->role) as $case)
                                                            @include('pages.my.coverage._case_link', ['testPlanData' => $userPlan['testPlanData']])
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td class="col-sm-1 text-nowrap">
                                                    <a href="/my-transaction-log?suite={{ $userSuite['testSuite']->ID }}&product={{ $userPlan['product']->ID }}" target="_blank" class="btn btn-primary btn-icon btn-view" data-tooltip="tooltip" title="View log"></a>

                                                    <a href="/testplan/{{ $userPlan['testPlan']->id }}/edit" data-toggle="modal" data-remote="true" data-ajax-modal data-target="#editPlanModal"
                                                       class="btn btn-primary btn-icon btn-edit" data-tooltip="tooltip" title="Edit plan"></a>
                                                    <a href="#removePlanModal-{{ $userPlan['testPlan']->id }}" data-toggle="modal" class="btn btn-danger btn-icon btn-delete" data-tooltip="tooltip"
                                                       title="Delete plan"></a>
                                                    @if($userPlan['testPlan']->canBeClaimed() && !$userPlan['testPlan']->is_claimed)
                                                        <a href="/testplan/{{ $userPlan['testPlan']->id }}/claim" class="btn btn-success btn-icon btn-confirm" data-tooltip="tooltip" title="Claim"></a>
                                                    @endif

                                                    <!-- Remove Plan Confirmation Modal-->
                                                    <div class="modal fade" id="removePlanModal-{{ $userPlan['testPlan']->id }}" tabindex="-1" role="dialog">
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
                                                                    <a href="/testplan/{{ $userPlan['testPlan']->id }}" data-plan-id="{{ $userPlan['testPlan']->id }}"
                                                                       class="btn btn-success btn-with-icon btn-confirm deleteTestCoveragePlan">Confirm</a>
                                                                    <button type="button" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                        @endforeach
                                    @else
                                        <tr>
                                            <td class="text-nowrap text-center" colspan="5">No plans yet</td>
                                        </tr>
                                    @endif

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <a href="/testplan/create/{{ $userSuite['testSuite']->ID }}" data-toggle="modal" data-remote="true" data-ajax-modal data-target="#editPlanModal" class="btn btn-success btn-with-icon btn-add" style="margin-bottom: 20px;">Add</a>

                @endforeach


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