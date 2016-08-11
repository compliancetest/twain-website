<div class="modal-header">
    <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
    Accept A Verify Request
</div>
<form action="/verify-requests/{{ $testSuiteId }}/accept/{{ $verifyRequest->id }}" id="acceptVerifyRequestForm" method="post">
    <div class="modal-body">

        <div class="form-group">
            <div class="col-sm-12">
                <input type="checkbox" id="confirmRequest">
                <label for="corfirmRequest">Please confirm that you want accept this Verify Request</label>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Confirm</button>
        <button type="button" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
    </div>
    <div class="block-loading">
        <div class="loading-content"><span class="loader"></span>
            <div class="loading-text">SAVING DATA</div>
            <div class="loading-wait">Please wait...</div>
        </div>
    </div>
</form>

<script>
    Page.verifyRequest.validateAcceptVerifyRequestDetailsForm();
</script>