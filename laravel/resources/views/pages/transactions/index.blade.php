@extends('app')

@section('content')

    <div class="container main-container">

        @include('pages.user-tabs', ['tab' => 'my-transaction-log'])

        <div class="main-content">
            <div class="filter-box">
                <div class="filter-box-title">Filter By:</div>
                <div id="transactionSearchFilterContent" class="filter-box-content block-loading-wrapper">

                    @include('pages.transactions.filters')

                </div>
            </div>
            <div class="block-loading-wrapper">
                <div class="filter-list-actions transaction-list-actions">
                    <div class="pull-left">
                        <a href="#bulkAuditModal" data-toggle="modal" class="btn btn-success btn-with-icon btn-trigger bulk_audit"
                           data-tooltip="tooltip" title="Select multiple Test Results as Audit Records">Bulk Audit</a>
                        @if($supportOrAdmin)
                            <a href="#verifyAsModal" data-toggle="modal" class="btn btn-success btn-with-icon btn-trigger change_status" data-outcome="Pass"
                               data-tooltip="tooltip" title="Change Test Results to Pass (after verification)">Verify As Pass</a>
                            <a href="#verifyAsModal" data-toggle="modal" class="btn btn-danger btn-with-icon btn-trigger change_status" data-outcome="Fail"
                               data-tooltip="tooltip" title="Change Test Results to Fail (after verification)">Verify As Fail</a>
                            <a href="#verifyAsModal" data-toggle="modal" class="btn btn-default btn-with-icon btn-trigger change_status" data-outcome="Skip"
                               data-tooltip="tooltip" title="Change Test Results to Skip">Verify As Skip</a>
                        @endif
                        <a href="#deleteTransactionModal" data-toggle="modal" class="btn btn-danger btn-with-icon btn-delete delete_transactions"
                           data-tooltip="tooltip" title="Permanently delete Test Results (this cannot be undone!)">Delete Test Results</a>
                        <button type="button" class="btn btn-default btn-with-icon btn-collapse" id="collapseAllResults" data-tooltip="tooltip"
                                title="Close all Test Result details and return to Test Result list">Collapse All Results
                        </button>

                    <span class="tooltip-wrapper" data-toggle="tooltip" data-trigger="hover" data-container="body" title="Copy Test Results to another Test Suite">
                        <a class="btn btn-success btn-with-icon btn-trigger migrate_transactions" href="/transactions/migrate" data-toggle="modal" data-remote="true"
                           data-ajax-modal data-target="#migrateTransactionModal">Migrate Test Results</a>
                    </span>

                    </div>
                    <div class="pull-right pagination-box">
                        <div class="form-inline">
                            <div class="form-group">
                                <label for="paginationLimit">Display #</label>
                                <select class="form-control" id="paginationLimit" name="limit">
                                    <option value="10" @if($perPage == 10) selected="selected" @endif>10</option>
                                    <option value="25" @if($perPage == 25) selected="selected" @endif>25</option>
                                    <option value="50" @if($perPage == 50) selected="selected" @endif>50</option>
                                    <option value="100" @if($perPage == 100) selected="selected" @endif>100</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="transactionSearchResultsTable">
                    @include('pages.transactions.transactions')
                </div>

                <div id="transactionSearchResultsSpinner" class="block-loading">
                    <div class="loading-content"><span class="loader"></span>
                        <div class="loading-text">LOADING DATA</div>
                        <div class="loading-wait">Please wait...</div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    {{-- Testing Details Modal--}}
    <div class="modal fade" id="modalLogTestingDetails" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup" data-placement="left" data-dismiss="modal" aria-label="Close">Close
                    </button>
                    Message Data
                </div>
                <div class="modal-body">
                    <div class="block-loading">
                        <div class="loading-content"><span class="loader"></span>
                            <div class="loading-text">LOADING DATA</div>
                            <div class="loading-wait">Please wait...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Migrate transactions Modal--}}
    <div class="modal fade" id="migrateTransactionModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document" style="width: 500px;">
            <div class="modal-content block-loading-wrapper">
                <div class="modal-header">
                    <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
                    Migrate Test Results
                </div>
                <div class="modal-body">
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

    {{-- Change transaction status Modal--}}
    <div class="modal fade" id="verifyAsModal" tabindex="-1" role="dialog">
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

    {{-- Bulk Audit Modal--}}
    <div class="modal fade" id="bulkAuditModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document" style="width: 500px;">
            <div class="modal-content block-loading-wrapper">
                <div class="modal-header">
                    <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
                    Bulk Audit
                </div>
                <div class="modal-body">
                    <div class="bulk_rows_exist">
                        <div class="alert alert-warning" role="alert">
                            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                            <span class="sr-only">Error:</span>
                            Note that entries with 'Pending' status will not be processed
                        </div>
                        Please confirm that you want mark selected test results as Audit Records
                    </div>
                    <div class="bulk_no_rows">
                        Please select a row
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success btn-with-icon btn-confirm confirm_bulk_audit">Confirm</button>
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

    {{-- Screen Captures Modal--}}
    <div class="modal fade" id="modalScreenCaptures" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document" style="width: 500px;">
            <div class="modal-content block-loading-wrapper">
                <div class="modal-header">
                    <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
                    Screen Captures
                </div>
                <div class="modal-body">
                    <div class="block-loading">
                        <div class="loading-content"><span class="loader"></span>
                            <div class="loading-text">LOADING DATA</div>
                            <div class="loading-wait">Please wait...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Transaction Modal--}}
    <div class="modal fade" id="deleteTransactionModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document" style="width: 500px;">
            <div class="modal-content block-loading-wrapper">
                <div class="modal-header">
                    <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
                    Delete Test Results
                </div>
                <div class="modal-body">
                    <div class="delete_message">
                        Are you sure you want delete selected test results?
                    </div>
                    <div class="delete_no_rows_message">
                        Please select a row
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success btn-with-icon btn-confirm confirm_delete_transactions">Confirm</button>
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

    @if($explainRequestsEnabled)
        {{-- Explanation Modal--}}
        <div class="modal fade" id="viewExplanationLogs" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document" style="width: 600px;">
                <div class="modal-content block-loading-wrapper">
                    <div class="modal-header">
                        <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
                        Messages
                    </div>
                    <div class="modal-body">
                        <div class="block-loading">
                            <div class="loading-content"><span class="loader"></span>
                                <div class="loading-text">LOADING DATA</div>
                                <div class="loading-wait">Please wait...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif


    @include('pages.popups.transaction_reason')

