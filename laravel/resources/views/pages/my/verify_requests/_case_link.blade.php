@if($status == 'Pass')
    <a data-toggle="modal" data-remote="true" data-ajax-modal data-target="#testDetailsModal" href="/testplan/{{ $userPlan['testPlan']->id }}/view/{{ $case->ID }}"
       data-tooltip="tooltip" class="coverage-test-case-item test-passed" title="{{ $case->post_title }}"></a>

@elseif($status == 'Skip')

    <a data-toggle="modal" data-remote="true" data-ajax-modal data-target="#testDetailsModal" href="/testplan/{{ $userPlan['testPlan']->id }}/view/{{ $case->ID }}"
       data-tooltip="tooltip" class="coverage-test-case-item test-skipped" title="{{ $case->post_title }}"></a>

@elseif($status == 'Fail')
    <a data-toggle="modal" data-remote="true" data-ajax-modal data-target="#testDetailsModal" href="/testplan/{{ $userPlan['testPlan']->id }}/view/{{ $case->ID }}"
       data-tooltip="tooltip" class="coverage-test-case-item test-failed" title="{{ $case->post_title }}"></a>

@else
    <a data-toggle="modal" data-remote="true" data-ajax-modal data-target="#testDetailsModal" href="/testplan/{{ $userPlan['testPlan']->id }}/view/{{ $case->ID }}"
       data-tooltip="tooltip" class="coverage-test-case-item" title="{{ $case->post_title }}"></a>
@endif