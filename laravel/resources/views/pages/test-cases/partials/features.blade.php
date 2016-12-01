<div class="colored-box-content">
    <?php $dataExist = false;?>
    @if($testSuites)
        @foreach($testSuites as $testSuite)
            @if(count($testSuite->features))
                <div class="definition-box">
                    <h4 class="test-item-subheader">{{ $testSuite->full_name }}</h4>
                    @foreach($testSuite->features->sortBy('name') as $feature)
                        <?php $dataExist = true;?>
                        <dl class="definition-list">
                            <dt>
                                <label>
                                    <input type="checkbox" name="features[{{ $testSuite->id }}][]" value="{{ $feature->id }}"
                                           @if($testCase && count($testCase->features->where('test_suites_feature_id', $feature->id))) checked="checked" @endif>{{ $feature->name }}
                                </label>
                            </dt>
                            <dd>{{ $feature->description }}</dd>
                        </dl>
                    @endforeach
                </div>
            @endif
        @endforeach
    @endif
    @if(!$dataExist)
        <div class="text-center">No data found</div>
    @endif
</div>