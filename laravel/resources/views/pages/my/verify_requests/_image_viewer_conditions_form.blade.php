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
                <div class="checkbox">
                    <label>
                        <input type="checkbox" value="{{ $skipCondition }}"  data-image="{{ $k }}" class="skipConditions" @if($readonly) disabled="disabled" @endif> {{ $skipCondition }}
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
</form>