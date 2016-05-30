<div class="modal-header">
    <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
    Edit Test Plan
</div>
<form action="/testplan/{{ $testPlan->id }}" id="coverageEditPlanForm" method="post">
    <div class="modal-body">

        <div class="form-group">
            <label for="availableProducts">Product</label>
            <select name="product_id" id="availableProducts" class="form-control">
                <option value="">Select a Product</option>
                @foreach($products as $product)
                    <option value="{{ $product->ID }}" @if($product->ID == $testPlan->product_id) selected="selected" @endif>{{ $product->post_title }}</option>
                @endforeach
            </select>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <h4 class="edit-plan-subheader">Level</h4>

                @foreach($levels as $level)
                    <div class="radio">
                        <label>
                            <input type="radio" name="level" class="level" value="{{ $level }}" @if($level == $testPlan->level) checked="checked" @endif>
                            {{ $level }}
                        </label>
                    </div>
                @endforeach

            </div>
            <div class="col-sm-6">
                <h4 class="edit-plan-subheader">Role</h4>

                @foreach($roles as $role)
                    <div class="radio">
                        <label>
                            <input type="radio" name="role" class="role" value="{{ $role }}" @if(str_replace(' ', '', $role) == str_replace(' ', '', $testPlan->role)) checked="checked" @endif>
                            {{ $role }}
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Confirm</button>
        <button type="button" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
    </div>
    <div class="block-loading"><div class="loading-content"><span class="loader"></span><div class="loading-text">SAVING DATA</div><div class="loading-wait">Please wait...</div></div></div>
</form>