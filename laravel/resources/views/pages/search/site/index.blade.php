@extends('app')

@section('content')

    <div class="container main-container">

        <div class="main-content">
            <div class="filter-box">
                <div class="filter-box-title">Filter By:</div>
                <div id="siteSearchFilterContent" class="filter-box-content block-loading-wrapper">

                    @include('pages.search.site.filters')

                </div>
            </div>
            <div class="block-loading-wrapper">

                <div id="siteSearchResultsTable">
                    @include('pages.search.site.list')
                </div>

                <div id="siteSearchResultsSpinner" class="block-loading">
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
            $('body').on('click', '.delete_search_entry', function(){
                $('#entry_id').val($('.delete_search_entry').data('id'));
            });
            $('body').on('click', '.confirm_delete_search_entry', function(e){

                jQuery('#deleteEntryModal .block-loading').show();

                jQuery.ajax({
                    url: '/search-results/' + $('#entry_id').val(),
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

            Page.ajaxSearchForm.init();
            Page.ajaxSearchForm.initSorting();
            Page.ajaxSearchForm.disableFormSubmit();

            $('body').on('click', '.download-site', function (e) {
                e.preventDefault();
                location.href = '/search-results/download/?' + $('#siteSearchFilterByForm').serialize();
            });

        });
    </script>
@stop