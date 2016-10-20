@extends('app')

@section('content')

    <div class="container main-container">

        <div class="main-content">
            <div class="transaction-filter">
                <div class="transaction-filter-title">Filter By:</div>
                <div class="transaction-filter-content block-loading-wrapper">

                    @include('pages.search.registry.filters')

                </div>
            </div>
            <div class="block-loading-wrapper">

                <div id="log-result-table">
                    @include('pages.search.registry.list')
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

    {{-- Delete Entry Modal--}}
    <div class="modal fade" id="deleteEntryModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document" style="width: 500px;">
            <div class="modal-content block-loading-wrapper">
                <div class="modal-header">
                    <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
                    Delete Search Entry
                </div>
                <div class="modal-body">
                    Are you sure you want delete selected entry?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success btn-with-icon btn-confirm confirm_delete_search_entry">Confirm</button>
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
    <input type="hidden" id="entry_id">
@stop

@section('page-scripts')
    <script src="{{ getSiteUrl() }}/laravel/resources/assets/js/vendor/bootstrap-datepicker.min.js"></script>
    <script>
        jQuery(document).ready(function ($) {
            $('body').on('click', '.filterCalendar1', function () {
                $('#filterDate1').datepicker('show');
            });

            $('body').on('click', '.filterCalendar2', function () {
                $('#filterDate2').datepicker('show');
            });

            $('body').on('click', '.delete_search_entry', function () {
                $('#entry_id').val($('.delete_search_entry').data('id'));
            });
            $('body').on('click', '.confirm_delete_search_entry', function (e) {

                jQuery('#deleteEntryModal .block-loading').show();

                jQuery.ajax({
                    url: '/products-and-services/' + $('#entry_id').val(),
                    type: 'delete',
                    dataType: 'json',
                    success: function (rsp) {
                        $('.modal').modal('hide');
                        $('#deleteEntryModal .block-loading').hide();
                        $('#filterByForm').submit();
                    },
                    error: function (jqXHR, status) {
                        jQuery('#deleteEntryModal .block-loading').hide();
                        $('#deleteEntryModal .modal-body').prepend('<div class="error-message">' + formatErrorMessage(jqXHR, status) + '</div>');
                        setTimeout(function () {
                            $('#deleteEntryModal .modal-body > .error-message').slideUp(function () {
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
            function getUrlVar(url, key) {
                var result = new RegExp(key + "=([^&]*)", "i").exec(url);
                return result && unescape(result[1]) || "";
            }

            $('body').on('click', '.pagination a', function (e) {
                e.preventDefault();
                $('#filterBySpinner, #loadLogResultsSpinner').show();
                var link = $(this);
                var form = $('#filterByForm');
                $.ajax({
                    url: '/products-and-services/logs-list',
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

            $('body').on('click', '.download-site', function (e) {
                e.preventDefault();
                location.href = '/products-and-services/download/?' + $('#filterByForm').serialize();
            });

            $('body').on('change', '#filterByForm .form-control', function () {
                $('#filterBySpinner').show();
                var form = $('#filterByForm');
                getTransactionFilters(form.serialize());
            });


            $('body').on('submit', '#filterByForm', function (e) {
                e.preventDefault();
                $('#filterBySpinner, #loadLogResultsSpinner').show();

                var form = $('#filterByForm');
                $.ajax({
                    url: '/products-and-services/logs-list',
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


            $('body').on('click', '#filterByForm .clear-filter', function (e) {
                $(this).parent().find('input, select').val('');
                var form = $('#filterByForm');
                getTransactionFilters(form.serialize());
            });

            /**
             * Load actual filters data
             * @param data Serialised form data
             */
            function getTransactionFilters(data) {
                $('#filterBySpinner').show();
                $.ajax({
                    url: '/products-and-services/filters',
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