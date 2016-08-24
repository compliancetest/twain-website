<div class="table-responsive">
    <div class="log-results-table-wrapper">
        <table class="table colored-parent-table log-results-table">
            <thead>
            <tr>
                <th class="text-center"><input type="checkbox" class="checkAll"></th>
                <th class="text-left">Product Name</th>
                <th>Test Suite<br/>Test Case</th>
                <th>Test<br/>Outcome</th>
                <th>Audit<br/>Record</th>
                <th>
                    @if($supportOrAdmin)
                        Organisation<br/>
                        Subscription Nickname<br/>
                    @endif
                    Execution ID
                </th>
                <th>Date<br/>Time</th>
            </tr>
            </thead>
            <tbody>
            @foreach($transactions AS $transaction)
                <?php
                $eloquentTransaction = \App\Transaction::find($transaction->id);
                $product = \App\Post::find($transaction->product_id);
                $testCase = \App\Post::find($transaction->test_case_id);
                $testSuite = \App\Post::find($transaction->test_suite_id);
                $outcomeStatus = \App\TestOutcomeStatus::find($transaction->test_outcome_status_id);
                $status = getOutcomeStatusClass($outcomeStatus->code);
                if($supportOrAdmin){
                    $subscription = \App\OrganisationSubscription::find($transaction->subscription_id);
                    $organisation = \App\Organisation::find($subscription->organisation_id);
                }
                $transactionUsedInClaims = !$eloquentTransaction->usedInClaims->isEmpty();
                ?>
                <tr>
                    <td class="text-center">
                        @if($transactionUsedInClaims)
                            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true" data-toggle="tooltip" title="This transaction is used in claim record and can't be deleted"></span>
                        @else
                            <input type="checkbox" name="id[]" id="id_{{ $transaction->id }}" value="{{ $transaction->id }}" class="checkTransaction"
                               @if($transaction->audit_record || $transactionUsedInClaims) disabled="disabled" @endif>
                        @endif
                    </td>
                    <td class="product-name">
                        <a data-toggle="collapse" class="loadLog product-collapse-link collapsed" href="#product-{{ $transaction->id }}"><span class="collapse-icon"></span></a>
                        <a href="/product/{{ $product->post_name }}" class="product-name-link" target="_blank">{{ $product->post_title . ' v' . $product->getMetaByKey('product_version') }}</a>
                    </td>
                    <td class="text-center">
                        <a href="/test-suite/{{ $testSuite->post_name }}/" target="_blank">{{ $testSuite->post_title }}</a>
                        <br/>
                        <a href="/test-case/{{ $testCase->post_name }}/" target="_blank">{{ $testCase->post_title }}</a>
                    </td>
                    <td>
                        <a data-toggle="collapse" class="loadLog collapsed" href="#product-{{ $transaction->id }}"><span class="collapse-icon"></span></a>
                                        <span class="text-status-{{ $status }}">
                                            @if(!empty($transaction->reason) && $transaction->test_outcome_status_id != \App\TestOutcomeStatus::getIdByCode('PASS'))
                                                <a href="/testingdetails/{{ $transaction->id }}/transaction-reason" data-toggle="modal" data-remote="true" data-ajax-modal data-target="#viewReasonModal"
                                                   data-tooltip="tooltip" title="Reason">
                                                    {{ $outcomeStatus->name }}
                                                </a>
                                            @else
                                                {{ $outcomeStatus->name }}
                                            @endif
                                        </span>
                    </td>
                    <td class="text-center">
                        <input type="checkbox" @if($transaction->audit_record) checked="checked" @endif class="auditRecordCheckbox" data-id="{{ $transaction->id }}">
                    </td>
                    <td class="text-center">
                        @if($supportOrAdmin)
                            {{ $organisation->organisation_name }}
                            <br/>
                            {{ $subscription->nickname }}
                            <br/>
                        @endif
                        @if(!empty($transaction->s3_link))
                            <a href="{{ $transaction->s3_link }}" target="_blank">{{ $transaction->execution_id }}</a>
                        @else
                            {{ $transaction->execution_id }}
                        @endif
                    </td>
                    <td class="text-center">
                        {{ formatDate($transaction->updated_at, 'Y-m-d') }}
                        <br>
                        {{ formatDate($transaction->updated_at, 'H:i:s') }}
                    </td>
                </tr>

                <tr id="product-{{ $transaction->id }}" data-transaction-id="{{ $transaction->id }}" class="logRow collapse">
                    <td colspan="7">
                        <div class="block-loading-wrapper">
                            <div class="block-loading loading-shown">
                                <div class="loading-content"><span class="loader"></span>
                                    <div class="loading-text">LOADING</div>
                                    <div class="loading-wait">Please wait...</div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="pagination-wrapper">
    {{ $transactions->appends($_GET)->render() }}
</div>

<script>
    $('[data-toggle="tooltip"]').tooltip();
</script>