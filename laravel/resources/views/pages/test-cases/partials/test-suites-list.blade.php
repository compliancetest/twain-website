<div class="colored-box-content">
    <div class="checkboxes-group two-col">
        @if(count($testSuites))
            @foreach($testSuites as $testSuite)
                <div class="checkbox">
                    <label>
                        <input name="test_suite_id[]" value="{{ $testSuite->id }}" type="checkbox" class="testSuite"
                               @if($testCase && count($testCase->testSuites->where('id', $testSuite->id))) checked="checked" @endif>
                        {{ $testSuite->full_name }}
                    </label>
                </div>
            @endforeach
        @else
            <div class="text-center">This community don't have any test suite</div>
        @endif
    </div>
</div>
