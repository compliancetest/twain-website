<div class="modal-header">
    <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
    Assign A Verify Request
</div>
<form action="/verify-requests/{{ $testSuiteId }}/assign/{{ $verifyRequest->id }}" id="assignVerifyRequestForm" method="post">
    <div class="modal-body">

        <div class="form-group">
            @if($verifyRequest->assignee_id)
                <div class="alert alert-danger">
                    This Verify Request already assigned
                </div>
            @endif

            <div class="col-sm-12">
                <label for="availableProducts">Support Users</label>
                <select name="user_id" id="availableSupportUsers" class="form-control">
                    <option value="">Select a Support User</option>
                    @foreach($moderators as $moderator)
                        <option @if($moderator->user_id == $verifyRequest->assignee_id) selected="selected"
                                @endif value="{{ $moderator->user_id }}">{{ cp_get_user_fullname($moderator->user_id) }}</option>
                    @endforeach
                </select>
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
    Page.verifyRequest.validateAssignVerifyRequestDetailsForm();
</script>