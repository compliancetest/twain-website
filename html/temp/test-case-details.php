<?php

sleep(2);

?>

<div class="modal-header">
    <button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup" data-placement="left" data-dismiss="modal" aria-label="Close">Close</button>
    Test Case Details
</div>
<form action="http://twain.lc/html/temp/test-case-details-submit.php" method="post" id="testCaseDetailsForm">
    <div class="modal-body">
        <ul class="coverage-test-details">
            <li>
                <label>Identifier:</label>
                <a href="http://compliancetest.lc/ss-ctr-05a-v1-1-1/" target="_blank">SS-CTR-05a v1.1.1 </a>
            </li>
            <li>
                <label>Transaction Log:</label>
                <a href="http://compliancetest.lc/my-transaction-log?case=4771" target="_blank">View Audit Record</a>
            </li>
            <li>
                <label>Optional:</label>
                <strong>No</strong>
            </li>
            <li>
                <label for="caseExclude">Exclude</label>
                <input type="checkbox" id="caseExclude" name="case_exclude">
            </li>
            <li class="isExcluded">
                <label for="caseExcludeReason">Reason</label>
                <textarea cols="40" rows="2" class="form-control" id="caseExcludeReason" name="case_exclude_reason"></textarea>
            </li>
        </ul>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success btn-with-icon btn-confirm isExcluded">Submit</button>
        <button type="button" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
    </div>
    <div class="block-loading"><div class="loading-content"><span class="loader"></span><div class="loading-text">PROCESSING THE EXCLUSION</div><div class="loading-wait">Please wait...</div></div></div>
</form>