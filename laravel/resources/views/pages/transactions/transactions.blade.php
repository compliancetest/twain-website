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
                        Organization<br/>
                        Subscription Nickname<br/>
                    @endif
                    Execution ID
                </th>
                <th>Date<br/>Time</th>
                <th>Action</th>
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
                            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true" data-toggle="tooltip" title="This test result is used in claim record and can't be deleted"></span>
                        @else
                            <input type="checkbox" name="id[]" id="id_{{ $transaction->id }}" value="{{ $transaction->id }}" class="checkTransaction"
                               @if($transaction->audit_record || $transactionUsedInClaims) disabled="disabled" @endif>
                        @endif
                    </td>
                    <td class="product-name">
                        <a data-toggle="collapse" class="loadLog product-collapse-link collapsed" href="#product-{{ $transaction->id }}"><span class="collapse-icon"></span></a>
                        <a href="/product/{{ $product->post_name }}" class="product-name-link" target="_blank">{{ $product->getProductFullName() }}</a>
                    </td>
                    <td class="text-center">
                        <a href="/test-suite/{{ $testSuite->post_name }}/" target="_blank">{{ $testSuite->post_title }}</a>
                        <br/>
                        <a href="/test-case/{{ $testCase->post_name }}/?test_suite_id={{ $testSuite->ID }}" target="_blank">{{ $testCase->post_title }}</a>
                    </td>
                    <td>
                        <a data-toggle="collapse" class="loadLog collapsed" href="#product-{{ $transaction->id }}"><span class="collapse-icon"></span></a>
                        @if(!empty($transaction->reason) && $transaction->test_outcome_status_id != \App\TestOutcomeStatus::getIdByCode('PASS'))
                            <a href="/testingdetails/{{ $transaction->id }}/transaction-reason" data-toggle="modal" data-remote="true" data-ajax-modal data-target="#viewReasonModal"
                               data-tooltip="tooltip" title="Reason" class="text-status-{{ $status }}">
                                {{ $outcomeStatus->name }}
                            </a>
                        @else
                            <span class="text-status-{{ $status }}">{{ $outcomeStatus->name }}</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <input type="checkbox" @if($transaction->audit_record) checked="checked" @endif class="auditRecordCheckbox"
                               @if(\App\TestOutcomeStatus::find($transaction->test_outcome_status_id)->code == 'PENDING') disabled="disabled" @endif
                               data-id="{{ $transaction->id }}">
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
                        {{ formatDate($transaction->created_at, 'Y-m-d') }}
                        <br>
                        {{ formatDate($transaction->created_at, 'H:i:s') }}
                    </td>
                    <td class="text-center">
                        @if($explainRequestsEnabled)
                            <?php $imageClass = count($eloquentTransaction->explanationLogs) > 0 ? 'btn-success' : 'btn-default';?>
                            <span class="tooltip-wrapper" data-toggle="tooltip" data-trigger="hover" data-container="body" title="Request Status Explanation">
                                <a class="btn {{ $imageClass }} btn-icon btn-question" href="/transactions/{{ $transaction->id }}/explanation-logs" data-toggle="modal" data-remote="true" data-ajax-modal data-target="#viewExplanationLogs">Request Status Explanation</a>
                            </span>
                        @endif
                        @if(isImageViewerEnabled())
                            <span class="tooltip-wrapper" data-toggle="tooltip" data-trigger="hover" data-container="body" title="Image Viewer">
                                <a class="btn btn-primary btn-icon btn-view showImageViewer" href="/verify-requests/{{ \App\Community::find(\App\Post::find($transaction->test_suite_id)->getMetaByKey('community_id'))->slug }}/transactions-image-viewer/{{ $transaction->id }}" data-toggle="modal" data-remote="true" data-ajax-modal data-target="#viewImagesModal">View Images</a>
                            </span>
                        @endif
                    </td>
                </tr>

                <tr id="product-{{ $transaction->id }}" data-transaction-id="{{ $transaction->id }}" class="logRow collapse">
                    <td colspan="8">
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