<div class="colored-box-content">
    <?php $dataExist = false;?>
    @if($testSuites)
        @foreach($testSuites as $testSuite)
            <div class="definition-box">
                <h4 class="test-item-subheader">{{ $testSuite->full_name }}</h4>
                @foreach($testSuite->conformanceLevels as $conformanceLevel)
                    <?php $dataExist = true;?>
                    <dl class="definition-list">
                        <dt>
                            <label>
                                <input type="checkbox" name="conformanceLevel[{{ $testSuite->id }}][]" value="{{ $conformanceLevel->id }}"
                                       @if($testCase && count($testCase->conformanceLevels->where('conformance_level_id', $conformanceLevel->id)))checked="checked"@endif>
                                {{ $conformanceLevel->code }}
                            </label>
                        </dt>
                        <dd>{{ $conformanceLevel->description }}</dd>
                    </dl>
                @endforeach
            </div>
        @endforeach
    @endif
    @if(!$dataExist)
        <div class="text-center">No data found</div>
    @endif
</div>