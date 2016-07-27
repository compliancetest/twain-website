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
                        <th>Requestor</th>
                        <th>Assignee</th>
                        <th>Submitted<br>Updated</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                        @foreach($userSuite['data'] as $verifyRequest)
                            <tr id="verify-request-{{ $verifyRequest['verifyRequest']->id }}">
                                <td class="text-left">{{ $verifyRequest['product']->post_title }}</td>
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
                                <td class="text-center">{{ cp_get_user_fullname($verifyRequest['verifyRequest']->requestor_id) }}</td>
                                <td class="text-center">{{ cp_get_user_fullname($verifyRequest['verifyRequest']->assignee_id) }}</td>
                                <td class="text-center">
                                    {{ formatDate($verifyRequest['verifyRequest']->created_at, 'Y-m-d H:i:s') }}
                                    <br>{{ formatDate($verifyRequest['verifyRequest']->updated_at, 'Y-m-d H:i:s') }}
                                </td>
                                <td class="text-center">
                                    @if($verifyRequest['verifyRequest']->canUserDelete())
                                        <a href="#removeVerifyRequestModal-{{ $verifyRequest['verifyRequest']->id }}" data-toggle="modal"
                                           class="btn btn-danger btn-icon btn-delete" data-tooltip="tooltip" title="Delete plan"></a>
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
                        @endforeach
                </table>
            </div>
        </div>
    </div>
    <a href="/verify-requests/{{ $userSuite['testSuite']->ID }}/create/" data-toggle="modal" data-remote="true" data-ajax-modal data-target="#createVerifyRequestModal"
       class="btn btn-success btn-with-icon btn-add" style="margin-bottom: 20px;">Add</a>
@endforeach