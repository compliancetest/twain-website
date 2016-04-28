<?php

sleep(2);

?>

<div class="modal-header">
    <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
    Test Plan Form
</div>
<form action="temp/ajax.php" id="coverageEditPlanForm">
    <div class="modal-body">
        <div class="form-group">
            <label for="availableProducts">Product</label>
            <select name="product_id" id="availableProducts" class="form-control">
                <option value="">Select a Product</option>
                <option value="1">Product 1</option>
                <option value="2">Product 2</option>
            </select>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <h4 class="edit-plan-subheader">Level</h4>
                <div class="radio">
                    <label>
                        <input type="radio" name="level[]" class="level" value="B" checked>
                        B
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="level[]" class="level" value="A">
                        A
                    </label>
                </div>
                <div class="radio disabled">
                    <label>
                        <input type="radio" name="level[]" class="level" value="AA">
                        AA
                    </label>
                </div>
            </div>
            <div class="col-sm-6">
                <h4 class="edit-plan-subheader">Role</h4>
                <div class="radio">
                    <label>
                        <input type="radio" name="role[]" class="role" value="Fund" checked>
                        Fund
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="role[]" class="role" value="SMSF">
                        SMSF
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Confirm</button>
        <button type="button" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
    </div>
    <div class="block-loading"><div class="loading-content"><span class="loader"></span><div class="loading-text">SAVING DATA</div><div class="loading-wait">Please wait...</div></div></div>
</form>