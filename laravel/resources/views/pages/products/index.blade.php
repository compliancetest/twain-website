@extends('app')

@section('content')
    <div class="container main-container">
        <div class="container main-container">

            @include('pages.user-tabs', ['tab' => 'my-products'])

            <div class="main-content products-list">

                @include('pages.products.partials.list', ['products' => $applicationProducts, 'productType' => 'Application'])
                @include('pages.products.partials.list', ['products' => $dataSourceProducts, 'productType' => 'DataSource'])

            </div>
        </div>
    </div>
@stop

@section('page-scripts')
<script>
    jQuery(document).ready(function ($) {
        $('.deleteProduct').click(function () {

            var productIndex = $(this).data('product-index');
            var productSlug = $(this).data('product-slug');

            $('#removingProductSpinner' + productIndex).show();
            $.ajax({
                url: '/product/' + productSlug,
                type: 'DELETE',
                success: function () {
                    window.location = "/my-products";
                },
                error: function (jqXHR, status) {
                    alert(formatErrorMessage(jqXHR, status));
                }
            });
        });

        $('.deleteProductClaimConfirm').click(function () {
            var claimId = $(this).data('claim-id');
            var productSlug = $(this).data('product-slug');
            var claimRow =  $('#claimRow' + claimId);
            var claimContainer = claimRow.parents('.colored-box-content');
            var claimSpinner = $('#removingProductClaimSpinner' + claimId);

            claimSpinner.show();

            $.ajax({
                url: '/product/' + productSlug +'/claim/' + claimId,
                type: 'DELETE',
                success: function () {
                    $('#deleteProductClaimModal' + claimId).modal('hide');
                    claimSpinner.hide();
                    claimRow.addClass('removing').fadeTo("slow", 0.3, function () {
                        $(this).remove();
                        claimContainer.prepend('<div class="success-message">Claim {{ $id }} was deleted successfully.</div>');
                        setTimeout(function () {
                            claimContainer.find('.success-message').slideUp(function () {
                                $(this).remove();
                            });
                        }, 2000);
                    });
                },
                error: function (jqXHR, status) {
                    alert(formatErrorMessage(jqXHR, status));
                }
            });
        });
    });
</script>
@stop