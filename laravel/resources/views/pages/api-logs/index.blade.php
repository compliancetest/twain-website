@extends('app')

@section('content')

    <div class="container main-container">

        <div class="tabs-menu">
            <ul>
                @if(is_organisation_admin())
                    <li class="organisation-tab"><a href="/my-organisation/" data-tooltip="tooltip" title="My Organization">Organization</a></li>
                @endif

                <li class="communities-tab"><a data-tooltip="tooltip" href="/my-communities/" title="My community memberships">Communities</a></li>
                <li class="test-suites-tab"><a href="/my-test-suites/" data-tooltip="tooltip" title="My test suite subscriptions">Test Suites</a></li>
                <li class="products-tab"><a href="/my-products/" data-tooltip="tooltip" title="My products under test">Products</a></li>
                <li class="coverage-tab"><a href="/test-suite-coverage/" data-tooltip="tooltip" title="Completeness of my testing">Coverage</a></li>
                <li class="coverage-tab"><a href="/verify-requests/" data-tooltip="tooltip" title="My Verify Test Results Requests">Verify Requests</a></li>
                <li class="transactions-tab"><a href="/my-transaction-log/" data-tooltip="tooltip" title="My Test Results">Test Results</a></li>
                <li class="support-tab"><a href="/my-support-tickets/" data-tooltip="tooltip" title="My support tickets">Support</a></li>
                <li class="profile-tab"><a href="/my-profile/" data-tooltip="tooltip" title="My profile">Profile</a></li>
                <li class="transactions-tab"><a href="/api-logs/" class="active" data-tooltip="tooltip" title="ApiLogs">ApiLogs</a></li>
                <li class="transactions-tab"><a href="/test-outcome-logs/" data-tooltip="tooltip" title="Outcome Logs">Outcome Logs</a></li>

            </ul>
        </div>

        <div class="main-content">
            <div class="filter-box">
                <div class="filter-box-title">Filter By:</div>
                <div id="apiLogSearchFilterContent" class="filter-box-content block-loading-wrapper">

                    @include('pages.api-logs.filters')

                </div>
            </div>
            <br/>
            <div class="block-loading-wrapper">

                <div id="apiLogSearchResultsTable">
                    @include('pages.api-logs.logs')
                </div>

                <div id="apiLogSearchResultsSpinner" class="block-loading">
                    <div class="loading-content"><span class="loader"></span>
                        <div class="loading-text">LOADING DATA</div>
                        <div class="loading-wait">Please wait...</div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    {{-- View ApiLog Modal--}}
    <div class="modal fade" id="viewLogModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document" style="width: 900px;">
            <div class="modal-content block-loading-wrapper">
                <div class="block-loading">
                    <div class="loading-content"><span class="loader"></span>

                        <div class="loading-text">LOADING DATA</div>
                        <div class="loading-wait">Please wait...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop

@section('page-scripts')
    <script src="{{ getSiteUrl() }}/laravel/resources/assets/js/vendor/bootstrap-datepicker.min.js"></script>
    <script>
        jQuery(document).ready(function ($) {

            //OnClose Message Data popup remove data and replace it to the loader
            $('#viewLogModal').on('hidden.bs.modal', function (e) {
                var popupLoadingBlock = '<div class="modal-header">' +
                        '<button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup" data-placement="left" data-dismiss="modal" aria-label="Close">Close</button>' +
                        'Api Logs' +
                        '</div>' +
                        '<div class="modal-body">' +
                        '<div class="block-loading"><div class="loading-content"><span class="loader"></span><div class="loading-text">LOADING DATA</div><div class="loading-wait">Please wait...</div></div></div>' +
                        '</div>';
                $(this).find('.modal-content').html(popupLoadingBlock);
            });

            Page.ajaxSearchForm.init();
        });
    </script>
@stop