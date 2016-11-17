<div class="blue-colored-table-wrapper table-responsive">
    <table class="table blue-colored-table test-cases-table">
        <thead>
        <tr>
            <th class="text-left">Test Scenario</th>
            <th class="text-left">Test Case</th>
            <th>Tester Role</th>
            <th>Conf Levels</th>
            <th>Outcome Type</th>
            <th class="noprint">Test Pattern</th>
            <th class="text-left">Test Intent Description</th>
            @can('changeTestSuite', $testSuite)
                <th class="noprint">Actions</th>
            @endcan
        </tr>
        </thead>
        <tbody>
        <?php $scenarioCode = false;?>
        <?php $testCases = $testSuite->getCases($filters, $isAdmin)->paginate(10);?>
        @if(count($testCases))
            @foreach($testCases  as $index => $testCase)
                <tr>
                    @if($scenarioCode !== $testCase->scenarioCode)
                        <?php $scenarioCode = $testCase->scenarioCode;?>
                        <td rowspan="{{ $testCases->filter(function ($value) use ($scenarioCode) {
                                                return $value->scenarioCode == $scenarioCode;
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
                    <td class="text-center noprint">
                        <a href="/help-faq/test-patterns/" data-tooltip="tooltip"
                           title="{{ get_test_patterns_description($testCase->test_pattern) }}">
                            <span class="test-pattern-icon test-pattern-{{ $testCase->test_pattern }}"></span>
                        </a>
                    </td>
                    <td>{!! $testCase->description !!}</td>
                        @can('changeTestSuite', $testSuite)
                            <td class="text-center noprint">
                                <a href="/test-case/{{ $testCase->slug }}/edit" class="btn btn-primary btn-icon btn-edit" data-tooltip="tooltip" title="Edit Case">Edit</a>
                                <a data-target="#deleteTestCaseModal" class="btn btn-danger btn-icon btn-delete"
                                   data-tooltip="tooltip" href="/test-case/{{ $testCase->slug }}/delete" data-toggle="modal"
                                   data-remote="true" data-ajax-modal data-original-title="Delete Test Case">Delete
                                </a>
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

<div class="pagination-wrapper noprint">
    {{ $testCases->links() }}
</div>

@include('pages.test-cases.partials.delete-test-case-popup')