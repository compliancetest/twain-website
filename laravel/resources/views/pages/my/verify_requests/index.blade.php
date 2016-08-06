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
                <li class="coverage-tab"><a href="/test-suite-coverage/" data-tooltip="tooltip" title="Completeness of my testing">Coverage</a></li>
                <li class="coverage-tab"><a href="/verify-requests/" class="active" data-tooltip="tooltip" title="My Verify Transactions Requests">Verify Requests</a></li>
                <li class="transactions-tab"><a href="/my-transaction-log/" data-tooltip="tooltip" title="My test transactions">Transactions</a></li>
                <li class="support-tab"><a href="/my-support-tickets/" data-tooltip="tooltip" title="My support tickets">Support</a></li>
                <li class="profile-tab"><a href="/my-profile/" data-tooltip="tooltip" title="My profile">Profile</a></li>

            </ul>
        </div>

        <div class="main-content">

            @if($isAdmin)
                <div class="row" style="margin-bottom: 10px;">
                    <div class="col-lg-4">
                        <input type="checkbox" id="hideResolved" value="1" checked="checked">Hide Resolved
                        <input type="checkbox" id="hideOthers" value="1" checked="checked">Hide Others
                    </div>
                </div>
            @endif

            <div class="test-coverage" id="verifyRequestsList">
                <div id="verifyRequestsListContent">
                    @include('pages.my.verify_requests.list', ['userSuites' => $userSuites])
                </div>

                <!-- Create Verify Request Modal-->
                <div class="modal fade" id="createVerifyRequestModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document" style="width: 700px;">
                        <div class="modal-content block-loading-wrapper">
                            <div class="modal-header">
                                <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
                                Add A Verify Request
                            </div>
                            <div class="modal-body"></div>
                            <div class="modal-footer">
                                <a href="#" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</a>
                            </div>
                            <div class="block-loading loading-shown">
                                <div class="loading-content"><span class="loader"></span>

                                    <div class="loading-text">LOADING DATA</div>
                                    <div class="loading-wait">Please wait...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Assign Verify Request Modal-->
                <div class="modal fade" id="assignVerifyRequestModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document" style="width: 500px;">
                        <div class="modal-content block-loading-wrapper">
                            <div class="modal-header">
                                <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
                                Assign A Verify Request
                            </div>
                            <div class="modal-body"></div>
                            <div class="modal-footer">
                                <a href="#" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</a>
                            </div>
                            <div class="block-loading loading-shown">
                                <div class="loading-content"><span class="loader"></span>

                                    <div class="loading-text">LOADING DATA</div>
                                    <div class="loading-wait">Please wait...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- View Transation Reason Modal-->
                <div class="modal fade" id="viewReasonModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document" style="width: 500px;">
                        <div class="modal-content block-loading-wrapper">
                            <div class="modal-header">
                                <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
                                Message Data
                            </div>
                            <div class="modal-body"></div>
                            <div class="modal-footer">
                                <a href="#" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</a>
                            </div>
                            <div class="block-loading loading-shown">
                                <div class="loading-content"><span class="loader"></span>

                                    <div class="loading-text">LOADING DATA</div>
                                    <div class="loading-wait">Please wait...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- View Output Modal-->
                <div class="modal fade" id="viewOutputModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document" style="width: 900px;">
                        <div class="modal-content block-loading-wrapper">
                            <div class="modal-header">
                                <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
                                Message Data
                            </div>
                            <div class="modal-body"></div>
                            <div class="modal-footer">
                                <a href="#" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</a>
                            </div>
                            <div class="block-loading loading-shown">
                                <div class="loading-content"><span class="loader"></span>

                                    <div class="loading-text">LOADING DATA</div>
                                    <div class="loading-wait">Please wait...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Change Status Modal-->
                <div class="modal fade" id="changeStatusModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document" style="width: 500px;">
                        <div class="modal-content block-loading-wrapper">
                            <div class="modal-header">
                                <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
                                Message Data
                            </div>
                            <div class="modal-body">
                                <div class="change_status_message">
                                    Are you sure you want change outcome status to "<span class="change_to_status"></span>" for selected transactions?

                                    <div class="form-group" id="transaction_reason" style="margin-top: 20px;">
                                        <label for="reason">Reason</label>
                                        <input name="reason" type="text" class="form-control" id="reason_message"/>
                                    </div>
                                    <input type="text" name="reason" id="transaction_reason" style="display: none;">
                                </div>
                                <div class="change_status_no_messages">
                                    Please select a row
                                </div>
                                <input type="hidden" value="" class="change_status_data_type">
                                <input type="hidden" value="" class="change_status_row_id">
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-success btn-with-icon btn-confirm confirm_change_status">Confirm</button>
                                <button class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
                            </div>
                            <div class="block-loading">
                                <div class="loading-content"><span class="loader"></span>

                                    <div class="loading-text">LOADING DATA</div>
                                    <div class="loading-wait">Please wait...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Init modal scripts-->
                <script>
                    jQuery(document).ready(function ($) {

                        @if($isAdmin)
                            Page.verifyRequest.supportUpdateCheckboxes();
                        @endif

                        $('.change_status').click(function(){
                            $('.change_status_data_type').val($(this).attr('data-outcome'));
                            if($(this).attr('data-outcome') == 'Pass'){
                                $('#transaction_reason').hide();
                            } else {
                                $('#transaction_reason').show();
                            }
                            $('.change_status_row_id').val($(this).closest('.details_row').attr('id'));
                            $('.change_to_status').text($(this).attr('data-outcome'));
                            var checkboxes = $(this).closest('.details_row').find('input.transaction:checked');
                            if(checkboxes.length == 0){
                                $('.change_status_message').hide();
                                $('.change_status_no_messages').show();
                                $('.confirm_change_status').hide();
                            } else {
                                $('.change_status_message').show();
                                $('.change_status_no_messages').hide();
                                $('.confirm_change_status').show();
                            }
                        })

                        $('.confirm_change_status').on('click', function(e){
                            var ids = new Array();
                            jQuery('#'+$('.change_status_row_id').val()+' input.transaction:checked').each(function () {
                                ids.push(this.value);
                            });

                            jQuery('#changeStatusModal .block-loading').show();


                            jQuery.ajax({
                                url: '/verify-requests/update-transactions',
                                data: {
                                    'verify_request_id': $('.change_status_row_id').val().replace('verify-request-details-', ''),
                                    'transactions': ids,
                                    'outcome_code': jQuery('.change_status_data_type').val(),
                                    'reason': jQuery('.change_status_data_type').val() == 'Pass' ? false : $('#reason_message').val()
                                },
                                type: 'post',
                                dataType: 'json',
                                success: function (rsp) {
                                    $('.modal').modal('hide');
                                    $('#changeStatusModal .block-loading').hide();
                                    $('#'+$('.change_status_row_id').val()+' input.transaction:checked').each(function (index, elem) {
                                        $(elem).removeAttr('checked').attr('disabled', 'disabled');
                                        $(elem).closest('tr').find('td.row-outcome-status').html(jQuery('.change_status_data_type').val());
                                    });
                                    if($('#'+$('.change_status_row_id').val()+' input.transaction:checked').length == 0){
                                        location.reload();
                                    }

                                },
                                 error: function (jqXHR, status) {
                                     jQuery('#changeStatusModal .block-loading').hide();
                                     $('#changeStatusModal .modal-body').prepend('<div class="error-message">' + formatErrorMessage(jqXHR, status) + '</div>');
                                     setTimeout(function () {
                                        $('#changeStatusModal .modal-body > .error-message').slideUp(function () {
                                            $(this).remove();
                                        });
                                     }, 3000);
                                }
                            });
                        });

                        //clear previous output data and show loading for next view popup
                        $('#viewOutputModal').on('hidden.bs.modal', function () {
                            $(this).find('.modal-content #data').html('');
                            $(this).find('.modal-content .block-loading').show();
                        });

                        $('#createVerifyRequestModal').on("show.bs.modal", function () {
                            $(this).find('.modal-content').append('<div class="block-loading loading-shown"><div class="loading-content"><span class="loader"></span><div class="loading-text">LOADING DATA</div><div class="loading-wait">Please wait...</div></div></div>');
                        }).on('hidden.bs.modal', function () {
                            $(this).find('.modal-content').append('<div class="block-loading loading-shown"><div class="loading-content"><span class="loader"></span><div class="loading-text">LOADING DATA</div><div class="loading-wait">Please wait...</div></div></div>');
                        });


                        //Remove test coverage plan modal
                        $('body').on('click', '.deleteVerifyRequest', function (e) {
                            var self = $(this);
                            e.preventDefault();

                            var testCoveragePlan = {
                                id: self.data('request-id'),
                                link: self.attr('href')
                            };

                            jQuery('#removeVerifyRequestModal-' + testCoveragePlan.id + ' .modal-content').append('<div class="block-loading loading-shown"><div class="loading-content"><span class="loader"></span><div class="loading-text">PROCESSING</div><div class="loading-wait">Please wait...</div></div></div>');
                            jQuery.ajax({
                                type: 'delete',
                                url: testCoveragePlan.link,
                                success: function (data) {
                                    $('.modal').modal('hide');
                                    if (data.status == 'success') {
                                        $('#verify-request-' + testCoveragePlan.id).addClass('removing').fadeTo("slow", 0.3, function () {
                                            $(this).next('tr').remove();
                                            $(this).remove();
                                            $('#verifyRequestsList').prepend('<div class="success-message">Verify Request has been removed</div>');
                                            setTimeout(function () {
                                                $('#verifyRequestsList > .success-message').slideUp(function () {
                                                    $(this).remove();
                                                });
                                            }, 2000);
                                        });
                                    }
                                },
                                error: function (jqXHR, status) {
                                    $('.modal').modal('hide');
                                    $('#verifyRequestsList').prepend('<div class="error-message">' + formatErrorMessage(jqXHR, status) + '</div>');
                                    setTimeout(function () {
                                        $('#verifyRequestsList > .error-message').slideUp(function () {
                                            $(this).remove();
                                        });
                                    }, 3000);
                                }
                            })
                        });
                    });
                </script>

            </div>

        </div>
    </div>

@stop