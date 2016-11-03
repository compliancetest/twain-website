<select class="form-control" name="related_ts[suite_id][]">
    <option>Select test suite</option>
    @if($suiteCommunity)
        @foreach($suiteCommunity->testSuites()->orderBy('name')->orderBy('version_major')->orderBy('version_minor')->get() as $communityTestSuite)
            @if($communityTestSuite->id != $suiteCommunity->id)
                <option value="{{ $communityTestSuite->id }}"
                        @if($communityTestSuite->id == $relatedTestSuite->related_test_suite_id) selected="selected"@endif>{{ $communityTestSuite->full_name }}</option>
            @endif
        @endforeach
    @endif
</select>