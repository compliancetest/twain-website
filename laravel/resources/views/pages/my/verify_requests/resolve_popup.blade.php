<div class="modal-header">
    <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
    Resolve Verify Request
</div>
<form action="/verify-requests/{{ $communityId }}/resolve/{{ $verifyRequest->id }}" id="resolveVerifyRequestForm" method="post">
    <div class="modal-body">

        <div class="table-responsive">
            <table class="table colored-table" style="margin-top: 20px;">
                <thead>
                <tr>
                    <th>Test Case ID</th>
                    <th>Transaction ID</th>
                    <th>Execution ID</th>
                    <th>Date</th>
                </tr>
                </thead>
                <tbody>
                @foreach($transactions as $transaction)
                    <?php $testCase = \App\Post::find($transaction->test_case_id);?>
                    <tr>
                        <td class="text-center">{{ $testCase->post_title }}</td>
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
                </tbody>
            </table>
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
    Page.verifyRequest.validateResolveVerifyRequestDetailsForm();
</script>