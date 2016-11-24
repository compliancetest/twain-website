@extends('app')

@section('content')

    <div class="container main-container">

        @include('pages.user-tabs', ['tab' => 'verify-requests'])

        <div class="main-content block-loading-wrapper">

            @if($isAdmin && $userSuites)
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

                <!-- UnAssign Verify Request Modal-->
                <div class="modal fade" id="unassignVerifyRequestModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document" style="width: 500px;">
                        <div class="modal-content block-loading-wrapper">
                            <div class="modal-header">
                                <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
                                Un-Assign A Verify Request
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

                <!-- Accept Verify Request Modal-->
                <div class="modal fade" id="acceptVerifyRequestModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document" style="width: 500px;">
                        <div class="modal-content block-loading-wrapper">
                            <div class="modal-header">
                                <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
                                Accept A Verify Request
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
                <div class="modal fade" id="modalLogTestingDetails" tabindex="-1" role="dialog">
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
                                Verify Test Result
                            </div>
                            <div class="modal-body">
                                <div class="change_status_message">
                                    Are you sure you want change outcome status to "<span class="change_to_status"></span>" for selected test results?

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

                <!-- UnAssign Verify Request Modal-->
                <div class="modal fade viewImagesModal" id="viewImagesModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-fluid" role="document">
                        <div class="modal-content block-loading-wrapper">
                            <div class="modal-header">
                                <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
                                Image Viewer
                            </div>
                            <div class="modal-body"></div>
                            <div class="modal-footer">
                                <button class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Close</button>
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

                @include('loader', ['loaderClass' => 'verify-requests-loading', 'loaderMessage' => 'LOADING...'])

                <!-- Init modal scripts-->
                <script>
                    jQuery(document).ready(function ($) {

                        @if($isAdmin)
                            Page.verifyRequest.supportUpdateCheckboxes();
                        @endif

                        $('body').on('click', '.change_status', function(){
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
                                    'reason': jQuery('.change_status_data_type').val() == 'Pass' ? 0 : $('#reason_message').val(),
                                    'hideResolved': $('#hideResolved:checked').length,
                                    'hideOthers': $('#hideOthers:checked').length,
                                },
                                type: 'post',
                                dataType: 'json',
                                success: function (rsp) {
                                    $('.modal').modal('hide');
                                    $('#changeStatusModal .block-loading').hide();
                                    $('#' + $('.change_status_row_id').val() + ' input.transaction:checked').each(function (index, elem) {
                                        $(elem).removeAttr('checked').attr('disabled', 'disabled');
                                        $(elem).closest('tr').find('td.row-outcome-status').html(jQuery('.change_status_data_type').val());
                                    });
                                    if ($('#' + $('.change_status_row_id').val() + ' input.transaction:checked').length == 0) {
                                        $('#verifyRequestsListContent').html(rsp.html);
                                        setTimeout(function () {
                                            $('.modal').modal('hide');
                                        }, 1500);
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
                        $('#modalLogTestingDetails').on('hidden.bs.modal', function () {
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
                                            var table = $('#verify-request-' + testCoveragePlan.id).closest('table');
                                            $(this).next('tr').remove();
                                            $(this).remove();
                                            if (table.find('tbody tr').length == 0) {
                                                table.find('tbody').html('<tr><td class="text-center" colspan="7">No Verify Requests yet</td></tr>')
                                            }
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

                        $('body').on('click', '.showImageViewer', function () {
                            $('#viewImagesModal .modal-body').html('');
                            $('#viewImagesModal .block-loading').addClass('loading-shown');
                        });

                         //When open log, load transaction details
                        $('body').on('show.bs.collapse', '.logRow', function () {
                            var transactionId = $(this).data('transactionId');
                            var entry = $(this);

                            if (!entry.data('loaded')) {
                                jQuery.ajax({
                                    url: '/testingdetails/' + transactionId + '/logs',
                                    type: 'get',
                                    success: function (data) {
                                        entry.find('td').html(data);
                                        entry.data('loaded', 1);
                                    }
                                });
                            }
                        });
                    });
                </script>

            </div>

        </div>
    </div>

@stop