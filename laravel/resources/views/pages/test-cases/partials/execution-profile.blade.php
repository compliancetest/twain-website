<div class="colored-box-content dynamic-rows">
    <div class="row">
        <div class="col-md-4">
            <label for="testCaseExecutionFile">Attach JSON test execution file:</label>
            <select name="test_execution_profile_id" id="testCaseExecutionFile" class="form-control">
                <option value="">-- Select execution file --</option>
                @foreach($profiles as $profile)
                    @if($profile->profile_role == 'TCEF')
                        <option value="{{ $profile->id }}"
                                @if($profile->id == $testCase->test_execution_profile_id) selected="selected"@endif>{{ $profile->profile_name }}</option>
                    @endif
                @endforeach
            </select>
        </div>
    </div>