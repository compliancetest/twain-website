{{-- Delete Product Modal--}}
<div class="modal fade" id="deleteProductModal{{ $k }}" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content block-loading-wrapper">
            <div class="modal-header">
                <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
                Delete Product
            </div>
            <div class="modal-body">
                Are you sure you want delete {{ $product->full_name }}?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-with-icon btn-confirm deleteProduct">Confirm</button>
                <button class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
            </div>
            <div class="block-loading" id="removingProductSpinner{{ $k }}">
                <div class="loading-content"><span class="loader"></span>
                    <div class="loading-text">DELETING</div>
                    <div class="loading-wait">Please wait...</div>
                </div>
            </div>
        </div>
    </div>
</div>
@section('page-scripts')
    <script>
        jQuery(document).ready(function ($) {
            $('.deleteProduct').click(function () {
                <!--todo-migration spinner doent work on /laravel-my-products page-->
                $('#removingProductSpinner{{ $k }}').show();
                $.ajax({
                    url: '/laravel-product/{{ $product->slug }}',
                    type: 'DELETE',
                    success: function () {
                        window.location = "/my-products";
                    },
                    error: function (jqXHR, status) {
                        alert(formatErrorMessage(jqXHR, status));
                    }
                });
            });
        });
    </script>
@stop