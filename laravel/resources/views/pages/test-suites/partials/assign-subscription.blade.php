<a href="#accessTestHarnessModal" data-toggle="modal" class="btn btn-danger btn-subscription"><strong>ACCESS</strong><span class="subline">Test Harness</span> <span class="glyphicon glyphicon-triangle-right"></span></a>
{{-- Access Test Harness Modal--}}
<div class="modal fade" id="accessTestHarnessModal" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content block-loading-wrapper">
            <form action="#" method="get" id="confirmSubscriptionForm">
                <div class="modal-header">
                    <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
                    Confirm Subscription
                </div>
                <div class="modal-body">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="agree_terms" id="agreeCustomerTerms" value="agree"> I agree with the <a href="/customer-tc" target="_blank">Terms &amp; Conditions</a>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Confirm</button>
                    <button class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
                </div>
                <div class="block-loading" id="confirmSubscriptionFormSpinner">
                    <div class="loading-content">
                        <span class="loader"></span>
                        <div class="loading-text">SUBMITTING REQUEST</div>
                        <div class="loading-wait">Please wait...</div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>