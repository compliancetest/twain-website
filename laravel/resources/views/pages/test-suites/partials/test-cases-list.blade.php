<div class="blue-colored-table-wrapper table-responsive">
    <table class="table blue-colored-table test-cases-table">
        <thead>
        <tr>
            <th class="text-left">Test Scenario</th>
            <th class="text-left">Test Case</th>
            <th>Tester Role</th>
            <th>Conf Levels</th>
            <th>Outcome Type</th>
            <th>Test Pattern</th>
            <th class="text-left">Test Intent Description</th>
            @can('change', $testSuite)
                <th>Actions</th>
            @endcan
        </tr>
        </thead>
        <tbody>
        <?php $scenarioId = false;?>
        <?php $testCases = $testSuite->getCases($filters, $isAdmin)->paginate(10);?>
        @if(count($testCases))
            @foreach($testCases  as $index => $testCase)
                <tr>
                    @if($scenarioId != $testCase->scenarioId || $index === 0)
                        <?php $scenarioId = $testCase->scenarioId;?>
                        <td rowspan="{{ $testCases->filter(function ($value) use ($scenarioId) {
                                                return $value->scenarioId == $scenarioId;
                                            })->count() }}" class="rowspan-cell">
                            @if($testCase->scenarioCode)
                                <strong>{{ $testCase->scenarioCode }}:</strong><br/> {!! $testCase->scenarioDescription !!}
                            @endif
                        </td>
                    @endif
                    <td class="text-nowrap"><span class="status status-circle status-{{ strtolower($testCase->status) }}" data-tooltip="tooltip"
                              title="{{ $testCase->status }}">{{ substr($testCase->status, 0, 1) }}</span><a
                                href="/test-case/{{ $testCase->slug }}">{{ $testCase->full_name }}</a></td>
                    <td class="text-center">{{ $testCase->tester_role}}</td>
                    <td class="text-center">
                        {{ implode(', ', array_unique($testCase->getConformanceLevels($isAdmin)->pluck('code')->toArray())) }}
                    </td>
                    <td class="text-center">{{ $testCase->outcome_type }}</td>
                    <td class="text-center">
                        <a href="/help-faq/test-patterns/" data-tooltip="tooltip"
                           title="{{ get_test_patterns_description($testCase->test_pattern) }}">
                            <span class="test-pattern-icon test-pattern-{{ $testCase->test_pattern }}"></span>
                        </a>
                    </td>
                    <td>{!! $testCase->description !!}</td>
                    @can('change', $testSuite)
                        <td class="text-center">
                            <a href="/test-case/{{ $testCase->slug }}/edit" class="btn btn-primary btn-icon btn-edit" data-tooltip="tooltip" title="Edit Case">Edit</a>
                            <button type="button" data-toggle="modal" data-target="#deleteTestCaseModal{{$index}}" class="btn btn-danger btn-icon btn-delete" data-tooltip="tooltip"
                                    title="Delete Case">Delete
                            </button>
                            {{-- Delete Test Case Modal--}}
                            <div class="modal fade" id="deleteTestCaseModal{{$index}}" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content block-loading-wrapper">
                                        <div class="modal-header">
                                            <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
                                            Delete Test Case
                                        </div>
                                        <div class="modal-body">
                                            Are you sure you want delete {{ $testCase->full_name }}?
                                        </div>
                                        <div class="modal-footer">
                                            <a href="#DELETE_URL" class="btn btn-success btn-with-icon btn-confirm">Confirm</a>
                                            <button class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    @endcan
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="{{ $isAdmin ? 8 : 7 }}" class="text-center">No Test Cases yet</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

<div class="pagination-wrapper">
    {{ $testCases->links() }}
</div>