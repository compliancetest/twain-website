@if(in_array($case->ID, $testPlanData['successCases']))
    <a data-toggle="modal" data-remote="true" data-ajax-modal data-target="#testDetailsModal" href="/testplan/{{ $userPlan['testPlan']->id }}/view/{{ $case->ID }}"
       data-tooltip="tooltip" class="coverage-test-case-item test-passed" title="{{ $case->post_title }}"></a>

@elseif(in_array($case->ID, $testPlanData['skippedCases']))

    <a data-toggle="modal" data-remote="true" data-ajax-modal data-target="#testDetailsModal" href="/testplan/{{ $userPlan['testPlan']->id }}/view/{{ $case->ID }}"
       data-tooltip="tooltip" class="coverage-test-case-item test-skipped" title="{{ $case->post_title }}"></a>

@elseif(array_key_exists($case->ID, $testPlanData['excludedCases']))

    <a data-toggle="modal" data-remote="true" data-ajax-modal data-target="#testDetailsModal" href="/testplan/{{ $userPlan['testPlan']->id }}/view/{{ $case->ID }}"
       data-tooltip="tooltip" class="coverage-test-case-item test-excluded" title="{{ $case->post_title }} (excl)"></a>

@elseif(in_array($case->ID, $testPlanData['failedCases']))

    <a data-toggle="modal" data-remote="true" data-ajax-modal data-target="#testDetailsModal" href="/testplan/{{ $userPlan['testPlan']->id }}/view/{{ $case->ID }}"
       data-tooltip="tooltip" class="coverage-test-case-item test-failed" title="{{ $case->post_title }}"></a>

@elseif(in_array($case->ID, $testPlanData['optionalCases']))

    <a data-toggle="modal" data-remote="true" data-ajax-modal data-target="#testDetailsModal" href="/testplan/{{ $userPlan['testPlan']->id }}/view/{{ $case->ID }}"
       data-tooltip="tooltip" class="coverage-test-case-item test-optional" title="{{ $case->post_title }}"></a>
@else
    <a data-toggle="modal" data-remote="true" data-ajax-modal data-target="#testDetailsModal" href="/testplan/{{ $userPlan['testPlan']->id }}/view/{{ $case->ID }}"
       data-tooltip="tooltip" class="coverage-test-case-item" title="{{ $case->post_title }}"></a>
@endif