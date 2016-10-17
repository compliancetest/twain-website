<form>
    <fieldset>
        @if($scannedImageData['passConditions'])
            @if($canModerate)
                <legend>Confirm that all pass conditions are met:</legend>
            @else
                <legend>Pass conditions:</legend>
            @endif
            @if(!$canModerate) <ul style="list-style-type: square;"> @endif
            @foreach($scannedImageData['passConditions'] as $passCondition)
                 @if($canModerate)
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" value="{{ $passCondition }}"  data-image="{{ $k }}" class="passConditions" @if($readonly) disabled="disabled" @endif> {{ $passCondition }}
                        </label>
                    </div>
                 @else
                     <li>{{ $passCondition }}</li>
                 @endif
            @endforeach
            @if(!$canModerate) </ul> @endif
        @endif
    </fieldset>

    <fieldset>
        @if($scannedImageData['skipConditions'])
            @if($canModerate)
                <legend>Or choose any of skip condition if it is met:</legend>
            @else
                <legend>Skip conditions:</legend>
            @endif
            @if(!$canModerate) <ul style="list-style-type: square;"> @endif
            @foreach($scannedImageData['skipConditions'] as $skipCondition)
                @if($canModerate)
                    <div class="radio">
                        <label>
                            <input type="radio" name="skip_{{ $k }}" value="{{ $skipCondition }}"  data-image="{{ $k }}" class="skipConditions" @if($readonly) disabled="disabled" @endif> {{ $skipCondition }}
                        </label>
                    </div>
                 @else
                     <li>{{ $skipCondition }}</li>
                @endif
            @endforeach
            @if(!$canModerate) </ul> @endif
        @endif
    </fieldset>

    @if($canModerate)
        <fieldset>
            @if($scannedImageData['skipConditions'] || $scannedImageData['passConditions'])
                <legend>Or provide a reason for skip or fail:</legend>
            @else
                <legend>Provide a reason for skip or fail if needed:</legend>
            @endif
            <div class="form-group">
                <input type="text" class="form-control reason" data-image="{{ $k }}" placeholder="Reason" @if($readonly) readonly="readonly" @endif>
            </div>
        </fieldset>
    @endif

    @if(!$readonly)
        <div class="row">
             @if($scannedImageData['imageInfo'] && isset($scannedImageData['imageInfo']->getOutput()['pImageInfo']))
                <fieldset class="col-md-6">
                    <legend>Image information:</legend>
                    <div id="imageInfoData{{ $k }}"></div>
                    <script>
                        var t_data1 = Jsonary.create({!! json_encode($scannedImageData['imageInfo']->getOutput()['pImageInfo']) !!}).readOnlyCopy();
                        var t_element1 = document.getElementById('imageInfoData{{ $k }}');
                        Jsonary.render(t_element1, t_data1);
                    </script>
                </fieldset>
            @endif
            @if($scannedImageData['extImageInfo'] && isset($scannedImageData['extImageInfo']->getOutput()['pExtImageInfo']))
                <fieldset class="col-md-6">
                    <legend>Extended image information:</legend>
                    <div id="extImageInfoData{{ $k }}"></div>
                    <script>
                        var t_data = Jsonary.create({!! json_encode($scannedImageData['extImageInfo']->getOutput()['pExtImageInfo']) !!}).readOnlyCopy();
                        var t_element = document.getElementById('extImageInfoData{{ $k }}');
                        Jsonary.render(t_element, t_data);
                    </script>
                </fieldset>
            @endif
        </div>
    @endif
</form>