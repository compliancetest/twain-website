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
            <li class="coverage-tab"><a href="/verify-requests/" data-tooltip="tooltip" title="My Verify Transactions Requests">Verify Requests</a></li>
            <li class="transactions-tab"><a href="/my-transaction-log/" class="active" data-tooltip="tooltip" title="My test transactions">Transactions</a></li>
            <li class="support-tab"><a href="/my-support-tickets/" data-tooltip="tooltip" title="My support tickets">Support</a></li>
            <li class="profile-tab"><a href="/my-profile/" data-tooltip="tooltip" title="My profile">Profile</a></li>

        </ul>
    </div>

    <div class="main-content">
        <div class="transaction-filter">
            <div class="transaction-filter-title">Filter By:</div>
            <div class="transaction-filter-content block-loading-wrapper">

                @include('pages.transactions.filters')

            </div>
        </div>
        <div class="block-loading-wrapper">
            <div class="transaction-list-actions">
                <div class="pull-left">
                    @if($supportOrAdmin)
                        <a href="#verifyAsModal" data-toggle="modal" class="btn btn-success btn-with-icon btn-trigger change_status" data-outcome="Pass"
                           data-tooltip="tooltip" title="Verify As Pass">Verify As Pass</a>
                        <a href="#verifyAsModal" data-toggle="modal" class="btn btn-danger btn-with-icon btn-trigger change_status" data-outcome="Fail"
                               data-tooltip="tooltip" title="Verify As Fail">Verify As Fail</a>
                        <a href="#verifyAsModal" data-toggle="modal" class="btn btn-default btn-with-icon btn-trigger change_status" data-outcome="Skip"
                               data-tooltip="tooltip" title="Verify As Skip">Verify As Skip</a>
                    @endif
                </div>
                <div class="pull-right">
                    <button type="button" class="btn btn-danger btn-with-icon btn-delete" data-tooltip="tooltip" data-placement="top">Remove Selected</button>
                    {{--<div class="form-inline">--}}
                        {{--<div class="form-group">--}}
                            {{--<label for="paginationLimit">Display #</label>--}}
                            {{--<select class="form-control" id="paginationLimit" name="limit">--}}
                                {{--<option value="10">10</option>--}}
                                {{--<option value="20">20</option>--}}
                                {{--<option value="50">50</option>--}}
                                {{--<option value="100">100</option>--}}
                                {{--<option value="-1">All</option>--}}
                            {{--</select>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                </div>
            </div>

            <div id="log-result-table">
                @include('pages.transactions.transactions')
            </div>

            <div id="loadLogResultsSpinner" class="block-loading">
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
                <button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup" data-placement="left" data-dismiss="modal" aria-label="Close">Close</button>
                Message Data
            </div>
            <div class="modal-body">
                <div class="block-loading"><div class="loading-content"><span class="loader"></span><div class="loading-text">LOADING DATA</div><div class="loading-wait">Please wait...</div></div></div>
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
                Verify Transaction
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

@include('pages.popups.transaction_reason')
@stop

@section('page-scripts')
<script src="{{ getSiteUrl() }}/laravel/resources/assets/js/vendor/bootstrap-datepicker.min.js"></script>
<script>
    jQuery(document).ready(function($) {
        $('#filterCalendar').click(function () {
            $('#filterDate').datepicker('show');
        });

        $('body').on('change', '.checkAll', function () {
            $('.checkTransaction').prop('checked', $(this).is(':checked'));
        });

        $('body').on('click', '.change_status', function(){
            $('.change_status_data_type').val($(this).attr('data-outcome'));
            if($(this).attr('data-outcome') == 'Pass'){
                $('#transaction_reason').hide();
            } else {
                $('#transaction_reason').show();
            }
            $('.change_to_status').text($(this).attr('data-outcome'));
            var checkboxes = $('input.checkTransaction:checked');
            if(checkboxes.length == 0){
                $('.change_status_message').hide();
                $('.change_status_no_messages').show();
                $('.confirm_change_status').hide();
            } else {
                $('.change_status_message').show();
                $('.change_status_no_messages').hide();
                $('.confirm_change_status').show();
            }
        });

        $('.confirm_change_status').on('click', function(e){
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
                    $('#filterByForm').submit();
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

        /**
         * Extract GET param value from URL
         * @param url
         * @param key
         * @returns {Array|{index: number, input: string}|string}
         */
        function getUrlVar(url, key){
            var result = new RegExp(key + "=([^&]*)", "i").exec(url);
            return result && unescape(result[1]) || "";
        }

        $('body').on('click', '.pagination a', function(e){
            e.preventDefault();
            $('#filterBySpinner, #loadLogResultsSpinner').show();
            var link = $(this);
            var form = $('#filterByForm');
            $.ajax({
                url: '/transactions/transactions-list',
                type: 'get',
                data: form.serialize() + "&page=" + getUrlVar(link.attr('href'), 'page'),
                error: function (jqXHR, status) {
                },
                success: function (rsp) {
                    $('#log-result-table').html(rsp.html);
                },
                complete: function () {
                    $('#filterBySpinner, #loadLogResultsSpinner').hide();
                }
            });
        });

        $('body').on('click', '.btn-clear', function () {
            $('#filterByForm')[0].reset();
            getTransactionFilters();
        });

        $('body').on('change', '#filterByForm .form-control', function () {
            $('#filterBySpinner').show();
            var form = $('#filterByForm');
            getTransactionFilters(form.serialize());
        });

        $('body').on('change', '.auditRecordCheckbox', function (e) {
            e.preventDefault();
            $('#filterBySpinner').show();
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
                    $('#filterBySpinner').hide();
                    if(auditCheckbox.is(':checked')){
                        auditCheckbox.closest('tr').find('.checkTransaction').attr('disabled', 'disabled').prop('checked', false);
                    } else {
                        auditCheckbox.closest('tr').find('.checkTransaction').removeAttr('disabled');
                    }
                }
            })
        });

        $('body').on('submit', '#filterByForm', function (e) {
            e.preventDefault();
            $('#filterBySpinner, #loadLogResultsSpinner').show();

            var form = $('#filterByForm');
            $.ajax({
                url: '/transactions/transactions-list',
                type: 'get',
                data: form.serialize(),
                error: function (jqXHR, status) {
                },
                success: function (rsp) {
                    $('#log-result-table').html(rsp.html);
                },
                complete: function () {
                    $('#filterBySpinner, #loadLogResultsSpinner').hide();
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
        $('#modalLogTestingDetails').on('hidden.bs.modal', function (e) {
            var popupLoadingBlock = '<div class="modal-header">' +
                '<button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup" data-placement="left" data-dismiss="modal" aria-label="Close">Close</button>' +
                'Message Data' +
                '</div>' +
                '<div class="modal-body">' +
                '<div class="block-loading"><div class="loading-content"><span class="loader"></span><div class="loading-text">LOADING DATA</div><div class="loading-wait">Please wait...</div></div></div>' +
                '</div>';
            $(this).find('.modal-content').html(popupLoadingBlock);
        });


        $('body').on('click', '#filterByForm .clear-filter', function (e) {
            $(this).parent().find('input, select').val('');
            var form = $('#filterByForm');
            getTransactionFilters(form.serialize());
        });

        /**
         * Load actual filters data
         * @param data Serialised form data
         */
        function getTransactionFilters(data){
            $('#filterBySpinner').show();
            $.ajax({
                url: '/transactions/filters',
                type: 'get',
                data: data,
                error: function (jqXHR, status) {
                },
                success: function (rsp) {
                    $('.transaction-filter-content').html(rsp.html);
                },
                complete: function () {
                    $('#filterBySpinner').hide();
                }
            })
        }


    });
</script>
@stop