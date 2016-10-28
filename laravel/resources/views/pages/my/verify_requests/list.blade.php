@if($userSuites)
    @foreach($userSuites as $userSuite)
        <div class="colored-box">
            <div class="colored-box-header"><a href="/test-suite/{{ $userSuite['testSuite']->slug }}/">{{ $userSuite['testSuite']->full_name }}</a></div>
            <div class="colored-box-body">
                <div class="table-responsive">
                    <table class="table colored-table">
                        <thead>
                        <tr>
                            <th class="text-left" style="width: 20%">Product</th>
                            <th class="col-sm-1">Level</th>
                            <th style="width: 30%">Test Cases</th>
                            <th>Status</th>
                            <th>Requestor</br>Assignee</th>
                            <th>Submitted</br>Updated</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        @if($userSuite['data'])
                            @foreach($userSuite['data'] as $verifyRequest)
                                <?php
                                $canModerate = $isAdmin && $verifyRequest['verifyRequest']->assignee_id == Auth::user()->ID && $verifyRequest['verifyRequest']->is_accepted;
                                ?>
                                <tr id="verify-request-{{ $verifyRequest['verifyRequest']->id }}">
                                    <td class="text-left">
                                        <a href="#verify-request-details-{{ $verifyRequest['verifyRequest']->id }}" class="collapsed" data-toggle="collapse">
                                            <span class="collapse-icon"></span></a>
                                        <a href="/product/{{ $verifyRequest['product']->slug }}" target="_blank"> {{ $verifyRequest['product']->full_name }} </a>
                                    </td>
                                    <td class="col-sm-1 text-center">{{ $verifyRequest['testPlan']->level }}</td>
                                    <td>
                                        {{ $verifyRequest->transactions }}
                                        <div class="coverage-progress">
                                            @foreach($verifyRequest['testCases'] as $case)
                                                <?php $status = $verifyRequest['verifyRequest']->getTestCaseStatus($case->ID);?>
                                                @include('pages.my.verify_requests._case_link', ['status' => $status])
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-status-{{ getOutcomeStatusClass($verifyRequest['verifyRequest']->status) }}">
                                            {{ $verifyRequest['verifyRequest']->status }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="/members/{{ $verifyRequest['requestor']->user_nicename }}"
                                           target="_blank"> {{ cp_get_user_fullname($verifyRequest['verifyRequest']->requestor_id) }}</a></br>
                                        @if($verifyRequest['verifyRequest']->assignee_id)
                                            <a href="/members/{{ $verifyRequest['assignee']->user_nicename }}"
                                               target="_blank">{{ cp_get_user_fullname($verifyRequest['verifyRequest']->assignee_id) }}</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{ formatDate($verifyRequest['verifyRequest']->created_at, 'Y-m-d H:i:s') }}<br>
                                        {{ formatDate($verifyRequest['verifyRequest']->updated_at, 'Y-m-d H:i:s') }}
                                    </td>
                                    <td class="text-center">

                                        @if($isAdmin && $verifyRequest['verifyRequest']->canBeResolved(Auth::user()))
                                            <a href="/verify-requests/{{ $userSuite['testSuite']->ID }}/resolve/{{ $verifyRequest['verifyRequest']->id }}" data-toggle="modal"
                                               data-remote="true" data-ajax-modal data-target="#assignVerifyRequestModal"
                                               class="btn btn-success btn-icon btn-confirm" data-tooltip="tooltip" title="Resolve"></a>
                                        @endif

                                        @if($isAdmin && $verifyRequest['verifyRequest']->status != 'Resolved')
                                            <a href="/verify-requests/{{ $userSuite['testSuite']->ID }}/assign/{{ $verifyRequest['verifyRequest']->id }}" data-toggle="modal"
                                               data-remote="true" data-ajax-modal data-target="#assignVerifyRequestModal"
                                               class="btn btn-primary btn-icon btn-view" data-tooltip="tooltip" title="Assign Verify Request"></a>
                                        @endif

                                        @if($isAdmin && ( $verifyRequest['verifyRequest']->status == 'New' ||
                                            ($verifyRequest['verifyRequest']->is_accepted == false && $verifyRequest['verifyRequest']->assignee_id == Auth::user()->ID)))
                                            <a href="/verify-requests/{{ $userSuite['testSuite']->ID }}/accept/{{ $verifyRequest['verifyRequest']->id }}" data-toggle="modal"
                                               data-remote="true" data-ajax-modal data-target="#acceptVerifyRequestModal"
                                               class="btn btn-success btn-icon btn-confirm" data-tooltip="tooltip" title="Accept Verify Request"></a>
                                        @endif

                                        @if($isAdmin && $verifyRequest['verifyRequest']->assignee_id == Auth::user()->ID && $verifyRequest['verifyRequest']->status != 'Resolved')
                                            <a href="/verify-requests/{{ $userSuite['testSuite']->ID }}/unassign/{{ $verifyRequest['verifyRequest']->id }}" data-toggle="modal"
                                               data-remote="true" data-ajax-modal data-target="#unassignVerifyRequestModal"
                                               class="btn btn-warning btn-icon btn-delete" data-tooltip="tooltip" title="Unassign Verify Request"></a>
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
                                                            <a href="/verify-requests/{{ $verifyRequest['verifyRequest']->id }}"
                                                               data-request-id="{{ $verifyRequest['verifyRequest']->id }}"
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
                                                    data-outcome="Pass" data-toggle="modal" data-remote="true" data-target="#changeStatusModal">Verify As Pass
                                            </button>
                                            <button class="verify_as_fail btn btn-danger btn-with-icon btn-trigger change_status"
                                                    data-outcome="Fail" data-toggle="modal" data-remote="true" data-target="#changeStatusModal">Verify As Fail
                                            </button>
                                            <button class="verify_as_skip btn btn-default btn-with-icon btn-trigger change_status"
                                                    data-outcome="Skip" data-toggle="modal" data-remote="true" data-target="#changeStatusModal">Verify As Skip
                                            </button>
                                        @endif
                                        <div class="table-responsive">
                                            <table class="table colored-table" style="margin-top: 20px;">
                                                <thead>
                                                <tr>
                                                    @if($canModerate)
                                                        <th></th>
                                                    @endif
                                                    <th>Test Case</th>
                                                    <th>Execution ID</th>
                                                    <th>Status</th>
                                                    <th>Date</th>
                                                    @if(isImageViewerEnabled())
                                                        <th>Action</th>
                                                    @endif
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach(json_decode($verifyRequest['verifyRequest']->transactions, true) as  $transactionId)
                                                    <?php
                                                    $transaction = \App\Transaction::find($transactionId);
                                                    $testOutcomeStatus = \App\TestOutcomeStatus::find($transaction->test_outcome_status_id);
                                                    $status = getOutcomeStatusClass($testOutcomeStatus->code);
                                                    $testOutcomeStatus = $testOutcomeStatus->name;
                                                    ?>
                                                    <tr>
                                                        @if($canModerate)
                                                            <td>
                                                                <input type="checkbox" name="transaction" class="transaction" value="{{ $transaction->id }}"
                                                                       data-case="{{ $transaction->test_case_id }}" @if($testOutcomeStatus != 'Pending') disabled="disabled" @endif>
                                                            </td>
                                                        @endif
                                                        <td>
                                                            <a href="#verify-request-transactions-{{ $transactionId }}" class="collapsed" data-toggle="collapse">
                                                                <span class="collapse-icon"></span>
                                                            </a>
                                                            <?php $testCaseData = \App\LaravelTestCase::find($transaction->test_case_id);?>
                                                            <a href="/test-case/{{ $testCaseData->slug }}/?test_suite_id={{ $transaction->suite_minor_family_mark }}"
                                                               target="_blank">{{ $testCaseData->full_name }}</a>
                                                        </td>
                                                        <td class="text-center">
                                                            @if($transaction->s3_link)
                                                                <a href="{!! $transaction->s3_link !!}" target="_blank"> {!! $transaction->execution_id !!} </a>
                                                            @else
                                                                {!! $transaction->execution_id !!}
                                                            @endif
                                                        </td>
                                                        <td class="text-center row-outcome-status">
                                                            @if(!empty($transaction->reason))
                                                                <a href="/testingdetails/{{ $transaction->id }}/transaction-reason" data-toggle="modal" data-remote="true"
                                                                   data-ajax-modal data-target="#viewReasonModal" class="s3_output text-status-{{ $status }}">
                                                                    {{ $testOutcomeStatus }}
                                                                </a>
                                                            @else
                                                                <span class="text-status-{{ $status }}">{{ $testOutcomeStatus }}</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">{{ formatDate($transaction->created_at, 'Y-m-d H:i:s') }}</td>
                                                        @if(isImageViewerEnabled())
                                                            <td class="text-center">
                                                                <a class="btn btn-success showImageViewer"
                                                                   href="/verify-requests/{{ \App\Community::find($userSuite['testSuite']->community_id)->slug }}/image-viewer/{{ $verifyRequest['verifyRequest']->id }}/{{ $transactionId }}"
                                                                   data-toggle="modal" data-remote="true" data-ajax-modal data-target="#viewImagesModal">View Images</a>
                                                            </td>
                                                        @endif
                                                    </tr>
                                                    <tr class="transactions_row collapse logRow" id="verify-request-transactions-{{ $transactionId }}"
                                                        data-transaction-id="{{ $transaction->id }}">
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
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td class="text-center" colspan="7">No Verify Requests yet</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        @if(!$isAdmin)
            <a href="/verify-requests/{{ $userSuite['testSuite']->ID }}/create/" data-toggle="modal" data-remote="true" data-ajax-modal data-target="#createVerifyRequestModal"
               class="btn btn-success btn-with-icon btn-add" style="margin-bottom: 20px;">Add</a>
        @endif

    @endforeach
@else
    <div class="colored-box">
        <div class="colored-box-header"></div>
        <div class="colored-box-body">
            <div class="table-responsive">
                <table class="table colored-table">
                    <thead>
                    <tr>
                        <th>You are currently not subscribed to any test suites.</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endif

@section('page-scripts')
    <script src="{{ getSiteUrl() }}/laravel/resources/assets/js/vendor/jquery.flexslider-min.js"></script>
    <script>

    </script>
@stop