@extends('app')

@section('content')

    <div class="container main-container">

        @include('pages.user-tabs', ['tab' => 'test-outcome-logs'])

        <div class="main-content">
            <div class="filter-box">
                <div class="filter-box-title">Filter By:</div>
                <div id="outcomeLogSearchFilterContent" class="filter-box-content block-loading-wrapper">

                    @include('pages.transactions-change-logs.filters')

                </div>
            </div>
            <br/>
            <div class="block-loading-wrapper">

                <div id="outcomeLogSearchResultsTable">
                    @include('pages.transactions-change-logs.logs')
                </div>

                <div id="outcomeLogSearchResultsSpinner" class="block-loading">
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
            Page.ajaxSearchForm.disableFormSubmit();

        });
    </script>
@stop