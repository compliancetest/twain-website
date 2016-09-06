<div class="modal-header">
    <button type="button" class="close-modal" title="Close popup" data-dismiss="modal" aria-label="Close">Close</button>
    Screen Captures
</div>
<div class="modal-body">
    @foreach($screenCaptures AS $screenCapture)
        <a href="{{ $screenCapture }}" target="_blank">{{ pathinfo($screenCapture, PATHINFO_BASENAME) }}</a><br>
    @endforeach
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
</div>
<div class="block-loading">
    <div class="loading-content"><span class="loader"></span>
        <div class="loading-text">LOADING DATA</div>
        <div class="loading-wait">Please wait...</div>
    </div>
</div>
