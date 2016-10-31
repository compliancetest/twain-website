<!--todo-migration delete claim modal doesnt show-->
{{-- Delete Product's Claim Modal--}}
<div class="modal fade" id="deleteClaimModal{{ $id }}" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content block-loading-wrapper">
            <div class="modal-header">
                <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
                Confirm Deletion
            </div>
            <div class="modal-body">
                Are you sure that you want to delete this compliance claim?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-with-icon btn-confirm deleteProduct">Confirm</button>
                <button class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
            </div>
            <div class="block-loading" id="removingProductClaimSpinner{{ $id }}">
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
            $('.deleteProductClaim').click(function () {
                $('#removingProductClaimSpinner{{ $id }}').show();
                $.ajax({
                    url: '/laravel-product/{{ $product->slug }}/claim/{{ $id }}',
                    type: 'DELETE',
                    success: function () {
                        <!--todo-migration append notification to claims table and delete claim row from claims list-->
                    },
                    error: function (jqXHR, status) {
                        alert(formatErrorMessage(jqXHR, status));
                    }
                });
            });
        });
    </script>
@stop