@extends('app')

@section('content')

    <div class="container main-container">

        @include('pages.user-tabs', ['tab' => 'api-logs'])

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