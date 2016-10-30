@extends('app')

@section('content')
    <div class="container main-container">
        <div class="main-content">
            <div class="filter-box">
                <div class="filter-box-title">Filter By:</div>
                <div class="filter-box-content block-loading-wrapper">

                    <form action="#" method="get" id="filterByForm">
                        <div class="row">

                            <div class="form-group col-sm-12 col-md-6">
                                <label for="filterKeyword">Keyword:</label>
                                <input class="form-control" name="q" id="filterKeyword" type="text">
                            </div>

                            <div class="form-group col-sm-6 col-md-3">
                                <label for="filterType">Type:</label>
                                <select class="form-control" id="filterType" name="post_type">
                                    <option value="">- All -</option>
                                    <option value="Environment">Environment</option>
                                    <option value="Quality">Quality</option>
                                    <option value="Data Exchange">Data Exchange</option>
                                    <option value="Some Other Type">Some Other Type</option>
                                    <option value="Web Technology">Web Technology</option>
                                </select>
                            </div>

                            <div class="form-group col-sm-6 col-md-3">
                                <label for="filterIssuer">Issuer:</label>
                                <select class="form-control" id="filterIssuer" name="issuer">
                                    <option value="">- All -</option>
                                    <option value="Issuer">Issuer</option>
                                    <option value="No Issuer">No Issuer</option>
                                    <option value="Filter">Filter</option>
                                    <option value="twain.org">twain.org</option>
                                    <option value="New_issuer">New_issuer</option>
                                </select>
                            </div>

                            <div class="form-group col-sm-6 col-md-3">
                                <label for="filterStatus">Status:</label>
                                <select class="form-control" id="filterStatus" name="status">
                                    <option value="">- All -</option>
                                    <option value="Draft">Draft</option>
                                    <option value="Active">Active</option>
                                    <option value="Obsolete">Obsolete</option>
                                </select>
                            </div>

                            <div class="form-group col-sm-6 col-md-3">
                                <label for="filterCommunity">Community:</label>
                                <select class="form-control" id="filterCommunity" name="community_id">
                                    <option value="">- All -</option>
                                    <option value="0da38ec7-ae99-40dd-9508-29b7b0c3010e">Canceling membership test</option>
                                    <option value="56280740-13a8-4f1b-8e0e-cddb32b14c58">CountProfiles</option>
                                    <option value="2b631ece-4fa1-4277-96bf-6efe1a279893">Find</option>
                                    <option value="9bb9b4f4-bdd3-4553-af65-3d7c0ac00096">Invite</option>
                                    <option value="90c89d3b-ab7b-4aea-85eb-a501b370c4fe">Something</option>
                                    <option value="857d8b15-76bd-49d4-9e4b-b2d35aea7db1">TWAIN</option>
                                    <option value="6ef4f494-0460-4539-9a72-3161597e65f4">Test data</option>
                                    <option value="857d8b15-76bd-49d4-9e4b-b2d35aea7db1">Twain</option>
                                </select>
                            </div>

                            <div class="form-group col-sm-6 col-md-3">
                                <label for="filterDateFrom">Published Date:</label>
                                <div class="input-group">
                                    <input class="form-control datepicker-form-control" id="filterDateFrom" readonly="" data-provide="datepicker" data-date-autoclose="true" data-date-format="yyyy-mm-dd" name="date_from" placeholder="Date From" type="text">
                                    <span class="input-group-addon filterCalendarFrom"><span class="calendar-icon"></span></span>
                                </div>
                            </div>
                            <div class="form-group col-sm-6 col-md-3">
                                <label for="filterDateTo">&nbsp;</label>
                                <div class="input-group">
                                    <input class="form-control col-md-1 datepicker-form-control" id="filterDateTo" readonly="" data-provide="datepicker" data-date-autoclose="true" data-date-format="yyyy-mm-dd" name="date_to" placeholder="Date To" type="text">
                                    <span class="input-group-addon filterCalendarTo"><span class="calendar-icon"></span></span>
                                </div>
                            </div>
                        </div>

                        <div class="filter-box-footer">
                            <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Confirm</button>
                            &nbsp;&nbsp;
                            <button type="button" class="btn btn-default btn-with-icon btn-clear">Clear</button>
                        </div>
                    </form>

                    <div class="block-loading" id="filterBySpinner">
                        <div class="loading-content"><span class="loader"></span>
                            <div class="loading-text">LOADING FILTERS</div>
                            <div class="loading-wait">Please wait...</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="block-loading-wrapper">
                <div id="test-suites-search-results">
                    <div class="filter-list-actions">
                        <div class="col-md-9">
                            <div class="filter-results-count">
                                Showing <strong>1</strong> -
                                <strong>10</strong>
                                of <strong>32</strong> Results
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <div class="blue-colored-table-wrapper">

                            <table class="table blue-colored-table sort-table test-suites-table">
                            <thead>
                                <tr>
                                    <th><a href="#">Name <span class="glyphicon glyphicon-sort-by-attributes"></span></a></th>
                                    <th>Community</th>
                                    <th>Issuer</th>
                                    <th><a href="#">Published <span class="glyphicon glyphicon-sort"></span></a></th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><a href="#">111 v0.0</a></td>
                                    <td class="text-center"><a href="#">Twain</a></td>
                                    <td class="text-center">Issuer</td>
                                    <td class="text-center">2016-05-31</td>
                                    <td class="text-center"><span class="status status-draft">Draft</span></td>
                                </tr>
                                <tr>
                                    <td><a href="#">Scanning Operations – Data Sources v1.1</a></td>
                                    <td class="text-center"><a href="#">TWAIN</a></td>
                                    <td class="text-center">twain.org</td>
                                    <td class="text-center">2016-02-19</td>
                                    <td class="text-center"><span class="status status-active">Active</span></td>
                                </tr>
                            </tbody>
                        </table>

                        </div>

                    </div>

                    <div class="pagination-wrapper">
                        <div class="pagination-wrapper">
                            <ul class="pagination">
                                <li class="disabled"><span>«</span></li>
                                <li class="active"><span>1</span></li>
                                <li><a href="#">2</a></li>
                                <li><a href="#">3</a></li>
                                <li><a href="#">4</a></li>
                                <li><a href="#">5</a></li>
                                <li><a href="#">6</a></li>
                                <li><a href="#">7</a></li>
                                <li><a href="#">8</a></li>
                                <li class="disabled"><span>...</span></li>
                                <li><a href="#">27</a></li>
                                <li><a href="#">28</a></li>
                                <li><a href="#" rel="next">»</a></li>
                            </ul>
                        </div>
                    </div>

                </div>

                <div id="loadTestSuitesSearchResultsSpinner" class="block-loading">
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
        jQuery(document).ready(function ($) {
            $('body').on('click', '.filterCalendarFrom', function () {
                $('#filterDateFrom').datepicker('show');
            });

            $('body').on('click', '.filterCalendarTo', function () {
                $('#filterDateTo').datepicker('show');
            });

            $('body').on('click', '.sortby', function (e) {
                e.preventDefault();
                $('#orderby').val($(this).data('type'));
                $('#order').val($(this).data('order'));
                $('#filterByForm').submit();
            });

            $('body').on('click', '.pagination a', function (e) {
                e.preventDefault();
                $('#filterBySpinner, #loadTestSuitesSearchResultsSpinner').show();
                var link = $(this);
                var form = $('#filterByForm');
                $.ajax({
                    url: '/search-results/logs-list',
                    type: 'get',
                    data: form.serialize() + "&page=" + getUrlVar(link.attr('href'), 'page'),
                    error: function (jqXHR, status) {
                    },
                    success: function (rsp) {
                        $('#test-suites-search-results').html(rsp.html);
                    },
                    complete: function () {
                        $('#filterBySpinner, #loadTestSuitesSearchResultsSpinner').hide();
                    }
                });
            });

            $('body').on('click', '.btn-clear', function () {
                $('#filterByForm')[0].reset();
                getBoxFilters('', '/search-results/filters');
            });

            $('body').on('change', '#filterByForm .form-control', function () {
                $('#filterBySpinner').show();
                var form = $('#filterByForm');
                getBoxFilters(form.serialize(), '/search-results/filters');
            });


            $('body').on('submit', '#filterByForm', function (e) {
                e.preventDefault();
                $('#filterBySpinner, #loadTestSuitesSearchResultsSpinner').show();

                var form = $('#filterByForm');
                $.ajax({
                    url: '/search-results/logs-list',
                    type: 'get',
                    data: form.serialize(),
                    error: function (jqXHR, status) {
                    },
                    success: function (rsp) {
                        $('#test-suites-search-results').html(rsp.html);
                    },
                    complete: function () {
                        $('#filterBySpinner, #loadTestSuitesSearchResultsSpinner').hide();
                    }
                })
            });


            $('body').on('click', '#filterByForm .clear-filter', function (e) {
                $(this).parent().find('input, select').val('');
                var form = $('#filterByForm');
                getBoxFilters(form.serialize(), '/search-results/filters');
            });

        });
    </script>
@stop