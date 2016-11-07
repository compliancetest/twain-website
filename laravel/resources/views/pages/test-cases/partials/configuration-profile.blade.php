<h4 class="test-item-subheader">Test Configuration</h4>
<div class="row">
    <div class="col-md-4">
        <label for="testCaseTestDataProfile">Attach JSON configuration profile file:</label>
        <select name="configuration_profile_id" id="testCaseTestDataProfile" class="form-control">
            <option value="">-- Select profile type --</option>
            @foreach($profiles as $profile)
                <option value="{{ $profile->id }}"
                        @if($profile->id == $testCase->configuration_profile_id) selected="selected"@endif>{{ $profile->profile_name }}</option>
            @endforeach
        </select>
    </div>
</div>