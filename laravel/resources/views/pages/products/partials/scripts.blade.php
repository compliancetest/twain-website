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
                        $('#removingProductSpinner' + productIndex).hide();
                        $('#deleteProductModal' + productIndex).find('.modal-body').append('<div class="message error-message">' + formatErrorMessage(jqXHR, status) + '</div>');
                    }
                });
            });

            $('.deleteProductClaimConfirm').click(function () {
                var claimId = $(this).data('claim-id');
                var productSlug = $(this).data('product-slug');
                var claimRow = $('#claimRow' + claimId);
                var claimContainer = claimRow.closest('.colored-box-content');
                var claimSpinner = $('#removingProductClaimSpinner' + claimId);

                claimSpinner.show();

                $.ajax({
                    url: '/product/' + productSlug + '/claim/' + claimId,
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
                        claimSpinner.hide();
                        $('#deleteProductClaimModal' + claimId).find('.modal-body').append('<div class="message error-message">' + formatErrorMessage(jqXHR, status) + '</div>');
                    }
                });
            });
        });
    </script>
@stop