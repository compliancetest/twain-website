@foreach($userSuites as $suiteName => $userSuite)
    <div class="colored-box">
        <div class="colored-box-header"><a href="/test-suite/{{ $userSuite['testSuite']->post_name }}/">{{ $suiteName }}</a></div>
        <div class="colored-box-body">
            <div class="table-responsive">
                <table class="table colored-table">
                    <thead>
                    <tr>
                        <th class="text-left">Product</th>
                        <th class="col-sm-1">Level</th>
                        <th class="col-sm-1 text-left">Role</th>
                        <th>Coverage</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>

                    @if($userSuite['testPlans'])
                        @foreach($userSuite['testPlans'] as $userPlan)
                            <tr id="coverage-plan-{{ $userPlan['testPlan']->id }}">
                                <td class="text-nowrap">
                                    {{ $userPlan['product']->post_title }}
                                    @if($userPlan['product']) {{ ' v' . $userPlan['product']->getMetaByKey('product_version') }}@endif
                                </td>
                                <td class="text-center">{{ $userPlan['testPlan']->level }}</td>
                                <td>{{ $userPlan['testPlan']->role }}</td>
                                <td>
                                    <div class="coverage-progress">
                                        <?php error_log($userPlan['testPlan']->role);?>
                                        @foreach($userSuite['testSuite']->getTestCases($userPlan['testPlan']->role, $userPlan['testPlan']->level) as $case)
                                            @include('pages.my.coverage._case_link', ['testPlanData' => $userPlan['testPlanData']])
                                        @endforeach
                                    </div>
                                </td>
                                <td class="col-sm-1 text-nowrap">
                                    <a href="/my-transaction-log?suite={{ $userSuite['testSuite']->ID }}&product={{ $userPlan['product']->ID }}" target="_blank" class="btn btn-primary btn-icon btn-view" data-tooltip="tooltip" title="View log"></a>

                                    <a href="/testplan/{{ $userPlan['testPlan']->id }}/edit" data-toggle="modal" data-remote="true" data-ajax-modal data-target="#editPlanModal"
                                       class="btn btn-primary btn-icon btn-edit" data-tooltip="tooltip" title="Edit plan"></a>
                                    <a href="#removePlanModal-{{ $userPlan['testPlan']->id }}" data-toggle="modal" class="btn btn-danger btn-icon btn-delete" data-tooltip="tooltip"
                                       title="Delete plan"></a>
                                    @if($userPlan['testPlan']->canBeClaimed() && !$userPlan['testPlan']->is_claimed)
                                        <a href="/testplan/{{ $userPlan['testPlan']->id }}/claim" class="btn btn-success btn-icon btn-confirm" data-tooltip="tooltip" title="Claim"></a>
                                    @endif

                                    <!-- Remove Plan Confirmation Modal-->
                                    <div class="modal fade" id="removePlanModal-{{ $userPlan['testPlan']->id }}" tabindex="-1" role="dialog">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content block-loading-wrapper">
                                                <div class="modal-header">
                                                    <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
                                                    Confirm Deletion
                                                </div>
                                                <div class="modal-body">
                                                    <p class="default-text">Are you sure that you want to delete this plan?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <a href="/testplan/{{ $userPlan['testPlan']->id }}" data-plan-id="{{ $userPlan['testPlan']->id }}"
                                                       class="btn btn-success btn-with-icon btn-confirm deleteTestCoveragePlan">Confirm</a>
                                                    <button type="button" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                        @endforeach
                    @else
                        <tr>
                            <td class="text-nowrap text-center" colspan="5">No plans yet</td>
                        </tr>
                    @endif

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <a href="/testplan/create/{{ $userSuite['testSuite']->ID }}" data-toggle="modal" data-remote="true" data-ajax-modal data-target="#editPlanModal" class="btn btn-success btn-with-icon btn-add" style="margin-bottom: 20px;">Add</a>

@endforeach