{{-- Delete Product's Claim Modal--}}
<div class="modal fade delete-product-claim-modal" id="deleteProductClaimModal{{ $id }}" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
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
                <button type="button" class="btn btn-success btn-with-icon btn-confirm deleteProductClaimConfirm" data-product-slug="{{ $productSlug }}" data-claim-id="{{ $id }}">Confirm</button>
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