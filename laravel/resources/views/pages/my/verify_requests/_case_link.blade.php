@if($status == 'Pass')
    <a href="#" onclick="return false;"
       data-tooltip="tooltip" class="coverage-test-case-item test-passed" title="{{ $case->post_title }}"></a>
@elseif($status == 'Skip')
    <a href="#" onclick="return false;"
       data-tooltip="tooltip" class="coverage-test-case-item test-skipped" title="{{ $case->post_title }}"></a>
@elseif($status == 'Fail')
    <a href="#" onclick="return false;"
       data-tooltip="tooltip" class="coverage-test-case-item test-failed" title="{{ $case->post_title }}"></a>
@else
    <a href="#" onclick="return false;"
       data-tooltip="tooltip" class="coverage-test-case-item" title="{{ $case->post_title }}"></a>
@endif