<div class="colored-box-content">
    <?php $dataExist = false;?>
    @if($testSuites)
        @foreach($testSuites as $testSuite)
            <div class="definition-box">
                <h4 class="test-item-subheader">{{ $testSuite->full_name }}</h4>
                @foreach($testSuite->scenarios as $scenario)
                    <?php $dataExist = true;?>
                    <dl class="definition-list">
                        <dt>
                            <label>
                                <input type="radio" name="scenario[{{ $testSuite->id }}][]" value="{{ $scenario->id }}"
                                       @if(count($testCase->scenarios->where('conformance_level_id', $conformanceLevel->id)))checked="checked"@endif>{{ $scenario->code }}
                            </label>
                        </dt>
                        <dd>{{ $scenario->description }}</dd>
                    </dl>
                @endforeach
            </div>
        @endforeach
    @endif
    @if(!$dataExist)
        <div class="text-center">No data found</div>
    @endif
</div>