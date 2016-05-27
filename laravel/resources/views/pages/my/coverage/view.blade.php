<div class="modal-header">
    <button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup" data-placement="left" data-dismiss="modal" aria-label="Close">Close</button>
    Test Case Details
</div>
<form action="/testplan/{{ $testPlan->id }}/exclude/{{ $testCase->ID }}" method="post" id="testCaseDetailsForm">
    <div class="modal-body">
        <ul class="coverage-test-details">
            <li>
                <label>Identifier:</label>
                <a href="/{{ $testCase->post_name }}" target="_blank">{{ $testCase->post_title }}</a>
            </li>
            <li>
                <label>Transaction Log:</label>
                <a href="/my-transaction-log?case={{ $testCase->ID }}&productu={{ $testPlan->product_id }}&suite={{ $testPlan->suite_id }}" target="_blank">View Audit Record</a>
            </li>
            <li>
                <label>Optional:</label>
                <strong>{{ \App\TestCase::isOptional($testCase->ID) ? 'Yes' : 'No' }}</strong>
            </li>
            <li>
                <label for="caseExclude">Exclude</label>
                <input type="checkbox" id="caseExclude" name="case_exclude" @if($isExcluded) checked="checked" @endif>
            </li>
            <li class="isExcluded">
                <label for="caseExcludeReason">Reason</label>
                <textarea cols="40" rows="2" class="form-control" id="caseExcludeReason" name="reason">@if($isExcluded) {{ $isExcluded['reason'] }}@endif</textarea>
            </li>
        </ul>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Submit</button>
        <button type="button" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
    </div>
    <div class="block-loading"><div class="loading-content"><span class="loader"></span><div class="loading-text">PROCESSING THE EXCLUSION</div><div class="loading-wait">Please wait...</div></div></div>
</form>