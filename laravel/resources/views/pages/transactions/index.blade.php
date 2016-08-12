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

        <div class="transaction-list-actions">
            <div class="pull-left">
                <button type="button" class="btn btn-success btn-with-icon btn-trigger">Verify As Pass</button>
                <button type="button" class="btn btn-danger btn-with-icon btn-trigger">Verify As Fail</button>
                <button type="button" class="btn btn-default btn-with-icon btn-trigger">Verify As Skip</button>
                <button type="button" class="btn btn-danger btn-with-icon btn-delete" data-tooltip="tooltip" data-placement="top">Remove Selected</button>
            </div>
            <div class="pull-right">
                <div class="form-inline">
                    <div class="form-group">
                        <label for="paginationLimit">Display #</label>
                        <select class="form-control" id="paginationLimit" name="limit">
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="-1">All</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div id="log-result-table">

            @include('pages.transactions.transactions')

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

@stop

@section('page-scripts')
<script src="{{ getSiteUrl() }}/laravel/resources/assets/js/vendor/bootstrap-datepicker.min.js"></script>
<script>
    jQuery(document).ready(function($) {
        $('#filterCalendar').click(function () {
            $('#filterDate').datepicker('show');
        });


        $('body').on('click', '.btn-clear', function () {
            $('#filterBySpinner').show();

            var form = $('#filterByForm');

            $.ajax({
                    url: '/transactions/filters',
                    type: 'get',
                    data: {},
                    error: function (jqXHR, status) {
                    },
                    success: function (rsp) {
                        $('.transaction-filter-content').html(rsp.html);
                    },
                    complete: function () {
                        $('#filterBySpinner').hide();
                    }
                })

        });

        $('body').on('change', '#filterByForm .form-control', function () {
            $('#filterBySpinner').show();

            var form = $('#filterByForm');

            $.ajax({
                    url: '/transactions/filters',
                    type: 'get',
                    data: form.serialize(),
                    error: function (jqXHR, status) {
                    },
                    success: function (rsp) {
                        $('.transaction-filter-content').html(rsp.html);
                    },
                    complete: function () {
                        $('#filterBySpinner').hide();
                    }
                })

        });


        $('body').on('change', '.auditRecordCheckbox', function (e) {
            e.preventDefault();
            $('#filterBySpinner').show();

            var form = $('#filterByForm');

            $.ajax({
                    url: '/transactions/updateauditrecord',
                    type: 'get',
                    data: form.serialize(),
                    error: function (jqXHR, status) {
                    },
                    success: function (rsp) {
                        $('#log-result-table').html(rsp.html);
                    },
                    complete: function () {
                        $('#filterBySpinner').hide();
                    }
                })

        });

        $('body').on('submit', '#filterByForm', function (e) {
            e.preventDefault();
            $('#filterBySpinner').show();

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
                        $('#filterBySpinner').hide();
                    }
                })

        });

        //When open log, load transaction details
        $('body').on('show.bs.collapse','.logRow', function () {
            var transactionId = $(this).data('transactionId');
            var entry = $(this);

            if (!entry.data('loaded')){
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
        })

    });
</script>
@stop