@stop

@section('page-scripts')
    <script src="{{ getSiteUrl() }}/laravel/resources/assets/js/vendor/jquery.flexslider-min.js"></script>
    <script src="{{ getSiteUrl() }}/laravel/resources/assets/js/vendor/bootstrap-datepicker.min.js"></script>
    <script>
        jQuery(document).ready(function ($) {

            $('body').on('change', '#paginationLimit', function () {
                $('#perPage').val($(this).val());
                $('#filterByForm').submit();
            });

            $('body').on('change', '.checkAll', function () {
                $('.checkTransaction').not(':disabled').prop('checked', $(this).is(':checked'));
            });

            $('body').on('click', '.change_status', function () {
                $('.change_status_data_type').val($(this).attr('data-outcome'));
                if ($(this).attr('data-outcome') == 'Pass') {
                    $('#transaction_reason').hide();
                } else {
                    $('#transaction_reason').show();
                }
                $('.change_to_status').text($(this).attr('data-outcome'));
                var checkboxes = $('input.checkTransaction:checked');
                if (checkboxes.length == 0) {
                    $('.change_status_message').hide();
                    $('.change_status_no_messages').show();
                    $('.confirm_change_status').hide();
                } else {
                    $('.change_status_message').show();
                    $('.change_status_no_messages').hide();
                    $('.confirm_change_status').show();
                }
            });

            $('body').on('click', '.bulk_audit', function () {
                $.each($('input.checkTransaction:checked'), function (index, checkboxEntry) {
                    if ($(checkboxEntry).closest('tr').find('.auditRecordCheckbox').is(':disabled')) {
                        $(checkboxEntry).prop('checked', false);
                    }
                });
                var checkboxes = $('input.checkTransaction:checked');

                if (checkboxes.length == 0) {
                    $('.bulk_rows_exist, .confirm_bulk_audit').hide();
                    $('.bulk_no_rows').show();
                } else {
                    $('.bulk_rows_exist, .confirm_bulk_audit').show();
                    $('.bulk_no_rows').hide();
                }
            });

            $('.confirm_bulk_audit').on('click', function () {
                var ids = new Array();
                jQuery('.checkTransaction:checked').each(function () {
                    ids.push(this.value);
                });

                jQuery('#bulkAuditModal .block-loading').show();

                jQuery.ajax({
                    url: '/transactions/bulk-audit',
                    data: {
                        'transactions': ids
                    },
                    type: 'post',
                    dataType: 'json',
                    success: function (rsp) {
                        $('.modal').modal('hide');
                        $('#bulkAuditModal .block-loading').hide();
                        $('#transactionSearchFilterByForm').submit();
                    },
                    error: function (jqXHR, status) {
                        jQuery('#bulkAuditModal .block-loading').hide();
                        $('#bulkAuditModal .modal-body').append('<div class="error-message">' + formatErrorMessage(jqXHR, status) + '</div>');
                        setTimeout(function () {
                            $('#bulkAuditModal .modal-body > .error-message').slideUp(function () {
                                $(this).remove();
                            });
                        }, 3000);
                    }
                });
            });

            $('body').on('click', '.submit-new-message', function () {
                jQuery('#viewExplanationLogs .block-loading').show();
                var transactionId = $('#transactionId').val();
                jQuery.ajax({
                    url: '/transactions/' + transactionId + '/explanation-logs/create',
                    data: {
                        'message': jQuery('#explanationMessage').val()
                    },
                    type: 'post',
                    dataType: 'json',
                    success: function (message) {
                        $('#viewExplanationLogs .modal-content').html(message.html);
                        $('#viewExplanationLogs .modal-body').append('<div class="success-message">Your message has been sent!</div>');
                        $('#questionBtn' + transactionId).removeClass('btn-default').addClass('btn-success');
                        setTimeout(function () {
                            $('#viewExplanationLogs .modal-body > .success-message').slideUp(function () {
                                $(this).remove();
                            });
                        }, 3000);

                    },
                    error: function (jqXHR, status) {
                        jQuery('#viewExplanationLogs .block-loading').hide();
                        $('#viewExplanationLogs .modal-body').append('<div class="error-message">' + formatErrorMessage(jqXHR, status) + '</div>');
                        setTimeout(function () {
                            $('#viewExplanationLogs .modal-body > .error-message').slideUp(function () {
                                $(this).remove();
                            });
                        }, 3000);
                    }
                });
            });

            $('body').on('click', '.delete_transactions', function () {
                var checkboxes = $('input.checkTransaction:checked');
                if (checkboxes.length == 0) {
                    $('.delete_message, .confirm_delete_transactions').hide();
                    $('.delete_no_rows_message').show();
                } else {
                    $('.delete_message, .confirm_delete_transactions').show();
                    $('.delete_no_rows_message').hide();
                }
            });

            $('.confirm_delete_transactions').on('click', function () {
                var ids = new Array();
                jQuery('.checkTransaction:checked').each(function () {
                    ids.push(this.value);
                });

                jQuery('#verifyAsModal .block-loading').show();

                jQuery.ajax({
                    url: '/transactions/batch-delete',
                    type: 'delete',
                    data: {
                        'transactions': ids,
                    },
                    dataType: 'json',
                    success: function (rsp) {
                        $('.modal').modal('hide');
                        $('#verifyAsModal .block-loading').hide();
                        $('#transactionSearchFilterByForm').submit();
                    },
                    error: function (jqXHR, status) {
                        jQuery('#verifyAsModal .block-loading').hide();
                        $('#verifyAsModal .modal-body').prepend('<div class="error-message">' + formatErrorMessage(jqXHR, status) + '</div>');
                        setTimeout(function () {
                            $('#verifyAsModal .modal-body > .error-message').slideUp(function () {
                                $(this).remove();
                            });
                        }, 3000);
                    }
                });
            });

            $('.confirm_change_status').on('click', function () {
                var ids = new Array();
                jQuery('.checkTransaction:checked').each(function () {
                    ids.push(this.value);
                });

                jQuery('#verifyAsModal .block-loading').show();

                jQuery.ajax({
                    url: '/transactions/update-transactions',
                    data: {
                        'transactions': ids,
                        'outcome_code': jQuery('.change_status_data_type').val(),
                        'reason': jQuery('.change_status_data_type').val() == 'Pass' ? 0 : $('#reason_message').val(),
                    },
                    type: 'post',
                    dataType: 'json',
                    success: function (rsp) {
                        $('.modal').modal('hide');
                        $('#verifyAsModal .block-loading').hide();
                        $('#transactionSearchFilterByForm').submit();
                    },
                    error: function (jqXHR, status) {
                        jQuery('#verifyAsModal .block-loading').hide();
                        $('#verifyAsModal .modal-body').prepend('<div class="error-message">' + formatErrorMessage(jqXHR, status) + '</div>');
                        setTimeout(function () {
                            $('#verifyAsModal .modal-body > .error-message').slideUp(function () {
                                $(this).remove();
                            });
                        }, 3000);
                    }
                });
            });

            $('body').on('change', '.auditRecordCheckbox', function (e) {
                e.preventDefault();
                $('#loadLogResultsSpinner').show();
                var auditCheckbox = $(this);
                $.ajax({
                    url: '/transactions/' + auditCheckbox.attr('data-id') + '/updateauditrecord',
                    type: 'post',
                    data: {
                        'audit_record': auditCheckbox.is(':checked')
                    },
                    error: function (jqXHR, status) {
                    },
                    success: function (rsp) {
                    },
                    complete: function () {
                        $('#loadLogResultsSpinner').hide();
                        if (auditCheckbox.is(':checked')) {
                            auditCheckbox.closest('tr').find('.checkTransaction').attr('disabled', 'disabled').prop('checked', false);
                        } else {
                            auditCheckbox.closest('tr').find('.checkTransaction').removeAttr('disabled');
                        }
                    }
                })
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

            //OnClose Message Data popup remove data and replace it to the loader
            $('#modalLogTestingDetails').on('hidden.bs.modal', function () {
                var popupLoadingBlock = '<div class="modal-header">' +
                        '<button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup" data-placement="left" data-dismiss="modal" aria-label="Close">Close</button>' +
                        'Message Data' +
                        '</div>' +
                        '<div class="modal-body">' +
                        '<div class="block-loading"><div class="loading-content"><span class="loader"></span><div class="loading-text">LOADING DATA</div><div class="loading-wait">Please wait...</div></div></div>' +
                        '</div>';
                $(this).find('.modal-content').html(popupLoadingBlock);
            });

            $('#collapseAllResults').click(function () {
                $('.logRow').collapse('hide');
            });

            var actionPos = $('.transaction-list-actions').offset();
            $(window).bind('scroll', function () {
                var topScroll = $(window).scrollTop();
                if (topScroll > actionPos.top) {
                    $('.transaction-list-actions').addClass('fixed');
                    $('.transaction-list-actions .btn').removeAttr('data-tooltip');
                }
                else {
                    $('.transaction-list-actions').removeClass('fixed');
                    $('.transaction-list-actions .btn').attr('data-tooltip', 'tooltip');
                }
            });

            $('body').on('change', '#suiteFrom, #suiteTo', function () {
                jQuery('#migrateTransactionModal .block-loading').show();
                jQuery.ajax({
                    url: '/transactions/migrate',
                    data: {
                        'suiteFrom': jQuery('#suiteFrom').val(),
                        'suiteTo': jQuery('#suiteTo').val(),
                    },
                    type: 'get',
                    dataType: 'json',
                    success: function (message) {
                        $('#migrateTransactionModal .modal-content').html(message.html);
                    },
                    error: function (jqXHR, status) {
                        jQuery('#migrateTransactionModal .block-loading').hide();
                        $('#migrateTransactionModal .modal-body').append('<div class="error-message">' + formatErrorMessage(jqXHR, status) + '</div>');
                        setTimeout(function () {
                            $('#migrateTransactionModal .modal-body > .error-message').slideUp(function () {
                                $(this).remove();
                            });
                        }, 3000);
                    }
                });
            });

            $('body').on('click', '.submit-migration', function () {
                jQuery('#migrateTransactionModal .block-loading').show();
                jQuery.ajax({
                    url: '/transactions/migrate',
                    data: {
                        'suiteFrom': jQuery('#suiteFrom').val(),
                        'suiteTo': jQuery('#suiteTo').val(),
                        'transactions': $('.transaction:checked').map(function () {
                            return $(this).val()
                        }).get()
                    },
                    type: 'post',
                    dataType: 'json',
                    success: function (message) {
                        $('#migrateTransactionModal .modal-body').append('<div class="success-message">Transactions has been copied successfully!</div>');
                        $('.submit-migration').hide();
                        $('#migrateTransactionModal .block-loading').hide();
                        $('#filterSuite').val(jQuery('#suiteTo').val());
                        setTimeout(function () {
                            $('.modal').modal('hide');
                            $('#transactionSearchFilterByForm').submit();
                        }, 2500);
                    },
                    error: function (jqXHR, status) {
                        jQuery('#migrateTransactionModal .block-loading').hide();
                        $('#migrateTransactionModal .modal-body').append('<div class="error-message">' + formatErrorMessage(jqXHR, status) + '</div>');
                        setTimeout(function () {
                            $('#migrateTransactionModal .modal-body > .error-message').slideUp(function () {
                                $(this).remove();
                            });
                        }, 3000);
                    }
                });
            });

            Page.ajaxSearchForm.init();

        });
    </script>
@stop