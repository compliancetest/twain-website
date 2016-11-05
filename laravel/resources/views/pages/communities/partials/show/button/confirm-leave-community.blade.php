<!-- Confirm Membership Cancellation -->
<div class="modal fade" id="confirmCancelMembership{{ $community->slug }}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content block-loading-wrapper">
            <div class="modal-header">
                <button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup" data-placement="left" data-dismiss="modal" aria-label="Close">Close</button>
                Confirm Community Membership Cancellation
            </div>
            <div class="modal-body">
                This will cancel your membership of the {{ $community->title }} community. Are you sure?
            </div>
            <div class="modal-footer">
                <a class="btn btn-success btn-with-icon btn-confirm cancelMembershipInCommunity" data-community-id="{{ $community->slug }}">Confirm</a>
                <button type="button" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
            </div>
            <div class="block-loading">
                <div class="loading-content"><span class="loader"></span>
                    <div class="loading-text">Loading</div>
                    <div class="loading-wait">Please wait...</div>
                </div>
            </div>
        </div>
    </div>
</div>