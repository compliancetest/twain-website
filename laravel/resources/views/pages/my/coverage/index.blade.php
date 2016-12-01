@extends('app')

@section('content')

    <div class="container main-container">

        @include('pages.user-tabs', ['tab' => 'test-suite-coverage'])

        <div class="main-content">

            <div class="test-coverage" id="testCoveragePlanList">
                <div id="testCoveragePlanListContent" class="block-loading-wrapper">
                    {!! view('pages.my.coverage.test_plans_list', ['userSuites' => $userSuites]) !!}
                </div>

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
                        $('body').on('click', '.deleteTestCoveragePlan', function(e){
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
                                            var table = $('#coverage-plan-' + testCoveragePlan.id).closest('table');
                                            $(this).remove();
                                            if (table.find('tbody tr').length == 0) {
                                                table.find('tbody').html('<tr><td class="text-center" colspan="7">No Test Plans yet</td></tr>')
                                            }
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