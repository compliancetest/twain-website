@extends('app')

@section('content')
    <div class="container main-container">
        <div class="main-content">
            <div class="filter-box">
                <div class="filter-box-title">Filter By:</div>
                <div id="productsSearchFilterContent" class="filter-box-content block-loading-wrapper">

                    @include('pages.search.test-suites.filters')

                </div>
            </div>

            <div class="block-loading-wrapper">
                <div id="productsSearchResultsTable">

                    @include('pages.search.test-suites.list')

                </div>

                <div id="productsSearchResultsSpinner" class="block-loading">
                    <div class="loading-content">
                        <span class="loader"></span>
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
        Page.ajaxSearchForm.init();
        Page.ajaxSearchForm.initSorting();
        Page.ajaxSearchForm.disableFormSubmit();
    </script>
@stop