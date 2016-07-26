<div class="modal-header">
    <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
    Add A Verify Request
</div>
<form action="/verify-requests/" id="createVerifyRequestForm" method="post">
    <div class="modal-body">

        <input type="hidden" name="suite_id" id="suiteId" value="{{ $testSuiteId }}">

        <div class="form-group">
            <div class="col-sm-6">
                <label for="availableProducts">Product</label>
                <select name="product_id" id="availableProducts" class="form-control">
                    <option value="">Select a Product</option>
                    @foreach($products as $product)
                        <option @if($product->ID == $selectedProductId) selected="selected" @endif value="{{ $product->ID }}">{{ $product->post_title . ' V' . $product->getMetaByKey('product_version')}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6">
                <label for="testPlan">Test Plan</label>
                <select name="test_plan" id="testPlanId" class="form-control">
                    <option value="">Select Test Plan</option>
                    @foreach($testPlans as $testPlan)
                        <option value="{{ $testPlan->id }}" @if($testPlan->id == $selectedTestPlanId) selected="selected" @endif>{{ $testPlan->level }}</option>
                    @endforeach
                </select>

            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                @if($transactions)
                    <div class="table-responsive">
                        <table class="table colored-table" style="margin-top: 20px;">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Transaction ID</th>
                                    <th>Execution ID</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $testCase => $caseTransactions)
                                        <tr>
                                            <td colspan="4" class="caseIdList" data-id="{!! $testCase !!}"> {{ \App\Post::find($testCase)->post_title }}</td>
                                        </tr>
                                        @foreach($caseTransactions as $transaction)
                                            <tr>
                                                @if(count($caseTransactions) > 1)
                                                    <td><input type="checkbox" name="transaction" class="transaction" value="{{ $transaction->id }}" data-case="{{ $testCase }}"></td>
                                                @else
                                                    <td><input type="checkbox" checked="checked" disabled="disabled" name="transaction" class="transaction" value="{{ $transaction->id }}" data-case="{{ $testCase }}"></td>
                                                @endif

                                                <td class="text-center">{{ $transaction->id }}</td>
                                                <td class="text-center">
                                                    @if($transaction->s3_link)
                                                        <a href="{!! $transaction->s3_link !!}" target="_blank"> {!! $transaction->execution_id !!} </a>
                                                    @else
                                                        {!! $transaction->execution_id !!}
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ formatDate($transaction->created_at, 'Y-m-d H:i:s') }}</td>
                                            </tr>
                                        @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Confirm</button>
        <button type="button" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
    </div>
    <div class="block-loading"><div class="loading-content"><span class="loader"></span><div class="loading-text">LOADING DATA</div><div class="loading-wait">Please wait...</div></div></div>
</form>

<script>
     Page.verifyRequest.updateVerifyRequestDetailsForm();
     Page.verifyRequest.validateVerifyRequestDetailsForm();
     Page.verifyRequest.selectTransactionWithMultipleValues();
</script>