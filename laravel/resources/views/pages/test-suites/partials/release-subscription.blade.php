<div class="success-message lg">
    You have currently been allocated a subscription to this test suite.
    If you want to release this subscription, click <a href="#confirmReleaseSubscriptionModal" data-toggle="modal">here</a>.
</div>
{{-- Access Test Harness Modal--}}
<div class="modal fade" id="confirmReleaseSubscriptionModal" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content block-loading-wrapper">
            <form action="#" method="get" id="confirmReleaseSubscriptionForm">
                <div class="modal-header">
                    <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
                    Confirm Subscription Release
                </div>
                <div class="modal-body">
                    Are you sure that you want to release this subscription?
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Confirm</button>
                    <button class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
                </div>
                <div class="block-loading" id="confirmReleaseSubscriptionSpinner">
                    <div class="loading-content">
                        <span class="loader"></span>
                        <div class="loading-text">UNSUBSCRIBING</div>
                        <div class="loading-wait">Please wait...</div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>