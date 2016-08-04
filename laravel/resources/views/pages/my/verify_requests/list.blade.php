@foreach($userSuites as $userSuite)
    <div class="colored-box">
        <div class="colored-box-header"><a href="/test-suite/{{ $userSuite['testSuite']->post_name }}/">{{ $userSuite['testSuite']->post_title }}</a></div>
        <div class="colored-box-body">
            <div class="table-responsive">
                <table class="table colored-table">
                    <thead>
                    <tr>
                        <th class="text-left">Product</th>
                        <th class="col-sm-1">Level</th>
                        <th>Test Cases</th>
                        <th>Status</th>
                        <th>Requestor</br>Assignee</th>
                        <th>Submitted</br>Updated</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                        @foreach($userSuite['data'] as $verifyRequest)
                            <?php $canModerate = $isAdmin && $verifyRequest['verifyRequest']->assignee_id == Auth::user()->ID;?>
                            <tr id="verify-request-{{ $verifyRequest['verifyRequest']->id }}">
                                <td class="text-left">
                                    <a href="#verify-request-details-{{ $verifyRequest['verifyRequest']->id }}" class="collapsed" data-toggle="collapse"><span class="collapse-icon"></span></a><a href="/product/{{ $verifyRequest['product']->post_name }}" target="_blank"> {{ $verifyRequest['product']->post_title }} </a>
                                </td>
                                <td class="col-sm-1 text-center" >{{ $verifyRequest['testPlan']->level }}</td>
                                <td>
                                    {{ $verifyRequest->transactions }}
                                    <div class="coverage-progress">
                                        @foreach($verifyRequest['testCases'] as $case)
                                            <a href="#" data-tooltip="tooltip" class="coverage-test-case-item" title="{{ $case->post_title }}"></a>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="text-center">{{ $verifyRequest['verifyRequest']->status }}</td>
                                <td class="text-center">
                                    <a href="/members/{{ $verifyRequest['requestor']->user_nicename }}" target="_blank"> {{ cp_get_user_fullname($verifyRequest['verifyRequest']->requestor_id) }}</a></br>
                                    @if($verifyRequest['verifyRequest']->assignee_id)
                                        <a href="/members/{{ $verifyRequest['assignee']->user_nicename }}" target="_blank">{{ cp_get_user_fullname($verifyRequest['verifyRequest']->assignee_id) }}</a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{ formatDate($verifyRequest['verifyRequest']->created_at, 'Y-m-d H:i:s') }}<br>
                                    {{ formatDate($verifyRequest['verifyRequest']->updated_at, 'Y-m-d H:i:s') }}
                                </td>
                                <td class="text-center">
                                    @if($isAdmin && $verifyRequest['verifyRequest']->status == 'In Progress' && $verifyRequest['verifyRequest']->canBeResolved(Auth::user()))
                                        <a href="/verify-requests/{{ $userSuite['testSuite']->ID }}/resolve/{{ $verifyRequest['verifyRequest']->id }}" data-toggle="modal" data-remote="true" data-ajax-modal data-target="#assignVerifyRequestModal"
                                           class="btn btn-success btn-icon btn-confirm" data-tooltip="tooltip" title="Resolve"></a>
                                    @endif
                                    @if($isAdmin && $verifyRequest['verifyRequest']->status != 'Resolved')
                                        <a href="/verify-requests/{{ $userSuite['testSuite']->ID }}/assign/{{ $verifyRequest['verifyRequest']->id }}" data-toggle="modal" data-remote="true" data-ajax-modal data-target="#assignVerifyRequestModal"
                                           class="btn btn-primary btn-icon btn-view" data-tooltip="tooltip" title="Assign Verify Request"></a>
                                    @endif
                                    @if($verifyRequest['verifyRequest']->canUserDelete())
                                        <a href="#removeVerifyRequestModal-{{ $verifyRequest['verifyRequest']->id }}" data-toggle="modal"
                                           class="btn btn-danger btn-icon btn-delete" data-tooltip="tooltip" title="Delete Verify Request"></a>
                                        <!-- Remove VerifyReqest Confirmation Modal-->
                                        <div class="modal fade" id="removeVerifyRequestModal-{{ $verifyRequest['verifyRequest']->id }}" tabindex="-1" role="dialog">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content block-loading-wrapper">
                                                    <div class="modal-header">
                                                        <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
                                                        Confirm Deletion
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="default-text">Are you sure that you want to delete this Verify Request?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <a href="/verify-requests/{{ $verifyRequest['verifyRequest']->id }}" data-request-id="{{ $verifyRequest['verifyRequest']->id }}"
                                                           class="btn btn-success btn-with-icon btn-confirm deleteVerifyRequest">Confirm</a>
                                                        <button type="button" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            <tr class="details_row collapse" id="verify-request-details-{{ $verifyRequest['verifyRequest']->id }}">
                                <td colspan="7">
                                    @if($canModerate)
                                        <button class="verify_as_pass btn btn-success btn-with-icon btn-trigger change_status"
                                                data-outcome="Pass" data-toggle="modal" data-remote="true" data-target="#changeStatusModal">Verify As Pass</button>
                                        <button class="verify_as_fail btn btn-danger btn-with-icon btn-trigger change_status"
                                                data-outcome="Fail" data-toggle="modal" data-remote="true" data-target="#changeStatusModal">Verify As Fail</button>
                                        <button class="verify_as_skip btn btn-default btn-with-icon btn-trigger change_status"
                                                data-outcome="Skip" data-toggle="modal" data-remote="true" data-target="#changeStatusModal">Verify As Skip</button>
                                    @endif
                                    <div class="table-responsive">
                                        <table class="table colored-table" style="margin-top: 20px;">
                                            <thead>
                                                <tr>
                                                    @if($canModerate)
                                                        <th></th>
                                                    @endif
                                                    <th>Test Case</th>
                                                    <th>Transaction ID</th>
                                                    <th>Execution ID</th>
                                                    <th>Status</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach(json_decode($verifyRequest['verifyRequest']->transactions, true) as  $transactionId)
                                                    <?php
                                                        $transaction = \App\Transaction::find($transactionId);
                                                        $testOutcomeStatus = \App\TestOutcomeStatus::find($transaction->test_outcome_status_id)->name;
                                                    ?>
                                                    <tr>
                                                        @if($canModerate)
                                                            <td>
                                                                <input type="checkbox" name="transaction" class="transaction" value="{{ $transaction->id }}"
                                                                       data-case="{{ $testCase }}" @if($testOutcomeStatus != 'Pending') disabled="disabled" @endif>
                                                            </td>
                                                        @endif
                                                        <td>
                                                            <a href="#verify-request-transactions-{{ $transactionId }}" class="collapsed" data-toggle="collapse">
                                                                <span class="collapse-icon"></span>{{ \App\Post::find($transaction->test_case_id)->post_title }}
                                                            </a>
                                                        </td>
                                                        <td class="text-center">{{ $transaction->id }}</td>
                                                        <td class="text-center">
                                                            @if($transaction->s3_link)
                                                                <a href="{!! $transaction->s3_link !!}" target="_blank"> {!! $transaction->execution_id !!} </a>
                                                            @else
                                                                {!! $transaction->execution_id !!}
                                                            @endif
                                                        </td>
                                                        <td class="text-center row-outcome-status">
                                                            @if($transaction->reason)
                                                                <a href="/testingdetails/{{ $transaction->id }}/transaction-reason/laravel" data-toggle="modal" data-remote="true" data-ajax-modal data-target="#viewReasonModal" class="s3_output">
                                                                    {{ $testOutcomeStatus }}
                                                                </a>
                                                            @else
                                                                {{ $testOutcomeStatus }}
                                                            @endif
                                                        </td>
                                                        <td class="text-center">{{ formatDate($transaction->created_at, 'Y-m-d H:i:s') }}</td>
                                                    </tr>
                                                    <tr class="transactions_row collapse" id="verify-request-transactions-{{ $transactionId }}">
                                                        <td colspan="7">
                                                            <div class="table-responsive">
                                                                <table class="table colored-table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>From<br>To</th>
                                                                            <th>Test<br>Step</th>
                                                                            <th>Operation Triplet<br>Return Code</th>
                                                                            <th>Session State</th>
                                                                            <th>Message Data</th>
                                                                            <th>Date Time</th>
                                                                            <th>Step Outcome</th>
                                                                            <th>Screen Capture</th>
                                                                            <th>Scan Results</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($transaction->logs as $message)
                                                                            <?php $testCase = \App\Post::find($transaction->test_case_id);?>
                                                                            <tr>
                                                                                <td class="text-center">{{ $message->from }}<br>{{ $message->to }}</td>
                                                                                <td class="text-center">
                                                                                    @if(!empty($message->test_step))
                                                                                        <a href="/test-case/{{ $testCase->post_name }}#step_anchor_{{ $message->test_step }}" target="_blank">{{ $message->test_step }}</a>
                                                                                    @endif
                                                                                </td>
                                                                                <td class="text-center">
                                                                                    {{ $message->data_group }} / {{ $message->data_argument_type }} / {{ $message->messages }} </br>
                                                                                    <span style="color: {{ getReturnCodeColor($message->return_code) }}">{{ $message->return_code }}</span>
                                                                                </td>
                                                                                <td class="text-center">
                                                                                    @if($message->session_state)
                                                                                        {{ $message->session_state }}
                                                                                    @endif
                                                                                </td>
                                                                                <td class="text-center">
                                                                                    @if(!empty($message->log_output))
                                                                                        <a href="/testingdetails/{{ $message->id }}/output/laravel" data-toggle="modal" data-remote="true" data-ajax-modal data-target="#viewOutputModal" class="s3_output">View</a>
                                                                                    @endif
                                                                                </td>
                                                                                <td class="text-center">{{ $message->updated_at }}</td>
                                                                                <td class="text-center">
                                                                                    @if(empty($message->reason))
                                                                                        <span class="status-<?php echo getOutcomeStatusClass(strtoupper($message->step_outcome));?>" data-toggle="modal" data-remote="true" data-ajax-modal data-target="#viewReasonModal" >
                                                                                            {{ $message->step_outcome }}
                                                                                        </span>
                                                                                    @else
                                                                                        <a href="/testingdetails/{{ $message->id }}/reason/laravel"  data-toggle="modal" data-remote="true" data-ajax-modal data-target="#viewReasonModal" >
                                                                                            <span class="status-{{ getOutcomeStatusClass(strtoupper($message->step_outcome)) }}">{{ $message->step_outcome }}</span>
                                                                                        </a>
                                                                                    @endif
                                                                                </td>
                                                                                <td class="text-center">-</td>
                                                                                <td class="text-center">
                                                                                    @if ($scanImages = json_decode($message->scan_results))
                                                                                        @foreach ($scanImages as $scanImage)
                                                                                            <a href="{{ $scanImage }}" target="_blank">View</a>
                                                                                        @endforeach
                                                                                    @else
                                                                                        -
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                </table>
            </div>
        </div>
    </div>

    @if(!$isAdmin)
        <a href="/verify-requests/{{ $userSuite['testSuite']->ID }}/create/" data-toggle="modal" data-remote="true" data-ajax-modal data-target="#createVerifyRequestModal"
           class="btn btn-success btn-with-icon btn-add" style="margin-bottom: 20px;">Add</a>
    @endif

@endforeach