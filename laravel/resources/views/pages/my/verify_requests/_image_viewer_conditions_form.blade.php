<form>
    <fieldset>
        @if($scannedImageData['passConditions'])
            <legend>Confirm that all pass conditions are met:</legend>
            @foreach($scannedImageData['passConditions'] as $passCondition)
                <div class="checkbox">
                    <label>
                        <input type="checkbox" value="{{ $passCondition }}"  data-image="{{ $k }}" class="passConditions" @if($readonly) disabled="disabled" @endif> {{ $passCondition }}
                    </label>
                </div>
            @endforeach
        @endif
    </fieldset>

    <fieldset>
        @if($scannedImageData['skipConditions'])
            <legend>Or choose any of skip condition if it is met:</legend>
            @foreach($scannedImageData['skipConditions'] as $skipCondition)
                <div class="radio">
                    <label>
                        <input type="radio" name="skip_{{ $k }}" value="{{ $skipCondition }}"  data-image="{{ $k }}" class="skipConditions" @if($readonly) disabled="disabled" @endif> {{ $skipCondition }}
                    </label>
                </div>
            @endforeach
        @endif
    </fieldset>

    <fieldset>
        <legend>Or provide a reason for skip or fail:</legend>
        <div class="form-group">
            <input type="text" class="form-control reason" data-image="{{ $k }}" placeholder="Reason" @if($readonly) readonly="readonly" @endif>
        </div>
    </fieldset>
    <div class="row">
         @if($scannedImageData['imageInfo'])
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
        @if($scannedImageData['extImageInfo'])
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
</form>