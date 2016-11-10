@if($status == 'Pass')
    <a href="#" onclick="return false;"
       data-tooltip="tooltip" class="coverage-test-case-item test-passed" title="{{ $case->full_name }}"></a>
@elseif($status == 'Skip')
    <a href="#" onclick="return false;"
       data-tooltip="tooltip" class="coverage-test-case-item test-skipped" title="{{ $case->full_name }}"></a>
@elseif($status == 'Fail')
    <a href="#" onclick="return false;"
       data-tooltip="tooltip" class="coverage-test-case-item test-failed" title="{{ $case->full_name }}"></a>
@else
    <a href="#" onclick="return false;"
       data-tooltip="tooltip" class="coverage-test-case-item" title="{{ $case->full_name }}"></a>
@endif