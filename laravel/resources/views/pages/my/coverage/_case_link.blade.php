@if(in_array($case->id, $testPlanData['successCases']))
    <a data-toggle="modal" data-remote="true" data-ajax-modal data-target="#testDetailsModal" href="/testplan/{{ $userPlan['testPlan']->id }}/view/{{ $case->id }}"
       data-tooltip="tooltip" class="coverage-test-case-item test-passed" title="{{ $case->full_name }}"></a>

@elseif(in_array($case->id, $testPlanData['skippedCases']))

    <a data-toggle="modal" data-remote="true" data-ajax-modal data-target="#testDetailsModal" href="/testplan/{{ $userPlan['testPlan']->id }}/view/{{ $case->id }}"
       data-tooltip="tooltip" class="coverage-test-case-item test-skipped" title="{{ $case->full_name }}"></a>

@elseif(array_key_exists($case->id, $testPlanData['excludedCases']))

    <a data-toggle="modal" data-remote="true" data-ajax-modal data-target="#testDetailsModal" href="/testplan/{{ $userPlan['testPlan']->id }}/view/{{ $case->id }}"
       data-tooltip="tooltip" class="coverage-test-case-item test-excluded" title="{{ $case->full_name }} (excl)"></a>

@elseif(in_array($case->id, $testPlanData['failedCases']))

    <a data-toggle="modal" data-remote="true" data-ajax-modal data-target="#testDetailsModal" href="/testplan/{{ $userPlan['testPlan']->id }}/view/{{ $case->id }}"
       data-tooltip="tooltip" class="coverage-test-case-item test-failed" title="{{ $case->full_name }}"></a>

@elseif(in_array($case->id, $testPlanData['optionalCases']))

    <a data-toggle="modal" data-remote="true" data-ajax-modal data-target="#testDetailsModal" href="/testplan/{{ $userPlan['testPlan']->id }}/view/{{ $case->id }}"
       data-tooltip="tooltip" class="coverage-test-case-item test-optional" title="{{ $case->full_name }}"></a>
@else
    <a data-toggle="modal" data-remote="true" data-ajax-modal data-target="#testDetailsModal" href="/testplan/{{ $userPlan['testPlan']->id }}/view/{{ $case->id }}"
       data-tooltip="tooltip" class="coverage-test-case-item" title="{{ $case->full_name }}"></a>
@endif